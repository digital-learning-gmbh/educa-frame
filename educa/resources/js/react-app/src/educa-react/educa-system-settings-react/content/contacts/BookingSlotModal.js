import React, {useEffect, useRef, useState} from 'react';
import {EducaCircularButton, EducaDefaultTable} from "../../../../shared/shared-components";
import Button from "react-bootstrap/Button";
import BookingSlotEditor from "./BookingSlotEditor";
import Modal from "react-bootstrap/Modal";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import {useEducaLocalizedStrings} from "../../../helpers/StringLocalizationHelper";
import moment from "moment";
import EducaModal, {MODAL_BUTTONS} from "../../../../shared/shared-components/EducaModal";
import {getDayOptions} from "../../../helpers/EducaHelper.js";

function BookingSlotModal({contact, hide}) {

    const [bookingSlots, setBookingSlots] = useState([])
    const [selectedBookingSlot, setSelectedBookingSlot] = useState()
    const [translate] = useEducaLocalizedStrings()
    const days = getDayOptions(translate)
    let educaModalRef = useRef();

    useEffect(() => {
        if(contact?.id)
            loadBookingSlots(contact.cloudid)
    },[contact])

    const loadBookingSlots = (cloudId) =>
    {
        AjaxHelper.loadSystemSettingsBookingSlots(cloudId)
            .then( resp => {
                setBookingSlots(resp.payload.bookingSlots)
            })
            .catch( () => SharedHelper.fireErrorToast("Fehler", "Terminslots konnten nicht geladen werden."))
    }

    let onDeleteClick = (slot) => {

        const exec = () => {
            AjaxHelper.deleteSystemSettingsBookingSlot(slot.id)
                .then(resp => {
                    if (resp.status > 0) {
                        SharedHelper.fireSuccessToast("Erfolg", "Der Terminslot wurde erfolgreich gelöscht")
                        return loadBookingSlots(contact.cloudid)
                    } else
                        throw new Error(resp.message)
                })
                .catch(err => {
                    SharedHelper.fireErrorToast("Fehler", "Fehler beim löschen des Terminslots. " + err.message)
                })
        }

        educaModalRef?.current?.open(
            (btn) => btn == MODAL_BUTTONS.YES? exec() : null,
            "Terminslot löschen",
            "Soll der Terminslot wirklich gelöscht werden?",
            [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
        )
    }


    return (
        <Modal show={!!contact} onHide={() => hide()} size={"xl"}>
            <Modal.Header closeButton={true}>
                <Modal.Title>
                    Terminslots
                </Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <EducaDefaultTable
                    customButtonBarComponents={[
                        <Button
                            variant={"primary"}
                            onClick={() => setSelectedBookingSlot({})}
                        >
                            <i className={"fas fa-plus"}/> Terminslot hinzufügen
                        </Button>,
                    ]}
                    columns={[
                        {Header: "Tag", accessor: "dayComp"},
                        {Header: "Start", accessor: "startComp"},
                        {Header: "Ende", accessor: "endComp"},
                        {Header: "Slot (min)", accessor: "slot_duration"},
                        {Header: "Pause (min)", accessor: "slot_breaks"},
                        {Header: "", accessor: "actions"},
                    ]}
                    data={bookingSlots? bookingSlots.map(slot =>
                    {
                        return {...slot,
                            dayComp : days?.find( d => d.value == slot.day_week)?.label,
                            startComp : slot.start,
                            endComp : slot.end,
                            actions: <>
                                <EducaCircularButton size={"small"} onClick={() => setSelectedBookingSlot(slot)}><i className={"fa fa-pencil-alt"}/></EducaCircularButton>
                                <EducaCircularButton className={"ml-1"} variant={"danger"} size={"small"}
                                                     onClick={() => onDeleteClick(slot)}><i
                                    className={"fas fa-trash"}/></EducaCircularButton>
                            </>
                        }
                    }) : []}
                />
                <BookingSlotEditor cloudId={contact?.cloudid}
                                   bookingSlots={bookingSlots}
                                   bookingSlot={selectedBookingSlot}
                                   hide={(reload) => {
                                       if(reload)
                                           loadBookingSlots(contact.cloudid);
                                       setSelectedBookingSlot()
                                   }}/>
            </Modal.Body>
            <EducaModal ref={educaModalRef} />
            <Modal.Footer>
                <Button variant={"secondary"} onClick={() => hide()}>
                    Schließen
                </Button>
            </Modal.Footer>
        </Modal>
    );
}

export default BookingSlotModal;
