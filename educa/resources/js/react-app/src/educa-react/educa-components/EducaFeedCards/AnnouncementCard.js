import React, {useEffect, useRef, useState} from "react";
import {useSelector} from "react-redux";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper, {EducaCKEditorDefaultConfig} from "../../../shared/shared-helpers/SharedHelper";
import {Button, Card, Collapse, Dropdown} from "react-bootstrap";
import TextareaAutosize from "react-textarea-autosize";
import ReactTimeAgo from "react-time-ago";
import ReactTooltip from "react-tooltip";
import {CKEditor} from "@ckeditor/ckeditor5-react";
import {EducaCardLinkButton, EducaCircularButton} from "../../../shared/shared-components/Buttons";
import AttachedFileComponent from "../../../shared/shared-components/AttachedFileComponent";
import EducaModal, {MODAL_BUTTONS} from "../../../shared/shared-components/EducaModal";
import {BASE_ROUTES} from "../../App";
import EducaHelper, {LIMITS} from "../../helpers/EducaHelper";
import ReactSwitch from "react-switch";
import EducaComment from "./EducaComment";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import EducaBlockUserModal from "../EducaBlockUserModal";
import EducaReportModal from "../EducaReportModal";
import moment from "moment";

export default function AnnouncementCard(props) {
    const [newComment, setNewComment] = useState("");
    const [isCommentsSectionOpen, setIsCommentsSectionOpen] = useState(false);
    const amountCommentsBeforeCollapse = 5
    const store = useSelector(state => state) // redux hook

    let announcement = props.announcement
    let creator = store.allCloudUsers.find(u => u.id === announcement.cloudid)
    let isCreator = creator?.id === store.currentCloudUser?.id

    let [editMode, setEditMode] = useState(false)
    let [announcementToEdit, setAnnouncementToEdit] = useState(props.announcement ? props.announcement : {
        title: "",
        content: ""
    });
    let group = props.group;
    let section = props.section;

    let modalRef = useRef() // for yes no modal (deleteion)

    //Likes
    let count_comments = announcement.comments?.filter((c) => {return !c.hidden}).length;
    let count_likes = announcement.likes?.length;
    let currentUserLikesThis = false
    let cloudUsersForLikes = []

    //permissions

    let [canLike, setCanLike] = useState(props.canLike || isCreator)
    let [canComment, setCanComment] = useState(props.canComment || isCreator)


    let [blockUserModalShow, setBlockUserModalShow] = useState(false);
    let [reportContentShow, setReportContentShow] = useState(false);

    useEffect(() =>
    {
        if((props.canLike || isCreator) !== canLike)
            setCanLike(props.canLike  || isCreator)
        if((props.canComment || isCreator) !== canComment)
            setCanComment(props.canComment || isCreator)
    },[props.canLike, props.canComment])

    if (Array.isArray(store.allCloudUsers) && Array.isArray(announcement?.likes)) {
        announcement.likes.forEach(like => {
            let usr = store.allCloudUsers.find(user => like.cloudid === user.id)
            if (usr?.id === store.currentCloudUser?.id)
                currentUserLikesThis = true
            else if (usr)
                cloudUsersForLikes.push(usr)
        })
    }

    if (!store.currentCloudUser)
        return <div></div>
    let imageUrl = AjaxHelper.getCloudUserAvatarUrl(creator?.id, 30, creator?.image)


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


    let updateAnnouncement = (announcement) => {

        AjaxHelper.updateAnnouncement(announcement.id, announcement.newFiles, announcement.fileIdsToDelete, announcement.content, announcement.comments_active, announcement.comments_hide)
            .then(resp => {
                if (resp.status > 0 && resp.payload) {
                    setEditMode(false)
                    props.updatedAnnouncementCallback(resp.payload.announcement)
                    setAnnouncementToEdit(resp.payload.announcement)
                    EducaHelper.fireSuccessToast("Erfolg", "Die Ankündigung wurde updated.")
                    return
                }
                throw new Error(resp.message)
            })
            .catch(err => {

                EducaHelper.fireErrorToast("Fehler", "Der Beitrag konnte nicht verändert werden. " + err.message)
            })
    }

    let deleteAnnouncementClick = () => {
        modalRef.current.open((btn) => {
            if (btn === MODAL_BUTTONS.YES) deleteAnnouncement()
        }, "Löschen", "Möchtest du die Ankündigung wirklich löschen?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO])
    }

    let deleteAnnouncement = () => {
        AjaxHelper.deleteAnnouncement(announcement.id)
            .then(resp => {
                if (resp.status > 0) {
                    props.deletedAnnouncementCallback(announcement.id);
                    return EducaHelper.fireSuccessToast("Erfolg", "Der Beitrag wurde erfolgreich gelöscht.")
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Der Beitrag konnte nicht gelöscht werden. " + err.message)
            })
    }

    // Callback from announcement card
    let newCommentOnClick = (comment) => {
        if(!canComment)
            return
        AjaxHelper.createNewCommentInAnnouncement(announcement.id, comment)
            .then(resp => {
                if (resp.payload) {
                    let newAnnouncement = resp.payload.announcement;
                    props.updatedAnnouncementCallback(newAnnouncement);
                } else
                    throw new Error("Fehler. Diese Aktion ist zur Zeit nicht möglich.")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", err.message)
            })
    }

    let likeOnClick = () => {
        if(!canLike)
            return
        AjaxHelper.likeAnnouncement(announcement.id)
            .then(resp => {
                if (resp.payload) {
                    let newAnnouncement = resp.payload.announcement;
                    props.updatedAnnouncementCallback(newAnnouncement);
                } else
                    throw new Error("Fehler. Diese Aktion ist zur Zeit nicht möglich.")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", err.message)
            })
    }


    let createCommentSection = (announcement) => {

        let latestComments = []
        let followUpComments = []
        announcement?.comments?.map((obj, num) => {
            if (num >= announcement.comments?.length - amountCommentsBeforeCollapse)
                latestComments.push(createNewComment(obj,announcement))
            else
                followUpComments.push(createNewComment(obj,announcement))
        })
        let followUpCommentsCount = followUpComments.length;
        return <Card.Footer>
            {
                followUpCommentsCount ?
                    <div><Button className="btn-light"
                                 style={{width: "100%", backgroundColor: "#e5e6eb", marginTop: "5px"}}
                                 onClick={() => setIsCommentsSectionOpen(!isCommentsSectionOpen)}>
                        {isCommentsSectionOpen ?
                            <div><i className="fas fa-chevron-down"/> Ausblenden </div> :
                            <div><i className="fas fa-chevron-up"/> {followUpCommentsCount} ältere Kommentare einblenden
                            </div>}</Button>
                        <Collapse in={isCommentsSectionOpen}>
                            <div>
                                {followUpComments.map(e => e)}
                            </div>
                        </Collapse>
                    </div> : null
            }
            {latestComments.map(e => e)}
            <div className="pt-3">
                {canComment && announcement.comments_active? <div className="form-group">
                    <label className="sr-only" htmlFor="comment">Kommentar</label>
                    <TextareaAutosize
                        maxLength={LIMITS.COMMENT_LIMIT}
                        maxRows={6}
                        minRows={3}
                        value={newComment}
                        className="form-control editor"
                        placeholder="Verfasse einen neuen Kommentar..."
                        onChange={(evt) => {
                            setNewComment(evt.target.value)
                            if(evt.target.value.length > LIMITS.COMMENT_LIMIT - 1)
                            {
                                EducaHelper.fireWarningToast("Hinweis","Das Zeichenlimit für Kommentare bei "+ LIMITS.COMMENT_LIMIT + " Zeichen");
                            }
                        }}
                    >

                    </TextareaAutosize>
                </div> : null}
                {canComment && announcement.comments_active? <div className="btn-toolbar justify-content-between align-items-end">
                    <div className="btn-group">
                        <Button
                            type="submit"
                            title={newComment === "" ? "Du musst zuerst ein Kommentare schreiben" : "Abschicken"}
                            disabled={newComment === "" || newComment.length > LIMITS.COMMENT_LIMIT}
                            className="btn btn-primary"
                            onClick={() => {
                                newComment !== "" ? newCommentOnClick(newComment) : null;
                                setNewComment("")
                            }}
                        ><i className="fas fa-comment"/> Kommentar hinzufügen</Button>
                    </div>
                </div> : null}
            </div>
        </Card.Footer>
    }

    let normalView = () => {
        return <div><Card.Header style={{backgroundColor: "white"}}>
            <div className="d-flex justify-content-between align-items-center">
                <div className="d-flex justify-content-between align-items-center">
                    <div className="mr-2">
                        <img loading={"lazy"} className="rounded-circle" width="30" src={imageUrl}
                             alt=""/>
                    </div>
                    <div className="ml-2">
                        <div className="h5 m-0"><b>{creator?.name} {group ? <>in
                            <EducaCardLinkButton
                                className={"ml-1"}
                                onClick={() => props.changeRouteCallback(BASE_ROUTES.ROOT_GROUPS + "/" + group.id, null)}>{group.name}
                            </EducaCardLinkButton></> : ""}
                            {section ? <> <i className="fas fa-arrow-right"></i>
                                <EducaCardLinkButton
                                    className={"ml-1"}
                                    onClick={() => props.changeRouteCallback(BASE_ROUTES.ROOT_GROUPS + "/" + group.id + "/sections/" + section.id + "/announcement", null)}>{section.name}
                                </EducaCardLinkButton></> : ""}

                        </b></div>
                    </div>
                </div>
                <div className="d-flex justify-content-end ">
                    <div className="d-flex text-muted h7 mr-2">
                        <div className="float-right"><i className="fa fa-clock"></i> <ReactTimeAgo
                            date={moment(announcement.created_at).toDate()} locale="de-DE"/></div>
                    </div>
                    <Dropdown className="m-1">
                    <Dropdown.Toggle as={CustomToggle} variant="outline-secondary" id="dropdown-basic" />
                    <Dropdown.Menu>
                        {FliesentischZentralrat.globalCanReport() && !isCreator ? <Dropdown.Item onClick={() => setReportContentShow(true)}><i className="far fa-flag fa-fw"></i> Spam melden</Dropdown.Item> : <></> }
                        {FliesentischZentralrat.globalCanBlock()  && !isCreator  ? <Dropdown.Item onClick={() => setBlockUserModalShow(true)}><i className="fas fa-ban fa-fw"></i>Nutzer blockieren</Dropdown.Item>: <></> }
                        {isCreator ? <Dropdown.Item
                        onClick={() => setEditMode(true)}><i className="fas fa-edit fa-fw"></i>Beitrag bearbeiten</Dropdown.Item>: <></> }
                    </Dropdown.Menu>
                </Dropdown>
                </div>
            </div>

            <EducaBlockUserModal show={blockUserModalShow} hideCallback={() => setBlockUserModalShow(false)} user={creator}/>
            <EducaReportModal show={reportContentShow} hideCallback={() => setReportContentShow(false)} content={announcement} content_type="announcement" />
        </Card.Header>
            <Card.Body>
                <div className="card-text">
                    <div dangerouslySetInnerHTML={SharedHelper.sanitizeHtml(announcement.content)}/>
                    {moment(announcement.planned_for).isAfter(moment()) ? <i>Die Ankündigung ist geplant für den { moment(announcement.planned_for).format("DD.MM.Y HH:mm") }</i> : <></>}
                </div>
                        <div className={"row"}>
                            {announcement.media?.map((obj, index) => mediaSection(obj, announcement.media.length, index))}
                        </div>
            </Card.Body>

            <Card.Footer>
                <div style={{display: "flex", flexDirection: "row"}}>

                    <div className={"mr-1"}
                         data-tip={"tooltip"}
                         data-for={"uid_ttLikes_" + announcement.id}
                         style={{cursor: ""}}>
                        {currentUserLikesThis ? "Du und " + (count_likes - 1) + " weitere " : (count_likes)}
                        <i className="far fa-thumbs-up ml-1 mr-1 mt-1"/>
                    </div>

                    {canLike? <div className={"mr-2"}>
                        <a className="card-link"
                           onClick={() => {
                               likeOnClick()
                           }}
                           style={{cursor: "pointer", color: "rgb(3, 102, 214)"}}>
                            {currentUserLikesThis ? "Gefällt mir nicht mehr" : "Gefällt mir"}
                        </a>
                    </div> : null}
                    { announcement.comments_hide ? <></> : <><a className="card-link">{count_comments} <i className="fa fa-comment"></i> Kommentar(e)</a>
                        <div className="collapse" id="comment">

                        </div></> }
                    {/* Likes Tooltip*/}
                    <ReactTooltip
                        id={"uid_ttLikes_" + announcement.id}
                        place={"bottom"}>
                        {cloudUsersForLikes.map((usr, num) => {
                            if(!usr)
                                return
                            if (num < 15)
                                return <div
                                    key={"ttLikes_" + announcement.id + "_" + usr.id + usr.name}>{usr.name}</div>
                            else if (num === 15)
                                return <div
                                    key={"ttLikes_" + announcement.id + "_" + usr.id + usr.name}>Und {cloudUsersForLikes.length - num} weitere</div>
                        })}
                    </ReactTooltip>
                </div>
            </Card.Footer>
        </div>
    }

    let editView = () => {
        return <div><Card.Header style={{backgroundColor: "white"}}>
            <div className="d-flex justify-content-between align-items-center">
                <div className="d-flex justify-content-between align-items-center">
                    <div className="mr-2">
                        <img loading={"lazy"} className="rounded-circle" width="30" src={imageUrl}
                             alt=""/>
                    </div>
                    <div className="ml-2">
                        <div className="h5 m-0"><b>{creator?.name} {group ? <> in
                            <EducaCardLinkButton
                                className={"ml-1"}
                                onClick={() => props.changeRouteCallback(BASE_ROUTES.ROOT_GROUPS + "/" + group.id, null)}>{group.name}
                            </EducaCardLinkButton></> : ""}
                            {section ? <> <i className="fas fa-arrow-right"></i>
                                <EducaCardLinkButton
                                    className={"ml-1"}
                                    onClick={() => props.changeRouteCallback(BASE_ROUTES.ROOT_GROUPS + "/" + group.id + "/section/" + section.id, null)}>{section.name}
                                </EducaCardLinkButton></> : ""}

                        </b></div>
                    </div>
                </div>
                <div className="text-muted h7">
                    <div className="float-right"><i className="fa fa-clock"></i> <ReactTimeAgo
                        date={moment(announcement.created_at).toDate()} locale="de-DE"/></div>
                </div>
            </div>

        </Card.Header>
            <Card.Body>
                <div className="card-text">
                    <CKEditor
                        config={EducaCKEditorDefaultConfig}
                        editor={window.ClassicEditor}
                        data={announcementToEdit.content}
                        onChange={(event, editor) => {
                            const data = editor.getData();
                            setAnnouncementToEdit({...announcementToEdit, content: data})
                        }}
                    />
                </div>
                {announcementToEdit.media ?
                    <div>
                        <div style={{display: "flex", flexDirection: "column"}}>
                            <div style={{display: "flex", flexDirection: "row"}}>
                                {announcementToEdit.media.map((obj, index) => {
                                    if (announcementToEdit.fileIdsToDelete?.includes(obj.id)) //already added to list of deletion
                                        return

                                    return <div key={index}
                                                style={{display: "flex", flexDirection: "row", marginRight: "5px"}}>
                                        <div style={{
                                            display: "flex",
                                            justifyContent: "flex-start",
                                            flexDirection: "column"
                                        }}>
                                            <EducaCircularButton
                                                size={"small"}
                                                variant={"danger"}
                                                onClick={() => {
                                                    let fileIdsToDelete = announcementToEdit.fileIdsToDelete ? announcementToEdit.fileIdsToDelete : []
                                                    fileIdsToDelete.push(obj.id)
                                                    setAnnouncementToEdit({
                                                        ...announcementToEdit,
                                                        fileIdsToDelete: fileIdsToDelete
                                                    })
                                                }}
                                            >
                                                <i className={"fa fa-times"}>
                                                </i>
                                            </EducaCircularButton>
                                        </div>
                                        <img loading={"lazy"} width={50} src={"/storage/" + obj.disk_name}/>
                                    </div>
                                })}
                            </div>
                            {
                                <div className={"mt-2"}>
                                    {getFileAddComponent
                                    (
                                        announcementToEdit.id,
                                        announcementToEdit.newFiles,
                                        (files) => {
                                            let newFiles = announcementToEdit.newFiles ? announcementToEdit.newFiles : []
                                            newFiles = newFiles.concat(Array.from(files))
                                            setAnnouncementToEdit({...announcementToEdit, newFiles: newFiles})
                                        },
                                        (index) => {
                                            let newFiles = announcementToEdit.newFiles
                                            newFiles.splice(index, 1)
                                            setAnnouncementToEdit({...announcementToEdit, newFiles: newFiles})
                                        }
                                    )}
                                </div>}

                        </div>
                    </div>
                    : null}
                <div className={"mt-3"}>
                    <b><i className="fas fa-wrench"></i> Optionen</b>
                    <div className={"container"}>
                        <div className={"row mt-1"}>
                            <ReactSwitch
                                uncheckedIcon={false}
                                checkedIcon={false}
                                checked={announcementToEdit.comments_active}
                                onChange={(flag) =>
                                    setAnnouncementToEdit({...announcementToEdit, comments_active: flag})}
                            />
                            <div className={"ml-1"}>
                                Kommentare zulassen
                            </div>
                        </div>
                        <div className={"row mt-2"}>
                            <ReactSwitch
                                uncheckedIcon={false}
                                checkedIcon={false}
                                checked={!announcementToEdit.comments_hide}
                                onChange={(flag) =>  setAnnouncementToEdit({...announcementToEdit, comments_hide: !flag})}
                            />
                            <div className={"ml-1 mr-1 mt-2"}>
                                Kommentare anzeigen
                            </div>
                        </div>
                    </div>
                </div>

                <div style={{display: "flex", justifyContent: "end", flexDirection: "row", flex: 1}}>
                    <Button
                        className={"btn-primary mr-1"}
                        onClick={() => {
                            updateAnnouncement(announcementToEdit)
                        }}
                    > Speichern </Button>
                    <Button
                        className={"btn-danger"}
                        onClick={() => {
                            deleteAnnouncementClick()
                        }}
                    > Löschen </Button>
                </div>
            </Card.Body>

            <Card.Footer>
                <div style={{display: "flex", flexDirection: "row"}}>

                    <div className={"mr-1"}
                         data-tip={"tooltip"}
                         data-for={"uid_ttLikes_" + announcement.id}
                         style={{cursor: ""}}>
                        {currentUserLikesThis ? "Du und " + (count_likes - 1) + " weitere " : (count_likes)}
                        <i className="far fa-thumbs-up ml-1 mr-1 mt-1"/>
                    </div>

                    <div className={"mr-2"}>
                        <a className="card-link"
                           onClick={() => {
                               likeOnClick()
                           }}
                           style={{cursor: "pointer", color: "rgb(3, 102, 214)"}}>
                            {currentUserLikesThis ? "Gefällt mir nicht mehr" : "Gefällt mir"}
                        </a>
                    </div>
                    { announcement.comments_hide ? <></> : <><a className="card-link">{count_comments} <i className="fa fa-comment"></i> Kommentar(e)</a>
                    <div className="collapse" id="comment">

                    </div></> }
                    {/* Likes Tooltip*/}
                    <ReactTooltip
                        id={"uid_ttLikes_" + announcement.id}
                        place={"bottom"}>
                        {cloudUsersForLikes.map((usr, num) => {
                            if (num < 15)
                                return <div
                                    key={"ttLikes_" + announcement.id + "_" + usr.id + usr.name}>{usr.name}</div>
                            else if (num === 15)
                                return <div
                                    key={"ttLikes_" + announcement.id + "_" + usr.id + usr.name}>Und {cloudUsersForLikes.length - num} weitere</div>
                        })}
                    </ReactTooltip>
                </div>
            </Card.Footer>
        </div>
    }


    return (
        <Card className="mt-2 announcementCard">
            {editMode ? editView() : normalView()}
            {announcement.comments_hide ? <></> : createCommentSection(announcement)}
            <EducaModal ref={modalRef}/>
        </Card>
    )
}

function mediaSection(media, length, index) {
    let style = "col-" + Math.round(12 / Math.min(3, length));
    let url = "/storage/" + media.disk_name;
//    if(index >= 3)
//        return   <img className={"d-none"} src={url} />
    return (<div key={media.id} className={style}>
            <img loading={"lazy"} className={"img-fluid img-thumbnail"} src={url}/>
        </div>
    );
}

function createNewComment(comment, announcement) {
    return <EducaComment key={comment?.id} comment={comment} announcement={announcement}/>;
}

function getFileAddComponent(someUID, files, fileAddCallback, fileRemoveCallback) {
    return <div>
        <input
            multiple
            type="file"
            id={"input_announcementCard_upload" + someUID}
            onChange={(evt) => {
                fileAddCallback(evt.target.files)
            }}
            accept="image/x-png,image/gif,image/jpeg"
            style={{width: "0px", display: "none"}}/>
        <EducaCircularButton
            size={"medium"}
            title={"Datei Anhängen"}
            className="btn btn-secondary"
            onClick={() => {
                $('#input_announcementCard_upload' + someUID).click()
            }}
            type="button"><i className="fa fa-file-medical"></i>
        </EducaCircularButton>
        {!files || files.length === 0 ? <div> Keine neuen angehängten Dateien</div>
            :
            files.map((file, num) => {
                return <AttachedFileComponent
                    key={num}
                    zebra={num % 2}
                    fileRemoveCallback={() => {
                        fileRemoveCallback(num)
                    }}
                    file={file}/>
            })
        }
    </div>
}
