import React, { Component, useState } from "react";
import { Badge, Dropdown, Navbar } from "react-bootstrap";
import {BASE_ROUTES, THEME} from "../App";
import {connect, useSelector} from "react-redux";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import { RCHelper } from "../rocket-chat-components/RocketChatHelper";
import StatusPicker from "../rocket-chat-components/educa-chat-react/chat-components/StatusPicker";
import EducaSearchBox, { SEARCH_CATEGORIES } from "./EducaSearchBox";
import EducaHelper, {
    ALLOWED_NAVBAR_APPS_NAMES,
    APP_NAMES,
} from "../helpers/EducaHelper";
import ErrorsRamazotti, {
    ERROR_MODE,
} from "../../shared/shared-components/ErrorsRamazotti";
import EducaModal, {
    MODAL_BUTTONS,
} from "../../shared/shared-components/EducaModal";
import { EducaAppLogin } from "./EducaAppLogin";
import { EducaLanguageDropdown } from "../educa-components/EducaLanguageDropdown";
import { GENERAL_SET_CURRENT_CLOUD_USER } from "../reducers/GeneralReducer";
import FliesentischZentralrat from "../FliesentischZentralrat";
import ReactTooltip from "react-tooltip";
import ErrorHandler from "../../shared-local/ErrorHandler";
import {EducaAccessCodeModal} from "./EducaAccessCodeModal.js";
import { withEducaLocalizedStrings } from "../helpers/StringLocalizationHelper";

const styles = {
    icon: {
        width: "25px",
        height: "25px",
        cursor: "pointer",
    },
};


export function GenerateNavbarIcon({
                                       imgSrc,
                                       route,
                                       name,
                                       appKey,
                                       currentlyActiveApp,
                                       changeRoute
                                   }) {
    let [isMouseOverActive, setIsMouseOverActive] = useState(false);
    let currentCloudUser = useSelector(s => s.currentCloudUser);
    let isActive = appKey == currentlyActiveApp() || isMouseOverActive;
    if(!THEME.flatStyle) {
        return (
            <li
                key={appKey}
                className="nav-item active"
                onMouseEnter={() => setIsMouseOverActive(true)}
                onMouseLeave={() => setIsMouseOverActive(false)}
            >
                <div style={{display: "flex", flexDirection: "column"}}>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "row",
                            justifyContent: "center",
                            color: "rgb(229, 0, 70)",
                        }}
                    >
                        <a
                            onClick={() => changeRoute(route, appKey)}
                            className="nav-link"
                            style={
                                isActive
                                    ? {
                                        marginBottom: "1px",
                                        paddingBottom: "0px",
                                        paddingTop: "0px",
                                        opacity: 1,
                                    }
                                    : {opacity: 0.6}
                            }
                            title={name}
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <img style={styles.icon} src={imgSrc}/>

                            {currentCloudUser?.counts[appKey] ? <Badge
                                variant="danger">{currentCloudUser?.counts[appKey]}</Badge> : null}{' '}
                        </a>
                    </div>
                    {isActive ? (
                        <div style={{fontSize: "10px", textAlign: "center"}}>
                            {name}
                        </div>
                    ) : null}
                </div>
            </li>
        );
    } else {
        return (
            <li
                key={appKey}
                className="nav-item active"
                onMouseEnter={() => setIsMouseOverActive(true)}
                onMouseLeave={() => setIsMouseOverActive(false)}
            >
                <div style={{display: "flex", flexDirection: "column"}}>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "row",
                            justifyContent: "center",
                            color: "rgb(229, 0, 70)",
                            cursor: "pointer",
                            fontSize: "0.9rem"
                        }}
                    >
                        <a
                            onClick={() => changeRoute(route, appKey)}
                            className="nav-link"
                            style={
                                isActive
                                    ? {
                                        fontWeight: "bold",
                                    }
                                    : {
                                    }
                            }
                            title={name}
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            {name}
                            {currentCloudUser?.counts[appKey] ? <Badge className={"ml-1"}
                                                                       variant="danger">{currentCloudUser?.counts[appKey]}</Badge> : null}{' '}
                        </a>
                    </div>
                </div>
            </li>
        );
    }
}

