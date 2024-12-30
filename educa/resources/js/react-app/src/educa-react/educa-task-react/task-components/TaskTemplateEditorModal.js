import React, {useRef, useState} from 'react';
import Form from "react-bootstrap/Form";
import Button from "react-bootstrap/Button";
import {CKEditor} from "@ckeditor/ckeditor5-react";
import EducaModal, {MODAL_BUTTONS} from "../../../shared/shared-components/EducaModal";
import Modal from "react-bootstrap/Modal";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper, {EducaCKEditorDefaultConfig, MODELS} from "../../../shared/shared-helpers/SharedHelper";
import EducaFileBrowser from "../../educa-components/EducaFileBrowser/EducaFileBrowser";
import EducaHelper, {LIMITS, TASK_TYPES_OPTIONS} from "../../helpers/EducaHelper";
import Select from "react-select";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import {getSelectErrorStyle} from "../../../shared/shared-components/Selects";
import {Alert} from "react-bootstrap";
import EducaAICKEditor from "../../educa-components/EducaAICKEditor";
import {NumberInput} from "../../../shared/shared-components/Inputs";


export default class TaskTemplateEditorModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            taskTemplate: {},
            isOpen: false,
            isNewTemplate: false
        }
    }


    open(taskTemplate, isNewTemplate) {
        this.setState({taskTemplate: taskTemplate, isOpen: true, isNewTemplate: isNewTemplate})
    }


    render() {

        return <Modal
            autofocus={false}
            enforceFocus={false}
            backdrop="static"
            size={"lg"}
            show={this.state.isOpen}
            onHide={() => this.setState({isOpen: false})}
        > <Modal.Header>
            <Modal.Title>
                {!this.state.taskTemplate?.id ? "Neue Aufgabenvorlage" : "Aufgabenvorlage bearbeiten"}
            </Modal.Title>
        </Modal.Header>
            <TaskTemplateEditor
                isNewTemplate={this.state.isNewTemplate}
                cancelCallback={() => this.setState({isOpen: false, taskTemplate: null})}
                taskTemplateChangedCallback={() => this.props.taskTemplateChangedCallback()}
                taskTemplate={this.state.taskTemplate}/>

        </Modal>

    }
}

