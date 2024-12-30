import React, {createRef, useEffect, useState} from 'react';
import {EducaLoading} from "../../../../shared-local/Loading";
import Card from "react-bootstrap/Card";
import {EducaCircularButton, EducaDefaultTable} from "../../../../shared/shared-components";
import Button from "react-bootstrap/Button";
import {Alert, Col} from "react-bootstrap";
import AjaxHelper from "../../../helpers/EducaAjaxHelper.js";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper.js";

function SystemSettingsMaintenance(props) {

    let [loading, setLoading] = useState(false);
    let [license, setLicense] = useState(null);
    let [revision, setRevision] = useState(null);
    let [logs, setLogs] = useState([]);
    let [availableLogDates, setAvailableLogDates] = useState([]);

    useEffect(() => {
        loadGroups()
    },[])

    const loadGroups = () =>
    {
        setLoading(true)
        AjaxHelper.getSystemSettingsMaintenance()
            .then(resp => {
                if(resp.payload.revision && resp.payload.license) {
                    setRevision(resp.payload.revision)
                    setLicense(resp.payload.license)
                    setLogs(resp.payload.logs?.original?.data?.logs)
                    return;
                }
                throw new Error()
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Die Systeminformationen konnten nicht geladen werden."))
            .finally(() => setLoading(false))

    }

    return (
        loading ? <EducaLoading/> :
            <div>
                <div className="row">
                    <Col>

                        <Card
                            bg={"white"}
                            text={'dark'}
                        >
                            <Card.Header><i className="fas fa-code-branch"></i> Version-Information</Card.Header>
                            <Card.Body>
                                <Card.Title className={"text-center"}><b>{revision?.hash}</b></Card.Title>
                                <Card.Text className={"text-center"}>
                                    auf Basis der Version {revision?.baseVersion}
                                </Card.Text>
                            </Card.Body>
                        </Card>
                    </Col>
                    <Col>
                        <Card
                            bg={"danger"}
                            text={'white'}
                        >
                            <Card.Header><i className="fas fa-bug"></i> Fehler in den letzten 24 Stunden</Card.Header>
                            <Card.Body>
                                <Card.Title className={"text-center"}><b>{(logs ?? []).filter(s => s.type === "ERROR").length}</b></Card.Title>
                                <Card.Text className={"text-center"}>
                                    von {(logs ?? []).length} Meldungen
                                </Card.Text>
                            </Card.Body>
                        </Card></Col>
                    <Col>
                        <Card
                            bg={"white"}
                            text={'dark'}
                        >
                            <Card.Header><i className="far fa-id-badge"></i> Lizenz</Card.Header>
                            <Card.Body>
                                <Card.Title className={"text-center"}><b>{license?.license}</b></Card.Title>
                                <Card.Text className={"text-center"}>
                                    Ablauf der Lizenz: {license?.expire}
                                </Card.Text>
                            </Card.Body>
                        </Card></Col>
                </div>
                <div className="row mt-2">
                    <Col>
                        <Card
                            bg={"white"}
                            text={'dark'}
                        >
                            <Card.Header><i className="fas fa-scroll"></i> Log-Protokolle</Card.Header>
                            <Card.Body>
                                <EducaDefaultTable
                                    size={"lg"}
                                    globalFilter={true}
                                    pagination={true}
                                    columns={[{Header: "Zeitstempel", accessor: 'timestamp', filter: true},
                                        {Header: 'Typ', accessor: 'type', filter: true},
                                        {Header: 'Inhalt', accessor: 'message', filter: true},
                                    ]}
                                    data={logs ?? []}
                                />
                            </Card.Body>
                        </Card></Col>
                </div>
            </div>
    )
}

export default SystemSettingsMaintenance;
