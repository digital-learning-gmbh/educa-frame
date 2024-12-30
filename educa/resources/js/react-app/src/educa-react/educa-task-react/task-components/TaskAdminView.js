import React, { createRef, useEffect, useRef, useState } from "react";
import Card from "react-bootstrap/Card";
import { CKEditor } from "@ckeditor/ckeditor5-react";
import { EducaDefaultTable } from "../../../shared/shared-components/Tables";
import Button from "react-bootstrap/Button";
import { connect, useSelector } from "react-redux";
import { TASK_STATES } from "../EducaTaskViewReact";
import Form from "react-bootstrap/Form";
import EducaModal, {
    MODAL_BUTTONS
} from "../../../shared/shared-components/EducaModal";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper, {
    EducaCKEditorDefaultConfig,
    MODELS
} from "../../../shared/shared-helpers/SharedHelper";
import EducaHelper from "../../helpers/EducaHelper";
import EducaFileBrowser from "../../educa-components/EducaFileBrowser/EducaFileBrowser";
import EducaAutoFormBuilder from "../../educa-components/EducaAutoFormBuilder";
import Modal from "react-bootstrap/Modal";
import {Alert, Dropdown, ProgressBar} from "react-bootstrap";
import {DisplayPair, NumberInput} from "../../../shared/shared-components/Inputs";
import {EducaTextArea} from "../../../shared/shared-components/EducaTextArea";
import {EducaLoading} from "../../../shared-local/Loading";
import moment from "moment";

const SUBMISSION_STATUS_SORT_LIST = ["draft", "review", "completed"];

function submissionStatusSortComparator(aSubmission, bSubmission, _, desc) {
    if (aSubmission.stage === bSubmission.stage) {
        return desc
            ? (bSubmission.name ?? "").localeCompare(aSubmission.name ?? "")
            : (aSubmission.name ?? "").localeCompare(bSubmission.name ?? "");
    }

    let aSortIndex = SUBMISSION_STATUS_SORT_LIST.indexOf(
        aSubmission.stage ?? ""
    );
    let bSortIndex = SUBMISSION_STATUS_SORT_LIST.indexOf(
        bSubmission.stage ?? ""
    );

    if (aSortIndex > bSortIndex) return 1;
    if (bSortIndex > aSortIndex) return -1;

    return 0;
}

const SUBMISSIONS_TABLE_COLUMNS = [
    {
        Header: "Name",
        accessor: "name" // accessor is the "key" in the data
    },
    {
        Header: "Letzte Änderung",
        accessor: "updated_at"
    },
    {
        Header: "Status",
        accessor: "displayStage",
        sortType: (a, b, _, desc) => {
            let aSubmission = a?.original;
            let bSubmission = b?.original;

            if (aSubmission == null && bSubmission == null) return 0;
            if (aSubmission == null) return 1;
            if (bSubmission == null) return -1;

                return submissionStatusSortComparator(aSubmission, bSubmission, _, desc)
            }
        },
        {
            Header: 'Gelesen',
            accessor: 'isReadComp'
        },
        {
            Header: 'Aktion',
            accessor: 'action',
        },
    ]

