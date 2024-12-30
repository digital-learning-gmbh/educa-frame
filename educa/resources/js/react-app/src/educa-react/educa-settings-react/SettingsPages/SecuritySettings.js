import React, { useEffect, useState } from "react";
import { useTranslation } from "react-i18next";
import EducaAjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import { getDisplayPair } from "../../../shared/shared-components/Inputs";
import Select from "react-select";
import Button from "react-bootstrap/Button";
import {Alert, Card, FormControl} from "react-bootstrap";
import {useDispatch, useSelector} from "react-redux";
import {GENERAL_SET_CURRENT_CLOUD_USER} from "../../reducers/GeneralReducer";
import EducaLabeledSwitch from "../../../shared/shared-components/EducaLabeledSwitch";





export default function SecuritySettings(props) {

    const SECURITY_QUESTIONS = [
        { value: "In welcher Stadt wurden Sie geboren?", label: "In welcher Stadt wurden Sie geboren?"},
        { value: "Wie lautet der Mädchenname Ihrer Mutter?", label: "Wie lautet der Mädchenname Ihrer Mutter?"},
        { value: "Was war Ihr Lieblingsessen als Kind?", label: "Was war Ihr Lieblingsessen als Kind?"},
        { value: "Wo haben Sie Ihren Ehepartner kennengelernt?", label: "Wo haben Sie Ihren Ehepartner kennengelernt?"},
        { value: "Was ist der Name deines ersten Haustieres?", label: "Was ist der Name deines ersten Haustieres?"},
        { value: "Was war das erste Konzert, auf dem du warst?", label: "Was war das erste Konzert, auf dem du warst?"},
        { value: "Wie lautete der Nachname Ihrer Lehrerin oder Ihres Lehrers in der dritten Klasse?", label: "Wie lautete der Nachname Ihrer Lehrerin oder Ihres Lehrers in der dritten Klasse?"},
    ];

    const dispatch = useDispatch();
    const [password, setPassword] = useState("");
    const [password2, setPassword2] = useState("");
    const me = useSelector(s => s.currentCloudUser);
    const setMe = me =>
        dispatch({ payload: me, type: GENERAL_SET_CURRENT_CLOUD_USER });
    const [securitySettings, setSecuritySettings] = useState({});
    const t = useTranslation().t;

    useEffect(() => {
        getSecuritySettings();
    }, []);
    const getSecuritySettings = () => {
        EducaAjaxHelper.getSecuritySettings()
            .then(resp => {
                if (resp.payload?.securitySettings) {
                    setSecuritySettings(resp.payload?.securitySettings);
                }
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Die Sicherheitseinstellungen konnten nicht geladen werden. " + err.message
                );
            });
    };
    const updatePassword = () => {
        if (
            password &&
            !/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(password)
        )
            return SharedHelper.fireWarningToast(
                "Achtung",
                "Das Passwort muss mindestens 8 Zeichen lang sein, einen Großbuchstaben und eine Zahl enthalten."
            );

        if (password != password2)
            return SharedHelper.fireErrorToast(
                "Achtung",
                "Die Passwörter stimmen nicht überein."
            );

        EducaAjaxHelper.updatePasssword(password)
            .then(resp => {
                if (resp.status > 0) {
                    SharedHelper.fireSuccessToast(
                        "Erfolg",
                        "Das Passwort wurde erfolgreich geändert."
                    );
                    setPassword("")
                    setPassword2("")
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Das Passwort konnte nicht geändert werden. " +
                        err.message
                );
            });
    };

    const toggle2FA = () => {
        EducaAjaxHelper.toggle2FA()
            .then(resp => {
                if (resp.status > 0 && resp.payload.cloudUser) {
                    SharedHelper.fireSuccessToast(
                        "Erfolg",
                        "Die Einstellung wurde erfolgreich geändert."
                    );
                    setMe({ ...me, has2FaKey: resp.payload.cloudUser.has2FaKey });
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Die Einstellung wurde erfolgreich geändert. " +
                    err.message
                );
            });
    }


    const updateSecuritySettings = () => {
        EducaAjaxHelper.updateSecuritySettings(securitySettings)
            .then(resp => {
                if (resp.status > 0 && resp.payload.securitySettings) {
                    SharedHelper.fireSuccessToast(
                        "Erfolg",
                        "Die Einstellung wurde erfolgreich geändert."
                    );
                    setSecuritySettings(resp.payload.securitySettings)
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Die Einstellung wurde erfolgreich geändert. " +
                    err.message
                );
            });
    }

    return (
        <div
            style={{ display: "flex", flexDirection: "column" }}
            className={"container"}
        >
            <h3>Sicherheit</h3>
            <h5>
                Passwort</h5>
            <Card>
                <Card.Body>
            <div
                style={{
                    display: "flex",
                    flexDirection: "column",
                    width: "450px"
                }}
                className={"mb-2"}
            >
                {me.loginType === "eloquent" ? (
                    <>
                        {getDisplayPair(
                            "Neues Passwort",
                            <FormControl
                                type={"password"}
                                autoComplete="new-password"
                                value={password}
                                onChange={evt =>
                                    setPassword(
                                        evt.target.value?.length > 60
                                            ? evt.target.value?.substring(
                                                0,
                                                60
                                            )
                                            : evt.target.value
                                    )
                                }
                            />
                        )}
                        {getDisplayPair(
                            "Neues Passwort wiederholen",
                            <FormControl
                                type={"password"}
                                autoComplete="new-password2"
                                value={password2}
                                onChange={evt =>
                                    setPassword2(
                                        evt.target.value?.length > 60
                                            ? evt.target.value?.substring(
                                                0,
                                                60
                                            )
                                            : evt.target.value
                                    )
                                }
                            />
                        )}
                    </>
                ) : <Alert variant={"info"}><i className="fas fa-info-circle"></i> Das Passwort kann nicht in educa geändert werden, weil der Login von einem anderen Dienst erfolgt ist.</Alert>}
            </div>
            <div>
                <Button
                    onClick={() => {
                        updatePassword();
                    }}
                >
                    Speichern
                </Button>
            </div>
                </Card.Body>
            </Card>

            <h5 style={{
                marginTop: "4rem"
            }}>
                Wiederherstellungsoptionen</h5>
            <Card>
                <Card.Body>
                    {getDisplayPair(
                        "Einmal-Code per E-Mail senden",
                        <EducaLabeledSwitch onChange={(evt) => {
                            setSecuritySettings({...securitySettings, emailRecover: !securitySettings?.emailRecover})
                        }}
                                            size={"lg"}
                                            checked={securitySettings?.emailRecover}
                                            labelLeft={""}
                                            labelRight={"Soll ein Code per E-Mail gesendet werden, wenn Sie das Passwort vergessen haben?"} />
                    )}
                    {getDisplayPair(
                        "Wiederherstellung per Sicherheitsfragen",
                        <EducaLabeledSwitch onChange={(evt) => {
                            setSecuritySettings({...securitySettings, questionRecover: !securitySettings?.questionRecover})
                        }}
                                            size={"lg"}
                                            checked={securitySettings?.questionRecover}
                                            labelLeft={""}
                                            labelRight={"Soll der Login, über die richtige Beantwortung der Sicherheitsfragen möglich sein?"} />
                    )}
                    {
                        securitySettings?.questionRecover ? <>
                            {getDisplayPair(
                                "1. Sicherheitsfrage",
                                <Select
                                    options={SECURITY_QUESTIONS}
                                    placeholder={"Bitte auswählen"}
                                    onChange={(evt) => {
                                        setSecuritySettings({...securitySettings, firstQuestion: evt})
                                    }}
                                    value={SECURITY_QUESTIONS.find((s) => s.value == securitySettings?.firstQuestion)}
                                    />
                            )}
                            {getDisplayPair(
                                "Antwort zur ersten Frage",
                                <FormControl onChange={(evt) => {
                                    setSecuritySettings({...securitySettings, firstAnswer: evt.target.value})
                                }}
                                             value={securitySettings?.firstAnswer}
                                />
                            )}
                            {getDisplayPair(
                                "2. Sicherheitsfrage",
                                <Select
                                    options={SECURITY_QUESTIONS}
                                    placeholder={"Bitte auswählen"}
                                    onChange={(evt) => {
                                        setSecuritySettings({...securitySettings, secondQuestion: evt})
                                    }}
                                    value={SECURITY_QUESTIONS.find((s) => s.value == securitySettings?.secondQuestion)}
                                />
                            )}
                            {getDisplayPair(
                                "Antwort zur zweiten Frage",
                                <FormControl onChange={(evt) => {
                                    setSecuritySettings({...securitySettings, secondAnswer: evt.target.value})
                                }}
                                             value={securitySettings?.secondAnswer}
                                />
                            )}
                        </> : <></>
                    }

                    {getDisplayPair(
                        "Wiederherstellung per alternativer E-Mail Adresse",
                        <EducaLabeledSwitch onChange={(evt) => {
                            setSecuritySettings({...securitySettings, secondEmailRecover: !securitySettings?.secondEmailRecover})
                        }}
                                            size={"lg"}
                                            checked={securitySettings?.secondEmailRecover}
                                            labelLeft={""}
                                            labelRight={"Wir senden einen Code an eine alternative E-Mail Adresse"} />
                    )}
                    {
                        securitySettings?.secondEmailRecover ? <>
                            {getDisplayPair(
                                "Alternative E-Mail Adresse",
                                <FormControl onChange={(evt) => {
                                    setSecuritySettings({...securitySettings, secondEmail: evt.target.value})
                                }}
                                             type={"email"}
                                             value={securitySettings?.secondEmail}
                                />
                            )}

                        </> : <></>
                    }
                    <button onClick={() => updateSecuritySettings()} className="btn btn-primary">Speichern</button>
                </Card.Body>
            </Card>

            <h5 style={{
                marginTop: "4rem"
            }}>
                2-Faktor-Authentifizierung</h5>
            <Card>
                <Card.Body>
                    <div
                        style={{
                            textAlign:"center",
                            display: "flex",
                            flexDirection: "column",
                            width: "450px"
                        }}
                        className={"mb-2"}
                    >
                        {me?.has2FaKey ? <>
                                <p>Durch die Aktivierung der 2-Faktor-Authentifizierung erhöhen Sie die Sicherheit, in dem Sie bei der Anmeldung zusätzlich ein einmaliges Passwort eingeben müssen.</p>
                                <img src={EducaAjaxHelper.get2FACode()} className={"img-responsive"} />
                                <button onClick={() => toggle2FA()} className="btn btn-danger">2FA deaktivieren</button></>

                            : <>
                                <p>Durch die Aktivierung der 2-Faktor-Authentifizierung erhöhen Sie die Sicherheit, in dem Sie bei der Anmeldung zusätzlich ein einmaliges Passwort eingeben müssen.</p>
                                <button onClick={() => toggle2FA()} className="btn btn-primary">2FA aktivieren</button></>
                        }
                    </div>
                </Card.Body>
            </Card>
        </div>
    );
}
