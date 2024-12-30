import React, {Component} from 'react';
import SharedHelper from "../shared-helpers/SharedHelper";
import PropTypes from "prop-types";
import Modal from "react-bootstrap/Modal";
import Button from "react-bootstrap/Button";
import {Spinner} from "react-bootstrap";
import {getDisplayPair} from "./Inputs";
import TextareaAutosize from "react-textarea-autosize";

const defaultState = {
    open : false,
    image : null,
    text : "",
    isLoading : false,
    requestLoading : false
}

/**
 * npm install --save react html2canvas
 * npm install --save use-react-screenshot
 */
class SupportModal extends Component {

    constructor(props) {
        super(props);
        this.state = defaultState

        this.canvasRef = React.createRef()
    }

    open()
    {
        this.createScreenshot();
        this.setState({open : true})
    }

    close()
    {
        this.setState(defaultState)
    }

    createScreenshot(){


        this.setState({isLoading : true})
        html2canvas(document.querySelector("#react-root"))
            .then(
                canvas => {
                    this.setState({image : canvas.toDataURL("image/jpeg"), isLoading : false})
                }).catch(() =>
        {
            this.setState({isLoading : false})
            SharedHelper.fireErrorToast("Fehler", "Screenshot konnte nicht aufgenommen werden.")
        })

    }

    render() {

        return (
            <Modal
                size={"xl"}
                onHide={()=> this.close()}
                show={this.state.open}>
            <Modal.Title>
                <Modal.Header closeButton={true}>
                    Supportanfrage
                </Modal.Header>
            </Modal.Title>
                <Modal.Body>
                    <div>
                        <b>{this.props.headerText}</b>
                    </div>
                    <div style={{display :"flex", flex : 1, flexDirection :"column"}}>
                        {this.state.isLoading ?
                            <div style={{display :"flex", flex : 1, justifyContent :"center"}}>
                                <Spinner size={"xl"} animation={"grow"} />
                            </div>
                            :
                            <>
                            {getDisplayPair(
                                "Screenshot", <div><img src={this.state.image} style={{maxWidth :"100%", maxHeight : "100%"}}/></div>)}
                            {getDisplayPair(
                                this.props.errorText, <TextareaAutosize
                                    maxLength={this.props.maxCharacters}
                                    autoFocus={true}
                                    className={"form-control"}
                                    value={this.state.text}
                                    minRows={"3"}
                                    onChange={(e) => this.setState({text : e.target.value})} /> )}
                        </>}
                    </div>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant={"secondary"} className={"mr-1"} onClick={() => this.close()}>Abbrechen</Button>
                    <Button variant={"primary"} onClick={() => {
                        this.setState({requestLoading: true})
                        this.props.ajaxSendSupport( this.state.image, this.state.text, () => this.setState({requestLoading: false}))
                    }} disabled={this.state.requestLoading}>Senden</Button>
                </Modal.Footer>
            </Modal>
        );
    }
}


SupportModal.defaultProps = {
    ajaxSendSupport : () => { SharedHelper.fireErrorToast("Fehler", "Support ist aktuell nicht verfügbar. Bitte kontaktieren Sie Ihren Administrator.")},
    headerText: "Deine Anfrage wird direkt per Mail an die IT und den Hersteller geschickt. Du bekommst eine Rückmeldung.",
    errorText: "Fehler beschreiben",
};

SupportModal.propTypes =
    {
        ajaxSendSupport: PropTypes.func.isRequired,
        headerText: PropTypes.string
    }



export default SupportModal;
