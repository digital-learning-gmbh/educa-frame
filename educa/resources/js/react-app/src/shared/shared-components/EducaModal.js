import React, {Component} from 'react';

import Button from "react-bootstrap/Button";
import Modal from "react-bootstrap/Modal";

export const MODAL_BUTTONS =
    {
        YES : "YES",
        NO : "NO",
        CANCEL : "CANCEL",
        OK : "",
        CLOSE : "Schließen",
        SAVE : "Speichern",
        SAVE_PRIMARY: "Speichern_primary",
    }

const defaultState = {
    isOpen : false,
    header : null,
    body : null,
    callback : () => {},
    buttons : [],
    customButtons : []
}
class EducaModal extends Component {

    constructor(props) {
        super(props);

        this.state = defaultState
    }

    componentDidMount() {
        this._isMounted = true
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    open(callbackFunc, header, body, buttons = [MODAL_BUTTONS.OK, MODAL_BUTTONS.CANCEL] )
    {
        if(this._isMounted) this.setState({
            ...defaultState,
            isOpen : true,
            callback : callbackFunc,
            header : header,
            body : body,
            buttons : buttons,
        })
    }

    openWithCustomButtons(header, body, customButtons = [], modalButtons = [])
    {
        if(this._isMounted) this.setState({
            ...defaultState,
            isOpen : true,
            header : header,
            body : body,
            customButtons : customButtons,
            buttons : modalButtons
        })
    }

    close()
    {
        if(this._isMounted) this.setState(defaultState)
    }

    getButton(btn, key)
    {

        let classname= "btn-primary"
        let content = ""
        if( btn === MODAL_BUTTONS.NO)
        {
            classname="btn-secondary"
            content="Nein"
        }
        else if( btn === MODAL_BUTTONS.CANCEL)
        {
            classname="btn-secondary"
            content = "Abbrechen"
        }
        else if( btn === MODAL_BUTTONS.YES)
        {
            classname="btn-primary"
            content = "Ja"
        }
        else if( btn === MODAL_BUTTONS.OK)
        {
            classname="btn-primary"
            content = "OK"
        }
        else if( btn === MODAL_BUTTONS.CLOSE)
        {
            classname="btn-secondary"
            content = "Schließen"
        }  else if( btn === MODAL_BUTTONS.SAVE)
        {
            classname="btn-success"
            content = "Speichern"
        } else if(btn === MODAL_BUTTONS.SAVE_PRIMARY)
        {
            classname = "btn-primary"
            content = "Speichern"
        }
            return  <Button
                key={key}
                onClick={()=> {this.state.callback(btn); this.close()}}
                className={classname}>{content} </Button>

    }
    render() {
        return <Modal
                size= {this.props.size?this.props.size : "sm"}
                show={this.state.isOpen}
                backdrop={this.props.noBackdrop?undefined:"static"}
                onHide={() => {this.close()}}
            >
                <Modal.Header closeButton={!!this.props.closeButton}>
                    <Modal.Title>
                        {this.state.header}
                    </Modal.Title>
                </Modal.Header>
            <Modal.Body> {this.state.body}</Modal.Body>
            {this.state.buttons?.length > 0 || this.state.customButtons?.length?
                <Modal.Footer>
                    {this.state.customButtons?.map((btn, i) =>  <btn.type
                         {...btn.props}
                         key={i}
                         onClick = { (e) => {if(typeof btn.props.onClick == "function")  btn.props.onClick(e); this.close() } }/>
                    )}
                    {this.state.buttons?.map((btn, id) => this.getButton(btn, "modal_btn" + id))}
                </Modal.Footer>
                : null}
        </Modal>
    }
}

export default EducaModal;
