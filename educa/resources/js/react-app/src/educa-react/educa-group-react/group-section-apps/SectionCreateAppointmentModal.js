import React from "react";
import { Modal } from 'react-bootstrap';
import EventEditor from "../../educa-calendar-react/EventEditor";
import moment from "moment";


function SectionCreateAppointmentModal({show, onClose, onUpdate, onCreate, onDelete, eventToEdit, occurrenceDate, eventDateRange, newEventPreselectedSections}) {

    let onEventUpdated = (evt, close = true) => {
        onUpdate(evt, close)
    }

    let onEventCreated = (evt) => {
        console.log("onEventCreated", evt)
        onCreate(evt)
    }

    let onEventDeleted = (evt) => {
        onDelete(evt)
    }

    let eventCancelCallback = () => {
        onClose()
    }

    return <Modal size={"lg"} show={show} onHide={() => onClose()} style={{width: '100%'}}>
        <Modal.Header closeButton>
            <Modal.Title>
                Jahreskalender - Klausur bearbeiten
            </Modal.Title>
        </Modal.Header>
        <Modal.Body>
            <EventEditor
                borderless={true}
                event={eventToEdit}
                occurrenceDate={occurrenceDate}
                canEdit={true}
                newEventDateRange={eventDateRange}
                newEventPreselectedSections={newEventPreselectedSections}
                hideChoice={true}
                eventDeletedCallback={(evt) => onEventDeleted(evt)}
                eventUpdatedCallback={(evt, close) => onEventUpdated(evt, close)}
                eventCreatedCallback={(evt) => onEventCreated(evt)}
                cancelClickCallback={() => eventCancelCallback()}>
            </EventEditor>
        </Modal.Body>
    </Modal>

}


export default SectionCreateAppointmentModal
