import React, {useEffect, useRef, useState} from "react";
import {Button, Card, ListGroup, ListGroupItem} from "react-bootstrap";
import {EducaCircularButton} from "../../../shared/shared-components";
import EducaModal, {MODAL_BUTTONS} from "../../../shared/shared-components/EducaModal";
import AddIntegrationModal from "./components/AddIntegrationModal";
import {useDispatch, useSelector} from "react-redux";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {GENERAL_SET_EXTERNAL_INTEGRATION_TEMPLATES} from "../../reducers/GeneralReducer";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import {EducaLoading} from "../../../shared-local/Loading";

export default function GroupExternalLinks({group, setGroup})
{
    const [isLoading, setIsLoading] = useState(false)
    const [addIntegrationModalOpen, setAddIntegrationModalOpen] = useState(false)
    const educaModalRef = useRef()

    const dispatch = useDispatch()
    const setExternalIntegrationTemplates = (tmplts) => dispatch({type : GENERAL_SET_EXTERNAL_INTEGRATION_TEMPLATES, payload : tmplts})
    const externalIntegrationTemplates = useSelector(s => s.externalIntegrationTemplates)

    useEffect(() => {
        loadExternalIntegrationTemplates()
    },[])

    const loadExternalIntegrationTemplates = () =>
    {
        if(externalIntegrationTemplates)
            return
        setIsLoading(true)
        AjaxHelper.getExternalIntegrationTemplates()
            .then(resp => {
                setExternalIntegrationTemplates(resp.payload.templates)
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Externen Integrationen konnten nicht geladen werden."))
            .finally(() => setIsLoading(false))
    }

    const deleteExternalIntegration = (id) =>
    {
        const exec = () => {
            AjaxHelper.deleteGroupExternalIntegration(group.id, id)
                .then( resp =>{
                    setGroup({...group, external_integrations : resp.payload.externalIntegrations})
                    SharedHelper.fireSuccessToast("Erfolg", "Die Integration wurde gelöscht.")
                })
                .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Integration konnte nicht gelöscht werden."))

        }

        educaModalRef.current.open(btn => btn == MODAL_BUTTONS.YES?exec() : null, "Integration löschen", "Möchtest du die externe Integration wirklich löschen?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO])
    }


    const getContent = () => {

        if(isLoading)
            return <EducaLoading/>

        return <Card>
        <ListGroup variant={"flush"}>
            {group?.external_integrations?.map( integr => {
                return <ListGroupItem key={integr.id}>
                    <div className={"row"}>
                        <div className={"col-3"} style={{width : "150px", height:"150px",   backgroundImage: "url('" + integr.icon + "')",
                            backgroundSize: "contain",
                            backgroundPosition: "center", backgroundRepeat: "no-repeat"}}>
                        </div>
                        <div className={"d-flex col-9"}>
                            <div className={"d-flex flex-column w-100"}>
                                <div class="d-flex w-100 justify-content-between">
                                <h5>{integr.displayName}</h5>
                                <small class="text-muted">
                                    <EducaCircularButton onClick={() => deleteExternalIntegration(integr.id)} size={"small"}>
                                        <i className={"fas fa-trash"}/>
                                    </EducaCircularButton>
                                </small>
                                </div>
                                <div class={"text-muted"}>{integr.description}</div>
                            </div>
                        </div>
                    </div>
                </ListGroupItem>
            })
            }
            {!group?.external_integrations?.length? <Card.Body className={"text-center"}><i>Noch keine externe Integrationen erstellt.</i></Card.Body>
                : null}
        </ListGroup>
        </Card>
    }

    return   <div
        style={{
            width: "750px",
            marginRight: "auto",
            marginLeft: "auto"
        }}
        className={"mt-2"}
    >
        <div className={"d-flex"}>
            <h5 className={"mt-2 mb-2"}>
                <b>Externe Integrationen</b>
            </h5>
            {isLoading? null : <div className={"d-flex align-items-center"}>
                <EducaCircularButton className={"ml-2"} size={"small"} variant="success" onClick={() => setAddIntegrationModalOpen(true)}>
                    <i className={"fas fa-plus"}/>
                </EducaCircularButton>
            </div>}

        </div>

        <p>Erweitere die educa Gruppe um externe Integrationen, um das Lernerlebnis individuell zu verbessern.</p>
            {getContent()}
        <AddIntegrationModal
            setGroup={setGroup}
            open={addIntegrationModalOpen}
            group={group}
            hide={() => setAddIntegrationModalOpen(false)}
        />
        <EducaModal ref={educaModalRef}/>
    </div>
}
