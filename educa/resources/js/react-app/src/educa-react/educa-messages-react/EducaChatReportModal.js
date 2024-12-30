import React, {Component} from 'react';

import Button from "react-bootstrap/Button";
import Modal from "react-bootstrap/Modal";
import TextareaAutosize from "react-textarea-autosize";
import ReactSwitch from "react-switch";
import moment from "moment";

export const MODAL_BUTTONS =
    {
        YES : "YES",
        NO : "NO",
        CANCEL : "CANCEL",
        OK : "",
    }

class EducaChatReportModal extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isOpen : false,
                header : null,
                body : null,
                msgProps : {},
                messageChunk : {},
                additionalInfo : "",
                isTechnical : false,
                callback : () => {},
            }
    }

    componentDidMount() {
        this._isMounted = true
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    open( callbackFunc, msgProps, members, messageChunk )
    {
        let msgs = JSON.parse(JSON.stringify(messageChunk))
        msgs?.messages?.reverse()
        let index = null
        msgs?.messages?.forEach(  (m, i) =>
        {
            if(m._id === msgProps?.id)
                index = i
        })

        if(this._isMounted) this.setState({
            isOpen : true,
            callback : callbackFunc,
            msgProps : msgProps,
            messageChunk : msgs,
            startIndex : index,
            members : members
        })
    }

    render() {
        return <Modal
                size={"lg"}
                show={this.state.isOpen}
                backdrop={"static"}
                onHide={() => {this.setState({isOpen : false})}}
            >
                <Modal.Header>
                    <Modal.Title>
                        Meldung einreichen
                    </Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <div style={{display:"flex", flexDirection :"column"}}>
                        <div>
                            <div style={{display :"flex", flexDirection :"column"}}>
                                <h5>Gemeldete Nachricht</h5>
                                <div><b>{this.state.msgProps.from}</b> schrieb am {moment(this.state.msgProps.date).format("DD.MM.YYYY HH:mm")}</div>
                                <div style={{wordWrap :"anywhere"}}><i>{this.state.msgProps.message}</i></div>
                            </div>
                            <hr/>
                            <h5>Protokoll</h5>
                            <div style={{overflow :"auto", maxHeight : "250px"}}>
                            {this.state.messageChunk?.messages?.map( (m,index) =>  {
                            if( index <= this.state.startIndex +2 )
                            {
                                return <div key={m._id}>
                                    <b>{m.u?.name}</b> : <div style={{wordWrap :"anywhere"}}>{m.msg}</div>
                                </div>
                            }
                             return null})}
                            </div>
                        </div>
                        <div>
                            <hr/>
                            <h5>Zusätzliche Informationen</h5>
                            <TextareaAutosize
                                minRows={8}
                                style={{width :"100%"}}
                                className={"form-control"}
                                value={this.state.additionalInfo}
                                onChange={(evt) => this.setState({additionalInfo : evt.target.value})}
                            ></TextareaAutosize>
                        </div>
                    </div>

                </Modal.Body>
                <Modal.Footer>
                <Button
                    variant={"primary"}
                    onClick={() => this.state.callback( true,
                        {
                            msgObj : this.state.msgObj,
                            msgChunk : this.state.messageChunk,
                            isTechnical : this.state.isTechnical,
                            additionalInfo:  this.state.additionalInfo,
                            members : this.state.members
                        })}
                >
                    Meldung Einreichen
                </Button>
                <Button
                    variant={"secondary"}
                    onClick={() => this.setState({isOpen : false})}
                >
                    Abbrechen
                </Button>
                </Modal.Footer>
            </Modal>
    }
}

export default EducaChatReportModal;
