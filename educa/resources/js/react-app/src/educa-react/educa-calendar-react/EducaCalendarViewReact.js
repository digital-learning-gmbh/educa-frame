import React, {Component} from 'react';
import {connect} from "react-redux";
import SideMenu from "../educa-components/SideMenu";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import Form from "react-bootstrap/Form";
import './styles.css';
import {Button} from "react-bootstrap";
import {EducaCardLinkButton} from "../../shared/shared-components/Buttons";
import {SideMenuHeadingStyle} from "../educa-components/EducaStyles";
import EducaCalendar, {EDUCA_CALENDAR_DEFAULT_END_DATE, EDUCA_CALENDAR_DEFAULT_START_DATE,} from "./EducaCalendar";
import EducaCalendarInvitesModal from "./EducaCalendarInvitesModal";
import EducaHelper, {APP_NAMES} from "../helpers/EducaHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";
import SharedHelper, {MODELS} from "../../shared/shared-helpers/SharedHelper";
import {getDisplayPair} from "../../shared/shared-components/Inputs";
import KompostFabrik, {WIDGET_TYPES} from "../../shared/shared-helpers/KompostFabrik";
import xAPIProvider, {XAPI_VERBS} from "../xapi/xAPIProvider";
import ReactTooltip from 'react-tooltip';
import {useEducaLocalizedStrings, withEducaLocalizedStrings} from "../helpers/StringLocalizationHelper.js";