class EducaMainNavbar extends Component {
    constructor(props) {
        super(props);
        this.state = {
            currentPath: props.location.pathname,
            currentSubscriptions: [],
            unreadMessagesAmount: 0,
            availableApps: [],
            hasError: false,
            showAccessCodeModal: false,

        };
        this.educaModalRef = React.createRef();
    }

    componentDidMount() {
        this.initNavBar();
    }

    componentWillUnmount() {
        RCHelper.getRcEventManager()?.unSubscribeToNotyUserSubscriptionsChanged(
            "navbarNotifyUser"
        );
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        //That's the case when a user logs out and logs in with a new account
        if (
            prevProps.store.currentCloudUser.id !==
            this.props.store.currentCloudUser.id &&
            this.props.store.currentCloudUser.id
        ) {
            this.initNavBar();
        }
        if (prevProps.location.pathname !== this.props.location.pathname) {
            this.setState({ currentPath: this.props.location.pathname });
        }
    }

    initNavBar() {
        if (
            this.props.store.currentCloudUser.apps &&
            Array.isArray(this.props.store.currentCloudUser.apps)
        ) {
            let shownApps = [];
            this.props.store.currentCloudUser.apps.forEach((app) => {
                let a = ALLOWED_NAVBAR_APPS_NAMES.find(
                    (obj) => obj === app.appName
                );
                if (a) shownApps.push(app);
            });
            this.setState({ availableApps: shownApps });
        }

        RCHelper.getSubscriptions()
            .then((resp) => {
                if (resp.update) {
                    this.setState({ currentSubscriptions: resp.update }, () =>
                        this.calculateUnreadMessagesAmount()
                    );
                } else throw new Error("Server Error");
            })
            .catch((err) => {
                SharedHelper.logError(
                    "EducaMainNavbar: Error. Subscription response does not contain objects"
                );
            });

        RCHelper.getRcEventManager()?.subscribeToNotifyUserSubscriptionsChanged(
            "navbarNotifyUser",
            (data) => {
                this.handleSubscriptionChange(data);
            }
        );
    }

    handleSubscriptionChange(data) {
        if (
            data.fields &&
            data.fields.eventName &&
            data.fields.eventName.includes("subscriptions-changed") &&
            data.fields.args &&
            data.fields.args.length > 0 &&
            data.fields.args[0].includes("updated")
        ) {
            let subscriptionObj = data.fields.args[1];
            if (
                this.state.currentSubscriptions &&
                this.state.currentSubscriptions.length > 0
            ) {
                for (
                    let i = 0;
                    i < this.state.currentSubscriptions.length;
                    +i++
                ) {
                    //find the subscription object that exist by the room id and set only
                    // if this room ID is not equal to it
                    if (
                        this.state.currentSubscriptions[i].rid ===
                        subscriptionObj.rid
                    ) {
                        let temp = this.state.currentSubscriptions;
                        temp[i] = subscriptionObj;
                        this.setState({ currentSubscriptions: temp }, () =>
                            this.calculateUnreadMessagesAmount()
                        );
                    }
                }
            }
        }
    }

    calculateUnreadMessagesAmount() {
        let amount = 0;
        this.state.currentSubscriptions.forEach((sub) => {
            if (sub.rid !== "GENERAL") amount += sub.unread;
        });
        this.setState({ unreadMessagesAmount: amount });
    }

    changeRoute(newRoute, activeAppKey) {
        this.setState({ currentPath: newRoute, hasError: false });
        this.props.history.push({
            pathname: newRoute,
        });
    }

    getIconForKey(key) {
        switch (key) {
            case APP_NAMES.DASHBOARD:
                return this.getDashboardIcon();
                break;
            case APP_NAMES.EXPLORE:
                return this.getExploreIcon();
                break;
            case APP_NAMES.TASKS:
                return this.getTasksIcon();
                break;
            case APP_NAMES.CALENDER:
                return this.getCalenderIcon();
                break;
            case APP_NAMES.CLASSBOOK:
                return this.getClassBookIcon();
                break;
            case APP_NAMES.LEARNMATERIALS:
                return this.getLearnInhalteIcon();
                break;
            case APP_NAMES.CLOUD:
                return this.getSystemSettingsIcon();
                break;
            case APP_NAMES.DOCUMENTS:
                return this.getDocumentsIcon();
                break;
            case APP_NAMES.CONTACTS:
                return this.getContactsIcon();
                break;
            default:
                return null;
        }
    }

