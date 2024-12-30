import React, { useRef, useState } from "react";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper, { LIMITS } from "../../helpers/EducaHelper";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import { BASE_ROUTES } from "../../App";
import { GENERAL_REMOVE_GROUP } from "../../reducers/GeneralReducer";
import { withRouter } from "react-router";
import { connect } from "react-redux";
import Card from "react-bootstrap/Card";
import { EducaInputConfirm } from "../../../shared/shared-components/Inputs";
import ReactTextareaAutosize from "react-textarea-autosize";
import Button from "react-bootstrap/Button";
import { CirclePicker } from "react-color";
import Select from "react-select";
import SafeDeleteModal from "../../../shared/shared-components/SafeDeleteModal";
import EducaModal, {MODAL_BUTTONS} from "../../../shared/shared-components/EducaModal";
import {FormControl} from "react-bootstrap";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";

export const GROUP_SETTINGS_TABS = {
    GENERAL_GROUP_SETTINGS: "generalgroupWithSettings",
    APP_SETTINGS: "appSettings"
};

export const GROUP_VISIBILITY_SETTINGS = [
    {
        value: "closed",
        label: "Geschlossen, Mitglieder müssen manuell hinzugefügt werden"
    },
    { value: "open", label: "Öffentlich, für alle Tenants" },
    { value: "open_restricted", label: "Öffentlich, nur im Tenant" }
    // {value: "restricted", label: "Bestätigung"}
];

const DEFAULT_COLORS = [
    "#343A40",
    "#3490dc",
    "#f44336",
    "#e91e63",
    "#9c27b0",
    "#673ab7",
    "#3f51b5",
    "#2196f3",
    "#03a9f4",
    "#00bcd4",
    "#009688",
    "#4caf50",
    "#8bc34a",
    "#cddc39",
    "#ffeb3b",
    "#ff9800",
    "#ff5722",
    "#795548"
];

function GroupSettingsTab(props) {
    const deleteModalRef = useRef();
    const educaModalRef = useRef();

    return (
        <div>
            <div
                style={{
                    width: "750px",
                    marginLeft: "auto",
                    marginRight: "auto"
                }}
            >
                <GroupSettingsTabGeneral {...props} />

                <GroupSettingsTabVisibility {...props} />
                <GroupSettingsTabAdvanced
                    {...props}
                    deleteModalRef={deleteModalRef}
                    educaModalRef={educaModalRef}
                />
            </div>
            <SafeDeleteModal ref={deleteModalRef} />
            <EducaModal ref={educaModalRef}/>
        </div>
    );
}

