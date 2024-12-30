import React, {useEffect, useState} from "react";
import {SideMenuHeadingStyle} from "../educa-components/EducaStyles";
import SideMenu from "../educa-components/SideMenu";
import {useHistory} from "react-router";
import {Button, Card, FormControl, InputGroup} from "react-bootstrap";
import Form from "react-bootstrap/Form";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import { EducaLoading } from "../../shared-local/Loading";
import EducaHelper from "../helpers/EducaHelper";
import { BASE_ROUTES } from "../App";
import EmailContactPage from "./commons/EmailContactPage";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper.js";

export default function EducaContactsViewReact(props) {
    const history = useHistory();

    let [isLoading, setIsLoading] = useState(true);
    let [page, setPage] = useState("dozent");
    let [detailPage, setDetailPage] = useState("");

    let [contacts, setContacts] = useState([]);
    let [currentDisplayContacts, setCurrentDisplayContacts] = useState([]);
    let [q, setQ] = useState("");

    let [selectedContact, setSelectedContact] = useState(null);

    const [translate] = useEducaLocalizedStrings();

    useEffect(() => {
        setIsLoading(true)
        AjaxHelper.getAddressbook().then(resp => {
            if (resp.status > 0 && resp.payload) {
                setContacts(resp.payload);

                if(resp.payload?.generalContacts?.length > 0)
                    setPage("general")

                if(resp.payload?.schoolContacts?.length > 0)
                    setPage("school");

                if(resp.payload?.teacherContacts?.length > 0)
                    setPage("dozent");

            } else
                throw new Error("")
        })
        .catch(err => {
            console.log(err)
            EducaHelper.fireErrorToast("Fehler", "Die Kontaktliste konnte nicht geladen werden.")
        })
        .finally(() => {
            setIsLoading(false)
        })
    }, []);

    useEffect(() => {
        if(page == "dozent")
        {
            setCurrentDisplayContacts(contacts?.teacherContacts?.filter((contact) => {
                if(q == "" || q == null)
                {
                    return true;
                }
                return contact?.name?.toLowerCase()?.includes(q.toLowerCase()) || contact?.role?.toLowerCase()?.includes(q.toLowerCase())
            }))
        }

        if(page == "school")
        {
            setCurrentDisplayContacts(contacts?.schoolContacts)
        }

        if(page == "general")
        {
            setCurrentDisplayContacts(contacts?.generalContacts)
        }
    },[contacts,page,q])

    let getSidemenu = () => {
        let newMenu = [];

        newMenu.push(contactsGroup())
        return <SideMenu menus={newMenu}></SideMenu>;
    }

    let changeRoute = (path, search) => {
        history.push({
            pathname: path,
            search: search
        });
    };


    let contactsGroup = () => {
        let newMenu = [];

        if(contacts?.teacherContacts?.length > 0)
        newMenu.push({
            component: (
                <div>
                    <i className="fas fa-chalkboard-teacher"></i> {translate("contacts_view.scholars","Meine Dozent*innen")}
                </div>
            ),
            clickCallback: () => { setPage("dozent"); setDetailPage(null); setSelectedContact(null) },
        })


        if(contacts?.schoolContacts?.length > 0)
        newMenu.push({
            component: (
                <div>
                    <i className="fas fa-school"></i> {translate("contacts_view.management","Schulverwaltung / Sekretariat")}
                </div>
            ),
            clickCallback: () => { setPage("school"); setDetailPage(null); setSelectedContact(null) },
        })


        if(contacts?.generalContacts?.length > 0)
        newMenu.push({
            component: (
                <div>
                    <i className="fas fa-globe-europe"></i> {translate("contacts_view.global_contacts","Globale Kontakte")}
                </div>
            ),
            clickCallback: () => { setPage("general"); setDetailPage(null); setSelectedContact(null) },
        })
        return {
            heading: {textAndId:translate("group.sections","Bereiche"), component: <></>},
            content: newMenu,
        };
    }

    return <div>
        <div className="d-flex justify-content-between">
            <div style={{width: "300px"}} className={"m-2"}>
                <div style={SideMenuHeadingStyle}>{translate("navbar.addressbook","Adressbuch")}</div>
                {getSidemenu()}
            </div>
            <div style={{width: "calc(100vw - 300px)"}} className="mt-2">
                { isLoading ? <EducaLoading/> : <>
                <div style={SideMenuHeadingStyle}>{translate("contacts_view.whom_contact","Mit wem möchtest du Kontakt aufnehmen?")}</div>
                <div className={"container-fluid"}>
                    <div className={"row"}>
                        <div className={"col-3"}>
                            <InputGroup className="mb-3 mt-3">
                                <Form.Control
                                    placeholder= {translate("search","Suche...")}
                                    aria-label={translate("search","Suche...")}
                                    value={q}
                                    aria-describedby="basic-addon2"
                                    onChange={(evt) => setQ(evt.target.value)}
                                />
                                <Button variant="outline-secondary" id="button-addon2">
                                    <i className="fas fa-search"></i>
                                </Button>
                            </InputGroup>
                            {currentDisplayContacts?.map((contact) => {
                            return <Card key={contact.id} onClick={() => { setDetailPage(null); setSelectedContact(contact) } } bg={selectedContact?.identifier == contact.identifier ? "primary" : ""}  text={selectedContact?.identifier == contact.identifier ? "white" : "dark"}>
                                <Card.Body>
                                    <div className="row no-gutters">
                                        {contact.cloudId ?
                                        <div className="col-auto mr-2">
                                            <img
                                                src={ "/api/image/cloud?cloud_id=" + contact.cloudId.id + "&size=60&name=" + contact.cloudId.image }
                                                className="d-inline-block align-top rounded-circle" alt="" width="60"
                                                height="60"/>
                                        </div> : <></> }
                                        <div className="col">
                                            <Card.Title>{contact.name}</Card.Title>
                                            <Card.Text>{contact.role}</Card.Text>
                                        </div>
                                    </div>
                                </Card.Body>
                            </Card> })}
                        </div>
                        <div className={"col-9"}  style={{maxWidth: "900px"}}>
                            { selectedContact == null ?              <Card>
                                <Card.Body className="text-center">
                                    <i><i className="fa-solid fa-circle-info"></i>{translate("Bitte wähle ein Kontakt aus der Liste aus.")}</i>
                                </Card.Body>
                                </Card> : <>
                            <Card>
                                <Card.Body>
                                    <div className="row no-gutters">
                                    {selectedContact.cloudId ?
                                        <div class="col-auto mr-2">
                                            <img
                                                src={ "/api/image/cloud?cloud_id=" + selectedContact.cloudId.id + "&size=60&name=" + selectedContact.cloudId.image }
                                                className="d-inline-block align-top rounded-circle" alt="" width="60"
                                                height="60"/>
                                           </div> : <></> }
                                        <div className="col">
                                            <Card.Title>{selectedContact.name}</Card.Title>
                                            <Card.Text>{selectedContact.role}</Card.Text>
                                        </div>
                                    </div>
                                    <div>
                                        <div className={"mt-2"}>
                                            <h5><b>Details</b></h5>
                                            { selectedContact?.location ? <h6><b><i className="fas fa-map-marker-alt"></i></b> {selectedContact?.location}</h6> : <></> }
                                            { selectedContact?.telephone ? <h6><b><i className="fas fa-phone"></i></b> {selectedContact?.telephone}</h6> : <></> }
                                            { selectedContact?.email ? <h6><b><i className="fas fa-envelope"></i></b> {selectedContact?.email}</h6> : <></> }
                                        </div>
                                        <div className={"mt-2"}>
                                            <h5><b>{translate("contacts_view.contact","Kontakt")}</b></h5>
                                            <h6>{translate("contacts_view.how_contact","Wie möchtest du Kontakt aufnehmen?")}</h6>
                                            <div className={"d-flex"}>
                                                { selectedContact.cloudId ?
                                                <div className={"border m-1"} onClick={() => setDetailPage("chat")} style={{
                                                    borderRadius: "4px",
                                                    width: "75px",
                                                    height: "75px",
                                                    padding: "14px",
                                                    textAlign: "center"
                                                }}><i className="fas fa-comments fa-3x"></i></div> : <></> }
                                                    { selectedContact?.email && selectedContact.isMailAllowed ?
                                                 <div className={"border m-1"} onClick={() => setDetailPage("email")} style={{
                                                    borderRadius: "4px",
                                                    width: "75px",
                                                    height: "75px",
                                                    padding: "14px",
                                                    textAlign: "center"
                                                }}><i className="fas fa-envelope fa-3x"></i></div> : <></> }
                                                { (selectedContact?.email == null  && selectedContact.isMailAllowed) && selectedContact?.cloudId == null ?
                                                <>
                                                        <label><i className="fas fa-info-circle"></i>{translate("contacts_view.no_contact_chances","Keine Kontaktmöglichkeiten")}</label>
                                                </> :
                                                <>
                                                </> }

                                            </div>
                                        </div>
                                    </div>
                                </Card.Body>
                            </Card>
                            { detailPage == "email" ? <EmailContactPage contact={selectedContact}/>
                             : <></>
                            }
                              { detailPage == "chat" ?
                                           <Card  className={"mt-3"}>
                                           <Card.Body>
                                           <div>
                                                <h6><b>{translate("contacts_view.contact_chat","Kontakt per Chat")}</b></h6>
                                                <p>{translate("contacts_view.chat_info","Über den Chat können direkt in educa Nachrichten und Dokumente ausgetauscht werden.")}</p>
                                                <div className="float-right">
                                                    <Button variant="primary" onClick={() => {
                                                        changeRoute(
                                                            BASE_ROUTES.ROOT_MESSAGES,
                                                            "?message_to=" + selectedContact?.cloudId?.id
                                                        )
                                                    }}><i className="fas fa-comments"></i>{translate("contacts_view.start_chat","Chat starten")}</Button>
                                                </div>
                                            </div>
                                            </Card.Body>
                                            </Card> : <></> }
                            </> }
                        </div>
                    </div>
                </div></> }
            </div>
        </div>
    </div>
}