export function TaskAdminView(props) {
    let [submissions, setSubmissions] = useState(props.submissions);
    let [task, setTask] = useState(props.task);
    let [currentSubmission, setCurrentSubmission] = useState({});

    let educaModalRef = useRef();
    let store = useSelector(state => state);
    let evaluationModalRef = useRef();
    let aiModalRef = useRef();

    useEffect(() => {
        if (props.submissions) {
            setSubmissions(props.submissions);
        }
    }, [props.submissions]);

    useEffect(() => {
        if (props.task) {
            setTask(props.task);
        }
    }, [props.task]);

    let getStageString = stage => {
        if (stage === TASK_STATES.DRAFT) return "Offen";
        if (stage === TASK_STATES.REVIEW) return "Rückmeldung";
        if (stage === TASK_STATES.COMPLETED) return "Erledigt";
        return "Fehler";
    };

    let getAction = submission => {
        let textAndIcon = null;
        let toolTip = "";

        if (submission.stage === TASK_STATES.DRAFT) {
            textAndIcon = (
                <i className="material-icons">remove_red_eye_outlined</i>
            );
            toolTip = "Entwurf ansehen";
        }
        if (submission.stage === TASK_STATES.REVIEW) {
            textAndIcon = <i className="material-icons">star_rate_outlined</i>;
            toolTip = "Bewertung abgeben";
        }
        if (submission.stage === TASK_STATES.COMPLETED) {
            textAndIcon = (
                <i className="material-icons">remove_red_eye_outlined</i>
            );
            toolTip = "Rückmeldung ansehen";
        }

        if (textAndIcon) {
            return (
                <Button
                    style={{ width: 48 }}
                    title={toolTip}
                    className={"btn btn-primary m-1"}
                    onClick={() => {
                        evaluationModalRef.current?.open(submission);
                        setCurrentSubmission(submission);
                    }}
                >
                    {textAndIcon}
                </Button>
            );
        }
        return "Fehler";
    };
    let resetSubmission = submission => {
        let execCompleteAll = () => {
            AjaxHelper.resetSubmission(task.id, submission.id)
                .then(resp => {
                    if (resp.status > 0) {
                        EducaHelper.fireSuccessToast(
                            "Aufgabe freigegeben",
                            "Die Aufgabe wurde zur Bearbeitung freigegeben."
                        );
                        setSubmissions(resp.payload.submissions);
                    } else throw new Error(resp.message);
                })
                .catch(err => {
                    EducaHelper.fireErrorToast(
                        "Fehler",
                        "Die Aufgabe konnten nicht zur Bearbeitung freigegeben werden." +
                            err.message
                    );
                });
        };

        educaModalRef?.current?.openWithCustomButtons(
            "Aufgabe zur Bearbeitung zurücksetzen",
            "Dadurch wird die Aufgabe wieder für den Nutzer freigegeben, so dass er die Aufgabe erneut bearbeiten kann.",
            [
                <Button
                    variant={"secondary"}
                    onClick={() => educaModalRef?.current?.close()}
                >
                    Abbrechen
                </Button>,
                <Button variant={"success"} onClick={() => execCompleteAll()}>
                    Freigeben
                </Button>
            ]
        );
    };

    let getResetAction = submission => {
        let textAndIcon = null;
        let toolTip = "";

        if (
            submission.stage === TASK_STATES.REVIEW ||
            submission.stage === TASK_STATES.COMPLETED
        ) {
            textAndIcon = (
                <i className="material-icons" style={{ color: "white" }}>
                    undo
                </i>
            );
            toolTip = "Aufgabe zur Bearbeitung freigeben";
        }

        if (textAndIcon) {
            return (
                <Button
                    style={{ width: 48 }}
                    title={toolTip}
                    variant={"secondary"}
                    className={"m-1"}
                    onClick={() => {
                        resetSubmission(submission);
                    }}
                >
                    {textAndIcon}
                </Button>
            );
        }
        return <></>;
    };

    let completeAll = () => {
        let execCompleteAll = () => {
            AjaxHelper.completeAllSubmissions(task.id)
                .then(resp => {
                    if (resp.status > 0) {
                        EducaHelper.fireSuccessToast(
                            "Rückmeldungen abgegeben",
                            "Alle Rückmeldungen wurden abgegeben."
                        );
                        setSubmissions(resp.payload.submissions);
                    } else throw new Error(resp.message);
                })
                .catch(err => {
                    EducaHelper.fireErrorToast(
                        "Fehler",
                        "Die Rückmeldungen konnten nicht abgegeben werden." +
                            err.message
                    );
                });
        };

        educaModalRef?.current?.openWithCustomButtons(
            "Alle Rückmeldungen abgeben",
            "Hast Du alle Entwürfe für Rückmeldungen eingetragen? Möchtest Du jetzt allen die Rückmeldung abgeben? Du kannst die Rückmeldung danach nicht mehr bearbeiten! Damit wird bei allen der Status auf erledigt gesetzt.",
            [
                <Button
                    variant={"secondary"}
                    onClick={() => educaModalRef?.current?.close()}
                >
                    Abbrechen
                </Button>,
                <Button variant={"success"} onClick={() => execCompleteAll()}>
                    Abgeben
                </Button>
            ]
        );
    };

    let isViewOnly = currentSubmission?.stage === TASK_STATES.COMPLETED;

    let tableData = submissions.map(submission => {
        return {
            name: store.allCloudUsers.find(u => u.id === submission.cloudid)?.name,
            stage: submission.stage,
            isReadComp : submission?.has_seen? <i style={{color : "green"}} className={"fas fa-check"}/> : <i style={{color : "red"}} className={"fas fa-times"}/>,
            updated_at: moment(submission.updated_at).format("DD.MM.YYYY HH:mm"),
            action: <>{getAction(submission)}{getResetAction(submission)}</>,
            displayStage: getStageString(submission.stage)
        }
    }).sort((a, b) => submissionStatusSortComparator(b, a))

    let tableView = (
        <div className={"mt-5"}>
            <div className={"d-flex justify-content-between"}>
                <h5
                    className="mt-2 float-left"
                    style={{ color: "rgb(108, 117, 125)" }}
                >
                    <b>Bearbeitungsstatus</b>
                </h5>
                <div>
                {task.state === TASK_STATES.REVIEW ? (
                    <Button
                        onClick={() => completeAll()}
                        disabled={isViewOnly}
                        className="btn btn-success m-1 float-right"
                    >
                        Alle Rückmeldungen veröffentlichen
                    </Button>
                ) : null}
                    {task.type === "text" && task.state === TASK_STATES.REVIEW ?
                    <Button
                        onClick={() =>  aiModalRef?.current?.open()}
                        disabled={isViewOnly}
                        className="btn btn-secondary m-1 float-right"
                    >
                        <i className="fas fa-robot"></i> Per Learn AI auswerten
                    </Button> : null }
                    </div>
            </div>
            <Card className="mt-2">
                <EducaDefaultTable
                    pageSize={50}
                    size={"lg"}
                    columns={SUBMISSIONS_TABLE_COLUMNS}
                    data={tableData}
                />
            </Card>
            <EducaModal ref={educaModalRef} />
        </div>
    );

    return (
        <div>
            {tableView}
            <TaskEvaluationModal
                submissionsChangedCallback={sub =>
                    props.submissionsChangedCallback(sub)
                }
                task={task}
                ref={evaluationModalRef}
            />
            <TaskAIEvaluationModal
                task={task}
                ref={aiModalRef}
                getAction={getAction}
                getResetAction={getResetAction}
                store={store}
            />
        </div>
    );
}

