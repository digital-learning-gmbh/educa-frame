import React, {createRef, useEffect, useState} from 'react';
import Card from "react-bootstrap/Card";
import Button from "react-bootstrap/Button";
import EducaContactEdit from "./EducaContactEdit";
import SafeDeleteModal from "../../../../shared/shared-components/SafeDeleteModal";
import EducaModal, {MODAL_BUTTONS} from "../../../../shared/shared-components/EducaModal";
import {EducaLoading} from "../../../../shared-local/Loading";
import EducaHelper from "../../../helpers/EducaHelper";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import {EducaCircularButton, EducaDefaultTable} from "../../../../shared/shared-components";
import BookingSlotModal from "./BookingSlotModal";

function SystemSettingsAddressbookEdit(props) {

    let [adressbookEntries, setAdressbookEntries] = useState([])
    let [roles, setRoles] = useState([])
    let [loading, setLoading] = useState(false);
    const [selectedContact, setSelectedContact] = useState()
    const [users, setUsers] = useState([])

    var contact = null;

    let educaModalRef = createRef();
    let safeDeleteModalRef = createRef();

    const loadUserList = () => {
        AjaxHelper.loadSystemSettingsUserList()
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.users) {
                    setUsers(resp.payload.users);
                } else
                    throw new Error("")
            })
            .catch(err => {
                console.log(err)
                EducaHelper.fireErrorToast("Fehler", "Die Benutzerliste konnte nicht geladen werden.")
            })
            .finally(() => {
                setLoading(false)
            })
    }


    useEffect(() => {
        loadAllAddressbookEntries()
            .then( () => {
                loadUserList()
            })

    },[]);

    let loadAllAddressbookEntries = () => {
        setLoading(true)
        return AjaxHelper.loadAllAddressbookEntries()
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.entries) {
                    setAdressbookEntries(resp.payload.entries);
                    setRoles(resp.payload.roles)
                } else
                    throw new Error("")
            })
            .catch(err => {
                console.log(err)
                EducaHelper.fireErrorToast("Fehler", "Die Kontaktliste konnte nicht geladen werden.")
            })
    }

    let modifyContact = (contact) =>
    {
        educaModalRef?.current?.open((btn) => {
            if (btn === MODAL_BUTTONS.OK) {
                contact.roles = contact.roles?.map((role) => role.id);
                AjaxHelper.updateContact(contact)
                    .then(resp => {
                        if (resp.status > 0 && resp.payload && resp.payload.entries) {
                            setAdressbookEntries(resp.payload.entries);
                            setRoles(resp.payload.roles)
                        } else
                            throw new Error("")
                    })
                    .catch(err => {
                        console.log(err)
                        EducaHelper.fireErrorToast("Fehler", "Die Rollen konnten nicht aktualisiert werden.")
                    })
            }
        }, "Kontakt bearbeiten", <EducaContactEdit users={users} roles={roles} modal={educaModalRef} contact={contact} setContact={(roles) => {
            contact = roles;
        }} />, [MODAL_BUTTONS.CANCEL, MODAL_BUTTONS.OK])
    }

    let addButtonClick = () => {
        educaModalRef?.current?.open((btn) => {
            if (btn === MODAL_BUTTONS.OK) {
                contact.roles = contact.roles?.map((role) => role.id);
                AjaxHelper.addSystemSettingsContact(contact)
                    .then(resp => {
                        if (resp.status > 0 && resp.payload && resp.payload.entries) {
                            setAdressbookEntries(resp.payload.entries);
                            setRoles(resp.payload.roles)
                        } else
                            throw new Error("")
                    })
                    .catch(err => {
                        console.log(err)
                        EducaHelper.fireErrorToast("Fehler", "Der Kontakt konnte nicht erstellt werden.")
                    })
            }
        }, "Kontakt hinzufügen", <EducaContactEdit users={users} roles={roles} modal={educaModalRef} setContact={(roles) => {
            contact = roles;
        }} />, [MODAL_BUTTONS.CANCEL, MODAL_BUTTONS.OK])
    }

    let deleteContact = (contact) => {
        AjaxHelper.deleteSystemSettingsContact(contact)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.entries) {
                    setAdressbookEntries(resp.payload.entries);
                    setRoles(resp.payload.roles)
                } else
                    throw new Error("")
            })
            .catch(err => {
                console.log(err)
                EducaHelper.fireErrorToast("Fehler", "Der Kontakt konnte nicht erstellt werden.")
            })
    }

    let onDeleteClick = (contact) => {
        const keyword = "LÖSCHEN"

        safeDeleteModalRef?.current?.open(
            (b) => b? deleteContact(contact) : null,
            "'" + contact?.name +"' löschen",
            "Achtung, dieser Kontakt-Eintrag wird gelöscht. Wenn der Kontakt-Eintrag wirklich gelöscht werden soll, geben Sie bitte "+keyword+ " in das untere Textfeld ein.",
            keyword
        )
    }

    return (
        loading ? <EducaLoading/> :
            <Card style={{backgroundColor: "white"}}>
                <Card.Header>
                    <div style={{flex : 1, display : "flex", flexDirection :"row"}}>
                        <h5 className="card-title">
                            <b><i className="fas fa-pencil-alt"></i> Globale Kontakte</b>
                        </h5>
                    </div>
                </Card.Header>
                <Card.Body>
                    <EducaDefaultTable
                        size={"lg"}
                        defaultPageSize={50}
                        pagination={true}
                        columnResizing={true}
                        globalFilter={true}
                        buttonPdfExport={true}
                        buttonExcelExport={true}
                        pageSizePicker={true}
                        columnPicker={true}
                        multiSelect={true}
                        filename={"benutzer_"}
                        columns={[
                            { Header: 'Name', accessor: 'name',  filter : true  },
                            { Header: 'E-Mail', accessor: 'email',  filter : true  },
                            { Header: 'Funktion', accessor: 'role',  filter : true  },
                            { Header: 'Ort', accessor: 'location',  filter : true  },
                            { Header: 'Telefon', accessor: 'telephone',  filter : true  },
                            { Header: 'sichtbar für', accessor: 'visible_for',  filter : true  },
                            { Header: 'Aktion', accessor: 'actions',  filter : true , width: 75 },
                        ]}
                        customButtonBarComponents={
                            [   <Button onClick={() => addButtonClick() }><i className={"fa fa-plus"}/> Hinzufügen</Button>]
                        }
                        data={
                            adressbookEntries?
                                adressbookEntries?.map( s =>
                                {
                                    return {...s,
                                        visible_for: s?.roles.map(r => r.name).join(", "),
                                        actions: <>
                                            <EducaCircularButton size={"small"} onClick={() => modifyContact(s)}><i className={"fa fa-pencil-alt"}/></EducaCircularButton>
                                            <EducaCircularButton disabled={!s.cloudid} tooltip={s.cloudid?"" : "Keine verlinkte Person"} className={"ml-1"} variant={"outline-primary"} size={"small"} onClick={() => setSelectedContact(s)}><i className={"fa fa-calendar-alt"}/></EducaCircularButton>
                                            <EducaCircularButton className={"ml-1"} variant={"danger"} size={"small"}
                                                                 onClick={() => onDeleteClick(s)}><i
                                                className={"fas fa-trash"}/></EducaCircularButton>
                                        </>
                                    };
                                }) : []
                        }
                    />
                </Card.Body>
                <EducaModal size={"lg"} ref={educaModalRef}/>
                <SafeDeleteModal ref={safeDeleteModalRef} />
                <BookingSlotModal contact={selectedContact} hide={() => setSelectedContact()}/>
            </Card>
    );
}


export default SystemSettingsAddressbookEdit;
