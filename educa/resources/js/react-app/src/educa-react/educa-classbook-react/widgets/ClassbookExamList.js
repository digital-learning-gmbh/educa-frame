import React, {Component, useEffect, useRef, useState} from 'react';
import {Alert, Card, Container, Row} from "react-bootstrap";
import {useSelector} from "react-redux";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {EducaLoading} from "../../../shared-local/Loading";
import {EducaDefaultTable} from "../../../shared/shared-components/Tables";

const EXAM_TYPES =
    [{label: "Erstprüfung", value: "first_exam"},
        {label: "Wiederholungsprüfung", value: "repeat_exam"},
        {label: "mündliche Prüfung", value: "oral_exam"},
    ]


export function ClassbookExamList(props) {

    let [ data, setData ] = useState([])
    let [ isLoading, setIsLoading ] = useState(true)

    let store = useSelector(state => state)

    useEffect(() => {
        loadExamDates()
    }, [])

    let loadExamDates = () =>
    {
        const me = store.currentCloudUser
        if(!me.student)
            return

        setIsLoading(true)
        AjaxHelper.getExamDates(me.student.id)
            .then( resp =>
            {
                if( resp.status > 0 && resp?.payload?.dates)
                {
                    setData(resp.payload.dates)
                    setIsLoading(false)
                    return
                }
                throw new Error(resp.message)
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Die Prüfungstermine konnten nicht geladen werden.")
            })
            .finally(() =>
            {
                setIsLoading(false)
            })
    }

    return <Card>
        <Card.Header style={{ backgroundColor: "#fff"}}>
            <h5 className="card-title"><b><i className="far fa-calendar-check"/> Prüfungstermine</b>
            </h5>
        </Card.Header>
        <Card.Body>
            <EducaDefaultTable
                size={"lg"}
                defaultPageSize={50}
                pagination={true}
                columnPicker={true}
                buttonPdfExport={true}
                buttonExcelExport={true}
                columns={[
                    { Header: 'Datum', accessor: 'semesterComp', filter : true, width : "100"},
                    { Header: 'Fächer', accessor: 'subjectComp', filter : true, width : "100"},
                    { Header: 'Prüfungsform', accessor: 'examPartLabel', filter : true, width : "100"},
                    { Header: "Prüfungsart", accessor: 'typeComp',  filter : true },
                    { Header: "Räum(e)", accessor: 'roomComp',  filter : true },
                    { Header: "Dozent*innen", accessor: 'dozentComp',  filter : true }
                    ,]}
                data={data ? data?.map( d => {
                    console.log(d)
                    return {
                        ...d,
                        semesterComp : moment(d.start).format("DD.MM.YYYY HH:mm") + " - " + moment(d.end).format("HH:mm"),
                        subjectComp : d.examParts.flatMap( d => d.subjects).map( sub => sub.name).join(", "),
                        roomComp : d.rooms.map(r => r.name).join(", "),
                        dozentComp: d.teacher.map(t => t.firstname + " " + t.lastname).join(", "),
                        examPartLabel: [...new Set(d.examParts.flatMap( d => d.exam_part_label).map( sub => sub.name))].join(", "),
                        typeComp : EXAM_TYPES.find( o => o.value == d?.exam_execution?.type)?.label
                    }
                    }) : []
                } />
        </Card.Body>
    </Card>

}
