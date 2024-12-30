import React, { useEffect, useRef, useState } from "react";
import Card from "react-bootstrap/Card";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {
    EducaCardLinkButton,
    EducaCircularButton
} from "../../../shared/shared-components/Buttons";
import ReactTooltip from "react-tooltip";
import { useSelector } from "react-redux";
import ReactTimeAgo from "react-time-ago";
import SharedHelper, {
    MODELS
} from "../../../shared/shared-helpers/SharedHelper";
import Button from "react-bootstrap/Button";
import TaskEditorModal from "./TaskEditorModal";
import EducaFileBrowser from "../../educa-components/EducaFileBrowser/EducaFileBrowser";
import { BASE_ROUTES } from "../../App";
import { withRouter } from "react-router";
import EducaHelper from "../../helpers/EducaHelper";
import Accordion from "react-bootstrap/Accordion";
import EducaModal, {
    MODAL_BUTTONS
} from "../../../shared/shared-components/EducaModal";
import { TASK_STATES } from "../EducaTaskViewReact";
import moment from "moment";

function TaskDetailCard({submission, ...props}) {
    let [task, setTask] = useState(props.task);
    const store = useSelector(state => state); // redux hook
    let creator = store.allCloudUsers.find(user => user.id === task.cloud_id);
    let editorModalRef = useRef();
    let educaModalRef = useRef();

    useEffect(() => {
        setTask(props.task);
    }, [props.task]);

    let changeRoute = (path, search) => {
        props.history.push({
            pathname: path,
            search: search
        });
    };

    let closeTasks = task => {
        AjaxHelper.closeTask(task.id)
            .then(resp => {
                if (resp.status > 0) {
                    EducaHelper.fireSuccessToast(
                        "Aufgabe beendet",
                        "Die Aufgabe wurde beendet"
                    );
                    props.reloadTasksCallback(resp.payload.task);
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Die Aufgabe konnte nicht beendet werden." + err.message
                );
            });
    };

    return (
        <div>
            <Card
                className="mt-2"
                style={{ ...props?.style }}
                onClick={() => {
                    props.taskClickCallback
                        ? props.taskClickCallback(task)
                        : null;
                }}
            >
                <div className="d-flex justify-content-between align-items-center px-3 mt-2">
                    <div className="d-flex justify-content-between align-items-center">
                        <div className="mr-2 mt-1">
                            <img
                                className="rounded-circle"
                                width="30"
                                src={AjaxHelper.getCloudUserAvatarUrl(
                                    creator?.id,
                                    35,
                                    creator?.image
                                )}
                                alt=""
                            />
                        </div>
                        <div className="ml-2 mt-1">
                            <div className="h5 m-0">
                                <b>{creator?.name}</b>
                            </div>
                        </div>
                    </div>
                    <div className="text-muted h7">
                        <div className="float-right">
                            {creator?.id === store.currentCloudUser.id ? (
                                <>
                                    <EducaCircularButton
                                        className="mr-2"
                                        variant={"secondary"}
                                        onClick={() =>
                                            editorModalRef.current?.open(
                                                props.task
                                            )
                                        }
                                    >
                                        <i className="fas fa-pencil-alt"></i>
                                    </EducaCircularButton>
                                </>
                            ) : (
                                <EducaCircularButton
                                    size={"small"}
                                    title={"Frage Stellen"}
                                    onClick={() =>
                                        changeRoute(
                                            BASE_ROUTES.ROOT_MESSAGES,
                                            "?message_to=" + creator?.id
                                        )
                                    }
                                    className="btn btn-secondary mr-2"
                                >
                                    <i className="fas fa-question"></i>
                                </EducaCircularButton>
                            )}
                            <i className="fa fa-clock"></i>{" "}
                            {task.end ? (
                                <ReactTimeAgo
                                    date={new Date(task.created_at)}
                                    locale="de-DE"
                                />
                            ) : (
                                "N/A"
                            )}
                        </div>
                    </div>
                </div>
                <div className="px-3 pt-3">
                    <h3 className="name" style={{ textAlign: "left" }}>
                        <b>{task.title}</b>
                    </h3>
                    <div
                        className=""
                        dangerouslySetInnerHTML={SharedHelper.sanitizeHtml(
                            task.description
                        )}
                    ></div>
                    {task.privatenote ? (
                        <Accordion>
                            <Card>
                                <Card.Header>
                                    <Accordion.Toggle
                                        as={Button}
                                        variant="link"
                                        eventKey="0"
                                    >
                                        <img
                                            src={
                                                "/images/task_icons/Aufgabe_Notizen_neu_schwarz.png"
                                            }
                                            height={20}
                                        />{" "}
                                        private Notiz{" "}
                                    </Accordion.Toggle>
                                </Card.Header>
                                <Accordion.Collapse eventKey="0">
                                    <Card.Body>{task.privatenote}</Card.Body>
                                </Accordion.Collapse>
                            </Card>
                        </Accordion>
                    ) : (
                        <></>
                    )}
                    {task.documentCount > 0 ? (
                        <>
                            <div className="bg-light mt-2">
                                <EducaFileBrowser
                                    modelType={MODELS.TASK}
                                    modelId={task.id}
                                    canUserEdit={
                                        creator?.id ===
                                        store.currentCloudUser.id
                                    }
                                    canUserUpload={
                                        creator?.id ===
                                        store.currentCloudUser.id
                                    }
                                />
                            </div>
                        </>
                    ) : (
                        <></>
                    )}
                </div>

                <div className="d-flex flex-row justify-content-between pt-3">
                    <div className="d-flex flex-column align-items-start px-3 pb-3">
                        {task.start !=
                        null /*&& moment().isBefore(moment(task.start))*/ ? (
                            <div className="d-flex justify-content-start align-items-center pl-1">
                                <i className="fas fa-hourglass-start"></i>
                                <span className="quote2 pl-2">
                                    <b className={"mr-1"}>Ab</b>
                                    {task.start
                                        ? moment(task.start).format(
                                            "DD.MM.YYYY HH:mm"
                                        )
                                        : "N/A"}
                                </span>
                            </div>
                        ) : null}

                        <div className="d-flex justify-content-start align-items-center">
                            <img src={"/images/Endzeit.png"} height={17} />
                            <span className="quote2 pl-2">
                                <b className={"mr-1"}>Frist</b>
                                {task.end
                                    ? moment(task.end).format(
                                        "DD.MM.YYYY HH:mm"
                                    )
                                    : "N/A"}
                            </span>
                        </div>
                    </div>

                    <div className="d-flex flex-column align-items-end px-3 pb-3">
                        <div className="d-flex justify-content-end pb-3">
                            {getAttendeesFooter(task?.attendees)}
                        </div>

                        <div className="d-flex justify-content-end">
                            {getGroupFooterFromSections(
                                task?.sections,
                                store.currentCloudUser.groups
                            )}
                        </div>
                    </div>
                </div>

                {(task.state === TASK_STATES.DRAFT ||
                    task.state === TASK_STATES.REVIEW) &&
                creator?.id === store.currentCloudUser.id ? (
                    <div className="d-flex justify-content-end pb-3 px-2">
                        <Button
                            variant={"danger"}
                            className={"btn-warn mr-2"}
                            onClick={() => {
                                educaModalRef?.current?.open(
                                    btn =>
                                        btn === MODAL_BUTTONS.YES
                                            ? closeTasks(props.task)
                                            : null,
                                    "Aufgabe beenden",
                                    "Möchtest du die Aufgabe wirklich beenden?",
                                    [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
                                );
                            }}
                        >
                            Aufgabe beenden
                        </Button>
                    </div>
                ) : null}
            </Card>
            <TaskEditorModal
                isNewTask={false}
                taskChangedCallback={task => {
                    props.reloadTasksCallback(task);
                }}
                ref={editorModalRef}
            />
            <EducaModal ref={educaModalRef} />
        </div>
    );
}

export function getAttendeesFooter(attendees) {
    return attendees?.map((a, i) => {
        if (i < 5)
            return (
                <div
                    key={a.id}
                    data-for={"uid_attendees_tasks_" + a.id}
                    data-tip={"tooltip"}
                >
                    <img
                        src={AjaxHelper.getCloudUserAvatarUrl(
                            a.id,
                            35,
                            a.image
                        )}
                        width="23"
                        style={{ borderRadius: "50%" }}
                        className={"mr-1"}
                    />
                    <ReactTooltip
                        place={"bottom"}
                        id={"uid_attendees_tasks_" + a.id}
                    >
                        {a.name}
                    </ReactTooltip>
                </div>
            );
        if (i === 5) {
            return (
                <div key={a.id}>
                    <div
                        data-tip={"tooltip"}
                        data-for={
                            "uid_attendees_tasks_sub" + a.id + "_" + a.name
                        }
                    >
                        <EducaCardLinkButton>
                            +{attendees.length - 5}
                        </EducaCardLinkButton>
                    </div>
                    <ReactTooltip
                        place={"bottom"}
                        id={"uid_attendees_tasks_sub" + a.id + "_" + a.name}
                    >
                        {attendees.slice(5, attendees.length).map(at => (
                            <div
                                style={{
                                    display: "flex",
                                    flexDirection: "row"
                                }}
                                key={at.id}
                                className={"m-1"}
                            >
                                <img
                                    src={AjaxHelper.getCloudUserAvatarUrl(
                                        at.id,
                                        35,
                                        at.image
                                    )}
                                    width="23"
                                    style={{ borderRadius: "50%" }}
                                    className={"mr-1"}
                                />
                                <div>{at.name}</div>
                            </div>
                        ))}
                    </ReactTooltip>
                </div>
            );
        }
    });
}

export function getGroupFooterFromSections(sections, allGroups) {
    let groups = EducaHelper.getGroupsForSections(sections, allGroups);

    return groups?.map((g, i) => {
        if (i < 5)
            return (
                <div
                    key={g.id}
                    data-for={"uid_groups_tasks_" + g.id}
                    data-tip={"tooltip"}
                >
                    <img
                        src={AjaxHelper.getGroupAvatarUrl(g.id, 23, g.image)}
                        width="23"
                        className={"mr-1"}
                    />
                    <ReactTooltip
                        place={"bottom"}
                        id={"uid_groups_tasks_" + g.id}
                    >
                        {g.name}
                    </ReactTooltip>
                </div>
            );
        if (i === 5) {
            return (
                <div key={g.id}>
                    <div
                        data-tip={"tooltip"}
                        data-for={"uid_groups_tasks_sub" + g.id + "_" + g.name}
                    >
                        <EducaCardLinkButton>
                            +{groups.length - 5}
                        </EducaCardLinkButton>
                    </div>
                    <ReactTooltip
                        place={"bottom"}
                        id={"uid_groups_tasks_sub" + g.id + "_" + g.name}
                    >
                        {groups.slice(5, groups.length).map(gr => (
                            <div
                                style={{
                                    display: "flex",
                                    flexDirection: "row"
                                }}
                                key={gr.id}
                                className={"m-1"}
                            >
                                <img
                                    src={AjaxHelper.getGroupAvatarUrl(
                                        gr.id,
                                        23,
                                        gr.image
                                    )}
                                    width="23"
                                    style={{ borderRadius: "50%" }}
                                    className={"mr-1"}
                                />
                                <div>{gr.name}</div>
                            </div>
                        ))}
                    </ReactTooltip>
                </div>
            );
        }
    });
}

export default withRouter(TaskDetailCard);
