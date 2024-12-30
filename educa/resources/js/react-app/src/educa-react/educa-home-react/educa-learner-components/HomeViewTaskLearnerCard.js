import React, {useState} from 'react';
import Card from "react-bootstrap/Card";
import {ListGroup} from "react-bootstrap";
import {EducaCardLinkButton} from "../../../shared/shared-components/Buttons";
import {BASE_ROUTES} from "../../App";
import EducaHelper from "../../helpers/EducaHelper";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";


const MAX_TASKS = 3
export default function HomeViewTaskLearnerCard(props) {

    let [tasks, setTasks] = useState([])
    const [translate] = useEducaLocalizedStrings()

    let _isMounted = false;
    React.useEffect(() => {
        _isMounted = true
        getTasks()
        return () => {
            _isMounted = false;
        };
    }, []);


    let getTasks = () => {
        AjaxHelper.getMainFeedTasks()
            .then(resp => {
                if (resp.status > 0 && resp.payload?.tasks)
                    return _isMounted ? setTasks(resp.payload.tasks) : null
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast(translate("error","Fehler"), translate("task.error","Die Aufgaben konnten nicht geladen werden. ") + err.message)
            })
    }

    return (
        <div className="mb-2 animate__animated animate__fadeIn">
            <div className={"d-flex justify-content-between"}><h4><img style={{width: "30px", height: "30px"}}
                                     src="/images/aufgaben_launcher.png"/> {translate("tasks","Aufgaben")}</h4>
                    <EducaCardLinkButton
                        onClick={() => props.changeRoute(BASE_ROUTES.ROOT_TASKS, "")}
                        className="card-link m-1" style={{fontSize: "0.9rem"}}>
                        {translate("see_all","Alle ansehen")}
                    </EducaCardLinkButton>
            </div>
            {tasks?.length > 0 ?
            <ListGroup> {
                    tasks.map((task, index) => {
                        if (index >= MAX_TASKS)
                            return
                        return <ListGroup.Item
                            onClick={() => props.changeRoute(BASE_ROUTES.ROOT_TASKS, "?task_id=" + task.id)}
                            key={index}
                            style={{cursor: "pointer"}}>
                            <h5 style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>
                                <b>{task.title}</b></h5>
                            <div>
                                {task.end ? <> <i
                                    className="fas fa-clock"></i> {SharedHelper.getFormattedDateString(task.end)}</> : null}
                                {task.documentCount > 0 ? <><i
                                    className="fas fa-paperclip"></i> {task.documentCount} {translate("files2","Datei(en)")}</> : null}
                            </div>
                        </ListGroup.Item>
                    })} </ListGroup>
                    :
                    <i>{translate("task.no_tasks","Es gibt noch keine Aufgaben für dich.")} </i> }
        </div>);

}

