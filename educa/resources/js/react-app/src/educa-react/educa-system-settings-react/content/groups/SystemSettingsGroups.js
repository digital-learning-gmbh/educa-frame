import React, {useEffect, useRef, useState} from 'react';
import {EducaDefaultTable} from "../../../../shared/shared-components";
import Card from "react-bootstrap/Card";
import Button from "react-bootstrap/Button";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import SafeDeleteModal from "../../../../shared/shared-components/SafeDeleteModal";
import {Spinner} from "react-bootstrap";

function SystemSettingsGroups(props) {

    const [groups, setGroups] = useState([])
    let [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        loadGroups()
    },[])

    const safeDeleteModalRef = useRef()

    const loadGroups = () =>
    {
        setIsLoading(true)
         AjaxHelper.getSystemSettingsGroups()
                .then(resp => {
                    if(resp.payload.groups)
                        return setGroups(resp.payload.groups)
                    throw new Error()
                })
             .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Gruppen konnten nicht geladen werden."))
             .finally(() => setIsLoading(false))

    }

    const deArchive = (group) =>
    {
        setIsLoading(true)
        AjaxHelper.archiveSystemSettingsGroups(group.id)
            .then( resp => {
                if(resp.status > 0)
                {
                    SharedHelper.fireSuccessToast("Erfolg", "Die Gruppe wurde aus dem Archiv entnommen.")
                    return loadGroups()
                }

            })
            .catch( () => SharedHelper.fireErrorToast("Fehler","Die Gruppe konnte nicht aus dem Archiv entnommen werden."))
            .finally(() => setIsLoading(false))

    }

    const onDeleteClicked = (group) =>
    {
        const exec = () =>  {
            setIsLoading(true)
            AjaxHelper.deleteSystemSettingsGroup(group.id)
                .then(resp => {
                    if(resp.status > 0)
                    {
                        setGroups(groups.filter( g => g.id !== group.id))
                        SharedHelper.fireSuccessToast("Erfolg", "Die Gruppe")
                    }
                })
                .catch( () => SharedHelper.fireErrorToast("Fehler", "Die Gruppe konnte nicht gelöscht werden."))
                .finally(() => setIsLoading(false))

        }

        const keyword = "LÖSCHEN"

        safeDeleteModalRef.current?.open(
            (b) => b? exec() : null,
            "'" + group.name +"' löschen",
            "Achtung, alle zugehörigen Daten dieser Gruppe werden gelöscht. Wenn Sie fortfahren möchten geben Sie bitte "+keyword+ " in das untere Textfeld ein.",
            keyword
        )

    }

    return (
        <div>
            <Card style={{backgroundColor: "white"}}>
                <Card.Header>
                    <div style={{flex : 1, display : "flex", flexDirection :"column"}}>
                        <h5 className="card-title">
                            <b><i className="fas fa-pencil-alt"></i> Gruppen </b>
                            {isLoading && <Spinner className={"ml-2 align-self-center"} animation={"grow"}/>}

                        </h5>
                        <div className={"text-muted"}>Übersichtsliste aller Gruppen im System</div>
                    </div>
                </Card.Header>
                <Card.Body>
                    <EducaDefaultTable
                        size={"lg"}
                        defaultSorted={[
                            {
                                id: "name",
                                desc: true
                            }
                        ]}
                        defaultPageSize={50}
                        pagination={true}
                        columnResizing={true}
                        globalFilter={true}
                        buttonPdfExport={true}
                        buttonExcelExport={true}
                        pageSizePicker={true}
                        columnPicker={true}
                        multiSelect={false}
                        filename={"benutzer_"}
                        columns={[
                            { Header: 'Name', accessor: 'name'},
                            { Header: 'Anzahl Mitglieder', accessor: 'amountStudents',  filter : true  },
                            { Header: 'Archiviert', accessor: 'archivedComp',  },
                            { Header: 'Aktion', accessor: 'actions', width: 150 },
                        ]}
                        data={
                            groups?
                                groups?.map( s =>
                                {
                                    return {...s,
                                        amountStudents : s.membersCount,
                                        archivedComp : s.archived? <i className={"fas fa-check"}/> :  <i className={"fas fa-times"}/> ,
                                        actions: <>
                                            <Button onClick={() => onDeleteClicked(s)} variant={"danger"} className={"mr-1"}><i className={"fas fa-trash"}/> Löschen</Button>
                                            {s.archived?<>
                                            <Button className={"outline-primary"} onClick={() => deArchive(s)}>< i className={"fas fa-lock-open"}/> Dem Archiv entnehmen</Button>
                                                 </> : null}
                                            </>
                                    }
                                }) : []
                        }
                    />
                </Card.Body>
                <SafeDeleteModal ref={safeDeleteModalRef}/>
            </Card>

        </div>
    );
}

export default SystemSettingsGroups;
