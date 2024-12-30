import React, {Component, useEffect, useRef, useState} from 'react';
import {Card, Container, Row} from "react-bootstrap";
import {useSelector} from "react-redux";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import ReactTabulator from "react-tabulator/lib/ReactTabulator";
import AjaxHelper from "../../helpers/EducaAjaxHelper";

export function ClassbookAbsenteeism(props) {

    let [ data, setData ] = useState([])
    let [ isLoading, setIsLoading ] = useState(true)
    let [ isStudent, setIsStudent ] = useState(true)
    let [overall, setOverall] = useState(0)
    let tableRef = useRef()

    let store = useSelector(state => state)

    useEffect(() =>
    {
        loadBalanceData()
    }, [])


    let loadBalanceData = () =>
    {
        setIsLoading(true)

        const me = store.currentCloudUser
        if(!me.student)
        {
            setIsStudent(false)
            return
        }
        setIsStudent(true)
        AjaxHelper.getAbsenteeism(me.student.id, 0, 999999999999999999999)
            .then( resp =>
            {
                if( resp.status > 0 && resp?.payload?.data)
                {
                  //  setData(resp.payload.data)
                  //  setOverall(resp.payload.overall)
                    setIsLoading(false)
                    return
                }
                throw new Error(resp.message)
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Die Fehlzeiten des Schülers konnten nicht geladen werden.")
            })
    }

    const columns = [
        { title: 'Datum', field: 'name' },
        { title: 'Fach', field: 'course', formatter: "html" },
        { title: 'Anwesenheit', field: 'fehlzeit_name' },
    ];

    const options =
        { movableColumns: true,
            movableRows: true,
            placeholder:" Keine Fehlzeiten vorhanden",
            printAsHtml: true,
            downloadDataFormatter: (data) => data,
            downloadReady: (fileContents, blob) => blob,
        };

    return <Card style={{height : "100%"}}>
        <Card.Header style={{ backgroundColor: "#fff"}}>
            <h5 className="card-title"><b><i className="fas fa-user-clock"></i> Fehlzeiten</b>
                <a
                    className="card-link m-1 card-link"
                    onClick={() => { loadBalanceData() } }
                    style={{fontSize: "0.9rem", color: "rgb(0, 123, 255)", cursor: "pointer", float: "right"}}
                ><i
                    className="fas fa-redo-alt"></i> Aktualisieren</a></h5>
        </Card.Header>
        { isStudent?
            <Container className={"mt-2"}>
                <Row>
                    <Card
                        bg={"light"}
                        text={'dark'}
                        style={{ width: '18rem' }}
                        className="mb-2 ml-2"
                    >
                        <Card.Header>Σ Gesamt</Card.Header>
                        <Card.Body>
                            <Card.Title className={"text-center"}><b>0</b></Card.Title>
                            <Card.Text className={"text-center"}>
                                Summe aller Vorlesungsstunden
                            </Card.Text>
                        </Card.Body>
                    </Card>

                    <Card
                        bg={"info"}
                        text={'white'}
                        style={{ width: '18rem' }}
                        className="mb-2 ml-2"
                    >
                        <Card.Header><i className="fas fa-percent"></i> Fehlquote entschuldigt</Card.Header>
                        <Card.Body>
                            <Card.Title className={"text-center"}><b>0 %</b></Card.Title>
                            <Card.Text className={"text-center"}>
                                über alle dokumentieren Vorlesungen
                            </Card.Text>
                        </Card.Body>
                    </Card>

                    <Card
                        bg={"danger"}
                        text={'white'}
                        style={{ width: '18rem' }}
                        className="mb-2 ml-2"
                    >
                        <Card.Header><i className="fas fa-percent"></i> Fehlquote unentschuldigt</Card.Header>
                        <Card.Body>
                            <Card.Title className={"text-center"}><b>0 %</b></Card.Title>
                            <Card.Text className={"text-center"}>
                                über alle dokumentieren Vorlesungen
                            </Card.Text>
                        </Card.Body>
                    </Card>
                </Row>
        <div className={"row"}>
            <div className={"m-2 col-5 text-left"}>
                <b>Gesamte Fehlzeiten:</b><p> {overall}</p>
            </div>
            <div className={"m-2 col-6 text-right"}>
                <button className={"btn btn-primary m-1"} onClick={() => {
                    console.log(tableRef);
                    tableRef.current?.table.download("xlsx", "Fehlzeiten.xlsx");
                }}><i className="fas fa-file-excel"></i> Excel</button>
                <button className={"btn btn-success m-1"} onClick={() => {
                    console.log(tableRef);
                    tableRef.current?.table.print()
                }}><i className="fas fa-print"></i> Drucken</button>
            </div>
        </div>
            </Container>: <></> }
        { isStudent ?
        <>{ isLoading ? <div>
                 <h6 className="text-center mt-1">Lade Daten...</h6></div> :
            <>{ data.length > 0?
                <ReactTabulator
                    ref={tableRef}
                    columns={columns}
                    data={data}
                    options={options}
                /> : <h6 className="text-center mt-1">Diese/r Schüler*in hat keine Fehlzeiten.</h6> }</>
        }</> : <div>
                <h6 className="text-center mt-1">Diese Funktion steht nur für Teilnehmer zur Verfügung. Als Dozent*in melden Sie sich bitte im Klassenbuch in StuPla an.</h6></div>  }
    </Card>
}
