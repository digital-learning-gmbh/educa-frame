import React, {Component} from 'react';
import {Button} from "react-bootstrap";
import TextareaAutosize from 'react-textarea-autosize';
import EducaHelper, {LIMITS} from "../../../helpers/EducaHelper";
import {RCHelper} from "../../RocketChatHelper";

const styles =
    {
        textInput:
            {
                root: {
                    paddingRight: "15px",
                    paddingLeft: "20px",
                    marginBottom: "35px",
                    marginTop: "5px"
                }
            },
        button:
            {
                maxHeight: "40px"
            },
        recordButton:
            {
                Off:
                    {
                        maxHeight: "40px"
                    },

                On:
                    {
                        color: "red",
                    },

            }
    }


let isShiftPressed = false

// key 16 : shift
// key 13: Enter
window.document.addEventListener("keydown",function (e) {
    if (e.which === 16)
        isShiftPressed = true;

});

window.document.addEventListener("keyup",function (e) {
    if (e.which === 16)
        isShiftPressed = false
});


class TextInputComponent extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isRecording: false
            }
        this.mediaRecorder = null
        this.chunks = []
    }


    recordingButtonClicked() {

        if (!this.state.isRecording) {

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia)
                return EducaHelper.fireErrorToast("Fehler", "Für deinen Browser ist dieses Feature nicht verfügbar")

            navigator.mediaDevices.getUserMedia({audio: true})
                .then(stream => stream)
                .catch(err => console.error('Unable to get Voice stream', err))
                .then((stream) => {

                    this.mediaRecorder = new MediaRecorder(stream, {mimeType : "audio/webm;codecs=opus"})
                    this.setState({isRecording: true}, () => this.mediaRecorder.start())
                    this.mediaRecorder.onstop = (e) => {
                    }

                    // on stop
                    this.mediaRecorder.ondataavailable = (e) => {

                        let blob = e.data
                        this.props.recordFinishedCallback(blob)
                        this.setState({isRecording: false})
                    }

                })
        }
        if (this.state.isRecording) {
            this.mediaRecorder.stop()

        }

    }


    render() {
        return (
            <div className="input-group mb-3" style={styles.textInput.root}>
                <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}}>
                    <div style={{display: "flex", flexDirection: "row", flex: 1}}>
                        { this.props.hideEmoji ? <></>:
                        <Button
                            variant="outline-dark"
                            className="m-1"
                            style={styles.button}
                            onClick={() => {
                                this.props.emojiClickCallback()
                            }}
                            type="button"><i className="fa fa-smile"/>
                        </Button> }
                        <input
                            multiple
                            type="file"
                            id={"input_" + this.props.uniqueID}
                            onChange={(evt) => {
                                this.props.fileUploadOnChangeCallback(evt)
                            }}
                            style={{width: "0px", display: "none"}}/>
                        <Button
                            title={"Datei hochladen"}
                            variant="outline-dark"
                            className="m-1"
                            style={styles.button}
                            onClick={() => {
                                document.getElementById('input_' + this.props.uniqueID).click()
                            }}
                            type="button"><i className="fa fa-plus"></i>
                        </Button>
                        { this.props.hideMic ? <></>:
                            <Button
                            title={"Sprachnachricht aufnehmen"}
                            variant="outline-dark"
                            className="m-1"
                            style={this.state.isRecording ? {...styles.button, ...styles.recordButton.On} : {...styles.button, ...styles.recordButton.Off}}
                            onClick={() => this.recordingButtonClicked()}
                            type="button"><i className="fa fa-microphone"></i>
                        </Button> }
                        <input type="file" id="my_file" style={{display: "none"}}/>

                    </div>
                </div>
                <div style={{display :"flex", flexDirection : "column", flex : 1}}>
                    {this.props.citation?
                        <div>
                            <div className={"m-1"} style={{display :"flex", flexDirection : "row", flex : 1}}>
                                <div>Antworten auf</div>
                                <div style={{display :"flex", flexDirection : "row", flex : 1, justifyContent : "flex-end"}}>
                                    <Button
                                        variant={"danger"}
                                        size={"sm"}
                                        onClick={() => this.props.resetCitation()}>
                                        <i className={"fas fa-times"}/>
                                    </Button>
                                </div>
                            </div>
                            <div style={ RCHelper.getCitationStyle()}>
                                {this.props.citation.message}
                            </div>
                        </div>
                  : null}
                <TextareaAutosize
                    maxLength={LIMITS.CHAT_MESSAGE_LIMIT}
                    type="text"
                    minRows={1}
                    className="form-control m-1"
                    id={"textarea_" + this.props.uniqueID}
                    onKeyDown={(evt) => {
                        if (evt.key === "Enter" && isShiftPressed)
                            return // Linebreak is automatically added
                        else if (evt.key === "Enter")
                            this.props.clickCallback(document.getElementById("textarea_" + this.props.uniqueID).value, () => document.getElementById("textarea_" + this.props.uniqueID).value = "")
                        if(document.getElementById("textarea_" + this.props.uniqueID).value.length > LIMITS.CHAT_MESSAGE_LIMIT - 5)
                        {
                            EducaHelper.fireWarningToast("Hinweis","Das Zeichenlimit für Text-Nachrichten liegt bei "+ LIMITS.CHAT_MESSAGE_LIMIT + " Zeichen");
                        }
                    }}
                    placeholder="Schreibe eine Nachricht..."/>
                </div>
                <Button
                    className="btn btn-primary m-1"
                    onClick={() => {
                        this.props.clickCallback(document.getElementById("textarea_" + this.props.uniqueID).value, () => document.getElementById("textarea_" + this.props.uniqueID).value = "")
                    }}
                    type="button"><i className="fas fa-paper-plane"></i> Senden
                </Button>
                <div className="input-group-append">
                </div>
            </div>
        );
    }
}

export default TextInputComponent;