const SUBMISSIONS_AI_TABLE_COLUMNS = [
    {
        Header: "Name",
        accessor: "name" // accessor is the "key" in the data
    },
    {
        Header: 'Antwort',
        accessor: 'answer'
    },
    {
        Header: 'Learn AI Bewertung',
        accessor: 'result'
    },
    {
        Header: 'Aktion',
        accessor: 'action',
    },
]
class TaskAIEvaluationModalC extends React.Component {
    constructor(props) {
        super(props);
        this.modalRef = createRef();
        this.state = defaultState;
    }

    componentDidMount() {
        this._isMounted = true;
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    open() {
        if (this._isMounted)
            this.setState({
                isOpen: true
            });
    }

    calculcateScore() {
        this.setState({ isLoading: true});
        AjaxHelper.taskAIRateSubmission(this.props.task.id, this.state.correctAnswer)
            .then(resp => {
                if (resp.status > 0) {
                    EducaHelper.fireSuccessToast(
                        "Bewertung abgeschlossen",
                        "Alle Antworten wurden bewertet."
                    );
                    this.setState({ aiResult: resp.payload.aiResult, isLoading: false});
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Die Aufgabe konnte nicht durch Learn AI bewertet werden." +
                    err.message
                );
                this.setState({ isLoading: false});
            });
    }

    render() {

        let tableData = this.props.task?.submissions.map(submission => {
            return {
                name: this.props.store.allCloudUsers.find(u => u.id === submission.cloudid)?.name,
                answer: <div dangerouslySetInnerHTML={SharedHelper.sanitizeHtml(submission.description)} />,
                result: this.state.aiResult?.find(result => result.submission_id === submission.id) ? <ProgressBar variant="success"  now={this.state.aiResult?.find(result => result.submission_id === submission.id)?.score * 100} label={`${Math.round(this.state.aiResult?.find(result => result.submission_id === submission.id)?.score * 100)}%`} /> : null,
                updated_at: moment(submission.updated_at).format("DD.MM.YYYY HH:mm"),
                action: <>{this.props.getAction(submission)}{this.props.getResetAction(submission)}</>,
            }
        }).sort((a, b) => submissionStatusSortComparator(b, a))

        return (
            <Modal
                size={"xl"}
                show={this.state.isOpen}
                backdrop={"static"}
                onHide={() => {
                    this.setState({ isOpen: false });
                }}
            >
                <Modal.Header closeButton>
                    <Modal.Title>
                        Aufgabe per Learn AI auswerten
                    </Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Alert variant={"info"}><b>Hinweis:</b> Geben Sie zunächst die Muster-Antwort in das untenstehende Textfeld ein. Danach erstellt Learn-AI eine Ähnlichkeitsbewertung der Antworten im Verhältnis zu der Musterantwort. Dafür nutzen wir Technologien des Deep-Learnings, um den Inhalt der Antworten zu verstehen und mit der Muster-Antwort zu vergleichen.</Alert>
                    <DisplayPair title={"Korrekte Antwort"}>
                        <EducaTextArea
                            value={this.state.correctAnswer}
                            minRows={5}
                            onChange={(evt) => this.setState({ correctAnswer: evt.target.value })}
                        />
                    </DisplayPair>
                    { this.state.isLoading ? <EducaLoading/> :
                        <Button className={"btn btn-success"} onClick={() => this.calculcateScore()}>
                            Bewerten
                        </Button> }
                    { this.state.aiResult?
                    <div className={"mt-3"}>
                        <EducaDefaultTable
                            pageSize={50}
                            size={"lg"}
                            columns={SUBMISSIONS_AI_TABLE_COLUMNS}
                            data={tableData}
                        />
                    </div> : null }
                </Modal.Body>
                <Modal.Footer>

                </Modal.Footer>
            </Modal>
        );
    }
}

export const TaskAIEvaluationModal = connect(null, null, null, {
    forwardRef: true
})(TaskAIEvaluationModalC);


const defaultState = {
    isOpen: false,
    currentSubmission: null
};

class TaskEvaluationModalC extends React.Component {
    constructor(props) {
        super(props);
        this.modalRef = createRef();
        this.state = defaultState;
    }

