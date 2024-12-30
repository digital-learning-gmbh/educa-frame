import React, {useEffect, useRef, useState} from 'react';
import FullCalendar from "@fullcalendar/react";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import EducaModal, {MODAL_BUTTONS} from "../../shared/shared-components/EducaModal";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import EventEditor from "./EventEditor";
import EducaHelper from "../helpers/EducaHelper";
import EducaClassbook from "../educa-classbook-react/EducaClassbook";
import moment from "moment";
import listPlugin from "@fullcalendar/list";
import EducaCalendarYearPlan from "./EducaCalendarYearPlan";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper.js";


export const EDUCA_CALENDAR_TIME_FRAME_DAYS = 365

export const EDUCA_CALENDAR_DEFAULT_START_DATE = moment().subtract(EDUCA_CALENDAR_TIME_FRAME_DAYS, "days").unix()
export const EDUCA_CALENDAR_DEFAULT_END_DATE = moment().add(EDUCA_CALENDAR_TIME_FRAME_DAYS, "days").unix()

export const INVITATION_STATES =
    {
        PERHAPS: 0,
        ACCEPTED: 1,
        DECLINED: 2
    }

export function EducaCalendar(props) {
    let isMounted = false
    let [startDate, setStartDate] = useState(EDUCA_CALENDAR_DEFAULT_START_DATE);
    let [endDate, setEndDate] = useState(EDUCA_CALENDAR_DEFAULT_END_DATE);
    let events = props.events;
    let [eventToEdit, setEventToEdit] = useState(null);
    let [viewMode, setViewMode] = useState("calendar");
    let [occurrenceDate, setOccurrenceDate] = useState(null);
    let [newEventDateRange, setNewEventDateRange] = useState(null);
    let [showEventEditor, setShowEventEditor] = useState(false);
    let modalRef = useRef()
    let calendarRef = useRef()

    let [canCreate, setCanCreate] = useState(props.canCreate)
    let [canEdit, setCanEdit] = useState( props.canEdit )

    const [translate] = useEducaLocalizedStrings()

    useEffect(() =>
    {
        if( props.canCreate !== canCreate )
            setCanCreate(props.canCreate )
        if( props.canEdit !== canEdit )
            setCanEdit(props.canEdit )
    },[props.canCreate, props.canEdit])

    //If this ID changed and is unequal to the currently selected event: update
    useEffect(() => {

        if( props.selectedUniqueId && props.selectedUniqueId != eventToEdit?.unique_id) // lessonPlan and lesson Type event
        {
                let desiredEvent = props.events.find(event => event.unique_id == props.selectedUniqueId)
                if (desiredEvent) {
                    setEventToEdit(desiredEvent)
                    setShowEventEditor(true)
                    if(desiredEvent.start)
                        calendarRef?.current?.getApi().gotoDate(desiredEvent.start)
                }
        }
     else if (props.selectedEventId && (props.selectedEventId != eventToEdit?.id || props.selectedOccurenceDate != occurrenceDate)) {

            setOccurrenceDate(props.selectedOccurenceDate)
            let desiredEvent = props.events.find(event => (event.id == props.selectedEventId && (props.selectedOccurenceDate == null || moment(event.start).isSame(moment(props.selectedOccurenceDate)))))
            if (desiredEvent) {
                setEventToEdit(desiredEvent)
                setShowEventEditor(true)
                if(desiredEvent.start)
                    calendarRef?.current?.getApi().gotoDate(desiredEvent.start)
            }
        }

    }, [props.selectedEventId, props.selectedUniqueId, props.events, props.selectedOccurenceDate])

    /**
     * Whenever props.toggleNewEvent changes, this effect is triggered ( side effect handling -> useEffect. its like "componentDidMount"
     */
    useEffect(() => {
        if (props.toggleNewEvent) // new event creation triggered from outside
        {
            setEventToEdit(null);
            setShowEventEditor(true)
            props.newEventOpenedCallback();
        }
    }, [props.toggleNewEvent]);

    // effect on startDate and endDate -> load events if state updated
    useEffect(() => {
        loadEvents()
    }, [startDate, endDate])


    //Listen to eventToEdit to notify the parent, that the event changed (URL parameter)
    useEffect(() => {
        props.selectionChanged(eventToEdit, occurrenceDate)
    }, [eventToEdit, occurrenceDate])

    let loadEvents = () => {
        props.loadEventsFunc(startDate, endDate)
    }

    const isClassbookEvent = () =>
    {
        return eventToEdit?.type === "lessonPlan" || eventToEdit?.type === "lesson"
    }

    /**
     * Called whenever a user navigates through the calendar
     * @param dateSet
     */
    let onDateSetsChanged = (dateSet) => {

        if(!canCreate)
            return
        let newEndDate = null
        let newStartDate = null
        // If new date range is beyond the current frame
        if (moment(dateSet.endStr).unix() - endDate > 0 || startDate - moment(dateSet.startStr).unix() > 0) {
            newEndDate = moment(dateSet.startStr).add(EDUCA_CALENDAR_TIME_FRAME_DAYS, "days").unix()
            newStartDate = moment(dateSet.startStr).subtract(EDUCA_CALENDAR_TIME_FRAME_DAYS, "days").unix()
            setStartDate(newStartDate)
            setEndDate(newEndDate)
            loadEvents()
        }

    }

    let getEventById = (id) => {
        for (let i = 0; i < events.length; i++)
            if (events[i].id == id) //Do not ===
            {
                return events[i]
            }
    }


    let dragDropYesNoCallback = (btn, eventDropOrResizeInfo, isResize) => {

        if (btn === MODAL_BUTTONS.YES) {
            if (!eventDropOrResizeInfo || !eventDropOrResizeInfo.event || !eventDropOrResizeInfo.event._def || !eventDropOrResizeInfo.event._def.publicId)
                return eventDropOrResizeInfo.revert()

            let evt = events.find( e => e.id == eventDropOrResizeInfo.event._def.publicId)
            if (!evt)
                return SharedHelper.fireErrorToast("Fehler", "Fehler beim verschieben des Termins")

            //Add deltas
            let newStart = moment(evt.start)
            let newEnd = moment(evt.end)
            let startKey = "delta"
            let endKey = "delta"
            if(isResize)
            {
                startKey = "startDelta"
                endKey = "endDelta"
            }

            newStart.add(eventDropOrResizeInfo[startKey].days, "days").add(eventDropOrResizeInfo[startKey].milliseconds, "milliseconds").add(eventDropOrResizeInfo[startKey].months, "months").add(eventDropOrResizeInfo[startKey].years, "years")

            newEnd.add(eventDropOrResizeInfo[endKey].days, "days").add(eventDropOrResizeInfo[endKey].milliseconds, "milliseconds").add(eventDropOrResizeInfo[endKey].months, "months").add(eventDropOrResizeInfo[endKey].years, "years")

            evt.end = newEnd.toDate()
            evt.start = newStart.toDate()
            AjaxHelper.moveEvent(evt)
                .then(resp => {
                    if (resp.status > 0) {
                        //let event = resp.payload.event
                        setShowEventEditor(false) // quickfix
                        //setEventToEdit(event)
                        EducaHelper.fireSuccessToast("Erfolg", isResize? "Die Zeiten wurden angepasst." : "Der Termin wurde verschoben")
                        // to be secure that all is fine
                        loadEvents();
                        return
                    } else
                        throw new Error(resp.message)
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Fehler beim Übertragen des Termins. " + err.message)
                })

        } else
            eventDropOrResizeInfo.revert()
    }

    let handleEventDragDrop = (eventDropInfo) => {
        if(isClassbookEvent())
            return eventDropInfo.revert()

        if(!canEdit)
            return

        modalRef.current.open((btn) => dragDropYesNoCallback(btn, eventDropInfo), "Termin verschieben", "Soll der Termin wirklich verschoben werden?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO])
    }

    let handleEventResize = (resizeInfo) =>
    {
        if( resizeInfo?.endDelta && resizeInfo?.event )
        {
            modalRef.current.open(
                (btn) => dragDropYesNoCallback(btn, resizeInfo, true),
                "Termin ändern", "Soll der Termin wirklich" +" "+(resizeInfo.endDelta.milliseconds>0? "verlängert" : "verkürzt")+" "+"werden?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO])
            return
        }
        resizeInfo?.revert()
    }


    let onDateClicked = (dateClickInfo) => {
        if(!canCreate)
            return
        setShowEventEditor(true)
        setEventToEdit(null)
        setNewEventDateRange({start: dateClickInfo.date})
    }

    let onDateRangeSelected = (selectionInfo) => {
        if(!canCreate)
            return  setShowEventEditor(false)
        setShowEventEditor(true)
        setEventToEdit(null)
        setNewEventDateRange({start: selectionInfo.start, end: selectionInfo.end})
    }

    let onEventClicked = (eventClickInfo) => {
        if (!eventClickInfo || !eventClickInfo.event || !eventClickInfo.event._def || !eventClickInfo.event._def.publicId)
            return
        let eventToEdit

        if( eventClickInfo.event._def.extendedProps?.unique_id) // lessonplan or lesson
        {
            eventToEdit = props.events?.find( e => e.unique_id === eventClickInfo.event._def?.extendedProps?.unique_id)
        }
        else if( eventClickInfo.event._def.extendedProps?.type === "single-appointment")
        {
            eventToEdit = getEventById(eventClickInfo.event._def.publicId)?.appointment
        }
        else
        {
            eventToEdit = getEventById(eventClickInfo.event._def.publicId)
        }

        if (eventToEdit) {
            setEventToEdit(eventToEdit);
            if(eventClickInfo.event._def.extendedProps?.type === "single-appointment")
            {
                setOccurrenceDate(moment(eventClickInfo.event._def.extendedProps?.occurrence_date).unix())
            } else {
                setOccurrenceDate(moment(eventClickInfo.event.start).unix())
            }
            setShowEventEditor(true)
        }
    }


    let onEventUpdated = (evt, close = true) => {
        //setEventToEdit(evt) //update editor
        if(close) {
            setShowEventEditor(false);
            setEventToEdit({}) //Trick #17 (non-null)
        }

        loadEvents()// client sided instead of server call?
    }

    let onEventCreated = (evt) => {
        console.log("onEventCreated", evt)
        setEventToEdit(evt) //update editor
        loadEvents()// client sided instead of server call?
    }

    let onEventDeleted = (evt) => {
        setShowEventEditor(false);
        setEventToEdit({}) //Trick #17 (non-null)
        loadEvents()// client sided instead of server call?
    }

    let eventCancelCallback = () => {
        setShowEventEditor(false)
    }

    const eventRender = (arg, element, view) => {
        if(arg?.view?.type !== "timeGridWeek" && arg?.view?.type !== "timeGridDay") {
            if (arg.event.extendedProps?.eventClass === 'exam')
                return <div className={"p-1 fc-daygrid-event fc-daygrid-dot-event fc-event fc-event-draggable fc-event-resizable fc-event-start fc-event-end fc-event-today"} style={{backgroundColor: arg?.event?.backgroundColor, color: "#fff", overflow: "hidden"}}>
                    <div className="fc-event-title">{arg.event.title}</div>
                </div>;

            const range = arg.event?._instance?.range
            if (!range) return
            return <div className={"p-1 fc-daygrid-event fc-daygrid-dot-event fc-event fc-event-draggable fc-event-resizable fc-event-start fc-event-end fc-event-today"} style={{backgroundColor: arg?.event?.backgroundColor, color: "#fff", overflow: "hidden"}}>
                <div className="fc-event-time">{moment(range.start).utc().format('HH:mm')}</div>
                <div className="fc-event-title">{arg.event.title}</div>
            </div>;
        }
        if (arg.event.display === 'background')
            return <BreakContent event={arg.event} />

        if (arg.event.extendedProps?.type === 'lessonPlan' || arg.event.extendedProps?.type === 'lesson')
            return (
            <LessonContent
                event={arg.event}
            />
        )

        if (arg.event.extendedProps?.eventClass === 'exam')
            return (
                <ExamContent
                    event={arg.event}
                />
            )

        return (
            <DefaultContent
                event={arg.event}
            />
        )
    }

    return (
        <div>
            {viewMode == "calendar" ?
            <div className={"d-flex"}>
                <div className={"col"} style={{padding: "0px"}}>
                    <FullCalendar
                        aspectRatio={2}
                        plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin]}
                        initialView="timeGridWeek"
                        themeSystem="bootstrap"
                        contentHeight="calc(100vh - 200px)"
                        eventClick={(eventClickInfo) => onEventClicked(eventClickInfo)}
                        dateClick={(dateClickInfo) => onDateClicked(dateClickInfo)}
                        select={(selectionInfo) => onDateRangeSelected(selectionInfo)}
                        locale="de"
                        slotEventOverlap={false}
                        eventDrop={(eventDropInfo) => {
                            handleEventDragDrop(eventDropInfo)
                        }}
                        eventResize={(resizeInfo) =>  handleEventResize(resizeInfo)}
                        ref={calendarRef}
                        selectable={canCreate}
                        editable={canEdit || canCreate}
                        datesSet={(datesSet) => {
                            onDateSetsChanged(datesSet)
                        }}
                        headerToolbar={{
                            start: 'title',
                            end: 'jahresplanerButton, listMonth,listWeek dayGridMonth,timeGridWeek,timeGridDay,todayCustom, prev,next',
                        }}
                        slotMinTime={"07:30:00"}
                        slotMaxTime={"20:00:00"}
                        slotDuration={"00:10:00"}
                        snapDuration={"00:05:00"}
                        allDayText={translate("calender.whole_day","Ganztägig")}
                        eventDisplay={"block"}
                        customButtons={
                            {
                                todayCustom:
                                    {
                                        text: translate("calender.today","Heute"),
                                        click(ev, element) {
                                            calendarRef.current?.getApi()?.today()
                                        },
                                    },
                                jahresplanerButton: {
                                    text: translate("calender.year_plan","Jahresplaner"),
                                    click: function() {
                                        setViewMode("yearplaner");
                                    },
                                    bootstrapFontAwesome: "fa-calendar-check"
                                },
                            }}
                        buttonText={{
                            today: translate("calender.year_plan","Jahresplaner"),
                            month: translate("calender.month","Monat"),
                            week: translate("calender.week","Woche"),
                            day: translate("calender.day","Tag"),
                            listWeek: translate("calender.list_week","Wochen-Liste"),
                            listMonth: translate("calender.list_month","Monats-Liste"),
                        }}
                        businessHours={{
                            // days of week. an array of zero-based day of week integers (0=Sunday)
                            daysOfWeek: [1, 2, 3, 4, 5],
                            startTime: '06:00', // a start time (10am in this example)
                            endTime: '18:00', // an end time (6pm in this example)
                        }}
                        eventContent={eventRender}
                        scrollTime="08:00:00"
                        firstDay={1}
                        weekNumbers={true}
                        events={events.map( e => !canEdit? {...e, editable : false} : e).filter( e=> e !== null)}
                    />
                </div>
                {!isClassbookEvent() && showEventEditor ?
                    <div className="col-6">
                        <EventEditor
                            event={eventToEdit}
                            occurrenceDate={occurrenceDate}
                            canEdit={canEdit}
                            newEventDateRange={newEventDateRange}
                            newEventPreselectedSections={props.preselectedSections}
                            fixedGroup={null}
                            hideChoice={false}
                            eventDeletedCallback={(evt) => onEventDeleted(evt)}
                            eventUpdatedCallback={(evt, close) => onEventUpdated(evt, close)}
                            eventCreatedCallback={(evt) => onEventCreated(evt)}
                            cancelClickCallback={() => eventCancelCallback()}
                        />
                    </div> : null}
                {isClassbookEvent() &&  showEventEditor?
                    <div className="col-6">
                    <EducaClassbook
                        closeCallback={() => setShowEventEditor(false)}
                        event={eventToEdit}
                    />
                    </div> : null}
                <EducaModal ref={modalRef}/>
            </div> : <div className={"fc"}>
                    <div className="fc-header-toolbar fc-toolbar ">
                        <div className="fc-toolbar-chunk"><h2 className="fc-toolbar-title" id="fc-dom-16">{translate("calender.year_plan","Jahresplaner")}</h2></div>
                        <div className="fc-toolbar-chunk"></div>
                        <div className="fc-toolbar-chunk">
                            <div className="fc-button-group">
                                <button type="button" title="[object Object]" aria-pressed="false"
                                        className="fc-jahresplanerButton-button fc-button fc-button-primary fc-button-active"><i
                                    className="fa fa fa-calendar-check"></i>{translate("calender.year_plan","Jahresplaner")}
                                </button>
                                <button type="button" aria-pressed="false"
                                        className="fc--button fc-button fc-button-primary"></button>
                            </div>
                            <div className="fc-button-group">
                                <button type="button" title="Monats-Liste view" aria-pressed="false"
                                        className="fc-listMonth-button fc-button fc-button-primary" onClick={() => { setViewMode("calendar")
                                    setTimeout(function (){     calendarRef.current?.getApi()?.changeView('listMonth'); },100);
                                        }}>{translate("calender.list_month","Monats-Liste")}
                                </button>
                                <button type="button" title="Wochen-Liste view" aria-pressed="false"  onClick={() => { setViewMode("calendar")
                                    setTimeout(function (){     calendarRef.current?.getApi()?.changeView('listWeek'); },100);
                                }}
                                        className="fc-listWeek-button fc-button fc-button-primary">{translate("calender.list_week","Wochen-Liste")}
                                </button>
                            </div>
                            <div className="fc-button-group">
                                <button type="button" title="Monat view" aria-pressed="false"  onClick={() => { setViewMode("calendar")
                                    setTimeout(function (){     calendarRef.current?.getApi()?.changeView('dayGridMonth'); },100);
                                }}
                                        className="fc-dayGridMonth-button fc-button fc-button-primary">{translate("calender.month","Monat")}
                                </button>
                                <button type="button" title="Woche view" aria-pressed="true" onClick={() => { setViewMode("calendar")
                                    setTimeout(function (){     calendarRef.current?.getApi()?.changeView('timeGridWeek'); },100);
                                }}
                                        className="fc-timeGridWeek-button fc-button fc-button-primary ">{translate("calender.week","Woche")}
                                </button>
                                <button type="button" title="Tag view" aria-pressed="false"  onClick={() => { setViewMode("calendar")
                                    setTimeout(function (){     calendarRef.current?.getApi()?.changeView('timeGridDay'); },100);
                                }}
                                        className="fc-timeGridDay-button fc-button fc-button-primary">{translate("calender.day","Tag")}
                                </button>
                                <button type="button" title="Heute" aria-pressed="false"
                                        className="fc-todayCustom-button fc-button fc-button-primary">{translate("calender.today","Heute")}
                                </button>
                                <button type="button" aria-pressed="false"
                                        className="fc--button fc-button fc-button-primary"></button>
                            </div>
                            <div className="fc-button-group">
                                <button type="button" title="Previous Woche" aria-pressed="false"
                                        className="fc-prev-button fc-button fc-button-primary"><span
                                    className="fc-icon fc-icon-chevron-left"></span></button>
                                <button type="button" title="Next Woche" aria-pressed="false"
                                        className="fc-next-button fc-button fc-button-primary"><span
                                    className="fc-icon fc-icon-chevron-right"></span></button>
                            </div>
                        </div>
                    </div>
                    <EducaCalendarYearPlan
                        newEventPreselectedSections={props.preselectedSections}
                        events={events}
                        group={props.group}
                        loadEvents={() => {loadEvents()}}
                    />
            </div> }
        </div>
    );
}