function TaskTemplateEditor(props) {

    let [taskTemplateToEdit, setTaskTemplateToEdit] = useState(
        props.taskTemplate ? props.taskTemplate :
            {
                title: "",
                description: "",
                privatenote: ""
            })

    let [titleError, setTitleError] = useState(false)
    let [taskTypeError, setTaskTypeError] = useState(false)
    let [descriptionError, setDescriptionError] = useState(false)
    let [noteError, setNoteError] = useState(false)

    let [step, setStep] = useState(1)

    let modalRef = useRef()
    let templateEditorRef = useRef();

    let secondStepClick = () => {
        if (!taskTemplateToEdit.title) {
            setTitleError(true);
            return
        }
        if(!taskTemplateToEdit.type) {
            setTaskTypeError(true);
            return
        }

        let promise = null
        if (!taskTemplateToEdit.id) // create
            promise = AjaxHelper.createTaskTemplate(taskTemplateToEdit.title, taskTemplateToEdit.description, taskTemplateToEdit.privatenote, taskTemplateToEdit.handIn, taskTemplateToEdit.type,false, -1, false, taskTemplateToEdit.maxPoints)
        else
            promise = AjaxHelper.updateTaskTemplate(taskTemplateToEdit.id, taskTemplateToEdit.title, taskTemplateToEdit.description, taskTemplateToEdit.privatenote, taskTemplateToEdit.handIn, taskTemplateToEdit.type,false, -1, false, taskTemplateToEdit.maxPoints)

        promise.then(resp => {
            if (resp.status > 0 && resp.payload?.taskTemplate) {
                props.taskTemplateChangedCallback(resp.payload.taskTemplate);
                setTaskTemplateToEdit(resp.payload.taskTemplate)
                setStep(2)
            } else
                throw new Error(resp.message)
        })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Aufgabenvorlage konnte nicht übermittelt werden. " + err.message)
            })
    }

    let modalCallback = (btn) => {
        if (btn === MODAL_BUTTONS.YES) {
            AjaxHelper.deleteTaskTemplate(taskTemplateToEdit.id)
                .then(resp => {
                    if (resp.status > 0) {
                        props.taskTemplateChangedCallback();
                        if(!props.isNewTemplate)
                            EducaHelper.fireSuccessToast("Aufgabenvorlage gelöscht", "Die Aufgabenvorlage wurde erfolgreich gelöscht.")
                        props.cancelCallback()

                    } else
                        throw new Error(resp.message)
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Die Aufgabenvorlage konnte nicht gelöscht werden." + err.message)
                })
        }
    }

    let onDeleteClick = () => {
        if(props.isNewTemplate) {
            modalCallback(MODAL_BUTTONS.YES)
            return
        }
        modalRef.current?.open((btn) => modalCallback(btn), "Aufgabenvorlage löschen", "Soll diese Aufgabenvorlage wirklich gelöscht werden?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO])
    }

    let saveAndClose = () => {
        EducaHelper.fireSuccessToast("Erfolg", "Aufgabenvorlage wurde übermittelt.")
        props.cancelCallback()
    }

    const saveFormTemplate = template => {
        AjaxHelper.updateTaskTemplateFormTemplate(taskTemplateToEdit.id, template)
            .then(resp => {
                if (resp.payload?.taskTemplate) {
                    props.taskTemplateChangedCallback(resp.payload.taskTemplate);
                    setTaskTemplateToEdit({ ...resp.payload?.taskTemplate, type: "form" });
                    SharedHelper.fireSuccessToast(
                        "Erfolg",
                        "Das Formular wurde erfolgreich gespeichert."
                    );
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Aufgabe konnte nicht übermittelt werden. " + err.message
                );
            });
    };

    let validTaskTypeOptions = TASK_TYPES_OPTIONS.filter( opt => opt?.value == "form"? FliesentischZentralrat.globalTaskFormCreate() : true)

    console.log(taskTemplateToEdit)

    return <>{ step === 2 ?
        <><Modal.Body>
            <div className="form-group row">
                <div className="col-12">
                    <h5>Dateien für Aufgabenstellung</h5>
                </div>
                <div className="col-12">
                    <div style={{display: "flex", flex: 1}}>
                        <div style={{display: "flex", flex: 1, flexDirection: "column"}}>
                            {!taskTemplateToEdit.id ?
                                <div><i className="fas fa-paperclip"></i> Dateibrowser wird im nächsten Schritt
                                    aktiv.</div> :
                                <EducaFileBrowser
                                    modelType={MODELS.TASKTEMPLATE}
                                    modelId={taskTemplateToEdit.id}
                                    canUserUpload={true}
                                    canUserEdit={true}
                                />}
                        </div>
                    </div>
                </div>
            </div>

                {taskTemplateToEdit.type == "document" ? <div className="form-group row">
                    <div className="col-12">
                        <h5>Vorlage für Antwort</h5>
                    </div>
                    <div className="col-12">
                        <div style={{display: "flex", flex: 1}}>
                            <div style={{display: "flex", flex: 1, flexDirection: "column"}}>
                                {!taskTemplateToEdit.submissiontemplate ?
                                    <div><i className="fas fa-paperclip"></i> Dateibrowser wird im nächsten Schritt
                                        aktiv.</div> :
                                    <><EducaFileBrowser
                                        modelType={MODELS.TASKSUBMISSIONTASKTEMPLATE}
                                        modelId={taskTemplateToEdit.submissiontemplate.id}
                                        canUserUpload={true}
                                        canUserEdit={true}
                                    />
                                        <Alert className={"mt-1"} variant={"info"}>Diese Dateien können vom Bearbeiter*innen gelesen und bearbeitet werden.</Alert>
                                    </>}
                            </div>
                        </div>
                    </div>
                </div> : null}

            {taskTemplateToEdit.type === "form" ? (
                <div className="form-group row">
                    <label
                        style={{
                            width: "120px",
                            paddingLeft: "15px",
                            paddingRight: "15px"
                        }}
                        className="col-form-label"
                    >
                        <i className="fab fa-wpforms"></i>{" "}
                        Formular
                    </label>
                    <div className="col">
                        {taskTemplateToEdit?.id > 0 ? (
                            <Button
                                onClick={() =>
                                    templateEditorRef?.current?.open(
                                        taskTemplateToEdit?.formular
                                            ?.lastRevision?.data
                                    )
                                }
                            >
                                {" "}
                                <i className="fas fa-edit"></i>{" "}
                                Formular-Editor öffnen
                            </Button>
                        ) : (
                            <div>
                                {" "}
                                Formular Editor wird im nächsten
                                Schritt aktiv.
                            </div>
                        )}
                    </div>
                </div>
            ) : null}

            <Modal.Footer>
                {taskTemplateToEdit?.id && !props.isNewTemplate ? <Button
                    className="btn btn-danger mr-2"
                    onClick={() => onDeleteClick()}
                >Löschen
                </Button> : <Button
                    className="btn btn-secondary mr-2"
                    onClick={() => onDeleteClick()}
                >Abbrechen
                </Button>}
                <Button
                    className="btn btn-primary"
                    onClick={() => setStep(1)}
                >Zurück
                </Button>
                <Button
                    className="btn btn-primary"
                    onClick={() => saveAndClose()}
                >Speichern & schließen
                </Button>
            </Modal.Footer>
        </Modal.Body>

        </>

        : <div><Modal.Body>
            <div>
                <div style={{ marginTop:"20px"}} className="form-group row">
                    <label style={{ width: "120px", paddingLeft: "15px", paddingRight: "15px"}} className="col-form-label"><i className="fas fa-pencil-alt"></i> Titel</label>
                    <div className="col">
                        <Form.Control
                            name="title"
                            type="text"
                            isInvalid={titleError}
                            value={taskTemplateToEdit.title}
                            onChange={(evt) => {
                                setTaskTemplateToEdit({...taskTemplateToEdit, title: evt.target.value});
                                setTitleError(false)
                            }}
                            className="form-control"
                            placeholder="Titel hinzufügen"
                        />
                    </div>
                </div>

                <div className="form-group row">
                    <label style={{ width: "120px", paddingLeft: "15px", paddingRight: "15px" }} className="col-form-label"><i className="fas fa-file-signature"></i> Abgabe-Format
                    </label>
                    <div className="col">
                        <Select
                            styles={getSelectErrorStyle(taskTypeError)}
                            value={taskTemplateToEdit.type ? validTaskTypeOptions.find( o => o.value == taskTemplateToEdit.type) : null}
                            options={validTaskTypeOptions}
                            placeholder={"Aufgabentyp auswählen..."}
                            closeMenuOnSelect={true}
                            onChange={(obj) => {
                                setTaskTypeError(false)
                                setTaskTemplateToEdit({
                                    ...taskTemplateToEdit,
                                    type: obj.value
                                })
                            }}
                            isDisabled={taskTemplateToEdit.id > 0 && !props.isNewTemplate}
                        />
                        <Alert className={"mt-1"} variant={"info"}>Aufgabentyp kann nach Erstellen der Aufgabenvorlage nicht mehr geändert werden.</Alert>
                    </div>
                </div>

                <div className="form-group row">
                    <label style={{ width: "120px", paddingLeft: "15px", paddingRight: "15px" }} className="col-form-label"><i
                        className="fas fa-star-half-alt"></i> maximale Punktzahl</label>
                    <div className="col">
                            <NumberInput
                                min={0}
                                max={10000}
                                value={taskTemplateToEdit.maxPoints}
                                onChangeNumber={(number) => {
                                    setTaskTemplateToEdit({...taskTemplateToEdit, maxPoints: number})
                                }}
                            />
                    </div>
                </div>
                <div className="form-group row">
                    <label style={{ width: "120px", paddingLeft: "15px", paddingRight: "15px" }} className="col-form-label"><i className="fas fa-list-ul"></i> Beschreibung</label>
                    <div className="col">
                        <EducaAICKEditor
                            className=" col-sm-10"
                            editor={window.ClassicEditor}
                            config={{
                                toolbar: {
                                    items: [
                                        'heading',
                                        '|',
                                        'bold',
                                        'italic',
                                        'link',
                                        'bulletedList',
                                        'numberedList',
                                        '|',
                                        'indent',
                                        'outdent',
                                        '|',
                                        'blockQuote',
                                        'insertTable',
                                        'undo',
                                        'redo'
                                    ]
                                },
                                placeholder: 'Aufgabenstellung ändern...',
                                link: { addTargetToExternalLinks: true },
                                language: 'de',
                                table: {
                                    contentToolbar: [
                                        'tableColumn',
                                        'tableRow',
                                        'mergeTableCells'
                                    ]
                                },
                            }}
                            data={taskTemplateToEdit.description ? taskTemplateToEdit.description : ""}
                            onChange={(event, editor) => {
                                const data = editor.getData();
                                setTaskTemplateToEdit({...taskTemplateToEdit, description: data})

                                const textarea = document.createElement('textarea');
                                textarea.innerHTML = editor.getData()
                                const shortText = textarea.innerText
                                    .replace(/<[^>]*>?/gm, "");
                                setDescriptionError(shortText.length > LIMITS.TASK_DESCRIPTION_LIMIT)
                                if (shortText.length > LIMITS.TASK_DESCRIPTION_LIMIT) {
                                    EducaHelper.fireWarningToast("Hinweis", "Das Zeichenlimit für die Aufgabenbeschreibung liegt bei " + LIMITS.TASK_DESCRIPTION_LIMIT + " Zeichen");
                                }
                            }}
                        />
                    </div>
                </div>
                <div className="form-group row">
                    <label style={{ width: "120px", paddingLeft: "15px", paddingRight: "15px" }} className="col-form-label"><i className="fas fa-list-ul"></i> private Notiz</label>
                    <div className="col">
                    <textarea
                        className="form-control col-sm-12"
                        value={taskTemplateToEdit.privatenote ? taskTemplateToEdit.privatenote : null}
                        onChange={(event) => {
                            const data = event.target.value;
                            setTaskTemplateToEdit({...taskTemplateToEdit, privatenote: data})

                            setNoteError(data.length > LIMITS.TASK_PRIVATE_NOTE_LIMIT)
                            if (data.length > LIMITS.TASK_PRIVATE_NOTE_LIMIT) {
                                EducaHelper.fireWarningToast("Hinweis", "Das Zeichenlimit für die private Notiz liegt bei " + LIMITS.TASK_PRIVATE_NOTE_LIMIT + " Zeichen");
                            }
                        }}
                        placeholder={"Private Notiz hinzufügen..."}
                    />
                    </div>
                </div>
            </div>

            <Alert className={"mt-1"} variant={"info"}>Dateien können erst im nächsten Schritt hochgeladen werden.</Alert>

            <Modal.Footer>
                {taskTemplateToEdit?.id && !props.isNewTemplate ? <Button
                    className="btn btn-danger mr-2"
                    onClick={() => onDeleteClick()}
                >Löschen
                </Button> : null}
                <Button
                    className="btn btn-secondary mr-2"
                    onClick={() => taskTemplateToEdit?.id && props.isNewTemplate ? onDeleteClick() : props.cancelCallback()}
                >Abbrechen
                </Button>
                <Button
                    className="btn btn-primary"
                    disabled={descriptionError || noteError}
                    onClick={() => secondStepClick()}
                >Weiter
                </Button>


            </Modal.Footer>

        </Modal.Body>
        </div>
    }
        <EducaModal ref={modalRef}/>
        <FormTemplateBuilderModal
            onSaveClick={template => saveFormTemplate(template)}
            ref={templateEditorRef}
        />{" "}
    </>
}