function GroupSettingsTabGeneral(props) {
    const [name, setName] = useState(props.group.name ?? "");
    const [description, setDescription] = useState(
        props.group.description ?? ""
    );
    const [image, setImage] = useState(undefined);
    const [color, setColor] = useState(props.group.color);

    return (
        <>
            <h5 className={"m-2"}>
                <b>Allgemein</b>
            </h5>
            <Card className={"m-2"}>
                <Card.Body>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "column",
                            flex: 1
                        }}
                    >
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
                            >
                                Name
                            </h6>
                            <EducaInputConfirm
                                placeholder="Gruppenname"
                                maxLetters={200}
                                value={name}
                                onChange={evt => setName(evt.target.value)}
                                onConfirmClick={() =>
                                    updateName(
                                        name,
                                        props.group,
                                        props.setGroup
                                    )
                                }
                            />
                        </div>

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
                            >
                                Beschreibung
                            </h6>
                            <ReactTextareaAutosize
                                maxLength={LIMITS.GROUP_DESCRIPTION}
                                maxRows={6}
                                minRows={3}
                                value={description}
                                className="form-control editor"
                                placeholder="Wofür ist die Gruppe da?"
                                onChange={evt => {
                                    setDescription(evt.target.value);

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
                            ></ReactTextareaAutosize>
                        </div>
                        {(description ?? "") !==
                        (props.group.description ?? "") ? (
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
                                        updateDescription(
                                            description,
                                            props.group,
                                            props.setGroup
                                        )
                                    }
                                    className={"btn btn-primary"}
                                >
                                    Speichern
                                </Button>
                            </div>
                        ) : null}
                        <div
                            className={"m-2"}
                            style={{
                                display: "flex",
                                flexDirection: "row",
                                maxWidth: "400px"
                            }}
                        >
                            <h6
                                className={"mr-2 pt-2"}
                                style={{ minWidth: "120px" }}
                            >
                                Bild
                            </h6>
                            <input
                                type="file"
                                accept=".jpeg,.jpg,.png"
                                id={"input_group_settings" + props.group.id}
                                onChange={evt => {
                                    if (evt.target.files.length > 0)
                                        setImage(evt.target.files[0]);
                                }}
                                style={{ width: "0px" }}
                            />
                            {image ? (
                                <>
                                    <div
                                        className={"mr-1"}
                                        style={{
                                            textOverflow: "ellipsis",
                                            maxWidth: "400px",
                                            overflow: "hidden"
                                        }}
                                    >
                                        {image.name}
                                    </div>
                                    <Button
                                        title={"Hochladen"}
                                        className={"mr-1"}
                                        style={{ minWidth: "40px" }}
                                        onClick={() =>
                                            updateImage(
                                                image,
                                                setImage,
                                                props.group,
                                                props.setGroup
                                            )
                                        }
                                        variant="success"
                                    >
                                        <i className={"fa fa-check"} />{" "}
                                    </Button>
                                    <Button
                                        title={"Abbrechen"}
                                        style={{ minWidth: "40px" }}
                                        onClick={() => setImage(undefined)}
                                        variant="danger"
                                    >
                                        <i className={"fa fa-times"} />{" "}
                                    </Button>
                                </>
                            ) : (
                                <Button
                                    style={{ minWidth: "150px" }}
                                    title="Bild auswählen"
                                    className="btn-primary"
                                    onClick={() => {
                                        window.document.getElementById(
                                            "input_group_settings" +
                                                props.group.id
                                        ).click();
                                    }}
                                    type="button"
                                >
                                    Bild auswählen
                                </Button>
                            )}
                        </div>
                        <div
                            className={"m-2"}
                            style={{
                                display: "flex",
                                flexDirection: "row"
                            }}
                        >
                            <h6
                                className={"mr-2 pt-2"}
                                style={{ minWidth: "120px" }}
                            >
                                Farbe
                            </h6>
                            <CirclePicker
                                colors={DEFAULT_COLORS}
                                color={color}
                                onChangeComplete={color => setColor(color.hex)}
                            />
                            {color.toString() !== props.group.color ? (
                                <div
                                    style={{
                                        display: "flex",
                                        flexDirection: "column",
                                        justifyContent: "center"
                                    }}
                                >
                                    <Button
                                        title={"Farbe ändern"}
                                        style={{
                                            marginLeft: "2rem",
                                            width: "40px",
                                            height: " 40px"
                                        }}
                                        onClick={() =>
                                            updateColor(
                                                color,
                                                props.group,
                                                props.setGroup
                                            )
                                        }
                                        variant="success"
                                    >
                                        <i className={"fa fa-check"} />{" "}
                                    </Button>
                                </div>
                            ) : null}
                        </div>

                        <div
                            className={"m-2"}
                            style={{
                                display: "flex",
                                flexDirection: "row",
                                justifyContent: "center",
                                marginTop: "10px"
                            }}
                        ></div>
                    </div>
                </Card.Body>
            </Card>
        </>
    );
}

function GroupSettingsTabVisibility(props) {
    const [visibility, setVisibility] = useState(props.group.type);

    return (
        <>
            <h5 className={"m-2"}>
                <b>Sichtbarkeit</b>
            </h5>
            <Card className={"m-2"}>
                <Card.Body>
                    <div
                        className={"m-2"}
                        style={{
                            display: "flex",
                            flexDirection: "row"
                        }}
                    >
                        <h6
                            className={"mr-2 pt-2"}
                            style={{ minWidth: "120px" }}
                        >
                            Gruppe gehört zur Plattform
                        </h6>
                        <h6
                            className={"mr-2 pt-2"}
                            style={{ minWidth: "120px" }}
                        >
                            <b>{props.group.tenant?.name}</b>
                        </h6>
                    </div>
                    <div
                        className={"m-2"}
                        style={{
                            display: "flex",
                            flexDirection: "row"
                        }}
                    >
                        <h6
                            className={"mr-2 pt-2"}
                            style={{ minWidth: "120px" }}
                        >
                            Gruppenbeitritt
                        </h6>
                        <div style={{ width: "500px" }}>
                            <Select
                                value={GROUP_VISIBILITY_SETTINGS.find(
                                    e => e.value === visibility
                                )}
                                options={GROUP_VISIBILITY_SETTINGS}
                                placeholder={
                                    "Gruppenbeitrittsregeln auswählen..."
                                }
                                closeMenuOnSelect={true}
                                onChange={evt => setVisibility(evt.value)}
                            />
                        </div>
                    </div>
                    {visibility !== props.group.type ? (
                        <Button
                            onClick={() =>
                                updateVisibility(
                                    visibility,
                                    props.group,
                                    props.setGroup
                                )
                            }
                            className={"btn btn-primary"}
                        >
                            Speichern
                        </Button>
                    ) : null}
                </Card.Body>
            </Card>
        </>
    );
}

