import {Modal} from "react-bootstrap";
import Select from "react-select";
import React, {useEffect, useState} from "react";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import Button from "react-bootstrap/Button";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";


function EducaChangeRolesModal ({show, tenant, userIdsAndAction, onClose}) {

    const [isLoading, setIsLoading] = useState(true)
    const [roleOptions, setRoleOptions] = useState([])
    const [selectedRoles, setSelectedRoles] = useState([])

    useEffect(() => {
        if (show) {
            AjaxHelper.getSystemSettingsTenant(tenant.id)
                .then(resp => {
                    if (resp.payload?.roles) {
                        let rolesToShow = resp.payload?.roles.filter((r) => r.scope_id != "-1")
                        //setSelectedRoles([])
                        setRoleOptions(rolesToShow)
                        setIsLoading(false)
                    }
                })
                .catch(err => {
                    SharedHelper.fireErrorToast(
                        "Fehler",
                        "Die Rollen konnten nicht geladen werden. " + err.message
                    )
                })

        }
        else setSelectedRoles([])
    }, [show, tenant]);

    function onSaveClicked() {
        console.log("sending roles: ", selectedRoles)
        AjaxHelper.updateSystemSettingsMultipleUsersRoles(userIdsAndAction.userIds, tenant.id, selectedRoles.map(r => r.id), userIdsAndAction.add)
            .then((resp) => {
                if (resp.status > 0) {
                    let message = userIdsAndAction.add ? "hinzugefügt." : "entfernt."
                    SharedHelper.fireSuccessToast("Erfolg", "Rollen wurden erfolgreich " + message)
                    setSelectedRoles([])
                    onClose()
                    return
                }
                throw new Error("")
            })
            .catch((err) => {
                let message = userIdsAndAction.add ? "hinzugefügt werden. " : "entfernt werden. "
                SharedHelper.fireErrorToast( "Fehler", "Rollen konnten nicht " + message + err.message)
            })
    }

    return <Modal show={show}
                  onHide={onClose}
    >
        <Modal.Header>
            <Modal.Title>
                {!!userIdsAndAction.add ? "Rollen hinzufügen" : "Rollen entfernen"}
            </Modal.Title>
        </Modal.Header>
        <Modal.Body>
            {isLoading ?
                null
                :
                <Select
                    isMulti
                    getOptionLabel ={(option)=>option.name}
                    getOptionValue ={(option)=>option.id}
                    placeholder={"Wähle Rolle(n) aus"}
                    noOptionsMessage={() => "Keine Rollen verfügbar"}
                    options={roleOptions}
                    value={selectedRoles}
                    onChange={(selection) => setSelectedRoles(selection)}
                />
            }
        </Modal.Body>
        <Modal.Footer>
            <Button
                variant={"primary"}
                onClick={() => onClose()}
            >
                Abbrechen
            </Button>
            <Button
                disabled={!selectedRoles || !selectedRoles.length > 0}
                variant={"danger"}
                onClick={() => onSaveClicked()}
            >
                Speichern
            </Button>
        </Modal.Footer>

    </Modal>

}

export default EducaChangeRolesModal
