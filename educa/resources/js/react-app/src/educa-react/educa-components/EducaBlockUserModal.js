import React, { useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import { useSelector } from 'react-redux';
import AjaxHelper from '../helpers/EducaAjaxHelper';
import EducaHelper from '../helpers/EducaHelper';

function EducaBlockUserModal(props) {

    if(props.user == null)
        return <></>

    let [success, setSuccess] = useState(false);

    let cloud_user = useSelector(s => s.currentCloudUser)

    let handleBlock = () => {
        AjaxHelper.blockUser(props.user?.id)
        .then(resp => {
            if (resp.payload) {
                setSuccess(true)
                window.location.reload();
            } else
                throw new Error("Fehler. Diese Aktion ist zur Zeit nicht möglich.")
        })
        .catch(err => {
            EducaHelper.fireErrorToast("Fehler", err.message)
        })
    }

    return  <Modal show={props.show} onHide={() => props.hideCallback()}>
    <Modal.Header closeButton>
      <Modal.Title><i className="fas fa-ban fa-fw"></i> Nutzer '{props.user?.name}' blockieren</Modal.Title>
    </Modal.Header>
    { success ?
    <Modal.Body>Erfolg! Wir werden den Nutzer dir nicht mehr anzeigen.</Modal.Body> :
    <Modal.Body>Es tut uns leid, dass du schlechte Erfahrung gemacht hast. Du kannst diesen Nutzer blockieren, sodass keine Inhalte mehr von '{props.user?.name}' angezeigt werden.</Modal.Body>
    }
    <Modal.Footer>
      <Button variant="secondary" onClick={() => props.hideCallback()}>
        Abbrechen
      </Button>
      <Button variant="primary" onClick={handleBlock}>
        Nutzer blockieren
      </Button>
    </Modal.Footer>
  </Modal>
}

export default EducaBlockUserModal;
