import React from "react";
import {Card} from "react-bootstrap";
import ReactTimeAgo from "react-time-ago";
import "../Card.css"
import {useSelector} from 'react-redux'
import {getAttendeesFooter, getGroupFooterFromSections} from "../../educa-task-react/task-components/TaskCard";
import {EducaCardLinkButton} from "../../../shared/shared-components/Buttons";
import {BASE_ROUTES} from "../../App";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper, {MODELS} from "../../../shared/shared-helpers/SharedHelper";
import moment from "moment";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper.js";

export const CARD_EMOJIS =
    {
        SMILE: <i className="fas fa-smile"></i>,
        USERS: <i className="fas fa-users"></i>,
        INFO: <i className="fas fa-info"></i>,
    }

export const CARD_HEADING_SIZE =
    {
        BIG: "BIG",
        MEDIUM: "MEDIUM",
        SMALL: "SMALL"
    }

export const CARD_BODY_SIZE =
    {
        BIG: "BIG",
        MEDIUM: "MEDIUM",
        SMALL: "SMALL"
    }

function getSizedBodyText(text, size) {
    if (!text)
        return null
    if (!size)
        return <p>{text}</p>
    switch (size) {
        case CARD_HEADING_SIZE.BIG :
            return <p className={"lead"}>{text}</p>
        case CARD_HEADING_SIZE.MEDIUM :
            return <p>{text}</p>
        case CARD_HEADING_SIZE.SMALL :
            return <small>{text}</small>
    }
}


function getSizedHeadingText(text, size) {
    if (!text)
        return null
    if (!size)
        return <h5>{text}</h5>
    switch (size) {
        case CARD_HEADING_SIZE.BIG :
            return <h1>{text}</h1>
        case CARD_HEADING_SIZE.MEDIUM :
            return <h5>{text}</h5>
        case CARD_HEADING_SIZE.SMALL :
            return <p>{text}</p>
    }
}

function BaseColoredCard(props) {
    let emoji = getSizedHeadingText(props.emoji, props.bodySize);
    let bodyText = getSizedBodyText(props.bodyText, props.bodySize);
    let bodyComponent = props.bodyComponent;

    let headingText = getSizedHeadingText(props.headingText, props.headingSize, emoji);
    let headingComponent = props.headingComponent;

    let imagePath = props.imagePath;

    let header = headingComponent ? <div>{headingComponent} </div> : headingText ?
        <div style={{display: "flex", flexDirection: "row"}}>
            <div>{headingText}</div>
            {emoji ? <div style={{
                marginLeft: "5px",
                display: "flex",
                flexDirection: "column",
                justifyContent: "center"
            }}>{emoji}</div> : null}</div> : null
    let body = bodyComponent ? <div>{bodyComponent} </div> : bodyText ? <div dangerouslySetInnerHTML={{__html: props.bodyText}}></div> : null

    let cardBody = <div style={{display: "flex", flexDirection: "column"}}>
        {header}
        {body}
    </div>

        return (
            props.color ? <Card className="mt-2" style={{ backgroundColor: props.color }} text={props.text}>
            {imagePath ? <Card.Img variant="top" src={imagePath}/> : null}
            <Card.Body>{cardBody}</Card.Body>
        </Card> :  <Card className="mt-2" text={props.text} bg={props.bg}>
                {imagePath ? <Card.Img variant="top" src={imagePath}/> : null}
                <Card.Body>{cardBody}</Card.Body>
            </Card>
    )
}


export function EducaNoContentCard(props) {
    return (
        <Card className="mt-2">
            <Card.Img variant="top" src="/images/loading.gif"/>
            <Card.Body className="text-center">
                <h4>Noch keine Inhalte ...</h4>
            </Card.Body>
        </Card>
    )
}

export function EducaFailureCard(props) {
    return (
        <Card className="mt-2">
            <Card.Body className="text-center">
                <h4>Leider ist ein Fehler beim Laden des Feed aufgetreten ...</h4>
            </Card.Body>
        </Card>
    )
}


