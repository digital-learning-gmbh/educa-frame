import React, {useEffect, useState} from "react";
import SharedHelper from "../shared-helpers/SharedHelper";
import {FormControl, InputGroup} from "react-bootstrap";
import Button from "react-bootstrap/Button";
import TimeKeeper from "react-timekeeper";
import {EducaCardLinkButton} from "./Buttons";

export default function TimePickerBox(props) {
    let [isShown, setIsShown] = useState(false)
    let [uid, setUid] = useState("")
    let [text, setText] = useState(props.dateTime ? props.dateTime.format("HH:mm") : "")
    const [stateHasFocus, setStateHasFocus] = useState(false);

    useEffect(() => {
        setUid(SharedHelper.createUUID)
    }, [])

    useEffect(() => {
        setText(props.dateTime ? props.dateTime.format("HH:mm") : "")
    }, [props.dateTime])

    useEffect(() => {
        if (isShown)
            document.addEventListener('click', outsideClickListener)

        //clean up after unmount
        return () => {
            document.removeEventListener('click', outsideClickListener)
        }
    }, [isShown])

    let momentToTime = () => {
        if (!props.dateTime)
            return {hour: 0, minute: 0}
        return {hour: parseInt(props.dateTime.format("HH")), minute: parseInt(props.dateTime.format("mm"))}
    }

    let timeToMoment = (time) => {
        if (!props.dateTime)
            return null
        let t = props.dateTime
        return t.set({"hour": time.hour, "minute": time.minute})
    }

    let outsideClickListener = event => {
        let element = document.getElementById(uid)
        if(!element)
            return
        if (!element.contains(event.target)) {
            document.removeEventListener('click', outsideClickListener)
            setIsShown(false)
        }
    }


    return <div>
        <InputGroup>
            <InputGroup.Prepend>
                <Button
                    style={{zIndex: 0}}
                    onClick={() => {
                        setIsShown(true)
                    }} variant={"secondary"}><i
                    className="fa fa-clock"/></Button>
            </InputGroup.Prepend>
            <FormControl
                onChange={(e) => {
                    if (!props.dateTime)
                        return
                    let string = e.target.value?.trim();
                    setText(string)
                    let parts = string.split(":")
                    if(parts.length === 2 && !isNaN(parseInt(parts[0])) && !isNaN(parseInt(parts[1])) && parseInt(parts[0]) >= 0 && parseInt(parts[0]) <= 24 && parseInt(parts[1]) >= 0 && parseInt(parts[1]) <= 59)
                    {
                        let time = {};
                        time.hour = parseInt(parts[0]);
                        time.minute = parseInt(parts[1]);
                        let t = props.dateTime
                        t.set({"hour": time.hour, "minute": time.minute})
                        props.timeChanged(t, time)
                    }
                        }}
                onBlur={() =>
                    setText(props.dateTime?.format("HH:mm"))}
                onClick={() => setIsShown(true)}
                value={text}
                placeholder="Uhrzeit"
            />
        </InputGroup>
        {isShown ?
            <div
                id={uid}
                style={{position: "fixed", zIndex: 3, display: "inline-block"}}>
                <TimeKeeper
                    style={{display: "none"}}
                    switchToMinuteOnHourSelect={true}
                    closeOnMinuteSelect={true}
                    hour24Mode={true}
                    doneButton={(newTime) => (
                        <div
                            onClick={() => setIsShown(false)}
                            style={{textAlign: 'center', padding: '10px 0', cursor: "pointer"}}
                        >
                            <EducaCardLinkButton
                            >Schließen</EducaCardLinkButton>
                        </div>
                    )}
                    time={momentToTime()}
                    onChange={(data) => {
                        props.timeChanged(timeToMoment(data), data)
                        setText(props.dateTime?.format("HH:mm"))
                    }}
                /></div> : null}
    </div>
}
