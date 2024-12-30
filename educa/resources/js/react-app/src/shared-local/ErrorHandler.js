import React, {useEffect, useState} from "react";
import {Card, Collapse} from "react-bootstrap";
import Button from "react-bootstrap/Button";
import SyntaxHighlighter from "react-syntax-highlighter/dist/cjs/light";
import dark from "react-syntax-highlighter/dist/cjs/styles/hljs/dark";


export const ERROR_MODE =
    {
        EASY: "easy",
        SERIOUS: "serious"
    }
const ErrorHandler = (props) => {
    let [open, setOpen] = useState(false)
    let [clickCount, setClickCount] = useState(0)

    useEffect(() => {
        setOpen(false)
        setClickCount(0)
    }, [props.info])

    useEffect(() => {
        if (clickCount >= 5)
            setOpen(true)
    }, [clickCount])

    const serious = () => {
        return <div className={"container"}><Card className={"mt-5"}>
            <Card.Body>
                <Card.Title><i className="fas fa-bolt"></i> Es ist leider ein Fehler aufgetreten ...</Card.Title>
                <Card.Text>Versuche die Seite erneut zu laden. Sollte das Problem weiter bestehen, so kontaktiere bitte
                    den Systemadministrator. Bitte sende dazu den folgenden Fehler-Code mit einem Screenshot an dein Support-Team.</Card.Text>

                <div className={"mb-3"}>
                    <Button variant="primary" className={"m-1"} onClick={() => window.location.href = "/"}>Zur Startseite</Button>
                    <Button variant="outline-secondary" className={"m-1"}  onClick={() => window.location.reload()}>Seite erneut laden</Button>
                </div>
                <Collapse in={true} unmountOnExit={true} style={{textAlign: "left"}}>
                    <div>
                        <SyntaxHighlighter language="javascript" style={dark}>
                            {props.info?.componentStack}
                        </SyntaxHighlighter>
                    </div>
                </Collapse></Card.Body>
        </Card></div>
    }


    return serious()
}

export default ErrorHandler