export function EducaCardGreen(props) {

    return <BaseColoredCard text={"light"} bg={"success"} {...props}/>
}

export function EducaCardBlack(props) {

    return <BaseColoredCard text={"light"} bg={"dark"} color={props.color} {...props}/>
}

export function EducaCardBlue(props) {

    return <BaseColoredCard text={"light"} bg={"info"} {...props}/>
}

export function EducaCardRed(props) {

    return <BaseColoredCard text={"light"} bg={"danger"} {...props}/>
}

export function EducaCardWhite(props) {

    return <BaseColoredCard text={"dark"} bg={"white"} {...props}/>
}

export function FeedCard(props) {
    let date = props.date
    let cloudUser = props.cloudUser;
    let avatar = props.avatar

    return <div>Sieht das für dich fertig aus?</div>
}


export function AppointmentCard(props) {
    var appointment = props.appointment;
    var month = moment(appointment.startDate).locale('de').format("MMM");
    var day = moment(appointment.startDate).locale('de').format("D");
    var start = moment(appointment.startDate).locale('de').format("HH:mm");
    var end = moment(appointment.endDate).locale('de').format("HH:mm");
    const [translate] = useEducaLocalizedStrings()

    var organisator = props.organisators;

    var object = " erstellt";
    if(props.typeAppointment == "update")
    {
        object = " aktualisiert";
    }

    return (
        <Card className="mt-2">
            <Card.Body>
                <div style={{display: "flex", flexDirection: "row", flex: 1}}>
                    <div className="card-text">Ein Termin wurde von
                        <b>{organisator.map((obj, i) => " " + obj.name + (i < organisator.length - 1 ? "," : " "))}</b>
                        {object}
                    </div>
                    <div style={{display: "flex", justifyContent: "flex-end", flex: 1}}>
                        <EducaCardLinkButton
                            onClick={() => {
                                props.changeRouteCallback(BASE_ROUTES.ROOT_CALENDER, "?event_id=" + appointment.id)
                            }}
                        >{translate("appointment.open","Zum Termin")} <i className={"fa fa-arrow-right"}/></EducaCardLinkButton>
                    </div>
                </div>
            </Card.Body>
            <ul className="list-group list-group-flush">
                <li className="list-group-item d-flex" style={{border: "solid 2px #000"}}>
                    <div className="text-center">
                        <h2 className="text-danger"><b>{day}.</b></h2>
                        <h6>{month}</h6>
                    </div>
                    <div className="ml-2 m-1">
                        <h5><b>{appointment.title}</b></h5>
                        <div><i className="fas fa-clock"></i> {start} - {end} {locationDislay(appointment.location)}
                        </div>
                    </div>
                </li>
            </ul>
        </Card>)
}


export function EducaSupportCard(props) {
    let datum = moment(props.supportTicket?.created_at).locale('de').format("DD.MM.YYYY");
    let ticketNr = props.additionalInformation?.number;

    return (
        <Card className="mt-2">

            <ul className="list-group list-group-flush">
                <li className="list-group-item d-flex" style={{border: "solid 2px #000"}}>
                    <div className="text-center">
                        <i className="fas fa-life-ring fa-3x" />
                    </div>
                    <div className="ml-2 m-1">
                        <h5><b>Es gibt eine Rückmeldung zu deiner Supportanfrage</b></h5>
                        <div className={"text-muted"}><i className="fas fa-calendar-alt"></i> {datum}</div>
                        <div className={"text-muted"}>Ticket-Nr. #{ticketNr}</div>
                    </div>
                </li>
            </ul>
            <Card.Body>
                <div style={{display: "flex", justifyContent: "flex-end", flex: 1}}>
                    <EducaCardLinkButton
                        onClick={() => {
                            props.changeRouteCallback(BASE_ROUTES.ROOT_MESSAGES, "?chat_type=helpdesk&ticket_id=" + props.supportTicket?.id)
                        }}
                    ><h5>Zum Support <i className={"fa fa-arrow-right"}/></h5> </EducaCardLinkButton>
                </div>
            </Card.Body>
        </Card>
    )
}



