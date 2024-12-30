import React, {useContext, useEffect, useState} from 'react';
import Card from "react-bootstrap/Card";
import {EducaCircularButton, EducaDefaultTable} from "../../../../shared/shared-components";
import {useDispatch, useSelector} from "react-redux";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import TenantEditDrawer from "./TenantEditDrawer";
import {useHistory, useLocation} from "react-router";
import Button from "react-bootstrap/Button";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import {SYSTEM_SETTINGS_SET_TENANTS} from "../../../reducers/GeneralReducer";
import "./styles.css"
import {BASE_ROUTES} from "../../../App";
import {MODAL_BUTTONS} from "../../../../shared/shared-components/EducaModal";
import {ModalContext} from "../../EducaSystemSettingsRoot";
import {Spinner} from "react-bootstrap";
import Modal from "react-bootstrap/Modal";
import "../../../../shared/shared-components/modals.css";
import SystemSettingsRoles from "../roles/SystemSettingsRoles";

function SystemSettingsTenants() {

    const [tenantId, setTenantId] = useState(null)
    const [roleEditorTenant, setRoleEditorTenant] = useState(null)
    const tenantsStore = useSelector(s => s.tenants)
    const location = useLocation()
    const history = useHistory()
    let [isLoading, setIsLoading] = useState(false);

    const glow = location?.state?.glow

    const {educaModalRef, safeDeleteModalRef} = useContext(ModalContext)


    const dispatch = useDispatch()
    const setTenantsStore = (tenants) => dispatch({type : SYSTEM_SETTINGS_SET_TENANTS, payload : tenants})

    useEffect(() => {
        const tenantId = new URLSearchParams(location?.search)?.get("tenant_id")
        setTenantId(tenantId)
    },[location?.search])

    useEffect(() => {
        if(location?.state?.tenantId > 0)
            setTenantToEdit(location?.state?.tenantId)
    },[location?.state?.tenantId])

    const setTenantToEdit = (id) => {
        const urlParams = new URLSearchParams(location.search)
        urlParams.set("tenant_id", id);
        history.push({
            search: "?"+urlParams.toString()
        });
    }
    const resetTenantId = () => history.push({search: ""})

    const navigateToTenant = (s) =>
    {
        const exec = () => window.location.href = "https://"+(s.domain+"/"+BASE_ROUTES.ROOT_SYSTEM_SETTINGS).replace(/([^:]\/)\/+/g, "$1")

        educaModalRef.current.open( b => b == MODAL_BUTTONS.YES? exec() : null,
            "Tenant wechseln",
            "Sie verlassen diese Seite um zum Tenant \""+s.name+"\" zu navigieren. Fortfahren?",
            [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
            )
    }
    const deleteTenant = (tenant) => {

        const exec = () =>
        {
            setIsLoading(true)
            AjaxHelper.deleteSystemSettingsTenant(tenant.id)
                .then( resp =>
                {
                    if(resp.status > 0)
                    {
                        setTenantsStore(tenantsStore.filter(t => t.id != tenant.id))
                        return SharedHelper.fireSuccessToast("Erfolg", "Der Tenant wurde erfolgreich gelöscht.")
                    }
                    throw new Error()
                })
                .catch( () => SharedHelper.fireErrorToast("Fehler", "Der Tenant konnte nicht gelöscht werden."))
                .finally(() => setIsLoading(false))
        }

        const keyword = "LÖSCHEN"
        safeDeleteModalRef?.current?.open(
            (b) => b? exec() : null,
            "'" + tenant.name +"' löschen",
            "Achtung, alle zugehörigen Daten dieses Tenants werden gelöscht. Wenn der Tenant wirklich gelöscht werden soll, geben Sie bitte "+keyword+ " in das untere Textfeld ein.",
            keyword
        )


    }


    return (
        <div  onClick={() => glow? history.push({state : {glow : false}}): null}>
            <TenantEditDrawer tenantId={tenantId} open={!!tenantId} onClose={() => resetTenantId()}/>
            <Card style={{backgroundColor: "white"}}>
                <Card.Header>
                    <div style={{flex : 1, display : "flex", flexDirection :"column"}}>
                        <h5 className="card-title">
                            <b><i className="fas fa-pencil-alt"></i> Tenants </b>
                            {isLoading && <Spinner className={"ml-2 align-self-center"} animation={"grow"}/>}
                        </h5>
                        <div className={"text-muted"}>Übersichtsliste aller Tenants im System</div>
                    </div>
                </Card.Header>
                <Card.Body>
                    <EducaDefaultTable
                        size={"lg"}
                        defaultSorted={[
                            {
                                id: "name",
                                desc: true
                            }
                        ]}
                        defaultPageSize={50}
                        pagination={true}
                        columnResizing={true}
                        globalFilter={true}
                        buttonPdfExport={true}
                        buttonExcelExport={true}
                        pageSizePicker={true}
                        columnPicker={true}
                        multiSelect={false}
                        filename={"benutzer_"}
                        customButtonBarComponents={
                            [
                                <Button
                                    variant={"primary"}
                                    onClick={() => setTenantToEdit(-1)}><i className={"fas fa-plus"}/> Tenant hinzufügen </Button>
                            ]}
                        columns={[
                            { Header: 'Domain', accessor: 'domain',  filter : true },
                            { Header: 'Name', accessor: 'name',  filter : true },
                            { Header: 'Logo', accessor: 'logoComp', width : "35" },
                            { Header: 'Farbe', accessor: 'colorComp', width : "35" },
                            { Header: 'Lizenz', accessor: 'licence',  filter : true },
                            { Header : 'Aktionen', accessor: 'actions' }
                        ]}
                        data={
                            tenantsStore?
                                tenantsStore?.map( s =>
                                {
                                    const logo = s?.logo? <img src={AjaxHelper.getTenantLogoUrl(s.logo)} width={30}/> : ""
                                    const dot = <span
                                        className={"mr-1"}
                                        style={{
                                            height: 15 + "px",
                                            width: 15 + "px",
                                            backgroundColor: s.color,
                                            borderRadius: "50%",
                                            display: "inline-block"
                                        }}
                                    ></span>
                                    return {...s,
                                        logoComp : logo,
                                        colorComp : dot,
                                        actions: <>
                                            <EducaCircularButton
                                                tooltip={"Bearbeiten"}
                                                className={"mr-1"}
                                                size={"small"} onClick={() => setTenantToEdit(s.id)}>
                                                <i className={"fas fa-pencil-alt"}/>
                                            </EducaCircularButton>
                                            <EducaCircularButton className={"mr-1"}
                                                                 variant={"outline-primary"}
                                                                 size={"small"}
                                                                 onClick={() => setRoleEditorTenant(s)}>
                                                <i className={"fas fa-key"}/>
                                            </EducaCircularButton>
                                            <EducaCircularButton className={"mr-1"}
                                                                 tooltip={"Löschen"}
                                                                 variant={"danger"}
                                                                 size={"small"}
                                                                 onClick={() => deleteTenant(s)}>
                                                <i className={"fas fa-trash"}/>
                                            </EducaCircularButton>
                                            <EducaCircularButton
                                                tooltip={"In Tenant wechseln"}
                                                className={glow?"glowing-button" : ""}
                                                variant={"secondary"}
                                                size={"small"}
                                                onClick={() => navigateToTenant(s)}>
                                                <i className={"fas fa-random"}/>
                                            </EducaCircularButton>
                                        </>
                                    }
                                }) : []
                        }
                    />
                </Card.Body>
            </Card>
        <RoleEditor tenant={roleEditorTenant} hide={() => setRoleEditorTenant(null)}/>
        </div>
    );
}

const RoleEditor = ({tenant, hide}) => {

    return <Modal show={tenant?.id} onHide={hide} dialogClassName={"modal-90-vw"}>
        <Modal.Header>
            <Modal.Title>
                {tenant?.name}: Rechte und Rollen
            </Modal.Title>
        </Modal.Header>
        <Modal.Body>
            {tenant? <SystemSettingsRoles tenant={tenant} returnRawContent={true}/> : null}
        </Modal.Body>
        <Modal.Footer>
            <Button onClick={() => hide()} variant={"secondary"}>Schließen</Button>
        </Modal.Footer>
    </Modal>

}

export default SystemSettingsTenants;
