import React, {useEffect, useRef, useState} from "react";
import {Col, Container, FormControl, ListGroup, ListGroupItem, Row, Tab, Tabs} from "react-bootstrap";
import {EducaCircularButton} from "../../../../shared/shared-components";
import {useSelector} from "react-redux";
import Modal from "react-bootstrap/Modal";
import Button from "react-bootstrap/Button";
import {DisplayPair} from "../../../../shared/shared-components/Inputs";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";

const defaultIntegration = {
    displayName : "",
    description : "",
    url :"",
}
export default function AddIntegrationModal({open, group, setGroup, hide})
{

    const templates = useSelector(s => s.externalIntegrationTemplates)?.filter( temp => !group?.external_integrations?.find(obj => obj?.template_id  == temp.id))
    const [selectedTab, setSelectedTab] = useState(0)
    const [showEditView, setShowEditView] = useState(false)
    const [newIntegration, setNewIntegration] = useState(defaultIntegration)

    useEffect(() => {
        if(open)
        {
            setSelectedTab(0)
            setShowEditView(false)
            setNewIntegration(defaultIntegration)
        }
    },[open])
    const saveIntegration = () =>
    {
        AjaxHelper.createGroupExternalIntegration(group.id, newIntegration)
            .then( resp =>{
                setGroup({...group, external_integrations : resp.payload.externalIntegrations})
                SharedHelper.fireSuccessToast("Erfolg", "Die Integration wurde gespeichert.")
                hide()
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Integration konnte nicht gespeichert werden."))

    }
    const getEditView = () =>
    {
        return <div class={"d-flex flex-column"}>
            {showEditView? <div>
                <Button onClick={() => setShowEditView(false)} variant={"secondary"}>
                    <i className={"fas fa-arrow-left"}/> Zurück </Button>
            </div>: null}
            {!!newIntegration?.id?
                <div>
                    <b>Wenn du die externe Integration "{newIntegration.displayName}" hinzufügen möchtest, klicke jetzt speichern.</b>
                </div>
                :
            <>
                <DisplayPair title={"Anzeigename"}>
                <FormControl
                    value={newIntegration?.displayName??""}
                    onChange={(e) => setNewIntegration({...newIntegration, displayName: e.target.value})}
                />
            </DisplayPair>
            <DisplayPair title={"Beschreibung"}>
                <FormControl
                    value={newIntegration?.description??""}
                    onChange={(e) => setNewIntegration({...newIntegration, description: e.target.value})}
                />
            </DisplayPair>
            <DisplayPair title={"URL"}>
                <FormControl
                    value={newIntegration?.url??""}
                    onChange={(e) => setNewIntegration({...newIntegration, url: e.target.value})}
                />
            </DisplayPair>
            </>
            }
            <div>
                <Button onClick={() => saveIntegration()}>
                <i className={"fas fa-save"}/> Speichern
            </Button>
            </div>
        </div>
    }

    const getContent = () =>
    {
        if(!open)
            return
        if(!showEditView)
            return getList()
        return getEditView()

    }

    const getList = () =>
    {

        return  <Container>
            <Tabs activeKey={selectedTab} onSelect={(key) => {setSelectedTab(key); setNewIntegration(defaultIntegration)}}>
                <Tab eventKey={0} title={<> <i className={"fas fa-plus"}/> Auswählen</>}>
                    <ListGroup variant={"flush"}>
                        {templates?.map( (t,i) => {

                            return  <ListGroupItem key={i}>
                                <div className={"row"}>
                                    <div className={"col-2"} style={{width : "100px", height:"100px", backgroundImage: "url('" + t?.icon + "')",
                                        backgroundSize: "contain",
                                        backgroundPosition: "center", backgroundRepeat: "no-repeat"}}>
                                    </div>
                                    <div className={"d-flex col-10"}>
                                        <div className={"d-flex flex-column w-100"}>
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5>{t?.displayName}</h5>
                                                <small class="text-muted">
                                                    <EducaCircularButton
                                                        size={"small"}
                                                        onClick={() => {setShowEditView(true); setNewIntegration({...t, template_id : t.id})}}
                                                        variant={"success"}>
                                                        <i className="fas fa-plus"></i>
                                                    </EducaCircularButton>
                                                </small>
                                            </div>
                                            <div class={"text-muted"}>
                                                {t?.description}
                                            </div>
                                            <small class="text-muted">{t?.type == "link" ? "Die Integration erfolgt als externer Link" : ""}</small>
                                        </div>
                                    </div>
                                </div>
                            </ListGroupItem>
                        })
                        }
                        {!templates?.length? <i>Du hast alle externen Integration hinzugefügt.</i> : null}

                    </ListGroup>
                </Tab>
                <Tab eventKey={1} title={<> <i className={"fas fa-pencil-alt"}/> Erstellen</>}>
                    {getEditView(true)}
                </Tab>
            </Tabs>

        </Container>
    }

    return   <Modal show={open} onHide={hide} size={"xl"}>
        <Modal.Header>
            <Modal.Title>
                Externe Integration hinzufügen
            </Modal.Title>
        </Modal.Header>
        <Modal.Body>
            {getContent()}
        </Modal.Body>
    </Modal>
}
