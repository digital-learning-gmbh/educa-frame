import React, { useEffect, useState } from "react";
import { sortGroupSections } from "../EducaGroupViewReact";
import GroupBrowseContent from "./GroupBrowseContent";
import GroupBrowseNavigation from "./GroupBrowseNavigation";
import { BASE_ROUTES } from "../../App";
import EducaAjaxHelper from "../../helpers/EducaAjaxHelper";
import { GENERAL_UPDATE_OR_ADD_GROUP } from "../../reducers/GeneralReducer";
import { connect } from "react-redux";
import { FullScreen, useFullScreenHandle } from "react-full-screen";

export const GROUP_VIEWS = {
    SECTIONS: "sections",
    SETTINGS: "settings",
    FEED: "feed",
    TIMETABLE: "timetable",
    INTEGRATION: "integrations"
};

export const GROUP_SECTION_OVERVIEW_APP = "overview";

function GroupBrowse(props) {
    const [group, setGroup] = useState(null);
    const [groupView, setGroupView] = useState(null);
    const [section, setSection] = useState(null);
    const [sectionApp, setSectionApp] = useState(null);
    const [integration, setIntegration] = useState(null);
    const handle = useFullScreenHandle();

    useEffect(() => {
        let newGroup = null;
        let newGroupView = null;
        let newSection = null;
        let newSectionApp = null;
        let newIntegration = null;

        if (props.path.length === 0) {
            return;
        }

        if (props.path.length > 0) {
            if (Array.isArray(props.store.currentCloudUser.groups))
                newGroup = props.store.currentCloudUser.groups.find(
                    element => element.id === parseInt(props.path[0])
                );

            if (newGroup) newGroup = sortGroupSections(newGroup);
        }

        if (props.path.length === 1 && newGroup) {
            props.navigate([newGroup.id, GROUP_VIEWS.FEED], true);
        }

        if (props.path.length > 1 && newGroup) {
            newGroupView = Object.values(GROUP_VIEWS).find(
                element => element === props.path[1]
            );
        }

        if (props.path.length > 2 && newGroup && newGroupView && newGroupView !== GROUP_VIEWS.INTEGRATION) {
            if (
                !section ||
                section.id !== parseInt(props.path[2]) ||
                !section.members
            ) {
                newSection = newGroup.sections.find(
                    element => element.id === parseInt(props.path[2])
                );

                if (newSection) {
                    newSection = { ...newSection, members: {} };

                    EducaAjaxHelper.getSectionMembers(newSection.id).then(
                        resp => {
                            if (
                                resp.status > 0 &&
                                resp.payload &&
                                resp.payload.members
                            ) {
                                setSection({
                                    ...newSection,
                                    members: resp.payload.members
                                });
                            }
                        }
                    );
                }
            } else {
                newSection = section;
            }
        }

        if (props.path.length > 3 && newGroup && newGroupView && newSection) {
            newSectionApp = newSection.section_group_apps?.find(
                element => element.group_app.type === props.path[3]
            ) ?? {
                group_app: { type: GROUP_SECTION_OVERVIEW_APP }
            };
        }


        if (props.path.length > 2 && newGroup && newGroupView) {
            newIntegration = newGroup.external_integrations.find(
                element => element.id === parseInt(props.path[2])
            );
        }

        setGroup(newGroup);
        setGroupView(newGroupView);
        setSection(newSection);
        setSectionApp(newSectionApp);
        setIntegration(newIntegration);
    }, [props.path]);

    if (!group) return <></>;

    return (
        <div className="my-2">
            <GroupBrowseNavigation
                path={props.path}
                navigate={props.navigate}
                group={group}
                section={section}
                fullScreenHandle={handle}
            />
                    <FullScreen handle={handle}>
                        <div style={{backgroundColor: "#fbfbfc", minHeight:"100vh"}}>
                        <GroupBrowseContent
                            path={props.path}
                            navigate={props.navigate}
                            setGroup={group => {
                                setGroup(group);
                                props.updateGroup(group);
                            }}
                            setSection={section => {
                                setSection(section);

                                const index = group?.sections.findIndex(
                                    element => element.id === section.id
                                );
                                if (index < 0) return;

                                let _group = group;
                                _group.sections[index] = section;
                                setGroup(_group);
                                props.updateGroup(_group);
                            }}
                            group={group}
                            groupView={groupView}
                            section={section}
                            sectionApp={sectionApp}
                            integration={integration}
                        /></div>
                        </FullScreen>
        </div>
    );
}

const mapStateToProps = state => ({ store: state });

const mapDispatchToProps = dispatch => {
    return {
        // dispatching plain actions
        updateGroup: group =>
            dispatch({ type: GENERAL_UPDATE_OR_ADD_GROUP, payload: group })
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(GroupBrowse);