function locationDislay(location) {
    if (location != "") {
        return <div><i className="fas fa-map-marker-alt"></i> {location}</div>
    }
    return <div></div>
}


export function TaskCard(props) {
    let creator = props.creator;
    if(creator == null)
        return <></>;
    let task = props.task;
    if(task == null)
        return <></>;
    const [translate] = useEducaLocalizedStrings()
    const store = useSelector(state => state) // redux hook
    AjaxHelper.getCloudUserAvatarUrl(creator.id, 30, creator.image)
    let imageUrl = AjaxHelper.getCloudUserAvatarUrl(creator.id, 30, creator.image)

    return (<Card className="mt-2 taskCard">
        <Card.Body style={{borderBottom: "1px solid rgba(0, 0, 0, 0.125)"}}>
            <div style={{display: "flex", flexDirection: "row", flex: 1}}>
                <div className="card-text">
                    Eine neue Aufgabe wurde von <b>{creator.name}</b> erstellt:
                </div>
                <div style={{display: "flex", justifyContent: "flex-end", flex: 1}}>
                    <EducaCardLinkButton
                        onClick={() => {
                            props.changeRouteCallback(BASE_ROUTES.ROOT_TASKS, "?task_id=" + task.id)
                        }}
                    >{translate("task.open","Zur Aufgabe")} <i className={"fa fa-arrow-right"}/></EducaCardLinkButton>
                </div>
            </div>
        </Card.Body>
        <div className="d-flex justify-content-end px-3 pt-1">
        </div>
        <div className="d-flex justify-content-between align-items-center px-3">
            <div className="d-flex justify-content-between align-items-center">
                <div className="mr-2">
                    <img loading={"lazy"} className="rounded-circle" width="30" src={imageUrl}
                         alt=""/>
                </div>
                <div className="ml-2">
                    <div className="h5 m-0"><b>{creator.name}</b></div>
                </div>
            </div>
            <div className="text-muted h7">
                <div className="float-right"><i className="fa fa-clock"></i> <ReactTimeAgo
                    date={moment(task.created_at).toDate()} locale="de-DE"/></div>
            </div>
        </div>

        <div className="px-3 pt-3">
            <h3 className="name" style={{textAlign: "left"}}><b>{task.title}</b></h3>
            <div dangerouslySetInnerHTML={SharedHelper.sanitizeHtml(task.description)}></div>
        </div>

        <div className="d-flex justify-content-between px-3 align-items-center pb-2 pt-2">
            <div className="d-flex justify-content-start align-items-center">
                {task.handIn ? <><i className="fas fa-paperclip"></i> <span
                    className="quote2 pl-2"> Abgabe erforderlich </span></> : null}
            </div>
            <div className="d-flex justify-content-end">
                {getAttendeesFooter(task?.attendees)}
            </div>
        </div>
        <div className="d-flex justify-content-between px-3 align-items-center pb-3">
            <div className="d-flex justify-content-start align-items-center">
                <i
                    className="fas fa-history"></i><span className="quote2 pl-2"><b
                className={"mr-1"}>Frist</b>{task.end ? moment(task.end).format("DD.MM.YYYY") : "N/A"}</span>
            </div>

            <div className="d-flex justify-content-end">
                {getGroupFooterFromSections(task?.sections, store.currentCloudUser.groups)}
            </div>
        </div>
    </Card>)
}

function imageTask(user, userType) {
    if (userType) {
        let imageUrl = "/api/image/cloud/?cloud_id=" + user.id + "&size=30";
        return <img
            loading={"lazy"}
            src={imageUrl}
            key={user.id}
            width="20" className="img1 rounded-circle m-1"/>;
    }
    let imageUrl = "/api/image/group/?id=" + user.id + "&size=30";
    return <img
        loading={"lazy"}
        src={imageUrl}
        key={user.id}
        width="20" className="img1 m-1"/>;
}

function attachmentDislay(type) {
    if (type === "no") {
        return <div></div>
    }
    return (<div><i
        className="fas fa-paperclip"/> <span className="quote2 pl-2">Abgabe erforderlich</span>
    </div>)
}


