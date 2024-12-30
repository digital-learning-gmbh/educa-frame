import React, {useContext, useEffect, useRef, useState} from 'react';
import Card from "react-bootstrap/Card";
import Button from "react-bootstrap/Button";
import {Collapse, FormControl, InputGroup, Row, Spinner} from "react-bootstrap";
import {useLocation} from "react-router";
import {useHistory} from "react-router";
import EducaCloudUserEdit from "./EducaCloudUserEdit";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import EducaHelper from "../../../helpers/EducaHelper";
import {MODAL_BUTTONS} from "../../../../shared/shared-components/EducaModal";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import {EducaCircularButton, EducaDefaultTable} from "../../../../shared/shared-components";
import {DisplayPair} from "../../../../shared/shared-components/Inputs";
import {ModalContext} from "../../EducaSystemSettingsRoot";
import {useSelector} from "react-redux";
import EducaChangeRolesModal from "./EducaChangeRolesModal";

const defaulSelectedUsers = { userIds: [], add: 1 }

function SystemSettingsUsers() {

    let [users, setUsers] = useState([])
    let [isLoading, setIsLoading] = useState(false);
    const [excelImportOpen, setExcelImportOpen] = useState(false)
    const [importFile, setImportFile] = useState(null)
    const [currentUserId, setCurrentUserId] = useState(null)
    const [selectedUsers, setSelectedUsers] = useState(defaulSelectedUsers)
    const fileInputRef = useRef()
    const location = useLocation()
    const history = useHistory()
    const tenant = useSelector((s) => s.tenant);

    const {educaModalRef} = useContext(ModalContext)

    useEffect(() => {
        loadUserList();
    },[]);


    useEffect(() => {
        const userId = new URLSearchParams(location?.search)?.get("user_id")
        setCurrentUserId(userId)
    },[location?.search])

    let loadUserList = () => {
        setIsLoading(true)
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
                setIsLoading(false)
            })
    }

    let onSwitchToUserClick = (stud) =>
    {
        let callback = (btn) =>
        {
            if(btn !== MODAL_BUTTONS.YES)
                return
            EducaHelper.changeUser(stud.id)
        }

        educaModalRef?.current?.open( callback, "Als Nutzer anmelden", "Möchten Sie sich wirklich im educa System als '"+stud.name+"' anmelden?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO] )
    }

    const excelImport = () =>
    {
        AjaxHelper.excelImportUsers(importFile)
            .then( resp => {
                if(resp.status > 0 )
                {
                    SharedHelper.fireSuccessToast("Erfolg", "Der Import war erfolgreich.")
                    return loadUserList()
                }
                throw new Error()
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Import fehlgeschlagen."))

    }

    const onEditClicked = (id) =>
    {
        const urlParams = new URLSearchParams(location.search)
        urlParams.set("user_id", id);
        history.push({
            search: "?"+urlParams.toString()
        });

    }

    const onClose = (usr, deleteObj) =>
    {
        if(!usr)
            return history.push({search: ""})
        if(deleteObj)
             setUsers(users?.filter( u => u.id != usr.id))
        else
            setUsers(users?.map( u => u.id == usr.id? usr : u))
        history.push({search: ""})
    }

    const onNewUser = (usr) =>
    {
        setUsers([...users, ...[usr]])
        history.push({search: ""})
    }

    return (
            <>
                <EducaCloudUserEdit userId={currentUserId}
                                    onNewUser={onNewUser}
                                    onClose={onClose}/>
                <EducaChangeRolesModal show={!!selectedUsers.userIds.length}
                                       userIdsAndAction={selectedUsers}
                                       tenant={tenant}
                                       onClose={() => {
                                           setSelectedUsers(defaulSelectedUsers);
                                           loadUserList();
                                       }} />
                <Card style={{backgroundColor: "white"}}>
                    <Card.Header>
                        <div style={{flex : 1, display : "flex", flexDirection :"row"}}>
                            <h5 className="card-title">
                                <b><i className="fas fa-pencil-alt"></i> Benutzer</b>
                                {isLoading && <Spinner className={"ml-2 align-self-center"} animation={"grow"}/>}
                            </h5>
                        </div>
                    </Card.Header>
                    <Card.Body>
                        <Collapse in={excelImportOpen}>
                            <div style={{width : "550px"}}>
                                <DisplayPair title={"Excel-Import"}>
                                    <input
                                        type="file"
                                        ref={fileInputRef}
                                        onChange={(evt) => {
                                            if(evt.target.files?.length === 1 && ( evt.target.files[0].name.includes("csv")
                                                ||  evt.target.files[0].name.includes("xls")
                                                || evt.target.files[0].name.includes("xlsx")))
                                                return setImportFile(evt.target.files[0])
                                            SharedHelper.fireWarningToast("Achtung", "Bitte nur eine csv oder xlsx Datei auswählen.")
                                        }}
                                        accept={".csv,.xls,.xlsx"}
                                        style={{width: "0px", display: "none"}}/>
                                    <InputGroup>
                                        <FormControl
                                            disabled={true}
                                            value={importFile? importFile?.name : "Keine Datei"}
                                        />
                                        <InputGroup.Prepend>
                                            <Button
                                                title={"Datei Hochladen"}
                                                variant={importFile? "secondary" : "primary"}
                                                onClick={() => fileInputRef.current.click()}
                                                type="button"><i className={"fas fa-file"}/> Datei auswählen
                                            </Button>
                                        </InputGroup.Prepend>
                                        <InputGroup.Prepend>
                                            {!!importFile &&
                                                <Button
                                                    disabled={!importFile}
                                                    title={"Datei Hochladen"}
                                                    variant={"primary"}
                                                    onClick={() => excelImport()}
                                                    type="button">
                                                    <i className={"fas fa-upload"}/> Jetzt hochladen
                                                </Button>}
                                        </InputGroup.Prepend>
                                    </InputGroup>
                                    <label>Beispiel-Datei <a href={"/user-import-sample.xlsx"} target={"_blank"}>herunterladen</a></label>
                                </DisplayPair>

                            </div>
                        </Collapse>

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
                            customButtonBarComponents={[
                                <Button
                                    variant={excelImportOpen?"danger" : "primary"}
                                    key={1}
                                    onClick={() => {
                                        if(!excelImportOpen)
                                            return setExcelImportOpen(true)
                                        setExcelImportOpen(false)
                                        setImportFile(null)
                                    }}
                                >
                                    <i className={"fas fa-" +(excelImportOpen? "times" : "file-import")}/> {excelImportOpen? " Abbrechen" : " Excel import" }
                                </Button>,
                                <Button
                                    variant={"primary"}
                                    key={2}
                                    onClick={() => onEditClicked(-1)}
                                >
                                    <i className={"fas fa-user-plus"}/> Nutzer hinzufügen
                                </Button>
                            ]}
                            columns={[
                                { Header: 'E-Mail / Login', accessor: 'email',  filter : true  },
                                { Header: 'Name', accessor: 'name',  filter : true  },
                                { Header: 'Rollen', accessor: 'rols',  filter : true  },
                                { Header: 'Gruppen', accessor: 'groups',  filter : true  },
                                { Header: 'Apps', accessor: 'apps_react',  filter : true  },
                                { Header: 'Aktion', accessor: 'actions',  width: 50 },
                            ]}
                            customOnSelectionButtons={[
                                <Button
                                    onClickWithSelection={
                                        (selectedRows) =>
                                        {
                                            setSelectedUsers({userIds: selectedRows.map(u => u.id), add: 1})
                                        }}
                                >
                                    <i className="fas fa-plus"></i> Rollen hinzufügen
                                </Button>,
                                <Button
                                    onClickWithSelection={
                                        (selectedRows) =>
                                        {
                                            setSelectedUsers({userIds: selectedRows.map(u => u.id), add: 0})
                                        }}
                                >
                                    <i className="fas fa-minus"></i> Rollen entfernen
                                </Button>
                            ]}
                            data={
                                users?
                                    users?.map( s =>
                                    {
                                        return {...s,
                                            rols : s.roles_global?.map(role => role.name).join(", "),
                                            groups: s.groups?.map(role => role.name)?.join(", "),
                                            apps_react: s.apps?.map(role => role.name)?.join(", "),
                                            actions: <>
                                                <EducaCircularButton size={"small"} onClick={() => onEditClicked(s.id)}><i className={"fa fa-pencil-alt"}/></EducaCircularButton>
                                                <EducaCircularButton className={"ml-1"} variant={"secondary"} size={"small"} onClick={() => onSwitchToUserClick(s)}><i className={"fas fa-random"}/></EducaCircularButton>
                                            </>
                                        };
                                    }) : []
                            }
                        />
                    </Card.Body>
                </Card>
            </>
    );
}


export default SystemSettingsUsers;
