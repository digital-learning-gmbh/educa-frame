import React, { useState } from "react";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import { Tab, Tabs } from "react-bootstrap";
import GroupMembersTab from "./GroupMembersTab";
import GroupSettingsTab from "./GroupSettingsTab";
import GroupSectionsTab from "./GroupSectionsTab";
import GroupRoleTab from "./GroupRoleTab";
import GroupExternalLinks from "./GroupExternalLinks";

export default function GroupTabs(props) {
    const [activeTab, setActiveTab] = useState("general");

    return (
        <Tabs
            id="controlled-tab-example"
            activeKey={activeTab}
            onSelect={tab => setActiveTab(tab)}
        >
            {FliesentischZentralrat.groupEditGroup(props.group) ? (
                <Tab eventKey={"general"} title={"Allgemein"}>
                    <GroupSettingsTab
                        group={props.group}
                        setGroup={props.setGroup}
                    />
                </Tab>
            ) : null}
            {FliesentischZentralrat.groupEditMember(props.group) ? (
                <Tab eventKey={"members"} title={"Mitglieder"}>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "column"
                        }}
                        className={"m-2"}
                    >
                        <GroupMembersTab
                            group={props.group}
                            setGroup={props.setGroup}
                        />
                    </div>
                </Tab>
            ) : null}
            {FliesentischZentralrat.groupEditGroup(props.group) ? (
                <Tab eventKey={"section"} title={"Bereiche"}>
                    <GroupSectionsTab
                        group={props.group}
                        setGroup={props.setGroup}
                    />
                </Tab>
            ) : null}
             {FliesentischZentralrat.groupEditGroup(props.group) ? (
                <Tab eventKey={"external_identifiers"} title={"Externe Integrationen"}>
                    <GroupExternalLinks
                        group={props.group}
                        setGroup={props.setGroup}
                    />
                </Tab>
            ) : null}
            {FliesentischZentralrat.groupEditRoles(props.group) ? (
                <Tab eventKey={"roles"} title={"Rollen"}>
                    <div
                        style={{
                            display: "flex",
                            flexDirection: "column"
                        }}
                        className={"m-2"}
                    >
                        <GroupRoleTab
                            group={props.group}
                            roles={props.group.rolesWithPermission}
                            groupId={props.group.id}
                            setGroup={props.setGroup}
                        />
                    </div>
                </Tab>
            ) : null}
        </Tabs>
    );
}