export function TaskSubmittedCard(props) {
    var user = props.user;
    var task = props.task;
    var submission = props.submission;

    if(task == null || user == null)
        return <></>

    return (
        <Card className="mt-2" bg="primary" text="light">
            <div className="row no-gutters">
                <div className="col-md-2 d-flex align-items-center justify-content-center">
                    <i className="fas fa-clipboard-check fa-3x"></i>
                </div>
                <div className="col-md-10">
                    <Card.Body>
                        <EducaCardLinkButton style={{float: "right"}} color={"#fff"} colorHover={"rgb(108, 117, 125)"}
                                             className={"m-1"}
                                             onClick={() => {
                                                 props.changeRouteCallback(BASE_ROUTES.ROOT_TASKS, "?task_id=" + task.id)
                                             }}
                        >Zur Aufgabe <i className={"fa fa-arrow-right"}/></EducaCardLinkButton>
                        <div className={"clearfix"}></div>
                        <h5 className="card-title">{user.name} hat eine Antwort zur Aufgabe '{task.title}'
                            eingereicht.</h5>
                        <p className="card-text">Die Antwort von {user.name} kann nun bewertet werden.</p>
                        <p className="card-text"><small><ReactTimeAgo date={moment(submission.updated_at).toDate()}
                                                                      locale="de-DE"/></small></p>


                    </Card.Body>
                </div>
            </div>
        </Card>
    )
}


export function TaskRatedCard(props) {

    var user = props.user;
    var task = props.task;
    var submission = props.submission;

    if(task == null || user == null)
        return <></>

    return (
        <Card className="mt-2" bg="success" text="light">
            <div className="row no-gutters">
                <div className="col-md-2 d-flex align-items-center justify-content-center">
                    <i className="fas fa-star-half-alt fa-3x"></i>
                </div>
                <div className="col-md-10">
                    <Card.Body>
                        <EducaCardLinkButton style={{float: "right"}} color={"#fff"} colorHover={"rgb(108, 117, 125)"}
                                             className={"m-1"}
                                             onClick={() => {
                                                 props.changeRouteCallback(BASE_ROUTES.ROOT_TASKS, "?task_id=" + task.id)
                                             }}
                        >Zur Aufgabe <i className={"fa fa-arrow-right"}/></EducaCardLinkButton>
                        <div className={"clearfix"}></div>
                        <h5 className="card-title">{user.name} hat deine Antwort zur Aufgabe '{task.title}'
                            bewertet.</h5>
                        <p className="card-text">Deine Antwort wurde von {user.name} bewertet und das Ergebnis kann in
                            den Aufgaben eingesehen werden.</p>
                        <p className="card-text"><small><ReactTimeAgo date={moment(submission.updated_at).toDate()}
                                                                      locale="de-DE"/></small></p>


                    </Card.Body>
                </div>
            </div>
        </Card>
    )
}

export function GroupCard(props) {
    var group = props.group;
    var headText = "Es wurde eine neue Gruppe '" + group.name + "' erstellt.";
    var bodyText = "Du wurdest zu der Gruppe '" + group.name + "' hinzugefügt.";
    return <BaseColoredCard text={"light"} bg={"dark"} headingText={headText} bodyComponent={
        <>{bodyText}
            <EducaCardLinkButton style={{float: "right"}} color={"#fff"} colorHover={"rgb(108, 117, 125)"}
                                 className={"m-1"}
                                 onClick={() => {
                                     props.changeRouteCallback(BASE_ROUTES.ROOT_GROUPS + "/" + group.id, null)
                                 }}
            >Zur Gruppe <i className={"fa fa-arrow-right"}/></EducaCardLinkButton></>}
    />;
}

