import React, { useEffect, useRef } from "react";
import { Card, ListGroup } from "react-bootstrap";
import {
    Redirect,
    Route,
    Switch,
    useHistory,
    useRouteMatch,
} from "react-router";
import SystemSettingsUsers from "./content/users/SystemSettingsUsers";
import { SideMenuHeadingStyle } from "../educa-components/EducaStyles";
import "./styles.css";
import { useDispatch, useSelector } from "react-redux";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import {
    SYSTEM_SETTINGS_SET_ROLES,
    SYSTEM_SETTINGS_SET_TENANTS,
} from "../reducers/GeneralReducer";
import SystemSettingsRoles from "./content/roles/SystemSettingsRoles";
import SystemSettingsGroups from "./content/groups/SystemSettingsGroups";
import FliesentischZentralrat from "../FliesentischZentralrat";
import { BASE_ROUTES } from "../App";
import SystemSettingsTenants from "./content/tenants/SystemSettingsTenants";
import SystemSettingsGeneral from "./content/general/SystemSettingsGeneral";
import SystemSettingsAnalytics from "./content/analytics/SystemSettingsAnalytics";
import Button from "react-bootstrap/Button";
import { EducaCardLinkButton } from "../../shared/shared-components/Buttons";
import EducaModal from "../../shared/shared-components/EducaModal";
import SafeDeleteModal from "../../shared/shared-components/SafeDeleteModal";
import SystemSettingsAddressbookEdit from "./content/contacts/SystemSettingsAddressbookEdit";
import SystemSettingsLearnContent from "./content/learnContent/SystemSettingsLearnContent";
import SystemSettingsStore from "./content/store/SystemSettingsStore";
import SystemSettingsMaintenance from "./content/maintenance/SystemSettingsMaintenance.js";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper.js";

export const ModalContext = React.createContext();

