import React, { useEffect, useRef, useState } from "react";
import Card from "react-bootstrap/Card";
import { CKEditor } from "@ckeditor/ckeditor5-react";
import Button from "react-bootstrap/Button";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper, {
    EducaCKEditorDefaultConfig,
    MODELS
} from "../../../shared/shared-helpers/SharedHelper";
import { TASK_STATES } from "../EducaTaskViewReact";
import EducaModal, {
    MODAL_BUTTONS
} from "../../../shared/shared-components/EducaModal";
import Form from "react-bootstrap/Form";
import EducaHelper, { LIMITS } from "../../helpers/EducaHelper";
import EducaFileBrowser from "../../educa-components/EducaFileBrowser/EducaFileBrowser";
import AutoFormBuilderManaged from "../../../shared/shared-helpers/educa-form-builder/AutoFormBuilderManaged";
import AutoFormBuilderUnmanaged from "../../../shared/shared-helpers/educa-form-builder/AutoFormBuilderUnmanaged";
import AutoFormBuilderAjax from "../../../shared/shared-helpers/educa-form-builder/AutoFormBuilderAjax";
import EducaAutoFormBuilder from "../../educa-components/EducaAutoFormBuilder";
import { Alert } from "react-bootstrap";
import {useSelector} from "react-redux";

