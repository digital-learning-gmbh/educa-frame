import React, {Component} from 'react';
import {Modal} from "react-bootstrap";


const defaultState = {open : false}
class EdbCopyModal extends Component {

    constructor(props) {
        super(props);
        this.state = defaultState
    }

    open(){
        this.setState( {...defaultState, open : true})
    }

    close()
    {
        this.setState( defaultState)
    }

    render() {
        return (
            <Modal
                size={"lg"}
                show={this.state.open}
                onHide={() => this.close()}
            >
                <Modal.Header>
                    <Modal.Title>
                        Veranstaltung kopieren
                    </Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <EDBCopy />
                </Modal.Body>
                <Modal.Footer>
                    <button className="btn btn-primary"><i className="fas fa-copy"></i> Kopieren</button>
                </Modal.Footer>
            </Modal>
        );
    }
}

export default EdbCopyModal;


const EDBCopy = (props) =>
{

    return <div className={"form"}>
        <p className={"alert alert-info"}>Hier können Sie die Veranstaltung kopieren. Bitte wählen Sie die Daten aus, die kopiert werden sollen.</p>
        <div className="form-check">
            <input className="form-check-input" type="checkbox" value="" id="defaultCheck1" />
                <label className="form-check-label" htmlFor="defaultCheck1">
                    Kopieren von Allgemein
                </label>
        </div>
        <div className="form-check">
            <input className="form-check-input" type="checkbox" value="" id="defaultCheck1" />
            <label className="form-check-label" htmlFor="defaultCheck1">
                Kopieren von Details
            </label>
        </div>
        <div className="form-check">
            <input className="form-check-input" type="checkbox" value="" id="defaultCheck1" />
            <label className="form-check-label" htmlFor="defaultCheck1">
                Kopieren von Teilnehmer*innen
            </label>
        </div>
        <div className="form-check">
            <input className="form-check-input" type="checkbox" value="" id="defaultCheck1" />
            <label className="form-check-label" htmlFor="defaultCheck1">
                Kopieren von Referenden*innen
            </label>
        </div>
        <div className="form-check">
            <input className="form-check-input" type="checkbox" value="" id="defaultCheck1" />
            <label className="form-check-label" htmlFor="defaultCheck1">
                Kopieren von Workshops
            </label>
        </div>

    </div>
}
