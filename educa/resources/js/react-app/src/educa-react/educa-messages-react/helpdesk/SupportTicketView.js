import React, {Component, useEffect, useState} from 'react';
import Card from "react-bootstrap/Card";
import {Picker} from "emoji-mart";
import TextInputComponent from "../../rocket-chat-components/educa-chat-react/chat-components/TextInputComponent";
import {Alert, Dropdown, Navbar} from "react-bootstrap";
import {EducaCircularButton} from "../../../shared/shared-components/Buttons";
import ReactTooltip from "react-tooltip";
import EducaHelper from "../../helpers/EducaHelper";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import moment from "moment/moment";
import {EducaLoading} from "../../../shared-local/Loading";
import FileUploadDialogRC from "../../rocket-chat-components/educa-chat-react/chat-components/FileUploadDialogRC";
import {RCHelper} from "../../rocket-chat-components/RocketChatHelper";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import Button from "react-bootstrap/Button";


const styles = {
    root: {
        marginTop: "10px",
        display: "flex",
        width: "100%",
        flex: 1,
        flexDirection: "row"
    },
    wrapper: {
        padding: "10px",
        display: "flex",
        flex: 1,
        flexDirection: "column"
    },

    from: {
        display: "flex",
        flexDirection: "row",
        fontWeight: "bold",
        marginRight: "10px",
        fontSize: "15px",
    },
    time: {
        display: "flex",
        justifyContent: "center",
        flexDirection: "column",
        fontStyle: "italic",
        fontSize: "12px",
    },
    message: {
        display: "flex",
        flexDirection: "row",
        whiteSpace: "pre-wrap",
        fontSize: "15px",
        width: "100%"
    },
    imageWrapper:
        {
            display: "flex",
            flexDirection: "column",
            justifyContent: "start",
            marginTop: "10px"
        }
}



export function SupportTicketView(props) {

    let [loading, setLoading] = useState(false)
    let [closed, setClosed] = useState(false)
    let [messages, setMessages] = useState([])
    const fileUploadRef = React.createRef();

    let loadSupportTicket = () => {
        setLoading(true)
        AjaxHelper.getSupportTicket(props.ticket.id)
            .then(resp => {
                if (resp?.payload?.messages) {
                    setMessages(resp.payload.messages);
                } else
                    throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler beim Laden des Supporttickets", err.message)
            })
            .finally(() => {
                setLoading(false)
            })
    }

    useEffect(() => {
        loadSupportTicket();
    }, []);

    useEffect(() => {
        loadSupportTicket();
    }, [props.ticket]);

    let postMessage = (msg, clearCallback) => {
        setLoading(true)
        AjaxHelper.addSupportTicketAnswer(props.ticket.id,msg)
            .then(resp => {
                if (resp?.payload?.messages) {
                    setMessages(resp.payload.messages);
                } else
                    throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler beim Erstellen der Antwort für das Supportticket", err.message)
            })
            .finally(() => {
                setLoading(false)
                clearCallback();
            })
    }

    let uploadFiles = (objs) => {
        setLoading(true)
        if (objs.length > 10)
            return console.error("You cannot upload that amount of files")

        let allPromises = objs.map((obj, id) => {
            return  AjaxHelper.uploadFileSupportTicketAnswer(props.ticket.id,obj.message,obj.file)
        })

        Promise.all(allPromises)
            .then(resp => {
                loadSupportTicket();
            })
            .catch(err => {
                console.log(err)
            }).finally(() => {
                setLoading(false)
        })

    }

    let closeTicket = () => {
        setLoading(true)
        AjaxHelper.closeSupportTicket(props.ticket.id)
            .then(resp => {
                if (resp?.payload?.messages) {
                    setMessages(resp.payload.messages);
                    setClosed(true)
                    window.location.reload();
                } else
                    throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler beim Laden des Supporttickets", err.message)
            })
            .finally(() => {
                setLoading(false)
            })
    }

    return closed ? <EducaLoading/> : (loading ? <EducaLoading/>: <div className="mt-2 col" style={{display: "flex"}}>
        <div
            className="card"
            style={{display: "flex", flex: 1, flexDirection: "column"}}
        ><Navbar sticky="top" bg="dark" variant="dark"
                    style={{borderTopLeftRadius: "0.25rem", borderTopRightRadius: "0.25rem"}}>
            <div style={{display: "flex", flexDirection: "row"}} className={"mr-auto"}>
                <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}}>
                    <div style={{display: "flex", flexDirection: "row", color: "white"}}>
                        {props.ticket.title}
                    </div>
                </div>

            </div>
            <div className={"float-right"}>
                <Button variant={"success"} onClick={closeTicket}><i className="fas fa-check"></i> Anfrage beenden</Button>
            </div>
        </Navbar><div style={{display: "flex", flex: 1, flexDirection: "column"}}>
            <div style={{display: "flex", flex: 1}}>
                <div className="col"
                     style={{
                         overflowY: "auto",
                         overflowX: "hidden",
                         wordBreak: "break-all",
                         height: "calc(100vh - 300px)"
                     }}
                     id={"chat_wrapper"}>
                    {
                        messages.length === 0 ? <p>Bisher keine Nachrichten</p> :
                           messages.map((message) => {
                               if(message.type === "message") {
                                   return <Card className={"mb-2"}><div key={message.id} style={styles.wrapper}>
                                       <div style={{display: "flex", flexDirection: "row"}}>
                                           <div style={styles.from}>{message.sender}</div>
                                           <div
                                               style={styles.time}>{moment(message.created_at).locale("de").format("DD.MM.YYYY HH:mm")}</div>
                                       </div>
                                       <div>{message.title ?
                                           <b>{message.title}<br></br></b> : <></>}{message.body}</div>
                                       <div>{message.attachments ? message.attachments.map(function (attachment) {
                                           let link = "/api/v1/support/ticket/" + props.ticket.id + "/article/" + message.id + "/attachment/" + attachment.id + "?token=" + SharedHelper.getJwt()
                                           return <a target={"_blank"} key={attachment.id} href={link}><i className="fas fa-paperclip"/> {attachment.filename}</a>
                                       }) : <></>}</div>
                                   </div></Card>
                               }
                               return <div key={message.id} style={styles.wrapper}>
                                   <Alert variant={"warning"}><b>{message.title}</b> {message.body}</Alert>
                               </div>
                           })
                    }
                </div>
            </div>
            { props.ticket.is_answer_supported ?
            <div style={{flex: "1", display: "flex", flexDirection: "column", justifyContent: "flex-end"}}>
                <Card.Footer>
                  <TextInputComponent
                        hideEmoji={true}
                        hideMic={true}
                        uniqueID={props.ticket.id}
                        clickCallback={(msg, clearCallback) => postMessage(msg, clearCallback)}
                        fileUploadOnChangeCallback={(evt) => fileUploadRef.current.open(evt.target.files, (objs) => uploadFiles(objs))}
                    />
                    <FileUploadDialogRC ref={fileUploadRef}/>
                </Card.Footer>
            </div> : <Card.Footer><Alert variant={"warning"}>Es ist keine weitere Interaktion möglich, da das Ticket über einen Kanal verschickt wurde, der keine Antworten oder nachfragen erlaubt.</Alert></Card.Footer> }
        </div>
        </div>
    </div>)
}
