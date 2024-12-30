import React, {useState} from "react";
import {Collapse, ListGroup} from "react-bootstrap";
import AjaxHelper from "./EducaAjaxHelper";
import {EducaCircularButton} from "../../shared/shared-components/Buttons";
import {SideMenuHeadingStyle} from "../educa-components/EducaStyles";
import {EducaInputConfirm} from "../../shared/shared-components/Inputs";
import {isUserLoggedIn} from "../EducaEventManager";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";
import {redux_store} from "../../../store";
import _ from "lodash";
import Select, {components} from "react-select";

export const SelectPlaceholder = props => {
    return <components.Placeholder {...props} />;
};


export const APP_NAMES =
    {
        //Permanent
        MESSAGES: "messages",
        EMAIL: "email",
        DASHBOARD: "dashboard",
        EXPLORE: "explore",

        //optional
        ANALYTICS: "analytics",
        CLASSBOOK: "klassenbuch",
        GROUPS: "groups",
        CALENDER: "calendar",
        CLOUD: "cloud",
        COMPANYPORTAL: "company",
        DEVICEMANAGER: "devices",
        LEARNMATERIALS: "bibliothek",
        SETTINGS: "settings",
        TASKS: "tasks",
        VERWALTUNG: "stupla",
        CONTACTS: "contacts",
        DOCUMENTS: "documents"
    }

export const LIMITS = {
    CHAT_MESSAGE_LIMIT: 2000,
    COMMENT_LIMIT: 2000,
    ANNOUCMENT_LIMIT: 2000,
    GROUP_DESCRIPTION: 2000,
    DOCUMENT_DESCRIPTION_LIMIT: 2000,
    TEXT_LIMIT: 200000,
    TASK_DESCRIPTION_LIMIT : 2000,
    TASK_PRIVATE_NOTE_LIMIT: 2000,
}

export const GLOBAL_CONFIG_SETTINGS = {
    OPEN_LEARNBIB_NEW_WINDOW_DEFAULT: false,
}

export const ALLOWED_NAVBAR_APPS_NAMES = [
    APP_NAMES.DASHBOARD /*Hardcoded*/,
    // APP_NAMES.MESSAGES /*Hardcoded*/,
    // APP_NAMES.TASKS,
    // APP_NAMES.CALENDER,
    // APP_NAMES.CLASSBOOK,
    // APP_NAMES.EMAIL,
    // APP_NAMES.EXPLORE,
    // APP_NAMES.LEARNMATERIALS,
    // APP_NAMES.CLOUD,
    // APP_NAMES.CONTACTS,
    // APP_NAMES.DOCUMENTS
];

export const TASK_TYPES_OPTIONS = [
    {value: "text", label: "Text-Antwort"},
    {value: "document", label: "Dokumente"},
    {value: "check", label: "Bestätigung"},
    // {value: "form", label: "Formular"},
    {value: "practical", label: "Praxis-Aufgabe"}
];

export const DEFAULT_COLORS =
    [
        "#343A40",
        "#3490dc",
        "#f44336",
        "#e91e63",
        "#9c27b0",
        "#673ab7",
        "#3f51b5",
        "#2196f3",
        "#03a9f4",
        "#00bcd4",
        "#009688",
        "#4caf50",
        "#8bc34a",
        "#cddc39",
        "#ffeb3b",
        "#ff9800",
        "#ff5722",
        "#795548"]

class EducaHelperClass {

    getGroupsForSections(sections, allGroups) {
        if (!Array.isArray(sections) || !Array.isArray(allGroups))
            return []

        let groups = []
        allGroups.forEach(group => {
            if (sections.find(section => section.group_id === group.id))
                groups.push(group)
        })

        return groups
    }


    /**
     * Components
     */
    getStatusImage(status, returnOnline) {
        let color = "rgb(183, 183, 183)" // offline default
        let tag = "Offline"
        if (status === "online" || returnOnline) {
            color = "#2de0a5"
            tag = "Online"
        } else if (status === "busy" || returnOnline) {
            color = "#F5455C"
            tag = "Beschäftigt"
        } else if (status === "away" || returnOnline) {
            color = "#FFD21F"
            tag = "Abwesend"
        }

        return <div title={tag}
                    style={{display: "flex", flexDirection: "column", justifyContent: "center", marginRight: "5px"}}>
            <span style={{height: "12px", width: "12px", borderRadius: "50%", background: color}}></span>
        </div>
    }

    /**
     *  SIDE MENU
     *
     *
     */

