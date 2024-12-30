import {Badge, Card, Col, ListGroup} from "react-bootstrap";
import Accordion from "react-bootstrap/Accordion";
import Button from "react-bootstrap/Button";
import React, {useRef} from "react";
import EducaModal from "../../../../shared/shared-components/EducaModal";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import PdfViewer from "../../../../shared/shared-components/PdfViewer";


function AIContent({query, aiResponse}) {

    const modalRef = useRef()
    const pdfModalRef = useRef()

    let handlePreviewViewer = (file) => {
        let mediumUrl = AjaxHelper.downloadDocumentUrl(file.id, file.access_hash)
        if(["jpg", "jpeg", "png"].includes(file.file_type))
        {
            modalRef?.current.open(() => {}, "Datei: "+file.name,
                <>
                    <div style={{display :"flex", flex : 1, flexDirection :"row", justifyContent :"center"}}>
                        <img style={{maxWidth : "100%", maxHeight :"70vh"}} src={mediumUrl} />
                    </div>
                    <Button onClick={() =>download() }>Download</Button>
                </>
                ,[]
            )
        }
        else if(["mp4","webm"].includes(file.file_type))
        {
            modalRef?.current.open(() => {}, "Datei: "+file.name,
                <>
                    <div style={{display :"flex", flex : 1, flexDirection :"row", justifyContent :"center"}}>
                        <video controls style={{maxWidth:"100%"}}>
                            <source src={mediumUrl} />
                        </video>
                    </div>
                    <Button onClick={() =>download() }>Download</Button>
                </>
                ,[]
            )
        }
        else if(["pdf"].includes(file.file_type))
        {
            pdfModalRef?.current.open(() => {}, "Datei: "+file.name,
                <>
                    <div>
                        <PdfViewer url={mediumUrl}/>
                    </div>
                    <Button onClick={() =>download() }>Download</Button>
                </>
                ,[]
            )
        }
    }

    return <div className={"row"}>
        <Col>
            <Card className={"mt-2"}>
                <Card.Body>
                    <h4><b>educa AI</b></h4>
                    <p><b>Frage:</b> {query}</p>
                    {aiResponse == null ? <div className={"text-center"}><img style={{width: "25px", height: "25px"}}
                                                                              src={"/images/educa_ai_loading_indicator.gif"}/>
                    </div> : <></>}
                </Card.Body>
            </Card>
            {aiResponse == null ? <></> : <>
                <Card className={"mt-2"}>
                    <Card.Body>
                        <p><b>Antwort:</b> {aiResponse?.result?.response == "Die gesuchten Informationen waren nicht enthalten." ? "Es könnte sein, dass die gesuchten Informationen möglicherweise im Skript enthalten sind, aber ich finde sie gerade nicht. Vielleicht wäre es ratsam, alternative Ressourcen zu konsultieren, um die Antwort zu finden. Alternativ könnten deine Dozierenden möglicherweise weiterhelfen. Bitte habe Verständnis dafür, dass ich mich noch im Lernprozess befinde und darauf bedacht bin, keine falschen Informationen zu geben." : aiResponse?.result?.response}</p>
                        <Accordion defaultActiveKey="2">
                            <Accordion.Toggle as={Button} variant="link" eventKey="0">
                                <Button variant={"primary"}>Quellen anzeigen</Button>
                            </Accordion.Toggle>

                            <Accordion.Collapse eventKey="0">
                                <p className={"mt-2"}>
                                    Ich habe dafür folgende Dokumente gelesen:
                                    <ListGroup>
                                        {aiResponse?.parts.sort(({score: a}, {score: b}) => a - b).map((part) => {
                                            return <ListGroup.Item>
                                                <h6><b>{part.document?.name}</b> <Badge
                                                    variant={"primary"}>Relevanz-Score: {Math.round(part?.score * -10000) / 100}</Badge>
                                                </h6>
                                                <p><i>Auszug:</i> {part.content}</p>
                                                <Button variant={"outline-secondary"} onClick={() => {
                                                    handlePreviewViewer(part.document)
                                                }}>Datei anzeigen</Button>
                                            </ListGroup.Item>
                                        })}
                                    </ListGroup>
                                </p>
                            </Accordion.Collapse>
                        </Accordion>
                    </Card.Body>
                </Card>
            </>}</Col>

        <EducaModal closeButton={true} noBackdrop={true} size={"lg"} ref={modalRef}/>
        <EducaModal closeButton={true} noBackdrop={true} size={"xl"} ref={pdfModalRef}/>
    </div>
}

export default AIContent;
