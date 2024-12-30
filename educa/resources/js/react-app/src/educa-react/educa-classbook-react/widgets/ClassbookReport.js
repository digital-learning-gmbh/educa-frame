import React, {Component, useEffect, useRef, useState} from 'react';
import {Alert, Card, Container, Row} from "react-bootstrap";
import {useSelector} from "react-redux";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import ReactTabulator from "react-tabulator/lib/ReactTabulator";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import Select from "react-select";
import Button from "react-bootstrap/Button";
import {SelectPlaceholder} from "../../helpers/EducaHelper";

function ClassbookReport(props) {

    let [isStudent, setIsStudent] = useState(false)
    let [reports, setReports] = useState([])
    let [report, setReport] = useState(null)
    let [reportID, setReportID] = useState(null)
    let [reportToken, setReportToken] = useState(null)

    let store = useSelector(state => state)

    useEffect(() => {
        setReports([])
        const me = store.currentCloudUser
        if(!me.student)
        {
            setIsStudent(false)
            return
        }
        setIsStudent(true)
    }, [])


    let processReport = () => {

        console.log("test")
        setReportToken(null);
        const me = store.currentCloudUser
        AjaxHelper.generateReportTemplate(report,me.student.id, "student")
            .then(function (resp)
            {
                if(resp.payload?.download_cache)
                {
                    setReportToken(resp.payload?.download_cache?.token);
                    return
                }
                throw new Error(resp?.message)
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Das Dokument konnte nicht erzeugt werden.")
            })
    }

    let downloadReport = () => {
        window.open( AjaxHelper.getReportTemplateUrl(reportToken),"_blank").focus();
    }


    return <Card>
        <Card.Header style={{ backgroundColor: "#fff"}}>
            <h5 className="card-title"><b><i className="fas fa-file-signature"/> Bescheinigungen</b>
            </h5>
        </Card.Header>
        <Card.Body>
            <Select
                components={{SelectPlaceholder}}
                isClearable={true}
                styles={{
                    // Fixes the overlapping problem of the component
                    menu: provided => ({...provided, zIndex: 9999})
                }}
                value={reports.find( l => l === report)}
                noOptionsMessage={() => "Keine Bescheinigungen verfügbar"}
                placeholder={"Name der Bescheinigung"}
                onChange={(obj) => {
                    setReportID(null)
                    setReport(obj?.value)
                }
                }
                options={reports}
            />
        </Card.Body>
        <Card.Footer>
            <Button className={"m-1"}
                    onClick={processReport}
            >Erzeugen</Button>
            { reportToken ? <Button className={"m-1"} variant={"success"}
                                 onClick={downloadReport}
            >Download</Button> : <></> }
        </Card.Footer>
    </Card>

}

export default ClassbookReport;
