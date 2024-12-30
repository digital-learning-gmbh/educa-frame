import React, { useEffect, useRef, useState } from "react";
import Form from "react-bootstrap/Form";
import DatePicker from "react-datepicker";
import Button from "react-bootstrap/Button";
import {
    CloudIdSelectMultiple,
    SectionSelectMultiple
} from "../../../shared/shared-components/EducaSelects";
import { CKEditor } from "@ckeditor/ckeditor5-react";
import EducaModal, {
    MODAL_BUTTONS
} from "../../../shared/shared-components/EducaModal";
import Modal from "react-bootstrap/Modal";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper, {
    MODELS
} from "../../../shared/shared-helpers/SharedHelper";
import EducaFileBrowser from "../../educa-components/EducaFileBrowser/EducaFileBrowser";
import EducaHelper, {
    LIMITS,
    TASK_TYPES_OPTIONS
} from "../../helpers/EducaHelper";
import Select from "react-select";
import moment from "moment";
import { Alert } from "react-bootstrap";
import { getSelectErrorStyle } from "../../../shared/shared-components/Selects";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import EducaAICKEditor from "../../educa-components/EducaAICKEditor";

const SELECT_MINUTES = [-1, 5, 10, 15, 30, 45, 60, 90, 120];

let SELECT_MINUTES_OBJECTS = null;

export default class TaskEditorModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            task: {},
            isOpen: false
        };

        if (!SELECT_MINUTES_OBJECTS) {
            SELECT_MINUTES_OBJECTS = SELECT_MINUTES.map(m => {
                return m == -1
                    ? { value: -1, label: "Keine Erinnerung" }
                    : { value: m, label: m + " Minuten" };
            });
        }
    }

    open(task) {
        this.setState({ task: task, isOpen: true });
    }

    render() {
        return (
            <Modal
                autofocus={false}
                enforceFocus={false}
                backdrop="static"
                size={"lg"}
                keyboard={false}
                show={this.state.isOpen}
                onHide={() => this.setState({ isOpen: false })}
            >
                {" "}
                <Modal.Header>
                    <Modal.Title>
                        {!this.state.task?.id
                            ? "Neue Aufgabe erstellen"
                            : "Aufgabe bearbeiten"}
                    </Modal.Title>
                </Modal.Header>
                <TaskEditor
                    isNewTask={this.props.isNewTask}
                    preselectedSections={this.props.preselectedSections}
                    taskChangedCallback={task =>
                        this.props.taskChangedCallback(task)
                    }
                    cancelCallback={() =>
                        this.setState({ isOpen: false, task: null })
                    }
                    task={this.state.task}
                />
            </Modal>
        );
    }
}

