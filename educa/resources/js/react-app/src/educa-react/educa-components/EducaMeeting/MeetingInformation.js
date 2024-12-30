
import React, {useEffect, useState} from 'react'
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import EducaHelper from "../../helpers/EducaHelper";
import Button from "react-bootstrap/Button";
import {Spinner} from "react-bootstrap";
import ReactTabulator from "react-tabulator/lib/ReactTabulator";

export function MeetingInformation(props) {

    let [ meeting, setMeeting ] = useState(null)
    let [ records, setRecords ] = useState([])
    let [isMeetingLoading, setIsMeetingLoading] = useState(false)

    useEffect(() =>
    {
        loadMeetingInformation()
    }, [props])


    let loadMeetingInformation = () =>
    {
        AjaxHelper.infoMeeting(props.model_type, props.model_id)
            .then( resp =>
            {
                if(resp.status > 0 )
                {
                    setMeeting(resp.payload?.meeting)
                    setRecords(resp.payload?.recordings)
                    return
                }
                throw new Error(resp.message)
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Meeting-Infos konnte nicht verarbeitet werden.")
            })
    }

    const columns = [
        { title: 'Start', field: 'startTime' },
        { title: 'Ende', field: 'endTime' },
        { title: 'Wiedergabe', field: 'url_button' , formatter: 'html'},
    ];

    const options =
        { movableColumns: true,
            movableRows: true,
            placeholder:" Keine Aufzeichnungen vorhanden"
        };


    const joinMeeting = () => {
        setIsMeetingLoading(true)
        AjaxHelper.joinMeeting(props.model_type, props.model_id)
            .then(resp => {
                if (!resp.payload?.url)
                    throw new Error(resp.message)
                window.open(resp.payload.url)
                loadMeetingInformation()
            })
            .catch(err => {

                EducaHelper.fireErrorToast("Fehler", "Meeting konnte nicht gestartet werden. " + err.message)
            })
            .finally(() => {
                setIsMeetingLoading(false)
            })
    }

    return <div>
        {meeting ?
            <div>  <p>Externe Nutzer können sich über den folgenden Link einwählen:</p>
                <p><a href={ window.location.origin + "/meeting/join/?model_type=" + props.model_type + "&model_id=" + props.model_id + "&pin=" + meeting.password_member?.substring(0,6)}>{ window.location.origin + "/meeting/join/?model_type=" + props.model_type + "&model_id=" + props.model_id + "&pin=" + meeting.password_member?.substring(0,6)}</a></p>
                <p><b>PIN: {meeting.password_member?.substring(0,6)}</b></p>
                <p>Folgende Aufzeichnungen sind zu diesem Termin vorhanden:</p>
                <ReactTabulator
                    columns={columns}
                    data={records}
                    options={options}
                />
            </div>
            : <div> <p>Es wurde noch kein Meeting gestartet oder es wurde bereits wieder beendet. Um ein Link zu erzeugen, starten Sie einmalig das Meeting.
                {props.model_id ? <Button
                    className="btn btn-success ml-2"
                    onClick={() => joinMeeting()}
                >
                    <div style={{display: "flex", flexDirection: "row"}}>
                        Meeting starten
                        <div style={{
                            display: "flex",
                            marginLeft: "2px",
                            flex: "1 1 0%",
                            flexDirection: "column",
                            justifyContent: "center"
                        }}>
                            {isMeetingLoading ? <Spinner
                                as="span"
                                animation="grow"
                                size="sm"
                                role="status"
                                aria-hidden="true"
                            /> : <i className="fas fa-external-link-alt"></i>}
                        </div></div>
                </Button> : null}

            </p></div> }
    </div>
}