const LessonContent = (props) => {
    const evt = props.event?._def
    const range = props.event?._instance?.range
    if (!evt || !range) return 'Ein Fehler ist aufgetreten'

    const content = (
        <div
            style={{
                display: 'flex',
                flexDirection: 'column',
                height: '100%',
                color: evt?.extendedProps.eventTextColor,
                backgroundColor: evt?.ui?.backgroundColor, padding: "3px", borderRadius: "2px"
            }}
        >
            <h6 style={{ marginBottom: '0px' }}>
                <b>{evt.title}</b>
                <div style={{ fontSize: '0.8em', float: 'right' }}>
                    {moment(range.start).utc().format('HH:mm')} -{' '}
                    {moment(range.end).utc().format('HH:mm')}
                </div>
            </h6>
            <i>{evt?.extendedProps?.subtitle}</i>
            {evt?.extendedProps?.dozent?.length > 0 ? (
                <i
                    style={{
                        fontSize: '0.75em',
                        textOverflow: 'ellipsis',
                        whiteSpace: 'nowrap',
                        overflow: 'hidden'
                    }}
                >
                    <i className='fa fa-user-tie mr-1' />
                    {evt?.extendedProps?.dozent}
                </i>
            ) : null}
            {evt?.extendedProps?.raum?.length > 0 ? (
                <i
                    style={{
                        fontSize: '0.75em',
                        textOverflow: 'ellipsis',
                        whiteSpace: 'nowrap',
                        overflow: 'hidden'
                    }}
                >
                    <i className='fa fa-home mr-1' />
                    {evt?.extendedProps?.raum}
                </i>
            ) : null}
            {evt?.extendedProps?.klassen_name?.length > 0 ? (
                <i
                    style={{
                        fontSize: '0.75em',
                        textOverflow: 'ellipsis',
                        whiteSpace: 'nowrap',
                        overflow: 'hidden'
                    }}
                >
                    <i className='fa fa-users mr-1' />
                    {evt?.extendedProps?.klassen_name.join(', ')}
                </i>
            ) : null}
        </div>
    )
    return content

}

