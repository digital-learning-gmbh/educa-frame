import React, {Component} from 'react';
import {Card, Form, Collapse, Spinner, Tab, Tabs, Button} from "react-bootstrap";
import {CKEditor} from "@ckeditor/ckeditor5-react";

import DatePicker, {registerLocale} from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";

import de from "date-fns/locale/de"
import {connect} from "react-redux";
import {EducaCKEditorDefaultConfig, MODELS} from "../../shared/shared-helpers/SharedHelper";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import EducaModal, {MODAL_BUTTONS} from "../../shared/shared-components/EducaModal";
import {
    CloudIdSelectMultiple,
    RoomSelectMultiple,
    SectionSelectMultiple
} from "../../shared/shared-components/EducaSelects";
import EducaFileBrowser from "../educa-components/EducaFileBrowser/EducaFileBrowser";
import EducaHelper, {SelectPlaceholder} from "../helpers/EducaHelper";
import Select from "react-select";
import ReactSwitch from "react-switch";
import DatePickerBox from "../../shared/shared-components/DatePickerBox";
import moment from "moment";
import _ from "lodash";
import {MeetingInformation} from "../educa-components/EducaMeeting/MeetingInformation";
import {CirclePicker} from "react-color";
import {element} from "prop-types";
import EducaFileBrowserAdvanced from "../educa-components/EducaFileBrowser/EducaFileBrowserAdvanced.jsx";


registerLocale('de-DE', de); // MÜLL!!!

const SELECT_MINUTES = [-1,5,10,15,30,45,60,90,120, 60 * 24,  60 * 24 * 7] // 60 * 24 * 7 -> 1 Woche

let SELECT_MINUTES_OBJECTS = null

let SELECT_DISPLAY_OBJECTS = [ {value : "auto", label : "Normal"}, {value : "background", label : "Im Hintergrund (für TN)"}];

const SELECT_RHYTHM_OPTIONS = [{value : "weekly", label : "Wöchentlich"}, {value : "monthly", label : "Monatlich"}]
const SELECT_TURNUS_OPTIONS = [1,2,3,4].map( (i) => ({value : i, label : i}))

const DEFAULT_COLORS = [
    "#343A40",
    "#3490dc",
    "#f44336",
    "#e91e63",
    "#9c27b0",
    "#673ab7",
    "#3f51b5",
    "#2196f3",
    "#03a9f4",
    "#00bcd4",
    "#009688",
    "#4caf50",
    "#8bc34a",
    "#cddc39",
    "#ffeb3b",
    "#ff9800",
    "#ff5722",
    "#795548"
];

class EventEditor extends Component {

    constructor(props) {
        super(props);

        this.state = {
            id: null,
            eventClass: "",
            title: "",
            start: null,
            end: null,
            attendees: [],
            collisions: [],
            sections: [],
            blockedSections: [],
            location: "",
            rooms : [],
            description: "",
            protocol: "",
            color: "#3490dc",
            remember_minutes : -1,

            titleError: false,
            startError: false,
            endError: false,

            hasRepetition : false,
            repetitionUntil : moment().add(3,"month"),
            repetitionRhythm : SELECT_RHYTHM_OPTIONS[0],
            repetitionTurnus : SELECT_TURNUS_OPTIONS[0],

            currentTab: "general",
            isMeetingLoading: false,
            isEditable : props.event == null ? true : props.event.canEdit,

            occurrenceDate: null,

            single_appoint_id: null,
            single_appoint_title: "",
            single_appoint_start: null,
            single_appoint_end: null,
            single_appoint_exception_type: "move",
            single_appoint_location :  "",
            single_appoint_color:  "",
            single_appoint_description: "",
            single_appoint_rooms: [],
            single_appoint_protocol:  "",
            single_appoint_attendees: [],
            single_appoint_sections: [],
            single_appoint_remember_minutes: -1,
            singel_appoint_display: "auto",
            single_appoint_organisators: [],

            single_appoint_titleError: false,
            single_appoint_startError: false,
            single_appoint_endError: false,

        }
        this.calendarStartRef = React.createRef();
        this.calendarEndRef = React.createRef();

        this.calendarSingleStartRef = React.createRef();
        this.calendarSingleEndRef = React.createRef();

        this.modalRef = React.createRef()

        this.color = "#3490dc"

        if(!SELECT_MINUTES_OBJECTS)
        {
            SELECT_MINUTES_OBJECTS = SELECT_MINUTES.map( m =>
            {
                return m==-1
                    ? {value : -1, label : "Keine Erinnerung"}
                    : m == 24*60*7
                        ? { value : m, label : "1 Woche"}
                        :  m == 24*60 ? { value : m, label : "1 Tag"} : { value : m, label : m + " Minuten"}
            })
        }
    }

