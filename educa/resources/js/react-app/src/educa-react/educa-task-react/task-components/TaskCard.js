import React, {useEffect, useState} from "react"
import Card from "react-bootstrap/Card";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {EducaCardLinkButton} from "../../../shared/shared-components/Buttons";
import ReactTooltip from "react-tooltip";
import {useSelector} from "react-redux";
import ReactTimeAgo from "react-time-ago";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import EducaHelper from "../../helpers/EducaHelper";
import {Row, Col} from "react-bootstrap";
import moment from "moment";


export function TaskCard(props) {

    let [task, setTask] = useState(props.task)
    let [selected, setSelected] = useState(props.selected)
    const store = useSelector(state => state) // redux hook
    let creator = store.allCloudUsers.find(user => user.id === task.cloud_id)
    let isCurrentUserCreator = creator?.id === store.currentCloudUser.id

    useEffect(() => {
        setTask(props.task);
        setSelected(props.selected)
    }, [props.selected]);


    useEffect(() => {
        setTask(props.task);
    }, [props.task]);


    let className = "custom-task-card"
    if(isCurrentUserCreator) {
        className += " left-border-primary-color"
    }

    let cardStyle = {...props?.style}
    if(!isCurrentUserCreator) {
        cardStyle.borderLeftColor = "grey"
    }

    if(selected) {
        className += " border-selected-color"
    }
    var span = document.createElement('span');
    span.innerHTML = task.description;
    let textRemoved= span.textContent || span.innerText;

    return <Card className={className}
                 style={cardStyle}
                 onClick={() => {
                     props.taskClickCallback(task)
                 }}>
        <div className="d-flex justify-content-between align-items-center px-3 mt-2">
            <div className="d-flex justify-content-between align-items-center">
                <div className="mr-2 mt-1">
                    <img className="rounded-circle" width="30" src={AjaxHelper.getCloudUserAvatarUrl(creator?.id, 35, creator?.image)}
                         alt=""/>
                </div>
                <div className="ml-2 mt-1">
                    <div className="h6 m-0"><b>{creator?.name}</b></div>
                </div>
            </div>
            <div className="text-muted h7">
                <div className="float-right d-flex align-items-center">
                    <div className={"d-flex align-items-center"}>
                        <i className="material-icons mr-1" style={{fontSize: "1.3em"}}>access_time</i> {task.end ?
                        <ReactTimeAgo date={new Date(task.created_at)} locale="de-DE"/> : "N/A"}
                    </div>
                    <div>
                        {!isCurrentUserCreator && !task.is_submission_seen? <span className={"ml-2 bg-danger"} style={{
                        height: 12 + "px",
                        width: 12 + "px",
                        borderRadius: "50%",
                        display: "inline-block"
                    }}></span> : null}
                    </div>
                 </div>

            </div>
        </div>

        <div className="px-3 pt-3">
            <h5 className="name overflow-ellips-threelines" style={{textAlign: "left"}}>
                <b>{task.title}</b>
            </h5>
            <div className="overflow-ellips-threelines">
                {textRemoved}
            </div>
        </div>
        <Col className="mb-2 mt-2">
            <Row className="justify-content-between pr-3 pl-3 mb-1">
                {task.start != null /*&& moment().isBefore(moment(task.start))*/ ?
                    <div className="d-flex justify-content-start">
                        <img height={17} src={"/images/start_icon.png"}/><span className="quote2 pl-2"><b
                        className={"mr-1"}>Ab</b>{task.start ? moment(task.start).format("DD.MM.YYYY  HH:mm") : "N/A"}</span>
                    </div> : <div className="d-flex justify-content-start"></div>}

                {task?.sections?.length != 0 ?
                <div className="d-flex justify-content-end">
                    {getGroupFooterFromSections(task?.sections, store.currentCloudUser.groups)}
                </div> 
             : <div className="d-flex justify-content-end"></div>}
            </Row>

            <Row className="justify-content-between pr-3 pl-3">
                <div className="d-flex justify-content-start">
                    <img height={17} src={"/images/Endzeit.png"}/><span className="quote2 pl-2"><b
                    className={"mr-1"}>Frist</b>{task.end ? moment(task.end).format("DD.MM.YYYY  HH:mm") : "N/A"}</span>
                </div>

                {task?.attendees?.length != 0 ?
                    <div className="d-flex justify-content-end">
                        {getAttendeesFooter(task?.attendees)}
                    </div> : <div className="d-flex justify-content-end"></div>}
            </Row>

        </Col>

    </Card>
}

