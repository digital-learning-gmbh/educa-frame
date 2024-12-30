import React, {useEffect, useState} from 'react';
import AjaxHelper from '../helpers/EducaAjaxHelper';
import "./styles.css"
import { Button, Modal } from 'react-bootstrap';
import { EducaLoading } from '../../shared-local/Loading';
import SharedHelper from '../../shared/shared-helpers/SharedHelper';

export function EducaAccessCodeModal(props) {

    let [codeValidationError, setCodeValidationError] = useState(false);
    let [code, setCode] = useState();
    let [isLoading, setIsLoading] = useState(false);

    let checkCode = () => {
        setIsLoading(true)
        AjaxHelper.checkCodeInApp(code)
            .then(resp => {
                if (resp.payload?.code) {
                    window.location.reload();
                    return
                }
                throw new Error(resp.payload?.message)
            })
            .catch(err => {
                setCodeValidationError(true)
                SharedHelper.fireErrorToast(
                    "Fehler",
                    "Der Zugangscode konnte nicht eingelöst werden. " + err.message
                );
            })
            .finally(() => setIsLoading(false))
    }

    if(!props.show)
     return <></>

    return (
        <Modal
        show={props.show}
        backdrop={"static"}
        onHide={() => {props.close()}}
        >
  <Modal.Header>
    <Modal.Title>Zugangscode</Modal.Title>
  </Modal.Header>

  <Modal.Body>
    { isLoading ? <EducaLoading/> :  <div>
                <h1 className="h5 mb-3 font-weight-normal">Bitte gebe deinen Zugangscode ein, um Funktionen in educa freizuschalten.</h1>


                <div style={{width: "100%", textAlign: "center"}} className="mb-2">
                    <input
                        onChange={(evt) => setCode(evt.target.value)} maxLength='6' name="digits" type="text" id="inputEmail" value={code}
                        className="digitInput" required="" autoFocus=""/>
                    { codeValidationError ?
                        <small id="emailHelp" className="form-text text-danger">Bitte überprüfe den Zugangscode</small>: <></>}
                </div>
                </div>
}
  </Modal.Body>

  <Modal.Footer>
    <Button variant="secondary" onClick={() => props.close()}>Abbrechen</Button>
    <Button variant="primary" onClick={() => checkCode()}>Zugangscode einlösen</Button>
  </Modal.Footer>
</Modal>
    )
}
