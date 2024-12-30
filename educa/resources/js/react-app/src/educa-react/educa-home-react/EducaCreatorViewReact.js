import React, { Component } from "react";
import { connect } from "react-redux";
import SideMenu from "../educa-components/SideMenu";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import { BASE_ROUTES } from "../App";
import EducaFeed from "../educa-components/EducaFeed";
import { GENERAL_UPDATE_OR_ADD_GROUP } from "../reducers/GeneralReducer";
import HomeViewTaskCard from "./HomeViewTaskCard";
import HomeViewEventCard from "./HomeViewEventCard";
import EducaHelper, { APP_NAMES } from "../helpers/EducaHelper";
import xAPIProvider, { XAPI_VERBS } from "../xapi/xAPIProvider";
import { Col, Row } from "react-bootstrap";
import { EducaCardLinkButton } from "../../shared/shared-components/Buttons";
import EducaModal, {
    MODAL_BUTTONS
} from "../../shared/shared-components/EducaModal";
import SupportModal from "../../shared/shared-components/SupportModal";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import HomeBadgeCard from "./HomeBadgeCard";
import HomeInteractiveCourseCard from "./HomeInteractiveCourseCard";
import { GROUP_VIEWS } from "../educa-group-react/group-browse/GroupBrowse";
import {withEducaLocalizedStrings} from "../helpers/StringLocalizationHelper";
import { withTour } from '@reactour/tour'
import EducaAITextbox from "../educa-components/EducaAITextbox";
import HomeViewAddressbookCard from "./HomeViewAddressbookCard";
import FliesentischZentralrat from "../FliesentischZentralrat";
import HomeFeedCreator from "./HomeFeedCreator";

class EducaCreatorViewReact extends Component {
    constructor(props) {
        super(props);

        this.state = {};
        this.supportModalRef = React.createRef();
        this.educaModalRef = React.createRef();
        this.feed = React.createRef();
    }

