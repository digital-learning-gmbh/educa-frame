import React, { useState } from "react";
import { ListGroup, Spinner } from "react-bootstrap";
import { GROUP_VIEWS } from "../GroupBrowse";
import FliesentischZentralrat from "../../../FliesentischZentralrat";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import { MODELS } from "../../../../shared/shared-helpers/SharedHelper";
import EducaHelper from "../../../helpers/EducaHelper";

export default function GroupBrowseSideGeneral(props) {
    const [meetingLoading, setMeetingLoading] = useState(false);

    function joinMeeting() {
        setMeetingLoading(true);
        AjaxHelper.joinMeeting(MODELS.GROUP, props.group?.id)
            .then(resp => {
                if (!resp.payload?.url) throw new Error(resp.message);
                window.open(resp.payload.url);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Meeting konnte nicht gestartet werden. " + err.message
                );
            })
            .finally(() => setMeetingLoading(false));
    }

    return (
        <ListGroup bg={"transparent"} variant={"flush"}>
            <ListGroup.Item
                style={{
                    cursor: "pointer",
                    fontWeight:
                        props.groupView === GROUP_VIEWS.FEED ? "bold" : "normal"
                }}
                active={props.groupView === GROUP_VIEWS.FEED}
                onClick={() =>
                    props.navigate([props.group.id, GROUP_VIEWS.FEED])
                }
                className={"bg-transparent border-0"}
            >
                <i className="fas fa-rocket"></i> Aktivitäten
            </ListGroup.Item>
            {FliesentischZentralrat.groupEditGroup(props.group) ? (
                <ListGroup.Item
                    style={{
                        cursor: "pointer",
                        fontWeight:
                            props.groupView === GROUP_VIEWS.SETTINGS
                                ? "bold"
                                : "normal"
                    }}
                    active={props.groupView === GROUP_VIEWS.SETTINGS}
                    onClick={() =>
                        props.navigate([props.group.id, GROUP_VIEWS.SETTINGS])
                    }
                    className={"bg-transparent border-0"}
                >
                    <i className="fas fa-cogs"></i> Einstellungen
                </ListGroup.Item>
            ) : null}
            {props.group.schoolclass ? (
                <ListGroup.Item
                    style={{
                        cursor: "pointer",
                        fontWeight:
                            props.groupView === GROUP_VIEWS.TIMETABLE
                                ? "bold"
                                : "normal"
                    }}
                    active={props.groupView === GROUP_VIEWS.TIMETABLE}
                    onClick={() =>
                        props.navigate([props.group.id, GROUP_VIEWS.TIMETABLE])
                    }
                    className={"bg-transparent border-0"}
                >
                    <i className="fas fa-calendar-alt"></i> Stundenplan
                </ListGroup.Item>
            ) : null}
        </ListGroup>
    );
}
