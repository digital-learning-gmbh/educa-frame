import React, { useState } from 'react';
import { Button, Dropdown } from 'react-bootstrap';
import { useSelector } from 'react-redux';
import ReactTextareaAutosize from 'react-textarea-autosize';
import ReactTimeAgo from "react-time-ago";
import FliesentischZentralrat from '../../FliesentischZentralrat';
import AjaxHelper from '../../helpers/EducaAjaxHelper';
import EducaHelper, { LIMITS } from '../../helpers/EducaHelper';
import EducaBlockUserModal from '../EducaBlockUserModal';
import EducaReportModal from '../EducaReportModal';
import EducaCommentHistory from "./EducaCommentHistory";
import moment from "moment";

const CustomToggle = React.forwardRef(({ children, onClick }, ref) => (
    <a
        href=""
        ref={ref}
        className="text-muted"
        onClick={(e) => {
            e.preventDefault();
            onClick(e);
        }}
    >
        <i className="fas fa-ellipsis-v"></i>
    </a>
));


function EducaComment(props) {


    let [edit, setEdit] = useState(false);
    let [comment, setComment] = useState(props.comment);
    let [commentOldText, setCommentOldText] = useState(props.comment.content);

    let [blockUserModalShow, setBlockUserModalShow] = useState(false);
    let [reportContentShow, setReportContentShow] = useState(false);
    let [commentHistory, setCommentHistroy] = useState(false);

    let cloud_user = useSelector(s => s.currentCloudUser)

    let toggleHidden = () => {
        AjaxHelper.toggleCommentInAnnouncement(props.announcement.id, comment.id)
        .then(resp => {
            if (resp.payload) {
                setComment(resp.payload.comment)
            } else
                throw new Error("Fehler. Diese Aktion ist zur Zeit nicht möglich.")
        })
        .catch(err => {
            EducaHelper.fireErrorToast("Fehler", err.message)
        })
    }

    let updateComment = () => {
        AjaxHelper.updateCommentInAnnouncement(props.announcement.id, comment.id, comment.content)
        .then(resp => {
            if (resp.payload) {
                setComment(resp.payload.comment)
                setEdit(false)
            } else
                throw new Error("Fehler. Diese Aktion ist zur Zeit nicht möglich.")
        })
        .catch(err => {
            EducaHelper.fireErrorToast("Fehler", err.message)
        })
    }

    if (!comment.author)
        return;
    let imageUrl = AjaxHelper.getCloudUserAvatarUrl(comment.author.id, 30, comment.author.image)

    if (edit)
        return (<div key={comment.id} className="group-comment">
            <div className={"mr-2 float-left"}>
                <img loading={"lazy"} className="rounded-circle" src={imageUrl}
                    alt="" />
            </div>
            <div className="media-body" style={{ overflow: "visible" }}>
                <div style={{ marginBottom: "0px" }} className="d-flex justify-content-between">
                    <div>
                        <b>{comment.author.name}</b>
                        <small
                            className="text-muted"> <ReactTimeAgo date={moment(comment.created_at).toDate()} locale="de-DE" />
                        </small>
                    </div>
                </div>
                <ReactTextareaAutosize
                        maxLength={LIMITS.COMMENT_LIMIT}
                        maxRows={6}
                        minRows={3}
                        value={comment.content}
                        className="form-control editor"
                        placeholder="Dein Kommentar..."
                        onChange={(evt) => {
                            setComment({...comment, content: evt.target.value})
                            if(evt.target.value.length > LIMITS.COMMENT_LIMIT - 1)
                            {
                                EducaHelper.fireWarningToast("Hinweis","Das Zeichenlimit für Kommentare bei "+ LIMITS.COMMENT_LIMIT + " Zeichen");
                            }
                        }}
                    >

                    </ReactTextareaAutosize>
                    <div className="btn-group">
                        <Button
                            type="submit"
                            title={comment.content === "" ? "Du musst zuerst ein Kommentare schreiben" : "Speichern"}
                            disabled={comment.content === "" || comment.content.length > LIMITS.COMMENT_LIMIT}
                            className="btn btn-primary m-1"
                            onClick={() => {
                                updateComment();
                            }}
                        >Kommentar speichern</Button>
                                <Button
                            title={"Abbrechen"}
                            className="btn btn-secondary m-1"
                            onClick={() => {
                                setComment({...comment, content: commentOldText})
                                setEdit(false);
                            }}
                        >Abbrechen</Button>
                    </div>
                    </div>
        </div>)

    if(comment.hidden && props.announcement?.cloudid != cloud_user?.id )
        return <></>

    return (<div key={comment.id} className="group-comment">
        <div className={"mr-2 float-left"}  style={{ opacity: comment.hidden ? 0.3 : 1.0}}>
            <img loading={"lazy"} className="rounded-circle" src={imageUrl}
                alt="" />
        </div>
        <div className="media-body" style={{ overflow: "visible"}}>
            <div style={{ marginBottom: "0px" }} className="d-flex justify-content-between">
                <div style={{ opacity: comment.hidden ? 0.3 : 1.0}}>
                    <b>{comment.author.name}</b>
                    <small
                        className="text-muted"> <ReactTimeAgo date={moment(comment.created_at).toDate()} locale="de-DE" />
                    </small>
                    {
                        comment.edited ? <a onClick={() => setCommentHistroy(true)} style={{fontSize: "80%", fontWeight: 400}} className='text-muted font-italic'> bearbeitet</a> : null
                    }
                </div>
                <Dropdown>
                    <Dropdown.Toggle as={CustomToggle} variant="outline-secondary" id="dropdown-basic" />
                    <Dropdown.Menu>
                        {FliesentischZentralrat.globalCanReport() && comment.cloudid != cloud_user?.id  ? <Dropdown.Item onClick={() => setReportContentShow(true)}><i className="far fa-flag fa-fw"></i> Spam melden</Dropdown.Item> : <></> }
                        {FliesentischZentralrat.globalCanBlock() && comment.cloudid != cloud_user?.id  ? <Dropdown.Item onClick={() => setBlockUserModalShow(true)} ><i className="fas fa-ban fa-fw"></i> Nutzer blockieren</Dropdown.Item>: <></> }
                        {props.announcement?.cloudid == cloud_user?.id || comment.cloudid == cloud_user?.id ? <Dropdown.Item onClick={() => toggleHidden()}> {
                            comment.hidden ? <><i className="fas fa-eye fa-fw"></i> Kommentar anzeigen</> :
                            <><i className="fas fa-eye-slash fa-fw"></i> Kommentar verbergen</> }</Dropdown.Item> : <></> }
                        {props.announcement?.cloudid == cloud_user?.id || comment.cloudid == cloud_user?.id ? <Dropdown.Item onClick={() => setEdit(true)}><i className="fas fa-pencil-alt fa-fw"></i> Kommentar bearbeiten</Dropdown.Item> : <></> }
                    </Dropdown.Menu>
                </Dropdown>
            </div><p style={{ marginBottom: "0px", opacity: comment.hidden ? 0.3 : 1.0}}>{comment.content}
            </p></div>
            <EducaBlockUserModal show={blockUserModalShow} hideCallback={() => setBlockUserModalShow(false)} user={comment.author}/>
            <EducaReportModal show={reportContentShow} hideCallback={() => setReportContentShow(false)} content={comment} content_type="comment" />
            <EducaCommentHistory show={commentHistory} hideCallback={() => setCommentHistroy(false)} comment={comment} />
    </div>)
}

export default EducaComment;