class EducaCalendarViewReact extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isLoading: false,
                groupUnCheckedStates: {}, //careful its UNchecked,
                isPersonalCheckboxChecked: true,
                isTimetableChecked : true,
                isRemovedEventsChecked : false,
                events: [],
                invitedEvents: [],
                toggleNewEvent: false,

                urlEventId: null,
                urlUniqueId : null,
                occurrenceDate : null,
                viewForCloudId: null,
                outlookShareToken : null,
                eventTypeFilter: {
                    defaultEventType: true,
                    examEventType: true
                }
            }

        this.invitesModal = React.createRef()
    }

    componentDidMount() {
        this._isMounted = true
        let params = new URLSearchParams(this.props.location.search);
        let eventId = params.get('event_id');
        let uniqueId = params.get('unique_id');
        let occurrenceDate = params.get('occurrence_date');
        let newState = {}

        if (eventId)   newState = {urlEventId: eventId }
        if (uniqueId)   newState = {...newState, urlUniqueId : uniqueId}
        if (occurrenceDate)  newState = {...newState, occurrenceDate : occurrenceDate}
        if(this._isMounted) this.setState(newState)
        this.loadInvites()
        xAPIProvider.create(null,XAPI_VERBS.OPEN, {
            'id': APP_NAMES.CALENDER,
            "objectType": "app",
            'definition': {
                'name': {'en-US': 'Kalender'}
            }
        });
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        let params = new URLSearchParams(this.props.location.search);
        let eventId = params.get('event_id');
        let uniqueId = params.get('unique_id');
        let occurrenceDate = params.get('occurrence_date');
        let newState = {}
        if (this.state.urlEventId !== eventId )
            newState = {urlEventId: eventId }

        if(this.state.urlUniqueId !== uniqueId)
            newState = {...newState, urlUniqueId : uniqueId}

        if(this.state.occurrenceDate !== occurrenceDate)
            newState = {...newState, occurrenceDate : occurrenceDate}

        if(this._isMounted && Object.keys(newState).length > 0 )  this.setState(newState)
    }

    setNewEventId(evt, occurrenceDate = null) {
        let parseUniqueId = (evt?.type === "lessonPlan" || evt?.type === "lesson") && evt.unique_id
        if (evt)
            this.props.history.push({
                pathname: this.props.location.pathname,
                search: (evt?.id ? '?event_id=' + evt.id : "")+(parseUniqueId? "&unique_id="+evt.unique_id :"")+(occurrenceDate? "&occurrence_date="+occurrenceDate :""),
            })
    }

    generateNewOutlookShareToken(){

        let groupIds = []
        this.props.store.currentCloudUser.groups.forEach(group => {
            if (!this.state.groupUnCheckedStates[group.id])
                groupIds.push(group.id)
        })

        const requestObject = {
            filters : {
                groupIds : groupIds,
                direct : !!this.state.isPersonalCheckboxChecked,
                isTimetableChecked : !!this.state.isTimetableChecked,
                showRemovedEvents : !!this.state.isRemovedEventsChecked,
                eventTypeFilter: this.state.eventTypeFilter
            }
        }

        AjaxHelper.generateOutlookShareToken(requestObject)
        .then(resp => {
            if (resp.status > 0) {
                this.setState({outlookShareToken : resp.payload.outlookShareToken})
                SharedHelper.fireSuccessToast("Erfolg", "Der Share-Link wurde generiert.")
                return;
            }
            throw new Error(resp.message)
        })
        .catch(err => {
            EducaHelper.fireErrorToast("Fehler", "Fehler beim erstellen der Verlinkung.")
            if (this._isMounted) this.setState({outlookShareToken: null})
        })
        .finally(() => {
            if (this._isMounted) this.setState({isLoading: false})
        })

    }

    loadInvites() {
        AjaxHelper.getEventInvites()
            .then(resp => {
                if (resp.status > 0 && resp.payload?.events)
                    return this.setState({invitedEvents: resp.payload.events})
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Fehler bei der Event Übertragung. Servernachricht: " + err.message)
            })
    }

    loadEvents(start = EDUCA_CALENDAR_DEFAULT_START_DATE, end = EDUCA_CALENDAR_DEFAULT_END_DATE) {
        //If redux store does not carry any groups
        if (!this.props.store.currentCloudUser.groups)
            return

        if (this._isMounted) this.setState({isLoading: true})
        let groupIds = []
        this.props.store.currentCloudUser.groups.forEach(group => {
            if (!this.state.groupUnCheckedStates[group.id])
                groupIds.push(group.id)
        })

        let events = []
        const me = this.props.store.currentCloudUser
        AjaxHelper.getEvents(start, end, groupIds, this.state.isPersonalCheckboxChecked, null,
            this.state.viewForCloudId, this.state.isRemovedEventsChecked, this.state.eventTypeFilter)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.events) {
                    //if (this._isMounted) this.setState({events: resp.payload.events})
                    events = events.concat(resp.payload.events)
                    this.setState({outlookShareToken :  resp.payload.outlookShareToken})
                    return null;
                }
                throw new Error(resp.message)

            })
            .then( () =>
            {
                if( me?.teacher && this.state.isTimetableChecked)
                    return AjaxHelper.getTimetableEvents(MODELS.TEACHER, [me.teacher.id], false, start,end)
                return null
            })
            .then( (resp) =>
            {
                if(resp && resp.status > 0 && resp.payload && resp.payload.events)
                    events = events.concat(resp.payload.events)
                if( me?.student && this.state.isTimetableChecked)
                    return AjaxHelper.getTimetableEvents(MODELS.STUDENT, [me.student.id], false, start,end)
                return null
            })
            .then( (resp) =>
            {
                if(resp && resp.status > 0 && resp.payload && resp.payload.events)
                {
                    events = events.concat(resp.payload.events)
                    return null;
                }
                return null
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Fehler bei der Event Übertragung. Servernachricht: " + err.message)
                if (this._isMounted) this.setState({events: []})
            })
            .finally(() => {
                if (this._isMounted) this.setState({isLoading: false, events : events})
            })
    }

    getAppsForSideMenu() {
        return {
            heading: {textAndId: this.props.translate("calender_view.personal","Persönliches"), component: null},
            content: [{
                component: <Form.Check
                    checked={this.state.isPersonalCheckboxChecked}
                    disabled={this.state.isLoading}
                    onChange={() => {
                        return null;
                    }}
                    type="checkbox"
                    label={this.props.translate("calender_view.invitation","Einladungen")}/>,
                clickCallback: () => {
                    if (this._isMounted) this.setState({isPersonalCheckboxChecked: !this.state.isPersonalCheckboxChecked}, () => this.loadEvents());
                }
            },
                {
                    component: <Form.Check
                        checked={this.state.isTimetableChecked}
                        disabled={this.state.isLoading}
                        onChange={() => {
                            return null;
                        }}
                        type="checkbox"
                        label={this.props.translate("calender_view.timetable","Stundenplan")}/>,
                    clickCallback: () => {
                        if (this._isMounted) this.setState({isTimetableChecked: !this.state.isTimetableChecked}, () => this.loadEvents());
                    }
                },
                {
                    component: <Form.Check
                        checked={this.state.isRemovedEventsChecked}
                        disabled={this.state.isLoading}
                        onChange={() => {
                            return null;
                        }}
                        type="checkbox"
                        label={this.props.translate("calender_view.cancelled_events","Abgesagte Termine anzeigen")}/>,
                    clickCallback: () => {
                        if (this._isMounted) this.setState({isRemovedEventsChecked: !this.state.isRemovedEventsChecked}, () => this.loadEvents());
                    }
                },
            ]
        }
    }

    getFilterEventType() {
        return {
            heading: {textAndId: this.props.translate("calender_view.event_type", "Termin-Art"), component: null},
            content: [{
                component: <Form.Check
                    checked={this.state.eventTypeFilter.defaultEventType}
                    disabled={this.state.isLoading}
                    onChange={() => {
                        return null;
                    }}
                    type="checkbox"
                    label={this.props.translate("calender_view.normal_event","Standard-Termin")}/>,
                clickCallback: () => {
                    if (this._isMounted) this.setState({eventTypeFilter: { ...this.state.eventTypeFilter, defaultEventType: !this.state.eventTypeFilter.defaultEventType}},
                        () => this.loadEvents())
                }
            },
                {
                    component: <Form.Check
                        checked={this.state.eventTypeFilter.examEventType}
                        disabled={this.state.isLoading}
                        onChange={() => {
                            return null;
                        }}
                        type="checkbox"
                        label={this.props.translate("calender_view.exams","Klausuren")}/>,
                    clickCallback: () => {
                        if (this._isMounted) this.setState({eventTypeFilter: { ...this.state.eventTypeFilter, examEventType: !this.state.eventTypeFilter.examEventType}},
                            () => this.loadEvents())
                    }
                },
            ]
        }
    }


    getOutlookSideMenu() {
        const outlookShareLink = this.state.outlookShareToken?.url
        return {
            heading: { textAndId: this.props.translate("calender_view.outlook", "Outlook / iCal / Kalender-Verlinkung"), component: null }, content: [{
                justComponent: outlookShareLink ? <p>{this.props.translate("calender_view.add_calender","Sie können den Kalender mit dem folgenden Link einbinden:")} <Form.Control
                    checked={this.state.isPersonalCheckboxChecked}
                    disabled={this.state.isLoading}
                    onChange={() => {
                        return null;
                    }}
                    value={window.location.origin + outlookShareLink}
                    type="text"
                    placeholder={this.props.translate("calender_view.calender_url","URL für Kalendar")} />
                    <div className="mt-1 d-flex align-items-center">
                        <Button disabled={this.state.isLoading} onClick={() => this.generateNewOutlookShareToken()} className="mr-1">
                            <i className='fas fa-share' /> {this.props.translate("calender_view.generate_new_link","Verlinkung neu generieren")}
                        </Button>
                        <i className='fas fa-info-circle' data-tip={"tooltip"} data-for={"link-tip"} />
                    </div>
                    <ReactTooltip id="link-tip">{this.props.translate("calender_view.generate_new_share_link","Share-Link mit gewählten Filtern neu generieren")}</ReactTooltip>
                </p>

                    : <p>{this.props.translate("calender_view.no_link_generated","Sie haben noch keine Verlinkung generiert.")}
                        <div className="mt-1 d-flex align-items-center">
                            <Button disabled={this.state.isLoading} onClick={() => this.generateNewOutlookShareToken()} className="mr-1">
                                <i className='fas fa-share' /> {this.props.translate("calender_view.generate_link","Verlinkung generieren")}
                            </Button>
                            <i className='fas fa-info-circle' data-tip={"tooltip"} data-for={"link-tip"} />
                        </div>
                        <ReactTooltip id="link-tip">{this.props.translate("calender_view.generate_share_link","Share-Link mit gewählten Filtern generieren")}</ReactTooltip>
                    </p>
            }]
        }
    }

    _areAllBoxesUnChecked() {
        let keys = Object.keys(this.state.groupUnCheckedStates);
        if (keys.length == 0)
            return false
        return keys.reduce((acc, key) => {
            return acc && this.state.groupUnCheckedStates[key]
        })
    }

    getGroupMenuObjectsForSideMenu(groups) {
        if (!groups || !Array.isArray(groups))
            return {
                heading: {textAndId: this.props.translate("groups","Gruppen"), component: null},
                content: [{component: <div>Du hast noch keine Gruppen</div>}]
            }
        let content = []
        groups.forEach(group => {
            content.push(
                {
                    isSelected: !this.state.groupUnCheckedStates[group.id], //For list item active style
                    component: <Form.Check
                        checked={!this.state.groupUnCheckedStates[group.id]}
                        type="checkbox"
                        disabled={this.state.isLoading}
                        onChange={() => {
                            return null;
                        }}
                        label={group.name}/>,
                    clickCallback: () => {
                        let newState = this.state.groupUnCheckedStates;
                        newState[group.id] = !newState[group.id];
                        if (this._isMounted) this.setState({groupUnCheckedStates: newState}, () => {
                            this.loadEvents();
                        })

                    }
                })
        })

        let headingComponent = <div style={{display: "flex", flexDirection: "row"}}>
            <div style={{...SideMenuHeadingStyle, marginRight: "5px"}}>{this.props.translate("groups","Gruppen")}</div>
            <EducaCardLinkButton
                onClick={() => {
                    if (this._areAllBoxesUnChecked()) {
                        if (this._isMounted) this.setState({groupUnCheckedStates: {}}, () => {
                            this.loadEvents()
                        })
                    } else {
                        let newGroupState = {}
                        groups.forEach(grp => {
                            newGroupState[grp.id] = true
                        })
                        if (this._isMounted) this.setState({groupUnCheckedStates: newGroupState}, () => {
                            this.loadEvents()
                        })
                    }
                }}
                disabled={this.state.isLoading}
            > {this._areAllBoxesUnChecked() ? this.props.translate("task_view.select_all","Alle auswählen") : this.props.translate("task_view.deselect_all","Alle abwählen")}
            </EducaCardLinkButton>
        </div>


        return {
            heading: {textAndId: this.props.translate("groups","Gruppen"), component: headingComponent},
            content: content
        }
    }

    getPreselectedSections() {
        let preselectedSelection = []
        let groups = this.props.store.currentCloudUser.groups
        let unCheckedGroups = this.state.groupUnCheckedStates
        groups.forEach(group => {
            if (!unCheckedGroups[group.id]) {
                let sections = group.sections
                sections.forEach(section => {
                    preselectedSelection.push(section)
                })
            }
        })
        return preselectedSelection
    }

    getArchiveToggleForSideMenu() {
        return {
            heading: {textAndId: this.props.translate("calender_view.view","Ansicht"), component: null}, content: [
                {
                    justComponent: getDisplayPair(this.props.translate("calender_view.view_for","Ansicht für:"), KompostFabrik.parseComponent(
                        {type : WIDGET_TYPES.SELECT,
                            options : this.props.store?.currentCloudUser?.advisor_for.map(function (cloudId) { return {label: cloudId.name, value: cloudId.id}}),
                            value : this.state.viewForCloudId,
                        },(value) => {if (this._isMounted) {
                            this.setState({viewForCloudId: value},() => {
                                this.loadEvents();
                            });
                        }  })
                    )

                }]
        }
    }

    getSideMenu() {
        let menu = [
      //      this.getArchiveToggleForSideMenu(),
            this.getAppsForSideMenu(this.props.store.currentCloudUser.apps),
            this.getFilterEventType(),
            this.getGroupMenuObjectsForSideMenu(this.props.store.currentCloudUser.groups)
        ];
        if( FliesentischZentralrat.globalCalendarEventOutlook())
        {
            menu.push(this.getOutlookSideMenu())
        }
        return <SideMenu
            menus={menu
            }
        >
        </SideMenu>
    }

    render() {
        return <div>
            <div className="d-flex justify-content-between">
                <div style={{width: "300px"}} className={"m-2"}>
                    {FliesentischZentralrat.globalCalendarEventCreate()? <Button onClick={() => {
                        if (this._isMounted) this.setState({toggleNewEvent: true})
                    }}
                                                                                 variant={"light"}
                                                                                 style={{backgroundColor: "rgb(229, 230, 235)", width: "100%"}} className={"mt-2 mb-2"}>
                        <i className="fas fa-plus"></i> {this.props.translate("calender_view.add_event","Termin erstellen")}</Button> : null}

                    <Button
                        onClick={() => {
                            this.invitesModal.current?.open()
                        }}
                        variant={"light"}
                        disabled={!this.state.invitedEvents?.length}
                        style={{backgroundColor: "rgb(229, 230, 235)", width: "100%"}}
                        className={"mb-2"}>
                        <i className="fas fa-envelope-open-text"></i> {this.state.invitedEvents?.length > 0 ? this.state.invitedEvents?.length +" "+ this.props.translate("calender_view.open_invitations"," Offene Einladungen") : this.props.translate("calender_view.no_invitations", "Keine offenen Einladungen")}
                    </Button>
                    {this.getSideMenu()}

                </div>
                <div style={{width: "calc(100vw - 300px)"}} className="mt-2">
                    <EducaCalendar
                        canEdit={ FliesentischZentralrat.globalCalendarEventCreate() }
                        canCreate={ FliesentischZentralrat.globalCalendarEventCreate() }
                        selectedEventId={this.state.urlEventId /* From URL*/}
                        selectedOccurenceDate={this.state.occurrenceDate}
                        selectedUniqueId={this.state.urlUniqueId}
                        selectionChanged={(evt, occurenceDate) => this.setNewEventId(evt, occurenceDate)}
                        toggleNewEvent={this.state.toggleNewEvent}
                        newEventOpenedCallback={() => {
                            if (this._isMounted) this.setState({toggleNewEvent: false})
                        }}
                        loadEventsFunc={(start, end) => this.loadEvents(start, end)}
                        events={this.state.events}
                        preselectedSections={this.getPreselectedSections()}
                        loadEventsCallback={() => this.loadEvents()}/>
                </div>
            </div>
            <EducaCalendarInvitesModal ref={this.invitesModal} events={this.state.invitedEvents}
                                       eventChangedCallback={() => {
                                           this.loadInvites();
                                           this.loadEvents()
                                       }}/>
        </div>
    }

}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(withEducaLocalizedStrings(EducaCalendarViewReact));
