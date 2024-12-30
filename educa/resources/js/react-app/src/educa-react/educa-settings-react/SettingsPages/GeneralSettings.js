import { useDispatch, useSelector } from "react-redux";
import { GENERAL_SET_CURRENT_CLOUD_USER } from "../../reducers/GeneralReducer";
import React, { useRef, useState } from "react";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import EducaAjaxHelper from "../../helpers/EducaAjaxHelper";
import { getDisplayPair } from "../../../shared/shared-components/Inputs";
import Button from "react-bootstrap/Button";
import ImageCropper from "../../../shared/shared-components/ImageCropper";
import EducaModal, {
    MODAL_BUTTONS
} from "../../../shared/shared-components/EducaModal";
import {Card, FormControl, ListGroup, ListGroupItem} from "react-bootstrap";
import { EducaLanguageSelect } from "../../educa-components/EducaLanguageSelect";

export default function GeneralSettings(props) {
    const dispatch = useDispatch();
    const setMe = me =>
        dispatch({ payload: me, type: GENERAL_SET_CURRENT_CLOUD_USER });
    const me = useSelector(s => s.currentCloudUser);
    const tenant = useSelector(s => s.tenant);
    const [name, setName] = useState(me.name);
    const [language, setLanguage] = useState(me.language);

    const educaModalRef = useRef();

    function saveUser() {
        if (!name)
            return SharedHelper.fireErrorToast(
                "Fehler",
                "Bitten einen Namen angeben."
            );

        if (!language)
            return SharedHelper.fireErrorToast(
                "Fehler",
                "Bitten wähle deine bevorzugte Sprache aus."
            );

        EducaAjaxHelper.updateGeneralSetting(
            name,
            language,
            null
        )
            .then(resp => {
                if (resp.status > 0 && resp.payload.cloudUser) {
                    setMe({
                        ...me,
                        name: resp.payload.cloudUser.name,
                        language: resp.payload.cloudUser.language
                    });
                    SharedHelper.fireSuccessToast(
                        "Erfolg",
                        "Speichern erfolgreich."
                    );
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Profil konnte nicht gespeichert werden. " + err.message
                );
            });
    }

    function saveImage(img) {
        EducaAjaxHelper.updateGeneralSettingsProfileImage(img)
            .then(resp => {
                if (resp.status > 0 && resp.payload.cloudUser) {
                    setMe({ ...me, image: resp.payload.cloudUser.image });
                    SharedHelper.fireSuccessToast(
                        "Erfolg",
                        "Speichern erfolgreich."
                    );
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Profilbild konnte nicht gespeichert werden. " + err.message
                );
            });
    }

    return (
        <div
            style={{ display: "flex", flexDirection: "column" }}
            className={"container"}
        >
            <h3
                style={{
                    marginBottom: "1rem"
                }}
            >
                Einstellungen
            </h3>

            <h5>
                Allgemeine Einstellungen</h5>
            <Card>
                <Card.Body>
            <div
                style={{
                    display: "flex",
                    flexDirection: "column",
                    minWidth: "300px",
                    maxWidth: "600px"
                }}
            >
                <img
                    width={100}
                    className={"rounded-circle img-responsive"}
                    src={EducaAjaxHelper.getCloudUserAvatarUrl(
                        me.id,
                        100,
                        me.image
                    )}
                />
                    <div
                    style={{
                        display: "flex",
                        flexDirection: "column",
                        marginLeft: "-0.5rem"
                    }}
                >
                    {getDisplayPair("E-Mail/Loginkennung", me?.email)}
                    {getDisplayPair("Logintyp", me?.loginType)}
                    {getDisplayPair(
                        "Profilbild ändern",
                        <Button
                            onClick={() => {
                                educaModalRef.current.open(
                                    () => {},
                                    "Profilbild ändern",
                                    <ImageCropper
                                        initImage={EducaAjaxHelper.getCloudUserAvatarUrl(
                                            me.id,
                                            300,
                                            me.image
                                        )}
                                        imageReadyCallback={img => {
                                            educaModalRef?.current?.close();
                                            saveImage(img);
                                        }}
                                        image
                                    />,
                                    [MODAL_BUTTONS.CLOSE]
                                );
                            }}
                        >
                            Ändern
                        </Button>
                    )}
                    <div style={{ height: "1.2rem" }}></div>
                    {getDisplayPair(
                        "Name",
                        <FormControl
                            value={name}
                            onChange={evt =>
                                setName(
                                    evt.target.value?.length > 35
                                        ? evt.target.value?.substring(0, 35)
                                        : evt.target.value
                                )
                            }
                        />
                    )}
                    {getDisplayPair(
                        "Bevorzugte Sprache",
                        <EducaLanguageSelect
                            value={language}
                            onChange={event => setLanguage(event.code)}
                        />
                    )}
                </div>
                <div style={{ marginTop: "0.5rem" }}>
                    <Button onClick={() => saveUser()}>Speichern</Button>
                </div>
            </div>
                </Card.Body>
            </Card>
            <EducaModal size={"lg"} ref={educaModalRef} />
        </div>
    );
}
