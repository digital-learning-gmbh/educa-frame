import React, {useEffect, useState} from "react";
import {Card, Accordion, Button} from "react-bootstrap";
import EducaAjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import {DisplayPair} from "../../../shared/shared-components/Inputs";
import EducaLabeledSwitch from "../../../shared/shared-components/EducaLabeledSwitch";

export default function NotificationSettings() {

    let [groupSettings, setGroupSettings] = useState([]);
    const [isLoading, setIsLoading] = useState(false)

    useEffect(() => {
        loadGroupNotifications();
    }, []);

    const loadGroupNotifications = () => {
        setIsLoading(true)
        EducaAjaxHelper.getGroupNotificationSettings()
            .then(resp => {
                if (resp.payload?.groups) {
                    setGroupSettings(resp.payload?.groups);
                    return
                }
                throw new Error()
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Die Einstellungen konnten nicht geladen werden. " + err.message
                );
            })
            .finally(() => setIsLoading(false))
    };


    const flipSectionNotifications = (group, section, flag) => {
        setIsLoading(true)
        EducaAjaxHelper.setSectionNotificationSetting(group.id, section.id, !flag)
            .then(resp => {
                if (resp.payload?.group) {
                    setGroupSettings(groupSettings?.map(grp => grp?.id == resp.payload.group.id ? resp.payload.group : grp));
                    return SharedHelper.fireSuccessToast("Erfolg", "Die Einstellung wurde gespeichert.");
                }
                throw new Error();
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Die Einstellungen konnten nicht gespeichert werden. " + err.message
                );
            })
            .finally(() => setIsLoading(false))
    }

    const flipGroupNotifications = (group, flag) => {
        setIsLoading(true)
        EducaAjaxHelper.setSectionNotificationSetting(group.id, null, !flag)
            .then(resp => {
                if (resp.payload?.group) {
                    setGroupSettings(groupSettings?.map(grp => grp?.id == resp.payload.group.id ? resp.payload.group : grp));
                    return SharedHelper.fireSuccessToast("Erfolg", "Die Einstellung wurde gespeichert.");
                }
                throw new Error();
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Die Einstellungen konnten nicht gespeichert werden. " + err.message
                );
            })
            .finally(() => setIsLoading(false))
    }

    return <div
        style={{display: "flex", flexDirection: "column"}}
        className={"container"}
    >
        <h3
            style={{
                marginBottom: "1rem"
            }}
        >
            Benachrichtigungen
        </h3>

                {groupSettings?.map((group, i) => {

                    return <div key={i} className={"mb-1"}>
                        <Accordion>
                            <Card>
                                <Card.Header>
                                    <div className={"d-flex justify-content-between"}>
                                        <Accordion.Toggle
                                            as={Button}
                                            variant="link"
                                            eventKey={i + 1}
                                        >
                                            <div> {group?.name}</div>
                                        </Accordion.Toggle>
                                        <EducaLabeledSwitch
                                            disabled={isLoading}
                                            size="md"
                                            checked={group?.sections?.reduce((r, sect) => r && !sect?.notificationDisabled,true)}
                                            labelLeft={"Inaktiv"}
                                            labelRight={"Aktiv"}
                                            onChange={b => flipGroupNotifications(group, b)}/>
                                    </div>
                                </Card.Header>
                                <Accordion.Collapse eventKey={i + 1}>
                                    <Card.Body>
                                        {group?.sections?.map((sect, j) => {
                                            return <div key={j}>
                                                <b>{sect?.name}</b>
                                                <EducaLabeledSwitch
                                                    disabled={isLoading}
                                                    size="sm"
                                                    checked={!sect?.notificationDisabled}
                                                    labelLeft={"Inaktiv"}
                                                    labelRight={"Aktiv"}
                                                    onChange={b => flipSectionNotifications(group, sect, b)}/>
                                            </div>

                                        })}
                                    </Card.Body>
                                </Accordion.Collapse>
                            </Card>
                        </Accordion>
                    </div>
                })}
    </div>

}
