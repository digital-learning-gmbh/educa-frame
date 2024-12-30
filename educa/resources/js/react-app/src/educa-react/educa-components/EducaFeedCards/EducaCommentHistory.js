import React, {useEffect, useState} from "react";
import {useSelector} from "react-redux";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper, {LIMITS} from "../../helpers/EducaHelper";
import {Button, Modal} from "react-bootstrap";
import {EducaLoading} from "../../../shared-local/Loading";
import ReactTimeAgo from "react-time-ago";
import moment from "moment";

function EducaCommentHistory(props) {

    if (props.comment == null)
        return <></>

    let [success, setSuccess] = useState(false);
    let [history, setHistory] = useState([]);


    let cloud_user = useSelector(s => s.currentCloudUser)

    let loadHistory = () => {
        AjaxHelper.commentHistoryAnnouncement(props.comment.beitrag_id, props.comment.id)
            .then(resp => {
                if (resp.payload && resp.payload.history) {
                    setSuccess(true)
                    setHistory(resp.payload.history)
                } else
                    throw new Error("Fehler. Diese Aktion ist zur Zeit nicht möglich.")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", err.message)
            })
    }

    useEffect(() => {
        if (props.show)
            loadHistory();
    }, [props.show])


    let imageUrl = AjaxHelper.getCloudUserAvatarUrl(props.comment.author.id, 30, props.comment.author.image)

    let lastChange = null;
    return <Modal show={props.show} onHide={() => props.hideCallback()}>
        <Modal.Header closeButton>
            <Modal.Title><i className="fas fa-history"></i> Kommentar-Historie</Modal.Title>
        </Modal.Header>
        <Modal.Body>
            {success ? <>
                    {
                        history.map(function (entry) {
                            let dom = <div key={entry.id} className="group-comment">
                                <div className={"mr-2 float-left"}>
                                    <img loading={"lazy"} className="rounded-circle" src={imageUrl}
                                         alt=""/>
                                </div>
                                <div className="media-body" style={{overflow: "visible"}}>
                                    <div style={{marginBottom: "0px"}} className="d-flex justify-content-between">
                                        <div>
                                            <b>{props.comment.author.name}</b>
                                            <small
                                                className="text-muted"> <ReactTimeAgo
                                                date={moment(entry.properties.old?.updated_at ? entry.properties.old?.updated_at : entry.properties.old?.created_at).toDate()} locale="de-DE"/>
                                            </small>
                                            <small>
                                                { lastChange != null ? <> von {lastChange.causer.name}</> : null }
                                            </small>
                                        </div>
                                    </div>
                                    <p>{entry.properties.old?.content}
                                    </p>
                                </div>
                            </div>
                            lastChange = entry;
                            return dom;
                        })
                    }
                    <div className="group-comment">
                        <div className={"mr-2 float-left"}>
                            <img loading={"lazy"} className="rounded-circle" src={imageUrl}
                                 alt=""/>
                        </div>
                        <div className="media-body" style={{overflow: "visible"}}>
                            <div style={{marginBottom: "0px"}} className="d-flex justify-content-between">
                                <div>
                                    <b>{props.comment.author.name}</b>
                                    <small
                                        className="text-muted"> <ReactTimeAgo
                                        date={moment(props.comment.updated_at).toDate()} locale="de-DE"/>
                                    </small>
                                    <small>
                                        { lastChange != null ? <> von {lastChange.causer.name}</> : null }
                                    </small>
                                </div>
                            </div>
                            <p>{props.comment.content}
                            </p>
                        </div>
                    </div>
                    </>
                :
                <EducaLoading/>
            }
        </Modal.Body>
        <Modal.Footer>
            <Button variant="secondary" onClick={() => props.hideCallback()}>
                Schließen
            </Button>
        </Modal.Footer>
    </Modal>
}

export default EducaCommentHistory;