export function DocumentCard(props) {
    const me = useSelector(s => s.currentCloudUser)
    const [translate] = useEducaLocalizedStrings()

    let modelName = "einem unbekannten Objekt";
    let modelInformation = props.modelInformation;
    let user = props.user;
    let document = props.document;
    let buttonText = translate("task.open", "Zur Aufgabe");
    let routeParams = ["",""]

    let headingText = ""

    if(user == null || document == null)
        return <></>

    if (modelInformation.model_type === MODELS.SECTION) {
        buttonText = translate("section.open", "Zum Bereich");
        modelName = "im Bereich '" + props.model.name + "'";
        routeParams = [BASE_ROUTES.ROOT_GROUPS + "/" + props.model?.group_id + "/sections/" + props.modelInformation?.model_id + "/files"];

        let group = me.groups.find((group) => group.id === props.model.group_id)
        headingText = user.name + " hat die Datei '" + (document.name ?? "") + "' in der Gruppe '" + (group?.name ?? "") + "' " + modelName + " geändert";
    } else if (modelInformation.model_type === MODELS.CALENDAR) {
        buttonText = translate("appointment.open", "Zum Termin");
        modelName = "dem Termin '" + props.model.title + "'";
        routeParams = [BASE_ROUTES.ROOT_CALENDER, "?event_id=" + props.modelInformation.model_id]

        headingText = user.name + " hat die Datei '" + (document.name ?? "") + "' in " + modelName + " geändert";
    } else if (modelInformation.model_type === MODELS.TASK) {
        buttonText = translate("task.open", "Zur Aufgabe");
        modelName = "der Aufgabe '" + props.model.title + "'";
        routeParams = [BASE_ROUTES.ROOT_TASKS, "?task_id=" + props.modelInformation.model_id]

        headingText = user.name + " hat die Datei '" + (document.name ?? "") + "' in " + modelName + " geändert";
    }


    return <Card className="mt-2" bg="light" text="dark">
        <div className="row no-gutters">
            <div className="col-md-2 d-flex align-items-center justify-content-center">
                <i className="far fa-file-alt fa-3x"></i>
            </div>
            <div className="col-md-10">
                <Card.Body>
                    <div className={"clearfix"}></div>
                    <h5 className="card-title">{headingText}</h5>
                    {/*<p className="card-text">{bodyText}</p>*/}
                    <p className="card-text"><small className={"text-muted"}><ReactTimeAgo
                        date={moment(document.updated_at).toDate()} locale="de-DE"/></small></p>
                </Card.Body>
                <div style={{padding : "1.25rem", display :"flex", justifyContent : "flex-end"}}>
                    {buttonText? <EducaCardLinkButton style={{float: "right"}}
                                                      className={"m-1"}
                                                      onClick={() => {
                                                          props.changeRouteCallback(routeParams[0], routeParams[1])
                                                      }}
                    ><div className="h5 m-0">{buttonText} <i className={"fa fa-arrow-right"}/></div></EducaCardLinkButton> : null}
                </div>
            </div>
        </div>
    </Card>;
}



export function TaskResetCard(props) {

    var user = props.user;
    var task = props.task;
    var submission = props.submission;

    if(task == null || user == null || submission == null)
        return <></>;

    return (
        <Card className="mt-2" bg="primary" text="light">
            <div className="row no-gutters">
                <div className="col-md-12">
                    <Card.Body>
                        <div className={"clearfix"}></div>
                        <h5 className="card-title">{user.name} hat die Aufgabe '{task.title}' nochmal geöffnet. Du kannst sie wieder bearbeiten.</h5>
                        <p className="card-text"><small><ReactTimeAgo date={moment(submission.updated_at).toDate()}
                                                                      locale="de-DE"/></small></p>


                    </Card.Body>
                    <div style={{padding : "1.25rem", display :"flex", justifyContent : "flex-end"}}>
                        <EducaCardLinkButton style={{float: "right"}} color={"#fff"} colorHover={"rgb(108, 117, 125)"}
                                             className={"m-1"}
                                             onClick={() => {
                                                 props.changeRouteCallback(BASE_ROUTES.ROOT_TASKS, "?task_id=" + task.id)
                                             }}
                        ><div className="h5 m-0">Zur Aufgabe <i className={"fa fa-arrow-right"}/></div></EducaCardLinkButton>
                    </div>
                </div>
            </div>
        </Card>
    )
}