    getCurrentlyActiveApp() {
        switch (this.state.currentPath) {
            case BASE_ROUTES.ROOT_HOME:
                return APP_NAMES.DASHBOARD;
            case BASE_ROUTES.ROOT_MESSAGES:
                return APP_NAMES.MESSAGES;
            case BASE_ROUTES.ROOT_CALENDER:
                return APP_NAMES.CALENDER;
            case BASE_ROUTES.ROOT_TASKS:
                return APP_NAMES.TASKS;
            case BASE_ROUTES.ROOT_CLASSBOOK:
                return APP_NAMES.CLASSBOOK;
            case BASE_ROUTES.ROOT_EMAIL:
                return APP_NAMES.EMAIL;
            case BASE_ROUTES.ROOT_LEARNMATERIALS:
                return APP_NAMES.LEARNMATERIALS;
            case BASE_ROUTES.ROOT_EXPLORE:
                return APP_NAMES.EXPLORE;
            case BASE_ROUTES.ROOT_SYSTEM_SETTINGS:
                return APP_NAMES.CLOUD;
            case BASE_ROUTES.ROOT_CONTACTS:
                return APP_NAMES.CONTACTS;
            case BASE_ROUTES.ROOT_DOCUMENTS:
                return APP_NAMES.DOCUMENTS;
        }
        if (this.state.currentPath?.includes(BASE_ROUTES.ROOT_LEARNMATERIALS))
            return APP_NAMES.LEARNMATERIALS;

        if (this.state.currentPath?.includes(BASE_ROUTES.ROOT_SYSTEM_SETTINGS))
            return APP_NAMES.CLOUD;

        return null;
    }