function GroupSettingsTabAdvanced(props) {
    return (
        <>
            <h5 className={"m-2"}>
                <b>Erweitert</b>
            </h5>
            <Card className={"m-2"}>
                <Card.Body>
                    <p>
                        Hier gibt es die Möglichkeit eine Gruppe zu archivieren
                        oder permanent löschen. Archiverte Gruppen können nur
                        von Administratoren wieder aktiviert werden.
                    </p>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "row",
                            flex: 1
                        }}
                    >
                        <div
                            style={{
                                display: "flex",
                                flexDirection: "column"
                            }}
                        >
                            {FliesentischZentralrat.groupArchiveGroup(
                                props.group
                            ) ? (
                                <Button
                                    onClick={() =>
                                        openArchiveModal(
                                            props.deleteModalRef,
                                            props.history,
                                            props.group,
                                            props.removeGroup
                                        )
                                    }
                                    className={"m-2"}
                                    variant={"danger"}
                                >
                                    Gruppe archivieren
                                </Button>
                            ) : null}

                            {FliesentischZentralrat.groupDeleteGroup(
                                props.group
                            ) ? (
                                <Button
                                    onClick={() =>
                                        openDeleteModal(
                                            props.deleteModalRef,
                                            props.history,
                                            props.group,
                                            props.removeGroup
                                        )
                                    }
                                    className={"m-2"}
                                    variant={"danger"}
                                >
                                    Gruppe löschen
                                </Button>
                            ) : null}
                        </div>
                    </div>
                    <p>
                        Hier gibt es die Möglichkeit eine Gruppe zu einem Template umzuwandeln. Dadurch kann eine Gruppe auf Basis des Templates erstellt werden.
                        Die Templates werden bei der Erstellung einer Gruppe zur Auswahl angezeigt.
                    </p>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "row",
                            flex: 1
                        }}
                    >
                        <div
                            style={{
                                display: "flex",
                                flexDirection: "column"
                            }}
                        >
                            {FliesentischZentralrat.groupDeleteGroup(
                                props.group
                            ) ? (
                                <Button
                                    onClick={() =>
                                        openCreateGroupTemplate(
                                            props.educaModalRef,
                                            props.history,
                                            props.group,
                                            props.removeGroup
                                        )
                                    }
                                    className={"m-2"}
                                    variant={"primary"}
                                >
                                    Template aus der Gruppe erstellen
                                </Button>
                            ) : null}
                        </div>
                    </div>
                </Card.Body>
            </Card>
        </>
    );
}

const mapStateToProps = state => ({ store: state });

const mapDispatchToProps = dispatch => {
    return {
        removeGroup: group =>
            dispatch({ type: GENERAL_REMOVE_GROUP, payload: group })
    };
};

export default withRouter(
    connect(mapStateToProps, mapDispatchToProps)(GroupSettingsTab)
);

function updateName(name, group, setGroup) {
    AjaxHelper.setGroupSettings(group.id, {
        name
    })
        .then(resp => {
            if (resp.status > 0 && resp.payload?.group) {
                EducaHelper.fireSuccessToast(
                    "Erfolg",
                    "Der Gruppenname wurde erfolgreich geändert."
                );

                setGroup(resp.payload.group);
                return;
            }
            throw new Error(resp.message);
        })
        .catch(err => {
            EducaHelper.fireErrorToast(
                "Fehler",
                "Der Gruppenname konnte nicht geändert werden. " + err.messages
            );
        });
}

function updateVisibility(visibility, group, setGroup) {
    AjaxHelper.setGroupSettings(group.id, {
        type: visibility
    })
        .then(resp => {
            if (resp.status > 0 && resp.payload && resp.payload.group) {
                EducaHelper.fireSuccessToast(
                    "Erfolg",
                    "Die Sichtbarkeit wurde erfolgreich geändert."
                );

                setGroup(resp.payload.group);
                return;
            }
            throw new Error(resp.message);
        })
        .catch(err => {
            EducaHelper.fireErrorToast(
                "Fehler",
                "Der Sichtbarkeit konnte nicht geändert werden. " + err.messages
            );
        });
}