    _getComponentForApp(app, changeRoute) {
        let baseLink = "/appswitcher/switch/"
        let img = <img style={{width: "25px", height: "25px"}} src={app.icon}/>
        let imgExtern = <div style={{display: "flex", flex: 1, flexDirection: "row", justifyContent: "flex-end"}}><i
            className="fas fa-external-link-alt"/></div>

        if ( app.appName === APP_NAMES.ANALYTICS)
            return {
                component:
                    <div style={{display: "flex", flexDirection: "row"}}>
                        {img}
                        {app.name}
                    </div>,
                clickCallback: () =>  changeRoute("/app/analytics")
            }

        if (
            app.appName === APP_NAMES.VERWALTUNG
            || app.appName === APP_NAMES.CLOUD
            || app.appName === APP_NAMES.ANALYTICS
            || app.appName === APP_NAMES.COMPANYPORTAL
            || app.appName === APP_NAMES.DEVICEMANAGER)
            return {
                component:
                    <div style={{display: "flex", flexDirection: "row"}}>
                        {img}
                        {app.name}
                        {imgExtern}
                    </div>,
                clickCallback: () => {
                    let win = window.open(baseLink + app.appName, '_blank');
                    win.focus();
                }
            }

        return {
            component: <div> {img}{app.name}</div>,
            clickCallback: () => alert("clicked " + app.name)
        }
    }

    /**
     * Returns an JS object that contains information about Apps for the SideMenu
     * @param apps Apps from the server/redux store (currentCloudUser.apps)

     */
    getAppMenuObjectsForSideMenu(apps, changeRoute, translate) {
        if (!apps || !Array.isArray(apps) || (apps?.length == 1 && apps[0].appName === APP_NAMES.SETTINGS) )
            return null;
        let content = []
        apps.forEach(app => {
            if (app.appName !== APP_NAMES.SETTINGS && !(ALLOWED_NAVBAR_APPS_NAMES.find(name => app.appName === name)))
                content.push(this._getComponentForApp(app, changeRoute))
        })
        if(content?.length == 0 )
            return null
        return {
            heading: {textAndId: translate("apps","Apps")},
            content: content
        }

    }


    /**
     * Component for Groups Header in SideMenu
     * @param props
     * @returns {JSX.Element}
     * @constructor
     */
    _SideMenuGroupCreateHeadingComponent(props) {

        return <><div style={{display: "flex", flexDirection: "column"}}>
            <div style={{display: "flex", flexDirection: "row"}}>
                <div style={SideMenuHeadingStyle}>{props.name}</div>
                {FliesentischZentralrat.globalGroupCreate()? <EducaCircularButton
                    style={{marginLeft: "5px"}}
                    tooltip={"Neue Gruppe erstellen"}
                    variant={ "success"}
                    onClick={() => {props.groupCreateClickCallback()}}
                    size={"small"}>
                    {<i className="fas fa-plus"></i>}
                </EducaCircularButton> : null}
            </div>
        </div></>
    }

    getGroupClusterMenuObjectsForSideMenu(routeChangeCallback, groupCreateClickCallback, translate)
    {
        let group_cluster = _.cloneDeep(redux_store.getState().currentCloudUser?.group_cluster)
        let heading = <this._SideMenuGroupCreateHeadingComponent name={translate("home.my_groups","Meine Gruppen")} groupCreateClickCallback={groupCreateClickCallback}/>

        if( Array.isArray(group_cluster))
            group_cluster?.forEach(c => {
                if (Array.isArray(c?.groups)) {
                    c.groups = c?.groups?.map(group => ({
                        id: group.id,
                        isSelected: false,
                        component: <div className={"row"}><img loading={"lazy"} className={"mr-1 rounded"} style={{width: "60px", height: "60px"}}
                                                               src={AjaxHelper.getGroupAvatarUrl(group.id, 100, group.image)}/> <div style={{width:"calc(100% - 70px)"}}>{group.name}</div>
                        </div>,
                        clickCallback: () => routeChangeCallback ? routeChangeCallback(group.id) : null
                    }))
                }})

        let content = []
        const groups = redux_store.getState().currentCloudUser?.groups
        if (!groups || !Array.isArray(groups)) {
            content.push({
                heading: {textAndId: translate("home.my_groups","Meine Gruppen"), component: heading}, content: [{
                    component: <div>{translate("group.no_groups","Du hast noch keine Gruppen")}</div>
                }]
            })
            return {
                heading: {textAndId: translate("home.my_groups","Meine Gruppen"), component: heading},
                content: content
            }
        }
        groups.forEach(group => {
            content.push(
                {
                    id: group.id,
                    isSelected: false,
                    component: <div className={"row"}><img loading={"lazy"} className={"mr-1 rounded"} style={{width: "60px", height: "60px"}}
                                                           src={AjaxHelper.getGroupAvatarUrl(group.id, 100, group.image)}/><div style={{width:"calc(100% - 70px)", display: "flex", alignItems: "center"}}>
                        <div>
                        <h5 style={{textOverflow: "hidden", fontWeight: "bold"}}>{group.name}</h5>
                        <h6 style={{textOverflow: "hidden"}}>{group.sections?.length} Bereiche</h6>
                    </div></div>
                    </div>,
                    clickCallback: () => routeChangeCallback ? routeChangeCallback(group.id) : null
                })
        })

        return {
            heading: {textAndId: translate("home.my_groups","Meine Gruppen"), component: heading},
            cluster : group_cluster,
            content: content
        }
    }


