import {Form} from "react-bootstrap";
import React, {useEffect, useRef, useState} from "react";
import Button from "react-bootstrap/Button";

function ChatDialog () {

    let [conversation, setConversation] = useState(null)
    let [message, setMessage] = useState(null)


    useEffect(() => {

    }, []);

    return <>
        <div>
            <Form.Group controlId="exampleForm.ControlTextarea1">
                <Form.Label>Du kannst educa AI zu allen Themen etwas Fragen oder mich bitten Dinge zu erledigen, z.B. kann ich dir eine Struktur für einen Aufsatz geben oder Tipps für ein effizienteres Lernen</Form.Label>
                <Form.Control placeholder={"Schreib hier eine Nachricht..."} as="textarea" rows={3} value={message} onChange={(evt) => setMessage(evt.target.value)} />
            </Form.Group>
            <Button variant={"primary"}>Senden <i className="fas fa-paper-plane"></i></Button>
        </div>
    </>
}

export default ChatDialog;
