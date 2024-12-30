import React, {Component, lazy} from "react";
import EducaMessagesViewReact from "./educa-messages-react/EducaMessagesViewReact";
import { redux_store } from "../../store";
import AjaxHelper from "./helpers/EducaAjaxHelper";
import { connect } from "react-redux";
import {
    GENERAL_SET_ALL_CLOUD_USERS,
    GENERAL_SET_CURRENT_CLOUD_USER,
    GENERAL_SET_GROUPS_ALL_APPS,
    GENERAL_SET_ROOMS,
    GENERAL_SET_TENANT,
    GENERAL_SET_VIEWPORT,
    ROCKET_CHAT_SET_ME,
} from "./reducers/GeneralReducer";
import ProtectedRoute from "./ProtectedRoute";
import EducaMainNavbar from "./educa-navbar-react/EducaMainNavbar";
import { BASE_ROUTES } from "./App";
import { Redirect, Switch } from "react-router";
const EducaHomeViewReact = lazy(() =>
    import("./educa-home-react/EducaHomeViewReact")
);
const EducaFrameViewReact = lazy(() =>
    import("./educa-frame-react/EducaFrameViewReact")
);
import EventManager from "./EducaEventManager";
import EducaCalendarViewReact from "./educa-calendar-react/EducaCalendarViewReact";
import EducaGroupViewReact from "./educa-group-react/EducaGroupViewReact";
import EducaTaskViewReact from "./educa-task-react/EducaTaskViewReact";
import EducaClassbookViewReact from "./educa-classbook-react/EducaClassbookViewReact";
import { EducaLoading } from "../shared-local/Loading";
import EducaHelper from "./helpers/EducaHelper";
import EducaWebmailViewReact from "./educa-webmail-react/EducaWebmailViewReact";
import EducaSettingsReact from "./educa-settings-react/EducaSettingsReact";
import { EducaCardLinkButton } from "../shared/shared-components/Buttons";
import SupportModal from "../shared/shared-components/SupportModal";
import SharedHelper from "../shared/shared-helpers/SharedHelper";
import xAPIProvider, { XAPI_VERBS } from "./xapi/xAPIProvider";
import EducaAnalyticsReact from "./educa-analytics-react/EducaAnalyticsReact";
import EducaModal, {
    MODAL_BUTTONS,
} from "../shared/shared-components/EducaModal";
import EducaPrivacyDialog from "./educa-privacy-react/EducaPrivacyDialog";
import FliesentischZentralrat from "./FliesentischZentralrat";
import EducaLoginForbiddenDialog from "./educa-login-react/EducaLoginForbiddenDialog";
import EducaSystemSettingsRoot from "./educa-system-settings-react/EducaSystemSettingsRoot";
import EducaGlobalWiki from "./educa-global-wiki-react/EducaGlobalWiki";
import EducaDocumentsViewReact from "./educa-documents-react/EducaDocumentsViewReact";
import {withEducaLocalizedStrings} from "./helpers/StringLocalizationHelper";
import EducaContactsViewReact from "./educa-contacts-react/EducaContactsViewReact";
import EducaCreateGroupView from "./educa-group-react/EducaCreateGroupView";
import EducaxAPIView from "./educa-components/EducaxAPIView";
import {EducaLearnerViewReact} from "./educa-group-react/EducaLearnerViewReact";
import {EducaLearnerAppViewReact} from "./educa-group-react/EducaLearnerAppViewReact";
import moment from "moment";
import BottomAIBar from "./educa-components/EducaAIComponents/BottomAIBar.jsx";
import EducaProfilReact from "./educa-profil-react/EducaProfilReact.js";

export const EDUCA_PAGES = {
    CLOUD: "react-cloud-editUser",
};