const DefaultContent = (props) => {
    const evt = props.event?._def
    const range = props.event?._instance?.range
    if (!evt || !range) return 'Ein Fehler ist aufgetreten'

    const content = (
        <div
            style={{
                display: 'flex',
                flexDirection: 'column',
                height: '100%',
                color: evt?.extendedProps.eventTextColor
            }}
        >
            <h6 style={{ marginBottom: '0px' }}>
                <b>{evt.title}</b>
                <div style={{ fontSize: '0.8em', float: 'right' }}>
                    {moment(range.start).utc().format('HH:mm')} -{' '}
                    {moment(range.end).utc().format('HH:mm')}
                </div>
            </h6>
        </div>
    )
    return content

}

const ExamContent = (props) => {
    const evt = props.event?._def
    const range = props.event?._instance?.range
    if (!evt || !range) return 'Ein Fehler ist aufgetreten'

    const content = (
        <div
            style={{
                display: 'flex',
                flexDirection: 'column',
                height: '100%',
                color: evt?.extendedProps.eventTextColor
            }}
        >
            <h6 style={{ marginBottom: '0px' }}>
                <b>{evt.title}</b>
            </h6>
        </div>
    )
    return content

}

const BreakContent = (props) => {
    return (
        <div style={{ textAlign: 'center' }}>
            <p className='text-muted m-1'>{props.event.title}</p>{' '}
        </div>
    )
}

export default EducaCalendar;