function TaskEditor(props) {
    let [taskToEdit, setTaskToEdit] = useState(
        props.task
            ? props.task
            : {
                  title: "",
                  start: null,
                  end: null,
                  attendees: [],
                  sections: props.preselectedSections
                      ? props.preselectedSections
                      : [],
                  description: "",
                  privatenote: "",
                  remember_minutes: -1,
                  submissiontemplate: null
              }
    );

    let [titleError, setTitleError] = useState(false);
    let [endError, setEndError] = useState(false);
    let [startError, setStartError] = useState(false);
    let [attendeesError, setAttendeesError] = useState(false);
    let [taskTypeError, setTaskTypeError] = useState(false);
    let [descriptionError, setDescriptionError] = useState(false);
    let [noteError, setNoteError] = useState(false);
    let [importFile, setImportFile] = useState(null);
    let [step, setStep] = useState(1);
    let [loading, setLoading] = useState(false);

    let calendarEndRef = useRef();
    let calendarStartRef = useRef();
    let modalRef = useRef();
    let templateEditorRef = useRef();

    useEffect(() => {
        if (props.task?.id > 0) loadTaskDetails(props.task.id);
    }, [props.task]);

    const loadTaskDetails = id => {
        AjaxHelper.getTaskDetails(id)
            .then(resp => {
                if (resp.payload?.task) {
                    setTaskToEdit(resp.payload?.task);
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Aufgabe konnte nicht übermittelt werden. " + err.message
                );
            });
    };

    let onSaveClick = () => {
        AjaxHelper.finishTaskUpdate(taskToEdit.id)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.task) {
                    props.taskChangedCallback(resp.payload.task);
                    EducaHelper.fireSuccessToast(
                        "Erfolg",
                        "Aufgabe wurde übermittelt."
                    );
                    setTaskToEdit(resp.payload.task);
                    if (taskToEdit?.id) props.cancelCallback();
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Aufgabe konnte nicht übermittelt werden. " + err.message
                );
            });
    };

    const saveFormTemplate = template => {
        AjaxHelper.updateTaskFormTemplate(taskToEdit.id, template)
            .then(resp => {
                if (resp.payload?.task) {
                    props.taskChangedCallback(resp.payload.task);
                    setTaskToEdit({ ...resp.payload?.task, type: "form" });
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

    let secondStepClick = () => {
        let err = false;
        if (!taskToEdit.title) {
            setTitleError(true);
            err = true;
        }
        if (!taskToEdit.end) {
            //  setEndError(true);
            //  err = true;
            taskToEdit.end = moment()
                .add(1, "week")
                .toDate();
        }

        if (!taskToEdit.type) {
            setTaskTypeError(true);
            err = true;
        }

        if (!taskToEdit.start) {
            taskToEdit.start = moment().toDate();
        }
        if (
            taskToEdit.start &&
            moment(taskToEdit.end).isBefore(moment(taskToEdit.start))
        ) {
            setStartError(true);
            err = true;
        }
        if (
            !(taskToEdit.attendees?.length > 0) &&
            !(taskToEdit.sections?.length > 0)
        )
            return setAttendeesError(true);

        if (err) return;

        let sectionIds = taskToEdit.sections?.map(section => section.id);
        let attendeesIds = taskToEdit.attendees?.map(attendee => attendee.id);

        console.log(taskToEdit);

        let promise = null;
        if (!taskToEdit.id)
            // create
            promise = AjaxHelper.createTask(
                taskToEdit.title,
                taskToEdit.start,
                taskToEdit.end,
                taskToEdit.description,
                taskToEdit.privatenote,
                attendeesIds,
                sectionIds,
                true,
                taskToEdit.remember_minutes,
                taskToEdit.type
            );
        else
            promise = AjaxHelper.updateTask(
                taskToEdit.id,
                taskToEdit.title,
                taskToEdit.start,
                taskToEdit.end,
                taskToEdit.description,
                taskToEdit.privatenote,
                attendeesIds,
                sectionIds,
                true,
                taskToEdit.remember_minutes,
                taskToEdit.type
            );

        promise
            .then(resp => {
                if (resp.status > 0 && resp.payload?.task) {
                    props.taskChangedCallback(resp.payload.task);
                    setTaskToEdit(resp.payload.task);
                    setStep(2);
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Aufgabe konnte nicht übermittelt werden. " + err.message
                );
            });
    };

    let modalCallback = btn => {
        if (btn === MODAL_BUTTONS.YES) {
            AjaxHelper.deleteTask(taskToEdit.id, !props.isNewTask)
                .then(resp => {
                    if (resp.status > 0) {
                        if (!props.isNewTask)
                            EducaHelper.fireSuccessToast(
                                "Aufgabe gelöscht",
                                "Die Aufgabe wurde erfolgreich gelöscht."
                            );
                        props.taskChangedCallback({
                            ...taskToEdit,
                            deleteMe: true
                        });
                        props.cancelCallback();
                    } else throw new Error(resp.message);
                })
                .catch(err => {
                    EducaHelper.fireErrorToast(
                        "Fehler",
                        "Die Aufgabe konnte nicht gelöscht werden." +
                            err.message
                    );
                });
        }
    };

    let onDeleteClick = () => {
        if (props.isNewTask) {
            modalCallback(MODAL_BUTTONS.YES);
            return;
        }
        modalRef.current?.open(
            btn => modalCallback(btn),
            "Aufgabe löschen",
            "Soll diese Aufgabe wirklich gelöscht werden?",
            [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
        );
    };

    let uploadContent = () => {
        AjaxHelper.uploadContentToTask(taskToEdit.id, importFile)
            .then(resp => {
                if (resp.status > 0) {
                    setTaskToEdit(resp.payload.task);
                    EducaHelper.fireSuccessToast(
                        "H5P Inhalt hochgeladen",
                        "Der Inhalt wurde erfolgreich zur Aufgabe hinzugefügt."
                    );
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Der Inhalt konnte nicht zur Aufgabe hinzugefügt werden." +
                        err.message
                );
            });
    };

    let playerInitialized = contentId => {
        console.log("init called " + contentId);
    };

    let onxAPIStatement = () => {
        // do nothing
    };

    let validTaskTypeOptions = TASK_TYPES_OPTIONS.filter(opt =>
        opt?.value == "form"
            ? FliesentischZentralrat.globalTaskFormCreate()
            : true
    );

    return (
        <>
            {step === 2 ? (
                <>
                    <Modal.Body>
                        {taskToEdit.type === "h5p" ? (
                            <div className="form-group row">
                                <div className="col-12">
                                    <h5>H5P Datei hochladen</h5>
                                </div>
                                <div className="col-12">
                                    <input
                                        accept=".h5p, .ecc"
                                        type="file"
                                        name="file"
                                        onChange={event => {
                                            setImportFile(
                                                event.target.files[0]
                                            );
                                        }}
                                    />
                                    {importFile ? (
                                        <Button
                                            variant="primary"
                                            onClick={() => uploadContent()}
                                        >
                                            Datei importieren
                                        </Button>
                                    ) : (
                                        <></>
                                    )}
                                </div>
                                {taskToEdit.contentId != null ? (
                                    <div className={"col-12"}>
                                    </div>
                                ) : (
                                    <></>
                                )}
                            </div>
                        ) : (
                            <>
                                <div className="form-group row">
                                    <div className="col-12">
                                        <h5>Dateien für Aufgabenstellung</h5>
                                    </div>
                                    <div className="col-12">
                                        <div
                                            style={{ display: "flex", flex: 1 }}
                                        >
                                            <div
                                                style={{
                                                    display: "flex",
                                                    flex: 1,
                                                    flexDirection: "column"
                                                }}
                                            >
                                                {!taskToEdit.id ? (
                                                    <div>
                                                        <i className="fas fa-paperclip"></i>{" "}
                                                        Dateibrowser wird im
                                                        nächsten Schritt aktiv.
                                                    </div>
                                                ) : (
                                                    <>
                                                        <EducaFileBrowser
                                                            modelType={
                                                                MODELS.TASK
                                                            }
                                                            modelId={
                                                                taskToEdit.id
                                                            }
                                                            canUserUpload={true}
                                                            canUserEdit={true}
                                                        />
                                                        <Alert
                                                            className={"mt-1"}
                                                            variant={"info"}
                                                        >
                                                            Diese Dateien sind
                                                            bearbeitbar nur
                                                            durch den
                                                            Aufgabensteller*innen.
                                                            Bearbeiter*innen
                                                            kann diese nur
                                                            lesen.
                                                        </Alert>
                                                    </>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {taskToEdit.type == "document" ? (
                                    <div className="form-group row">
                                        <div className="col-12">
                                            <h5>Vorlage für Antwort</h5>
                                        </div>
                                        <div className="col-12">
                                            <div
                                                style={{
                                                    display: "flex",
                                                    flex: 1
                                                }}
                                            >
                                                <div
                                                    style={{
                                                        display: "flex",
                                                        flex: 1,
                                                        flexDirection: "column"
                                                    }}
                                                >
                                                    {!taskToEdit.submissiontemplate ? (
                                                        <div>
                                                            <i className="fas fa-paperclip"></i>{" "}
                                                            Dateibrowser wird im
                                                            nächsten Schritt
                                                            aktiv.
                                                        </div>
                                                    ) : (
                                                        <>
                                                            <EducaFileBrowser
                                                                modelType={
                                                                    MODELS.SUBMISSIONTEMPLATE
                                                                }
                                                                modelId={
                                                                    taskToEdit
                                                                        .submissiontemplate
                                                                        .id
                                                                }
                                                                canUserUpload={
                                                                    true
                                                                }
                                                                canUserEdit={
                                                                    true
                                                                }
                                                            />
                                                            <Alert
                                                                className={
                                                                    "mt-1"
                                                                }
                                                                variant={"info"}
                                                            >
                                                                Diese Dateien
                                                                können vom
                                                                Bearbeiter*innen
                                                                gelesen und
                                                                bearbeitet
                                                                werden.
                                                            </Alert>
                                                        </>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ) : null}

{taskToEdit.type === "form" ? (
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
                                        {taskToEdit?.id > 0 ? (
                                            <Button
                                                onClick={() =>
                                                    templateEditorRef?.current?.open(
                                                        taskToEdit?.formular
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

                            </>
                        )}
                    </Modal.Body>
                    <Modal.Footer>
                        {taskToEdit?.id && !props.isNewTask ? (
                            <Button
                                className="btn btn-danger mr-2"
                                onClick={() => onDeleteClick()}
                            >
                                Löschen
                            </Button>
                        ) : (
                            <Button
                                className="btn btn-secondary mr-2"
                                onClick={() => onDeleteClick()}
                            >
                                Abbrechen
                            </Button>
                        )}
                        <Button
                            className="btn btn-primary"
                            onClick={() => setStep(1)}
                        >
                            Zurück
                        </Button>
                        <Button
                            className="btn btn-primary"
                            onClick={() => onSaveClick()}
                        >
                            Speichern & schließen
                        </Button>
                    </Modal.Footer>
                </>
            ) : (
                <>
                    <Modal.Body>
                        <div>
                            <div
                                className="form-group row"
                                style={{ marginTop: "20px" }}
                            >
                                <label
                                    style={{
                                        width: "120px",
                                        paddingLeft: "15px",
                                        paddingRight: "15px"
                                    }}
                                    className="col-form-label"
                                >
                                    <i className="fas fa-pencil-alt"></i>{" "}
                                    Titel
                                </label>
                                <div className="col">
                                    <Form.Control
                                        name="title"
                                        type="text"
                                        isInvalid={titleError}
                                        value={taskToEdit.title}
                                        onChange={evt => {
                                            setTaskToEdit({
                                                ...taskToEdit,
                                                title: evt.target.value
                                            });
                                            setTitleError(false);
                                        }}
                                        className="form-control"
                                        placeholder="Titel hinzufügen"
                                    />
                                </div>
                            </div>

                            <div className="form-group row">
                                <label
                                    style={{
                                        width: "120px",
                                        paddingLeft: "15px",
                                        paddingRight: "15px"
                                    }}
                                    className="col-form-label"
                                >
                                    <i className="fas fa-file-signature"></i>{" "}
                                    Abgabe-Format
                                </label>
                                <div className="col">
                                    <Select
                                        styles={getSelectErrorStyle(
                                            taskTypeError
                                        )}
                                        value={
                                            taskToEdit.type
                                                ? validTaskTypeOptions.find(
                                                      o =>
                                                          o.value ==
                                                          taskToEdit.type
                                                  )
                                                : null
                                        }
                                        options={validTaskTypeOptions}
                                        placeholder={"Aufgabentyp auswählen..."}
                                        closeMenuOnSelect={true}
                                        onChange={obj => {
                                            setTaskTypeError(false);
                                            setTaskToEdit({
                                                ...taskToEdit,
                                                type: obj.value
                                            });
                                        }}
                                        isDisabled={taskToEdit.id > 0}
                                    />
                                    <Alert className={"mt-1"} variant={"info"}>
                                        Aufgabentyp kann nach Erstellen der
                                        Aufgabe nicht mehr geändert werden.
                                    </Alert>
                                </div>
                            </div>

                            <div className="form-group row">
                                <label
                                    style={{
                                        width: "120px",
                                        paddingLeft: "15px",
                                        paddingRight: "15px"
                                    }}
                                    className="col-form-label"
                                >
                                    <i className="fas fa-hourglass-start"></i>{" "}
                                    Startdatum
                                </label>
                                <div className="col-sm-6">
                                    <div
                                        style={
                                            startError
                                                ? {
                                                      display: "flex",
                                                      flexDirection: "row"
                                                  }
                                                : {}
                                        }
                                    >
                                        <div
                                            className={
                                                startError ? "" : "input-group"
                                            }
                                            style={
                                                startError
                                                    ? {
                                                          padding: "2px",
                                                          border:
                                                              "1px solid red",
                                                          display: "flex",
                                                          flexShrink: "1"
                                                      }
                                                    : {}
                                            }
                                        >
                                            <DatePicker
                                                ref={calendarStartRef}
                                                selected={
                                                    taskToEdit.start
                                                        ? moment(
                                                              taskToEdit.start
                                                          ).toDate()
                                                        : moment().toDate()
                                                }
                                                timeIntervals={10}
                                                onChange={date => {
                                                    setTaskToEdit({
                                                        ...taskToEdit,
                                                        start: date
                                                    });
                                                    setStartError(false);
                                                }}
                                                locale="de-DE"
                                                className={"form-control"}
                                                timeCaption={"Startdatum"}
                                                showTimeSelect
                                                dateFormat="dd.MM.yyyy  HH:mm"
                                            />
                                            <div className="input-group-append">
                                                <Button
                                                    style={{ zIndex: 0 }}
                                                    onClick={() => {
                                                        calendarStartRef.current?.setOpen(
                                                            true
                                                        );
                                                    }}
                                                    variant={
                                                        "outline-secondary"
                                                    }
                                                >
                                                    <i className="far fa-calendar-alt" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="form-group row">
                                <label
                                    style={{
                                        width: "120px",
                                        paddingLeft: "15px",
                                        paddingRight: "15px"
                                    }}
                                    className="col-form-label"
                                >
                                    <i className="fas fa-history"></i>{" "}
                                    Frist
                                </label>
                                <div className="col-sm-6">
                                    <div
                                        style={
                                            endError
                                                ? {
                                                      display: "flex",
                                                      flexDirection: "row"
                                                  }
                                                : {}
                                        }
                                    >
                                        <div
                                            className={
                                                endError ? "" : "input-group"
                                            }
                                            style={
                                                endError
                                                    ? {
                                                          padding: "2px",
                                                          border:
                                                              "1px solid red",
                                                          display: "flex",
                                                          flexShrink: "1"
                                                      }
                                                    : {}
                                            }
                                        >
                                            <DatePicker
                                                ref={calendarEndRef}
                                                selected={
                                                    taskToEdit.end
                                                        ? moment(
                                                              taskToEdit.end
                                                          ).toDate()
                                                        : moment()
                                                              .add(1, "week")
                                                              .toDate()
                                                }
                                                timeIntervals={10}
                                                onChange={date => {
                                                    setTaskToEdit({
                                                        ...taskToEdit,
                                                        end: date
                                                    });
                                                    setEndError(false);
                                                }}
                                                locale="de-DE"
                                                className={"form-control"}
                                                timeCaption={"Frist"}
                                                showTimeSelect
                                                dateFormat="dd.MM.yyyy  HH:mm"
                                            />
                                            <div className="input-group-append">
                                                <Button
                                                    style={{ zIndex: 0 }}
                                                    onClick={() => {
                                                        calendarEndRef.current?.setOpen(
                                                            true
                                                        );
                                                    }}
                                                    variant={
                                                        "outline-secondary"
                                                    }
                                                >
                                                    <i className="far fa-calendar-alt" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="form-group row">
                                <label
                                    style={{
                                        width: "120px",
                                        paddingLeft: "15px",
                                        paddingRight: "15px"
                                    }}
                                    className="col-form-label"
                                >
                                    <i className="far fa-bell"></i>{" "}
                                    Erinnerung
                                </label>
                                <div className="col">
                                    <Select
                                        value={
                                            taskToEdit.remember_minutes
                                                ? SELECT_MINUTES_OBJECTS.find(
                                                      o =>
                                                          o.value ==
                                                          taskToEdit.remember_minutes
                                                  )
                                                : SELECT_MINUTES_OBJECTS[0]
                                        }
                                        options={SELECT_MINUTES_OBJECTS}
                                        closeMenuOnSelect={true}
                                        onChange={obj => {
                                            setTaskToEdit({
                                                ...taskToEdit,
                                                remember_minutes: obj.value
                                            });
                                        }}
                                    />
                                </div>
                            </div>

                            <div className="form-group row">
                                <label
                                    style={{
                                        width: "120px",
                                        paddingLeft: "15px",
                                        paddingRight: "15px"
                                    }}
                                    className="col-form-label"
                                >
                                    <i className="fas fa-user-plus"></i>{" "}
                                    Teilnehmer
                                </label>
                                <div className="col">
                                    <CloudIdSelectMultiple
                                        styles={getSelectErrorStyle(
                                            attendeesError
                                        )}
                                        value={taskToEdit.attendees}
                                        closeMenuOnSelect={false}
                                        placeholder={"Personen auswählen..."}
                                        cloudUserListChangedCallback={obj => {
                                            setAttendeesError(false);
                                            setTaskToEdit({
                                                ...taskToEdit,
                                                attendees: obj
                                            });
                                        }}
                                    />
                                </div>
                            </div>
                            <div className="form-group row">
                                <label
                                    style={{
                                        width: "120px",
                                        paddingLeft: "15px",
                                        paddingRight: "15px"
                                    }}
                                    className="col-form-label"
                                >
                                    <i className="fas fa-users"></i>{" "}
                                    Bereiche {" "}
                                </label>
                                <div className="col">
                                    <SectionSelectMultiple
                                        styles={getSelectErrorStyle(
                                            attendeesError
                                        )}
                                        value={taskToEdit.sections}
                                        placeholder={"Gruppen auswählen..."}
                                        sectionListChangedCallback={obj => {
                                            setAttendeesError(false);
                                            setTaskToEdit({
                                                ...taskToEdit,
                                                sections: obj
                                            });
                                        }}
                                    />
                                </div>
                            </div>
                            <div className="form-group row">
                                <label
                                    style={{
                                        width: "120px",
                                        paddingLeft: "15px",
                                        paddingRight: "15px"
                                    }}
                                    className="col-form-label"
                                >
                                    <i className="fas fa-list-ul"></i>{" "}
                                    Beschreibung
                                </label>
                                <div className="col">
                                    <EducaAICKEditor
                                        className=" col-sm-10"
                                        editor={window.ClassicEditor}
                                        config={{
                                            toolbar: {
                                                items: [
                                                    "heading",
                                                    "|",
                                                    "bold",
                                                    "italic",
                                                    "link",
                                                    "bulletedList",
                                                    "numberedList",
                                                    "|",
                                                    "indent",
                                                    "outdent",
                                                    "|",
                                                    "blockQuote",
                                                    "insertTable",
                                                    "undo",
                                                    "redo"
                                                ]
                                            },
                                            placeholder:
                                                "Aufgabenstellung ändern...",
                                            language: "de",
                                            link: {
                                                addTargetToExternalLinks: true
                                            },
                                            table: {
                                                contentToolbar: [
                                                    "tableColumn",
                                                    "tableRow",
                                                    "mergeTableCells"
                                                ]
                                            }
                                        }}
                                        data={
                                            taskToEdit.description
                                                ? taskToEdit.description
                                                : ""
                                        }
                                        onChange={(event, editor) => {
                                            const data = editor.getData();
                                            setTaskToEdit({
                                                ...taskToEdit,
                                                description: data
                                            });

                                            const textarea = document.createElement('textarea');
                                            textarea.innerHTML = editor.getData()
                                            const shortText = textarea.innerText
                                                .replace(/<[^>]*>?/gm, "");
                                            setDescriptionError(
                                                shortText.length >
                                                    LIMITS.TASK_DESCRIPTION_LIMIT
                                            );
                                            if (
                                                shortText.length >
                                                LIMITS.TASK_DESCRIPTION_LIMIT
                                            ) {
                                                EducaHelper.fireWarningToast(
                                                    "Hinweis",
                                                    "Das Zeichenlimit für die Aufgabenbeschreibung liegt bei " +
                                                        LIMITS.TASK_DESCRIPTION_LIMIT +
                                                        " Zeichen"
                                                );
                                            }
                                        }}
                                    />
                                </div>
                            </div>
                            <div className="form-group row">
                                <label
                                    style={{
                                        width: "120px",
                                        paddingLeft: "15px",
                                        paddingRight: "15px"
                                    }}
                                    className="col-form-label"
                                >
                                    <img
                                        src={
                                            "/images/task_icons/Aufgabe_Notizen_neu_schwarz.png"
                                        }
                                        height={20}
                                    />{" "}
                                    private Notiz
                                </label>
                                <div className="col">
                                    <textarea
                                        className="form-control col-sm-12"
                                        value={
                                            taskToEdit.privatenote
                                                ? taskToEdit.privatenote
                                                : null
                                        }
                                        onChange={event => {
                                            const data = event.target.value;
                                            setTaskToEdit({
                                                ...taskToEdit,
                                                privatenote: data
                                            });

                                            setNoteError(
                                                data.length >
                                                    LIMITS.TASK_PRIVATE_NOTE_LIMIT
                                            );
                                            if (
                                                data.length >
                                                LIMITS.TASK_PRIVATE_NOTE_LIMIT
                                            ) {
                                                EducaHelper.fireWarningToast(
                                                    "Hinweis",
                                                    "Das Zeichenlimit für die private Notiz liegt bei " +
                                                        LIMITS.TASK_PRIVATE_NOTE_LIMIT +
                                                        " Zeichen"
                                                );
                                            }
                                        }}
                                        placeholder="Private Notiz hinzufügen..."
                                    />
                                </div>
                            </div>
                            <Alert className={"mt-1"} variant={"info"}>
                                Dateien können erst im nächsten Schritt
                                hochgeladen werden.
                            </Alert>
                        </div>
                    </Modal.Body>
                    <Modal.Footer>
                        {taskToEdit?.id && !props.isNewTask ? (
                            <Button
                                className="btn btn-danger mr-2"
                                onClick={() => onDeleteClick()}
                            >
                                Löschen
                            </Button>
                        ) : null}
                        <Button
                            className="btn btn-secondary mr-2"
                            onClick={() =>
                                taskToEdit?.id && props.isNewTask
                                    ? onDeleteClick()
                                    : props.cancelCallback()
                            }
                        >
                            Abbrechen
                        </Button>
                        <Button
                            disabled={descriptionError || noteError}
                            className="btn btn-primary"
                            onClick={() => secondStepClick()}
                        >
                            Weiter
                        </Button>
                    </Modal.Footer>
                </>
            )}
            <EducaModal ref={modalRef} />
        </>
    );
}
