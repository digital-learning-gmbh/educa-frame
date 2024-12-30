import React, { Component } from "react";
import "./styles.css";
import { Link, Redirect } from "react-router-dom";
import {
    EducaCardLinkButton,
    EducaPrimaryButton, EducaSecondaryButton
} from "../../shared/shared-components/Buttons";
import Form from "react-bootstrap/Form";
import InputGroup from "react-bootstrap/InputGroup";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import { BASE_ROUTES } from "../App";
import { GENERAL_SET_CURRENT_CLOUD_USER } from "../reducers/GeneralReducer";
import EventManager from "../EducaEventManager";
import EducaHelper from "../helpers/EducaHelper";
import xAPIProvider, { XAPI_VERBS } from "../xapi/xAPIProvider";
import { Col, Overlay, Popover, Row } from "react-bootstrap";
import { connect } from "react-redux";
import { withEducaLocalizedStrings } from "../helpers/StringLocalizationHelper";
import { EducaLanguageSelect } from "../educa-components/EducaLanguageSelect";
import Button from "react-bootstrap/Button";
import Bowser from "bowser";
import moment from "moment";

const INITAL_STATE = {
    emailText: "",
    passwordText: "",
    loginSuccessful: false,

    emailValidationError: false,
    passwordValidationError: false,
    passwordShown: false,
    showLanguagePopover: false,
    targetLanguagePopover: null,
    show2FAView: false,
    twoFaDigits: "",
    video: Math.floor(Math.random() * (4 - 1) + 1)
};
class EducaLoginViewReact extends Component {



    constructor(props) {
        super(props);

        this.state = INITAL_STATE;
    }

    onInputChanged(type, value) {
        if (type === "email") this.setState({ emailText: value });
        else if (type === "password") this.setState({ passwordText: value });
    }

    onLoginButtonClicked() {
        if (!this.state.emailText || !this.state.passwordText) {
            this.setState({
                emailValidationError: !this.state.emailText,
                passwordValidationError: !this.state.passwordText
            });
            return;
        }
        const browser = Bowser.getParser(window.navigator.userAgent);

        AjaxHelper.login(this.state.emailText, this.state.passwordText, this.state.twoFaDigits, browser.getBrowserName() + " " + browser.getBrowserVersion(),
            browser.getPlatformType(),browser.getOSName() + "" + browser.getOSVersion())
            .then(resp => {
                if (resp.payload && resp.payload["2fa_required"]) {
                    if(!resp.payload["2fa"] && this.state.show2FAView)
                    {
                        this.setState({twoFaDigits : ""})
                        EducaHelper.fireErrorToast(
                            this.props.translate("error", "Fehler"),
                            this.props.translate(
                                "login.2faerror",
                                "Der 2-Faktor-Code war nicht gültig.."
                            ),
                            5000,
                            true
                        );
                    }
                    this.setState({ show2FAView: true });
                } else if (resp.status > 0 && resp.payload && resp.payload.token) {
                    if (!SharedHelper.setJwt(resp.payload.token))
                        throw new Error(
                            this.props.translate("error", "Fehler"),
                            this.props.translate(
                                "login.error",
                                "Der Login war leider nicht erfolgreich. Bitte überprüfe deine Eingaben."
                            )
                        );
                    this.props.updateCurrentUser(resp.payload.user);
                    SharedHelper.resetUserAliasJwt();
                    EventManager.fireLoginEvent(); // if the gateway was mounted before, it will refresh
                    xAPIProvider.create(null, XAPI_VERBS.LOGIN, null);
                    this.setState({ loginSuccessful: true });
                } else throw new Error();
            })
            .catch(err => {
                this.setState({ show2FAView: false });
                EducaHelper.fireErrorToast(
                    this.props.translate("error", "Fehler"),
                    this.props.translate(
                        "login.error",
                        "Der Login war leider nicht erfolgreich. Bitte überprüfe deine Eingaben."
                    ),
                    5000,
                    true
                );
            });
    }

    togglePasswordVisiblity() {
        this.setState({ passwordShown: !this.state.passwordShown });
    }

    onLoginButtonOfficeClicked()
    {
        window.location = "/connect";
    }

    onLoginButtonKeyCloakClicked()
    {
        window.location = "/sso";
    }

    onRegisterButttonClicked()
    {
        this.props.history.push({
            pathname: BASE_ROUTES.REGISTER,
            search: null
        });
    }

    isCookieExistent() {
        return !!SharedHelper.getJwt() ? true : false
    }

