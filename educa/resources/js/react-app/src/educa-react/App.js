import React, { Component, lazy, Suspense } from "react";
import history from "../../history";
import { BrowserRouter as Router, Route } from "react-router-dom";
import { EducaLoading } from "../shared-local/Loading";
import ProtectedRoute from "./ProtectedRoute";
import { Switch } from "react-router";
import { connect } from "react-redux";
import {
    GENERAL_SET_SYSTEM_INFORMATION,
    GENERAL_SET_TENANT,
} from "./reducers/GeneralReducer";
import AjaxHelper from "./helpers/EducaAjaxHelper";
import EducaHelper from "./helpers/EducaHelper";
import {Toaster} from "react-hot-toast";

const Gateway = lazy(() => import("./EducaAppGatewayReact"));
const Login = lazy(() => import("./educa-login-react/EducaLoginViewReact"));
const Register = lazy(() =>
    import("./educa-login-react/EducaRegisterViewReact")
);

const CodeSignUp = lazy(() =>
    import("./educa-login-react/EducaCodeSignUpViewReact")
);

const ForgetPassword = lazy(() =>
    import("./educa-login-react/EducaLoginForgetPasswordDialog")
);

export const THEME = {
    showIcon: false,
    flatStyle: true,
}
export const BASE_ROUTES = {
    ROOT: "/app",
    ROOT_HOME: "/app/home",
    ROOT_MESSAGES: "/app/messages",
    ROOT_CALENDER: "/app/calendar",
    ROOT_GROUPS: "/app/groups",
    ROOT_GROUPS_CREATE: "/app/groups/create",
    ROOT_TASKS: "/app/tasks",
    ROOT_CLASSBOOK: "/app/classbook",
    ROOT_EMAIL: "/app/email",
    ROOT_ANALYTICS: "/app/analytics",
    ROOT_LEARNMATERIALS: "/app/learn",
    ROOT_SETTINGS: "/app/settings",
    ROOT_PROFIL: "/app/profil",
    ROOT_SYSTEM_SETTINGS: "/app/systemsettings",
    ROOT_EXPLORE: "/app/explore",
    ROOT_CREATOR_ASSISTANT: "/app/creatorAssistant",
    ROOT_LEARNER: "/app/learner",
    ROOT_WIKI: "/app/wiki",
    ROOT_DOCUMENTS: "/app/documents",
    ROOT_CONTACTS: "/app/contacts",

    LOGIN: "/app/login",
    EMBEDDED: "/app/embedded",
    CODE: "/app/code",
    FORGET_PASSWORD: "/app/forgetPassword",
    REGISTER: "/app/register",
};

class App extends Component {
    componentDidMount() {
        AjaxHelper.getTenantConfig() // get the config of the tenant
            .then((resp) => {
                if (resp.payload && resp.payload.tenant) {
                    this.props.setTenant(resp.payload.tenant);
                    this.props.setSystemInformation(
                        resp.payload.systemInformation
                    );
                } else throw new Error("Server Error while loading tenants");
            })
            .catch((err) => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Kritischer Server Fehler." + err.message
                );
            });
    }

    render() {
        return (
            <div>
                {this.props.store?.systemInformation?.banner?.show ? (
                    <div
                        className="headerInformation text-light"
                        style={{
                            backgroundColor:
                                this.props.store?.systemInformation?.banner
                                    ?.color,
                        }}
                    >
                        <i className="fas fa-info-circle"></i>{" "}
                        {this.props.store?.systemInformation?.banner?.text}
                    </div>
                ) : (
                    <></>
                )}
                <Toaster
                    position="top-center"
                    reverseOrder={false}
                />
                <Router history={history}>
                    <Suspense fallback={<EducaLoading />}>
                        <Switch>
                            <Route
                                path={BASE_ROUTES.LOGIN}
                                render={(props) => <Login {...props} />}
                            />
                            <Route
                                path={BASE_ROUTES.CODE}
                                render={(props) => <CodeSignUp />}
                            />
                            <Route
                                path={BASE_ROUTES.FORGET_PASSWORD}
                                render={(props) => <ForgetPassword {...props} />}
                            />
                            <Route
                                path={BASE_ROUTES.REGISTER}
                                render={(props) => <Register {...props} />}
                            />
                            <ProtectedRoute
                                path={BASE_ROUTES.ROOT}
                                render={(props) => <Gateway {...props} />}
                            />
                        </Switch>
                    </Suspense>
                </Router>
            </div>
        );
    }
}

const mapStateToProps = (state) => ({ store: state });

const mapDispatchToProps = (dispatch) => {
    return {
        setTenant: (tenant) =>
            dispatch({
                type: GENERAL_SET_TENANT,
                payload: tenant,
            }),
        setSystemInformation: (systemInformation) =>
            dispatch({
                type: GENERAL_SET_SYSTEM_INFORMATION,
                payload: systemInformation,
            }),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(App);
