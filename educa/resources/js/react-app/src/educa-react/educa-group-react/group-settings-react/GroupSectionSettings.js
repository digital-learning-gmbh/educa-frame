import React, { Component } from "react";
import { Col, Row } from "react-bootstrap";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import ReactSwitch from "react-switch";
import { EducaInputConfirm } from "../../../shared/shared-components/Inputs";
import EducaHelper, {LIMITS} from "../../helpers/EducaHelper";
import Button from "react-bootstrap/Button";
import SafeDeleteModal from "../../../shared/shared-components/SafeDeleteModal";
import { withRouter } from "react-router";
import Card from "react-bootstrap/Card";
import ReactTextareaAutosize from "react-textarea-autosize";

export const GROUP_SETTINGS_TABS = {
    GENERAL_GROUP_SETTINGS: "generalGroupSettings",
    APP_SETTINGS: "appSettings"
};

class GroupSectionSettings extends Component {
    constructor(props) {
        super(props);

        this.state = {
            availableSectionApps: [],
            activatedSectionApps: [],
            section: props.section,

            isComponentLocked: false,
            image: null,
            description: null
        };
        this.deleteModalRef = React.createRef();
    }

    componentDidMount() {
        this._isMounted = true;
        this.setState({ section: this.props.section }, () =>
            this.getSectionSettingsInfos()
        );
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (
            this.props.section &&
            prevProps.section.id !== this.props.section.id
        )
            this.componentDidMount();
    }

    getSectionSettingsInfos() {
        if (this._isMounted)
            this.setState({
                activatedSectionApps: this.state.section.section_group_apps
            });
        AjaxHelper.getAvailableSectionApps(this.state.section.id).then(resp => {
            if (resp.payload)
                if (this._isMounted)
                    this.setState({ availableSectionApps: resp.payload });
        });
    }

    updateSectionName() {
        AjaxHelper.updateSection(this.state.section.id, this.state.section.name)
            .then(resp => {
                if (resp.status > 0 && resp?.payload?.section) {
                    EducaHelper.fireSuccessToast(
                        "Erfolg",
                        "Der Bereichsname wurde erfolgreich geändert"
                    );
                    this.props.updateSection(resp.payload["section"]);
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Der Bereichsname konnte nicht geändert werden. " +
                        err.message
                );
            });
    }

    updateSectionDescription() {
        AjaxHelper.updateSection(this.state.section.id, undefined, this.state.description)
            .then(resp => {
                if (resp.status > 0 && resp?.payload?.section) {
                    EducaHelper.fireSuccessToast(
                        "Erfolg",
                        "Die Beschreibung wurde erfolgreich geändert"
                    );
                    this.props.updateSection(resp.payload["section"]);
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Der Beschreibung konnte nicht geändert werden. " +
                    err.message
                );
            });
    }

    toggleApp(app) {
        if (this.state.isComponentLocked) return;
        if (this._isMounted)
            this.setState({ isComponentLocked: true }, () => {
                let sectionApp = this.state.activatedSectionApps.find(
                    activatedApp => activatedApp.group_app.type === app.type
                );
                if (sectionApp) {
                    let deactivate = () => {
                        AjaxHelper.removeSectionGroupApp(
                            this.state.section.id,
                            sectionApp.id
                        )
                            .then(resp => {
                                if (
                                    resp.status > 0 &&
                                    resp.payload &&
                                    resp.payload["section"]
                                ) {
                                    this.props.updateSection(
                                        resp.payload["section"]
                                    );

                                    let arr = this.state.activatedSectionApps;
                                    arr.splice(
                                        this.state.activatedSectionApps.indexOf(
                                            sectionApp
                                        ),
                                        1
                                    );
                                    if (this._isMounted)
                                        this.setState({
                                            activatedSectionApps: arr
                                        });
                                    return;
                                }
                                throw new Error("");
                            })
                            .catch(err => {
                                EducaHelper.fireErrorToast(
                                    "Fehler",
                                    "Die App konnte nicht entfernt werden. " +
                                        err.message
                                );
                            })
                            .finally(() => {
                                window.setTimeout(() => {
                                    if (this._isMounted)
                                        this.setState({
                                            isComponentLocked: false
                                        });
                                }, 500);
                            });
                    };
                    const word = "DEAKTIVIEREN";
                    this.deleteModalRef.current?.open(
                        flag =>
                            flag
                                ? deactivate()
                                : this.setState({ isComponentLocked: false }),
                        "App deaktivieren",
                        "Wenn du diese App deaktivierst gehen sämtliche App-relevanten Daten verloren. Wenn du damit einverstanden bist schreibe '" +
                            word +
                            "' in das Textfeld.",
                        word
                    );
                } else {
                    AjaxHelper.addSectionGroupApp(
                        this.state.section.id,
                        app.type
                    )
                        .then(resp => {
                            if (
                                resp.status > 0 &&
                                resp.payload &&
                                resp.payload["section"]
                            ) {
                                this.props.updateSection(
                                    resp.payload["section"]
                                );

                                if (this._isMounted)
                                    this.setState({
                                        activatedSectionApps:
                                            resp.payload["section"]
                                                .section_group_apps
                                    });
                                return;
                            }
                            throw new Error("");
                        })
                        .catch(err => {
                            EducaHelper.fireErrorToast(
                                "Fehler",
                                "Die App konnte nicht aktiviert werden. " +
                                    err.message
                            );
                        })
                        .finally(() => {
                            window.setTimeout(() => {
                                if (this._isMounted)
                                    this.setState({ isComponentLocked: false });
                            }, 500);
                        });
                }
            });
    }