    render() {
        if (this.state.loginSuccessful || this.isCookieExistent())
            return <Redirect to={BASE_ROUTES.ROOT} />;
        return (
            <Row>
                <Col md={4} lg={6} xl={8} className={"d-none d-md-block video-container"}>
                    <div
                        className="w-100 h-100 bg-image"
                        style={{
                            backgroundImage:
                                this.props.store.tenant &&
                                this.props.store.tenant.coverImage
                                    ? "url('/storage/images/tenants/" +
                                    this.props.store.tenant
                                        .coverImage +
                                    "')"
                                    : "url('/images/nlq_background.jpg')"
                        }}
                    ></div>
                    {/*<video autoPlay muted loop>*/}
                    {/*    <source src={"/videos/cover" + this.state.video + ".mp4"}*/}
                    {/*            type="video/mp4"/>*/}
                    {/*</video>*/}
                </Col>
                <Col md={8} lg={6} xl={4} style={{backgroundColor: "#fff"}}>
                    { this.state.show2FAView ?     <div className="login d-flex align-items-center mx-auto">
                            <div className="form-signin">
                                <h1 className="h3 mb-3 font-weight-normal">2-Faktor Authentifizierung</h1>
                                <h1 className="h5 mb-3 font-weight-normal">Bitte gebe deinen einmaligen Code aus der Authentifizierung App ein.</h1>
                                <div style={{width: "100%", textAlign: "center"}} className="mb-2">
                                    <input maxLength='6' name="digits" type="text" id="code" value={this.state.twoFaDigits}  onChange={evt =>
                                        this.setState({twoFaDigits: evt.target.value})
                                    }
                                           className="digitInput" required="" autoFocus=""></input>
                                </div>
                                <EducaPrimaryButton
                                    onClick={() =>
                                        this.onLoginButtonClicked()
                                    }
                                    className="btn-lg btn-block mt-2"
                                >
                                    {this.props.translate(
                                        "login.2faCheck",
                                        "Code überprüfen"
                                    )}
                                </EducaPrimaryButton>
                                <EducaSecondaryButton
                                    onClick={() =>
                                        this.setState(INITAL_STATE)
                                    }
                                    className="btn-lg btn-block mt-2"
                                >
                                    {this.props.translate(
                                        "cancel",
                                        "Abbrechen"
                                    )}
                                </EducaSecondaryButton>
                            </div></div> :
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
                                            this.props.store.tenant &&
                                            this.props.store.tenant.logo
                                                ? "/storage/images/tenants/" +
                                                this.props.store
                                                    .tenant.logo
                                                : "/images/neural.svg"
                                        }
                                        height="50"
                                        className="d-inline-block align-top"
                                        alt=""
                                    />
                                    <span>
                                                {" "}
                                        {this.props.store.tenant
                                            ? this.props.store.tenant
                                                ?.hideLogoText
                                                ? ""
                                                : this.props.store
                                                    .tenant.name
                                            : this.props.store.tenant
                                                ?.hideLogoText
                                                ? ""
                                                : "educa"}
                                            </span>
                                </div>
                                <h1 className="h5 mb-3 font-weight-normal">
                                    {this.props.translate(
                                        "login.headline",
                                        "Bitte melde dich an, um auf dein" +
                                        "                                            Konto zuzugreifen."
                                    )}
                                </h1>

                                <label
                                    htmlFor="inputEmail"
                                    className="sr-only"
                                >
                                    {this.props.translate(
                                        "login.username",
                                        "Nutzername"
                                    )}
                                </label>
                                <Form.Control
                                    onKeyPress={evt => {
                                        if (evt.key === "Enter")
                                            this.onLoginButtonClicked();
                                    }}
                                    name="email"
                                    isInvalid={
                                        this.state.emailValidationError
                                    }
                                    type="text"
                                    id="inputEmail"
                                    className="form-control  mb-1"
                                    placeholder={this.props.translate(
                                        "login.username",
                                        "Nutzername"
                                    )}
                                    required=""
                                    autoFocus=""
                                    onChange={evt =>
                                        this.onInputChanged(
                                            "email",
                                            evt.target.value
                                        )
                                    }
                                />
                                <label
                                    htmlFor="inputPassword"
                                    className="sr-only"
                                >
                                    {" "}
                                    {this.props.translate(
                                        "login.password",
                                        "Password"
                                    )}
                                </label>
                                <InputGroup className={"mb-1"}>
                                    <Form.Control
                                        onKeyPress={evt => {
                                            if (evt.key === "Enter")
                                                this.onLoginButtonClicked();
                                        }}
                                        name="password"
                                        isInvalid={
                                            this.state
                                                .passwordValidationError
                                        }
                                        type={
                                            this.state.passwordShown
                                                ? "text"
                                                : "password"
                                        }
                                        id="inputPassword"
                                        placeholder={this.props.translate(
                                            "login.password",
                                            "Password"
                                        )}
                                        required=""
                                        onChange={evt =>
                                            this.onInputChanged(
                                                "password",
                                                evt.target.value
                                            )
                                        }
                                    />
                                    <InputGroup.Text
                                        onClick={() =>
                                            this.togglePasswordVisiblity()
                                        }
                                        id="inputGroupPrepend"
                                    >
                                        <i
                                            className={
                                                this.state.passwordShown
                                                    ? "fas fa-eye-slash"
                                                    : "fas fa-eye"
                                            }
                                        ></i>
                                    </InputGroup.Text>
                                </InputGroup>

                                <Overlay
                                    onHide={() =>
                                        this.setState({
                                            showLanguagePopover: false
                                        })
                                    }
                                    show={
                                        this.state.showLanguagePopover
                                    }
                                    target={
                                        this.state.targetLanguagePopover
                                    }
                                    placement="bottom"
                                    container={
                                        this.state.targetLanguagePopover
                                    }
                                    containerPadding={20}
                                >
                                    <Popover
                                        title="Sprache wechseln"
                                        style={{
                                            display: "flex",
                                            flex: 1
                                        }}
                                    >
                                        <div
                                            className={"d-flex"}
                                            style={{ width: "300px" }}
                                        >
                                            <div
                                                style={{
                                                    width: "100%"
                                                }}
                                            >
                                                <EducaLanguageSelect
                                                    value={
                                                        this.props.store
                                                            ?.currentCloudUser
                                                            ?.language ??
                                                        "de"
                                                    }
                                                    onChange={event => {
                                                        this.props?.updateCurrentUser(
                                                            {
                                                                ...(this
                                                                        .props
                                                                        .store
                                                                        ?.currentCloudUser ??
                                                                    {}),
                                                                language:
                                                                event.code
                                                            }
                                                        );
                                                        this.setState({
                                                            showLanguagePopover: false
                                                        });
                                                    }}
                                                    placeholder={this.props.translate(
                                                        "langauge.action.switch",
                                                        "Sprache wechseln"
                                                    )}
                                                />
                                            </div>
                                        </div>
                                    </Popover>
                                </Overlay>
                                <EducaCardLinkButton
                                    underlineOnHover={true}
                                    color={"#3490dc"}
                                    onClick={e =>
                                        this.setState({
                                            showLanguagePopover: !this
                                                .state
                                                .showLanguagePopover,
                                            targetLanguagePopover:
                                            e.target
                                        })
                                    }
                                    className="float-left"
                                >
                                    {" "}
                                    {this.props.translate(
                                        "langauge.switch",
                                        "Sprache wechseln"
                                    )}
                                </EducaCardLinkButton>


                                { this.props.store?.tenant?.allowPasswordReset ?
                                    <Link
                                        to="/app/forgetPassword"
                                        className="float-right"
                                    >
                                        {" "}
                                        {this.props.translate(
                                            "login.forgetPassword",
                                            "Ich habe mein Passwort vergessen"
                                        )}
                                    </Link> : null }
                                <Link
                                    to="/app/code"
                                    className="float-right"
                                >
                                    {" "}
                                    {this.props.translate(
                                        "login.code",
                                        "Ich habe einen Code"
                                    )}
                                </Link>
                                <EducaPrimaryButton
                                    onClick={() =>
                                        this.onLoginButtonClicked()
                                    }
                                    className="btn-lg btn-block mt-2"
                                >
                                    {this.props.translate(
                                        "login",
                                        "Anmelden"
                                    )}
                                </EducaPrimaryButton>
                                { this.props.store?.tenant?.allowRegister ?
                                    <Button
                                        variant={"secondary"}
                                        onClick={() => this.onRegisterButttonClicked()}
                                        className="btn-lg btn-block mt-2"
                                    > {this.props.translate(
                                        "register.button",
                                        "Registrieren"
                                    )} </Button>
                                    : <></>   }
                                { this.props.store?.tenant?.ms_graph_tenant_id ?
                                    <Button
                                        variant={"dark"}
                                        onClick={() => this.onLoginButtonOfficeClicked()}
                                        className="btn-lg btn-block mt-2"
                                    ><i className="fab fa-microsoft"></i> Login mit Office 365</Button> : null }
                                { this.props.store?.tenant?.keycloak_server ?
                                    <Button
                                        variant={"dark"}
                                        onClick={() => this.onLoginButtonKeyCloakClicked()}
                                        className="btn-lg btn-block mt-2"
                                    ><i className="fas fa-key"></i> Login mit {this.props.store?.tenant?.keycloak_display}</Button> : null }
                                <div className="mt-5 mb-3 text-muted">
                                    Digital Learning GmbH © {moment().format("YYYY")} •{" "}
                                    <a href="#" id="actionBugReport">
                                        {" "}
                                        {this.props.translate(
                                            "support",
                                            "Support"
                                        )}
                                    </a>{" "}
                                    •{" "}
                                    <a
                                        href="#"
                                        data-toggle="modal"
                                        data-target="#impressum"
                                    >
                                        {" "}
                                        {this.props.translate(
                                            "imprint",
                                            "Impressum"
                                        )}
                                    </a>{" "}
                                    •{" "}
                                    <a
                                        target="_blank"
                                        href="https://educa-portal.de/app"
                                    >
                                        {this.props.translate(
                                            "app.download.apple",
                                            "App im AppStore herunterladen"
                                        )}
                                    </a>
                                </div>
                            </div>
                        </div> }
                </Col>
            </Row>

        );
    }
}

const mapStateToProps = state => ({ store: state });

const mapDispatchToProps = dispatch => {
    return {
        // dispatching plain actions
        updateCurrentUser: currentUser =>
            dispatch({
                type: GENERAL_SET_CURRENT_CLOUD_USER,
                payload: currentUser
            })
    };
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(withEducaLocalizedStrings(EducaLoginViewReact));