    componentDidMount() {
        this._isMounted = true;

        xAPIProvider.create(null, XAPI_VERBS.OPEN, {
            id: APP_NAMES.DASHBOARD,
            objectType: "app",
            definition: {
                name: { "en-US": "Home" }
            }
        });
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    changeRoute(path, search) {
        this.props.history.push({
            pathname: path,
            search: search
        });
    }

    /**
     * Changes the route if a user clicked a SideMenu group element
     * @param groupId
     */
    changeRouteToGroupCallback(groupId) {
        this.props.history.push({
            pathname: BASE_ROUTES.ROOT_GROUPS + "/" + groupId,
            search: null
        });
    }

    /**
     * When the side menu Group Add was clicked
     */
    onGroupCreateClick() {
        this.changeRoute(BASE_ROUTES.ROOT_GROUPS_CREATE)
    }

    getSideMenu() {
        return (
            <div className="groupSidemenu">
                <SideMenu
                menus={[
                    EducaHelper.getGroupClusterMenuObjectsForSideMenu(
                        groupId => this.changeRouteToGroupCallback(groupId),
                        () => this.onGroupCreateClick(),
                        this.props.translate
                    ),
                    EducaHelper.getAppMenuObjectsForSideMenu(
                        this.props.store.currentCloudUser.apps,
                        path => this.changeRoute(path),
                        this.props.translate
                    )
                ]}
            ></SideMenu>
            </div>
        );
    }

    openImpressModal() {
        this.educaModalRef?.current?.open(() => {}, "Impressum", <div />, [
            MODAL_BUTTONS.CLOSE
        ]);
    }

    render() {
        return (
            <Row className={"justify-content-between mt-2"}>
                <Col
                    sm={12}
                    className={"d-sm-none d-md-block animate__animated animate__fadeIn"}
                    md={3}
                    lg={3}
                    xl={3}
                >
                    {this.getSideMenu()}
                </Col>
                <Col sm={12} md={9} xl={6}>
                    <Row className={"mt-2"}>
                        <Col
                            style={{
                                maxWidth: "900px",
                                marginLeft: "auto",
                                marginRight: "auto"
                            }}
                            className={"personalFeed"}
                        >
                        <HomeFeedCreator refreshFeed={() => this.feed?.current?.refreshFeed()}/>
                        </Col>
                    </Row>
                    <Row className={"mt-2"}>
                        <Col
                            style={{
                                maxWidth: "900px",
                                marginLeft: "auto",
                                marginRight: "auto"
                            }}
                            className={"personalFeed"}
                        >
                            <div
                                style={{
                                    display: "flex",
                                    flexDirection: "row",
                                    fontWeight: "700",
                                    color: "rgb(108, 117, 125)",
                                    fontSize: "1.25rem",
                                    lineHeight: "1.2"
                                }}
                            >
                                {this.props.translate("home.personal_feed", "Persönlicher Lernfeed")}
                            </div>
                            {this.mainFeed()}
                        </Col>
                    </Row>
                </Col>
                <Col
                    sm={12}
                    className={"d-sm-none d-md-none d-lg-block"}
                    md={3}
                    lg={3}
                    xl={3}
                >
                    <HomeInteractiveCourseCard
                        changeRoute={(path, search) =>
                            this.changeRoute(path, search)
                        }
                    />
                    { FliesentischZentralrat.globalTaskView() ?
                    <HomeViewTaskCard
                        changeRoute={(path, search) =>
                            this.changeRoute(path, search)
                        }
                    /> : null }

                    { FliesentischZentralrat.globalCalendarView() ?
                    <HomeViewEventCard
                        changeRoute={(path, search) =>
                            this.changeRoute(path, search)
                        }
                    /> : null }

                    { FliesentischZentralrat.globalCanEdu() ?
                    <HomeBadgeCard
                        changeRoute={(path, search) =>
                            this.changeRoute(path, search)
                        }
                    /> : null }

                    { FliesentischZentralrat.globalAdressbookOpen() ?
                    <HomeViewAddressbookCard
                        changeRoute={(path, search) =>
                            this.changeRoute(path, search)
                        }
                    /> : null }
                    <div className="mt-4 mb-3 text-muted text-center">
                        Digital Learning GmbH © {moment().format("YYYY")} •
                        <EducaCardLinkButton
                            onClick={() =>
                                this.supportModalRef?.current?.open()
                            }
                        >
                            {" "}
                            {this.props.translate("support", "Support")}{" "}
                        </EducaCardLinkButton>{" "}
                        •
                        <a
                            target="_blank"
                            href="https://educa-portal.de/impressum/"
                            style={{ color: "rgb(0, 123, 255)" }}
                            className={"mr-1 ml-1"}
                        >
                            {" "}
                            {this.props.translate("imprint", "Impressum")}{" "}
                        </a>
                        • <EducaCardLinkButton
                            onClick={() =>
                                this.props.setIsOpen(true)
                            }
                        >
                            {" "}
                            {this.props.translate("tutorial.start", "Tutorial starten")}{" "}
                        </EducaCardLinkButton>

                        <br></br> <br></br>
                        <a
                            target="_blank"
                            href="https://play.google.com/store/apps/details?id=de.educaportal.educa"
                        >
                            <i className="fab fa-google-play"></i> {this.props.translate("app.download.goolge", "App im PlayStore herunterladen")}
                        </a>{" "}
                        <br></br>{" "}
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
                    <SupportModal
                        ajaxSendSupport={(image, text) =>
                            AjaxHelper.sendSupport(image, text)
                                .then(resp => {
                                    if (resp.status > 0) {
                                        SharedHelper.fireSuccessToast(
                                            this.props.translate("success", "Erfolg"),
                                            this.props.translate("support.ticket.created", "Supportmeldung wurde eingereicht"),
                                        );
                                        this.supportModalRef.current.close();
                                    }
                                })
                                .catch(err => {
                                    SharedHelper.fireErrorToast(
                                        this.props.translate("error", "Fehler"),
                                        this.props.translate("support.ticket.failed", "Supportmeldung konnte nicht gesendet werden. Bitte überprüfe deine Internetverbindung") +
                                            err.message
                                    );
                                })
                        }
                        ref={this.supportModalRef}
                    />
                </Col>
            </Row>
        );
    }

    mainFeed() {
        return (
            <>
                <EducaFeed
                    ref={this.feed}
                    reloadButtonStyle={{
                        position: "fixed",
                        zIndex: 100,
                        right: "50%",
                        top: "10vh",
                        fontSize: "15px",
                        fontWeight: "bold"
                    }}
                    key={"mainFeed"}
                    feedGetterFunc={timestamp => {
                        return AjaxHelper.getMainFeed(timestamp);
                    }}
                />
            </>
        );
    }
}

const mapStateToProps = state => ({ store: state });

const mapDispatchToProps = dispatch => {
    return {
        // dispatching plain actions
        updateOrAddOneGroup: group =>
            dispatch({ type: GENERAL_UPDATE_OR_ADD_GROUP, payload: group })
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(withTour(withEducaLocalizedStrings(EducaCreatorViewReact)));
