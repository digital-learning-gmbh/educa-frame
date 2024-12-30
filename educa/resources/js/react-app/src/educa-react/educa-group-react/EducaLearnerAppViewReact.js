import React, {useEffect, useState} from "react";
import {useHistory, useLocation} from "react-router";
import {useParams} from "react-router-dom";
import {useSelector} from "react-redux";
import {FullScreen, useFullScreenHandle} from "react-full-screen";
import {Alert, Breadcrumb, Button} from "react-bootstrap";
import FliesentischZentralrat from "../FliesentischZentralrat";
import AnnouncementsView from "../educa-home-react/AnnouncementsView";
import SectionAccessCodeView from "./group-section-apps/SectionAccessCodeView";
import SectionChatView from "./group-section-apps/SectionChatView";
import SectionCalendarView from "./group-section-apps/SectionCalendarView";
import SectionDocumentsView from "./group-section-apps/SectionDocumentsView";
import SectionTaskView from "./group-section-apps/SectionTaskView";
import EducaWiki from "./group-section-apps/EducaWiki";
import {GROUP_SECTION_OVERVIEW_APP, GROUP_VIEWS} from "./group-browse/GroupBrowse";
import {BASE_ROUTES} from "../App";


export function EducaLearnerAppViewReact(props) {

    let history = useHistory()
    let {group_id, section_id, app_id} = useParams();
    let currentCloudUser = useSelector(s => s.currentCloudUser);
    let [app, setApp] = useState(null)
    let [section, setSection] = useState(null)
    let [group, setGroup] = useState(null)
    const fullScreen = useFullScreenHandle();
    const location = useLocation();

    const [path, setPath] = useState("");
    const [plainPath, setPlainPath] = useState("");


    useEffect(() => {
        if(plainPath != location.pathname) {
            setPlainPath(location.pathname)
            setPath(location.pathname
                .replace(BASE_ROUTES.ROOT_LEARNER, "")
                .split("/")
                .filter(element => element !== ""))
        }
    },[location])

    let navigate = (newPath, replace = false) => {
        console.log(newPath)
        if (newPath.length <= 0) return;

        if (replace)
            history.replace(BASE_ROUTES.ROOT_LEARNER + "/"  + newPath.join("/"));
        else history.push(BASE_ROUTES.ROOT_LEARNER + "/" + newPath.join("/"));
    }


    useEffect(() => {
        let foundGroup = currentCloudUser?.groups?.find((g) => g.id == group_id);
        setGroup(foundGroup)
        let foundSection = foundGroup?.sections?.find((s) => s.id == section_id)
        setSection(foundSection);
        let foundApp = foundSection?.section_group_apps?.find((s) => s.id == app_id)
        setApp(foundApp);
    }, [currentCloudUser, section_id, group_id, app_id])


    let getContent = () => {
        if (app?.group_app?.type)
            switch (app.group_app.type) {
                case "announcement":
                    if (
                        FliesentischZentralrat.sectionAnnouncementView(section)
                    )
                        return (
                            <AnnouncementsView
                                sections={[section]}
                                groupBrowse={true}
                                loadTemplates={true}
                            />
                        );
                    break;

                case "accessCode":
                    if (
                        FliesentischZentralrat.sectionAccesscodeView(
                            section
                        )
                    )
                        return (
                            <SectionAccessCodeView
                                group={group}
                                section={section}
                            />
                        );
                    break;

                case "chat":
                    if (
                        FliesentischZentralrat.sectionMessagesView(
                            section
                        )
                    )
                        return (
                            <SectionChatView
                                group={group}
                                section={section}
                            />
                        );
                    break;

                case "calendar":
                    if (
                        FliesentischZentralrat.sectionCalendarView(
                            section
                        )
                    )
                        return (
                            <SectionCalendarView
                                group={group}
                                section={section}
                            />
                        );
                    break;

                case "files":
                    if (
                        FliesentischZentralrat.sectionFilesView(
                            section
                        )
                    )
                        return (
                            <SectionDocumentsView
                                group={group}
                                section={section}
                            />
                        );
                    break;

                case "task":
                    if (
                        FliesentischZentralrat.sectionTaskView(
                            section
                        )
                    )
                        return (
                            <SectionTaskView
                                group={group}
                                section={section}
                            />
                        );
                    break;

                case "wikiPage":
                    if (
                        FliesentischZentralrat.sectionWikiEdit(
                            section
                        )
                        ||
                        FliesentischZentralrat.sectionWikiOpen(
                            section
                        )
                    )
                        return (
                            <EducaWiki
                                pathTrail={"wikiPage"}
                                canCreatePage={FliesentischZentralrat.sectionWikiEdit(section)}
                                canEdit={FliesentischZentralrat.sectionWikiEdit(section)}
                                canOpen={FliesentischZentralrat.sectionWikiOpen(section)}
                                modelType={"section"}
                                modelId={section?.id}
                                menuText={"Wiki"}
                            />
                        );
                    break;

            }
        return <Alert className="text-center w-100 mt-3" variant="danger">
            <i className="fas fa-exclamation-triangle pr-1"></i> Inhalt konnte
            nicht geladen werden. Eventuell besitzen Sie unzureichende
            Berechtigungen.
        </Alert>
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
                        {section ? <Breadcrumb.Item  onClick={() => history.push("/app/learner/" + group?.id + "/section/" + section?.id)} >{section?.name}</Breadcrumb.Item> : null}
                        {app ? <Breadcrumb.Item active>{app?.name}</Breadcrumb.Item> : null}
                    </Breadcrumb>
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
            {getContent()}
        </FullScreen>
    </div>

}
