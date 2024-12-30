import React, {useEffect, useState} from "react";
import {TaskCard} from "./TaskCard";
import TaskDetailCard from "./TaskDetailCard";
import {useSelector} from "react-redux";
import TaskStudentView from "./TaskStudentView";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import {TaskAdminView} from "./TaskAdminView";
import EducaHelper from "../../helpers/EducaHelper";


export function TaskDetailSplitView({forceTaskRefresh, ...props}) {
    let [activeTask, setActiveTask] = useState(props.initialTask ? props.initialTask : {})
    let [submission, setSubmission] = useState(null) // student
    let [submissions, setSubmissions] = useState(null) // admin
    let [tasks, setTasks] = useState(props.tasks ? props.tasks : [])
    let store = useSelector(state => state)

    const isCurrentUserCreator = store?.currentCloudUser?.id && store?.currentCloudUser?.id === activeTask?.cloud_id

    useEffect(() => {
        if (!props.tasks)
            return setTasks([])
        setTasks(props.tasks)

        // update active task
        if (activeTask?.id) {
            let task = props.tasks.find(task => task.id === activeTask.id)
            if (task) {
                setActiveTask(task)
            }

        }
    }, [props.tasks?.map( t => t?.id+t?.is_submission_seen)?.join("")])

    // if the selection of tasks changed, notify parent
    useEffect(() => {
        if (props.selectedTaskChanged)
            props.selectedTaskChanged(activeTask)
    }, [activeTask])

    useEffect(() => {
        if (!props.initialTask)
            return setActiveTask({})
        else if (activeTask && props.initialTask.id === activeTask?.id)
            return
        switchTask(props.initialTask)
    }, [props.initialTask])

    // If active task changed, fetch task details
    useEffect(() => {
        if (!activeTask.id)
            return
        AjaxHelper.getTaskDetails(activeTask.id)
            .then(resp => {
                if(!isCurrentUserCreator && !activeTask?.is_submission_seen)
                {
                    props.taskChangedCallback(resp.payload.task) // reload tasks in parent so let the unseen-marker disappear
                    switchTask(resp.payload.task)
                }

                if (resp?.payload?.submission) { // Student view = submissions
                    setSubmission(resp.payload.submission)
                } else if (resp.payload?.submissions) { //adminview = submissions
                    setSubmissions(resp.payload.submissions)
                } else
                    throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Die Details der Aufgabe konnten nicht vom Server geholt werden. " + err.message)
            })

    }, [activeTask])

    let switchTask = (switchedTask) => {
        window.scrollTo(0, 0)

        setActiveTask(switchedTask)

        let newTasks = tasks.filter(function(task) {
            return task.id !== switchedTask.id
        })
        newTasks.unshift(switchedTask)
        setTasks(newTasks)
    }

    let onTaskChanged = (task) => {
        if(task.deleteMe)
            setActiveTask({})
        props.taskChangedCallback(task)
    }

    let onSubmissionChanged = (submission, fromAdminView) => {
        if (!submission)
            return SharedHelper.logError("Submission object is empty.")

        let task = tasks.find(t => t.id === submission.task_id)

        //update submission, but not submissions. From student view
        if (!fromAdminView) {
            if (task) {
                task.state = submission.stage
            }
            setSubmission(submission)
            return onTaskChanged(task)
        } else { // iterate over submissions. (From Admin view)
            for (let i = 0; i < submissions?.length; i++) {
                if (submission.id === submissions[i].id) {
                    let subs = Array.from(submissions)
                    subs[i] = submission // Set new submission coming from child
                    setSubmissions(subs)
                    if (task) {
                        task.submissions = submissions
                        return onTaskChanged(task)
                    }
                    break
                }
            }
        }

        if (task && !fromAdminView) {

        }

        SharedHelper.logError("Submission updated in component, but task is null. Cannot update in parent.")

    }


    const openTasks = props.tasks?.reduce( (prev,curr) => prev+(curr?.is_submission_seen? 0 : 1) ,0)
    let leftSide =
        <div className={"col-4"}>
            <b>Ungelesene Aufgaben: {openTasks}</b>
            <div>
                {tasks.map(task => {
                    return <TaskCard
                        key={task.id}
                        task={task}
                        selected={task.id === activeTask.id}
                        taskClickCallback={(task) => {
                            switchTask(task)
                        }}/>
                })}
            </div>

        </div>

    let rightSide =
        <div className={"col-8"}>
            {activeTask.id ? <>
                    <TaskDetailCard
                        key={activeTask.id}
                        submission={submission}
                        reloadTasksCallback={(task) => onTaskChanged(task)}
                        task={activeTask}/>
                    {props.viewForCloudId ? <p className={"m-2"}>Diese Ansicht ist in der Nutzeransicht nicht sichtbar.</p> :
                       <> {!isCurrentUserCreator && submission ?
                        <TaskStudentView
                        submissionSubmitCallback={(submission) => onSubmissionChanged(submission, false)}
                        submission={submission}
                        task={activeTask}
                        /> : null}
                    {isCurrentUserCreator && submissions ?
                        <TaskAdminView
                        submissionsChangedCallback={(submission) => onSubmissionChanged(submission, true)}
                        submissions={submissions}
                        task={activeTask}  /> : null}</>
                    }
                </>
                :
                <div style={{display: "flex", flexDirection: "row", justifyContent: "center"}}>
                    <h5>Klick auf eine Aufgabe, um den Aufgabeninhalt anzusehen.</h5>
                </div>
            }
        </div>


    return <div className={"row"}>
        {leftSide}
        {rightSide}
    </div>

}
