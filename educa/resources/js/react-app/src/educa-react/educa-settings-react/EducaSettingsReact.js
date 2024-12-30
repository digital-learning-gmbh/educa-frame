import React, {useEffect, useState} from "react";
import SideMenu from "../educa-components/SideMenu";
import { useSelector } from "react-redux";
import AdministrationSettingsEduca from "./SettingsPages/AdministrationSettings";
import GeneralSettings from "./SettingsPages/GeneralSettings";
import EducaAjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import {Alert} from "react-bootstrap";
import SecuritySettings from "./SettingsPages/SecuritySettings";
import AnalyticsDataSettings from "./SettingsPages/AnalyticsDataSettings";
import SessionSettings from "./SettingsPages/SessionSettings";
import NotificationSettings from "./SettingsPages/NotificationSettings";

export const SETTINGS_PAGES = {
    GENERAL: "settings",
    ADMINISTRATION: "stupla",
    SECURITY: "security",
    ANALYTICS_DATA: "analytics_data",
    SESSIONS: "sessions",
    NOTIFICATIONS : "notifications"
};

export default function EducaSettingsReact(props) {
    const me = useSelector(s => s.currentCloudUser);
    const [app, setApp] = useState(
        me.apps?.find(app => app?.appName === SETTINGS_PAGES.GENERAL)
    );

    const [settingsApps, setSettingsApps] = useState([]);

    useEffect(() => {
        EducaAjaxHelper.loadSettings()
            .then(resp => {
                if (resp.status > 0 && resp.payload.settingsApps) {
                    setSettingsApps(resp.payload.settingsApps);
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Konnte die Einstellungen nicht laden: " + err.message
                );
            });
    },[])

    const customGroupMenuItemsForSideMenu = () => {
        return {
            heading: { textAndId: "Einstellungen" },
            content:
                settingsApps
                    ?.map(app => {
                        return {
                            component: (
                                <div>
                                    <img
                                        src={app.icon}
                                        width={25}
                                        height={25}
                                        className={"mr-1"}
                                    />
                                    {app.name}
                                </div>
                            ),
                            clickCallback: () => setApp(app)
                        };
                    }) ?? []
        };
    };

    return (
        <div className="d-flex justify-content-between">
            <div style={{ width: "300px" }} className={"m-2"}>
                <SideMenu
                    menus={[
                        customGroupMenuItemsForSideMenu()
                    ]}
                />
            </div>
            <div className="col mt-2">
                <EducaSettingsReactContent app={app} setApp={setApp} />
            </div>
        </div>
    );
}

function EducaSettingsReactContent(props) {
    switch (props.app?.appName) {
        case SETTINGS_PAGES.GENERAL:
            return <GeneralSettings app={props.app} />;

        case SETTINGS_PAGES.ADMINISTRATION:
            return <AdministrationSettingsEduca />;

        case SETTINGS_PAGES.SECURITY:
            return <SecuritySettings />;

        case SETTINGS_PAGES.ANALYTICS_DATA:
            return <AnalyticsDataSettings />;

        case SETTINGS_PAGES.SESSIONS:
            return <SessionSettings />;

        case SETTINGS_PAGES.NOTIFICATIONS:
            return <NotificationSettings />;

        case null:
            props.setApp(SETTINGS_PAGES.GENERAL);
            return null;

        default:
            return (
                <div
                    style={{ display: "flex", flexDirection: "column" }}
                    className={"container"}
                >
                    <Alert variant={"info"}><i className="fas fa-info-circle"></i> Keine Einstellungen verfügbar</Alert>
                </div>
            );
    }
}
