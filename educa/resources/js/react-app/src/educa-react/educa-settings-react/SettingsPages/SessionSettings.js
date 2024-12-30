import React, {useEffect, useRef, useState} from "react";
import {Alert, Card} from "react-bootstrap";
import {EducaCircularButton, EducaDefaultTable} from "../../../shared/shared-components";
import EducaAjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import ReactTimeAgo from "react-time-ago";
import Button from "react-bootstrap/Button";
import moment from "moment";

export default function SessionSettings(props) {

    let [session, setSessions] = useState([]);

    useEffect(() => {
        getSessions();
    }, []);

    const getSessions = () => {
        EducaAjaxHelper.getSessions()
            .then(resp => {
                if (resp.payload?.sessions) {
                    setSessions(resp.payload?.sessions);
                }
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Die Sitzungen konnten nicht geladen werden. " + err.message
                );
            });
    };

    const closeSession = (id) => {
        EducaAjaxHelper.closeSession(id)
            .then(resp => {
                if (resp.payload?.sessions) {
                    setSessions(resp.payload?.sessions);
                }
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Die Sitzung konnte nicht beendet werden. " + err.message
                );
            });
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
            Sitzungen
        </h3>
        <h5>
            Du bist auf folgenden Geräten noch angemeldet</h5>
        <Card>
            <Card.Body>
                <EducaDefaultTable
                    columns={[
                        {Header :"Plattform", accessor : "device"},
                        {Header :"Betriebssystem", accessor : "os"},
                        {Header :"Browser / App-Version", accessor : "mixed_app"},
                        {Header :"zuletzt verwendet", accessor : "last_seen_relative"},
                        {Header :"Aktion", accessor : "action"},
                    ]}
                    data={session.map((s) =>{
                        return {
                            ...s,
                            mixed_app : s.browser ? s.browser : s.app,
                            last_seen_relative: <ReactTimeAgo date={moment(s.last_seen)}/>,
                            action: s.token == SharedHelper.getJwt() ? <div><i className="fas fa-info-circle mr-1"></i><i>Diese Sitzung</i></div> :  <EducaCircularButton variant={"danger"} onClick={() => closeSession(s.id)} size={"small"}><i
                                className="fas fa-trash"></i></EducaCircularButton>
                        }
                    })}
                />
            </Card.Body>
        </Card>
    </div>

}
