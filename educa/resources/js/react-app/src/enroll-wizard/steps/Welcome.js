import React, {useEffect, useRef, useState} from 'react';
import Form from "react-bootstrap/Form";
import InputGroup from "react-bootstrap/InputGroup";
import {Link} from "react-router-dom";
import "./styles.css"
import {EducaPrimaryButton} from "../../shared/shared-components/Buttons";
import { EducaLoading } from '../../shared-local/Loading';
import { TwitterPicker } from 'react-color';


function Welcome(props) {


    let [name, setName] = useState("")
    let [domain, setDomain] = useState("")
    let [email, setEmail] = useState("")
    let [password, setPassword] = useState("")
    let [password2, setPassword2] = useState("")
    let [color, setColor] = useState("#3490dc")
    let [step, setStep] = useState(1)
    let [domainValidationError, setDomainValidationError] = useState(false)
    let [emailValidationError, setEMailValidationError] = useState(false)
    let [passwordValidationError, setPasswordValidationError] = useState(false)
    let [nameValidationError, setNameValidationError] = useState(false)
    let [isLoading, setIsLoading] = useState(false)


    let deUmlaut = (value) => {
        value = value.toLowerCase();
        value = value.replace(/ä/g, 'ae');
        value = value.replace(/ö/g, 'oe');
        value = value.replace(/ü/g, 'ue');
        value = value.replace(/ß/g, 'ss');
        value = value.replace(/ /g, '-');
        value = value.replace(/\./g, '');
        value = value.replace(/,/g, '');
        value = value.replace(/\(/g, '');
        value = value.replace(/\)/g, '');
        return value;
    }

    let onButtonStepClicked = () => {
        if(step > 4)
        {
            if(domainValidationError || emailValidationError || nameValidationError || passwordValidationError)
            {
                // do nothing
                return;
            }

        }
        setStep(step+1)
    }

    useEffect(() => {
        setNameValidationError(false)
        if(name.trim() == "" || deUmlaut(name).replace(/[^0-9a-z]/gi, '').toLowerCase() == "") {
            setNameValidationError(true)
            return
        }
        setDomain(deUmlaut(name).replace(/[^0-9a-z]/gi, '').toLowerCase() + ".edunex.de")
    },[name])

    useEffect(() => {
        setDomainValidationError(false)
        if(domain == "" || !domain.includes("edunex.de"))
            setDomainValidationError(true)
    },[domain])

    useEffect(() => {
        setEMailValidationError(false)
        if(domain == "" || !String(email).toLowerCase()
        .match(
          /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        ))
            setEMailValidationError(true)
    },[email])

    useEffect(() => {
        setEMailValidationError(false)
        if(domain == "" || !String(email).toLowerCase()
        .match(
          /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        ))
            setEMailValidationError(true)
    },[email])


    useEffect(() => {
        setPasswordValidationError(false)
        if(password == "" || password != password2 || password.length < 6)
                setPasswordValidationError(true)
    },[email])

    return <div className="loginContainer">
        <div className="modal fade" id="impressum" tabIndex="-1" role="dialog"
             aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div className="modal-dialog modal-lg" role="document">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title" id="exampleModalLabel">Impressum</h5>
                        <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div className="modal-body">

                    </div>
                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" data-dismiss="modal">Schließen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div className="container-fluid card">
            <div className="">
                <div className="row no-gutter">
                    <div className="col-md-6 d-none d-md-flex bg-image"></div>
                    <div className="col-md-6">
                        <div className="enroll d-flex align-items-center mx-auto">
                            <div className="form-signin animate__animated animate__fadeInRight">
                                <div className="h3 mb-3 font-weight-normal"
                                     style={{display: "flex", alignItems: "center"}}>
                                    <img src="/images/neural.svg" width="50" height="50"
                                         className="d-inline-block align-top" alt="" loading="lazy"/>
                                    <span>educa</span></div>
                                    { isLoading ? <EducaLoading/> : <>
                                <h1 className="h5 mb-3 font-weight-normal">Fangen wir an. Bitte wähle einen Namen für deine Lernplattform.</h1>

                                <label>Wie soll dein educa heißen?</label>
                                <Form.Control
                                    onKeyPress={(evt) => {
                                        if (evt.key === "Enter") onButtonStepClicked()
                                    }}
                                    name="name_instanz"
                                    isInvalid={nameValidationError}
                                    type="text"
                                    id="inputEmail"
                                    className="form-control  mb-1"
                                    placeholder="Name der Instanz, z.b. Wäscherei Rüdiger"
                                    required=""
                                    autoFocus=""
                                    onChange={(evt) => setName(evt.target.value)}/>
                                { step > 1 ?
                                    <div class="  animate__animated animate__fadeIn">
                                        <label>Unter welcher Domäne soll dein educa laufen?</label>
                                        <Form.Control
                                            onKeyPress={(evt) => {
                                                if (evt.key === "Enter") onButtonStepClicked()
                                            }}
                                            name="name_instanz"
                                            isInvalid={domainValidationError}
                                            type="text"
                                            value={domain}
                                            id="inputEmail"
                                            className="form-control  mb-1"
                                            placeholder="Domäne"
                                            required=""
                                            autoFocus=""
                                            onChange={(evt) => setDomain(evt.target.value)}/>
                                            {
                                                domainValidationError ? <p className='text-danger'>Es sieht so aus, als wenn deine Domain nicht korrekt wäre oder die Domain nicht unter .edunex.de gehostet ist. Bitte wende dich dafür an support@digitallearning.gmbh</p> : null
                                            }
                                            <label>Welche Farbe passt für dein educa?</label>
                                            <TwitterPicker
        color={ color }
        onChangeComplete={(color) => setColor(color.hex)} />
                                    </div>
                                    : <></> }
                                    { step > 2 ?
                                    <div className=" animate__animated animate__fadeIn">
                                        <label>Deine E-Mail Adresse</label>
                                        <Form.Control
                                            onKeyPress={(evt) => {
                                                if (evt.key === "Enter") onButtonStepClicked()
                                            }}
                                            name="name_email"
                                            isInvalid={emailValidationError}
                                            type="email"
                                            value={email}
                                            id="inputEmail"
                                            className="form-control  mb-1"
                                            placeholder="E-Mail"
                                            required=""
                                            autoFocus=""
                                            onChange={(evt) => setEmail(evt.target.value)}/>
                                    </div>
                                    : <></> }
                                    { step > 3 ?
                                    <div className=" animate__animated animate__fadeIn">
                                        <label>Dein Password</label>
                                        <Form.Control
                                            onKeyPress={(evt) => {
                                                if (evt.key === "Enter") onButtonStepClicked()
                                            }}
                                            name="name_password"
                                            isInvalid={passwordValidationError}
                                            type="password"
                                            value={password}
                                            id="inputEmail"
                                            className="form-control  mb-1"
                                            placeholder="Passwort.."
                                            required=""
                                            autoFocus=""
                                            onChange={(evt) => setPassword(evt.target.value)}/>

                                        <label>Dein Password wiederholen</label>
                                        <Form.Control
                                            onKeyPress={(evt) => {
                                                if (evt.key === "Enter") onButtonStepClicked()
                                            }}
                                            name="name_password"
                                            isInvalid={passwordValidationError}
                                            type="password"
                                            value={password2}
                                            id="inputEmail"
                                            className="form-control  mb-1"
                                            placeholder="Password wiederholen"
                                            required=""
                                            autoFocus=""
                                            onChange={(evt) => setPassword2(evt.target.value)}/>
                                             {
                                                passwordValidationError ? <p className='text-danger'>Deine Passwörter stimmen nicht überein oder erfüllen nicht die Mindestvoraussetzungen (nicht leer und min. 8 Zeichen)</p> : null
                                            }
                                    </div>
                                    : <></> }
                               { !domainValidationError ? <div  className=" animate__animated animate__fadeIn"><EducaPrimaryButton style={{backgroundColor: color, borderColor: color}} onClick={() => onButtonStepClicked()}
                                    className="btn-lg btn-block mt-2"
                                >Weiter</EducaPrimaryButton>
                                <p>Ich stimme beim Erstellen der Lernplattform den AGB der Digital Learning GmbH zu und darf für Schulungszwecke kontaktiert werden.</p></div>  : <></>
                                        }
                                <div className="mt-5 mb-3 text-muted">Digital Learning GmbH © {moment().format("YYYY")} • <a href="#"
                                                                                        id="actionBugReport">Support</a> • <a
                                    href="#" data-toggle="modal" data-target="#impressum">Impressum</a> • <a
                                    target="_blank" href="https://educa-portal.de/app">App herunterladen</a>
                                </div>
                                </> }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>;
}


export default Welcome;
