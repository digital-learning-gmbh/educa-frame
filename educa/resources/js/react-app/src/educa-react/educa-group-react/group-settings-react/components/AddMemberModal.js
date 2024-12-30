import React, {useEffect, useRef, useState} from "react";
import Modal from "react-bootstrap/Modal";
import Button from "react-bootstrap/Button";
import {InputGroup} from "react-bootstrap";
import {CloudIdSelectMultiple} from "../../../../shared/shared-components/EducaSelects.js";

function AddMemberModal({show, close, group, addUsers}) {

    let [addUserList, setAddUserList] = useState([])

    useEffect(() => {
        if(show)
        {
            setAddUserList([])
        }
    }, [show])

    return <Modal show={show} onHide={close}>
        <Modal.Header closeButton>
            <Modal.Title>Mitglieder zur Gruppe hinzufügen</Modal.Title>
        </Modal.Header>
        <Modal.Body>
                <p className={"mr-2 pt-2"} style={{ minWidth: "120px" }}>
                    Mitglieder hinzufügen
                </p>
                <div style={{ maxWidth: " 500px", minWidth: "350px" }}>
                    <CloudIdSelectMultiple
                        exclude={group.members}
                        placeholder={"Nutzer auswählen"}
                        value={addUserList}
                        cloudUserListChangedCallback={users => {
                            setAddUserList(users)
                        }}
                    />
                </div>
        </Modal.Body>
        <Modal.Footer>
            <Button variant="secondary" onClick={close}>
                Abbrechen
            </Button>
            <Button variant="primary" onClick={() => addUsers(addUserList)}>
                Mitglieder zur Gruppe hinzufügen
            </Button>
        </Modal.Footer>
    </Modal>
}

export default AddMemberModal;
