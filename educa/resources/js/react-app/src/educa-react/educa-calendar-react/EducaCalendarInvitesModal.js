import React from "react";
import Modal from "react-bootstrap/Modal";
import {ListGroup} from "react-bootstrap";
import moment from "moment";
import Button from "react-bootstrap/Button";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import {INVITATION_STATES} from "./EducaCalendar";
import EducaHelper from "../helpers/EducaHelper";

export default class EducaCalendarInvitesModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            events: [],
            isOpen: false
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.events && this.props.events?.length !== this.state.events?.length) {
            this.setState({events: this.props.events})
        }
    }

    open() {
        this.setState({isOpen: true, events: this.props.event})
    }

    render() {
        return <Modal
            size={"lg"}
            show={this.state.isOpen}
            onHide={() => this.setState({isOpen: false})}
        > <Modal.Header closeButton>
            <Modal.Title>
                Einladungen bearbeiten
            </Modal.Title>
        </Modal.Header>
            <Modal.Body style={{maxHeight: 'calc(100vh - 210px)', overflowY: 'auto'}}>
                <EducaCalendarInvites
                    eventChangedCallback={() => this.props.eventChangedCallback()}
                    cancelCallback={() => this.setState({isOpen: false, events: []})}
                    events={this.state.events}/>
            </Modal.Body>
        </Modal>
    }
}


function EducaCalendarInvites(props) {


    let updateStatus = (id, status) => {
        AjaxHelper.updateEventInviteStatus(id, status)
            .then(resp => {
                console.log(resp)
                if (resp.status > 0) {
                    props.eventChangedCallback()
                    return EducaHelper.fireSuccessToast("Erfolg", "Dem Termin wurde erfolgreich " + (status == INVITATION_STATES.ACCEPTED ? "zugesagt." : "abgesagt."))
                }

                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Der Termin konnte nicht aktualisiert werden. " + err.message)
            })
    }

    return <ListGroup variant={"flush"}>
        {props.events?.length > 0 ?
            props.events.map((event, index) => {
                let end = moment(event.endDate)
                let start = moment(event.startDate)
                return <ListGroup.Item
                    className={"d-flex"}
                    key={index}>
                    <div className="text-center">
                        <h2><b>{start.format('D')}</b></h2>
                        <h6>{start.locale('de').format('MMMM')}</h6>
                    </div>
                    <div className="ml-2 m-1" style={{overflow: "hidden"}}>
                        <h5 style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>
                            <b>{event.title}</b></h5>
                        <div><i className="fas fa-clock"></i> {start.format("HH:mm")} - {end.format("HH:mm")}</div>
                    </div>
                    <div className={"m-2"} style={{
                        minWidth: "300px",
                        display: "flex",
                        justifyContent: "flex-end",
                        flex: 1,
                        flexDirection: "row",
                        maxHeight: "35px"
                    }}>
                        <Button className={"mr-1"} variant={"success"}
                                onClick={() => updateStatus(event.id, INVITATION_STATES.ACCEPTED)}><i
                            className="fas fa-check"/> Annehmen</Button>
                        <Button variant={"danger"} onClick={() => updateStatus(event.id, INVITATION_STATES.DECLINED)}><i
                            className="fas fa-times"/> Ablehnen</Button>
                    </div>
                </ListGroup.Item>
            })
            :
            <ListGroup.Item>Es gibt noch keine Einladungen für dich. </ListGroup.Item>}
    </ListGroup>
}