export function getAttendeesFooter(attendees) {
    return attendees?.map((a, i) => {
        if (i < 3)
            return <div
                style={{overflow: "hidden"}}
                key={a.id}
                data-for={"uid_attendees_tasks_" + a.id}
                data-tip={"tooltip"}
            >
                <img
                    src={AjaxHelper.getCloudUserAvatarUrl(a.id, 35, a.image)}
                    width="23" style={{borderRadius: "50%"}} className={"mr-1"}/>
                <ReactTooltip
                    place={"bottom"}
                    id={"uid_attendees_tasks_" + a.id}
                >
                    {a.name}
                </ReactTooltip>
            </div>
        if (i === 3) {
            return <div key={a.id} style={{overflow: "hidden"}}>
                <div
                    data-tip={"tooltip"}
                    data-for={"uid_attendees_tasks_sub" + a.id + "_" + a.name}>
                    <EducaCardLinkButton>+{attendees.length - 3}</EducaCardLinkButton></div>
                <ReactTooltip
                    place={"bottom"}
                    id={"uid_attendees_tasks_sub" + a.id + "_" + a.name}
                >
                    {attendees.slice(3, attendees.length).map(at =>
                        <div style={{display: "flex", flexDirection: "row"}} key={at.id} className={"m-1"}>
                            <img
                                src={AjaxHelper.getCloudUserAvatarUrl(at.id, 35, at.image)}
                                width="23" style={{borderRadius: "50%"}} className={"mr-1"}/>
                            <div>{at.name}</div>
                        </div>
                    )}
                </ReactTooltip>
            </div>
        }
    })
}

export function getGroupFooterFromSections(sections, allGroups) {

    let groups = EducaHelper.getGroupsForSections(sections, allGroups)

    return groups?.map((g, i) => {
        if (i < 3)
            return <div
                style={{overflow: "hidden"}}
                key={g.id}
                data-for={"uid_groups_tasks_" + g.id}
                data-tip={"tooltip"}
            >
                <img
                    src={AjaxHelper.getGroupAvatarUrl(g.id, 23, g.image)}
                    width="23" className={"mr-1"}/>
                <ReactTooltip
                    place={"bottom"}
                    id={"uid_groups_tasks_" + g.id}
                >
                    {g.name}
                </ReactTooltip>
            </div>
        if (i === 3) {
            return <div key={g.id}
                        style={{overflow: "hidden"}}>
                <div
                    data-tip={"tooltip"}
                    data-for={"uid_groups_tasks_sub" + g.id + "_" + g.name}>
                    <EducaCardLinkButton>+{groups.length - 3}</EducaCardLinkButton></div>
                <ReactTooltip
                    place={"bottom"}
                    id={"uid_groups_tasks_sub" + g.id + "_" + g.name}
                >
                    {groups.slice(3, groups.length).map(gr =>
                        <div style={{display: "flex", flexDirection: "row"}} key={gr.id} className={"m-1"}>
                            <img
                                src={AjaxHelper.getGroupAvatarUrl(gr.id, 23, gr.image)}
                                width="23" style={{borderRadius: "50%"}} className={"mr-1"}/>
                            <div>{gr.name}</div>
                        </div>
                    )}
                </ReactTooltip>
            </div>
        }
    })
}