function EducaSystemSettingsRoot(props) {
    let { url } = useRouteMatch();
    const history = useHistory();
    const educaRootModalRef = useRef();
    const educaRootSafeDeleteModalRef = useRef();

    const dispatch = useDispatch();
    const setTenants = (tenants) =>
        dispatch({ type: SYSTEM_SETTINGS_SET_TENANTS, payload: tenants });
    const setRoles = (roles) =>
        dispatch({ type: SYSTEM_SETTINGS_SET_ROLES, payload: roles });

    const tenant = useSelector((s) => s.tenant);
    const [translate] = useEducaLocalizedStrings()

    useEffect(() => {
        Promise.all([loadPermissions(), loadTenants()]).catch((err) => {
            SharedHelper.fireErrorToast(
                "Fehler",
                "Die Dante konnten nicht vollständig geladen werden."
            );
        });
    }, []);
    const setRoute = (route) => {
        history.push(url + "/" + route);
    };

    const loadPermissions = () => {
        return AjaxHelper.loadSystemSettingsRoles(tenant.id).then((resp) => {
            if (resp.payload.roles) return setRoles(resp.payload.roles);
            throw new Error();
        });
    };

    const loadTenants = () => {
        return AjaxHelper.loadSystemSettingsTenants().then((resp) => {
            if (resp.payload.tenants) return setTenants(resp.payload.tenants);
            throw new Error();
        });
    };

    if (!FliesentischZentralrat.systemSettingsOpen())
        return <Redirect to={BASE_ROUTES.ROOT} />;

    const listGroupItemStyle = { cursor: "pointer" };
    return (
        <div className={"mt-2 row flex-wt"}>
            <div
                style={{
                    paddingLeft: "15px",
                    paddingRight: "15px",
                    width: "300px",
                }}
            >
                <div style={SideMenuHeadingStyle}>{translate("navbar.system","Systemsteuerung")}</div>
                <Card className={"mt-2 mb-2 text-center"}>
                    <div
                        className="card-up aqua-gradient"
                        style={{
                            backgroundImage:
                                tenant && tenant.coverImage
                                    ? "url('/storage/images/tenants/" +
                                      tenant.coverImage +
                                      "')"
                                    : "url('/images/nlq_background.jpg')",
                        }}
                    ></div>
                    <div className="avatar mx-auto white">
                        <img
                            src={
                                tenant && tenant.logo
                                    ? "/storage/images/tenants/" + tenant.logo
                                    : "/images/neural.svg"
                            }
                            className="rounded-circle img-responsive bg-white p-3 roundedTenantImage"
                            alt="Tenant Logo"
                        />
                    </div>
                    <Card.Body>
                        <h5 className="card-title">{tenant?.name}</h5>
                        <p className="card-text">
                            {tenant?.information_text ? (
                                tenant?.information_text
                            ) : (
                                <i>
                                    {translate("system_settings.info_text_missing","Es wurde noch kein Beschreibungstext hinterlegt")}
                                </i>
                            )}
                        </p>
                        {FliesentischZentralrat.systemSettingsManageTenants() ? (
                            <>
                                <Button
                                    onClick={() =>
                                        history.push({
                                            pathname:
                                                BASE_ROUTES.ROOT_SYSTEM_SETTINGS +
                                                "/tenants",
                                            state: {
                                                glow: false,
                                                tenantId: tenant.id,
                                            },
                                        })
                                    }
                                >
                                    {" "}
                                    <i className={"fas fa-wrench"} /> {translate("system_settings.configure_tenant","Tenant konfigurieren")}
                                </Button>{" "}
                                <br></br>
                                <EducaCardLinkButton
                                    onClick={() =>
                                        history.push({
                                            pathname:
                                                BASE_ROUTES.ROOT_SYSTEM_SETTINGS +
                                                "/tenants",
                                            state: { glow: true },
                                        })
                                    }
                                >
                                    {" "}
                                    <i className={"fas fa-sync"} /> {translate("system_settings.change_tenant","Tenant wechseln")}
                                </EducaCardLinkButton>
                            </>
                        ) : null}
                    </Card.Body>
                </Card>
                <ListGroup variant={"flush"}>
                    <ListGroup.Item
                        style={listGroupItemStyle}
                        onClick={() => setRoute("general")}
                    >
                        <i className={"fas fa-cogs"} /> {translate("system_settings.general","Allgemein")}
                    </ListGroup.Item>
                    {FliesentischZentralrat.systemSettingsManageUsers() && (
                        <ListGroup.Item
                            style={listGroupItemStyle}
                            onClick={() => setRoute("users")}
                        >
                            <i className={"fas fa-user-friends"} /> {translate("system_settings.user","Benutzer")}
                        </ListGroup.Item>
                    )}
                    {FliesentischZentralrat.systemSettingsManagePermissions() && (
                        <ListGroup.Item
                            style={listGroupItemStyle}
                            onClick={() => setRoute("permissions")}
                        >
                            <i className={"fas fa-key"} /> {translate("system_settings.rights_roles","Rechte & Rollen")}
                        </ListGroup.Item>
                    )}
                    {FliesentischZentralrat.systemSettingsManageAnalytics() && (
                        <ListGroup.Item
                            style={listGroupItemStyle}
                            onClick={() => setRoute("analytics")}
                        >
                            <i className={"fas fa-chart-pie"} /> {translate("system_settings.analytics","Analytics")}
                        </ListGroup.Item>
                    )}
                    {FliesentischZentralrat.systemSettingsManageGroups() && (
                        <ListGroup.Item
                            style={listGroupItemStyle}
                            onClick={() => setRoute("groups")}
                        >
                            <i className={"fas fa-users"} /> {translate("system_settings.groups","Gruppen")}
                        </ListGroup.Item>
                    )}
                    {FliesentischZentralrat.systemSettingsManageTenants() && (
                        <ListGroup.Item
                            style={listGroupItemStyle}
                            onClick={() => setRoute("addressbook")}
                        >
                            <i className={"fas fa-address-book"} /> {translate( "navbar.addressbook","Adressbuch")}
                        </ListGroup.Item>
                    )}
                    {FliesentischZentralrat.systemSettingsManageTenants() && (
                        <ListGroup.Item
                            style={listGroupItemStyle}
                            onClick={() => setRoute("learnContent")}
                        >
                            <i className="fas fa-book"></i> {translate("home_feed.learning_contents","Lerninhalte",)}
                        </ListGroup.Item>
                    )}
                    {FliesentischZentralrat.systemSettingsManageTenants() && (
                        <ListGroup.Item
                            style={listGroupItemStyle}
                            onClick={() => setRoute("store")}
                        >
                            <i className="fas fa-shopping-basket"></i> {translate("system_settings.store","Store")}
                        </ListGroup.Item>
                    )}
                    {FliesentischZentralrat.systemSettingsManageTenants() && (
                        <ListGroup.Item
                            style={listGroupItemStyle}
                            onClick={() => setRoute("tenants")}
                        >
                            <i className={"fas fa-house-user"} /> {translate("system_settings.tenants","Tenants")}
                        </ListGroup.Item>
                    )}
                    {FliesentischZentralrat.systemSettingsMaintenance() && (
                        <ListGroup.Item
                            style={listGroupItemStyle}
                            onClick={() => setRoute("maintenance")}
                        >
                            <i className="fas fa-wrench"></i> {translate("system_settings.maintenance","Wartung & Maintenance")}
                        </ListGroup.Item>
                    )}
                </ListGroup>
            </div>
            <div className={"col"}>
                <ModalContext.Provider
                    value={{
                        educaModalRef: educaRootModalRef,
                        safeDeleteModalRef: educaRootSafeDeleteModalRef,
                    }}
                >
                    <Switch>
                        <Route
                            path={url + "/general"}
                            component={SystemSettingsGeneral}
                        />
                        {FliesentischZentralrat.systemSettingsManageUsers() && (
                            <Route
                                path={url + "/users"}
                                component={SystemSettingsUsers}
                            />
                        )}
                        {FliesentischZentralrat.systemSettingsManagePermissions() && (
                            <Route
                                path={url + "/permissions"}
                                render={() => <SystemSettingsRoles tenant={tenant}/>}
                            />
                        )}
                        {FliesentischZentralrat.systemSettingsManageGroups() && (
                            <Route
                                path={url + "/groups"}
                                component={SystemSettingsGroups}
                            />
                        )}
                        {FliesentischZentralrat.systemSettingsManageAnalytics() && (
                            <Route
                                path={url + "/analytics"}
                                component={SystemSettingsAnalytics}
                            />
                        )}
                        {FliesentischZentralrat.systemSettingsManageTenants() && (
                            <Route
                                path={url + "/tenants"}
                                component={SystemSettingsTenants}
                            />
                        )}
                        {FliesentischZentralrat.systemSettingsManageTenants() && (
                            <Route
                                path={url + "/addressbook"}
                                component={SystemSettingsAddressbookEdit}
                            />
                        )}
                        {FliesentischZentralrat.systemSettingsManageTenants() && (
                            <Route
                                path={url + "/learnContent"}
                                component={SystemSettingsLearnContent}
                            />
                        )}
                        {FliesentischZentralrat.systemSettingsManageTenants() && (
                            <Route
                                path={url + "/store"}
                                component={SystemSettingsStore}
                            />
                        )}
                        {FliesentischZentralrat.systemSettingsMaintenance() && (
                            <Route
                                path={url + "/maintenance"}
                                component={SystemSettingsMaintenance}
                            />
                        )}
                        <Route component={SystemSettingsGeneral} />
                    </Switch>
                </ModalContext.Provider>
            </div>
            <EducaModal ref={educaRootModalRef} />
            <SafeDeleteModal ref={educaRootSafeDeleteModalRef} />
        </div>
    );
}

export default EducaSystemSettingsRoot;
