import GroupFeedView from "../group-special-views/GroupFeedView";
import EducaGroupSettingsView from "../group-settings-react/EducaGroupSettingsView";
import GroupTimetableView from "../group-special-views/GroupTimetableView";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import SectionAnnouncementsView from "../group-section-apps/SectionAnnouncementsView";
import SectionAccessCodeView from "../group-section-apps/SectionAccessCodeView";
import SectionChatView from "../group-section-apps/SectionChatView";
import SectionCalendarView from "../group-section-apps/SectionCalendarView";
import SectionDocumentsView from "../group-section-apps/SectionDocumentsView";
import SectionTaskView from "../group-section-apps/SectionTaskView";
import { Alert } from "react-bootstrap";
import React from "react";
import { GROUP_SECTION_OVERVIEW_APP, GROUP_VIEWS } from "./GroupBrowse";
import EducaWiki from "../group-section-apps/EducaWiki";
import SectionMeetingView from "../group-section-apps/SectionMeetingView.js";
import SectionOpenCastView from "../group-section-apps/SectionOpenCastView.js";

export default function GroupBrowseContent(props) {
    switch (props.groupView) {
        case GROUP_VIEWS.FEED:
            return <GroupFeedView group={props.group} />;

        case GROUP_VIEWS.SETTINGS:
            return (
                <EducaGroupSettingsView
                    group={props.group}
                    section={props.section}
                    setGroup={props.setGroup}
                    setSection={props.setSection}
                />
            );

        case GROUP_VIEWS.TIMETABLE:
            return <GroupTimetableView group={props.group} />;

        case GROUP_VIEWS.SECTIONS:
            if (props.sectionApp?.group_app?.type)
                switch (props.sectionApp.group_app.type) {
                    case "announcement":
                        if (
                            FliesentischZentralrat.sectionAnnouncementView(
                                props.section
                            )
                        )
                            return (
                                <SectionAnnouncementsView
                                    group={props.group}
                                    section={props.section}
                                    setSection={props.setSection}
                                />
                            );
                        break;

                    case "accessCode":
                        if (
                            FliesentischZentralrat.sectionAccesscodeView(
                                props.section
                            )
                        )
                            return (
                                <SectionAccessCodeView
                                    group={props.group}
                                    section={props.section}
                                />
                            );
                        break;

                    case "chat":
                        if (
                            FliesentischZentralrat.sectionMessagesView(
                                props.section
                            )
                        )
                            return (
                                <SectionChatView
                                    group={props.group}
                                    section={props.section}
                                />
                            );
                        break;

                    case "calendar":
                        if (
                            FliesentischZentralrat.sectionCalendarView(
                                props.section
                            )
                        )
                            return (
                                <SectionCalendarView
                                    group={props.group}
                                    section={props.section}
                                />
                            );
                        break;

                    case "files":
                        if (
                            FliesentischZentralrat.sectionFilesView(
                                props.section
                            )
                        )
                            return (
                                <SectionDocumentsView
                                    group={props.group}
                                    section={props.section}
                                />
                            );
                        break;

                    case "meeting":
                        if (
                            FliesentischZentralrat.sectionMeetingView(
                                props.section
                            )
                        )
                            return (
                                <SectionMeetingView
                                    group={props.group}
                                    section={props.section}
                                />
                            );
                        break;

                    case "task":
                        if (
                            FliesentischZentralrat.sectionTaskView(
                                props.section
                            )
                        )
                            return (
                                <SectionTaskView
                                    group={props.group}
                                    section={props.section}
                                />
                            );
                        break;

                    case "opencast":
                        if (
                            FliesentischZentralrat.sectionOpencastOpen(
                                props.section
                            ) || true
                        )
                            return (
                                <SectionOpenCastView
                                    navigate={props.navigate}
                                    path={props.path}
                                    group={props.group}
                                    section={props.section}
                                    sectionApp={props.sectionApp}
                                />
                            );
                        break;

                    case "wikiPage":
                        if (
                            FliesentischZentralrat.sectionWikiEdit(
                                props.section
                            )
                            ||
                            FliesentischZentralrat.sectionWikiOpen(
                                props.section
                            )
                        )
                            return (
                                <EducaWiki
                                    pathTrail={"wikiPage"}
                                    canCreatePage={FliesentischZentralrat.sectionWikiEdit(props.section)}
                                    canEdit={FliesentischZentralrat.sectionWikiEdit(props.section)}
                                    canOpen={FliesentischZentralrat.sectionWikiOpen(props.section)}
                                    modelType={"section"}
                                    modelId={props.section?.id}
                                    menuText={"Wiki"}
                                />
                            );
                        break;

                    case GROUP_SECTION_OVERVIEW_APP:
                        if (
                            props.section?.section_group_apps &&
                            props.section.section_group_apps.length > 0 &&
                            props.section.section_group_apps[0].group_app?.type
                        )
                            props.navigate(
                                [
                                    props.group.id,
                                    GROUP_VIEWS.SECTIONS,
                                    props.section.id,
                                    props.section.section_group_apps[0]
                                        .group_app.type
                                ],
                                true
                            );

                        return <></>;
                }
            break;
    }

    return (
        <Alert className="text-center w-100 mt-3" variant="danger">
            <i className="fas fa-exclamation-triangle pr-1"></i> Inhalt konnte
            nicht geladen werden. Eventuell besitzen Sie unzureichende
            Berechtigungen.
        </Alert>
    );
}
