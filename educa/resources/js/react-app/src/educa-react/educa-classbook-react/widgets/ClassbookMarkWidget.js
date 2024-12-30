import React, {Component, useEffect, useRef, useState} from 'react';
import {Alert, Card, Container, Row} from "react-bootstrap";
import {useSelector} from "react-redux";
import ReactTabulator from "react-tabulator/lib/ReactTabulator";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {reactFormatter} from "react-tabulator";
import Button from "react-bootstrap/Button";
import moment from "moment";
import {buildStyles, CircularProgressbar} from "react-circular-progressbar";
import 'react-circular-progressbar/dist/styles.css';
import {EducaDefaultTable} from "../../../shared/shared-components";
import {getDisplayPair} from "../../../shared/shared-components/Inputs";

const ClassbookMarkWidget = (props) => {

    let [grades, setGrades] = useState([])
    let [ isLoading, setIsLoading ] = useState(true)
    let [ isStudent, setIsStudent ] = useState(true)
    let [notenCache, setNotenCache] = useState(null)
    let tableRef = useRef()

    let store = useSelector(state => state)

    useEffect(() =>
    {
        loadBalanceData()
    }, [])

    useEffect(() =>
    {
        if( typeof tableRef?.current?.table?.getRows == "function") {
            tableRef?.current?.table?.getRows()?.forEach(row => {
                if (typeof row.getTreeChildren == "function")
                    row.getTreeChildren().forEach(r2 => r2.treeCollapse())
            })
        }
        tableRef.current?.table?.setSort("semesterComp", "desc")
    },[grades])

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

        AjaxHelper.getMarks(me.student.id)
            .then( resp =>
            {
                if(resp.status > 0 && resp.payload?.grades)
                {
                    let mapping = []

                    setNotenCache(resp.payload?.notenCache)
                    resp.payload?.grades.forEach( g =>
                    {
                        if(!g)
                            return
                        let grade = {...g,
                            // semesterComp : g?.schuljahr ? g.schuljahr?.name : "-",
                            //   moduleComp : g?.model_type == "modul"? g?.belongsObject?.examination_number : g?.belongsObject?.lecture_number,
                            versionComp : g?.version?  <>{g.version} {g.partNoten?.length > 0 ? <Button size={"sm"} onClick={() => {openPartExamModal(g.partNoten)}}><i className={"fas fa-eye"}/> </Button> : null } </> : null,
                            dateComp : g?.datum? moment(g.datum, "YYYY-MM-DD").format("DD.MM.YYYY") : null,
                            statusComp : <>{g?.status == "public"? <i className={"fas fa-globe-europe"}/>: g?.status === "examination_office"? <i className={"fas fa-sync-alt"}/>: g?.status === "draft"? <i className={"fas fa-pencil-ruler"}/> : ""} { g?.transfer === 1 ?  <i className={"fas fa-exchange-alt"}/> : ""}</>,
                            noteComp : g?.note <= 0? "-" : (g?.points < 0 ? (g?.note == 1 ? "bestanden" : "nicht bestanden") : g?.note),
                            pointsComp : g?.points < 0? "-" : g?.points,
                            year: g?.schuljahr?.year
                        }

                        let desiredSemester
                        if(!grade.schuljahr)
                        {
                            desiredSemester = mapping.find( s => s?.id == -1)
                            if(!desiredSemester)
                            {
                                desiredSemester = {id : -1, semesterComp : "Kein Schuljahr", _children : []}
                                mapping.push(desiredSemester)
                            }
                        }
                        else
                        {
                            desiredSemester = mapping.find( s => s?.id == g.schuljahr.id)
                            if(!desiredSemester)
                            {
                                desiredSemester = {...grade.schuljahr, semesterComp : grade.schuljahr.name, year: g.schuljahr.year}
                                mapping.push(desiredSemester)
                            }
                        }


                        if(!desiredSemester["_children"])
                            desiredSemester["_children"] = []

                        let children = desiredSemester["_children"]

                        let desiredModule
                        if(!grade.belongsObject)
                        {
                            desiredModule = children.find( s => s?.id == -1)
                            if(!desiredModule)
                            {
                                desiredModule = {id : -1, moduleComp : "Kein Modul", _children : [], ectsComp: "-"}
                                children.push(desiredModule)
                            }
                        }
                        else
                        {
                            desiredModule = children.find( s => s?.id == g.model_id)
                            if(!desiredModule)
                            {
                                desiredModule =  {...grade.belongsObject, moduleComp : g?.belongsObject?.name, ectsComp: g?.belongsObject?.ects}
                                children.push(desiredModule)
                            }
                        }
                        if( !desiredModule["versionComp"] || desiredModule.versionComp < g.version)
                        {
                            desiredModule.versionComp = g.version
                            desiredModule.noteComp = g?.note <= 0? "-" : (g?.points < 0 ? (g?.note == 1 ? "bestanden" : "nicht bestanden") : g?.note)
                            desiredModule.pointsComp = g?.points < 0? "-" : g?.points
                            desiredModule.dateComp = g?.datum? moment(g.datum, "YYYY-MM-DD").format("DD.MM.YYYY") : null
                            desiredModule.statusComp  = <>{g?.status == "public"? <i className={"fas fa-globe-europe"}/>: g?.status === "examination_office"? <i className={"fas fa-sync-alt"}/>: g?.status === "draft"? <i className={"fas fa-pencil-ruler"}/> : ""} { g?.transfer === 1 ?  <i className={"fas fa-exchange-alt"}/> : ""}</>

                        }


                        if(!desiredModule["_children"])
                            desiredModule["_children"] = []


                        desiredModule["_children"].push(grade)
                    })

                    setGrades(mapping.sort( (b,a) =>  ('' + a.year).localeCompare(''+b.year)))
                    return
                }

            })
            .catch(() =>
            {
                SharedHelper.fireErrorToast("Fehler", "Noten konnten nicht geladen werden.")
            })
            .finally(() =>
            {
                setIsLoading(false)
            })
    }


    const StatusComp = (props) => props.cell.getData()?.statusComp? props.cell.getData()?.statusComp : null
    const VersionComp = (props) => props.cell.getData()?.versionComp? props.cell.getData()?.versionComp : null

    const columns = [
        {title: "Semester", field : "semesterComp", sorter:function(a, b, aRow, bRow, column, dir, sorterParams){
                return aRow.getData().year - bRow.getData().year; //you must return the difference between the two values
            }},
        {title: "Modul", field : "moduleComp"},
        {title: "ECTS", field : "ectsComp"},
        {title: "Versuch", field : "versionComp", formatter :reactFormatter(<VersionComp/>),},
        {title: "Note", field : "noteComp"},
        {title: "Punkte", field : "pointsComp"},
        {title: "Datum", field : "dateComp", width:135},
        {title: " ", field : "statusComp", formatter : reactFormatter(<StatusComp/>), headerFilter:false, headerSort : false},
    ];
    const options = {
        movableColumns: true,
        dataTree:true,
        dataTreeStartExpanded:true,
        pagination:"local", //enable local pagination.
        paginationSize: 35, // this option can take any positive integer value
        locale:"de-de",
        layout:"fitDataFill",
    };

    const percentage = 66;

    return <Card style={{height : "100%"}}>
        <Card.Header style={{ backgroundColor: "#fff"}}>
            <h5 className="card-title"><b><i className="fas fa-star-half"/> Noten</b>
                <a
                    className="card-link m-1 card-link"
                    onClick={() => { loadBalanceData() } }
                    style={{fontSize: "0.9rem", color: "rgb(0, 123, 255)", cursor: "pointer", float: "right"}}
                ><i
                    className="fas fa-redo-alt"></i> Aktualisieren</a></h5>
        </Card.Header>
        { isStudent ?
            <>
        <Card.Body>
            { notenCache ?
                <Container>
                    <Row>
                        <Card
                            bg={"info"}
                            text={'white'}
                            style={{ width: '18rem' }}
                            className="mb-2 ml-2"
                        >
                            <Card.Header><i className="fas fa-star-half-alt"></i> Notenschnitt</Card.Header>
                            <Card.Body>
                                <Card.Title className={"text-center"}><b>{ notenCache.note_schnitt }</b></Card.Title>
                                <Card.Text className={"text-center"}>
                                    berechnet am { moment(notenCache.update_at).format("DD.MM.YYYY HH:mm") }
                                </Card.Text>
                            </Card.Body>
                        </Card>

                        <Card
                            bg={"dark"}
                            text={'white'}
                            style={{ width: '18rem' }}
                            className="mb-2  ml-2"
                        >
                            <Card.Header><i className="fas fa-certificate"></i> Fortschritt</Card.Header>
                            <Card.Body style={{ display:"flex", justifyContent:"center"}}>
                                <div style={{width: '10rem'}}>
                                    <CircularProgressbar styles={buildStyles({
                                        textColor: "white",
                                        pathColor: "white",
                                        trailColor: "grey",
                                    })} textColor={"#fff"} value={Math.round(notenCache.ects_sum / notenCache.ects_max*100)} text={`${Math
                                        .round(notenCache.ects_sum / notenCache.ects_max*100)}%`} /></div>
                                <Card.Text className={"text-center"}>
                                    { notenCache.ects_sum } von { notenCache.ects_max } Credits bereits erreicht.
                                </Card.Text>
                            </Card.Body>
                        </Card>
                    </Row>
                </Container> : <></> }
        </Card.Body>
        <div className={"row"}>
        </div>
      { isLoading ? <div>
                    <h6 className="text-center mt-1">Lade Daten...</h6></div> :
                <>{ grades.length > 0?<>

                    {getDisplayPair("Legende",

                    <div style={{display :"flex"}}>
                        Veröffentlicht: <i className={"fas fa-globe-europe mt-1 mr-2 ml-1"}/>
                        In Erfassung: <i className={"fas fa-sync-alt mt-1 mr-2 ml-1"}/>
                        Entwurf: <i className={"fas fa-pencil-ruler mt-1 mr-2 ml-1"}/>
                        Transfer: <i className={"fas fa-exchange-alt mt-1 mr-2 ml-1"} />
                    </div>)}
                    <ReactTabulator
                        ref={tableRef}
                        columns={columns}
                        data={grades}
                        options={options}/></> : <h6 className="text-center mt-1">Diese/r Schüler*in hat keine Noten.</h6> }</>
            }</> : <div><Card.Body>

                <Alert variant={"info"}>
                    Hier können Sie die Prüfungsergebnisse digital erfassen und übermitteln. Wählen Sie zunächst eine Prüfung aus.
                </Alert>
                <EducaDefaultTable
                    size={"lg"}
                    defaultPageSize={50}
                    pagination={true}
                    columnPicker={true}
                    buttonPdfExport={true}
                    buttonExcelExport={true}
                    columns={[
                        { Header: 'Datum', accessor: 'semesterComp', filter : true, width : "100"},
                        { Header: 'Modul', accessor: 'statusComp', filter : true, width : "100"},
                        { Header: "Prüfungsform", accessor: 'form',  filter : true },
                        { Header: "Räum(e)", accessor: 'roomComp',  filter : true },
                        { Header: "Dozent*innen", accessor: 'dozentComp',  filter : true },
                        { Header: "Aktion", accessor: 'aktion'}
                        ,]}
                    data={[]
                    } />
            </Card.Body>
               </div>  }

    </Card>
}

export default ClassbookMarkWidget;
