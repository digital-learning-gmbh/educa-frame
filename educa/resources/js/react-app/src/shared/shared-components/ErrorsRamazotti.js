import React, {useEffect, useState} from "react";
import {Card, Collapse} from "react-bootstrap";
import Button from "react-bootstrap/Button";
import SyntaxHighlighter from "react-syntax-highlighter/dist/cjs/light";
import dark from "react-syntax-highlighter/dist/cjs/styles/hljs/dark";

import error_serious from "./images/error_serious.gif"
import error_easy from "./images/error_easy.gif"


export const ERROR_MODE =
    {
        EASY : "easy",
        SERIOUS : "serious"
    }
const ErrorsRamazotti = (props) =>
{
    let [open, setOpen] = useState(false)
    let [clickCount, setClickCount] = useState(0)

    useEffect( () =>
    {
        setOpen(false)
        setClickCount(0)
    },[props.info])

    useEffect( () =>
    {
        if(clickCount >= 5 )
            setOpen(true)
    }, [clickCount])

    const serious = () =>
    {
        return <div style={{width : "100%", height : "100%"}}>
            <div style={{display :"flex", flex : 1, justifyContent :"center"}}>
                <Card style={{maxWidth: "700px"}} className={"mt-5"}>
                    <Card.Img  onClick={() => setClickCount(clickCount+1)} variant="top" src={error_serious} />
                    <Card.Body style={{textAlign :"center"}}>
                        <Card.Title>Es ist ein Fehler aufgetreten ...</Card.Title>
                        <Card.Text>Versuche die Seite erneut zu laden. Sollte das Problem weiter bestehen, so kontaktiere bitte den Systemadministrator.</Card.Text>

                        <Button variant="primary" onClick={() => window.location.reload()}>Seite neu laden</Button>
                        <Collapse in={open} unmountOnExit={true} style={{textAlign: "left"}}>
                            <div>
                                <SyntaxHighlighter language="javascript" style={dark}>
                                    {props.info?.componentStack}
                                </SyntaxHighlighter>
                            </div>
                        </Collapse></Card.Body>
                </Card>
            </div>
        </div>
    }

    const easy = () =>
    {
        return <div style={{width : "100%", height : "100%"}}>
            <div style={{display :"flex", flex : 1, justifyContent :"center"}}>
                <Card style={{maxWidth: "700px"}} className={"mt-5"}>
                    <Card.Img  onClick={() => setClickCount(clickCount+1)} variant="top" src={error_easy} />
                    <Card.Body style={{textAlign :"center"}}>
                        <Card.Title>Upsi...</Card.Title>
                        <Card.Text>Hier ist wohl etwas schief gelaufen...</Card.Text>

                        <Button variant="primary" onClick={() => window.location.reload()}>Seite neuladen</Button>
                        <Collapse in={open} unmountOnExit={true} style={{textAlign: "left"}}>
                            <div>
                                <SyntaxHighlighter language="javascript" style={dark}>
                                    {props.info?.componentStack}
                                </SyntaxHighlighter>
                            </div>
                        </Collapse></Card.Body>
                </Card>
            </div>
        </div>
    }

    return props.errormode === ERROR_MODE.SERIOUS? serious() : easy()
}

export default ErrorsRamazotti
