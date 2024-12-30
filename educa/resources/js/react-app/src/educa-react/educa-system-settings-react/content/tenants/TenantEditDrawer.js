import React, {useContext, useEffect, useRef, useState} from 'react';
import Sidebar from "react-sidebar";
import {Card, Col, FormControl, Navbar, Row} from "react-bootstrap";
import {DisplayPair} from "../../../../shared/shared-components/Inputs";
import Button from "react-bootstrap/Button";
import {useDispatch, useSelector} from "react-redux";
import ReactSwitch from "react-switch";
import TextareaAutosize from "react-textarea-autosize";
import {ChromePicker} from "react-color";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import EducaLabeledSwitch from "../../../../shared/shared-components/EducaLabeledSwitch";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import {GENERAL_SET_TENANT, SYSTEM_SETTINGS_SET_TENANTS} from "../../../reducers/GeneralReducer";
import {MODAL_BUTTONS} from "../../../../shared/shared-components/EducaModal";
import {ModalContext} from "../../EducaSystemSettingsRoot";
import Select from "react-select";
import EducaAjaxHelper from "../../../helpers/EducaAjaxHelper";
import {DRAWER_DEFAULT_STYLES} from "../../../helpers/EducaHelper";

const defaultTenant =
    {
        name : "",
        logo : "",
        hideLogoText : false,
        color : "#0047ff",
        domain : "",
        licence : "default",
        impressum : "",
        maxUsers : -1,
        overrideLoadingAnimation : false,
        isVisibleForOther : true,
        information_text : "",
        ms_graph_client_id : "",
        ms_graph_secret_id : "",
        ms_graph_tenant_id : "",
        openai_key : "",
        allowRegister : true,
        roleRegister : null,
        isFallBackTenant : true,
        newLogo : null,
        deleteLogo : false,
        newCover : null,
        deleteCover : false
    }
