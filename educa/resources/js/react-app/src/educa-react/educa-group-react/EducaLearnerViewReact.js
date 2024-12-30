import React, {useEffect, useState} from "react";
import {Breadcrumb, Button} from "react-bootstrap";
import MainHeading from "../educa-home-react/educa-learner-components/MainHeading";
import MutedParagraph from "../educa-home-react/educa-learner-components/MutedParagraph";
import {useHistory} from "react-router";
import "./learnerViewer.css"
import {
    useParams
} from "react-router-dom";
import {useSelector} from "react-redux";
import {FullScreen, useFullScreenHandle} from "react-full-screen";
import {EducaShimmer} from "../../shared/shared-components/EducaShimmer";
import FliesentischZentralrat from "../FliesentischZentralrat";
import SubHeading from "../educa-home-react/educa-learner-components/SubHeading";
import SectionDocumentsPreview from "./group-section-apps-preview/SectionDocumentsPreview";
import HomeFeedLearner from "../educa-home-react/educa-learner-components/HomeFeedLearner";
import SectionNoPreview from "./group-section-apps-preview/SectionNoPreview";
import {BASE_ROUTES} from "../App";
import SectionAccessCodePreview from "./group-section-apps-preview/SectionAccessCodePreview";
import SectionCalendarPreview from "./group-section-apps-preview/SectionCalendarPreview";
import SectionAnnouncmentsPreview from "./group-section-apps-preview/SectionAnnouncementsPreview";
import SectionTasksPreview from "./group-section-apps-preview/SectionTasksPreview";

export function EducaLearnerViewReact(props) {

    let history = useHistory()
    let {group_id, section_id} = useParams();
    let currentCloudUser = useSelector(s => s.currentCloudUser);
    let [section, setSection] = useState(null)
    let [group, setGroup] = useState(null)
    const fullScreen = useFullScreenHandle();

    useEffect(() => {
        let foundGroup = currentCloudUser?.groups?.find((g) => g.id == group_id);
        setGroup(foundGroup)
        let foundSection = foundGroup?.sections?.find((s) => s.id == section_id)
        setSection(foundSection);
    }, [currentCloudUser, section_id, group_id])


    let openApp = (app) => {
        history.push(BASE_ROUTES.ROOT_LEARNER + "/" + group?.id +"/section/" + section?.id +"/app/" + app?.id + "/" + app?.group_app?.type )
    }

    return <div>
        <div className={"mb-2 mt-4 d-flex justify-content-between"}>
            <div className={"d-flex"}>
                <div className='d-flex'>
                    <Button variant="outline-secondary" className="m-1" onClick={() => history.push("/app")}>
                        <i className="fas fa-arrow-left"></i> Zur Startseite
                    </Button>
                </div>
                <div>
                    <Breadcrumb className="noPadding m-1">
                        <Breadcrumb.Item onClick={() => history.push("/app/home")}>Startseite</Breadcrumb.Item>
                        {group ? <Breadcrumb.Item
                            onClick={() => history.push("/app/groups/" + group?.id + "/feed")}>{group?.name}</Breadcrumb.Item> : null}
                        {section ? <Breadcrumb.Item active>{section?.name}</Breadcrumb.Item> : null}
                    </Breadcrumb>
                </div>
                <div>
                    { FliesentischZentralrat.groupEditGroup(group) ?  <Button onClick={() => history.push("/app/groups/" + group?.id + "/settings")} variant="outline-secondary" className="m-1">
                        <i className="fas fa-wrench"></i> Gruppe bearbeiten
                    </Button> : null }

                </div>
            </div>
            <div className={"d-flex"}>
                <Button variant="outline-secondary" className="m-1"
                        onClick={fullScreen.enter}>
                    <i className="fas fa-expand mr-1"></i> Vollbildmodus
                </Button>
            </div>
        </div>

        <FullScreen handle={fullScreen}>
            <div className={"mainContent"}>
                <div className={"row"}>
                    <div className={"col-8 mt-4"}>
                        <MainHeading>{section?.name}</MainHeading>
                        <MutedParagraph>{section?.description}</MutedParagraph>
                        {section
                            ? section.section_group_apps.map(app => {
                                if (
                                    !app ||
                                    !app.group_app ||
                                    (app.group_app.type === "announcement" &&
                                        !FliesentischZentralrat.sectionAnnouncementView(
                                            section
                                        )) ||
                                    (app.group_app.type === "accessCode" &&
                                        !FliesentischZentralrat.sectionAccesscodeView(
                                            section
                                        )) ||
                                    (app.group_app.type === "chat" &&
                                        !FliesentischZentralrat.sectionMessagesView(
                                            section
                                        )) ||
                                    (app.group_app.type === "calendar" &&
                                        !FliesentischZentralrat.sectionCalendarView(
                                            section
                                        )) ||
                                    (app.group_app.type === "files" &&
                                        !FliesentischZentralrat.sectionFilesView(
                                            section
                                        )) ||
                                    (app.group_app.type === "task" &&
                                        !FliesentischZentralrat.sectionTaskView(
                                            section
                                        )) ||
                                    (app.group_app.type === "wikiPage" &&
                                        !FliesentischZentralrat.sectionWikiOpen(
                                            section
                                        ))
                                )
                                    return;

                                const appIconClass = app?.group_app?.icon
                                    ? "fa " + app.group_app.icon
                                    : null;

                                let content = null;

                                if( app.group_app.type === "announcement")
                                    content = <SectionAnnouncmentsPreview section={section} app={app} openApp={openApp}/>;
                                if( app.group_app.type === "task")
                                    content = <SectionTasksPreview section={section} app={app} openApp={openApp}/>;
                                if( app.group_app.type === "calendar")
                                    content = <SectionCalendarPreview section={section} app={app} openApp={openApp}/>;
                                if( app.group_app.type === "files")
                                    content = <SectionDocumentsPreview section={section} app={app} openApp={openApp}/>;
                                if( app.group_app.type === "accessCode")
                                    content =  <SectionAccessCodePreview group={group} section={section} app={app} openApp={openApp}/>;

                                return <>
                                    {content ?   <div className={"m-2"}>
                                        <div className={"d-flex justify-content-between"}>
                                            <SubHeading>
                                                <i
                                                    className={appIconClass + " mr-1"}
                                                ></i>{app?.name}</SubHeading>
                                            <Button variant={"link"} onClick={() => openApp(app)}>
                                                <i className="fas fa-expand mr-1"></i> App öffnen</Button>
                                        </div>{content}</div> : <> <SubHeading>
                                        <i
                                            className={appIconClass + " mr-1"}
                                        ></i>{app?.name}</SubHeading>
                                        <SectionNoPreview openApp={openApp} app={app}/></>}
                                </>
                            }) : <div><EducaShimmer/>
                                <EducaShimmer/>
                                <EducaShimmer/></div>
                        }
                    </div>
                    <div className={"col-4 mt-4"}>
                        <HomeFeedLearner/>
                    </div>
                </div>
            </div>

        </FullScreen>
    </div>
}
