import React, {Component} from 'react';
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {EducaLoading} from "../../../shared-local/Loading";
import {Button, Card, Container, FormControl, Spinner} from "react-bootstrap";
import EducaHelper from "../../helpers/EducaHelper";
import QRCode from "react-qr-code";
import MeetingLiveInformation from "../../educa-components/MeetingLiveInformation.jsx";
import FliesentischZentralrat from "../../FliesentischZentralrat.js";
import {DisplayPair} from "../../../shared/shared-components/Inputs.js";
import InputGroup from "react-bootstrap/InputGroup";

class SectionMeetingView extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                sectionMeeting: null,
                isReady: false,
                editMode: false,
            }

    }

    componentDidMount() {
        this._isMounted = true
        this.init()
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.section.id !== prevProps.section.id || this.props.group.id !== prevProps.group.id)
            this.init()
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    init() {
        this.setState({isReady: false})
        AjaxHelper.getSectionMeetingDetails(this.props.section?.id)
            .then(resp => {
                if (resp.status > 0 && resp.payload)
                    this.setState({sectionMeeting: resp.payload.sectionMeeting, editMode: false})
                else
                    throw new Error("")
            })
            .catch(err => {
                console.log(err)
                EducaHelper.fireErrorToast("Fehler", "Die Details des Meetings konnten nicht geladen.")
            })
            .finally(() => {
                this.setState({isReady: true})
            })
    }

    updateMeeting() {
        this.setState({isReady: false})
        AjaxHelper.updateSectionMeetingDetails(this.props.section?.id, this.state.sectionMeeting?.name, this.state.sectionMeeting?.welcomeText)
            .then(resp => {
                if (resp.status > 0 && resp.payload)
                    this.setState({sectionMeeting: resp.payload.sectionMeeting, editMode: false})
                else
                    throw new Error("")
            })
            .catch(err => {
                console.log(err)
                EducaHelper.fireErrorToast("Fehler", "Die Details des Meetings konnten nicht gespeichert werden.")
            })
            .finally(() => {
                this.setState({isReady: true})
            })
    }

    joinMeeting() {
        this.setState({isMeetingLoading: true})
        AjaxHelper.joinMeeting(this.state.sectionMeeting?.modelMeeting?.model_type, this.state.sectionMeeting?.modelMeeting?.model_id)
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

    render() {
        if (!this.state.isReady)
            return <EducaLoading/>
        return (
            <Container className="gedf-wrapper">
                <Card className="mt-2">
                    <Card.Body className={"row"}>
                        {this.state.sectionMeeting == null || this.state.sectionMeeting?.modelMeeting == null ?
                        <div className="offset-4 col-4 text-center"><h1>Keine Videokonferenz erstellt.</h1>
                            <p>Bisher wurde kein Meeting erstellt. Du hast nicht die Berechtigungen einen neuen
                                Raum zu erstellen. Komm später zurück</p></div>
                        : <>
                            <div className="col-4 text-center">
                                <QRCode
                                className={"m-2"}
                                renderAs={"svg"}
                                bgColor={"#F2F3F5"}
                                size={256}
                                value={window.location.origin + "/meeting/join/?model_type=" + this.state.sectionMeeting?.modelMeeting?.model_type + "&model_id=" + this.state.sectionMeeting?.modelMeeting?.model_id + "&pin=" + this.state.sectionMeeting?.modelMeeting?.password_member?.substring(0, 6)}></QRCode>
                            </div>
                            { this.state.editMode ? <div className={"col-8"}>
                                <DisplayPair title={"Anzeigename"}>
                                <FormControl type={"text"} value={this.state.sectionMeeting?.name} onChange={(evt) => this.setState({sectionMeeting : {...this.state.sectionMeeting, name: evt.target.value}})} />
                                </DisplayPair>
                                    <DisplayPair title={"Beschreibung"}>
                                        <FormControl rows={3} as={"textarea"} type={"text"} value={this.state.sectionMeeting?.welcomeText} onChange={(evt) => this.setState({sectionMeeting : {...this.state.sectionMeeting, welcomeText: evt.target.value}})} />
                                    </DisplayPair>
                                <div>
                                    <Button variant={"secondary"} className={"mr-1"} onClick={() => this.init()}>
                                        Abbrechen
                                    </Button>
                                    <Button variant={"success"} className={"mr-1"} onClick={() => this.updateMeeting()}>
                                        Speichern
                                    </Button>
                                </div>
                            </div> :
                                <div className="col-8"><h2>{this.state.sectionMeeting?.name}</h2>
                                    <h4>Externe Personen:</h4>
                                    <p>Externe Nutzer können sich über den folgenden Link einwählen:</p>
                                    <p><InputGroup className="mb-3">
                                        <FormControl
                                            value={window.location.origin + "/meeting/join/?model_type=" + this.state.sectionMeeting?.modelMeeting?.model_type + "&model_id=" + this.state.sectionMeeting?.modelMeeting?.model_id + "&pin=" + this.state.sectionMeeting?.modelMeeting?.password_member?.substring(0, 6)}
                                        />
                                        <InputGroup.Append>
                                            <Button variant="outline-secondary" onClick={() => {navigator.clipboard.writeText(window.location.origin + "/meeting/join/?model_type=" + this.state.sectionMeeting?.modelMeeting?.model_type + "&model_id=" + this.state.sectionMeeting?.modelMeeting?.model_id + "&pin=" + this.state.sectionMeeting?.modelMeeting?.password_member?.substring(0, 6))}}><i className="far fa-copy"></i></Button>
                                        </InputGroup.Append>
                                    </InputGroup>
                                    </p>
                                    <p>
                                        <b>PIN: {this.state.sectionMeeting?.modelMeeting?.password_member?.substring(0, 6)}</b>
                                    </p>
                                    <h4>Beschreibung:</h4>
                                    <p>{this.state.sectionMeeting?.welcomeText}</p>

                                    <h4>Direkt beitreten:</h4>
                                    <div>
                                        {FliesentischZentralrat.sectionMeetingEdit(this.props.section) ?
                                            <Button variant={"secondary"} className={"mr-1"} onClick={() => this.setState({editMode: true})}>
                                                Meeting bearbeiten
                                            </Button>
                                            : null}
                                        <Button onClick={() => this.joinMeeting()} variant={"primary"}>Meeting beitreten
                                            {this.state.isMeetingLoading ? <Spinner
                                                as="span"
                                                animation="grow"
                                                size="sm"
                                                role="status"
                                                aria-hidden="true"
                                            /> : <i className="ml-1 fas fa-external-link-alt"></i>}
                                        </Button>
                                    </div>
                                    <MeetingLiveInformation className={"mt-2"}
                                                            model_id={this.state.sectionMeeting?.modelMeeting?.model_id}
                                                            model_type={this.state.sectionMeeting?.modelMeeting?.model_type}/>
                                </div> }

                            </>
                        }
                        </Card.Body>
                            </Card>
                            </Container>
                            );
                        }
                        }

                        export default SectionMeetingView;
