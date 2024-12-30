import React, { Component } from "react";
import "./styles.css";
import { Redirect } from "react-router-dom";
import { EducaPrimaryButton } from "../../shared/shared-components/Buttons";
import Form from "react-bootstrap/Form";
import InputGroup from "react-bootstrap/InputGroup";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import { BASE_ROUTES } from "../App";
import { GENERAL_SET_CURRENT_CLOUD_USER } from "../reducers/GeneralReducer";
import EventManager from "../EducaEventManager";
import EducaHelper from "../helpers/EducaHelper";
import xAPIProvider, { XAPI_VERBS } from "../xapi/xAPIProvider";
import { connect } from "react-redux";
import { withEducaLocalizedStrings } from "../helpers/StringLocalizationHelper";

class EducaRegisterViewReact extends Component {
    constructor(props) {
        super(props);

        this.state = {
            emailText: "",
            passwordText: "",
            displayName: "",
            loginSuccessful: false,

            emailValidationError: false,
            passwordValidationError: false,
            displayNameValidationError: false,
            passwordShown: false,
            showLanguagePopover: false,
            targetLanguagePopover: null,
        };
    }

    onInputChanged(type, value) {
        if (type === "email") {
        } else if (type === "password") this.setState({ passwordText: value });
        else if (type === "name") this.setState({ displayName: value });
    }

    onLoginButtonClicked() {
        if (
            this.state.emailValidationError ||
            this.state.passwordValidationError ||
            this.state.displayNameValidationError
        )
            return EducaHelper.fireInfoToast(this.props.translate);
        if (
            !this.state.emailText ||
            !this.state.passwordText ||
            !this.state.displayName
        ) {
            this.setState({
                emailValidationError: !this.state.emailText,
                passwordValidationError: !this.state.passwordText,
                displayNameValidationError: !this.state.displayName,
            });
            return;
        }
        AjaxHelper.register(
            this.state.displayName,
            this.state.emailText,
            this.state.passwordText
        )
            .then((resp) => {
                if (resp.payload && resp.payload.errorCode) {
                    if (resp.payload.errorCode == 1) {
                        this.setState({
                            emailValidationError: true,
                        });
                        throw new Error(
                            this.props.translate("error", "Fehler"),
                            this.props.translate(
                                "register.error",
                                "Deine E-Mail Adresse wird bereits verwendet."
                            )
                        );
                    }
                }
                if (resp.status > 0 && resp.payload && resp.payload.token) {
                    if (!SharedHelper.setJwt(resp.payload.token))
                        throw new Error(
                            this.props.translate("error", "Fehler"),
                            this.props.translate(
                                "register.error",
                                "Die Registrierung war leider nicht erfolgreich. Bitte überprüfe deine Eingaben."
                            )
                        );
                    this.props.updateCurrentUser(resp.payload.user);
                    SharedHelper.resetUserAliasJwt();
                    EventManager.fireLoginEvent(); // if the gateway was mounted before, it will refresh
                    xAPIProvider.create(null, XAPI_VERBS.REGISTER, null);
                    this.setState({ loginSuccessful: true });
                } else throw new Error();
            })
            .catch((err) => {
                EducaHelper.fireErrorToast(
                    this.props.translate("error", "Fehler"),
                    this.props.translate(
                        "register.error",
                        "Der Registrierung war leider nicht erfolgreich. Bitte überprüfe deine Eingaben."
                    ),
                    5000,
                    true
                );
            });
    }

    togglePasswordVisiblity() {
        this.setState({ passwordShown: !this.state.passwordShown });
    }

    onLoginButtonOfficeClicked() {
        window.location = "/connect";
    }