    generateFontAwesomeIcon(imgSrc, route, name, key) {
        let isActive = key === this.getCurrentlyActiveApp();
        return (
            <li key={key} className="nav-item active">
                <div
                    style={{
                        display: "flex",
                        flexDirection: "column",
                        color: "rgb(229, 0, 70)",
                    }}
                >
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "row",
                            justifyContent: "center",
                            cursor: "pointer",
                        }}
                    >
                        <a
                            onClick={() => this.changeRoute(route, key)}
                            className="nav-link"
                            style={
                                isActive
                                    ? {
                                        marginBottom: "1px",
                                        paddingBottom: "0px",
                                        paddingTop: "0px",
                                        opacity: 1,
                                    }
                                    : { opacity: 0.6 }
                            }
                            title={name}
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <i
                                className={imgSrc}
                                style={{
                                    fontSize: "24px",
                                    color: "rgb(229, 0, 70)",
                                }}
                            ></i>
                            {this.props.store.currentCloudUser?.counts[key] ? <Badge
                                variant="danger">{this.props.store.currentCloudUser?.counts[key]}</Badge> : null}{' '}
                        </a>
                    </div>
                    {isActive ? (
                        <div style={{ fontSize: "10px", textAlign: "center" }}>
                            {name}
                        </div>
                    ) : null}
                </div>
            </li>
        );
    }

    getDashboardIcon() {
        return (
            <GenerateNavbarIcon
                key={"home"}
                imgSrc={"/images/home.png"}
                route={BASE_ROUTES.ROOT_HOME}
                name={this.props.translate("home", "Home")}
                appKey={APP_NAMES.DASHBOARD}
                currentlyActiveApp={() => this.getCurrentlyActiveApp()}
                changeRoute={(route, key) => this.changeRoute(route, key)}
                path={this.state.currentPath}
            />
        );
    }

    getTasksIcon() {
        return (
            <GenerateNavbarIcon
                key={"aufgaben_launcher"}
                imgSrc={"/images/aufgaben_launcher.png"}
                route={BASE_ROUTES.ROOT_TASKS}
                name={this.props.translate("tasks", "Aufgaben")}
                appKey={APP_NAMES.TASKS}
                currentlyActiveApp={() => this.getCurrentlyActiveApp()}
                changeRoute={(route, key) => this.changeRoute(route, key)}
                path={this.state.currentPath}
            />
        );
    }

    getExploreIcon() {
        return (
            <GenerateNavbarIcon
                key={"explore"}
                imgSrc={"/images/explore.png"}
                route={BASE_ROUTES.ROOT_EXPLORE}
                name={this.props.translate("navbar.discover", "Erkunden")}
                appKey={APP_NAMES.EXPLORE}
                currentlyActiveApp={() => this.getCurrentlyActiveApp()}
                changeRoute={(route, key) => this.changeRoute(route, key)}
                path={this.state.currentPath}
            />
        );
    }

    getCalenderIcon() {
        return (
            <GenerateNavbarIcon
                key={"kalender_launcher"}
                imgSrc={"/images/kalender_launcher.png"}
                route={BASE_ROUTES.ROOT_CALENDER}
                name={this.props.translate("navbar.calender", "Kalender")}
                appKey={APP_NAMES.CALENDER}
                currentlyActiveApp={() => this.getCurrentlyActiveApp()}
                changeRoute={(route, key) => this.changeRoute(route, key)}
                path={this.state.currentPath}
            />
        );
    }

    getLearnInhalteIcon() {
        return (
            <GenerateNavbarIcon
                key={"edu_launcher"}
                imgSrc={"/images/edu_launcher.png"}
                route={BASE_ROUTES.ROOT_LEARNMATERIALS}
                name={this.props.translate("home_feed.learning_contents", "Lerninhalte")}
                appKey={APP_NAMES.LEARNMATERIALS}
                currentlyActiveApp={() => this.getCurrentlyActiveApp()}
                changeRoute={(route, key) => this.changeRoute(route, key)}
                path={this.state.currentPath}
            />
        );
    }

    getSystemSettingsIcon() {
        return (
            <GenerateNavbarIcon
                key={"cloud_launcher"}
                imgSrc={"/images/cloud_launcher.png"}
                route={BASE_ROUTES.ROOT_SYSTEM_SETTINGS}
                name={this.props.translate("navbar.system", "Systemsteuerung")}
                appKey={APP_NAMES.CLOUD}
                currentlyActiveApp={() => this.getCurrentlyActiveApp()}
                changeRoute={(route, key) => this.changeRoute(route, key)}
                path={this.state.currentPath}
            />
        );
    }

    getClassBookIcon() {
        return (
            <GenerateNavbarIcon
                key={"klassenbuch_launcher"}
                imgSrc={"/images/klassenbuch_launcher.png"}
                route={BASE_ROUTES.ROOT_CLASSBOOK}
                name={this.props.translate("navbar.classbook", "Klassenbuch")}
                appKey={APP_NAMES.CLASSBOOK}
                currentlyActiveApp={() => this.getCurrentlyActiveApp()}
                changeRoute={(route, key) => this.changeRoute(route, key)}
                path={this.state.currentPath}
            />
        );
    }

    getDocumentsIcon() {
        return (
            <GenerateNavbarIcon
                key={"documents"}
                imgSrc={"/images/documents.png"}
                route={BASE_ROUTES.ROOT_DOCUMENTS}
                name={this.props.translate("navbar.documents", "Dokumente")}
                appKey={APP_NAMES.DOCUMENTS}
                currentlyActiveApp={() => this.getCurrentlyActiveApp()}
                changeRoute={(route, key) => this.changeRoute(route, key)}
                path={this.state.currentPath}
            />
        );
    }

    getContactsIcon() {
        return (
            <GenerateNavbarIcon
                key={"contacts"}
                imgSrc={"/images/phone-book.png"}
                route={BASE_ROUTES.ROOT_CONTACTS}
                name={this.props.translate("navbar.addressbook", "Adressbuch")}
                appKey={APP_NAMES.CONTACTS}
                currentlyActiveApp={() => this.getCurrentlyActiveApp()}
                changeRoute={(route, key) => this.changeRoute(route, key)}
                path={this.state.currentPath}
            />
        );
    }

    getCustomBootstrapDropDownToggle() {
        return React.forwardRef(({ children, onClick }, ref) => (
            <div
                style={{ display: "flex", flexDirection: "row" }}
                ref={ref}
                onClick={(e) => {
                    e.preventDefault();
                    onClick(e);
                }}
            >
                <div
                    style={{
                        display: "flex",
                        flexDirection: "column",
                        justifyContent: "center",
                        cursor: "pointer",
                    }}
                >
                    <img
                        src={AjaxHelper.getCloudUserAvatarUrl(
                            this.props.store?.currentCloudUser?.id,
                            35,
                            this.props.store?.currentCloudUser?.image
                        )}
                        className="d-inline-block align-top rounded-circle"
                        alt=""
                        width="30"
                        height="30"
                    />
                </div>

                <div
                    style={{
                        display: "flex",
                        flexDirection: "column",
                        justifyContent: "center",
                    }}
                >
                    <div
                        className="nav-link dropdown-toggle dropright"
                        style={{ cursor: "pointer" }}
                        id="navbarDropdownMenuLink"
                        role="button"
                    >
                        {this.props.store.currentCloudUser.name}
                    </div>
                </div>
            </div>
        ));
    }

    appLogin() {
        this.educaModalRef?.current?.open(
            (btn) => {
                if (btn === MODAL_BUTTONS.OK) {
                }
            },
            "In der App anmelden",
            <EducaAppLogin modal={this.educaModalRef?.current} />,
            [MODAL_BUTTONS.CANCEL]
        );
    }

    accesCodeModal() {
        this.setState({showAccessCodeModal: true})
    }


    getUserDropDown() {
        return (
            <Dropdown alignRight={true} className="account-navbar">
                <Dropdown.Toggle
                    as={this.getCustomBootstrapDropDownToggle()}
                    id="dropdown-custom-components"
                ></Dropdown.Toggle>
                <Dropdown.Menu>
                    <Dropdown.Header as={"h6"}>
                        {this.props.store.currentCloudUser.email}
                    </Dropdown.Header>
                    <Dropdown.Item
                        onSelect={(key) => {
                            this.changeRoute(BASE_ROUTES.ROOT_PROFIL);
                        }}
                        eventKey="profil"
                    >
                        <div>
                            <i className="fas fa-id-card-alt"></i> {this.props.translate("navbar.profile", "Profil")}
                        </div>
                    </Dropdown.Item>
                    <Dropdown.Item
                        onSelect={(key) => {
                            this.changeRoute(BASE_ROUTES.ROOT_SETTINGS);
                        }}
                        eventKey="settings"
                    >
                        <div>
                            <i className="fas fa-tools"></i> {this.props.translate("navbar.settings", "Einstellungen")}
                        </div>
                    </Dropdown.Item>
                    {FliesentischZentralrat.globalWikiOpen() ? (
                        <Dropdown.Item
                            onSelect={(key) => {
                                this.changeRoute(BASE_ROUTES.ROOT_WIKI);
                            }}
                            eventKey="wiki"
                        >
                            <div>
                                <i className="far fa-life-ring"></i> {this.props.translate("navbar.help", "Hilfe")}
                            </div>
                        </Dropdown.Item>
                    ) : null}
                    <Dropdown.Divider/>
                    {SharedHelper.isUserAliasSession() ? (
                        <Dropdown.Item
                            eventKey="usr_alias_logout"
                            onSelect={(key) => {
                                AjaxHelper.userAliasLogout();
                            }}
                        >
                            <div>
                                <i className="fas fa-sign-out-alt"></i>
                                {this.props.translate("navbar.end_user_view", "Benutzeransicht beenden")}
                            </div>
                        </Dropdown.Item>
                    ) : null}
                    <Dropdown.Item
                        onSelect={(key) => {
                            this.appLogin();
                        }}
                        eventKey="login-app"
                    >
                        <div>
                            <i className="fas fa-qrcode"></i> {this.props.translate("navbar.login_app", "Login App")}
                        </div>
                    </Dropdown.Item>
                    <Dropdown.Item
                        onSelect={(key) => {
                            this.accesCodeModal();
                        }}
                        eventKey="login-app"
                    >
                        <div>
                            <i class="fas fa-unlock-alt"></i> {this.props.translate("navbar.use_code", "Zugangscode verwenden")}
                        </div>
                    </Dropdown.Item>

                    <Dropdown.Item
                        eventKey="logout"
                        onSelect={(key) => {
                            AjaxHelper.logout();
                            EducaHelper.fireSuccessToast(
                                "Logout",
                                "Erfolgreich ausgeloggt"
                            );
                            window.location.href = "/";
                        }}
                    >
                        <div>
                            <i className="fas fa-sign-out-alt"></i> {this.props.translate("navbar.logout", "Abmelden")}
                        </div>
                    </Dropdown.Item>
                </Dropdown.Menu>
            </Dropdown>
        );
    }

    componentDidCatch(error, errorInfo) {
        this.setState({ hasError: true, lastErrorInfo: errorInfo });
    }

    render() {
        return (
            <div>
                <Navbar
                    style={{ position: "sticky"}}
                    bg="light"
                    expand="lg"
                    className="fixed-top bg-back-new"
                >
                    <div className={"educa-main-container d-flex "}>
                        <Navbar.Brand className="tenant-logo-navbar" href="#" onClick={() => this.changeRoute("/app")}>
                            <img
                                src={"/storage/images/tenants/RIOS_Logo_kurz@2x.webp"}
                                height="30"
                                className="d-inline-block align-top"
                                alt=""
                            />{" "}
                        </Navbar.Brand>
                        <ul className="nav navbar-nav navbar-expand apps-icon-navbar ml-auto mr-auto">
                            {this.state.availableApps.map((app) => {
                                return this.getIconForKey(app.appName);
                            })}
                        </ul>
                        <div className="navbar-collapse collapse" id="navbar2">
                            <div
                                className="form-inline my-2 my-lg-0 ml-auto mr-auto"
                                style={{ margin: "0px" }}
                            >
                                <div className="input-group searchbar-navbar">
                                    <EducaSearchBox
                                        categories={[
                                            SEARCH_CATEGORIES.USERS,
                                            SEARCH_CATEGORIES.ANNOUNCEMENTS,
                                            SEARCH_CATEGORIES.EVENTS,
                                            SEARCH_CATEGORIES.TASKS /*, SEARCH_CATEGORIES.USERS*/,
                                            SEARCH_CATEGORIES.GROUPS,
                                            SEARCH_CATEGORIES.WIKI_PAGES,
                                            SEARCH_CATEGORIES.DOCUMENTS
                                        ]}
                                        className="form-control"
                                        placeholder="Suche ..."
                                        style={{ width: "300px" }}
                                    ></EducaSearchBox>
                                </div>
                            </div>
                        </div>
                        <ul className="navbar-nav navbar-expand ml-auto">
                            {/*<li*/}
                            {/*    className="nav-item"*/}
                            {/*>*/}
                            {/*    <div*/}
                            {/*        style={{*/}
                            {/*            display: "flex",*/}
                            {/*            flexDirection: "column",*/}
                            {/*        }}*/}
                            {/*        className="chat-navbar"*/}
                            {/*    >*/}
                            {/*        <div*/}
                            {/*            style={{*/}
                            {/*                display: "flex",*/}
                            {/*                flexDirection: "row",*/}
                            {/*                justifyContent: "center",*/}
                            {/*            }}*/}
                            {/*        >*/}
                            {/*            <a*/}
                            {/*                className="nav-link"*/}
                            {/*                aria-haspopup="true"*/}
                            {/*                style={*/}
                            {/*                    this.getCurrentlyActiveApp() ===*/}
                            {/*                    APP_NAMES.MESSAGES*/}
                            {/*                        ? {*/}
                            {/*                            marginBottom: "1px",*/}
                            {/*                            paddingBottom: "0px",*/}
                            {/*                            paddingTop: "0px",*/}
                            {/*                            opacity: 1,*/}
                            {/*                        }*/}
                            {/*                        : { opacity: 0.6 }*/}
                            {/*                }*/}
                            {/*                onClick={() => {*/}
                            {/*                    this.changeRoute(*/}
                            {/*                        BASE_ROUTES.ROOT_MESSAGES,*/}
                            {/*                        APP_NAMES.MESSAGES*/}
                            {/*                    );*/}
                            {/*                }}*/}
                            {/*                aria-expanded="false"*/}
                            {/*            >*/}
                            {/*                <div>*/}
                            {/*                    <img*/}
                            {/*                        style={styles.icon}*/}
                            {/*                        src={ THEME.flatStyle ? "/images/chat_flat.png" : "/images/chat.png" }*/}
                            {/*                    />*/}
                            {/*                    {this.state.unreadMessagesAmount ? (*/}
                            {/*                        <Badge variant="danger">*/}
                            {/*                            {*/}
                            {/*                                this.state*/}
                            {/*                                    .unreadMessagesAmount*/}
                            {/*                            }*/}
                            {/*                        </Badge>*/}
                            {/*                    ) : null}{" "}*/}
                            {/*                </div>*/}
                            {/*            </a>*/}
                            {/*        </div>*/}
                            {/*        {this.getCurrentlyActiveApp() ===*/}
                            {/*        APP_NAMES.MESSAGES ? (*/}
                            {/*            <div*/}
                            {/*                style={{*/}
                            {/*                    fontSize: "10px",*/}
                            {/*                    textAlign: "center",*/}
                            {/*                    marginRight: "5px",*/}
                            {/*                }}*/}
                            {/*            >*/}
                            {/*                Nachrichten*/}
                            {/*            </div>*/}
                            {/*        ) : null}*/}
                            {/*    </div>*/}
                            {/*</li>*/}
                            {FliesentischZentralrat.globalStoreCoinShow() && false ? (
                                <li
                                    className="nav-item dropdown"
                                >
                                    <div
                                        style={{
                                            display: "flex",
                                            flexDirection: "column",
                                        }}
                                        className="chat-navbar"
                                    >
                                        <div
                                            style={{
                                                display: "flex",
                                                flexDirection: "row",
                                                justifyContent: "center",
                                            }}
                                        >
                                            <div
                                                className="nav-link"
                                                aria-haspopup="true"
                                                aria-expanded="false"
                                                data-for={"skill-points-helper"}
                                                data-tip={"tooltip"}
                                            >
                                                <div>
                                                    <img
                                                        style={styles.icon}
                                                        src="/images/skill_points.png"
                                                    />
                                                    <Badge variant="primary">
                                                        0
                                                    </Badge>
                                                </div>
                                            </div>
                                            <ReactTooltip
                                                id={"skill-points-helper"}
                                                place={"bottom"}
                                            >
                                                Verwende und lerne mit educa, um
                                                mehr Skill-Points zu sammeln
                                            </ReactTooltip>
                                        </div>
                                    </div>
                                </li>
                            ) : (
                                <></>
                            )}
                            {this.props.store.systemInformation
                                ?.showLanguageSelectNavbar ? (
                                <li
                                    className="nav-item dropdown"
                                >
                                    <EducaLanguageDropdown
                                        value={
                                            this.props.store?.currentCloudUser
                                                ?.language ?? "de"
                                        }
                                        onChange={(event) => {
                                            this.props.updateCurrentUser({
                                                ...(this.props.store
                                                    ?.currentCloudUser ?? {}),
                                                language: event.code,
                                            });
                                        }}
                                    />
                                </li>
                            ) : (
                                <></>
                            )}
                            {this.getUserDropDown()}
                        </ul></div>
                </Navbar>
                <div className={"container-fluid"}>
                    {this.state.hasError ? (
                        <ErrorHandler
                            errormode={ERROR_MODE.SERIOUS}
                            info={this.state.lastErrorInfo}
                        />
                    ) : (
                        this.props.children
                    )}
                </div>
                <EducaAccessCodeModal show={this.state.showAccessCodeModal} close={() => this.setState({ showAccessCodeModal : false})}/>
                <EducaModal size="md" ref={this.educaModalRef} />
            </div>
        );
    }
}

const mapStateToProps = (state) => ({ store: state });

const mapDispatchToProps = (dispatch) => {
    return {
        // dispatching plain actions
        updateCurrentUser: (me) =>
            dispatch({ type: GENERAL_SET_CURRENT_CLOUD_USER, payload: me }),
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(withEducaLocalizedStrings(EducaMainNavbar));
