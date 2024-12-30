import React, {Component} from 'react';

import Button from "react-bootstrap/Button";
import Modal from "react-bootstrap/Modal";
import {FormControl} from "react-bootstrap";


class SafeDeleteModal extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isOpen : false,
                header : null,
                body : null,
                keyword : "_______",
                input : "",
                callback : () => {},
            }
    }

    componentDidMount() {
        this._isMounted = true
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    open( callbackFunc, header, body, keyword )
    {
        if(this._isMounted) this.setState({
            isOpen : true,
            callback : callbackFunc,
            header : header,
            body : body,
            input : "",
            keyword : keyword
        })
    }

    getOKButton()
    {
        return <Button
            onClick={()=> {this.state.callback(true); this.setState({isOpen : false})}}
            variant={this.props.okayVariant? this.props.okayVariant : "danger"}
            disabled={!this.state.input || !this.state.keyword || this.state.input?.toLowerCase() !== this.state.keyword?.toLowerCase()}
            className={"m-2"}>{this.props.okayLabel? this.props.okayLabel : "OK"}</Button>
    }
    getCancelButton()
    {
        return <Button
            onClick={()=> {this.state.callback(false); this.setState({isOpen : false})}}
            variant={"secondary"}>Abbrechen</Button>
    }

    render() {
        return <Modal
            show={this.state.isOpen}
            backdrop={"static"}
            onHide={() => {this.setState({isOpen : false})}}
        >
            <Modal.Header  closeButton={!!this.props.closeButton}>
                <Modal.Title>
                    {this.state.header}
                </Modal.Title>
            </Modal.Header>
            <Modal.Body>
                {this.state.body}
                <FormControl
                 placeholder={""+this.state.keyword}
                 value={this.state.input}
                 onChange={(evt) => this.setState({input : evt.target.value})}
                />
            </Modal.Body>
            <Modal.Footer>
                {this.props?.buttonsSwitched?
                    <>{this.getCancelButton()}{this.getOKButton()}</>
                    :
                    <>{this.getOKButton()}{this.getCancelButton()}</>}
                    </Modal.Footer>
        </Modal>
    }
}

export default SafeDeleteModal;
