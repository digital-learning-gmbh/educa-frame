import React, { useEffect, useState } from "react";
import { useTranslation } from "react-i18next";
import EducaAjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import { getDisplayPair } from "../../../shared/shared-components/Inputs";
import Select from "react-select";
import Button from "react-bootstrap/Button";
import {Card} from "react-bootstrap";

export default function AdministrationSettings(props) {
    let [schools, setSchools] = useState([]);
    let [school, setSchool] = useState();

    const t = useTranslation().t;

    useEffect(() => {
        getAppInfo();
    }, []);
    const getAppInfo = () => {
        EducaAjaxHelper.getAppSettings("stupla")
            .then(resp => {
                if (resp.payload?.options?.defaultSchool) {
                    setSchools(resp.payload?.options?.defaultSchool);
                    setSchool(
                        resp.payload?.selectedValues?.length > 0
                            ? resp.payload?.options?.defaultSchool?.find(
                                  s =>
                                      s.id ==
                                      resp.payload.selectedValues.find(
                                          s => s.key == "defaultSchool"
                                      ).value
                              )
                            : null
                    );
                }
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Schuldaten konnten nicht geladen werden. " + err.message
                );
            });
    };

    const saveSchool = () => {
        if (!school)
            return SharedHelper.fireErrorToast(
                "Fehler",
                "Bitte eine Schule auswählen."
            );

        EducaAjaxHelper.setAppSettings("stupla", { defaultSchool: school.id })
            .then(resp => {
                if (resp.status > 0) {
                    SharedHelper.fireSuccessToast(
                        "Erfolg",
                        "Speichern der Standardschule erfolgreich."
                    );
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Standardschule konnte nicht gespeichert werden. " +
                        err.message
                );
            });
    };

    return (
        <div
            style={{ display: "flex", flexDirection: "column" }}
            className={"container"}
        >
            <h3>Verwaltung</h3>
            <h5>
                Allgemeine Einstellungen</h5>
            <Card>
                <Card.Body>
            <div
                style={{
                    display: "flex",
                    flexDirection: "column",
                    width: "450px"
                }}
                className={"mb-2"}
            >
                {getDisplayPair(
                    "Standardschule",
                    <Select
                        getOptionValue={o => o.id}
                        getOptionLabel={o => o.name}
                        placeholder={t("school")}
                        options={schools}
                        value={school}
                        onChange={obj => setSchool(obj)}
                    />
                )}
            </div>
            <div>
                <Button
                    onClick={() => {
                        saveSchool();
                    }}
                >
                    Speichern
                </Button>
            </div>
                </Card.Body>
            </Card>
        </div>
    );
}