    render() {
        if (this.state.loginSuccessful)
            return <Redirect to={BASE_ROUTES.ROOT} />;
        return (
            <div className="loginContainer">
                <div
                    className="modal fade"
                    id="impressum"
                    tabIndex="-1"
                    role="dialog"
                    aria-labelledby="exampleModalLabel"
                    aria-hidden="true"
                >
                    <div className="modal-dialog modal-lg" role="document">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5
                                    className="modal-title"
                                    id="exampleModalLabel"
                                >
                                    {this.props.translate(
                                        "imprint",
                                        "Impressum"
                                    )}
                                </h5>
                                <button
                                    type="button"
                                    className="close"
                                    data-dismiss="modal"
                                    aria-label="Close"
                                >
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div className="modal-body">
                                {this.props.store.tenant?.impressum}
                            </div>
                            <div className="modal-footer">
                                <button
                                    type="button"
                                    className="btn btn-secondary"
                                    data-dismiss="modal"
                                >
                                    Schließen
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="container card  animate__animated animate__slideInUp">
                    <div className="">
                        <div className="row no-gutter">
                            <div
                                className="col-md-6 d-none d-md-flex bg-image"
                                style={{
                                    backgroundImage:
                                        this.props.store.tenant &&
                                        this.props.store.tenant.coverImage
                                            ? "url('/storage/images/tenants/" +
                                              this.props.store.tenant
                                                  .coverImage +
                                              "')"
                                            : "url('/images/nlq_background.jpg')",
                                }}
                            ></div>
                            <div className="col-md-6">
                                <div className="login d-flex align-items-center mx-auto">
                                    <div className="form-signin">
                                        <div
                                            className="h3 mb-3 font-weight-normal"
                                            style={{
                                                display: "flex",
                                                alignItems: "center",
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
                                                "register.headline",
                                                "Neuen Account erstellen."
                                            )}
                                        </h1>

                                        <label
                                            htmlFor="inputEmail"
                                            className="sr-only"
                                        >
                                            {this.props.translate(
                                                "register.name",
                                                "Anzeigename"
                                            )}
                                        </label>
                                        <Form.Control
                                            onKeyPress={(evt) => {
                                                if (evt.key === "Enter")
                                                    this.onLoginButtonClicked();
                                            }}
                                            name="name"
                                            isInvalid={
                                                this.state
                                                    .displayNameValidationError
                                            }
                                            type="text"
                                            id="inputEmail"
                                            className="form-control  mb-1"
                                            placeholder={this.props.translate(
                                                "register.name",
                                                "Anzeigename"
                                            )}
                                            required=""
                                            autoFocus=""
                                            onChange={(event) => {
                                                this.setState({
                                                    displayNameValidationError:
                                                        event.target.value
                                                            .length === 0,
                                                });
                                                this.setState({
                                                    displayName:
                                                        event.target.value,
                                                });
                                            }}
                                        />
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
                                            onKeyPress={(evt) => {
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
                                            onChange={(event) => {
                                                this.setState({
                                                    emailValidationError:
                                                        !event.target.value.match(
                                                            "^[a-zA-Z0-9]+$"
                                                        ) ||
                                                        event.target.value
                                                            .length === 0,
                                                });
                                                this.setState({
                                                    emailText:
                                                        event.target.value,
                                                });
                                            }}
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
                                                onKeyPress={(evt) => {
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
                                                onChange={(event) => {
                                                    this.setState({
                                                        passwordValidationError:
                                                            event.target.value
                                                                .length === 0,
                                                    });
                                                    this.setState({
                                                        passwordText:
                                                            event.target.value,
                                                    });
                                                }}
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

                                        <EducaPrimaryButton
                                            onClick={() =>
                                                this.onLoginButtonClicked()
                                            }
                                            className="btn-lg btn-block mt-2"
                                        >
                                            {this.props.translate(
                                                "register.create",
                                                "Account erstellen"
                                            )}
                                        </EducaPrimaryButton>

                                        <div className="mt-5 mb-3 text-muted">
                                            Digital Learning GmbH ©{" "}
                                            {moment().format("YYYY")} •{" "}
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
                                        </div>
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

const mapStateToProps = (state) => ({ store: state });

const mapDispatchToProps = (dispatch) => {
    return {
        // dispatching plain actions
        updateCurrentUser: (currentUser) =>
            dispatch({
                type: GENERAL_SET_CURRENT_CLOUD_USER,
                payload: currentUser,
            }),
    };
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(withEducaLocalizedStrings(EducaRegisterViewReact));
