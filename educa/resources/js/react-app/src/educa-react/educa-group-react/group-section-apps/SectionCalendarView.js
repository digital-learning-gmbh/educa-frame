import React from "react";
import EducaCalendar, {
    EDUCA_CALENDAR_DEFAULT_END_DATE,
    EDUCA_CALENDAR_DEFAULT_START_DATE
} from "../../educa-calendar-react/EducaCalendar";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {connect} from "react-redux";
import EducaHelper from "../../helpers/EducaHelper";
import FliesentischZentralrat from "../../FliesentischZentralrat";


class SectionCalendarView extends React.Component {

    constructor(props) {
        super(props);

        this.state =
            {
                toggleNewEvent: false,
                events: []
            }
    }

    /**
     * TODO             Swap out this function with a function, that exclusively loads section events
     * @param start
     * @param end
     */
    loadEvents(start = EDUCA_CALENDAR_DEFAULT_START_DATE, end = EDUCA_CALENDAR_DEFAULT_END_DATE) {

        //If redux store does not carry any groups
        if (!this.props.store.currentCloudUser.groups)
            return

        this.setState({isLoading: true})

        AjaxHelper.getEvents(start, end, null, false, [this.props.section.id])
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.events) {
                    this.setState({events: resp.payload.events})
                    return;
                }
                throw new Error(resp.message)

            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Fehler bei der Event Übertragung. Servernachricht: " + err.message)
                this.setState({events: []})
            })
            .finally(() => {
                this.setState({isLoading: false})
            })
    }

    render() {
        return <div>
            <EducaCalendar
                canEdit={ FliesentischZentralrat.sectionCalendarEdit(this.props.section) }
                canCreate={ FliesentischZentralrat.sectionCalendarEdit(this.props.section) }
                preselectedSections={[this.props.section]}
                selectionChanged={(evt) => {
                }}
                toggleNewEvent={this.state.toggleNewEvent}
                newEventOpenedCallback={() => this.setState({toggleNewEvent: false})}
                loadEventsFunc={(start, end) => this.loadEvents(start, end)}
                events={this.state.events}
                loadEventsCallback={() => this.loadEvents()}
                group={this.props.group}
            />
        </div>
    }

}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(SectionCalendarView);
