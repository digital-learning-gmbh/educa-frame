import React, {Component, useEffect, useState} from 'react';
import "./yearCalender.css"
import moment from "moment";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import EducaHelper from "../helpers/EducaHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";
import {EducaLoading} from "../../shared-local/Loading";
import {
    darkColors,
    FloatingButton,
    FloatingContainer,
    FloatingLink
} from "../educa-components/EducaFloatingButton/FloatingButton";
import SectionCreateAppointmentModal from "../educa-group-react/group-section-apps/SectionCreateAppointmentModal";

export default function EducaCalendarYearPlan(props) {
    let [isLoading, setIsLoading] = useState(false);
    let [events, setEvents] = useState([]);
    let [months, setMonths] = useState([]);
    let [eventToEdit, setEventToEdit] = useState(null);
    let [occurrenceDate, setOccurrenceDate] = useState(null);
    let [newEventDateRange, setNewEventDateRange] = useState(null);
    let [showExamEditor, setShowExamEditor] = useState(false)
    let [cache,setCache] = useState(null)
    moment.locale("de");
    let [startMonth, setStartMonth] = useState(moment().date(1).month(0))
    let [endMonth, setEndMonth] = useState(moment().date(1).month(0).add(12,"M")) // maybe config


    useEffect(() => {
        if(props.group?.schoolSettings?.find((e) => e.key === "yearcalendar_start_month"))
        {
            setEndMonth(moment().date(1).month(parseInt(props.group?.schoolSettings?.find((e) => e.key === "yearcalendar_start_month").value) -1).add(12,"M"))
            setStartMonth(moment().date(1).month(parseInt(props.group?.schoolSettings?.find((e) => e.key === "yearcalendar_start_month").value)-1))

        }
    },[props.group])

    useEffect(() => {
        setEvents(props.events)
    },[props.events])

    useEffect(() => {
        setIsLoading(true)
        setCache([...Array(31).keys()].map((i) => {
            return <tr key={i}>
                {
                    months.map((m) => {
                        let currentCellDate = startMonth.clone().date(i+1).month(m.id)
                        let w = currentCellDate.format("E")
                        if(i < currentCellDate.daysInMonth()) {
                            let dayEvents = events.filter(e => {
                                let momentStart = moment(e.start)
                                return momentStart.isSame(currentCellDate,"day")
                            });
                            let longEvents = events.filter(e => {
                                let momentStart = moment(e.start)
                                let momentEnd = moment(e.end)
                                return moment(currentCellDate).isBetween(momentStart, momentEnd)
                            });
                            let bgColor = dayEvents?.length > 0 ? dayEvents[0].color : (longEvents?.length > 0 ? longEvents[0].color :  (w == 6 ? "#f0f8ff" : (w == 7 ? "#d9edf7" : "#fff")));
                            return <td key={m.id}
                                       style={{backgroundColor: bgColor, color:
                                               SharedHelper.isColorTooDark(bgColor) ? "#fff" : "#000" }}>
                                <div className={"yc-innerDivCal"}>
                                    <b onClick={() => onDateClicked(currentCellDate, dayEvents)}
                                       style={{cursor: 'pointer'}}>{currentCellDate.format("D")}. {currentCellDate.format("dd")}</b>
                                    {
                                        w == 1 ?
                                            <small
                                                className="float-right">{currentCellDate.format("w")}</small> : <></>
                                    }
                                    <ul style={{marginBottom: "0px"}} className={"cal-elementList"}>
                                        {
                                            dayEvents.map((e) => {
                                                return <li key={e.id}
                                                           onClick={() => onEventClicked(e)}
                                                           style={{cursor: 'pointer'}}> {e.title} </li>
                                            })
                                        }
                                    </ul>
                                </div>
                            </td>
                        } else {
                            return <td key={m.id}></td>
                        }
                    })
                }
            </tr>
        }))
    }, [months,events,startMonth]);

    useEffect(() => {
        setIsLoading(false)
    },[cache])

    useEffect(() => {
        let tempMonths =[];
        let cloneStart = startMonth.clone()
        let i = 0;
        while (cloneStart.isBefore(endMonth))
        {
            tempMonths.push({ id: i, value: cloneStart.format("MMMM, YY") });
            cloneStart =  cloneStart.add(1,"M")
            i++;
        }
        setMonths(tempMonths);
    },[events,startMonth])

    const print = () =>
    {
        var mywindow = window.open('', 'PRINT');

        let title = ""

        mywindow.document.write('<html><head><title></title>');
        mywindow.document.write('<style>' +
            '\n' +
            '            table {\n' +
            '                page-break-inside: auto\n;  font-size: 1rem;' +
            '            }\n' +
            '\n' +
            '            tr {\n' +
            '                page-break-inside: avoid;\n' +
            '                page-break-after: auto\n' +
            '            }\n' +
            '\n' +
            '            thead {\n' +
            '                display: table-header-group\n' +
            '            }\n' +
            '\n' +
            '            tfoot {\n' +
            '                display: table-footer-group\n' +
            '            }\n' +
            '\n' +
            '            body {\n' +
            '                background-color: #fff !important;\n' +
            '            }\n' +
            '\n' +
            '            html, body {\n' +
            '                height: 100%;\n' +
            '                width: 100%;\n' +
            '                margin: 0;\n' +
            '                padding: 0;\n' +
            '                font-size: 8px !important;\n' +
            'background-color: #fff !important; font-size: 12px;' +
            '            }\n' +
            '\n' +
            '            @page {\n' +
            '                size: A4 landscape;\n' +
            '                max-height: 100%;\n' +
            '                max-width: 100%\n' +
            '            }\n' +
            '\n' +
            '            .cal-elementList {' +
            "padding-left: '0px'; padding-right: '0px';" +
            '} ' +
            '' +
            '' +
            'ul li {\n' +
            '                font-size: 6px !important;\n' +
            '                list-style-type: none;\n' +
            '            }\n' +
            '\n' +
            '            ul {\n' +
            '                padding-inline-start: 2px;\n' +
            '\n' +
            '            }\n' +
            '\n' +
            '            .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {\n' +
            '                padding: 2px;\n' +
            '\n' +
            '            }\n' +
            '\n' +
            '            h1 {\n' +
            '                font-size: 8px;\n' +
            '                margin-top: 0px;\n' +
            '                margin-bottom: 5px;\n' +
            '            }\n' +
            '\n' +
            '            .table-bordered, .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > thead > tr > th {\n' +
            '                border: 0.5px solid #000;\n' +
            '            }\n' +
            '.yc-headerColumn { width: 8% !important;}  .yc-innerDivCal { height:16px !important; overflow: hidden; word-wrap: break-word; word-break:break-word; }' +
            '' +
            '' +
            '\n' +
            '            td {\n' +
            '                height: 18px !important;\n' +
            '            }\n' +
            '\n' +
            '            ul {\n' +
            '                height: 8px !important;\n' +
            '                overflow: hidden;\n' +
            '            }\n' +
            '\n' +
            '            .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {\n' +
            '                line-height: 1 !important;\n' +
            '            }' +
            '' +
            '</style>')
        mywindow.document.write('<link href="/css/app_branded.css" media="screen, print" rel="stylesheet"></head><body style="background-color: #fff !important;" >');
        mywindow.document.write('<h2>' + title + '</h2>');
        mywindow.document.write('<div media="print">')
        mywindow.document.write(document.getElementById("section-to-print").outerHTML);
        mywindow.document.write('</div></body></html>');
        mywindow.document.close();

        setInterval( () => {
            mywindow.focus();
            mywindow.print();
            mywindow.close();
        },1000 );

        return true;
    }

    let onEventClicked = (event) => {
        console.log("Event clicked: ", event)

        if( event.eventClass !== 'exam')
        {
            SharedHelper.fireWarningToast("Jahresplaner", "Hier kannst du nur Klausurtermine ändern.")
        }
        else
        {
            setEventToEdit(event);
            setOccurrenceDate(moment(event.start).unix())
            setNewEventDateRange({start: event.start, end: event.end})
            setShowExamEditor(true)
        }

    }

    let onDateClicked = (dateClickInfo, dayEvents) => {
        if(!FliesentischZentralrat.globalCalendarView())
            return
        for (const evt of dayEvents) {
            if (evt.eventClass === "exam") {
                let errorOutput = "An diesem Datum findet bereits eine andere Klausur statt: " + evt.title
                SharedHelper.fireErrorToast("Blockiert!", errorOutput)
                return
            }
        }
        setEventToEdit({})
        setOccurrenceDate(dateClickInfo.toDate())
        setShowExamEditor(true)
    }

    const updateExamDate = (evt, close) => {
        setEventToEdit(evt)
        if (close) {
            setShowExamEditor(!showExamEditor)
            setEventToEdit({})
        }
        props.loadEvents()
    }

    const createExamDate = (evt) => {
        setShowExamEditor(!showExamEditor)
        setEventToEdit(evt)
        props.loadEvents()
    }

    const deleteExamDate = (evt) => {
        setShowExamEditor(false)
        setEventToEdit({})
        props.loadEvents()
    }

    const eventCancelCallback = () => {
        setShowExamEditor(false)
        setEventToEdit({})
    }

    if(isLoading)
        return <EducaLoading style={{top:"10%"}}/>
    return (
        <div style={{ backgroundColor: "#fff", overflowX: "auto"}}>

            <FloatingContainer>
                {/*<FloatingLink href="#"*/}
                {/*      onClick={() => {}}*/}
                {/*      tooltip="Termin erstellen"*/}
                {/*      icon="far fa-plus" />*/}
                <FloatingLink href="#"
                              onClick={() => setShowExamEditor(true)}
                              tooltip="Klausurtermin erstellen"
                              icon="far fa-calendar-plus"
                              iconStyles={{
                                  color: darkColors.white}}
                              styles={{
                                  backgroundColor: darkColors.lightBlue,
                                  color: darkColors.black,
                                  textDecoration: "none",
                                  border: "none"}}/>
                <FloatingLink href="#"
                              onClick={() => print()}
                              tooltip="Ansicht drucken"
                              icon="fas fa-print"
                              iconStyles={{
                                  color: darkColors.white}}
                              styles={{
                                  backgroundColor: darkColors.lightBlue,
                                  color: darkColors.black,
                                  textDecoration: "none",
                                  border: "none"}}/>
                <FloatingButton
                    icon="fas fa-plus"
                    rotate={true} />
            </FloatingContainer>
            <table id="section-to-print" className="table table-bordered" style={{tableLayout: "fixed", borderTop: "none"}}>
                <thead className="bg-dark text-light">
                <tr style={{border:"none"}}>
                    { months.map((m) => {
                        return <th key={m.id} className={"yc-headerColumn"}>{m.value}</th>
                    })}
                </tr>
                </thead>
                <tbody>
                { cache }
                </tbody>
            </table>
            <div style={{display: 'flex', flex: 1}}>
                <SectionCreateAppointmentModal
                    show={showExamEditor}
                    onDelete={(evt) => deleteExamDate(evt)}
                    onUpdate={(evt, close) => updateExamDate(evt, close)}
                    onCreate={(evt) => createExamDate(evt)}
                    onClose={eventCancelCallback}
                    eventToEdit={eventToEdit}
                    occurrenceDate={occurrenceDate}
                    eventDateRange={newEventDateRange}
                    newEventPreselectedSections={props.newEventPreselectedSections}
                    group={props.group}
                />
            </div>
        </div>
    )
}
