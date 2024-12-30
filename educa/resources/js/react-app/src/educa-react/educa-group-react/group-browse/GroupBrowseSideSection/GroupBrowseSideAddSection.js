import React, { useState } from "react";
import { Card, Collapse, ListGroup } from "react-bootstrap";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import FliesentischZentralrat from "../../../FliesentischZentralrat";
import { EducaCircularButton } from "../../../../shared/shared-components";
import { EducaInputConfirm } from "../../../../shared/shared-components/Inputs";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import EducaHelper from "../../../helpers/EducaHelper";
import {GROUP_SECTION_OVERVIEW_APP, GROUP_VIEWS} from "../GroupBrowse";

export default function GroupBrowseSideAddSection(props) {
    const [addSectionActive, setAddSectionActive] = useState(false);
    const [newSectionName, setNewSectionName] = useState("");

    function createNewSection() {
        AjaxHelper.addSectionToGroup(props.group.id, newSectionName)
            .then(resp => {
                if (resp.status > 0 && resp.payload) {
                    EducaHelper.fireSuccessToast(
                        "Hinzufügen erfolgreich.",
                        props.t("section") + " erfolgreich hinzugefügt."
                    );

                    props.reduxRefreshGroup(resp.payload);
                    props.navigate([
                        props.group.id,
                        GROUP_VIEWS.FEED
                    ])
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Hinzufügen fehlgeschlagen.",
                    "Hinzufügen der " +
                        props.t("section") +
                        " fehlgeschlagen. Servermessage: " +
                        err.message
                );
            })
            .finally(() => {
                setAddSectionActive(false);
                setNewSectionName("");
            });
    }

    return (
        <>
            <Card.Body style={{ paddingBottom: "0px" }}>
                <Card.Title>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "row"
                        }}
                    >
                        <div
                            style={{
                                display: "flex",
                                flexDirection: "column",
                                justifyContent: "center",
                                fontWeight: "700",
                                color: SharedHelper.isColorTooDark(
                                    props.group.color
                                )
                                    ? "#f8f9fa !important"
                                    : "#343a40 !important",
                                fontSize: "1.125rem"
                            }}
                        >
                            {props.t("sections")}
                        </div>
                        {FliesentischZentralrat.groupCreateSection(
                            props.group
                        ) ? (
                            <EducaCircularButton
                                style={{
                                    marginLeft: "10px"
                                }}
                                variant={
                                    addSectionActive ? "danger" : "success"
                                }
                                onClick={() =>
                                    setAddSectionActive(!addSectionActive)
                                }
                                size={"small"}
                            >
                                {addSectionActive ? (
                                    <i className="fas fa-minus"></i>
                                ) : (
                                    <i className="fas fa-plus"></i>
                                )}
                            </EducaCircularButton>
                        ) : null}
                    </div>
                </Card.Title>
            </Card.Body>
            <ListGroup bg={"transparent"} variant={"flush"}>
                <Collapse in={addSectionActive}>
                    <div>
                        {FliesentischZentralrat.groupCreateSection(
                            props.group
                        ) ? (
                            <ListGroup.Item
                                className={"bg-transparent border-0"}
                            >
                                <div
                                    style={{
                                        display: "flex",
                                        flexDirection: "row"
                                    }}
                                >
                                    <EducaInputConfirm
                                        maxLetters={200}
                                        placeholder={props.t(
                                            "nameOfTheNewSection"
                                        )}
                                        value={newSectionName}
                                        onChange={event =>
                                            setNewSectionName(
                                                event.target.value
                                            )
                                        }
                                        onConfirmClick={() =>
                                            createNewSection()
                                        }
                                    />
                                </div>
                            </ListGroup.Item>
                        ) : null}
                    </div>
                </Collapse>
            </ListGroup>
        </>
    );
}