function TenantEditDrawer({tenantId, onClose, open}) {

    const [tenant, setTenant] = useState(defaultTenant)
    const tenantsStore = useSelector(s => s.tenants)
    const [errors, setErrors] = useState({})
    const dispatch = useDispatch()
    const currentTenant = useSelector(s => s.tenant)
    const setStoreTenant = (tenant) => dispatch({type : GENERAL_SET_TENANT, payload : tenant})

    const setTenantsStore = (tenants) => dispatch({type : SYSTEM_SETTINGS_SET_TENANTS, payload : tenants})
    const fileInputRef = useRef()
    const coverFileInputRef = useRef()
    const [roles, setRoles] = useState([])

    const {educaModalRef} = useContext(ModalContext)

    useEffect(() => {
        setTenant(defaultTenant)
        setErrors({})
        if(tenantId)
            loadTenantDetails()
    },[tenantId, tenantsStore])

    const loadTenantDetails = () => {
        // ajax?
        if(tenantId != -1)
            return setTenant(tenantsStore?.find(t => t.id == tenantId))
        setTenant(defaultTenant)
    }

    useEffect(() => {
        if(!tenant || !tenant?.id)
            return

        EducaAjaxHelper.getSystemSettingsTenant(tenant.id)
            .then(resp => {
                if (resp.payload?.roles) {
                    setRoles(resp.payload?.roles);
                }
            })
            .catch(err => {
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Die Rollen konnten nicht geladen werden. " + err.message
                );
            });
    },[tenant])

    if(!tenant)
        return null

    const validate = () =>
    {
        let errs = {
            name : !tenant.name,
            domain : !tenant.domain
        };
        setErrors(errs);
        return Object.keys(errs).reduce((prev, curr) => !!(prev && !errs[curr]), true);
    }

    const save = () =>
    {
        if(!validate())
        {
            SharedHelper.fireWarningToast("Fehlende Daten", "Bitte geben Sie alle notwendigen Daten an.")
            return
        }

        const exec = () => {
            (tenant?.id ? AjaxHelper.updateSystemSettingsTenant(tenant) : AjaxHelper.createSystemSettingsTenant(tenant))
                .then(resp => {
                    if (resp.payload.tenant) {
                        if(currentTenant?.id == resp.payload.tenant.id)
                            setStoreTenant(resp.payload.tenant)
                        setTenant(resp.payload.tenant)
                        if(tenant?.id)
                            setTenantsStore(tenantsStore.map(t => t.id == resp.payload.tenant.id ? resp.payload.tenant : t))
                        else
                            setTenantsStore([...tenantsStore,resp.payload.tenant ])

                        SharedHelper.fireSuccessToast("Erfolg", "Der Tenant wurde gespeichert.")
                        return onClose()
                    }
                    throw new Error()
                })
                .catch(() => SharedHelper.fireErrorToast("Fehler", "Tenant-Daten konnten nicht geladen werden."))
        }

        educaModalRef?.current?.open(
            btn => btn == MODAL_BUTTONS.YES? exec() : null,
            "Speichern",
            "Möchten Sie den Tenant wirklich speichern?",
            [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
            )

    }

    const content = <>
        <Navbar sticky="top" bg="primary" variant={"dark"}>
            <div style={{display: "flex", flexDirection: "row", flex: 1,}}>
                <Navbar.Brand>{tenantId > 0? "Tenant bearbeiten: " + tenant?.name : "Neuer Tenant"} </Navbar.Brand>
                <div style={{flex: 1, display: "flex", flexDirection: "row", justifyContent: "flex-end"}}>
                    <Button
                        style={{backgroundColor: "transparent"}}
                        onClick={onClose}>
                        <i className={"fa fa-times"}/>
                    </Button>
                </div>
            </div>

        </Navbar>

        <div className={"col w-100 m-2"}>
            <Card>
                <Card.Header>
                    <Card.Title>
                        Allgemein
                    </Card.Title>
                </Card.Header>
                <Card.Body>
                    <Row>
                        <Col>
                            <DisplayPair title={"Name"}>
                                <FormControl
                                    isInvalid={errors.name}
                                    placeholder={"Name"}
                                    value={tenant.name??""}
                                    onChange={e => {setTenant({...tenant, name : e.target.value}); setErrors({...errors, name : false})}}
                                />
                            </DisplayPair>
                        </Col>
                        <Col>
                            <DisplayPair title={"Domain"}>
                                <FormControl
                                    isInvalid={errors.domain}
                                    disabled={tenant?.id}
                                    placeholder={"Domain"}
                                    value={tenant.domain??""}
                                    onChange={e => {setTenant({...tenant, domain : e.target.value}); setErrors({...errors, domain : false})}}
                                />
                            </DisplayPair>
                        </Col>
                    </Row>
                    <Row>
                        <Col>
                            <DisplayPair title={"Lizenz"}>
                                <FormControl
                                    placeholder={"Lizenz"}
                                    value={tenant.licence??""}
                                    onChange={e => setTenant({...tenant, licence : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                        <Col>
                            <DisplayPair title={"Maximale Anzahl von Nutzern"}>
                                <FormControl
                                    placeholder={"max. Nutzerzahl"}
                                    type={"number"}
                                    min={-1}
                                    value={tenant.maxUsers??""}
                                    onChange={(e) => {
                                        setTenant({...tenant, maxUsers : parseInt(e.target.value)})
                                    }}
                                />
                            </DisplayPair>
                        </Col>
                    </Row>
                    <Row>
                        <Col>
                            <DisplayPair title={"Impressum"}>
                                <TextareaAutosize
                                    className={"form-control"}
                                    minRows={4}
                                    placeholder={"Impressum"}
                                    value={tenant.impressum??""}
                                    onChange={e => setTenant({...tenant, impressum : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                        <Col>
                            <DisplayPair title={"Beschreibung"}>
                                <TextareaAutosize
                                    value={tenant.information_text??""}
                                    onChange={e => setTenant({...tenant, information_text : e.target.value})}
                                    className={"form-control"}
                                    minRows={4}
                                    placeholder={"Beschreibung"}
                                />
                            </DisplayPair>
                        </Col>
                    </Row>
                </Card.Body>
            </Card>
            <Card className={"mt-2"}>
                <Card.Header>
                    <Card.Title>
                        Darstellung & Design
                    </Card.Title>
                </Card.Header>
                <Card.Body>
                    <Row>
                        <Col>
                            <DisplayPair title={"Cover (Bild auf der Startseite)"}>
                                <Row>
                                    <img src={AjaxHelper.getTenantCoverUrl(tenant?.coverImage)} alt={"Kein Logo"}
                                         width={200}/>
                                    <Col>
                                        {tenant?.coverImage && <div className={"mb-2"}>
                                            <EducaLabeledSwitch labelRight={"Cover löschen"}
                                                                size={"lg"}
                                                                checked={!!tenant.deleteCover}
                                                                onChange={b => setTenant({
                                                                    ...tenant,
                                                                    deleteCover: b
                                                                })}/>
                                        </div>}
                                        {tenant.deleteCover ? null : <>
                                            <input
                                                type="file"
                                                ref={coverFileInputRef}
                                                onChange={(evt) => {
                                                    if (evt.target.files?.length === 1
                                                        && (evt.target.files[0].name?.toLowerCase()?.includes("jpg")
                                                            || evt.target.files[0].name?.toLowerCase()?.includes("jpeg")
                                                            || evt.target.files[0].name?.toLowerCase()?.includes("png")))
                                                        return setTenant({...tenant, newCover: evt.target.files[0]})
                                                    SharedHelper.fireWarningToast("Achtung", "Bitte nur Bilddateien auswählen.")
                                                }}
                                                accept={"image/png, image/jpeg, image/png"}
                                                style={{width: "0px", display: "none"}}/>
                                            <Button
                                                onClick={() => tenant.newCover ? setTenant({
                                                    ...tenant,
                                                    newCover: null
                                                }) : coverFileInputRef.current.click()}
                                                variant={tenant.newCover ? "danger" : "primary"}
                                                className={"mr-2"}>
                                                {tenant.newCover ? <><i className={"fas fa-times"}/> Logo
                                                    entfernen</> : <> <i className={"fas fa-file-upload"}/> Neues
                                                    Cover auswählen...</>}
                                            </Button>
                                            <div>
                                                {tenant.newCover && <><b>Gewählt:</b> {tenant.newCover?.name}</>}
                                            </div>
                                            <div>
                                                {tenant.newCover && <i>Bitte klicken Sie auf Speichern um die Änderungen zu übernehmen.</i>}
                                            </div>
                                        </>}
                                    </Col>
                                </Row>
                            </DisplayPair>

                        </Col>
                        <Col>
                                <DisplayPair title={"Logo"}>
                                    <Row>
                                        <img src={AjaxHelper.getTenantLogoUrl(tenant?.logo)} alt={"Kein Logo"}
                                             width={200}/>
                                        <Col>
                                            {tenant?.logo && <div className={"mb-2"}>
                                                <EducaLabeledSwitch labelRight={"Logo löschen"}
                                                                    size={"lg"}
                                                                    checked={!!tenant.deleteLogo}
                                                                    onChange={b => setTenant({
                                                                        ...tenant,
                                                                        deleteLogo: b
                                                                    })}/>
                                            </div>}
                                            {tenant.deleteLogo ? null : <>
                                                <input
                                                    type="file"
                                                    ref={fileInputRef}
                                                    onChange={(evt) => {
                                                        if (evt.target.files?.length === 1
                                                            && (evt.target.files[0].name?.toLowerCase()?.includes("jpg")
                                                                || evt.target.files[0].name?.toLowerCase()?.includes("jpeg")
                                                                || evt.target.files[0].name?.toLowerCase()?.includes("png")))
                                                            return setTenant({...tenant, newLogo: evt.target.files[0]})
                                                        SharedHelper.fireWarningToast("Achtung", "Bitte nur Bilddateien auswählen.")
                                                    }}
                                                    accept={"image/png, image/jpeg, image/png"}
                                                    style={{width: "0px", display: "none"}}/>
                                                <Button
                                                    onClick={() => tenant.newLogo ? setTenant({
                                                        ...tenant,
                                                        newLogo: null
                                                    }) : fileInputRef.current.click()}
                                                    variant={tenant.newLogo ? "danger" : "primary"}
                                                    className={"mr-2"}>
                                                    {tenant.newLogo ? <><i className={"fas fa-times"}/> Logo
                                                        entfernen</> : <> <i className={"fas fa-file-upload"}/> Neues
                                                        Logo auswählen...</>}
                                                </Button>
                                                <div>
                                                    {tenant.newLogo && <><b>Gewählt:</b> {tenant.newLogo?.name}</>}
                                                </div>
                                                <div>
                                                    {tenant.newLogo && <i>Bitte klicken Sie auf Speichern um die Änderungen zu übernehmen.</i>}
                                                </div>
                                            </>}
                                        </Col>
                                    </Row>
                                </DisplayPair>
                        </Col>
                    </Row>
                    <hr/>
                    <Row>
                        <Col>
                            <DisplayPair title={"Ladesymbol überschreiben mit dem Logo des Tenants"}>
                                <ReactSwitch checked={!!tenant.overrideLoadingAnimation} onChange={(b) => setTenant({...tenant, overrideLoadingAnimation: b})}/>
                            </DisplayPair>
                        </Col>
                        <Col>
                            <DisplayPair title={"Name zusätzlich zu dem Logo anzeigen"}>
                                <ReactSwitch checked={!!tenant.hideLogoText} onChange={(b) => setTenant({...tenant, hideLogoText: b})}/>
                            </DisplayPair>
                        </Col>
                    </Row>
                    <hr/>
                    <Row>
                        <Col>
                            <DisplayPair title={"Farbe"}>
                                <ChromePicker
                                    disableAlpha={true}
                                    color={tenant.color}
                                    onChange={color => setTenant({...tenant, color : color?.hex})}
                                />
                            </DisplayPair>
                        </Col>
                        <Col>

                        </Col>
                    </Row>
                </Card.Body>
            </Card>

            <Card className={"mt-2"}>
                <Card.Header>
                    <Card.Title>
                        Office 365 Verknüpfung
                    </Card.Title>
                </Card.Header>
                <Card.Body>
                    <Row>
                        <Col>
                            <DisplayPair title={"MS Graph Client ID"}>
                                <FormControl
                                    placeholder={"MS Graph Client ID"}
                                    value={tenant.ms_graph_client_id??""}
                                    onChange={e => setTenant({...tenant, ms_graph_client_id : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                        <Col>
                            <DisplayPair title={"MS Graph Secret ID"}>
                                <FormControl
                                    placeholder={"MS Graph Secret ID"}
                                    value={tenant.ms_graph_secret_id??""}
                                    onChange={e => setTenant({...tenant, ms_graph_secret_id : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                    </Row>
                    <Row>
                        <Col>
                            <DisplayPair title={"MS Graph Tenant ID"}>
                                <FormControl
                                    placeholder={"MS Graph Tenant ID"}
                                    value={tenant.ms_graph_tenant_id??""}
                                    onChange={e => setTenant({...tenant, ms_graph_tenant_id : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                        <Col>
                        </Col>
                    </Row>
                </Card.Body>
            </Card>

            <Card className={"mt-2"}>
                <Card.Header>
                    <Card.Title>
                        KeyCloak / OpenID / OAuth Verknüpfung
                    </Card.Title>
                </Card.Header>
                <Card.Body>
                    <Row>
                        <Col>
                            <DisplayPair title={"Anzeigen als"}>
                                <FormControl
                                    placeholder={"KeyCloak"}
                                    value={tenant.keycloak_display??""}
                                    onChange={e => setTenant({...tenant, keycloak_display : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                        <Col>
                            <DisplayPair title={"Server"}>
                                <FormControl
                                    placeholder={"https://..."}
                                    value={tenant.keycloak_server??""}
                                    onChange={e => setTenant({...tenant, keycloak_server : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                    </Row>
                    <Row>
                        <Col>
                            <DisplayPair title={"Client ID"}>
                                <FormControl
                                    placeholder={"Client ID"}
                                    value={tenant.keycloak_client_id??""}
                                    onChange={e => setTenant({...tenant, keycloak_client_id : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                        <Col>
                            <DisplayPair title={"Client Secret"}>
                                <FormControl
                                    placeholder={"Client Secret"}
                                    value={tenant.keycloak_secret_id??""}
                                    onChange={e => setTenant({...tenant, keycloak_secret_id : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                    </Row>
                    <Row>
                        <Col>
                            <DisplayPair title={"Realm"}>
                                <FormControl
                                    placeholder={"Realm"}
                                    value={tenant.keycloak_realm??""}
                                    onChange={e => setTenant({...tenant, keycloak_realm : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                        <Col>
                        </Col>
                    </Row>
                </Card.Body>
            </Card>

            <Card className={"mt-2"}>
                <Card.Header>
                    <Card.Title>
                        educa AI Verknüpfung
                    </Card.Title>
                </Card.Header>
                <Card.Body>
                    <Row>
                        <Col>
                            <DisplayPair title={"educa AI API Key"}>
                                <FormControl
                                    placeholder={"educa AI API Key"}
                                    value={tenant.openai_key??""}
                                    onChange={e => setTenant({...tenant, openai_key : e.target.value})}
                                />
                            </DisplayPair>
                        </Col>
                    </Row>
                </Card.Body>
            </Card>
            <Card className={"mt-2"}>
                <Card.Header>
                    <Card.Title>
                        Registrierung & Passwort-Wiederherstellung
                    </Card.Title>
                </Card.Header>
                <Card.Body>
                    <Row>
                        <Col>
                            <DisplayPair title={"Andere Nutzer können sich auf der Plattform ohne Zugriffscode registrieren"}>
                                <ReactSwitch checked={!!tenant.allowRegister} onChange={(b) => setTenant({...tenant, allowRegister: b})}/>
                            </DisplayPair>
                        </Col>
                        <Col>
                            <DisplayPair title={"Standard-Rolle nach der Registrierung"}>
                                <Select
                                    placeholder={"Bitte auswählen"}
                                    getOptionLabel ={(option)=> option.name}
                                    getOptionValue ={(option)=> option.id}
                                    value={roles.find(role => role.id == tenant.roleRegister)}
                                    isClearable
                                    options={roles}
                                    onChange={(val) => setTenant({...tenant, roleRegister : val?.id})}
                                />
                            </DisplayPair>
                        </Col>
                        <Col>
                            <DisplayPair title={"Möglichkeit zur Passwordwiederherstellung auf der Startseite anzeigen"}>
                                <ReactSwitch checked={!!tenant.allowPasswordReset} onChange={(b) => setTenant({...tenant, allowPasswordReset: b})}/>
                            </DisplayPair>
                        </Col>
                    </Row>
                </Card.Body>
            </Card>
            <Card className={"mt-2"}>
                <Card.Header>
                    <Card.Title>
                        Erkunden
                    </Card.Title>
                </Card.Header>
                <Card.Body>
                    <Row>
                        <Col>
                            <DisplayPair title={"Unter \"Erkunden\" für andere Plattformen anzeigen"}>
                                <ReactSwitch checked={!!tenant.isVisibleForOther} onChange={(b) => setTenant({...tenant, isVisibleForOther: b})}/>
                            </DisplayPair>
                        </Col>
                        <Col>

                        </Col>
                    </Row>
                </Card.Body>
            </Card>
            <Button onClick={save} className={"mt-2"}>
                <i className={"fas fa-save"}/> Speichern
            </Button>
        </div>
    </>


    return (
        <Sidebar
            sidebar={content}
            onSetOpen={onClose}
            open={open}
            defaultOpen={false}
            styles={{...DRAWER_DEFAULT_STYLES, sidebar: {...DRAWER_DEFAULT_STYLES.sidebar, width: "80vw"}}}
        ><></>
        </Sidebar>
    );
}

export default TenantEditDrawer;
