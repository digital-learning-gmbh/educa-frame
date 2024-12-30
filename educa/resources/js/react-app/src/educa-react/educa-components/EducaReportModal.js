import React, { useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import { useSelector } from 'react-redux';
import ReactTextareaAutosize from 'react-textarea-autosize';
import AjaxHelper from '../helpers/EducaAjaxHelper';
import EducaHelper, { LIMITS } from '../helpers/EducaHelper';

function EducaReportModal(props) {

    if(props.content_type == null || props.content == null)
        return <></>

    let [success, setSuccess] = useState(false);
    let [comment, setComment] = useState(null);

    let cloud_user = useSelector(s => s.currentCloudUser)

    let handleBlock = () => {
        AjaxHelper.reportContent(props.content_type, props.content)
        .then(resp => {
            if (resp.payload) {
                setSuccess(true)
            } else
                throw new Error("Fehler. Diese Aktion ist zur Zeit nicht möglich.")
        })
        .catch(err => {
            EducaHelper.fireErrorToast("Fehler", err.message)
        })
    }

    return  <Modal show={props.show} onHide={() => props.hideCallback()}>
    <Modal.Header closeButton>
      <Modal.Title><i className="far fa-flag fa-fw"></i> Inhalt als Spam melden</Modal.Title>
    </Modal.Header>
    { success ?
    <Modal.Body>Viele Dank, wir werden den Inhalt prüfen und gegenfalls entfernen.</Modal.Body> :
    <Modal.Body>
        <p>Es tut uns leid, dass du schlechte Erfahrung gemacht hast. Du kannst diesen Inhalt uns melden. Wir prüfen dann, ob dieser Inhalt gegen unsere Richtlinien verstößt.</p>
        <label>Warum ist dieser Inhalt Spam? (Pflichtfeld)</label>
        <ReactTextareaAutosize
                        maxLength={LIMITS.COMMENT_LIMIT}
                        maxRows={6}
                        minRows={3}
                        value={comment}
                        className="form-control editor"
                        placeholder="Warum denkst du, dass der Inhalt Spam oder gegen unsere Richtlinien verstößt?"
                        onChange={(evt) => {
                            setComment(evt.target.value)
                            if(evt.target.value.length > LIMITS.COMMENT_LIMIT - 1)
                            {
                                EducaHelper.fireWarningToast("Hinweis","Das Zeichenlimit für Kommentare bei "+ LIMITS.COMMENT_LIMIT + " Zeichen");
                            }
                        }}
                    ></ReactTextareaAutosize>
    </Modal.Body>
    }
     { success ?
    <Modal.Footer>
      <Button variant="secondary" onClick={() => props.hideCallback()}>
        Schließen
      </Button>
    </Modal.Footer>
    :
    <Modal.Footer>
    <Button variant="secondary" onClick={() => props.hideCallback()}>
      Abbrechen
    </Button>
    <Button variant="primary" onClick={handleBlock}>
      Inhalt melden
    </Button>
  </Modal.Footer>
    }
  </Modal>
}

export default EducaReportModal;
