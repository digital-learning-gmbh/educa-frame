import React, {Component, useEffect, useState} from 'react';
import {Button, Card, Container, Form, FormControl} from 'react-bootstrap';
import { useDispatch, useSelector } from 'react-redux';
import { EducaLoading } from '../../shared-local/Loading';
import AjaxHelper from '../helpers/EducaAjaxHelper';
import EducaHelper from '../helpers/EducaHelper';
import { GENERAL_SET_CURRENT_CLOUD_USER } from '../reducers/GeneralReducer';
import "./styles.css";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper";
import {EducaPrimaryButton} from "../../shared/shared-components/Buttons";
import {DisplayPair} from "../../shared/shared-components/Inputs";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import EventManager from "../EducaEventManager";
import {Link} from "react-router-dom";

export default function EducaLoginForgetPasswordDialog(props){

    let tenant = useSelector(s => s.tenant)
    const [translate] = useEducaLocalizedStrings()

    const dispatch = useDispatch()
    const setMe = (currentUser) => {
        dispatch({
            type: GENERAL_SET_CURRENT_CLOUD_USER,
            payload: currentUser
        })
    }

    const [email, setEmail] = useState("")
    const [step, setStep] = useState(0)
    const [recoverOptions, setRecoverOptions] = useState({})
    const [selectedOption, setSelectedOption] = useState(null)

    const [code, setCode] = useState(null)
    const [codeValidationError, setCodeValidationError] = useState(null)
    const [firstAnswer, setFirstAnswer] = useState(null)
    const [secondAnswer, setSecondAnswer] = useState(null)

    const [jwtToken, setJwtToken] = useState(null)
    const [newPassword, setNewPassword] = useState(null)
    const [newPassword2, setNewPassword2] = useState(null)


    let onNextClick = () => {
        if(step === 0)
        {
            AjaxHelper.checkAccountRecoverOptions(email)
                .then(resp => {
                    if (resp.status > 0 && resp.payload) {
                        setRecoverOptions(resp.payload.recoverOptions)
                        setStep(1)
                    } else
                        throw new Error()
                })
                .catch(err => {
                    console.log(err)
                    EducaHelper.fireErrorToast("Fehler", "Der Account konnte nicht wiederhergestellt werden. Bitte prüfe deine Eingaben", 5000, true)
                })
        }
        if(step == 1)
        {
            if(selectedOption == null)
            {
                EducaHelper.fireWarningToast("Achtung","Bitte wähle eine Option aus, um dein Passwort zurückzusetzen",5000,true);
            }
            setStep(2);
            if(selectedOption == "primaryEmail" || selectedOption == "secondaryEmail")
            {
                // send email
                AjaxHelper.sendRecoverMail(selectedOption, email)
                    .then(resp => {
                        if (resp.status > 0 && resp.payload) {
                          // email send
                        } else
                            throw new Error()
                    })
                    .catch(err => {
                        console.log(err)
                        EducaHelper.fireErrorToast("Fehler", "Der Account konnte nicht wiederhergestellt werden. Bitte prüfe deine Eingaben", 5000, true)
                    })
            }
        }
        if(step == 2)
        {
            AjaxHelper.executeRecover(selectedOption, email, code, firstAnswer, secondAnswer)
                .then(resp => {
                    if (resp.status > 0 && resp.payload) {
                        if(resp.payload.hasError)
                        {
                            setCodeValidationError(true)
                            EducaHelper.fireErrorToast("Fehler", "Der Account konnte nicht wiederhergestellt werden. Bitte prüfe deine Eingaben", 5000, true)
                            return;
                        }
                        setJwtToken(resp.payload.token)
                        setStep(3)
                    } else
                        throw new Error()
                })
                .catch(err => {
                    console.log(err)
                    EducaHelper.fireErrorToast("Fehler", "Der Account konnte nicht wiederhergestellt werden. Bitte prüfe deine Eingaben", 5000, true)
                })
        }

        if(step == 3)
        {
            if(newPassword !== newPassword2)
            {
                EducaHelper.fireErrorToast("Fehler", "Die Passwörter stimmen nicht überein", 5000, true)
                return;
            }

            AjaxHelper.resetPassword(email, newPassword, jwtToken)
                .then(resp => {
                    if (resp.status > 0 && resp.payload) {
                        setStep(4)
                    } else
                        throw new Error()
                })
                .catch(err => {
                    console.log(err)
                    EducaHelper.fireErrorToast("Fehler", "Der Account konnte nicht wiederhergestellt werden. Bitte prüfe deine Eingaben", 5000, true)
                })
        }
    }


    return <div className="loginContainer">
        <div className="container card">
            <div className="">
                <div className="row no-gutter">
                    <div className="col-md-6 d-none d-md-flex bg-image" style={{
                        backgroundImage: tenant && tenant.coverImage ? "url('/storage/images/tenants/" + tenant.coverImage + "')"  : "url('/images/nlq_background.jpg')"}}>

                    </div>
                    <div className="col-md-6 bg-light">
                        <div className="login d-flex align-items-center mx-auto">
                            <div className="form-signin">
                                <div
                                    className="h3 mb-3 font-weight-normal"
                                    style={{
                                        display: "flex",
                                        alignItems: "center"
                                    }}
                                >
                                    <img
                                        src={
                                            tenant &&
                                            tenant.logo
                                                ? "/storage/images/tenants/" +

                                                    tenant.logo
                                                : "/images/neural.svg"
                                        }
                                        height="50"
                                        className="d-inline-block align-top"
                                        alt=""
                                    />
                                    <span>
                                                {" "}
                                        {tenant
                                            ? tenant
                                                ?.hideLogoText
                                                ? ""
                                                :
                                                    tenant.name
                                            : tenant
                                                ?.hideLogoText
                                                ? ""
                                                : "educa"}
                                            </span>
                                </div>
                                { step == 0 ? <>
                                <h1 className="h5 mb-3 font-weight-normal">
                                    {translate(
                                        "login.forgetPassword.headline",
                                        "Du hast dein Passwort vergessen? Kein Problem."
                                    )}
                                </h1>
                                <DisplayPair title={"Dein Anmeldename oder E-Mail-Adresse"}>
                                    <FormControl
                                        type={"email"}
                                        placeholder={translate(
                                            "login.username",
                                            "Nutzername"
                                        )}
                                        value={email}
                                        onChange={(evt) => setEmail(evt.target.value)}
                                    />
                                </DisplayPair>
                                <EducaPrimaryButton
                                    onClick={() =>
                                        onNextClick()
                                    }
                                    className="btn-lg btn-block mt-2"
                                >
                                    {translate(
                                        "continue",
                                        "Weiter"
                                    )}
                                </EducaPrimaryButton>
                                    <Link to="/app/login" id="pwReset">Zurück zum Login</Link>
                                </>: null }
                                { step == 1 ? <>
                                {
                                    recoverOptions ? <>
                                        <h1 className="h5 mb-3 font-weight-normal">
                                            {translate(
                                                "login.forgetPassword.selectOption",
                                                "Bitte wähle eine Option aus, um dein Passwort zurückzusetzen."
                                            )}
                                        </h1>
                                        <>
                                        {
                                            recoverOptions.emailRecover ?  <Form.Check
                                                name={"recoverOption"}
                                                type={"radio"}
                                                id={'recoverOption-1'}
                                                onChange={(evt) => setSelectedOption("primaryEmail")}
                                                label={'Code an die primäre E-Mail Adresse senden'}
                                            /> : null
                                        }
                                            {
                                                recoverOptions.questionRecover ?  <Form.Check
                                                    name={"recoverOption"}
                                                    type={"radio"}
                                                    id={'recoverOption-2'}
                                                    label={'Sicherheitsfragen beantworten'}
                                                    onChange={(evt) => setSelectedOption("question")}
                                                /> : null
                                            }

                                            {
                                                recoverOptions.secondEmailRecover ?  <Form.Check
                                                    type={"radio"}
                                                    name={"recoverOption"}
                                                    id={'recoverOption-3'}
                                                    label={'Code an die alternative E-Mail Adresse senden'}
                                                    onChange={(evt) => setSelectedOption("secondaryEmail")}
                                                /> : null
                                            }
                                            <EducaPrimaryButton
                                                onClick={() =>
                                                    onNextClick()
                                                }
                                                className="btn-lg btn-block mt-2"
                                            >
                                                {translate(
                                                    "continue",
                                                    "Weiter"
                                                )}
                                            </EducaPrimaryButton>
                                            <Link to="/app/login" id="pwReset">Zurück zum Login</Link>
                                        </>
                                    </> : <>
                                        <h1 className="h5 mb-3 font-weight-normal">
                                            {translate(
                                                "login.forgetPassword.errorAccount",
                                                "Es tut uns Leid, wir können dein Passwort nicht zurücksetzen."
                                            )}
                                        </h1>
                                        <p>Entweder hast du keine Optionen zur Wiederherstellung angegeben oder dein Account wurde nicht gefunden. Du kannst dich jedoch an deinen Administrator wenden, um wieder Zugriff zu erlangen.</p>
                                        <Link to="/app/login" id="pwReset">Zurück zum Login</Link>
                                    </>
                                }
                                </> : null }

                                { step == 2 ? <>
                                { selectedOption == "primaryEmail" || selectedOption == "secondaryEmail" ? <>
                                    <h1 className="h5 mb-3 font-weight-normal">
                                        {translate(
                                            "login.forgetPassword.emailOptionHeadline",
                                            "Bitte geben Sie den Code ein, den wir per E-Mail Ihnen zugesandt haben."
                                        )}
                                    </h1>
                                    <div style={{width: "100%", textAlign: "center"}} className="mb-2">
                                        <input
                                            onChange={(evt) => setCode(evt.target.value)} maxLength='6' name="digits" type="text" id="inputEmail"
                                            className="digitInput" required="" autoFocus=""/>
                                        { codeValidationError ?
                                            <small id="emailHelp" className="form-text text-danger">Bitte überprüfe den Zugangscode</small>: <></>}
                                    </div>
                                    <EducaPrimaryButton
                                        onClick={() =>
                                            onNextClick()
                                        }
                                        className="btn-lg btn-block mt-2"
                                    >
                                        {translate(
                                            "continue",
                                            "Weiter"
                                        )}
                                    </EducaPrimaryButton>        <Link to="/app/login" id="pwReset">Zurück zum Login</Link>
                                    </> : null }

                                    { selectedOption == "question" ? <>
                                        <h1 className="h5 mb-3 font-weight-normal">
                                            {translate(
                                                "login.forgetPassword.questionOptionHeadline",
                                                "Bitte beantworten Sie folgende Sicherheitsfragen."
                                            )}
                                        </h1>
                                        <label>{JSON.parse(recoverOptions.firstQuestion)?.label}</label>
                                        <FormControl
                                            type={"text"}
                                            value={firstAnswer}
                                            onChange={(evt) => setFirstAnswer(evt.target.value)}
                                        />
                                        <label className={"mt-1"}>{JSON.parse(recoverOptions.secondQuestion)?.label}</label>
                                        <FormControl
                                            type={"text"}
                                            value={secondAnswer}
                                            onChange={(evt) => setSecondAnswer(evt.target.value)}
                                        />
                                        { codeValidationError ?
                                            <small id="emailHelp" className="form-text text-danger">Bitte überprüfe die Antworten</small>: <></>}
                                        <EducaPrimaryButton
                                            onClick={() =>
                                                onNextClick()
                                            }
                                            className="btn-lg btn-block mt-2"
                                        >
                                            {translate(
                                                "continue",
                                                "Weiter"
                                            )}
                                        </EducaPrimaryButton>        <Link to="/app/login" id="pwReset">Zurück zum Login</Link>
                                    </> : null }

                                    </> : null }

                                { step == 3 ? <>

                                    <h1 className="h5 mb-3 font-weight-normal">
                                        {translate(
                                            "login.forgetPassword.questionResetPassword",
                                            "Bitte vergib ein neues Passwort."
                                        )}
                                    </h1>
                                    <label>Neues Passwort</label>
                                    <FormControl
                                        type={"password"}
                                        value={newPassword}
                                        onChange={(evt) => setNewPassword(evt.target.value)}
                                    />
                                    <label>Passwort wiederholen</label>
                                    <FormControl
                                        type={"password"}
                                        value={newPassword2}
                                        onChange={(evt) => setNewPassword2(evt.target.value)}
                                    />
                                    <EducaPrimaryButton
                                        onClick={() =>
                                            onNextClick()
                                        }
                                        className="btn-lg btn-block mt-2"
                                    >
                                        {translate(
                                            "continue",
                                            "Weiter"
                                        )}
                                    </EducaPrimaryButton>
                                </> : null  }

                                { step ==4 ? <>
                                    <h1 className="h5 mb-3 font-weight-normal">
                                        {translate(
                                            "login.forgetPassword.questionResetPassword",
                                            "Dein Password wurde geändert."
                                        )}
                                    </h1>
                                    <p>Du kannst dich jetzt mit dem neuen Passwort einloggen.</p>
                                    <Link to="/app/login" id="pwReset">Zum Login</Link>
                                </> : null  }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
}