class EducaAppGatewayReact extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isComponentReady: false,
        };
        redux_store.subscribe(() => {
            //console.log(redux_store.getState())
        });
        this.updateWindowDimensions = this.updateWindowDimensions.bind(this);
        this.supportModalRef = React.createRef();
        this.educaModalRef = React.createRef();
        this.xapiRef = React.createRef();
    }

    componentDidMount() {
        // Listen to a global logout event, to redirect to the loginpage
        EventManager.registerLogoutEventHandler("EducaGateway", () =>
            this.logoutEventHandler()
        );
        EventManager.registerLoginEventHandler("EducaGateway", () =>
            this.loginEventHandler()
        );
        this.initApp();
    }

    componentWillUnmount() {
        EventManager.unregisterLogoutEventHandler("EducaGateway");
        EventManager.unregisterLoginEventHandler("EducaGateway");
        window.removeEventListener("resize", this.updateWindowDimensions);
    }

    /**
     * When a login is fired
     * This is only called, whenever this component was mounted BEFORE a user logs out and logs in again
     */
    loginEventHandler() {
        this.initApp();
    }

    /**
     * When a logout is fired
     */
    logoutEventHandler() {
        //this.setState({shallRedirectToLogin: true, isComponentReady :false})
    }

    updateWindowDimensions() {
        this.props.setViewPort({
            width: window.innerWidth,
            height: window.innerHeight,
        });
    }

    /**
     * Init the app and update the redux store with all recent data
     *
     */
    initApp() {
        this.updateWindowDimensions();
        window.addEventListener("resize", this.updateWindowDimensions);

        AjaxHelper.getCurrentUser() // get the current clouduser
            .then((resp) => {
                if (resp.payload && resp.payload.user) {
                    this.props.updateCurrentUser(resp.payload.user);
                } else throw new Error("Server Error");
                xAPIProvider.create(null, XAPI_VERBS.LOGIN, null);
                return AjaxHelper.getAllCloudUsers();
            })
            .then((resp) => {
                // Get all cloud users with name and ID
                if (resp.payload) this.props.updateAllCloudUsers(resp.payload);
                else throw new Error("Server Error");
            })
            .then(() => {
                return AjaxHelper.getAllGroupSectionApps();
            })
            .then((resp) => {
                // get all possible section group app descriptors
                if (resp.payload) {
                    this.props.setGroupSectionApps(resp.payload);
                    return AjaxHelper.getRooms();
                } else throw new Error("Server Error");
            })
            .then((resp) => {
                if (resp.payload?.rooms)
                    this.props.setRooms(resp.payload.rooms);
            })
            .catch((err) => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Kritischer Server Fehler." + err.message
                );
            })
            .finally(() =>
                    //finally, get the current RocketChat user
                {

                            AjaxHelper.getAllGroupSectionApps()
                                .then((resp) => {
                                    // get all possible section group app descriptors
                                    if (resp.payload) {
                                        this.props.setGroupSectionApps(
                                            resp.payload
                                        );
                                    } else throw new Error("Server Error");
                                })
                                .catch((err) => {
                                    EducaHelper.fireErrorToast(
                                        "Fehler",
                                        "Kritischer Server Fehler." +
                                        err.message
                                    );
                                })
                                .finally(() => {
                                    this.setState({ isComponentReady: true });
                                });
                }
            );
    }

    /**
     * Listen to RocketChat status changes
     * @param data
     */
    rocketChatNotifyLogged(data) {
        if (
            data.msg === "changed" &&
            data.collection === "stream-notify-logged" &&
            data.fields &&
            Array.isArray(data.fields.args)
        ) {
            this.userLoginChangedHandler(data.fields.args);
        }
    }

    /**
     * Updates the status of the currently online members
     */
    userLoginChangedHandler(users) {
        users.forEach((userArray) => {
            let id = userArray[0];
            let status = "offline";
            if (userArray[2] == 1) status = "online";
            else if (userArray[2] == 2) status = "away";
            else if (userArray[2] == 3) status = "busy";
            if (this.props.store.rocketChat.me._id === id) {
                // update me object if the user changed his status
                this.props.setRocketChatMe({
                    ...this.props.store.rocketChat.me,
                    status: status,
                });
            }
        });
    }

    openImpressModal() {
        this.educaModalRef?.current?.open(() => {}, "Impressum", <div>
            <div style="word-wrap: break-word;"><h1>Impressum</h1>
                <h2>Angaben gemäß § 5 TMG</h2>
                <p>Digital Learning GmbH<br/>
                    Am Sportplatz 4<br/>
                    37115 Duderstadt</p>
                <p>Handelsregister: HRB 206304<br/>
                    Registergericht: Amtsgericht Göttingen</p>
                <p><strong>Vertreten durch:</strong><br/>
                    Benjamin Ledel</p>
                <h2>Kontakt</h2>
                <p>Telefon: +49 7191 39915 70<br/>
                    E-Mail: info@digitallearning.gmbh</p>
                <h2>Umsatzsteuer-ID</h2>
                <p>Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:<br/>
                    DE346773305</p>
                <h2>EU-Streitschlichtung</h2>
                <p>Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: <a
                    href="https://ec.europa.eu/consumers/odr/" target="_blank"
                    rel="noopener noreferrer">https://ec.europa.eu/consumers/odr/</a>.<br/> Unsere E-Mail-Adresse finden
                    Sie oben im Impressum.</p>
                <h2>Verbraucher­streit­beilegung/Universal­schlichtungs­stelle</h2>
                <p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer
                    Verbraucherschlichtungsstelle teilzunehmen.</p></div>
        </div>, [
            MODAL_BUTTONS.CLOSE,
        ]);
    }

    routing() {
        if (!this.props.store.currentCloudUser?.agreedPrivacy)
            return <EducaPrivacyDialog/>;

        if (!FliesentischZentralrat.globalLoginAllowed()) {
            return <EducaLoginForbiddenDialog/>;
        }

        return (
            <div>
                <EducaMainNavbar {...this.props}>
                    <div className={"educa-main-container"}>
                        <Switch>
                            <ProtectedRoute path={BASE_ROUTES.ROOT_MESSAGES}>
                                <ProtectedRoute
                                    path={"/:room_id"}
                                    render={(props) => (
                                        <EducaMessagesViewReact {...props} />
                                    )}
                                />
                            </ProtectedRoute>
                            <ProtectedRoute path={BASE_ROUTES.ROOT_LEARNER}>
                                <ProtectedRoute
                                    path={BASE_ROUTES.ROOT_LEARNER + "/:group_id/section/:section_id"}
                                render={(props) => (
                                    <EducaLearnerViewReact {...props} />
                                )}
                                exact={true}
                            />
                            <ProtectedRoute
                                path={BASE_ROUTES.ROOT_LEARNER + "/:group_id/section/:section_id/app/:app_id"}
                                render={(props) => (
                                    <EducaLearnerAppViewReact {...props} />
                                )}
                            />
                        </ProtectedRoute>
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_CALENDER}
                            render={(props) => (
                                <EducaCalendarViewReact {...props} />
                            )}
                        />
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_TASKS}
                            render={(props) => <EducaTaskViewReact {...props} />}
                        />
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_CLASSBOOK}
                            render={(props) => (
                                <EducaClassbookViewReact {...props} />
                            )}
                        />
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_EMAIL}
                            render={(props) => <EducaWebmailViewReact {...props} />}
                        />
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_ANALYTICS}
                            render={(props) => <EducaAnalyticsReact {...props} />}
                        />
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_CONTACTS}
                            render={props => <EducaContactsViewReact {...props} />}
                        />
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_DOCUMENTS}
                            render={(props) => (
                                <EducaDocumentsViewReact {...props} />
                            )}
                        />
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_GROUPS_CREATE}
                            render={(props) => <EducaCreateGroupView {...props} />}
                        />
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_GROUPS}
                            render={(props) => <EducaGroupViewReact {...props} />}
                        />
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_WIKI}
                            render={(props) => <EducaGlobalWiki {...props} />}
                        />
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT_PROFIL}
                            render={(props) => <EducaProfilReact {...props} />}
                        />
                        <ProtectedRoute path={BASE_ROUTES.ROOT_SETTINGS}>
                            <ProtectedRoute
                                path={"/:group_id"}
                                render={(props) => (
                                    <EducaSettingsReact {...props} />
                                )}
                            />
                        </ProtectedRoute>
                        <ProtectedRoute path={BASE_ROUTES.ROOT_SYSTEM_SETTINGS}>
                            <ProtectedRoute
                                render={(props) => (
                                    <EducaSystemSettingsRoot {...props} />
                                )}
                            />
                        </ProtectedRoute>
                        <ProtectedRoute path={BASE_ROUTES.ROOT_HOME}>
                            <ProtectedRoute
                                path={BASE_ROUTES.ROOT_HOME + "/create-group"}
                                render={(props) => <EducaCreateGroupView {...props} />}
                            />
                            <ProtectedRoute
                                exact={true}
                                path={BASE_ROUTES.ROOT_HOME}
                                render={(props) => <EducaHomeViewReact {...props} />}
                            />
                        </ProtectedRoute>
                            <ProtectedRoute path={BASE_ROUTES.ROOT_FRAME}>
                                <ProtectedRoute
                                    exact={true}
                                    path={BASE_ROUTES.ROOT_FRAME}
                                    render={(props) => <EducaFrameViewReact {...props} />}
                                />
                                <ProtectedRoute
                                    path={"/:frame_id"}
                                    render={(props) => (
                                        <EducaFrameViewReact {...props} />
                                    )}
                                />
                            </ProtectedRoute>
                        <ProtectedRoute
                            path={BASE_ROUTES.ROOT}
                            render={(props) => (
                                <Redirect to={BASE_ROUTES.ROOT_FRAME} />
                            )}
                        />
                    </Switch>
                </div>
                <div style={{}}>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "row",
                            justifyContent: "center",
                        }}
                        className={"footer m-3 text-center text-muted"}
                    >
                        Digital Learning GmbH © <>{moment().format("YYYY")}</>
                        <div className={"mr-1 ml-1"}>•</div>
                        <EducaCardLinkButton
                            onClick={() =>
                                this.supportModalRef?.current?.open()
                            }
                        >
                            {" "}
                            Support{" "}
                        </EducaCardLinkButton>
                        <div className={"mr-1 ml-1"}>•</div>
                        <EducaCardLinkButton
                            onClick={() => this.openImpressModal()}
                            className={"mr-1 ml-1"}
                        >
                            {" "}
                            Impressum{" "}
                        </EducaCardLinkButton>
                        <div className={"mr-1 ml-1"}>• made with</div>
                        <div
                            style={{
                                display: "flex",
                                justifyContent: "center",
                                flexDirection: "column",
                            }}
                        >
                            <i className={"fa fa-heart"} />
                        </div>
                        <div className={"mr-1 ml-1"}>in Göttingen.</div>
                    </div>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "row",
                            justifyContent: "center",
                        }}
                    >
                        <a
                            target="_blank"
                            href="https://play.google.com/store/apps/details?id=de.educaportal.educa"
                        >
                            <i className="fab fa-google-play"></i> {this.props.translate("app.download.goolge", "App im PlayStore herunterladen")}
                        </a>
                        <div className={"mr-1 ml-1"}>•</div>{" "}
                        <a
                            target="_blank"
                            href="https://apps.apple.com/de/app/educa/id1556919716"
                        >
                            <i className="fab fa-app-store"></i> {this.props.translate("app.download.apple", "App im AppStore herunterladen")}
                        </a>
                    </div>
                    <EducaModal
                        ref={this.educaModalRef}
                        size={"lg"}
                        closeButton={true}
                    />
                </div>
                <SupportModal
                    ajaxSendSupport={(image, text) =>
                        AjaxHelper.sendSupport(image, text)
                            .then((resp) => {
                                if (resp.status > 0) {
                                    SharedHelper.fireSuccessToast(
                                        "Erfolg",
                                        "Meldung wurde eingereicht."
                                    );
                                    this.supportModalRef.current.close();
                                }
                            })
                            .catch((err) => {
                                SharedHelper.fireErrorToast(
                                    "Fehler",
                                    "Meldung konnte nicht gesendet werden. " +
                                    err.message
                                );
                            })
                    }
                    ref={this.supportModalRef}
                />
                { FliesentischZentralrat.globalLearnContentDeveloperxAPI() ? <EducaxAPIView ref={this.xapiRef}/> : null }
                {/*<BottomAIBar/>*/}
            </EducaMainNavbar>
            </div>
        );
    }

    render() {
        //@TODO Navbar
        return (
            <div style={{ height: "calc(100vh - 70px)" }}>
                {this.state.isComponentReady ? (
                    this.routing()
                ) : (
                    <EducaLoading />
                )}
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
        updateAllCloudUsers: (allUsers) =>
            dispatch({ type: GENERAL_SET_ALL_CLOUD_USERS, payload: allUsers }),
        setRooms: (rooms) =>
            dispatch({ type: GENERAL_SET_ROOMS, payload: rooms }),
        setViewPort: (viewPortObj) =>
            dispatch({ type: GENERAL_SET_VIEWPORT, payload: viewPortObj }),
        setGroupSectionApps: (groupAppsArr) =>
            dispatch({
                type: GENERAL_SET_GROUPS_ALL_APPS,
                payload: groupAppsArr,
            }),
        setRocketChatMe: (me) =>
            dispatch({ type: ROCKET_CHAT_SET_ME, payload: me }),
        setTenant: (tenant) =>
            dispatch({
                type: GENERAL_SET_TENANT,
                payload: tenant,
            }),
    };
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(withEducaLocalizedStrings(EducaAppGatewayReact));