function TaskStudentView(props) {
    let [submission, setSubmission] = useState(props.submission);
    let [task, setTask] = useState(props.task);
    let [editorReady, setEditorReady] = useState(false);
    const store = useSelector(state => state); // redux hook
    let creator = store.allCloudUsers.find(user => user.id === task.cloud_id);
    let modalRef = useRef();

    let [submissionError, setSubmissionError] = useState(false);

    useEffect(() => {
        if (props.submission) {
            setSubmission(props.submission);
        }
    }, [props.submission]);

    useEffect(() => {
        if (props.task) {
            setTask(props.task);
        }
    }, [props.task]);

    let isSubmitted = submission && submission.stage !== TASK_STATES.DRAFT;
    useEffect(() => {
        if (props.task && props.submission && !isSubmitted && props.task.type === "practical") {
            submit(true)
        }
    }, [props.task,props.submission]);

    // Ajax calls
    let submit = (silent = false) => {
        if (isSubmitted) return;
        let state =
            task.type === "check" ? TASK_STATES.COMPLETED : TASK_STATES.REVIEW;
        AjaxHelper.updateSubmission(
            submission.task_id,
            submission.id,
            submission.description,
            null,
            null,
            state
        )
            .then(resp => {
                if (resp.status > 0) {
                    if(!silent) {
                        EducaHelper.fireSuccessToast(
                            "Antwort abgegeben",
                            "Du hast deine Antwort erfolgreich übermittelt."
                        );
                    }
                    setSubmission(resp.payload.submission);
                    props.submissionSubmitCallback(resp.payload.submission);
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Die Antwort wurde nicht gespeichert." + err.message
                );
            });
    };

    let save = () => {
        setEditorReady(false);
        setTimeout(() => setEditorReady(true), 1000);
        if (task.type === "text" && !editorReady)
            return EducaHelper.fireWarningToast(
                "Achtung",
                "Bitte warte einen Moment bis du diese Aktion wieder ausführst."
            );
        if (isSubmitted) return;

        AjaxHelper.updateSubmission(
            submission.task_id,
            submission.id,
            submission.description
        )
            .then(resp => {
                if (resp.status > 0) {
                    EducaHelper.fireSuccessToast(
                        "Antwort abgegeben",
                        "Du hast deine Antwort erfolgreich übermittelt."
                    );
                    setSubmission(resp.payload.submission);
                    props.submissionSubmitCallback(resp.payload.submission);
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Die Antwort wurde nicht gespeichert." + err.message
                );
            });
    };

    let modalCallback = btn => {
        if (btn === MODAL_BUTTONS.YES) submit();
    };

    let submitClicked = () => {
        if (task.type === "text" && !editorReady)
            return EducaHelper.fireWarningToast(
                "Achtung",
                "Bitte warte einen Moment, bis du alle Inhalte geladen hast."
            );
        modalRef.current.open(
            btn => modalCallback(btn),
            "Antwort abgeben",
            "Möchtest du die Antwort wirklich abgeben? Sie kann anschließend nicht mehr bearbeitet werden.",
            [MODAL_BUTTONS.NO, MODAL_BUTTONS.YES]
        );
    };

    let detailSubmissionView = (
        <CKEditor
            disabled={isSubmitted}
            config={isSubmitted ? { toolbar: [] } : EducaCKEditorDefaultConfig}
            onReady={editor => {
                setEditorReady(true);
            }}
            className=" col-sm-10"
            editor={ClassicEditor}
            data={submission.description ? submission.description : ""}
            onChange={(event, editor) => {
                const data = editor.getData();
                setSubmission({ ...props.submission, description: data });

                const textarea = document.createElement('textarea');
                textarea.innerHTML = editor.getData()
                const shortText = textarea.innerText
                    .replace(/<[^>]*>?/gm, "");
                setSubmissionError(
                    shortText.length > LIMITS.TASK_TEXT_SUBMISSION_LIMIT
                );
                if (shortText.length > LIMITS.TASK_TEXT_SUBMISSION_LIMIT) {
                    EducaHelper.fireWarningToast(
                        "Hinweis",
                        "Das Zeichenlimit für die Antwort liegt bei " +
                            LIMITS.TASK_TEXT_SUBMISSION_LIMIT +
                            " Zeichen"
                    );
                }
            }}
        />
    );

    let saveButtonView = (
        <Button
            onClick={() => save()}
            disabled={isSubmitted || submissionError}
            className="btn btn-success m-1 float-right"
        >
            Entwurf speichern
        </Button>
    );

    if (task.type === "document") {
        console.log(isSubmitted);
        detailSubmissionView = (
            <div style={{ display: "flex", flexDirection: "column" }}>
                <EducaFileBrowser
                    modelType={MODELS.SUBMISSION}
                    modelId={submission.id}
                    canUserEdit={!isSubmitted}
                    canUserCreateFolder={!isSubmitted}
                    canUserUpload={!isSubmitted}
                    canUserDelete={!isSubmitted}
                />
                <Alert className={"m-1"} variant={"info"}>
                    Um die Aufgabe zu erledigen musst du Dateien hochladen oder
                    bearbeiten. Du kannst die Aufgabe abgeben, wenn Du das
                    gemacht hast.
                </Alert>
            </div>
        );
        saveButtonView = "";
    } else if (task.type === "check") {
        detailSubmissionView = (
            <h6 style={{ padding: "12px" }}>
                {isSubmitted
                    ? "Aufgabe als erledigt markiert."
                    : "Klicke auf den Button. Damit bestätigst Du, dass Du die Aufgabe erledigt hast."}
            </h6>
        );
        saveButtonView = "";
    } else if (task.type === "practical") {
        detailSubmissionView = (
            <h6 style={{ padding: "12px" }}>
                {isSubmitted
                    ? "Dies ist eine Praxis-Aufgabe. Lies den Aufgabentext und wende dich dann an den Aufgabensteller '" + creator?.name + "', für eine Bestätigung."
                    : "Klicke auf den Button, wenn deine Aufgabe nicht automatisch erstellt wird."}
            </h6>
        );
        saveButtonView = "";
    } else if (task.type === "form") {
        detailSubmissionView = (
            <div className={"p-2"}>
                <EducaAutoFormBuilder
                    readOnly={isSubmitted}
                    modelId={submission.id}
                    modelType={"submission"}
                    formId={task.formular_id}
                />
            </div>
        );
        saveButtonView = "";
    }

    let draftAndReviewView = (
        <div className={"mt-5"}>
            {isSubmitted ? (
                <div className={"mb-4"}>
                    <h4
                        className={"mt-2"}
                        style={{ color: "rgb(108, 117, 125)" }}
                    >
                        <i className="fas fa-info-circle"></i> Aufgabe wurde
                        eingereicht am{" "}
                        {moment(submission.updated_at).format("DD.MM.YYYY")}
                    </h4>
                </div>
            ) : null}

            {task.type !== "check" && task.type !== "practical" ? (
                <h5 className="mt-2" style={{ color: "rgb(108, 117, 125)" }}>
                    <b>Deine Antwort</b>
                </h5>
            ) : null}
            <Card className="mt-2">
                {detailSubmissionView}
                {!isSubmitted ? (
                    <>
                        <Card.Footer>
                            {saveButtonView}
                            {task.type == "form" ? (
                                <i>
                                    Achtung. Klicke erst im Formular auf
                                    'Speichern' bevor du die Aufgabe einreichst.
                                </i>
                            ) : null}
                            <Button
                                onClick={() => submitClicked()}
                                disabled={isSubmitted || submissionError}
                                className="btn btn-primary m-1 float-right"
                            >
                                <i className="fas fa-check"></i> Aufgabe
                                einreichen / erledigen
                            </Button>
                        </Card.Footer>
                    </>
                ) : null}
            </Card>
            <EducaModal ref={modalRef} />
        </div>
    );

    let completedView = (
        <div>
            <h5 className="mt-2" style={{ color: "rgb(108, 117, 125)" }}>
                <b>Rückmeldung</b>
            </h5>
            <Card className="mt-2">
                <CKEditor
                    disabled={true}
                    config={{ toolbar: [] }}
                    editor={ClassicEditor}
                    data={submission.rating ? submission.rating : "Kein Text"}
                />
                <Card.Body>
                    {task.type !== "text" ? (
                        <Form.Control
                            disabled={true}
                            placeholder={"Punkte"}
                            value={submission.points ? submission.points : ""}
                        />
                    ) : null}
                </Card.Body>
            </Card>
            <EducaModal ref={modalRef} />
        </div>
    );

    //if( submission?.stage === TASK_STATES.REVIEW || submission?.stage === TASK_STATES.DRAFT )
    //   return draftAndReviewView

    return (
        <div>
            {draftAndReviewView}
            {(submission?.stage === TASK_STATES.COMPLETED ||
                (submission?.stage === TASK_STATES.DRAFT &&
                    submission.rating)) &&
            task.type !== "check"
                ? completedView
                : null}
        </div>
    );
}

export default TaskStudentView;
