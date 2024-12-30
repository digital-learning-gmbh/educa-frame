import React, { useContext, useEffect, useState } from "react";
import { Alert, FormControl, Navbar } from "react-bootstrap";
import Button from "react-bootstrap/Button";
import Card from "react-bootstrap/Card";
import { useSelector } from "react-redux";
import { MODAL_BUTTONS } from "../../../../shared/shared-components/EducaModal";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import { EducaLoading } from "../../../../shared-local/Loading";
import { DisplayPair } from "../../../../shared/shared-components/Inputs";
import ReactSwitch from "react-switch";
import Select from "react-select";
import Sidebar from "react-sidebar";
import { ModalContext } from "../../EducaSystemSettingsRoot";
import {EducaCircularButton, EducaDefaultTable} from "../../../../shared/shared-components";
import ReactTimeAgo from "react-time-ago";
import EducaAjaxHelper from "../../../helpers/EducaAjaxHelper";
import {DRAWER_DEFAULT_STYLES} from "../../../helpers/EducaHelper";
import moment from "moment";

const defaultUser = {
    email: "",
    name: "",
    agreedPrivacy: false,
    newPassword: "",
    tenants: [],
};

function EducaCloudUserEdit({ userId, onClose, onNewUser }) {
    const [loading, setLoading] = useState(false);
    const [superAdminAllowed, setSuperAdminAllowed] = useState(false);
    const [user, setUser] = useState(defaultUser);
    const [userTenants, setUserTenants] = useState([]);
    const [errors, setErrors] = useState({});
    let [session, setSessions] = useState([]);
    const { educaModalRef } = useContext(ModalContext);

    const tenants = useSelector((s) => s.tenants);
    const roles = useSelector((s) => s.roles);

    useEffect(() => {
        if (!userId) return;
        setUser(defaultUser);
        loadUserDetails();
    }, [userId]);

    const validate = () => {
        let errs = {};

        if (!user.name) errs.name = true;
        if (!user.email || !user.email.match(
            "^[a-zA-Z0-9.@_-]+$"
        )) errs.email = true;
        if(!user.id && !user.newPassword) errs.newPassword = true;

        setErrors(errs);

        return Object.keys(errs).length === 0;
    };

    const save = () => {
        if (!validate())  {
            SharedHelper.fireErrorToast("Eingaben überprüfen","Deine Eingaben sind nicht vollständig. Bitte verwende keine Leerezeichen oder Sonderzeichen beim Login-Namen. Der Login-Namen muss mindestens ein @-Zeichen enthalten");
            return null;
        }

        const usr = { ...user, tenantIds: user.tenants.map((r) => r.id) };

        let promise = (
            user.id
                ? AjaxHelper.updateSystemSettingsUser(user.id, usr)
                : AjaxHelper.createSystemSettingsUser(usr)
        )
            .then((resp) => {
                let tmpUserTenants = [];
                if (resp.payload.cloudId) {
                    tmpUserTenants = resp.payload.cloudId?.tenants ?? [];
                    setUser(resp.payload.cloudId);
                    setUserTenants(tmpUserTenants);
                    SharedHelper.fireSuccessToast(
                        "Erfolg",
                        "Der Nutzer wurde gespeichert."
                    );
                    if (!user.id) return onNewUser(resp.payload.cloudId);
                    // onClose(resp.payload.cloudId)
                }
                return Promise.all(
                    userTenants
                        ?.filter(
                            (t) =>
                                !!tmpUserTenants?.find(
                                    (tmpT) => tmpT?.id == t.id
                                )
                        )
                        .map((ut) =>
                            AjaxHelper.updateSystemSettingsUserRoles(
                                user.id,
                                ut.id,
                                ut.hasRoles?.map((r) => r.id)
                            )
                        )
                );
            })
            .finally(() => loadUserDetails())
            .catch(() =>
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Der Nutzer und/oder die Rollen konnte nicht gespeichert werden."
                )
            );
    };

    const deleteUser = () => {
        const exec = () => {
            AjaxHelper.deleteSystemSettingsUser(user.id)
                .then((resp) => {
                    if (resp.status > 0) {
                        SharedHelper.fireSuccessToast(
                            "Erfolg",
                            "Der Nutzer wurde gelöscht."
                        );
                        return onClose(user, true);
                    }
                })
                .catch(() =>
                    SharedHelper.fireErrorToast(
                        "Fehler",
                        "Der Nutzer konnte nicht gelöscht werden."
                    )
                );
        };

        educaModalRef.current?.open(
            (btn) => (btn === MODAL_BUTTONS.YES ? exec() : null),
            "Nutzer Löschen",
            "Soll der Nutzer wirklich gelöscht werden?",
            [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
        );
    };

    const loadUserDetails = () => {
        if (userId == -1) return setUser(defaultUser);

        setLoading(true);
        AjaxHelper.loadSystemSettingsUserDetails(userId)
            .then((resp) => {
                if (resp.payload.cloudId) {
                    setUserTenants(resp.payload.cloudId?.tenants ?? []);
                    setSessions(resp.payload.sessions);
                    setSuperAdminAllowed(resp.payload.superAdmin)
                    return setUser({ ...resp.payload.cloudId });
                }

                throw new Error();
            })
            .catch((err) =>
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Details konnten nicht geladen werden."
                )
            )
            .finally(() => setLoading(false));
    };

    const closeSession = (id) => {
        EducaAjaxHelper.closeSession(id)
            .then(resp => {
                if (resp.payload?.sessions) {
                    setSessions(resp.payload?.sessions);
                }
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Die Sitzung konnte nicht beendet werden. " + err.message
                );
            });
    }

    const open = !!userId;
    const content = !open ? (
        <></>
    ) : loading ? (
        <EducaLoading />
    ) : (
        <div style={{ backgroundColor: "#f2f3f5" }}>
            <Navbar sticky="top" bg="primary" variant={"dark"}>
                <div style={{ display: "flex", flexDirection: "row", flex: 1 }}>
                    <Navbar.Brand>
                        {userId > 0
                            ? "Nutzer bearbeiten: " + user.name
                            : "Neuer Nutzer"}{" "}
                    </Navbar.Brand>
                    <div
                        style={{
                            flex: 1,
                            display: "flex",
                            flexDirection: "row",
                            justifyContent: "flex-end",
                        }}
                    >
                        <Button
                            style={{ backgroundColor: "transparent" }}
                            onClick={onClose}
                        >
                            <i className={"fa fa-times"} />
                        </Button>
                    </div>
                </div>
            </Navbar>

            <div className={"row w-100"}>
                <div className={"col-12"}>
                    <Card className={"mt-2 mb-2"}>
                        <Card.Header>
                            <Card.Title>Allgemein</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            <form autoComplete="off">
                                <div className={"d-flex flex-column"}>
                                    <div className={"d-flex"}>
                                        <input
                                            autoComplete="false"
                                            name="hidden"
                                            type="text"
                                            style={{ display: "none" }}
                                        ></input>
                                        <DisplayPair title={"E-Mail-Adresse / Login-Name"}>
                                            <FormControl
                                                isInvalid={!!errors.email}
                                                disabled={user.id}
                                                autoComplete={"off"}
                                                value={user.email}
                                                onChange={(e) => {
                                                    setUser({
                                                        ...user,
                                                        email: e.target.value,
                                                    });
                                                    setErrors({
                                                        ...errors,
                                                        email:
                                                            !e.target.value.match(
                                                                "^[a-zA-Z0-9.@_-]+$"
                                                            ) ||
                                                            e.target.value
                                                                .length === 0,
                                                    });
                                                }}
                                            />
                                        </DisplayPair>
                                        <DisplayPair title={"Passwort"}>
                                            <FormControl
                                                isInvalid={!!errors.newPassword}
                                                type={"password"}
                                                autoComplete={"new-password"}
                                                role="presentation"
                                                value={user.newPassword}
                                                onChange={(e) => {
                                                    setUser({
                                                        ...user,
                                                        newPassword:
                                                            e.target.value,
                                                    });
                                                    setErrors({
                                                        ...errors,
                                                        newPassword:
                                                            e.target.value
                                                                .length === 0,
                                                    });
                                                }}
                                            />
                                        </DisplayPair>
                                    </div>
                                    <div className={"d-flex"}>
                                        <DisplayPair title={"Name"}>
                                            <FormControl
                                                isInvalid={!!errors.name}
                                                value={user.name}
                                                onChange={(e) => {
                                                    setUser({
                                                        ...user,
                                                        name: e.target.value,
                                                    });
                                                    setErrors({
                                                        ...errors,
                                                        name:
                                                            e.target.value
                                                                .length === 0,
                                                    });
                                                }}
                                            />
                                        </DisplayPair>
                                        <DisplayPair
                                            title={"Datenschutz akzeptiert"}
                                        >
                                            <ReactSwitch
                                                checked={user.agreedPrivacy}
                                                onChange={(b) =>
                                                    setUser({
                                                        ...user,
                                                        agreedPrivacy: b,
                                                    })
                                                }
                                            />
                                        </DisplayPair>
                                    </div>
                                </div>
                            </form>
                        </Card.Body>
                    </Card>
                </div>
                <div className={"col-12"}>
                    <Card className={"mb-2"}>
                        <Card.Header>
                            <Card.Title>Rollen und Tenants</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            <h5>Tenants</h5>
                            <div className={"d-flex"}>
                                <DisplayPair title={"Tenants"}>
                                    <Select
                                        placeholder={"Bitte auswählen"}
                                        getOptionLabel={(option) => option.name}
                                        getOptionValue={(option) => option.id}
                                        closeMenuOnSelect={false}
                                        isMulti={true}
                                        value={user.tenants}
                                        options={tenants}
                                        onChange={(val) =>
                                            setUser({
                                                ...user,
                                                tenants: val ?? [],
                                            })
                                        }
                                    />
                                </DisplayPair>
                            </div>
                            <h5>Rollen</h5>
                            { superAdminAllowed ?
                            <DisplayPair
                                title={"Super-Administrator"}
                            >
                                <ReactSwitch
                                    checked={user.isSuperAdmin}
                                    onChange={(b) =>
                                        setUser({
                                            ...user,
                                            isSuperAdmin: b,
                                        })
                                    }
                                />
                            </DisplayPair> : null }
                            <Alert variant={"info"}>
                                <i className={"fas fa-info-circle"} /> Die
                                Rollen für neu hinzugefügte Tenants können Sie
                                nach dem Speichern anpassen.{" "}
                            </Alert>
                            {!userTenants?.length ? (
                                <Alert variant={"warning"}>
                                    Dieser Nutzer ist keinem Tenant zugeordnet.
                                </Alert>
                            ) : null}
                            {userTenants
                                ?.filter((ten) =>
                                    user?.tenants?.find((t) => t.id == ten.id)
                                )
                                .map((ten) => {
                                    return (
                                        <div>
                                            <b>{ten.name}</b>
                                            <DisplayPair title={"Rollen"}>
                                                <Select
                                                    styles={{
                                                        // Fixes the overlapping problem of the component
                                                        menu: provided => ({...provided, zIndex: 9999}),
                                                        input : provided => ({...provided, zIndex: 9999}),
                                                    }}
                                                    isDisabled={
                                                        ten.isEditBlocked
                                                    }
                                                    placeholder={
                                                        "Bitte auswählen"
                                                    }
                                                    getOptionLabel={(option) =>
                                                        option.name
                                                    }
                                                    getOptionValue={(option) =>
                                                        option.id
                                                    }
                                                    closeMenuOnSelect={false}
                                                    isMulti={true}
                                                    value={ten.hasRoles}
                                                    options={ten.possibleRoles}
                                                    onChange={(val) =>
                                                        setUserTenants(
                                                            userTenants.map(
                                                                (ut) =>
                                                                    ut?.id ==
                                                                        ten?.id &&
                                                                    !ut.isEditBlocked
                                                                        ? {
                                                                              ...ut,
                                                                              hasRoles:
                                                                                  val,
                                                                          }
                                                                        : ut
                                                            )
                                                        )
                                                    }
                                                />
                                            </DisplayPair>
                                        </div>
                                    );
                                })}
                        </Card.Body>
                    </Card>
                </div>
                <div className={"col-12"}>
                    <Card className={"mt-2 mb-2"}>
                        <Card.Header>
                            <Card.Title>Sitzungen</Card.Title>
                        </Card.Header>
                        <Card.Body>
                            <EducaDefaultTable
                                columns={[
                                    {Header :"Plattform", accessor : "device"},
                                    {Header :"Betriebssystem", accessor : "os"},
                                    {Header :"Browser / App-Version", accessor : "mixed_app"},
                                    {Header :"zuletzt verwendet", accessor : "last_seen_relative"},
                                    {Header :"Aktion", accessor : "action"},
                                ]}
                                data={session.map((s) =>{
                                    return {
                                        ...s,
                                        mixed_app : s.browser ? s.browser : s.app,
                                        last_seen_relative: <ReactTimeAgo date={moment(s.last_seen)}/>,
                                        action: s.token == SharedHelper.getJwt() ? <div><i className="fas fa-info-circle mr-1"></i><i>Diese Sitzung</i></div> :  <EducaCircularButton variant={"danger"} onClick={() => closeSession(s.id)} size={"small"}><i
                                            className="fas fa-trash"></i></EducaCircularButton>
                                    }
                                })}
                            />
                        </Card.Body>
                    </Card>
                </div>
            </div>
            <div className={"m-2 d-flex"}>
                <Button className={"mr-1"} onClick={() => save()}>
                    <i className={"fas fa-save"} /> Speichern
                </Button>
                {user?.id > 0 && (
                    <Button variant={"danger"} onClick={() => deleteUser()}>
                        <i className={"fas fa-trash"} /> Löschen
                    </Button>
                )}
            </div>
        </div>
    );

    return (
        <Sidebar
            sidebar={content}
            onSetOpen={onClose}
            open={open}
            defaultOpen={false}
            styles={{
                ...DRAWER_DEFAULT_STYLES,
                sidebar: { ...DRAWER_DEFAULT_STYLES.sidebar, width: "80vw" },
            }}
        >
            <></>
        </Sidebar>
    );
}

export default EducaCloudUserEdit;
