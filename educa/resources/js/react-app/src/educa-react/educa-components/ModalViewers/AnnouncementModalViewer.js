import React from 'react';
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap";
import AnnouncementCard from "../EducaFeedCards/AnnouncementCard";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";
import FliesentischZentralrat from "../../FliesentischZentralrat";

class AnnouncementModalViewer extends React.Component {


    constructor(props) {
        super(props);

        this.state =
            {
                isOpen: false,
                announcement: null
            }
    }

    componentDidMount() {
        this._isMounted = true
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    open(announcementId) {
        if (this._isMounted) this.setState({
            isOpen: true,
        })

        AjaxHelper.getAnnouncementById(announcementId)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.announcement) {
                    return this.setState({announcement: resp.payload.announcement})
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Der Beitrag konnte nicht geladen werden.")
            })
    }


    render() {
        return <Modal
            size={"lg"}
            show={this.state.isOpen}
            onHide={() => this.setState({isOpen: false})}
        >
            <Modal.Body className={"p-0"}>
                {this.state.announcement ?
                    <AnnouncementCard
                        updatedAnnouncementCallback={(a) => this.setState({announcement: a})}
                        deletedAnnouncementCallback={() => this.setState({open: false})}
                        announcement={this.state.announcement}
                    />
                    : null}
            </Modal.Body>
            <Modal.Footer>
                <Button variant={"secondary"} onClick={() => this.setState({isOpen: false})}>Schließen</Button>
            </Modal.Footer>
        </Modal>
    }

}

export default AnnouncementModalViewer;
