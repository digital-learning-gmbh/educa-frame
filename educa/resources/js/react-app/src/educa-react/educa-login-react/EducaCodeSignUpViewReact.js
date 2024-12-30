import React from 'react';
import "./styles.css"
import {Link, Redirect} from "react-router-dom";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import EventManager from "../EducaEventManager";
import EducaHelper from "../helpers/EducaHelper";
import Form from "react-bootstrap/Form";
import {EducaPrimaryButton} from "../../shared/shared-components/Buttons";
import {GENERAL_SET_CURRENT_CLOUD_USER} from "../reducers/GeneralReducer";
import {connect} from "react-redux";
import {BASE_ROUTES} from "../App";

class EducaCodeSignUpViewReact extends React.Component {

    constructor(props) {
        super(props);

        this.state =
            {
                email: "",
                password: "",
                code: "",
                name: "",
                codeSuccessful: false,
                loginSuccessful: false,

                codeValidationError: false,
                nameValidationError: false,
                emailValidationError: false,
                passwordValidationError: false
            }
    }


    onInputChanged(type, value) {
        if (type === "code")
            this.setState({code: value})
        else if (type === "password")
            this.setState({password: value})
        else if (type === "email")
            this.setState({email: value})
        else if (type === "name")
            this.setState({name: value})
    }