    removeSection() {
        AjaxHelper.removeSection(this.props.section.id)
            .then(resp => {
                if (resp.status > 0) {
                    this.props.removeSection(this.props.section.id);

                    EducaHelper.fireSuccessToast(
                        "Erfolg",
                        "Der Bereich wurde erfolgreich gelöscht."
                    );
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Fehler beim löschen des Bereichs. " + err.message
                );
            });
    }

    openDeleteModal() {
        this.deleteModalRef?.current?.open(
            flag => {
                if (flag) this.removeSection();
            },
            "Bereich löschen",
            "Soll der Bereich '" +
                this.props.section.name +
                "' wirklich gelöscht werden? Geben Sie bitte zur Bestätigung 'LÖSCHEN' in das Textfeld ein.",
            "LÖSCHEN"
        );
    }

    getAppGrid() {
        const COLS = 3;
        if (
            !this.state.availableSectionApps ||
            this.state.availableSectionApps.length === 0
        )
            return <div>Fehler</div>;

        let rows = [];
        for (let i = 0; i < this.state.availableSectionApps.length; i) {
            let rest = this.state.availableSectionApps.length - i;
            if (rest === 0) break;

            if (rest >= COLS) {
                let cols = [];
                for (let j = i; j < i + COLS; j++) {
                    cols.push(
                        this.createColumnComponentForGrid(
                            this.state.availableSectionApps[j]
                        )
                    );
                }
                rows.push(<Row key={"row_gs_" + i}>{cols}</Row>);
                i = i + COLS;
            } else if (rest < COLS) {
                let cols = [];
                for (
                    let j = this.state.availableSectionApps.length - rest;
                    j < this.state.availableSectionApps.length;
                    j++
                ) {
                    cols.push(
                        this.createColumnComponentForGrid(
                            this.state.availableSectionApps[j]
                        )
                    );
                }
                rows.push(<Row key={"row_gs_" + i}>{cols}</Row>);
                break;
            }
        }
        return rows;
    }

    updateImage(image, section_id) {
        AjaxHelper.setSectionImage(section_id, image)
            .then(resp => {
                if (resp.status > 0 && resp?.payload?.section) {
                    EducaHelper.fireSuccessToast(
                        "Erfolg",
                        "Das Bereichsbild wurde erfolgreich geändert"
                    );

                    this.props.updateSection(resp.payload.section);
                    this.setState({image: null});
                    this.setState({section: resp.payload.section});
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Das Bereichsbild konnte nicht geändert werden. " + err.messages
                );
            });
    }

