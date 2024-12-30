import React from 'react';
import Button from "react-bootstrap/Button";
import Table from 'react-bootstrap/Table';
import Modal from "react-bootstrap/Modal";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {EducaCircularButton} from "../../../shared/shared-components/Buttons";
import EducaHelper from "../../helpers/EducaHelper";
import TaskEditorModal from "./TaskEditorModal";
import TaskTemplateEditorModal from "./TaskTemplateEditorModal";
import _ from "lodash";
import EducaModal, {MODAL_BUTTONS} from "../../../shared/shared-components/EducaModal";
import {Alert} from "react-bootstrap";


export default class TaskTemplateSelectorModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            taskTemplates: [],
            isOpen: false
        }
        this.taskTemplateEditorRef = React.createRef()
        this.taskEditorRef = React.createRef()
        this.educaModalRef = React.createRef()
    }


    open() {
        this.setState({isOpen: true})
        this.loadTaskTemplates()

    }

    close() {
        this.setState({isOpen: false, taskTemplates: []})
    }

    loadTaskTemplates() {
        AjaxHelper.getTaskTemplates()
            .then(resp => {
                if (resp.status > 0 && resp.payload?.taskTemplates) {
                    this.setState({
                        taskTemplates: resp.payload.taskTemplates
                    })
                    return;
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Konnte Aufgabenvorlagen nicht laden. Servernachricht: " + err.message)
                this.setState({taskTemplates: []})
            })
    }

    createTaskFromTemplate(template) {
        AjaxHelper.createTaskFromTemplate(template.id)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.task) {
                    this.taskEditorRef.current?.open(resp.payload.task);
                    this.close()
                }
                throw new Error(resp.message)
            })
    }

    deleteTemplate(template)
    {
        let delExec = () =>
        {
            AjaxHelper.deleteTaskTemplate(template.id)
                .then(resp => {
                    if (resp.status > 0) {
                        EducaHelper.fireSuccessToast("Erfolg", "Die Vorlage wurde erfolgreich gelöscht.")
                        let arr = _.cloneDeep(this.state.taskTemplates)
                        arr.splice( arr.findIndex(t => template.id == t.id),1)
                        this.setState({
                            taskTemplates: arr
                        })
                        return;
                    }
                    throw new Error(resp.message)
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Das Löschen ist fehlgeschlagen. Servernachricht: " + err.message)
                    this.setState({taskTemplates: []})
                })
        }

        this.educaModalRef?.current?.open( (btn)=> btn == MODAL_BUTTONS.YES? delExec() : null, "Vorlage Löschen", "Soll die Vorlage '"+template.title+"' wirklich gelöscht werden?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO])
    }

    render() {

        return <div><Modal
            size={"lg"}
            show={this.state.isOpen}
            onHide={() => this.setState({isOpen: false})}
        > <Modal.Header closeButton>
            <Modal.Title>
                Vorlage auswählen <EducaCircularButton
                size={"small"}
                variant="success"
                tooltip={"Neue Vorlage erstellen"}
                onClick={() => {
                    this.taskTemplateEditorRef.current?.open(null, true)
                }}
            ><i className={"fa fa-plus"}/></EducaCircularButton>
            </Modal.Title>
        </Modal.Header>
            <div><Modal.Body>
                {this.state.taskTemplates == null || this.state.taskTemplates?.length == 0 ? <Alert variant={"info"}>Keine Vorlagen vorhanden</Alert> :
                <Table striped bordered hover>
                    <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>
                    {this.state.taskTemplates?.map((template, i) => {
                        return <tr key={template.id} style={{cursor: "pointer"}} onDoubleClick={() => {
                            this.createTaskFromTemplate(template)}}>
                            <td >{template.title}</td>
                            <td>
                                <Button variant={"secondary"}
                                        title={"Vorlage bearbeiten"}
                                        className={"mr-1"}
                                        onClick={() => {
                                            this.taskTemplateEditorRef?.current.open(template, false);
                                        }}>
                                    <i className="fas fa-edit"></i></Button>
                                <Button variant={"primary"}
                                        className={"mr-1"}
                                        title={"aus Vorlage erstellen"}
                                        onClick={() => {
                                            this.createTaskFromTemplate(template)
                                        }}>
                                    <i className="fas fa-check"></i></Button>
                                <Button variant={"danger"}
                                        title={"Vorlage löschen"}
                                        onClick={() => {
                                            this.deleteTemplate(template)
                                        }}>
                                    <i className="fas fa-trash"></i></Button>
                            </td>
                        </tr>
                    })}
                    </tbody>
                </Table>}
            </Modal.Body>
                <Modal.Footer>

                    <Button
                        className="btn btn-secondary mr-2"
                        onClick={() => this.close()}
                    >Abbrechen
                    </Button>
                    <Button
                        className="btn btn-primary mr-2"
                        onClick={() => {this.taskEditorRef.current?.open();  this.close()}}
                    >Weiter ohne Vorlage
                    </Button>

                </Modal.Footer>
            </div>
        </Modal>

            <TaskEditorModal
                isNewTask={true}
                taskChangedCallback={() => {
                    this.props.taskChangedCallback()
                }}
                ref={this.taskEditorRef}/>

            <TaskTemplateEditorModal
                taskTemplateChangedCallback={() => {
                    this.loadTaskTemplates()
                }}
                ref={this.taskTemplateEditorRef}/>
            <EducaModal ref={this.educaModalRef}/>

        </div>

    }
}

