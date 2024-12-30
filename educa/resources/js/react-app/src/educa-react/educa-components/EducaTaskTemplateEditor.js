import React, {useEffect, useRef, useState} from "react";
import Form from "react-bootstrap/Form";
import Select from "react-select";
import { getSelectErrorStyle } from "../../shared/shared-components/Selects";
import { Alert } from "react-bootstrap";
import { CKEditor } from "@ckeditor/ckeditor5-react";
import EducaHelper, {
    LIMITS,
    TASK_TYPES_OPTIONS
} from "../helpers/EducaHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";
import Button from "react-bootstrap/Button";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import EducaFileBrowser from "./EducaFileBrowser/EducaFileBrowser";
import SharedHelper, { MODELS } from "../../shared/shared-helpers/SharedHelper";
import {NumberInput} from "../../shared/shared-components/Inputs";

export default function EducaTaskTemplateEditor(props) {
    let [taskTemplateToEdit, setTaskTemplateToEdit] = useState({
        defaultEndOffset: -1
    });

    let [titleError, setTitleError] = useState(false);
    let [taskTypeError, setTaskTypeError] = useState(false);
    let [descriptionError, setDescriptionError] = useState(false);
    let [noteError, setNoteError] = useState(false);
    let templateEditorRef = useRef();

    const days = Array.from({length: 100}, (_, i) => { return { value: i, label: i == 1 ? "1 Tag" : i + " Tage" }})
    const hours = Array.from({length: 24}, (_, i) => { return { value: i, label: i == 1 ? "1 Stunde" : i + " Stunden" }})
    const minutes = Array.from({length: 60}, (_, i) => { return { value: i, label: i == 1 ? "1 Minute" : i + " Minuten" }})



    useEffect(() => {
        setTaskTemplateToEdit(props.taskTemplate);
    }, [props.taskTemplate]);

    let validTaskTypeOptions = TASK_TYPES_OPTIONS.filter(opt =>
        opt?.value == "form"
            ? FliesentischZentralrat.globalTaskFormCreate()
            : true
    );

    let secondStepClick = () => {
        if (!taskTemplateToEdit.title) {
            setTitleError(true);
            return;
        }
        if (!taskTemplateToEdit.type) {
            setTaskTypeError(true);
            return;
        }

        let promise = null;
        if (!taskTemplateToEdit.id)
            // create
            promise = AjaxHelper.createTaskTemplate(
                taskTemplateToEdit.title,
                taskTemplateToEdit.description,
                taskTemplateToEdit.privatenote,
                taskTemplateToEdit.handIn,
                taskTemplateToEdit.type,
                props.isLearnContent,
                taskTemplateToEdit.defaultEndOffset,
                taskTemplateToEdit.autostart,
                taskTemplateToEdit.maxPoints
            );
        else
            promise = AjaxHelper.updateTaskTemplate(
                taskTemplateToEdit.id,
                taskTemplateToEdit.title,
                taskTemplateToEdit.description,
                taskTemplateToEdit.privatenote,
                taskTemplateToEdit.handIn,
                taskTemplateToEdit.type,
                props.isLearnContent,
                taskTemplateToEdit.defaultEndOffset,
                taskTemplateToEdit.autostart,
                taskTemplateToEdit.maxPoints
            );

        promise
            .then(resp => {
                if (resp.status > 0 && resp.payload?.taskTemplate) {
                    props.storeCallback(resp.payload.taskTemplate);
                    setTaskTemplateToEdit(resp.payload.taskTemplate);

                    EducaHelper.fireSuccessToast(
                        "Erfolg",
                        "Aufgabenvorlage erfolgreich gespeichert."
                    );
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Aufgabenvorlage konnte nicht übermittelt werden. " +
                        err.message
                );
            });
    };

    const saveFormTemplate = template => {
        AjaxHelper.updateTaskTemplateFormTemplate(taskTemplateToEdit.id, template)
            .then(resp => {
                if (resp.payload?.taskTemplate) {
                    props.storeCallback(resp.payload.taskTemplate);
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

    if (!taskTemplateToEdit) return null;

    return (
        <div>
            <div style={{ marginTop: "20px" }} className="form-group row">
                <label
                    style={{
                        width: "100px",
                        paddingLeft: "15px",
                        paddingRight: "15px"
                    }}
                    className="col-form-label"
                >
                    <i className="fas fa-pencil-alt"></i> Titel
                </label>
                <div className="col">
                    <Form.Control
                        name="title"
                        type="text"
                        isInvalid={titleError}
                        value={taskTemplateToEdit.title}
                        onChange={evt => {
                            setTaskTemplateToEdit({
                                ...taskTemplateToEdit,
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
                        width: "100px",
                        paddingLeft: "15px",
                        paddingRight: "15px"
                    }}
                    className="col-form-label"
                >
                    <i className="fas fa-file-signature"></i> Abgabe-Format
                </label>
                <div className="col">
                    <Select
                        styles={getSelectErrorStyle(taskTypeError)}
                        value={
                            taskTemplateToEdit.type
                                ? validTaskTypeOptions.find(
                                      o => o.value == taskTemplateToEdit.type
                                  )
                                : null
                        }
                        options={validTaskTypeOptions}
                        placeholder={"Aufgabentyp auswählen..."}
                        closeMenuOnSelect={true}
                        onChange={obj => {
                            setTaskTypeError(false);
                            setTaskTemplateToEdit({
                                ...taskTemplateToEdit,
                                type: obj.value
                            });
                        }}
                        isDisabled={
                            taskTemplateToEdit.id > 0 && !props.isNewTemplate
                        }
                    />
                    <Alert className={"mt-1"} variant={"info"}>
                        Aufgabentyp kann nach Erstellen der Aufgabenvorlage
                        nicht mehr geändert werden.
                    </Alert>
                </div>
            </div>

            <div className="form-group row">
                <label style={{ width: "100px", paddingLeft: "15px", paddingRight: "15px" }} className="col-form-label"><i
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
                <label
                    style={{
                        width: "100px",
                        paddingLeft: "15px",
                        paddingRight: "15px"
                    }}
                    className="col-form-label"
                >
                    <i className="fas fa-list-ul"></i> Beschreibung
                </label>
                <div className="col">
                    <CKEditor
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
                            placeholder: "Aufgabenstellung ändern...",
                            link: { addTargetToExternalLinks: true },
                            language: "de",
                            table: {
                                contentToolbar: [
                                    "tableColumn",
                                    "tableRow",
                                    "mergeTableCells"
                                ]
                            }
                        }}
                        data={
                            taskTemplateToEdit.description
                                ? taskTemplateToEdit.description
                                : ""
                        }
                        onChange={(event, editor) => {
                            const data = editor.getData();
                            setTaskTemplateToEdit({
                                ...taskTemplateToEdit,
                                description: data
                            });

                            const textarea = document.createElement('textarea');
                            textarea.innerHTML = editor.getData()
                            const shortText = textarea.innerText
                                .replace(/<[^>]*>?/gm, "");

                            setDescriptionError(
                                shortText.length > LIMITS.TASK_DESCRIPTION_LIMIT
                            );
                            if (
                                shortText.length > LIMITS.TASK_DESCRIPTION_LIMIT
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
                        width: "100px",
                        paddingLeft: "15px",
                        paddingRight: "15px"
                    }}
                    className="col-form-label"
                >
                    <i class="fas fa-hourglass"></i> Bearbeitungszeit
                </label>
                <div className="col">
                    <Form.Group controlId="formBasicCheckbox">
                        <Form.Check type="checkbox" label="Keine Begrenzung" checked={taskTemplateToEdit?.defaultEndOffset < 0}
                        onClick={() => {
                            setTaskTemplateToEdit({...taskTemplateToEdit, defaultEndOffset: taskTemplateToEdit?.defaultEndOffset < 0 ? 60 : -1})
                        }}
                        />
                    </Form.Group>
                </div>
                <div className="col">
                    <label>Tage</label>
                    <Select
                        placeholder={"Tage"}
                        options={days}
                        isDisabled={taskTemplateToEdit?.defaultEndOffset < 0}
                        value={days.filter(s => s.value == Math.floor(taskTemplateToEdit?.defaultEndOffset / (24*60)))}
                        onChange={(val) => {
                            let reminder = taskTemplateToEdit?.defaultEndOffset % (24*60)
                            let hours = val.value * 24 * 60;
                            setTaskTemplateToEdit({...taskTemplateToEdit, defaultEndOffset: reminder + hours})
                        }}
                    />
                </div>
                <div className="col">
                    <label>Stunden</label>
                    <Select
                        placeholder={"Stunden"}
                        options={hours}
                        isDisabled={taskTemplateToEdit?.defaultEndOffset < 0}
                        value={hours.filter(s => s.value == Math.floor((taskTemplateToEdit?.defaultEndOffset % (24*60)) / 60 ))}
                        onChange={(val) => {
                            let reminder = taskTemplateToEdit?.defaultEndOffset % (24*60) % 60
                            let days = Math.floor(taskTemplateToEdit?.defaultEndOffset / (24*60)) * 24*60;
                            let hours = val.value * 60;
                            setTaskTemplateToEdit({...taskTemplateToEdit, defaultEndOffset: reminder + hours + days})
                        }}
                    />
                </div>
                <div className="col">
                    <label>Minuten</label>
                    <Select
                        placeholder={"Minuten"}
                        options={minutes}
                        isDisabled={taskTemplateToEdit?.defaultEndOffset < 0}
                        value={minutes.filter(s => s.value == (taskTemplateToEdit?.defaultEndOffset % (24*60)) % 60 )}
                        onChange={(val) => {
                            let reminder = taskTemplateToEdit?.defaultEndOffset - (taskTemplateToEdit?.defaultEndOffset % (24*60)) % 60
                            let minutes = val.value;
                            setTaskTemplateToEdit({...taskTemplateToEdit, defaultEndOffset: reminder + minutes})
                        }}
                    />
                </div>
            </div>
            <div className="form-group row">
                <label
                    style={{
                        width: "100px",
                        paddingLeft: "15px",
                        paddingRight: "15px"
                    }}
                    className="col-form-label"
                >
                    <i className="fas fa-magic"></i> automatisch starten
                </label>
                <div className="col">
                    <Form.Group controlId="formAutomatischStart">
                        <Form.Check type="checkbox" label="Die Aufgabe startet automatisch, sobald das Thema geöffnet wird" checked={taskTemplateToEdit?.autostart}
                                    onClick={() => {
                                        setTaskTemplateToEdit({...taskTemplateToEdit, autostart: taskTemplateToEdit?.autostart ? 0 : 1})
                                    }}
                        />
                    </Form.Group>
                </div>
            </div>
            <div className="form-group row">
                <label
                    style={{
                        width: "100px",
                        paddingLeft: "15px",
                        paddingRight: "15px"
                    }}
                    className="col-form-label"
                >
                    <i className="fas fa-list-ul"></i> private Notiz
                </label>
                <div className="col">
                    <textarea
                        className="form-control col-sm-12"
                        value={
                            taskTemplateToEdit.privatenote
                                ? taskTemplateToEdit.privatenote
                                : ""
                        }
                        onChange={event => {
                            const data = event.target.value;
                            setTaskTemplateToEdit({
                                ...taskTemplateToEdit,
                                privatenote: data
                            });

                            setNoteError(
                                data.length > LIMITS.TASK_PRIVATE_NOTE_LIMIT
                            );
                            if (data.length > LIMITS.TASK_PRIVATE_NOTE_LIMIT) {
                                EducaHelper.fireWarningToast(
                                    "Hinweis",
                                    "Das Zeichenlimit für die private Notiz liegt bei " +
                                        LIMITS.TASK_PRIVATE_NOTE_LIMIT +
                                        " Zeichen"
                                );
                            }
                        }}
                        placeholder={"Private Notiz hinzufügen..."}
                    />
                </div>
            </div>
            {taskTemplateToEdit?.id ? (
                <>
                    <div className="form-group row">
                        <div className="col-12">
                            <h5>Dateien für Aufgabenstellung</h5>
                        </div>
                        <div className="col-12">
                            <div style={{ display: "flex", flex: 1 }}>
                                <div
                                    style={{
                                        display: "flex",
                                        flex: 1,
                                        flexDirection: "column"
                                    }}
                                >
                                    {!taskTemplateToEdit.id ? (
                                        <div>
                                            <i className="fas fa-paperclip"></i>{" "}
                                            Dateibrowser wird im nächsten
                                            Schritt aktiv.
                                        </div>
                                    ) : (
                                        <EducaFileBrowser
                                            modelType={MODELS.TASKTEMPLATE}
                                            modelId={taskTemplateToEdit.id}
                                            canUserUpload={true}
                                            canUserEdit={true}
                                        />
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>

                    {taskTemplateToEdit.type === "document" ? (
                        <div className="form-group row">
                            <div className="col-12">
                                <h5>Vorlage für Antwort</h5>
                            </div>
                            <div className="col-12">
                                <div style={{ display: "flex", flex: 1 }}>
                                    <div
                                        style={{
                                            display: "flex",
                                            flex: 1,
                                            flexDirection: "column"
                                        }}
                                    >
                                        {!taskTemplateToEdit.submissiontemplate ? (
                                            <div>
                                                <i className="fas fa-paperclip"></i>{" "}
                                                Dateibrowser wird im nächsten
                                                Schritt aktiv.
                                            </div>
                                        ) : (
                                            <>
                                                <EducaFileBrowser
                                                    modelType={
                                                        MODELS.TASKSUBMISSIONTASKTEMPLATE
                                                    }
                                                    modelId={
                                                        taskTemplateToEdit
                                                            .submissiontemplate
                                                            .id
                                                    }
                                                    canUserUpload={true}
                                                    canUserEdit={true}
                                                />
                                                <Alert
                                                    className={"mt-1"}
                                                    variant={"info"}
                                                >
                                                    Diese Dateien können vom
                                                    Bearbeiter*innen gelesen und
                                                    bearbeitet werden.
                                                </Alert>
                                            </>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ) : null}

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
                    <FormTemplateBuilderModal
                        onSaveClick={template => saveFormTemplate(template)}
                        ref={templateEditorRef}
                    />
                </>
            ) : (
                <></>
            )}
            <div>
                <div className="d-flex flex-row-reverse mb-2">
                    <Button
                        className="btn btn-primary"
                        disabled={descriptionError || noteError}
                        onClick={() => secondStepClick()}
                    >
                        {!taskTemplateToEdit.id
                            ? "Aufgabe erstellen"
                            : "Aufgabe aktualisieren"}
                    </Button>
                </div>
            </div>
        </div>
    );
}