    getGroupClusterMenuObjectsForSideMenuSmall(routeChangeCallback, groupCreateClickCallback)
    {
        let group_cluster = _.cloneDeep(redux_store.getState().currentCloudUser?.group_cluster)
        let heading = <this._SideMenuGroupCreateHeadingComponent name={"Gruppen"} groupCreateClickCallback={groupCreateClickCallback}/>

        if( Array.isArray(group_cluster))
            group_cluster?.forEach(c => {
                if (Array.isArray(c?.groups)) {
                    c.groups = c?.groups?.map(group => ({
                        id: group.id,
                        isSelected: false,
                        component: <div className={"row"}><img className={"mr-1"} style={{width: "60px", height: "60px"}}
                                                               src={AjaxHelper.getGroupAvatarUrl(group.id, 100, group.image)}/> <div style={{width:"calc(100% - 70px)"}}></div>
                        </div>,
                        clickCallback: () => routeChangeCallback ? routeChangeCallback(group.id) : null
                    }))
                }})

        let content = []
        // content.push({
        //     heading: {textAndId: "Gruppen erkunden", component: heading}, content: [{
        //         component: <div>Du hast noch keine Gruppen</div>
        //     }]
        // })

        const groups = redux_store.getState().currentCloudUser?.groups
        if (!groups || !Array.isArray(groups)) {
            content.push({
                heading: {textAndId: "Gruppen", component: heading}, content: [{
                    component: <div>Du hast noch keine Gruppen</div>
                }]
            })
            return {
                heading: {textAndId: "Gruppen", component: heading},
                content: content
            }
        }
        groups.forEach(group => {
            content.push(
                {
                    id: group.id,
                    isSelected: false,
                    component: <div className={"row"}><img className={"mr-1"} style={{width: "60px", height: "60px"}}
                                                           src={AjaxHelper.getGroupAvatarUrl(group.id, 100, group.image)}/> <div style={{width:"calc(100% - 70px)"}}>
                                                    </div>
                    </div>,
                    clickCallback: () => routeChangeCallback ? routeChangeCallback(group.id) : null
                })
        })

        return {
            heading: {textAndId: "Gruppen", component: heading},
         //   cluster : group_cluster,
            content: content
        }
    }

    fireErrorToast(title, content, delay = 5000, force) {
        if (isUserLoggedIn() || force)
            SharedHelper.fireErrorToast(title, content, delay)
    }

    fireWarningToast(title, content, delay = 5000) {
        SharedHelper.fireWarningToast(title, content, delay)
    }

    fireInfoToast(title, content, delay = 5000) {
        SharedHelper.fireInfoToast(title,content, delay);
    }

    fireSuccessToast(title, content, delay = 5000) {
        SharedHelper.fireSuccessToast(title,content, delay);
    }

    changeUser = (id) => {
        localStorage.setItem('educa_rc_token_user_alias', SharedHelper.getCookie("educa_rc_token"));
        localStorage.setItem('educa_rc_uid_user_alias', SharedHelper.getCookie("educa_rc_uid"));

        fetch('/api/v1/administration/masterdata/users/' + id + '/jwt?token=' + localStorage.getItem('jwt'))
            .then(response => response.json())
            .then(data => {
                localStorage.setItem('jwt_user_alias', data["payload"]["jwt"]);
                window.location.href = "/cloud/user/" + id + "/switch";
            }).
        catch( err =>
        {
            SharedHelper.fireErrorToast("Fehler", "Ein Fehler ist beim Wechseln aufgetreten.")
        })
    }

}

export const DRAWER_DEFAULT_STYLES = {
    root: {
        position: "absolute",
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        overflow: "hidden"
    },
    sidebar: {
        zIndex: 1031,
        position: "fixed",
        background : "#f2f3f5",
        top: 0,
        bottom: 0,
        width : "100vw",
        transition: "transform .3s ease-out",
        WebkitTransition: "-webkit-transform .3s ease-out",
        willChange: "transform",
        overflowY: "auto"
    },
    content: {
        position: "absolute",
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        overflowY: "auto",
        WebkitOverflowScrolling: "touch",
        transition: "left .3s ease-out, right .3s ease-out"
    },
    overlay: {
        zIndex: 1,
        position: "fixed",
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        opacity: 0,
        visibility: "hidden",
        transition: "opacity .3s ease-out, visibility .3s ease-out",
        backgroundColor: "rgba(0,0,0,.3)"
    },
    dragHandle: {
        zIndex: 1,
        position: "fixed",
        top: 0,
        bottom: 0
    }
}

export const getDayOptions = (translate) => {

    return [
        {value : 1, label : translate("days.monday")},
        {value : 2, label : translate("days.tuesday")},
        {value : 3, label : translate("days.wednesday")},
        {value : 4, label : translate("days.thursday")},
        {value : 5, label : translate("days.friday")},
        {value : 6, label : translate("days.saturday")},
        {value : 0, label : translate("days.sunday")},
    ]
}



let EducaHelper = new EducaHelperClass();

export default EducaHelper
