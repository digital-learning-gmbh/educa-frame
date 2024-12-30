import React, {useEffect, useRef, useState} from 'react';
import Modal from "react-bootstrap/Modal";
import {MODAL_BUTTONS} from "../../../../shared/shared-components/EducaModal.js";
import {DisplayPair} from "../../../../shared/shared-components/Inputs.js";
import {FormControl} from "react-bootstrap";
import Button from "react-bootstrap/Button";
import AjaxHelper from "../../../helpers/EducaAjaxHelper.js";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper.js";

function CreateFolder({show, onHide, reloadCallback, modelId, modelType, parentId}) {

    let [newName, setNewName] = useState(null);

    let createFolder = () => {
        if(newName == null || newName.length < 2)
        {
            SharedHelper.fireErrorToast("Fehler", "Der Ordnername ist zu kurz, bitte min. 2 Zeichen angeben")
            return;
        }

        AjaxHelper.createDocumentFolder(modelId, modelType, parentId, newName)
            .then(resp => {
                if (resp.status > 0) {
                    SharedHelper.fireSuccessToast("Erfolg", "Der Ordner wurde erstellt.")
                    reloadCallback()
                    onHide()
                    return
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                SharedHelper.fireErrorToast("Fehler", "Der Ordner konnte nicht erstellt werden. " + err.message)
            })
    }

    return <Modal
        show={show}
        onHide={onHide}
        backdrop="static"
        keyboard={false}
    >
        <Modal.Header closeButton>
            <Modal.Title>Neuen Ordner erstellen</Modal.Title>
        </Modal.Header>
        <Modal.Body>
            <div><p>Wie soll der neue Ordner benannt werden?</p>
                <DisplayPair title={"Ordnername"}>
                    <FormControl type={"text"} placeholder={"Name des neuen Ordners"} value={newName} onChange={(evt) => setNewName(evt.target.value)} />
                </DisplayPair>
            </div>
        </Modal.Body>
        <Modal.Footer>
            <Button variant="secondary" onClick={onHide}>
                Abbrechen
            </Button>
            <Button variant="primary" onClick={createFolder}>Ordner erstellen</Button>
        </Modal.Footer>
    </Modal>
}


export default CreateFolder;
