import React, {useEffect, useState} from "react";
import {Alert, Button, Card, ProgressBar} from "react-bootstrap";
import Form from "react-bootstrap/Form";
import EducaAjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";

export default function EmailContactPage(props)
{
    let [isLoading, setIsLoading] = useState(false);
    let [uploadProgress, setUploadProgress] = useState(0);
    let [sendSuccess, setSendSucess] = useState(false);

    let [subject, setSubject] = useState("");
    let [message, setMessage] = useState("");

    let [file1, setFile1] = useState(null);
    let [file2, setFile2] = useState(null);
    let [file3, setFile3] = useState(null);

    let sendMail = () => {
        if(subject == "" || message == "")
        {
            EducaHelper.fireErrorToast("Fehler","Bitte gib sowohl einen Betreff als auch eine Nachricht ein");
            return;
        }

        setIsLoading(true)
        setUploadProgress(0)
        EducaAjaxHelper.sendMail(
            props.contact.email,
            props.contact.name,
            props.contact.isMailAnonymized,
            subject,
            message,
            file1,
            file2,
            file3,
            progressEvent => {
                let uploadProgress = Math.round(
                    (progressEvent.loaded * 100) / progressEvent.total
                );
                setUploadProgress(uploadProgress);
            }
        ).then(resp => {
            if (resp.status > 0) {
                setSubject("")
                setMessage("")
                setFile1(null)
                setFile2(null)
                setFile3(null)
                setSendSucess(true)
                return;
            } else
                throw new Error("")
        }).catch(err => {
            console.log(err)
            EducaHelper.fireErrorToast("Fehler", "Die Nachricht konnte nicht verschickt werden.")
        })
            .finally(() => {
                setIsLoading(false)
            })
    }

    if(sendSuccess)
        return  <Card  className={"mt-3"}>
            <Card.Body>
                <h6><b>Kontakt über E-Mail</b></h6>
                <p>Deine Nachricht wurde erfolgreich versendet.</p>
            </Card.Body>
        </Card>

    return <Card  className={"mt-3"}>
        <Card.Body>
            { isLoading ? <div>
                <p>Versende Nachricht...</p>
                    <ProgressBar
                        now={uploadProgress}
                        label={`${uploadProgress}%`}
                    />
                </div> :
            <div>
                <h6><b>Kontakt über E-Mail</b></h6>
                <p>Versenden Sie eine E-Mail an den Kontakt, in dem Sie das untenstehende Formular ausfüllen.</p>
                { props.contact.isMailAnonymized ? <Alert variant={"warning"}><b>Anonyme Zustellung:</b> Deine E-Mail wird anonym zugestellt, s.d. der Empfänger den Absender der E-Mail nicht sehen kann</Alert> : null }
                <Form.Group controlId="exampleForm.ControlInput1" required={true}>
                    <Form.Label>Betreff</Form.Label>
                    <Form.Control type="text" required={true} placeholder="Betreff der Nachricht..."  isInvalid={subject == ""}  onChange={(evt) => setSubject(evt.target.value)}  />
                </Form.Group>
                <Form.Group controlId="exampleForm.ControlTextarea1" required={true}>
                    <Form.Label>Nachricht</Form.Label>
                    <Form.Control as="textarea" rows={6} placeholder={"Deine Nachricht..."} isInvalid={message == ""} onChange={(evt) => setMessage(evt.target.value)} />
                </Form.Group>
                <Form.Group className="mb-1" controlId="formBasicEmail">
                    <Form.Label>Dokument 1</Form.Label>
                    <Form.File
                        id="custom-file"
                        label={ file1 ? "Datei ausgewählt" : "Bitte wählen.."}
                        custom
                        onChange={event => {
                            setFile1(event.target.files[0]);
                        }}
                    />
                </Form.Group>
                <Form.Group className="mb-1" controlId="formBasicEmail">
                    <Form.Label>Dokument 2</Form.Label>
                    <Form.File
                        id="custom-file2"
                        label={ file2 ? "Datei ausgewählt" : "Bitte wählen.."}
                        custom
                        onChange={event => {
                            setFile2(event.target.files[0]);
                        }}
                    />
                </Form.Group>
                <Form.Group className="mb-1" controlId="formBasicEmail">
                    <Form.Label>Dokument 3</Form.Label>
                    <Form.File
                        id="custom-file3"
                        label={ file3 ? "Datei ausgewählt" : "Bitte wählen.."}
                        custom
                        onChange={event => {
                            setFile3(event.target.files[0]);
                        }}
                    />
                </Form.Group>
                <div className="float-right mt-1">
                    <Button variant="primary" onClick={() => {
                        sendMail()
                    }}><i className="fas fa-envelope"></i> E-Mail versenden</Button>
                </div>
            </div> }
        </Card.Body>
    </Card>
}