    componentDidMount() {
        if (this.props.event && this.props.event.id)
            this.prepareExistentEventComponent()
        else {
            this.prepareNewEventComponent()
        }

        this._isMounted = true
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.event?.id && (this.state.id !== this.props.event.id || this.props.occurrenceDate !== this.state.occurrenceDate)) // not a new Event
        {
            this.prepareExistentEventComponent()
        } else if (!this.props.event && this.state.id) {
            this.setState({currentTab: this.state.hasRepetition ? "single": "general"})
            this.prepareNewEventComponent()
        } else if (!this.props.event && this.props.newEventDateRange) {
         //   if(_.isEqual(prevProps, this.props))
           //     return
            let dateRange = this.props.newEventDateRange
            let newStart = null
            let newEnd = null
            if (dateRange.start && !moment(this.state.start).isSame(dateRange.start))// if the new start date is not the same as before
                newStart = moment(dateRange.start).toDate();

            if (dateRange.end && !moment(this.state.end).isSame(dateRange.end))// if the new start date is not the same as before
                newEnd = moment(dateRange.end).toDate();

            if (newStart != null && newEnd != null) // check if there is even a change
            {
                if (this.state.eventClass === "exam") {
                    newStart.setHours(0,0)
                    newEnd.setHours(23,59)
                }
                return this.setState({
                    start: newStart,
                    end: newEnd
                })
            }
        }

    }

    checkExamPlanningBlocked(sections, prepare=false) {
        let blockedSections = []
        sections?.forEach(section => {
            let group = this.props.store.currentCloudUser?.groups?.find((g) => g.id === section.group_id)
            let groupWithSetting = group.schoolSettings? group.schoolSettings : []
            if (!!groupWithSetting) {
                let blocked = groupWithSetting.find(obj => {
                    return obj.key === "yearplaner_blocked"
                })
                if (blocked)
                    blockedSections.push(group.name + ": " + section.name)
            }
        })

        if (prepare)
            return blockedSections
        this.setState({blockedSections: blockedSections, sections: sections})
    }

    /**
     * Reinit the component for a new event
     * @param newStart
     * @param newEnd
     */
    prepareNewEventComponent(newStart = null, newEnd = null) {

        if (this.props.hideChoice) {
            newStart?.setHours(0,0)
            newEnd?.setHours(23,59)
        }
        let blockedSections = []
        if (this.props.newEventPreselectedSections && (this.state.eventClass === 'exam' || this.props.hideChoice))
            blockedSections = this.checkExamPlanningBlocked(this.props.newEventPreselectedSections, true)
        let preSelected = this.props.newEventPreselectedSections ? this.props.newEventPreselectedSections : []

        return this.setState({
            currentTab:"general",
            id: null,
            title: "",
            start: newStart,
            end: newEnd,
            attendees: [],
            sections: preSelected,
            blockedSections: blockedSections,
            organisators: [],
            collisions: [],
            location: "",
            rooms : [],
            description: "",
            protocol: "",
            remember_minutes : -1,
            display: "auto",
            isEditable: true,
            eventClass: this.props.hideChoice? 'exam' : "",

            titleError: false,
            startError: false,
            endError: false,

            hasRepetition : false,
            repetitionUntil : moment().add(3,"month"),
            repetitionRhythm : SELECT_RHYTHM_OPTIONS[0],
            repetitionTurnus : SELECT_TURNUS_OPTIONS[0],

            single_appoint_id: null,
            single_appoint_location :  "",
            single_appoint_color:  "",
            single_appoint_description: "",
            single_appoint_title: "",
            single_appoint_rooms: [],
            single_appoint_protocol:  "",
            single_appoint_attendees: [],
            single_appoint_sections: preSelected,
            single_appoint_remember_minutes: -1,
            singel_appoint_display: "auto",
            single_appoint_organisators: [],

            single_appoint_titleError: false,
            single_appoint_startError: false,
            single_appoint_endError: false,
        })
    }

    /**
     * Init the component with an existing event coming from props
     */
    prepareExistentEventComponent() {
        this.setState({
            id: this.props.event.id,
            title: this.props.event.title,
            start: moment(this.props.event.start).toDate(),
            end: moment(this.props.event.end).toDate(),
            attendees: this.props.event.attendees,
            collisions: [],
            sections: this.props.event.sections,
            organisators: this.props.event.organisators,
            isEditable: this.props.event.editable,
            occurrenceDate: this.props.occurrenceDate,
            eventClass: this.props.event.eventClass
        })

        //Load details from server
        AjaxHelper.getEventDetails(this.props.event.id, this.props.occurrenceDate)
            .then(resp => {
                if (resp.payload && resp.payload.event) {
                    let obj = resp.payload.event;
                    let singleEvent = resp.payload.singleEvent;
                    this.processEventDetails(obj,singleEvent)

                } else
                    throw new Error(resp.message)

            })
            .catch(err => {
                console.log(err)
                EducaHelper.fireErrorToast("Fehler", "Event Details konnten nicht geladen werden.")
            })

    }

    processEventDetails(obj, singleEvent)
    {
        let blockedSections = []
        if (obj.eventClass === 'exam' && obj.sections) {
            blockedSections = this.checkExamPlanningBlocked(obj.sections, true)
        }
        let state = {
            ...this.state,

            attendees: obj.attendees,
            sections: obj.sections,
            blockedSections: blockedSections,
            organisators: obj.organisators? obj.organisators : [],
            rooms : obj.rooms? obj.rooms : [],
            location: obj.location ? obj.location : "",
            description: obj.description ? obj.description : "",
            protocol: obj.protocol ? obj.protocol : "",
            remember_minutes: obj.remember_minutes ? obj.remember_minutes : -1,
            color: obj.color,
            display: obj.display,
            isEditable: obj.editable,

            hasRepetition : !!obj.hasRepetition,
            repetitionUntil: obj.hasRepetition && obj.repetitionUntil? moment(obj.repetitionUntil) : moment().add(3,"month"),
            repetitionRhythm : obj.hasRepetition && obj.recurrenceType? SELECT_RHYTHM_OPTIONS?.find( e => e.value == obj.recurrenceType)? SELECT_RHYTHM_OPTIONS?.find( e => e.value == obj.recurrenceType) :SELECT_RHYTHM_OPTIONS[0]: SELECT_RHYTHM_OPTIONS[0],
            repetitionTurnus : obj.hasRepetition && obj.repetitionTurnus? SELECT_TURNUS_OPTIONS?.find( e => e.value == obj.repetitionTurnus)?SELECT_TURNUS_OPTIONS?.find( e => e.value == obj.repetitionTurnus) :SELECT_TURNUS_OPTIONS[0] : SELECT_TURNUS_OPTIONS[0],

            single_appoint_exception_type: singleEvent? singleEvent.exception_type : "move",
            single_appoint_id : singleEvent? singleEvent.id : null,
            single_appoint_location : singleEvent? singleEvent.location : "",
            single_appoint_color: singleEvent? singleEvent.color : "",
            single_appoint_description: singleEvent? singleEvent.description : "",
            single_appoint_title: singleEvent? singleEvent.title: "",
            single_appoint_rooms: singleEvent? singleEvent.rooms: [],
            single_appoint_protocol: singleEvent? singleEvent.protocol: "",
            single_appoint_organisators: singleEvent? singleEvent.organisators: [],
            single_appoint_attendees: singleEvent? singleEvent.attendees: [],
            single_appoint_sections: singleEvent? singleEvent.sections: [],
            single_appoint_display: singleEvent? singleEvent.display : "auto",
            single_appoint_remember_minutes: singleEvent? singleEvent.remember_minutes : -1,
            single_appoint_start: singleEvent? moment(singleEvent.startDate).toDate() : this.state.start,
            single_appoint_end: singleEvent? moment(singleEvent.endDate).toDate() : this.state.end,

            currentTab: singleEvent?.id ? "single" : "general",
        };


        this.setState(state)
    }

    onSaveClick() {
        if(!this.state.isEditable)
            return

        if (moment(this.state.start).isAfter(this.state.end)) {
            this.setState({endError: true, startError: true})
            EducaHelper.fireErrorToast("Fehler", "Das Enddatum ist vor dem Anfangsdatum")
            return
        }
        if (!this.state.start || !this.state.end) {
            this.setState({endError: true, startError: true})
            return
        }
        if (!this.state.title) {
            this.setState({titleError: true})
            return;
        }

        let sectionIds = []
        let attendeesIds = []
        let organisatorIds = []
        let roomIds = []
        this.state.attendees?.forEach(a => {
            attendeesIds.push(a.id)
        })

        this.state.sections?.forEach(g => {
            sectionIds.push(g.id)
        })

        this.state.organisators?.forEach(o => {
            organisatorIds.push(o.id)
        })

        this.state.rooms?.forEach(r => {
            roomIds.push(r.id)
        })



        let evt = {
            id: this.state.id,
            title: this.state.title,
            start: this.state.start,
            end: this.state.end,
            remember_minutes : this.state.remember_minutes,
            organisators: organisatorIds,
            attendees: attendeesIds,
            sections: sectionIds,
            location: this.state.location,
            rooms : roomIds,
            description: this.state.description,
            protocol: this.state.protocol,
            display: this.state.display,
            color: this.state.color,
            eventClass: this.state.eventClass,

            hasRepetition : this.state.hasRepetition,
            repetitionUntil: this.state.hasRepetition? this.state.repetitionUntil.unix() : moment().add(3,"month"),
            repetitionRhythm : this.state.hasRepetition? this.state.repetitionRhythm.value : SELECT_RHYTHM_OPTIONS[0],
            repetitionTurnus : this.state.hasRepetition? this.state.repetitionTurnus.value : SELECT_TURNUS_OPTIONS[0],

        };
        let isUpdate = evt.id !== null
        let promise
        if (isUpdate) // if there ins id = Update; if none = create
            promise = AjaxHelper.updateEvent(evt)
        else
            promise = AjaxHelper.createEvent(evt)

        promise.then(resp => {
            if (resp.status > 0 && resp.payload && resp.payload.event) {

                let preparedNewEvent = {
                    id: resp.payload.event.id,
                    title: resp.payload.event.title,
                    start: moment(resp.payload.event.startDate).toDate(),
                    end: moment(resp.payload.event.endDate).toDate(),
                    attendees: resp.payload.event.attendees,
                    organisators: resp.payload.event.organisators,
                    sections: resp.payload.event.sections,
                    eventClass: resp.payload.event.eventClass
                }

                if (isUpdate)  //update
                {
                    this.setState({preparedNewEvent}, () => {
                        this.props.eventUpdatedCallback(preparedNewEvent)
                    })
                    EducaHelper.fireSuccessToast("Erfolg", "Event erfolgreich aktualisiert.")
                    return
                } else // creation
                {
                    this.props.eventCreatedCallback(preparedNewEvent)
                    EducaHelper.fireSuccessToast("Erfolg", "Event erfolgreich erstellt.")
                }
                return
            }
            throw new Error(resp.message);
        })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Serverfehler." + err.message)
            })
    }

    joinMeeting() {
        this.setState({isMeetingLoading: true})
        AjaxHelper.joinMeeting(MODELS.CALENDAR, this.state.id)
            .then(resp => {
                if (!resp.payload?.url)
                    throw new Error(resp.message)
                window.open(resp.payload.url)
            })
            .catch(err => {

                EducaHelper.fireErrorToast("Fehler", "Meeting konnte nicht gestartet werden. " + err.message)
            })
            .finally(() => {
                this.setState({isMeetingLoading: false})
            })
    }

    onCancelClick() {
        this.props.cancelClickCallback()
    }

    onDeleteYesNoModalClick(btn) {
        if (btn === MODAL_BUTTONS.YES) {
            AjaxHelper.deleteEvent(this.state.id)
                .then(resp => {
                    if (resp.status > 0) {
                        EducaHelper.fireSuccessToast("Erfolg", "Der Termin wurde gelöscht")
                        this.props.eventDeletedCallback(this.state)
                        return
                    } else
                        throw new Error(resp.message)
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Fehler beim Löschen des Termins. " + err.message)
                })
        }

    }

    onDeleteClick() {
        if (this.state.hasRepetition) {
            this.modalRef.current.open((btn) => {
                this.onDeleteYesNoModalClick(btn)
            }, "Serie löschen", "Soll die Serie wirklich gelöscht werden?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO])
        } else {
            this.modalRef.current.open((btn) => {
                this.onDeleteYesNoModalClick(btn)
            }, "Termin löschen", "Soll der Termin wirklich gelöscht werden?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO])
        }
    }

    onDeleteTerminYesNoModalClick(btn) {
        if (btn === MODAL_BUTTONS.YES) {
            AjaxHelper.deleteTerminEvent(this.state.id, this.state.occurrenceDate)
                .then(resp => {
                    if (resp.status > 0) {
                        EducaHelper.fireSuccessToast("Erfolg", "Der Termin wurde abgesagt")
                        this.props.eventDeletedCallback(this.state)
                        return
                    } else
                        throw new Error(resp.message)
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Fehler beim Absagen des Termins. " + err.message)
                })
        }
    }

    onDeleteTerminClick() {
        this.modalRef.current.open((btn) => {
            this.onDeleteTerminYesNoModalClick(btn)
        }, "Termin absagen", "Soll der einzelne Termin wirklich abgesagt werden?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO])
    }

    onDeleteRemovedTerminYesNoModalClick(btn, variant) {
        if (btn === MODAL_BUTTONS.YES) {
            AjaxHelper.deleteRemovedTerminEvent(this.state.id, this.state.single_appoint_id, this.state.occurrenceDate)
                .then(resp => {
                    if (resp.status > 0) {
                        EducaHelper.fireSuccessToast("Erfolg", variant === "Absage" ? "Die Absage wurde zurückgenommen" : "Die Änderung der Serie wurde gelöscht")

                        let obj = resp.payload.event;
                        let singleEvent = resp.payload.singleEvent;
                        this.processEventDetails(obj,singleEvent)
                        this.props.eventUpdatedCallback(null);
                        return
                    } else
                        throw new Error(resp.message)
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Fehler beim Löschen der Absage des Termins. " + err.message)
                })
        }
    }

    onDeleteRemovedTerminClick(variant = "Absage") {
        this.modalRef.current.open((btn) => {
            this.onDeleteRemovedTerminYesNoModalClick(btn, variant)
        }, variant === "Absage" ? "Termin Absage zurücknehmen": "Termin Veränderung löschen", variant === "Absage" ? "Soll diese Veränderung des Serie-Termins wirklich gelöscht werden?" : "Soll diese Absage des Serie-Termins wirklich zurückgenommen werden?", [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO])
    }

    createSingleAppointmentClick() {
        AjaxHelper.createSingleTerminEvent(this.state.id, this.state.occurrenceDate)
            .then(resp => {
                if (resp.status > 0) {
                    EducaHelper.fireSuccessToast("Erfolg", "Die Ausnahme wurde erstellt")

                    let obj = resp.payload.event;
                    let singleEvent = resp.payload.singleEvent;
                    this.processEventDetails(obj,singleEvent)
                    this.props.eventUpdatedCallback(obj,false);
                    return
                } else
                    throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Die Ausnahme konnte nicht erstellt werden. " + err.message)
            })
    }

    updateSingleAppointmentClick() {
        if(!this.state.isEditable)
            return

        if (moment(this.state.single_appoint_start).isAfter(this.state.single_appoint_end)) {
            this.setState({single_appoint_endError: true, single_appoint_startError: true})
            EducaHelper.fireErrorToast("Fehler", "Das Enddatum ist vor dem Anfangsdatum")
            return
        }
        if (!this.state.single_appoint_start || !this.state.single_appoint_end) {
            this.setState({single_appoint_endError: true, single_appoint_startError: true})
            return
        }
        if (!this.state.single_appoint_title) {
            this.setState({single_appoint_titleError: true})
            return;
        }

        let sectionIds = []
        let attendeesIds = []
        let organisatorIds = []
        let roomIds = []
        this.state.single_appoint_attendees?.forEach(a => {
            attendeesIds.push(a.id)
        })

        this.state.single_appoint_sections?.forEach(g => {
            sectionIds.push(g.id)
        })

        this.state.single_appoint_organisators?.forEach(o => {
            organisatorIds.push(o.id)
        })

        this.state.single_appoint_rooms?.forEach(r => {
            roomIds.push(r.id)
        })


        let evt = {
            id: this.state.single_appoint_id,
            title: this.state.single_appoint_title,
            start: this.state.single_appoint_start,
            end: this.state.single_appoint_end,
            remember_minutes : this.state.single_appoint_remember_minutes,
            organisators: organisatorIds,
            attendees: attendeesIds,
            sections: sectionIds,
            location: this.state.single_appoint_location,
            rooms : roomIds,
            description: this.state.single_appoint_description,
            protocol: this.state.single_appoint_protocol,
            display: this.state.single_appoint_display,
        };

        AjaxHelper.updateSingleTerminEvent(this.state.id, this.state.single_appoint_id, this.state.occurrenceDate, evt)
            .then(resp => {
                if (resp.status > 0) {
                    EducaHelper.fireSuccessToast("Erfolg", "Der Termin wurde aktualisiert")

                    let obj = resp.payload.event;
                    let singleEvent = resp.payload.singleEvent;
                    this.processEventDetails(obj,singleEvent)
                    this.props.eventUpdatedCallback(obj, true);
                    return
                } else
                    throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Der Termin konnte nicht aktualisiert werden. " + err.message)
            })
    }

    updateCollisions() {
        return;
        if(this.state.start && this.state.end)
        {
            let cloud_ids = []
            this.state.organisators.map(organisator => {
                if(cloud_ids.indexOf(organisator.id) === -1)
                {
                    cloud_ids.push(organisator.id)
                }
            })
            this.state.attendees.map(attendee => {
                if(cloud_ids.indexOf(attendee.id) === -1)
                {
                    cloud_ids.push(attendee.id)
                }
            })

            AjaxHelper.checkUserCalendars(moment(this.state.start).unix(), moment(this.state.end).unix(), cloud_ids, this.state.id)
                .then(resp => {
                    if (resp.status > 0) {
                        if(resp.payload.totalCollisions > 0)
                        {
                            let collision_cloudids = []
                            for (const [key, value] of Object.entries(resp.payload.collisions)) {
                                collision_cloudids.push(parseInt(key))
                            }

                            let collisions = []
                            this.state.organisators.map(organisator => {
                                if(collision_cloudids.indexOf(organisator.id) !== -1)
                                {
                                    collisions.push(organisator.name)
                                }
                            })
                            this.state.attendees.map(attendee => {
                                if(collision_cloudids.indexOf(attendee.id) !== -1 && collisions.indexOf(attendee.name) === -1)
                                {
                                    collisions.push(attendee.name)
                                }
                            })
                            this.setState({collisions: collisions})
                        }
                        else {
                            this.setState({collisions: []})
                        }
                    } else
                        throw new Error(resp.message)
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Fehler beim Überprüfen der Terminkollisionen." + err.message)
                })

        }
        else {
            this.setState({collisions: []})
        }

    }

    getViewNotException()
    {
        return <div className={"mt-4 mb-4"}>

            <div className={"text-center"}>
                <div className={"alert alert-info"}>

                    <h1><i className="fas fa-info"></i></h1>
                    Bisher wurde noch keine Ausnahme von der Serie für diesen Termin angelegt. Klicken Sie auf "Termin verändern", um Abweichungen von der Termin-Serie zu erstellen.</div>
            </div>
        </div>
    }

    getViewRemoveException()
    {
        return <div className={"mt-4 mb-4"}>
            <div className={"text-center"}>
                <div className={"alert alert-danger text-bold"}>
                    <h1><i className="fas fa-times"></i></h1>
                    <div>Dieser Termin wurde abgesagt.</div>
                    <a href={"#"} onClick={() => this.onDeleteRemovedTerminClick()}>Absage zurücknehmen</a>
                </div>
            </div>
        </div>
    }

    getViewMoveException()
    {
        return <div className={"mt-4 mb-4"}>
            <div className="form-group row" style={{marginTop: "20px"}}>
                <label className="col-sm-2 col-form-label"><i
                    className="fas fa-pencil-alt"></i> Titel</label>
                <div className="col-sm-10">
                    <Form.Control
                        disabled={!this.state.isEditable}
                        name="title"
                        type="text"
                        isInvalid={this.state.single_appoint_titleError}
                        value={this.state.single_appoint_title}
                        onChange={(evt) => {
                            this.setState({single_appoint_title: evt.target.value, single_appoint_titleError: false})
                        }}
                        className="form-control"
                        placeholder="Titel hinzufügen"
                    />
                </div>
            </div>
            <Collapse in={!this.state.single_appoint_rooms?.length}>
                <div className="form-group row">
                    <label className="col-sm-2 col-form-label"><i className="fas fa-map-marker-alt"></i> Ort</label>
                    <div className="col-sm-10">
                        <Form.Control
                            disabled={!this.state.isEditable}
                            name="location"
                            type="text"
                            value={this.state.single_appoint_location}
                            onChange={(evt) => {
                                this.setState({single_appoint_location: evt.target.value})
                            }}
                            className="form-control"
                            placeholder="Ort hinzufügen"
                        />
                    </div>
                </div>
            </Collapse>
            <div className="form-group row">
                <label className="col-sm-2 col-form-label"><i className="fa fa-home"></i> Räume </label>
                <div className="col-sm-10">
                    <RoomSelectMultiple
                        isDisabled={!this.state.isEditable}
                        value={this.state.single_appoint_rooms}
                        closeMenuOnSelect={false}
                        placeholder={"Räume auswählen..."}
                        roomsChangedCallback={(obj) => {
                            this.setState({single_appoint_rooms: obj})
                        }}
                    ></RoomSelectMultiple>
                </div>
            </div>
            <div className="form-group row">
                <label className="col-sm-2 col-form-label"><i
                    className="fas fa-calendar-week"></i> Start</label>
                <div className="col-sm-10">
                    <div style={this.state.single_appoint_startError ? {display: "flex", flexDirection: "row"} : {}}>
                        <div className={this.state.single_appoint_startError ? "" : "input-group"}
                             style={this.state.single_appoint_startError ? {
                                 padding: "2px",
                                 border: "1px solid red",
                                 display: "flex",
                                 flexShrink: "1",
                             } : {}}>
                            <DatePicker
                                disabled={!this.state.isEditable}
                                ref={this.calendarSingleStartRef}
                                selected={this.state.single_appoint_start}
                                timeIntervals={10}
                                onChange={date => this.setState({single_appoint_start: date, single_appoint_startError: false})}
                                locale="de-DE"
                                className={"form-control"}
                                timeCaption={"Startzeit"}
                                showTimeSelect
                                dateFormat="dd.MM.yyyy  HH:mm"
                            />
                            <div className="input-group-append">
                                <Button
                                    disabled={!this.state.isEditable}
                                    style={{zIndex: 0}}
                                    onClick={() => {
                                        this.calendarSingleStartRef.current.setOpen(true)
                                    }} variant={"outline-secondary"}><i
                                    className="far fa-calendar-alt"/></Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="form-group row">
                <label className="col-sm-2 col-form-label"><i className="fas fa-calendar-week"></i> Ende</label>
                <div className="col-sm-10">
                    <div style={this.state.single_appoint_endError ? {display: "flex", flexDirection: "row"} : {}}>
                        <div className={this.state.single_appoint_endError ? "" : "input-group"}
                             style={this.state.single_appoint_endError ? {
                                 padding: "2px",
                                 borderRadius: "1.125rem",
                                 display: "flex",
                                 flexShrink: "1",
                             } : {}}>
                            <DatePicker
                                disabled={!this.state.isEditable}
                                ref={this.calendarSingleEndRef}
                                selected={this.state.single_appoint_end}
                                timeIntervals={10}
                                minDate={this.state.single_appoint_start}
                                timeCaption={"Endzeit"}
                                className={"form-control"}
                                onChange={date => this.setState({single_appoint_end: date, single_appoint_endError: false})}
                                locale="de-DE"
                                showTimeSelect
                                dateFormat="dd.MM.yyyy  HH:mm"
                            />
                            <div className="input-group-append">
                                <Button
                                    disabled={!this.state.isEditable}
                                    style={{zIndex: 0}}
                                    onClick={() => {
                                        this.calendarSingleEndRef.current.setOpen(true)
                                    }} variant={"outline-secondary"}><i
                                    className="far fa-calendar-alt"/></Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="form-group row">
                <label className="col-sm-2 col-form-label"><i className="far fa-bell"></i> Erinnerung
                </label>
                <div className="col-sm-10">
                    <Select
                        isDisabled={!this.state.isEditable}
                        value={this.state?.single_appoint_remember_minutes? SELECT_MINUTES_OBJECTS.find( o => o.value == this.state?.single_appoint_remember_minutes) : SELECT_MINUTES_OBJECTS[0]}
                        options={SELECT_MINUTES_OBJECTS}
                        closeMenuOnSelect={true}
                        onChange={(obj) => {
                            if(!obj)
                                return this.setState({single_appoint_remember_minutes : -1})
                            this.setState({single_appoint_remember_minutes : obj.value})
                        }}
                    />
                </div>
            </div>

            <div className="form-group row">
                <label className="col-sm-2 col-form-label"><i className="fas fa-paint-roller"></i> Darstellung
                </label>
                <div className="col-sm-10">
                    <Select
                        isDisabled={!this.state.isEditable}
                        value={this.state?.display? SELECT_DISPLAY_OBJECTS.find( o => o.value == this.state?.single_appoint_display) : SELECT_DISPLAY_OBJECTS[0]}
                        options={SELECT_DISPLAY_OBJECTS}
                        closeMenuOnSelect={true}
                        onChange={(obj) => {
                            if(!obj)
                                return this.setState({single_appoint_display : "auto"})
                            this.setState({single_appoint_display : obj.value})
                        }}
                    />
                </div>
            </div>


            <div className="form-group row">
                <label className="col-sm-2 col-form-label"><i
                    className="fas fa-user-plus"></i> Organisator*innen
                </label>
                <div className="col-sm-10">
                    <CloudIdSelectMultiple
                        isDisabled={!this.state.isEditable}
                        value={this.state.single_appoint_organisators}
                        closeMenuOnSelect={false}
                        placeholder={"Organisator*innen auswählen..."}
                        cloudUserListChangedCallback={(obj) => {
                            this.setState({single_appoint_organisators: obj})
                        }}
                    />
                </div>
            </div>

            <div className="form-group row">
                <label className="col-sm-2 col-form-label"><i
                    className="fas fa-user-plus"></i> Teilnehmer*innen</label>
                <div className="col-sm-10">
                    <CloudIdSelectMultiple
                        isDisabled={!this.state.isEditable}
                        value={this.state.single_appoint_attendees}
                        closeMenuOnSelect={false}
                        placeholder={"Teilnehmer*innen auswählen..."}
                        cloudUserListChangedCallback={(obj) => {
                            this.setState({single_appoint_attendees: obj})
                        }}
                    />

                </div>
            </div>

            <div className="form-group row">
                <label className="col-sm-2 col-form-label"><i className="fas fa-users"></i> Bereiche
                </label>
                <div className="col-sm-10">
                    <SectionSelectMultiple
                        isDisabled={!this.state.isEditable}
                        value={this.state.single_appoint_sections}
                        placeholder={"Bereiche auswählen..."}
                        sectionListChangedCallback={(obj) => this.setState({single_appoint_sections: obj})}
                    />
                </div>
            </div>
            <div className="form-group row">
                <label className="col-sm-2 col-form-label"><i
                    className="fas fa-list-ul"></i> Beschreibung</label>
                <div className="col-sm-10">
                    <CKEditor
                        disabled={!this.state.isEditable}
                        className=" col-sm-10"
                        config={EducaCKEditorDefaultConfig}
                        editor={window.ClassicEditor}
                        data={this.state.single_appoint_description}
                        onChange={(event, editor) => {
                            const data = editor.getData();
                            this.setState({single_appoint_description: data})
                        }}
                    />
                </div>
            </div>

        </div>

    }

    changeKlausurColor(color) {
        this.setState({color: color.hex})
    }

    setKlausurDate(date, callback) {
        let endDate = new Date(date.getTime())
        date.setHours(0,0)
        endDate.setHours(23,59)
        this.setState({start: date, end: endDate}, callback)
    }

    getBlockedSectionsView() {
        let blocked = this.state.blockedSections
        let output = <></>
        if (blocked.length > 0)
            output = blocked.map((element, index) => {
                return <div key={index} className={'pl-1 pr-1 m-1'} style={{border: 'solid 0.5px', borderColor: 'darkgrey'}}>
                    {element}
                </div>
            })
        return <div className={'d-flex flex-wrap'}>{output}</div>
    }

    getButtons(page) {
        if(page === "files")
        {
            return <div className={"mt-2 d-flex"}>
                <Button
                    className="btn btn-secondary mr-2"
                    onClick={() => this.onCancelClick()}
                >Abbrechen</Button>
                {this.state.id ? <Button
                    className="btn btn-success ml-auto"
                    onClick={() => this.joinMeeting()}
                >
                    <div style={{display: "flex", flexDirection: "row"}}>
                        Meeting
                        <div style={{
                            display: "flex",
                            marginLeft: "2px",
                            flex: "1 1 0%",
                            flexDirection: "column",
                            justifyContent: "center"
                        }}>
                            {this.state.isMeetingLoading ? <Spinner
                                as="span"
                                animation="grow"
                                size="sm"
                                role="status"
                                aria-hidden="true"
                            /> : <i className="fas fa-external-link-alt"></i>}
                        </div></div>
                </Button> : null}
            </div>
        }
        if(page === "singleAppointment")
        {
            return <div className={"mt-2 d-flex"}>
                <Button
                    className="btn btn-secondary mr-2"
                    onClick={() => this.onCancelClick()}
                >Abbrechen</Button>
                {this.state.isEditable  ?
                    <>
                        { this.state.single_appoint_exception_type !== "remove" ?
                            <Button
                                className="btn btn-danger mr-2"
                                onClick={() => this.onDeleteTerminClick()}
                            >Termin absagen</Button> : null }
                    </> : null }
                {this.state.isEditable && this.state.single_appoint_id === -1  ?
                    <Button
                        className="btn btn-primary mr-2"
                        onClick={() => this.createSingleAppointmentClick()}
                    >Termin verändern</Button> : null
                }
                {this.state.isEditable && this.state.single_appoint_id !== -1  ?
                    <Button
                        className="btn btn-danger mr-2"
                        onClick={() => this.onDeleteRemovedTerminClick("move")}
                    >Veränderung löschen</Button> : null
                }
                {this.state.isEditable && this.state.single_appoint_id !== -1 && this.state.single_appoint_exception_type === "move" ? <Button
                    className="btn btn-primary"
                    onClick={() => this.updateSingleAppointmentClick()}
                >{"Termin speichern & Schließen"}</Button> : null}


                {this.state.id ? <Button
                    className="btn btn-success ml-auto"
                    onClick={() => this.joinMeeting()}
                >
                    <div style={{display: "flex", flexDirection: "row"}}>
                        Meeting
                        <div style={{
                            display: "flex",
                            marginLeft: "2px",
                            flex: "1 1 0%",
                            flexDirection: "column",
                            justifyContent: "center"
                        }}>
                            {this.state.isMeetingLoading ? <Spinner
                                as="span"
                                animation="grow"
                                size="sm"
                                role="status"
                                aria-hidden="true"
                            /> : <i className="fas fa-external-link-alt"></i>}
                        </div></div>
                </Button> : null}
            </div>
        }
        if(page === "exam") {
            return <div className={"mt-2 d-flex"}>
                <Button
                    className="btn btn-secondary mr-2"
                    onClick={() => this.onCancelClick()}
                >Abbrechen</Button>
                {this.state.isEditable && this.props.event && this.state.id ?
                    <Button
                        className="btn btn-danger mr-2"
                        onClick={() => this.onDeleteClick()}
                    >Löschen</Button> : null
                }
                {this.state.isEditable ? <Button
                    className="btn btn-primary"
                    disabled={!!this.state.blockedSections.length}
                    onClick={() => this.onSaveClick()}
                >{this.state.id ? "Speichern & Schließen" : "Weiter"}</Button> : null}
            </div>
        }
        return <div className={"mt-2 d-flex"}>
            <Button
                className="btn btn-secondary mr-2"
                onClick={() => this.onCancelClick()}
            >Abbrechen</Button>
            {this.state.isEditable && this.props.event && this.state.id ? ( this.state.hasRepetition ?
                <>
                    <Button
                        className="btn btn-danger mr-2"
                        onClick={() => this.onDeleteClick()}
                    >Serie löschen</Button>
                </>
                :
                <Button
                    className="btn btn-danger mr-2"
                    onClick={() => this.onDeleteClick()}
                >Löschen</Button>) : null}
            {this.state.isEditable? <Button
                className="btn btn-primary"
                onClick={() => this.onSaveClick()}
            >{this.state.id ? "Speichern & Schließen" : "Weiter"}</Button> : null}
            {this.state.id ? <Button
                className="btn btn-success ml-auto"
                onClick={() => this.joinMeeting()}
            >
                <div style={{display: "flex", flexDirection: "row"}}>
                    Meeting
                    <div style={{
                        display: "flex",
                        marginLeft: "2px",
                        flex: "1 1 0%",
                        flexDirection: "column",
                        justifyContent: "center"
                    }}>
                        {this.state.isMeetingLoading ? <Spinner
                            as="span"
                            animation="grow"
                            size="sm"
                            role="status"
                            aria-hidden="true"
                        /> : <i className="fas fa-external-link-alt"></i>}
                    </div></div>
            </Button> : null}
        </div>
    }

    getEventEditorView() {
        return <Card>
            <Card.Body>
                <Card.Title>{!this.props.event || !this.props.event.id ? this.state.title ? this.state.title : "Neuer Termin " : "Termin bearbeiten " + "(" + this.state.title + ")"}</Card.Title>
                <Tabs
                    mountOnEnter={true}
                    unmountOnExit={true}
                    id="controlled-tab-example"
                    activeKey={this.state.currentTab}
                    onSelect={(k) => this.setState({currentTab: k})}
                >
                    {this.state.hasRepetition && this.state.single_appoint_id != null ?
                        <Tab eventKey="single" title="Termin">
                            {
                                this.state.single_appoint_id === -1 ?
                                    this.getViewNotException() :
                                    (this.state.single_appoint_exception_type === "remove"
                                            ? this.getViewRemoveException() :
                                            this.getViewMoveException()
                                    )
                            }
                            {this.getButtons("singleAppointment")}
                        </Tab>
                        : null
                    }
                    <Tab eventKey="general" title={this.state.hasRepetition ? "Serie" : "Allgemein"}>
                        <div>
                            <div className="form-group row" style={{marginTop: "20px"}}>
                                <label className="col-sm-2 col-form-label"><i
                                    className="fas fa-pencil-alt"></i> Titel</label>
                                <div className="col-sm-10">
                                    <Form.Control
                                        disabled={!this.state.isEditable}
                                        name="title"
                                        type="text"
                                        isInvalid={this.state.titleError}
                                        value={this.state.title}
                                        onChange={(evt) => {
                                            this.setState({title: evt.target.value, titleError: false})
                                        }}
                                        className="form-control"
                                        placeholder="Titel hinzufügen"
                                    />
                                </div>
                            </div>
                            <Collapse in={!this.state.rooms?.length}>
                                <div className="form-group row">
                                    <label className="col-sm-2 col-form-label"><i className="fas fa-map-marker-alt"></i> Ort</label>
                                    <div className="col-sm-10">
                                        <Form.Control
                                            disabled={!this.state.isEditable}
                                            name="location"
                                            type="text"
                                            value={this.state.location}
                                            onChange={(evt) => {
                                                this.setState({location: evt.target.value})
                                            }}
                                            className="form-control"
                                            placeholder="Ort hinzufügen"
                                        />
                                    </div>
                                </div>
                            </Collapse>
                            <div className="form-group row">
                                <label className="col-sm-2 col-form-label"><i className="fa fa-home"></i> Räume </label>
                                <div className="col-sm-10">
                                    <RoomSelectMultiple
                                        isDisabled={!this.state.isEditable}
                                        value={this.state.rooms}
                                        closeMenuOnSelect={false}
                                        placeholder={"Räume auswählen..."}
                                        roomsChangedCallback={(obj) => {
                                            this.setState({rooms: obj})
                                        }}
                                    ></RoomSelectMultiple>
                                </div>
                            </div>
                            <div className="form-group row">
                                <label className="col-sm-2 col-form-label"><i
                                    className="fas fa-calendar-week"></i> {this.state.hasRepetition ? "Start des ersten Termins" : "Start"}</label>
                                <div className="col-sm-10">
                                    <div style={this.state.startError ? {display: "flex", flexDirection: "row"} : {}}>
                                        <div className={this.state.startError ? "" : "input-group"}
                                             style={this.state.startError ? {
                                                 padding: "2px",
                                                 border: "1px solid red",
                                                 display: "flex",
                                                 flexShrink: "1",
                                             } : {}}>
                                            <DatePicker
                                                disabled={!this.state.isEditable}
                                                ref={this.calendarStartRef}
                                                selected={this.state.start}
                                                timeIntervals={10}
                                                onChange={date => this.setState({start: date, startError: false},() => {this.updateCollisions()})}
                                                locale="de-DE"
                                                className={"form-control"}
                                                timeCaption={"Startzeit"}
                                                showTimeSelect
                                                dateFormat="dd.MM.yyyy  HH:mm"
                                            />
                                            <div className="input-group-append">
                                                <Button
                                                    disabled={!this.state.isEditable}
                                                    style={{zIndex: 0}}
                                                    onClick={() => {
                                                        this.calendarStartRef.current.setOpen(true)
                                                    }} variant={"outline-secondary"}><i
                                                    className="far fa-calendar-alt"/></Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="form-group row">
                                <label className="col-sm-2 col-form-label"><i className="fas fa-calendar-week"></i> {this.state.hasRepetition ? "Ende des ersten Termins" : "Ende"}</label>
                                <div className="col-sm-10">
                                    <div style={this.state.endError ? {display: "flex", flexDirection: "row"} : {}}>
                                        <div className={this.state.endError ? "" : "input-group"}
                                             style={this.state.endError ? {
                                                 padding: "2px",
                                                 borderRadius: "1.125rem",
                                                 display: "flex",
                                                 flexShrink: "1",
                                             } : {}}>
                                            <DatePicker
                                                disabled={!this.state.isEditable}
                                                ref={this.calendarEndRef}
                                                selected={this.state.end}
                                                timeIntervals={10}
                                                minDate={this.state.start}
                                                timeCaption={"Endzeit"}
                                                className={"form-control"}
                                                onChange={date => this.setState({end: date, endError: false},() => {this.updateCollisions()})}
                                                locale="de-DE"
                                                showTimeSelect
                                                dateFormat="dd.MM.yyyy  HH:mm"
                                            />
                                            <div className="input-group-append">
                                                <Button
                                                    disabled={!this.state.isEditable}
                                                    style={{zIndex: 0}}
                                                    onClick={() => {
                                                        this.calendarEndRef.current.setOpen(true)
                                                    }} variant={"outline-secondary"}><i
                                                    className="far fa-calendar-alt"/></Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {this.state.hasRepetition ?
                                <div className="form-group row">
                                    <label className="col-sm-2 col-form-label">
                                    </label>
                                    <div className="col-sm-10">
                                        <p className={"alert alert-warning"}><b>Hinweis:</b> Dieser Termin ist eine Serie. Die Angaben beziehen sich hier auf die gesamte Serie.</p>
                                    </div>
                                </div>

                                : null }

                            <div className="form-group row">
                                <label className="col-sm-2 col-form-label"><i className="far fa-bell"></i> Erinnerung
                                </label>
                                <div className="col-sm-10">
                                    <Select
                                        isDisabled={!this.state.isEditable}
                                        value={this.state?.remember_minutes? SELECT_MINUTES_OBJECTS.find( o => o.value == this.state?.remember_minutes) : SELECT_MINUTES_OBJECTS[0]}
                                        options={SELECT_MINUTES_OBJECTS}
                                        closeMenuOnSelect={true}
                                        onChange={(obj) => {
                                            if(!obj)
                                                return this.setState({remember_minutes : -1})
                                            this.setState({remember_minutes : obj.value})
                                        }}
                                    />
                                </div>
                            </div>

                            <div className="form-group row">
                                <label className="col-sm-2 col-form-label"><i className="fas fa-paint-roller"></i> Darstellung
                                </label>
                                <div className="col-sm-10">
                                    <Select
                                        isDisabled={!this.state.isEditable}
                                        value={this.state?.display? SELECT_DISPLAY_OBJECTS.find( o => o.value == this.state?.display) : SELECT_DISPLAY_OBJECTS[0]}
                                        options={SELECT_DISPLAY_OBJECTS}
                                        closeMenuOnSelect={true}
                                        onChange={(obj) => {
                                            if(!obj)
                                                return this.setState({display : "auto"})
                                            this.setState({display : obj.value})
                                        }}
                                    />
                                </div>
                            </div>


                            <div className="form-group row">
                                <label className="col-sm-2 col-form-label"><i
                                    className="fas fa-user-plus"></i> Organisator*innen
                                </label>
                                <div className="col-sm-10">
                                    <CloudIdSelectMultiple
                                        isDisabled={!this.state.isEditable}
                                        value={this.state.organisators}
                                        closeMenuOnSelect={false}
                                        placeholder={"Organisator*innen auswählen..."}
                                        cloudUserListChangedCallback={(obj) => {
                                            this.setState({organisators: obj}, () => {this.updateCollisions()})
                                        }}
                                    />
                                </div>
                            </div>

                            <div className="form-group row">
                                <label className="col-sm-2 col-form-label"><i
                                    className="fas fa-user-plus"></i> Teilnehmer*innen</label>
                                <div className="col-sm-10">
                                    <CloudIdSelectMultiple
                                        isDisabled={!this.state.isEditable}
                                        value={this.state.attendees}
                                        closeMenuOnSelect={false}
                                        placeholder={"Teilnehmer*innen auswählen..."}
                                        cloudUserListChangedCallback={(obj) => {
                                            this.setState({attendees: obj},() => {this.updateCollisions()})
                                        }}
                                    />

                                </div>
                            </div>
                            {this.state.collisions.length > 0 ? <div className={"form-group row"}>
                                <label className={"col-sm-2 col-form-label text-danger"}><i
                                    className="fas fa-exclamation-triangle"></i> Terminkollisionen:</label>
                                <div className={"col-sm-10 text-danger"}>
                                    {this.state.collisions.map((collision, i) => {
                                        return <div key={"col_" + i.toString()}>{collision}</div>
                                    })}
                                </div>
                            </div> : null}

                            <div className="form-group row">
                                <label className="col-sm-2 col-form-label"><i className="fas fa-users"></i> Bereiche
                                </label>
                                <div className="col-sm-10">
                                    <SectionSelectMultiple
                                        isDisabled={!this.state.isEditable}
                                        value={this.state.sections??[]}
                                        placeholder={"Bereiche auswählen..."}
                                        sectionListChangedCallback={(obj) => this.setState({sections: obj})}
                                    />
                                </div>
                            </div>
                            <div className="form-group row">
                                <label className="col-sm-2 col-form-label"><i
                                    className="fas fa-list-ul"></i> Beschreibung</label>
                                <div className="col-sm-10">
                                    <CKEditor
                                        disabled={!this.state.isEditable}
                                        className=" col-sm-10"
                                        config={EducaCKEditorDefaultConfig}
                                        editor={window.ClassicEditor}
                                        data={this.state.description}
                                        onChange={(event, editor) => {
                                            const data = editor.getData();
                                            this.setState({description: data})
                                        }}
                                    />
                                </div>
                            </div>

                        </div>
                        {this.getButtons("general")}
                    </Tab>

                    <Tab eventKey="planning" title="Planungsoptionen" disabled={!this.state.id}>
                        <div className={"mt-4"}>
                            <div className="form-group row">
                                <label className="col-sm-4 col-form-label"><i
                                    className="fas fa-redo-alt"></i> Wiederholung Aktiv? </label>
                                <div className="col-sm-6">
                                    <div style={{display :"flex", flexDirection : "row"}}>
                                        <div style={{display :"flex", flexDirection : "column", justifyContent : "center"}} className={"mr-1"}>{"Nein"}</div>
                                        <ReactSwitch checked={this.state.hasRepetition} onChange={(flag) => {this.setState({hasRepetition : flag})}}/>
                                        <div style={{display :"flex", flexDirection : "column", justifyContent : "center"}} className={"ml-1"}>{"Ja"}</div>
                                    </div>
                                </div>
                            </div>

                            <div className="form-group row">
                                <label className="col-sm-4 col-form-label"><i
                                    className="fas fa-calendar-alt"></i> Wiederholt sich bis </label>
                                <div className="col-sm-6">
                                    <DatePickerBox
                                        disabled={!this.state.hasRepetition}
                                        date={this.state.repetitionUntil}
                                        onDateChange={(mom) => this.setState({repetitionUntil : mom})}
                                    />
                                </div>
                            </div>

                            <div className="form-group row">
                                <label className="col-sm-4 col-form-label"><i
                                    className="fas fa-chart-bar"></i> Rhythmus </label>
                                <div className="col-sm-6">
                                    <Select
                                        components={{ SelectPlaceholder }}
                                        placeholder={"Rhythmus..."}
                                        isDisabled={!this.state.hasRepetition}
                                        value={this.state.repetitionRhythm}
                                        options={SELECT_RHYTHM_OPTIONS}
                                        onChange={(val) => this.setState({repetitionRhythm : val})}
                                    />
                                </div>
                            </div>

                            <div className="form-group row">
                                <label className="col-sm-4 col-form-label"><i
                                    className="fas fa-chart-line"></i> Turnus </label>
                                <div className="col-sm-6">
                                    <Select
                                        components={{ SelectPlaceholder }}
                                        placeholder={"Turnus..."}
                                        isDisabled={!this.state.hasRepetition}
                                        value={this.state.repetitionTurnus}
                                        onChange={(val) => this.setState({repetitionTurnus : val})}
                                        options={SELECT_TURNUS_OPTIONS}
                                    />
                                </div>
                            </div>

                        </div>

                        {this.getButtons("planing")}
                    </Tab>
                    <Tab eventKey="protocol" title="Protokoll" disabled={!this.state.id}>
                        <div style={{
                            display: "flex",
                            flexDirection: "column",
                            marginTop: "20px",
                            marginBottom: "10px",
                            flex: 1
                        }}>
                            <CKEditor
                                config={{

                                    toolbar: {
                                        items: [
                                            'heading',
                                            '|',
                                            'bold',
                                            'italic',
                                            'link',
                                            'bulletedList',
                                            'numberedList',
                                            '|',
                                            'indent',
                                            'outdent',
                                            '|',
                                            'blockQuote',
                                            'insertTable',
                                            'undo',
                                            'redo'
                                        ]
                                    },
                                    language: 'de',
                                    table: {
                                        contentToolbar: [
                                            'tableColumn',
                                            'tableRow',
                                            'mergeTableCells'
                                        ]
                                    },
                                }}
                                disabled={!this.state.isEditable}
                                editor={window.ClassicEditor}
                                data={this.state.protocol}
                                onChange={(event, editor) => {
                                    const data = editor.getData();
                                    this.setState({protocol: data})
                                }}
                            />
                        </div>

                        {this.getButtons("protocol")}
                    </Tab>

                    <Tab eventKey="files" title="Dateien" disabled={!this.state.id}>
                        <div className={"mt-3"}>
                            {this.state.id ?
                                <EducaFileBrowserAdvanced
                                    hasNavigationbar={true}
                                    hasSearchbar={false}
                                    title={""}
                                    modelId={this.state.id}
                                    modelType={MODELS.CALENDAR}
                                    canUserUpload={true}
                                    canUserEdit={true}/>
                                : "Fehler"}
                        </div>

                   {this.getButtons("files")}
                    </Tab>
                    <Tab eventKey="meeting" title="Meeting" disabled={!this.state.id}>
                        <div style={{
                            display: "flex",
                            flexDirection: "column",
                            marginTop: "20px",
                            marginBottom: "10px",
                            flex: 1
                        }}>
                            <MeetingInformation
                                model_type={MODELS.CALENDAR}
                                model_id={this.state.id}
                            />
                        </div>
                    </Tab>
                </Tabs>

                <EducaModal ref={this.modalRef}/>
            </Card.Body>
        </Card>
    }

    getKlausurEditorView() {
        return <Card className={this.props.borderless ? "border-0" : ""}>
            <Card.Body>
                <Card.Title>{!this.props.event || !this.props.event.id ? this.state.title ? this.state.title : "Neue Klausur " : "Klausur bearbeiten " + "(" + this.state.title + ")"}</Card.Title>

                <div>
                    <div className="form-group row" style={{marginTop: "20px"}}>
                        <label className="col-sm-2 col-form-label"><i
                            className="fas fa-pencil-alt"></i> Titel</label>
                        <div className="col-sm-10">
                            <Form.Control
                                disabled={!this.state.isEditable}
                                name="title"
                                type="text"
                                isInvalid={this.state.titleError}
                                value={this.state.title}
                                onChange={(evt) => {
                                    this.setState({title: evt.target.value, titleError: false})
                                }}
                                className="form-control"
                                placeholder="Titel hinzufügen"
                            />
                        </div>
                    </div>
                    <Collapse in={!this.state.rooms?.length}>
                        <div className="form-group row">
                            <label className="col-sm-2 col-form-label"><i className="fas fa-map-marker-alt"></i> Ort</label>
                            <div className="col-sm-10">
                                <Form.Control
                                    disabled={!this.state.isEditable}
                                    name="location"
                                    type="text"
                                    value={this.state.location}
                                    onChange={(evt) => {
                                        this.setState({location: evt.target.value})
                                    }}
                                    className="form-control"
                                    placeholder="Ort hinzufügen"
                                />
                            </div>
                        </div>
                    </Collapse>
                    <div className="form-group row">
                        <label className="col-sm-2 col-form-label"><i className="fa fa-home"></i> Räume </label>
                        <div className="col-sm-10">
                            <RoomSelectMultiple
                                isDisabled={!this.state.isEditable}
                                value={this.state.rooms}
                                closeMenuOnSelect={false}
                                placeholder={"Räume auswählen..."}
                                roomsChangedCallback={(obj) => {
                                    this.setState({rooms: obj})
                                }}
                            ></RoomSelectMultiple>
                        </div>
                    </div>


                    <div className="form-group row">
                        <label className="col-sm-2 col-form-label"><i
                            className="fas fa-calendar-week"></i> Datum</label>
                        <div className="col-sm-10">
                            <div className={"input-group"}>
                                <DatePicker
                                    disabled={!this.state.isEditable}
                                    ref={this.calendarStartRef}
                                    selected={this.state.start}
                                    timeIntervals={10}
                                    onChange={(date) => this.setKlausurDate(date,
                                        () => {this.updateCollisions()})}
                                    locale="de-DE"
                                    className={"form-control"}
                                    timeCaption={"Klausurtermin"}
                                    dateFormat="dd.MM.yyyy"
                                />
                                <div className="input-group-append">
                                    <Button
                                        disabled={!this.state.isEditable}
                                        style={{zIndex: 0}}
                                        onClick={() => {
                                            this.calendarStartRef.current.setOpen(true)
                                        }} variant={"outline-secondary"}><i
                                        className="far fa-calendar-alt"/></Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="form-group row">
                        <label className="col-sm-2 col-form-label"><i className="far fa-bell"></i> Erinnerung
                        </label>
                        <div className="col-sm-10">
                            <Select
                                isDisabled={!this.state.isEditable}
                                value={this.state?.remember_minutes? SELECT_MINUTES_OBJECTS.find( o => o.value == this.state?.remember_minutes) : SELECT_MINUTES_OBJECTS[0]}
                                options={SELECT_MINUTES_OBJECTS}
                                closeMenuOnSelect={true}
                                onChange={(obj) => {
                                    if(!obj)
                                        return this.setState({remember_minutes : -1})
                                    this.setState({remember_minutes : obj.value})
                                }}
                            />
                        </div>
                    </div>

                    <div className="form-group row">
                        <label className="col-sm-2 col-form-label"><i className="fas fa-users"></i> Klasse
                        </label>
                        <div className="col-sm-10">
                            <SectionSelectMultiple
                                isDisabled={!this.state.isEditable}
                                value={this.state.sections}
                                placeholder={"Klassen auswählen..."}
                                permissionCallback={(p) => { return p?.group?.external_identifier?.includes("schoolclass_")}}
                                sectionListChangedCallback={(obj) => this.checkExamPlanningBlocked(obj)}
                            />
                        </div>
                    </div>

                    <div className={'form-group row'} hidden={!this.state.blockedSections.length}>
                        <div className="col-sm-2 invisible">invisible spacing</div>
                        <div className={'alert alert-danger col-sm-10'} role={'alert'}>
                            {this.getBlockedSectionsView()}
                            <i className="fas fa-times-circle"></i> Gesperrt!
                        </div>
                    </div>

                    {this.state.collisions.length > 0 ? <div className={"form-group row"}>
                        <label className={"col-sm-2 col-form-label text-danger"}><i
                            className="fas fa-exclamation-triangle"></i> Terminkollisionen:</label>
                        <div className={"col-sm-10 text-danger"}>
                            {this.state.collisions.map((collision, i) => {
                                return <div key={"col_" + i.toString()}>{collision}</div>
                            })}
                        </div>
                    </div> : null}

                    <div className={'form-group row'}>
                        <label className={"col-sm-2 col-form-label"}><i className="fas fa-palette"></i> Farbe
                        </label>
                        <div className={'col-sm-10'}>
                            <div className={'input-group'}>
                                <CirclePicker
                                    colors={DEFAULT_COLORS}
                                    color={this.state.color}
                                    onChangeComplete={color => this.changeKlausurColor(color)}
                                />
                            </div>
                        </div>
                    </div>

                    <div className="form-group row">
                        <label className="col-sm-2 col-form-label"><i
                            className="fas fa-list-ul"></i> Beschreibung</label>
                        <div className="col-sm-10">
                            <CKEditor
                                disabled={!this.state.isEditable}
                                className=" col-sm-10"
                                config={EducaCKEditorDefaultConfig}
                                editor={window.ClassicEditor}
                                data={this.state.description}
                                onChange={(event, editor) => {
                                    const data = editor.getData();
                                    this.setState({description: data})
                                }}
                            />
                        </div>
                    </div>

                </div>
                {this.getButtons("exam")}

                <EducaModal ref={this.modalRef}/>
            </Card.Body>
        </Card>
    }

    getTerminKlausurChoiceView() {
        return (
            <div>
                <div style={{display: "flex", flexDirection: "row", fontWeight: "700", color: "rgb(108, 117, 125)", fontSize: "1.125rem", lineHeight: "1.2"}}>Welchen Termin möchtest du erstellen?</div>
                <div
                    className={'row mt-2'}
                >
                    <div className={'col-12 col-md-6'}>
                        <Card  onClick={() => this.setDefaultClass()}>
                            <Card.Body>
                                <Card.Title>Standard-Termin hinzufügen</Card.Title>
                                <h6>Füge einen neuen Standard-Termin oder eine Terminserie hinzu.</h6>
                            </Card.Body>
                        </Card></div>
                    <div className={'col-12 col-md-6'}>
                        <Card  onClick={() => this.setExamClass()}>
                            <Card.Body>
                                <Card.Title>Klausurtermin hinzufügen</Card.Title>
                                <h6>Füge einen neuen Termin für eine Klausur hinzu</h6>
                            </Card.Body>
                        </Card></div>
                </div></div>)
    }

    setExamClass() {
        this.setState({eventClass: 'exam'}, () => {
            this.state.start?.setHours(0, 0)
            this.state.end?.setHours(23,59)
        })
    }

    setDefaultClass() {
        this.setState({eventClass: 'default'})
    }

    render() {
        return (
            <div>
                <div hidden={this.state.eventClass !== "" || this.props.hideChoice}>
                    {this.getTerminKlausurChoiceView()}
                </div>
                <div hidden={!this.state.event && !!!this.state.eventClass}>
                    {this.state.eventClass === 'exam' ?
                        this.getKlausurEditorView()
                        :
                        this.state.eventClass === 'default'?
                            this.getEventEditorView() : null}
                </div>
            </div>
        );
    }
}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(EventEditor);
