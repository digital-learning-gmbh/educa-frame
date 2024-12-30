import React, {Component, useEffect, useState} from 'react';
import {connect, useSelector} from "react-redux";
import SharedHelper, {MODELS} from "../../../shared/shared-helpers/SharedHelper";
import Button from "react-bootstrap/Button";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper";
import MutedParagraph from "../../educa-home-react/educa-learner-components/MutedParagraph";
import {Card, Carousel, ListGroup} from "react-bootstrap";
import {EducaShimmer} from "../../../shared/shared-components/EducaShimmer";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";


const MAX_TASKS = 3
const SectionTasksPreview = ({section, app, openApp}) => {

    const [isMounted, setIsMounted] = useState(false)
    const [translate] = useEducaLocalizedStrings()
    const [tasks, setTasks] = useState(undefined)

    useEffect(() => {
        setIsMounted(true)
        return () => setIsMounted(false)
    }, [])

    useEffect(() => {
        if (section.id > 0)
            loadEvents()
    }, [section]);

    function loadEvents() {

        AjaxHelper.getSectionTasks(section.id)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.tasks) {
                    setTasks(resp.payload.tasks)
                    return
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Fehler bei der Aufgaben Übertragung. Servernachricht: " + err.message)
                if (isMounted) setTasks([])
            })
    }

    if (!isMounted)
        return <></>
    if(tasks === undefined)
        return <EducaShimmer/>
    if(!tasks || tasks.length == 0)
        return <Card className={"m-2 p-2"}>
            <div>
                <MutedParagraph><i className="fas fa-info-circle"></i>{translate("group_view.no_task","Bisher gibt es keine Aufgaben in diesem Bereich.")}</MutedParagraph>
                <Button variant={"outline-dark"} onClick={() => openApp(app)}>{translate("group_view.open_task","Aufgaben \u00f6ffnen")}</Button>
            </div>
        </Card>
    return (
        <div className="mb-2 animate__animated animate__fadeIn">
            <ListGroup> {
                    tasks.map((task, index) => {
                        if (index >= MAX_TASKS)
                            return
                        return <ListGroup.Item
                            onClick={() => openApp(app)}
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
        </div>
    )

}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(SectionTasksPreview);
