import React, {Component} from 'react';
import Modal from "react-bootstrap/Modal";
import Form from "react-bootstrap/Form";
import Button from "react-bootstrap/Button";

class FileUploadDialogRC extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isShown: false,
                uploadObjects: []
            }


    }

    prepareUploadObject(filesList) {
        let objs = []
        Array.from(filesList).forEach((file, id) => {
            objs.push(
                {
                    message: "",
                    file: file,
                    key: file.name + "_fu_" + id
                })
        })
        return objs
    }

    /**
     * Call this function to open modal via React.Ref
     * @param filesList
     * @param isFinishedCallback
     */
    open(filesList, isFinishedCallback) {
        this.setState(
            {
                uploadObjects: this.prepareUploadObject(filesList),
                isShown: true,
                isFinishedCallback: isFinishedCallback
            })
    }

    onMessageTextChanged(text, id) {
        let newUploadedObjects = this.state.uploadObjects
        newUploadedObjects[id].message = text
        this.setState({uploadObjects: newUploadedObjects})
    }

    render() {
        return (
            <Modal size="lg"
                   show={this.state.isShown}
                   onHide={() => {
                       this.setState({isShown: !this.state.isShown})
                   }}>
                <Modal.Header closeButton>
                    <Modal.Title>Datei Upload</Modal.Title>
                </Modal.Header>
                <Modal.Body>

                    {this.state.uploadObjects.map((obj, id) => {
                        return <Form key={obj.key}>
                            <Form.Text>Datei {id + 1}: {obj.file.name}</Form.Text>
                            <Form.Control
                                type="text"
                                value={this.state.uploadObjects[id].message}
                                onChange={(evt) => this.onMessageTextChanged(evt.target.value, id)}
                                placeholder={"Nachricht"}>

                            </Form.Control>
                        </Form>
                    })}
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={() => {
                        this.setState({isShown: false})
                    }}>
                        Abbrechen
                    </Button>
                    <Button variant="primary" onClick={() => {
                        this.state.isFinishedCallback(this.state.uploadObjects);
                        this.setState({isShown: false})
                    }}>
                        Hochladen
                    </Button>
                </Modal.Footer>
            </Modal>
        );
    }
}

export default FileUploadDialogRC;
