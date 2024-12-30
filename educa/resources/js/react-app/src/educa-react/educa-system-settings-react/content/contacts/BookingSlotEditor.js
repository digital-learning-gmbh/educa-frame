import React, {useEffect, useState} from 'react';
import Modal from "react-bootstrap/Modal";
import {Alert, Button, Col, Row} from "react-bootstrap";
import {DisplayPair, NumberInput} from "../../../../shared/shared-components/Inputs";
import {DatePickerBox} from "../../../../shared/shared-components";
import {useEducaLocalizedStrings} from "../../../helpers/StringLocalizationHelper";
import Select from "react-select";
import moment from "moment";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import {getDayOptions} from "../../../helpers/EducaHelper.js";

const defaultBs = {
    day_week : 1,
    start : undefined,
    end : undefined,
    slot_duration : 15,
    slot_breaks : 5
}
function BookingSlotEditor({cloudId, bookingSlots, bookingSlot, hide}) {

    const [translate] = useEducaLocalizedStrings()
    const days = getDayOptions(translate)//?.filter( day => !bookingSlots?.find( b => b.day_week == day.value))

    const [bs, setBs] = useState(defaultBs)

    useEffect(() => {
        setBs(bookingSlot?.id? {...bookingSlot, start : moment(bookingSlot.start, "HH:mm"), end : moment(bookingSlot.end, "HH:mm")} : defaultBs)
    },[bookingSlot])

    const calculateCountSlots = () => {
        if(!bs.start || !bs.end || !(bs.slot_duration>0))
            return null
        let count = parseInt(""+(moment(bs.end).diff(bs.start, "minutes") / (bs.slot_duration + bs.slot_breaks)))
        if(count > 0)
            return count
    }
    const countSlots = calculateCountSlots()

    const save = () => {
        if(!countSlots)
            return

        let promise
        if(bookingSlot.id)
            promise = AjaxHelper.updateSystemSettingsBookingSlot(bookingSlot.id, {...bs, start : bs.start.unix(), end : bs.end.unix(), slot_breaks: bs.slot_breaks??0})
        else
            promise = AjaxHelper.createSystemSettingsBookingSlot(cloudId, {...bs, start : bs.start.unix(), end : bs.end.unix(), slot_breaks: bs.slot_breaks??0})

        promise.then( () => {
            SharedHelper.fireSuccessToast("Erfolg", "Der Terminslot wurde erstellt.")
            hide(true)
        })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Der Terminslot konnte nicht erstellt werden."))

    }

    return (
        <Modal size={"lg"} show={!!bookingSlot} onHide={() => hide()} style={{background : "rgba(0,0,0,0.4)"}}>
            <Modal.Header closeButton={true}>
                <Modal.Title>
                    Terminslot {bookingSlot?.id? "bearbeiten" : "anlegen"}
                </Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <Col>
                    <Row>
                        <DisplayPair title={"Wochentag"}>
                            <Select
                                placehoder={"Tag auswählen"}
                                options={days}
                                value={days?.find( d => d.value == bs?.day_week)}
                                onChange={ele => setBs({...bs, day_week : ele.value})}
                            />
                        </DisplayPair>
                    </Row>
                    <Row>
                        <DisplayPair title={"Start"}>
                            <DatePickerBox date={bs.start} onDateChange={d => {setBs({...bs, start : d})}} fullWidth={true} showTimeSelectOnly={true}/>
                        </DisplayPair>
                        <DisplayPair  title={"Ende"}>
                            <DatePickerBox date={bs.end} onDateChange={d => setBs({...bs, end : d})} fullWidth={true} showTimeSelectOnly={true}/>
                        </DisplayPair>
                    </Row>
                    <Row>
                        <DisplayPair title={"Slot (min)"}>
                            <NumberInput min={0}
                                         max={1200}
                                         value={bs.slot_duration}
                                         onChangeNumber={(num) => setBs({...bs, slot_duration : num})}
                            />
                        </DisplayPair>
                        <DisplayPair title={"Pause (min)"}>
                            <NumberInput min={0}
                                         max={1200}
                                         value={bs.slot_breaks}
                                         onChangeNumber={(num) => setBs({...bs, slot_breaks : num})}/>
                        </DisplayPair>
                    </Row>
                </Col>
                {bs?.day_week >= 0 && countSlots?
                    <Alert variant={"info"}> Am Wochentag <b>{days?.find( d => d.value == bs?.day_week)?.label}</b> gibt
                        es <b>{countSlots}</b> Slot{countSlots>1?"s":""} zu je <b>{bs.slot_duration} Minuten</b> mit anschließenden Pausen von <b>{bs?.slot_breaks} Minuten.</b></Alert> : null}
            </Modal.Body>
            <Modal.Footer>
                <Button variant={"success"} disabled={!countSlots} onClick={() => save()}>
                    Speichern
                </Button>
                <Button variant={"secondary"} onClick={() => hide()}>
                    Schließen
                </Button>
            </Modal.Footer>
        </Modal>
    );
}

export default BookingSlotEditor;