    componentDidMount() {
        this._isMounted = true;
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    open(submission) {
        if (this._isMounted)
            this.setState({
                isOpen: true,
                currentSubmission: submission
            });
    }

    submitAndClose() {
        let modalCallback = btn => {
            if (btn === MODAL_BUTTONS.YES) this.submitEvaluation(true);
        };
        this.modalRef.current.open(
            btn => modalCallback(btn),
            "Rückmeldung abgeben",
            <div className={"d-flex flex-column"}>
                Möchtest Du diese Rückmeldung wirklich speichern? Die
                Rückmeldung kann dann nicht mehr verändert werden.
            </div>,
            [MODAL_BUTTONS.NO, MODAL_BUTTONS.YES]
        );
    }

    close() {
        this.setState(defaultState);
    }

    saveEvaluation(doClose) {
        let currentSubmission = this.state.currentSubmission;
        AjaxHelper.updateSubmission(
            currentSubmission.task_id,
            currentSubmission.id,
            null,
            currentSubmission.points,
            currentSubmission.rating
        )
            .then(resp => {
                if (resp.status > 0) {
                    EducaHelper.fireSuccessToast(
                        "Rückmeldung gespeichert",
                        "Du hast deinen Rückmeldungsentwurf erfolgreich gespeichert."
                    );
                    if (doClose) this.close();
                    this.props.submissionsChangedCallback(
                        resp.payload.submission
                    );
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Die Antwort wurde nicht gespeichert." + err.message
                );
            });
    }

    submitEvaluation(doClose) {
        let currentSubmission = this.state.currentSubmission;
        AjaxHelper.updateSubmission(
            currentSubmission.task_id,
            currentSubmission.id,
            null,
            currentSubmission.points,
            currentSubmission.rating,
            TASK_STATES.COMPLETED
        )
            .then(resp => {
                if (resp.status > 0) {
                    EducaHelper.fireSuccessToast(
                        "Rückmeldung gespeichert",
                        "Du hast deine Rückmeldung erfolgreich übermittelt."
                    );
                    // setCurrentSubmission(resp.payload.submission)
                    if (doClose) this.close();
                    this.props.submissionsChangedCallback(
                        resp.payload.submission
                    );
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Die Rückmeldung wurde nicht gespeichert." + err.message
                );
            });
    }

    getResponseComponent(viewOnly) {
        let currentSubmission = this.state.currentSubmission;
        return (
            <>
                <div className={"m-2"}>
                    <label>Textuelle Rückmeldung</label>
                    <CKEditor
                        config={EducaCKEditorDefaultConfig}
                        editor={window.ClassicEditor}
                        disabled={this.isViewOnly() || viewOnly}
                        data={
                            currentSubmission.rating
                                ? currentSubmission.rating
                                : ""
                        }
                        onChange={(event, editor) => {
                            const data = editor.getData();
                            this.setState({
                                currentSubmission: {
                                    ...this.state.currentSubmission,
                                    rating: data
                                }
                            });
                        }}
                    />
                </div>
                <div className={"m-2"}>
                    <label>Punkte</label>
                    <NumberInput
                        min={0}
                        max={100}
                        value={currentSubmission.points}
                        onChangeNumber={(number) => {
                            this.setState({
                                currentSubmission: {
                                    ...this.state.currentSubmission,
                                    points: number
                                }
                            });
                        }}
                    />
                </div>
            </>
        );
    }

    getDetailsSubmissionView() {
        let task = this.props.task;


        let onxAPIStatement = () => {
            // do nothing
        };

        let view = (
            <div>
                <h5 className={"mt-2 pl-1"}>
                    <b>Antwort</b>
                </h5>
                <CKEditor
                    className={"m-2"}
                    disabled={true}
                    config={{ toolbar: [] }}
                    editor={window.ClassicEditor}
                    data={
                        this.state.currentSubmission?.description
                            ? this.state.currentSubmission?.description
                            : ""
                    }
                />
            </div>
        );
        if (task.type === "document") {
            view = (
                <div>
                    <h5 className={"mt-2 pl-1"}>
                        <b>Antwort</b>
                    </h5>
                    <EducaFileBrowser
                        modelType={MODELS.SUBMISSION}
                        modelId={this.state.currentSubmission.id}
                        canUserEdit={false}
                        canUserUpload={true}
                    />
                </div>
            );
        } else if (task.type === "check") {
            view = (
                <h5>
                    {this.state.currentSubmission?.stage ===
                    TASK_STATES.COMPLETED
                        ? "Die Aufgabe wurde als erledigt markiert."
                        : "Die Aufgabe wurde noch nicht als erledigt markiert."}
                </h5>
            );
        } else if (task.type === "form") {
            view = (
                <Card className="mt-2">
                    <Card.Body>
                        <EducaAutoFormBuilder
                            readOnly={true}
                            modelId={this.state.currentSubmission.id}
                            modelType={"submission"}
                            formId={task.formular_id}
                        />
                    </Card.Body>
                </Card>
            );
        } else if (this.state.currentSubmission?.description == null) {
            view = <h5>Die Aufgabe wurde noch nicht als erledigt markiert.</h5>;
        }
        return <>{view}</>;
    }

    getEvaluationView() {
        let task = this.props.task;
        let currentSubmission = this.state.currentSubmission;
        if (
            task.type === "check" &&
            currentSubmission?.stage === TASK_STATES.COMPLETED
        ) {
            return (
                <div id={"evaluationViewTaskAdminView" + currentSubmission?.id}>
                    <Card className="mt-2">
                        <div className="row">
                            <div className="col">
                                <div className={"m-2"}>
                                    {this.getDetailsSubmissionView()}
                                </div>
                                <div className={"m-2"}>
                                    Für diesen Aufgabentyp ist keine Rückmeldung
                                    möglich.
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>
            );
        }

        if (task.type === "form") {
            return (
                <div id={"evaluationViewTaskAdminView" + currentSubmission?.id}>
                    <h5 className={"mt-2 pl-1"}>
                        <b>Antwort</b>
                    </h5>
                    <Card className="mt-2">
                        <Card.Body>
                            <EducaAutoFormBuilder
                                readOnly={true}
                                modelId={currentSubmission.id}
                                modelType={"submission"}
                                formId={task.formular_id}
                            />
                        </Card.Body>
                    </Card>
                    <h5 className={"mt-2 pb-1 pl-1"}>
                        <b>Rückmeldung</b>
                    </h5>
                    {this.getResponseComponent()}
                </div>
            );
        }

        return (
            <div id={"evaluationViewTaskAdminView" + currentSubmission?.id}>
                <Card className="mt-2">
                    <div className="row">
                        <div className="col">
                            <div className={"m-2 pb-3"}>
                                {this.getDetailsSubmissionView()}
                            </div>
                            <h5 className="m-1 pb-1 pl-1">
                                <b>Rückmeldung</b>
                            </h5>
                            {this.getResponseComponent()}
                        </div>
                    </div>
                </Card>
            </div>
        );
    }

    isViewOnly() {
        let currentSubmission = this.state.currentSubmission;
        return currentSubmission?.stage === TASK_STATES.COMPLETED;
    }

    isDraft() {
        let currentSubmission = this.state.currentSubmission;
        return (
            currentSubmission && currentSubmission?.stage === TASK_STATES.DRAFT
        );
    }

    isReview() {
        let currentSubmission = this.state.currentSubmission;
        return (
            currentSubmission && currentSubmission.stage === TASK_STATES.REVIEW
        );
    }

    isCompleted() {
        let currentSubmission = this.state.currentSubmission;
        return (
            currentSubmission &&
            currentSubmission.stage === TASK_STATES.COMPLETED
        );
    }

    isReviewOrCompleted() {
        let currentSubmission = this.state.currentSubmission;
        return currentSubmission && (this.isReview() || this.isCompleted());
    }
    getContent() {
        let currentSubmission = this.state.currentSubmission;
        if (!currentSubmission) return <></>;
        if (this.isDraft()) {
            return this.getDetailsSubmissionView();
        }
        if (this.isReviewOrCompleted()) {
            return this.getEvaluationView();
        }
    }

    render() {
        return (
            <Modal
                size={"xl"}
                show={this.state.isOpen}
                backdrop={"static"}
                onHide={() => {
                    this.setState({ isOpen: false });
                }}
            >
                <Modal.Header closeButton>
                    <Modal.Title>
                        {this.isDraft()
                            ? "Aktueller Stand (" +
                              this.props.store.allCloudUsers.find(
                                  u =>
                                      u.id ===
                                      this.state.currentSubmission?.cloudid
                              )?.name +
                              ")"
                            : this.isReviewOrCompleted()
                            ? "Rückmeldung zur Aufgabe (" +
                              this.props.store.allCloudUsers.find(
                                  u =>
                                      u.id ===
                                      this.state.currentSubmission?.cloudid
                              )?.name +
                              ")"
                            : null}
                    </Modal.Title>
                </Modal.Header>
                <Modal.Body>{this.getContent()}</Modal.Body>
                <Modal.Footer>
                    {this.isReview() ? (
                        <Button
                            onClick={() => {
                                this.saveEvaluation(true);
                            }}
                            variant={"success"}
                            className={"m-2"}
                        >
                            Entwurf speichern
                        </Button>
                    ) : null}
                    {this.isDraft() || this.isCompleted() ? null : (
                        <Button
                            onClick={() => {
                                this.setState({ isOpen: false });
                            }}
                            variant={"secondary"}
                        >
                            Abbrechen
                        </Button>
                    )}
                    <Button
                        onClick={() => {
                            this.isDraft() || this.isCompleted()
                                ? this.close()
                                : this.submitAndClose();
                        }}
                        variant={"primary"}
                        className={"m-2"}
                    >
                        {this.isDraft() || this.isCompleted()
                            ? "Schließen"
                            : "Rückmeldung abgeben"}
                    </Button>
                </Modal.Footer>
                <EducaModal ref={this.modalRef} size={"lg"} />
            </Modal>
        );
    }
}

const mapStateToProps = state => ({ store: state });
export const TaskEvaluationModal = connect(mapStateToProps, null, null, {
    forwardRef: true
})(TaskEvaluationModalC);