    createColumnComponentForGrid(app) {
        return (
            <Col xs={4} key={app.id}>
                <div
                    style={{
                        display: "flex",
                        flexDirection: "column",
                        margin: "20px"
                    }}
                >
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "column",
                            justifyContent: "center"
                        }}
                    >
                        <div
                            style={{
                                display: "flex",
                                flexDirection: "row",
                                justifyContent: "center"
                            }}
                        >
                            {app.icon?.startsWith("fa") ? (
                                <i
                                    className={app.icon}
                                    style={{ fontSize: "25px" }}
                                ></i>
                            ) : (
                                <img
                                    alt="App Icon"
                                    src={app.icon}
                                    style={{ height: "25px", width: "25px" }}
                                />
                            )}
                        </div>
                        <div
                            style={{
                                display: "flex",
                                flexDirection: "row",
                                justifyContent: "center"
                            }}
                        >
                            <div>{app.name}</div>
                        </div>
                    </div>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "row",
                            justifyContent: "center"
                        }}
                    >
                        <ReactSwitch
                            disabled={this.state.isComponentLocked}
                            checked={this.state.activatedSectionApps?.find(
                                activatedApp =>
                                    activatedApp.group_app.type === app.type
                            )}
                            onChange={() => {
                                this.toggleApp(app);
                            }}
                        />
                    </div>
                </div>
            </Col>
        );
    }

    render() {
        return (
            <Card>
                <Card.Body>
                    <div
                        style={{display: "flex", flexDirection: "column"}}
                        className={"m-2"}
                    >
                        <div style={{display: "flex", flexDirection: "row"}}>
                            <h6
                                className={"mr-2 pt-2"}
                                style={{minWidth: "120px"}}
                            >
                                Bereichsname
                            </h6>
                            <div
                                style={{
                                    display: "flex",
                                    flexDirection: "row",
                                    maxWidth: "350px"
                                }}
                            >
                                <EducaInputConfirm
                                    placeholder={"Name des Bereichs"}
                                    maxLetters={200}
                                    value={this.state.section.name}
                                    onChange={evt =>
                                        this.setState({
                                            section: {
                                                ...this.state.section,
                                                name: evt.target.value
                                            }
                                        })
                                    }
                                    onConfirmClick={() =>
                                        this.updateSectionName()
                                    }
                                />
                            </div>

                            <div
                                style={{
                                    display: "flex",
                                    flex: 1,
                                    flexDirection: "row",
                                    justifyContent: "flex-end"
                                }}
                            >
                                <div>
                                    <Button
                                        onClick={() => this.openDeleteModal()}
                                        variant={"danger"}
                                    >
                                        <i className="fas fa-trash"></i> Bereich
                                        löschen
                                    </Button>
                                </div>
                            </div>
                        </div>
                        <div style={{display: "flex", flexDirection: "row"}}>
                            <h6
                                className={"mr-2 pt-2"}
                                style={{minWidth: "120px"}}
                            >
                                Beschreibung
                            </h6>
                            <ReactTextareaAutosize
                                maxLength={LIMITS.GROUP_DESCRIPTION}
                                maxRows={6}
                                minRows={3}
                                value={this.state.section.description}
                                className="form-control editor"
                                placeholder="Wofür ist der Bereich da?"
                                onChange={evt => {
                                    this.setState({ description: evt.target.value});

                                    if (
                                        evt.target.value.length >
                                        LIMITS.GROUP_DESCRIPTION - 1
                                    ) {
                                        EducaHelper.fireWarningToast(
                                            "Hinweis",
                                            "Das Zeichenlimit für die Gruppenbeschreibung bei " +
                                            LIMITS.GROUP_DESCRIPTION +
                                            " Zeichen"
                                        );
                                    }
                                }}
                            />
                        {(this.state.description ?? "") !==
                        (this.state.section.description ?? "") ? (
                            <div
                                className={"m-2"}
                                style={{
                                    display: "flex",
                                    flexDirection: "row",
                                    maxWidth: "600px"
                                }}
                            >
                                <h6
                                    className={"mr-2 pt-2"}
                                    style={{ minWidth: "120px" }}
                                ></h6>
                                <Button
                                    onClick={() =>
                                        this.updateSectionDescription()
                                    }
                                    className={"btn btn-primary"}
                                >
                                    Speichern
                                </Button>
                            </div>
                        ) : null}
                        </div>
                            <div style={{display: "flex", flexDirection: "row"}}>
                                <h6
                                    className={"mr-2 pt-2"}
                                    style={{minWidth: "120px"}}
                                >
                                    Bild des Bereichs
                                </h6>
                                <div>
                                    <img loading={"lazy"} className="img-responsive m-2" src={
                                        AjaxHelper.getSectionAvatarUrl(
                                            this.state.section.id,
                                            200,
                                            this.state.section.image
                                        )} alt="Card image" style={{
                                        borderRadius: "calc(1rem - 1px)",
                                        border: "1px solid #eee",
                                        objectFit: "cover",
                                        width: "200px",
                                        height: "200px"
                                    }}></img>
                                <input
                                    type="file"
                                    accept=".jpeg,.jpg,.png"
                                    id={"input_section_settings" + this.state.section.id}
                                    onChange={evt => {
                                        if (evt.target.files.length > 0)
                                            this.setState({image: evt.target.files[0]});
                                    }}
                                    style={{width: "0px"}}
                                />
                                {this.state.image ? (
                                    <>
                                        <div
                                            className={"mr-1"}
                                            style={{
                                                textOverflow: "ellipsis",
                                                maxWidth: "400px",
                                                overflow: "hidden"
                                            }}
                                        >
                                            {this.state.image.name}
                                        </div>
                                        <Button
                                            title={"Hochladen"}
                                            className={"mr-1"}
                                            style={{minWidth: "40px"}}
                                            onClick={() =>
                                                this.updateImage(
                                                    this.state.image,
                                                    this.state.section.id
                                                )
                                            }
                                            variant="success"
                                        >
                                            <i className={"fa fa-check"}/>{" "}
                                        </Button>
                                        <Button
                                            title={"Abbrechen"}
                                            style={{minWidth: "40px"}}
                                            onClick={() => this.setState({image: null})}
                                            variant="danger"
                                        >
                                            <i className={"fa fa-times"}/>{" "}
                                        </Button>
                                    </>
                                ) : (
                                    <Button
                                        style={{minWidth: "150px"}}
                                        title="Bild auswählen"
                                        className="btn-primary"
                                        onClick={() => {
                                            window.document.getElementById(
                                                "input_section_settings" +
                                                this.state.section.id
                                            ).click();
                                        }}
                                        type="button"
                                    >
                                        Bild auswählen
                                    </Button>
                                )}

                                </div>
                            </div>
                            <h6 className={"mr-2 pt-2"}>Apps</h6>
                            {this.getAppGrid()}
                            <p className={"text-muted text-center"}>
                                Weitere Apps findest du im educa-Store.
                            </p>
                        </div>
                </Card.Body>
                <SafeDeleteModal ref={this.deleteModalRef}/>
            </Card>
    )
    ;
    }
    }

    export default withRouter(GroupSectionSettings);
