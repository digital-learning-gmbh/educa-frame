import * as React from "react";
import {FunctionComponent, useEffect, useState} from "react";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import {EducaDefaultTable} from "../../../../shared/shared-components";
import {Card, Spinner} from "react-bootstrap";
import Select from "react-select";
import Button from "react-bootstrap/Button";

export default function SystemSettingsAnalytics({}) {

    const [reports, setReports] = useState([])
    const [roles, setRoles] = useState([])
    const [isLoading, setIsLoading] = useState(false)

    useEffect(() => {
        loadReports()
    },[])

    const save = () =>
    {
        setIsLoading(true)
        AjaxHelper.updateSystemSettingsAnalyticsReports(reports.map(rep => ({report_id : rep.id, role_ids : rep.roles?.map(role => role.id)??[]})))
            .then( resp => {
                setRoles(resp.payload.roles)
                setReports(resp.payload.reports)
                SharedHelper.fireSuccessToast("Erfolg", "Die Rollen wurden erfolgreich zugeordnet.")
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Daten konnten nicht gespeichert werden."))
            .finally(() => setIsLoading(false))
    }
    const loadReports = () => {
        setIsLoading(true)
        AjaxHelper.getSystemSettingsAnalyticsReports()
            .then( resp => {
                setRoles(resp.payload.roles)
                setReports(resp.payload.reports)
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Daten konnten nicht geladen werden."))
            .finally(() => setIsLoading(false))

    }

    return <>
        <Card style={{backgroundColor: "white"}}>
            <Card.Header>
                <div style={{flex : 1, display : "flex", flexDirection :"row"}}>
                    <h5 className="card-title">
                        <b><i className="fas fa-pencil-alt"></i> Analytics</b>
                        {isLoading ? <Spinner className={"ml-2"} animation={"grow"}/> : null}
                    </h5>
                </div>
                <div className={"text-muted"}>Legen Sie fest, welche Rollenzugriff auf welche Berichte haben</div>
            </Card.Header>
            <Card.Body>
                <EducaDefaultTable
                    customButtonBarComponents={
                        [
                            <Button
                                key={1}
                                onClick={() => save()}
                                className={"mt-1"}
                            >
                                <i className={"fas fa-save"}/> Speichern
                            </Button>
                        ]
                    }
                    pagination={true}
                    defaultPageSize={8}
                    columns={[
                        {Header : "Bericht", accessor : "name", filter : true},
                        {Header : "Rollen mit Zugriffsberechtigung", accessor:  "rolesComp"}
                    ]}
                    data={reports?.map( r => {

                        const rolesComp = <Select
                            isMulti={true}
                            closeMenuOnSelect={false}
                            placeholder={"Rollen"}
                            getOptionLabel ={(option)=> option.name}
                            getOptionValue ={(option)=> option.id}
                            options={roles}
                            value={r.roles}
                            onChange={(val) => setReports(reports.map( rep => r.id !== rep.id? rep : ({...rep, roles : val})))}
                        />

                        return {...r, rolesComp}
                    })
                    }
                />

            </Card.Body>
        </Card>
    </>
}
