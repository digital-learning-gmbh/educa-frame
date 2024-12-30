import DatePicker from "react-datepicker";
import Button from "react-bootstrap/Button";
import React, {useRef, useState} from "react";
import {FormControl, InputGroup} from "react-bootstrap";
import "./datepicker.css"
import moment from "moment";

export default function DatePickerBox(props) {

    const calendarRef = useRef()
    const [stateHasFocus, setStateHasFocus] = useState(false);

    class CustomInput extends React.Component {

        render() {
            return <div style={{display : "flex", flexDirection : "row"}}>
                <InputGroup style={{width : props.inputWidth}}>
                    <InputGroup.Prepend>
                        <Button
                            disabled={props.disabled}
                            style={{zIndex: 0}}
                            onClick={() => {
                                calendarRef.current?.setOpen(true)
                            }} variant={"secondary"}><i
                            className="far fa-calendar-alt"/>
                        </Button>
                    </InputGroup.Prepend>
                    <FormControl placeholder={props.placeholder}
                                 disabled={props.disabled}
                                 {...this.props}
                                 autoFocus={stateHasFocus}
                    />
                </InputGroup>
                {props.deleteButton ?
                    <div style={{display :"flex", flexDirection: "column", justifyContent :"center", zIndex : 3, marginLeft : "-22px"}}>
                        <div
                            onClick={() => {
                                props.onDateChange(null)
                            }}
                        ><i className="fas fa-trash"></i></div>
                    </div> : null}
            </div>
        }
    }

    // Copy pasta KompostFabrik
    let date = props.date
    if(date === null)
        date = null
    else if( date === "" )
        props.onDateChange? props.onDateChange(null) : null
    else if(typeof date === 'string')
    {
        date = moment(date)
        props.onDateChange? props.onDateChange(date) : null
    }
    else if(Number.isInteger(date))
        date = moment.unix(date)


    return <DatePicker
        className="customDatePickerWidth form-control"
        showMonthDropdown={!!props.showMonthDropdown}
        showYearDropdown={!!props.showYearDropdown}
        ref={calendarRef}
        disabled={props.disabled}
        selected={date && typeof date?.isValid == "function" && date.isValid() && date?.isValid()? date.toDate() : null}
        timeIntervals={props.timeIntervals? props.timeIntervals : 10}
        onChange={date => {
            props.onDateChange(moment(date))
        }}
        locale="de-DE"
        timeCaption={props.timeCaption ? props.timeCaption : ""}
        showTimeSelect={props.showTime ? true : false}
        dateFormat={props.showTime ? "dd.MM.yyyy  HH:mm" : "dd.MM.yyyy"}
        customInput={<CustomInput/>}
        maxDate={props.maxDate? props.maxDate : undefined}
        minDate={props.minDate? props.minDate : undefined}
        onFocus={evt => {
            setStateHasFocus(true);
            if (props.onFocus) {
                props.onFocus.call(this, evt);
            }
        }}
        onBlur={evt => {
            setStateHasFocus(false);
            if (props.onBlur) {
                props.onBlur.call(this, evt);
            }
        }}
    />
}