function updateDescription(description, group, setGroup) {
    AjaxHelper.setGroupSettings(group.id, {
        description: description
    })
        .then(resp => {
            if (resp.status > 0 && resp.payload && resp.payload.group) {
                EducaHelper.fireSuccessToast(
                    "Erfolg",
                    "Die Beschreibung wurde erfolgreich geändert"
                );

                setGroup(resp.payload.group);
                return;
            }
            throw new Error(resp.message);
        })
        .catch(err => {
            EducaHelper.fireErrorToast(
                "Fehler",
                "Die Beschreibung konnte nicht geändert werden. " + err.messages
            );
        });
}

function updateImage(image, setImage, group, setGroup) {
    AjaxHelper.setGroupImage(group.id, image)
        .then(resp => {
            if (resp.status > 0 && resp?.payload?.group) {
                EducaHelper.fireSuccessToast(
                    "Erfolg",
                    "Das Gruppenbild wurde erfolgreich geändert"
                );

                setGroup(resp.payload.group);
                setImage(undefined);
                return;
            }
            throw new Error(resp.message);
        })
        .catch(err => {
            EducaHelper.fireErrorToast(
                "Fehler",
                "Das Gruppenbild konnte nicht geändert werden. " + err.messages
            );
        });
}

function updateColor(color, group, setGroup) {
    AjaxHelper.setGroupSettings(group.id, {
        color
    })
        .then(resp => {
            if (resp.status > 0 && resp.payload && resp.payload.group) {
                EducaHelper.fireSuccessToast(
                    "Erfolg",
                    "Die Gruppenfarbe wurde erfolgreich geändert"
                );

                setGroup(resp.payload.group);
                return;
            }
            throw new Error(resp.message);
        })
        .catch(err => {
            EducaHelper.fireErrorToast(
                "Fehler",
                "Die Gruppenfarbe konnte nicht geändert werden. " + err.messages
            );
        });
}

function openArchiveModal(deleteModalRef, history, group, removeGroup) {
    const keyword = "ARCHIVIEREN";
    deleteModalRef.current?.open(
        flag => {
            if (flag) archiveGroup(history, group, removeGroup);
        },
        "Gruppe archivieren",
        "Wenn du die Gruppe archivieren möchtest gebe bitte '" +
            keyword +
            "' in das Feld ein.",
        keyword
    );
}

function openCreateGroupTemplate(educaModalRef, history, group, removeGroup) {

    let name = ""

    educaModalRef.current?.open(
        (btn) => {
            btn == MODAL_BUTTONS.OK? AjaxHelper.createTemplateFromGroup(group.id, name)
                    .then( resp => {
                        if(resp.status > 0)
                            return SharedHelper.fireSuccessToast("Erfolg", "Das Template wurde erstellt.")
                        throw new Error()
                    }).catch(() => SharedHelper.fireErrorToast("Fehler", "Das Template konnte nicht erstellt werden."))
                : null
        },
        "Template erstellen",
        <div className={"d-flex flex-column"}>
            Bitte geben Sie einen Namen an, um ein Template zu erstellen
            <FormControl onChange={(e) => name = e.target.value}/>

        </div>,
    );
}

function archiveGroup(history, group, removeGroup) {
    AjaxHelper.groupArchive(group.id)
        .then(resp => {
            if (resp.status > 0) {
                removeGroup(group);
                EducaHelper.fireSuccessToast(
                    "Erfolg",
                    "Die Gruppe wurde erfolgreich archiviert. "
                );

                return toHome(history);
            }
            throw new Error(resp.message);
        })
        .catch(err => {
            EducaHelper.fireErrorToast(
                "Fehler",
                "Die Gruppe konnte nicht archiviert werden. " + err.message
            );
        });
}

function openDeleteModal(deleteModalRef, history, group, removeGroup) {
    const keyword = "LÖSCHEN";
    deleteModalRef.current?.open(
        flag => {
            if (flag) deleteGroup(history, group, removeGroup);
        },
        "Gruppe löschen",
        "Wenn du die Gruppe löschen möchtest gebe bitte '" +
            keyword +
            "' in das Feld ein.",
        keyword
    );
}

function deleteGroup(history, group, removeGroup) {
    AjaxHelper.removeGroup(group.id)
        .then(resp => {
            if (resp.status > 0) {
                removeGroup(group);
                EducaHelper.fireSuccessToast(
                    "Erfolg",
                    "Die Gruppe wurde erfolgreich gelöscht. "
                );

                return toHome(history);
            }
            throw new Error(resp.message);
        })
        .catch(err => {
            EducaHelper.fireErrorToast(
                "Fehler",
                "Die Gruppe konnte nicht gelöscht werden. " + err.message
            );
        });
}

function toHome(history) {
    history.push({
        pathname: BASE_ROUTES.ROOT
    });
}
