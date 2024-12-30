import React, {useContext, useEffect, useState} from 'react';
import {Alert, Card, FormControl, Spinner} from "react-bootstrap";
import {useDispatch, useSelector} from "react-redux";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import "./table-styles.css"
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import Button from "react-bootstrap/Button";
import Modal from "react-bootstrap/Modal";
import {DisplayPair} from "../../../../shared/shared-components/Inputs";
import {SYSTEM_SETTINGS_SET_ROLES} from "../../../reducers/GeneralReducer";
import {MODAL_BUTTONS} from "../../../../shared/shared-components/EducaModal";
import {ModalContext} from "../../EducaSystemSettingsRoot";
function SystemSettingsRoles({tenant, returnRawContent}) {

    const rolesStore = useSelector(s => s.roles)
    const thisTenant = useSelector(s => s.tenant)

    const [actions, setActions] = useState([])
    const [roleAddModalOpen, setRoleAddModalOpen] = useState(false)
    const [selectedRole, setSelectedRole] = useState(null)
    let [isLoading, setIsLoading] = useState(false);

    const [permissions, setPermissions] = useState([])
    const [roles, setRoles] = useState([])

    const dispatch = useDispatch()
    const setStoreRoles = (roles) => dispatch({type : SYSTEM_SETTINGS_SET_ROLES, payload : roles})

    useEffect(() => {
        loadRoles()
    },[])

    useEffect(() => {
        loadPermissions()
    },[])

    const loadRoles = () =>
    {
        setIsLoading(true)
        AjaxHelper.loadSystemSettingsRoles(tenant.id)
            .then( resp =>
            {
                if(resp.payload.roles) {
                    if (thisTenant?.id == tenant?.id)
                        setStoreRoles(resp.payload.roles)
                    return setRoles(resp.payload.roles)
                }

                throw new Error()
            })
            .finally(() => setIsLoading(false))
    }

    const loadPermissions = () =>
    {
        setIsLoading(true)
        AjaxHelper.loadSystemSettingsPermissions(tenant?.id)
            .then( resp => {
                if(resp.payload.permissions)
                    return setPermissions(resp.payload.permissions)
            })
            .finally(() => setIsLoading(false))
    }


    const roleHasPermission = (role,permission) =>
    {
       return !!role.permissions?.find( rolePermission => rolePermission.id == permission.id)
    }
    const checkBoxClicked = (role, permission) =>
    {
        setActions([...actions, ...[{role, permission}] ])
        flip(role, permission)
    }

    const flip = (role, permission) =>
    {
        AjaxHelper.flipSystemSettingsPermissionOfRole(role.id, permission.id, tenant.id)
            .then( resp => {
                if(resp.payload.roles) {
                    if (thisTenant?.id == tenant?.id)
                        setStoreRoles(resp.payload.roles)
                    return setRoles(resp.payload.roles)
                }

                throw new Error()
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Rolle konnte nicht angepasst werden."))
    }

    const getContent = () => {
        return <>
            <Alert variant={"info"}>
                <i className={"fas fa-info-circle"}/> Um eine Rolle zu bearbeiten oder zu löschen klicken Sie auf die Rolle im Tabellenkopf.
            </Alert>
            <div className={"mb-2 d-flex flex-grow-1"}>
                <Button
                    disabled={!actions.length}
                    onClick={() => {
                        let acts = _.cloneDeep(actions)
                        const ele = acts.splice(actions.length-1, 1)[0]
                        setActions(acts)
                        flip(ele.role, ele.permission)
                    }
                    }
                    className={"mr-1"}>
                    <i className={"fas fa-undo"}/> Rückgängig {actions.length? "("+actions.length+")" : ""}
                </Button>
                <div className={"d-flex flex-grow-1 justify-content-end"}>
                    <Button
                        onClick={() => setRoleAddModalOpen(true)}
                    >
                        <i className={"fas fa-plus"}/> Rolle hinzufügen
                    </Button>
                </div>
            </div>

            <div className={returnRawContent? "wrapper2" : "wrapper"}>
                <table id="table_id" className="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th className="sticky-col first-col" style={{zIndex : 10}}>Recht</th>
                        {roles?.map( role => {
                            return <th
                                style={{cursor  :"pointer"}}
                                onClick={() => setSelectedRole(role)}>
                                {role.name}
                            </th>
                        })
                        }
                    </tr>
                    </thead>
                    <tbody>
                    {permissions.map( p => {
                        return    <tr>
                            <td className="sticky-col first-col">{p.name}</td>
                            {roles.map(r => {
                                return  <td>
                                    <input name="permission_{{ $role->id }}_{{ $right->id }}"
                                           type="checkbox"
                                           onChange={() => {/*disable react error*/}}
                                           onClick={() => checkBoxClicked(r, p)}
                                           checked={roleHasPermission(r,p)}
                                    />
                                </td>
                            })}
                        </tr>
                    })}
                    </tbody>
                </table>
            </div>
            <AddRoleModal tenantId={tenant.id} open={roleAddModalOpen} onClose={() => setRoleAddModalOpen(false)} reload={() => loadRoles()}/>
            <EditRoleModal tenantId={tenant.id} role={selectedRole} onClose={() => setSelectedRole(null)} reload={() => loadRoles()}/>
        </>
    }
    if(returnRawContent)
        return getContent()

    return (
        <Card style={{backgroundColor: "white"}}>
            <Card.Header>
                <div style={{flex : 1, display : "flex", flexDirection :"row"}}>
                    <h5 className="card-title">
                        <b><i className="fas fa-pencil-alt"></i> Rechte und Rollen</b>
                        {isLoading && <Spinner className={"ml-2 align-self-center"} animation={"grow"}/>}
                    </h5>
                </div>
                <div className={"text-muted"}>Übersichtsliste aller Rollen und der jeweiligen Rechte</div>
            </Card.Header>
            <Card.Body>
                {getContent()}
            </Card.Body>
        </Card>
    );
}

export default SystemSettingsRoles;

const AddRoleModal = ({tenantId, open, onClose, reload}) =>
{

    const [name, setName] = useState("")

    useEffect(() => {
        setName("")
    },[open])

    const save = () =>
    {
        AjaxHelper.createSystemSettingsRole(tenantId, name)
            .then( resp => {
                if(resp.status > 0){
                    SharedHelper.fireSuccessToast("Erfolg", "Die Rolle wurde erfolgreich erstellt.")
                    reload()
                    return onClose()
                }
                throw new Error("")
            })
            .catch( () => SharedHelper.fireErrorToast("Fehler", "Die Rolle konnte nicht erstellt werden."))
    }

    return <Modal show={open}
                  onHide={() => onClose()}>
        <Modal.Header>
            <Modal.Title>
                Rolle Hinzufügen
            </Modal.Title>
        </Modal.Header>
        <Modal.Body>
        <DisplayPair title={"Name"}>
            <FormControl
                value={name}
                onChange={e => setName(e.target.value)}
            />
        </DisplayPair>


        </Modal.Body>
        <Modal.Footer>
            <Button onClick={() => save()}>
                <i className={"fas fa-save"}/> Erstellen
            </Button>
            <Button
                onClick={() => onClose()}
                className={"ml-1"}
                variant={"secondary"}
            >
                <i className={"fas fa-times"}/> Abbrechen
            </Button>
        </Modal.Footer>

    </Modal>
}

const EditRoleModal = ({tenantId, role, onClose, reload}) =>
{
    const [name, setName] = useState("")

    const {educaModalRef} = useContext(ModalContext)

    useEffect(() => {
        setName(role?.name)
    },[role])

    const save = () =>
    {
        AjaxHelper.editSystemSettingsRole(tenantId, role.id, name)
            .then( resp => {
                if(resp.status > 0){
                    SharedHelper.fireSuccessToast("Erfolg", "Die Rolle wurde erfolgreich gespeichert.")
                    reload()
                    return onClose()
                }
                throw new Error("")
            })
            .catch( () => SharedHelper.fireErrorToast("Fehler", "Die Rolle konnte nicht gespeichert werden."))
    }

    const deleteRole = () => {

        const exec = () => {
            AjaxHelper.deleteSystemSettingsRole(tenantId, role.id)
                .then( resp => {
                    if(resp.status > 0){
                        SharedHelper.fireSuccessToast("Erfolg", "Die Rolle wurde erfolgreich gelöscht.")
                        reload()
                        return onClose()
                    }
                    throw new Error("")
                })
                .catch( () => SharedHelper.fireErrorToast("Fehler", "Die Rolle konnte nicht gelöscht werden."))
        }

        educaModalRef.current.open( b => b == MODAL_BUTTONS.YES? exec() : null,
            "Rolle löschen",
            "Möchten Sie die Rolle wirklich löschen?",
            [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
            )

    }

    return <Modal show={!!role}
                  onHide={() => onClose()}>
        <Modal.Header>
            <Modal.Title>
                Rolle Ändern
            </Modal.Title>
        </Modal.Header>
        <Modal.Body>
            <DisplayPair title={"Name"}>
                <FormControl
                    value={name}
                    onChange={e => setName(e.target.value)}
                />
            </DisplayPair>


        </Modal.Body>
        <Modal.Footer>
            <Button onClick={() => save()}>
                <i className={"fas fa-save"}/> Speichern
            </Button>

            <Button
                className={"ml-1"}
                variant={"danger"}
                onClick={() => deleteRole()}>
                <i className={"fas fa-trash"}/> Löschen
            </Button>

            <Button
                onClick={() => onClose()}
                className={"ml-1"}
                variant={"secondary"}
            >
                <i className={"fas fa-times"}/> Abbrechen
            </Button>
        </Modal.Footer>

    </Modal>
}