    onCheckCodeButtonClicked() {
        if (!this.state.code) {
            this.setState({
                codeValidationError: !this.state.code,
            })
            return
        }
        AjaxHelper.checkCode(this.state.code)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.group) {
                    this.setState({codeSuccessful: true, group: resp.payload.group, codeValidationError: false})
                } else
                    throw new Error()
            })
            .catch(err => {
                console.log(err)
                this.setState({
                    codeValidationError: true,
                })
                EducaHelper.fireErrorToast("Fehler", "Der Code ist leider falsch. Bitte prüfe deine Eingaben", 5000, true)
            })
    }

    onCreateAccount()
    {
        if (!this.state.code || !this.state.password || !this.state.email || !this.state.name) {
            this.setState({
                codeValidationError: !this.state.code,
                emailValidationError: !this.state.email,
                passwordValidationError: !this.state.password,
                nameValidationError: !this.state.name
            })
            return
        }

        AjaxHelper.createAccountWithCode(this.state.code, this.state.email, this.state.name, this.state.password)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.token) {
                    if (!SharedHelper.setJwt(resp.payload.token))
                        throw new Error("Fehler", "Der Server konnte nicht als offizieller Educa Server identifiziert werden.")
                    this.props.updateCurrentUser(resp.payload.user)
                    SharedHelper.resetUserAliasJwt()
                    EventManager.fireLoginEvent() // if the gateway was mounted before, it will refresh
                    this.setState({loginSuccessful: true})
                } else
                    throw new Error()
            })
            .catch(err => {
                console.log(err)
                this.setState({
                    codeValidationError: true,
                    emailValidationError: true,
                    passwordValidationError: true,
                    nameValidationError: true
                })
                EducaHelper.fireErrorToast("Fehler", "Der Account konnte nicht erstellt werden. Bitte prüfe deine Eingaben", 5000, true)
            })
    }

    getForm() {
        if(this.state.codeSuccessful)
        {
            return <>
                <h1 className="h3 mb-3 font-weight-normal">Der Gruppe { this.state.group.name } beitreten</h1>
                <h1 className="h5 mb-3 font-weight-normal">Bitte gebe eine E-Mail Adresse, einen Namen und ein neues Passwort ein.</h1>


                <div style={{width: "100%", textAlign: "center"}} className="mb-2">
                    <label htmlFor="inputEmail" className="sr-only">E-Mail Address</label>
                    <Form.Control
                        onKeyPress={(evt) => {
                            if (evt.key === "Enter") this.onCreateAccount()
                        }}
                        name="email"
                        isInvalid={this.state.emailValidationError}
                        type="text"
                        id="inputEmail"
                        className="form-control  mb-1"
                        placeholder="E-Mail Addresse"
                        required="true"
                        autoFocus=""
                        onChange={(evt) => this.onInputChanged("email", evt.target.value)}/>
                    <label htmlFor="inputPassword" className="sr-only">Anzeigename</label>
                    <Form.Control
                        onKeyPress={(evt) => {
                            if (evt.key === "Enter") this.onCreateAccount()
                        }}
                        name="name"
                        isInvalid={this.state.nameValidationError}
                        type="text"
                        id="inputName"
                        className="form-control mb-1"
                        placeholder="Vor- und Nachname"
                        required="true"
                        onChange={(evt) => this.onInputChanged("name", evt.target.value)}/>
                    <label htmlFor="inputPassword" className="sr-only">Password</label>
                    <Form.Control
                        onKeyPress={(evt) => {
                            if (evt.key === "Enter") this.onCreateAccount()
                        }}
                        name="password"
                        isInvalid={this.state.passwordValidationError}
                        type="password"
                        id="inputPassword"
                        className="form-control mb-1"
                        placeholder="Passwort"
                        required=""
                        onChange={(evt) => this.onInputChanged("password", evt.target.value)}/>
                </div>


                <EducaPrimaryButton className="btn btn-lg btn-primary btn-block mt-2" onClick={() => this.onCreateAccount()}>
                    Account erstellen
                </EducaPrimaryButton>
                <p className="mt-5 mb-3 text-muted">Digital Learning GmbH © <>{moment().format("YYYY")}</> • <a href="#"
                                                                      id="actionBugReport">Support</a> • <a
                    href="#" data-toggle="modal" data-target="#impressum">Impressum</a></p>
            </>
        } else {
            return <>
                <h1 className="h3 mb-3 font-weight-normal">Zugangscode</h1>
                <h1 className="h5 mb-3 font-weight-normal">Bitte gebe deinen Zugangscode ein, um
                    dich auf educa zu registrieren.</h1>


                <div style={{width: "100%", textAlign: "center"}} className="mb-2">
                    <input
                        onChange={(evt) => this.onInputChanged("code", evt.target.value)} maxLength='6' name="digits" type="text" id="inputEmail"
                        className="digitInput" required="" autoFocus=""/>
                    { this.state.codeValidationError ?
                        <small id="emailHelp" className="form-text text-danger">Bitte überprüfe den Zugangscode</small>: <></>}
                </div>

                <Link to="/app/login" id="pwReset">Ich habe bereits einen Account</Link>

                <EducaPrimaryButton className="btn btn-lg btn-primary btn-block mt-2" onClick={() => this.onCheckCodeButtonClicked()}>
                    Code überprüfen
                </EducaPrimaryButton>
                <p className="mt-5 mb-3 text-muted">Digital Learning GmbH © {moment().format("YYYY")} • <a href="#"
                                                                      id="actionBugReport">Support</a> • <a
                    href="#" data-toggle="modal" data-target="#impressum">Impressum</a></p>
            </>
        }
    }

    render() {
        if (this.state.loginSuccessful)
            return <Redirect to={BASE_ROUTES.ROOT}/>
        return (
            <div className="loginContainer">
                <div className="container card">
                    <div className="">
                        <div className="row no-gutter">
                            <div className="col-md-6 d-none d-md-flex bg-image" style={{
                                backgroundImage: this.props.store.tenant && this.props.store.tenant.coverImage ? "url('/storage/images/tenants/" + this.props.store.tenant.coverImage + "')"  : "url('/images/nlq_background.jpg')"}}>

                            </div>
                            <div className="col-md-6 bg-light">
                                <div className="login d-flex align-items-center mx-auto">
                                    <div className="form-signin">
                                        {this.getForm()}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

const mapStateToProps = state => ({store: state})

const mapDispatchToProps = dispatch => {
    return {
        // dispatching plain actions
        updateCurrentUser: (currentUser) => dispatch({type: GENERAL_SET_CURRENT_CLOUD_USER, payload: currentUser}),
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(EducaCodeSignUpViewReact);
