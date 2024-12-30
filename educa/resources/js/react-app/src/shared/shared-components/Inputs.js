import {FormControl, InputGroup} from "react-bootstrap";
import Button from "react-bootstrap/Button";
import React, {useState} from "react";

export function EducaInputConfirm(props)
{

    let [textFieldChanged, setTextFieldChanged] = useState(false);
    let onChange = props.onChange? props.onChange : (evt) =>{}
    let confirmClicked = props.onConfirmClick? props.onConfirmClick : () =>{}
    const maxLetters = props.maxLetters? props.maxLetters : 35

  return  <InputGroup className="mb-3">
        <FormControl
            placeholder={props.placeholder}
            value={props.value}
            onChange={(evt) =>
            {
                if(evt.target.value?.length <  props.value?.length)
                {
                    onChange(evt)
                }
                else if(typeof props.value === "string" && props.value.length < maxLetters)
                {
                    setTextFieldChanged(true)
                    onChange(evt)
                }

            }}
        />
        {textFieldChanged && props.value ?
            <InputGroup.Append>
            <Button
                onClick={() => {setTextFieldChanged(false); confirmClicked()}}
                variant="success"><i className={"fa fa-check"}/> </Button>
        </InputGroup.Append> : null}
    </InputGroup>
}


export function NumberInput(propsRaw)
{
    const {isClearable, ...props} = propsRaw
    let onChangeNumber = props.onChangeNumber? props.onChangeNumber : (evt) =>{}
    let [error, setError] = useState(false)
    let [minError, setMinError] = useState(false)
    let [maxError, setMaxError] = useState(false)

    const checkValidFloat = (n) =>{
    return /^.*\d.*[.].*\d.*$/.test(n)
    }

    const checkMax = (n) =>
    {
        if(Number.isNaN(props.max))
            return true
        if(n > props.max)
            return false
        return true
    }
    const checkMin = (n) =>
    {
        if(Number.isNaN(props.min))
            return true
        if(n < props.min)
            return false
        return true
    }

    const floatValidation = (evt) =>
    {
        let num = evt.target.value?.replace(",",".")
        if(!num)
        {
            setError(false)
            setMinError(false)
            setMaxError(false)
        }
        else if(!/^\d*?[.]?\d*?$/.test(num))
           return setError(true)
        else if(!checkMin(evt.target.value))
            return setMinError(true)
        else if(!checkMax(evt.target.value))
            return setMaxError(true)
        else
        {
            setError(false)
            setMinError(false)
            setMaxError(false)
        }

        let parsedNum = checkValidFloat(num)?parseFloat(num) : num
        if(num == "0")
            parsedNum = 0

        //Only Trailing zeros
        if(/^.*[.][0]+$/.test(num))
            parsedNum = checkValidFloat(num)? parseFloat(num).toFixed(num.split('.')[1].length) : num
        return onChangeNumber(parsedNum !== null || parsedNum !== undefined? parsedNum : "" )
    }

    const intValidation = (evt) =>
    {
        let num = evt.target.value
        if(!num)
        {
            setError(false)
            setMinError(false)
            setMaxError(false)
        }

        else if(!/^\d+$/.test(evt.target.value))
            return setError(true)
        else if(!checkMin(evt.target.value))
            return setMinError(true)
        else if(!checkMax(evt.target.value))
            return setMaxError(true)
        else
        {
            setError(false)
            setMinError(false)
            setMaxError(false)
        }

        num = parseInt(num)?parseInt(num) : num
        return onChangeNumber(num? num : "" )
    }
    return  <InputGroup className={propsRaw?.inputClassName? propsRaw?.inputClassName: "mb-3"}>
        <div style={{display :"flex", flexDirection : "column", flex : 1}}>
            {error? <div style={{fontSize : "80%", color :"red"}}>
                { props.isFloat? "Bitte nur ganze oder Gleitpunktzahlen eingeben." : "Bitte nur ganze Zahlen eingeben."}</div> : null}
            {maxError? <div style={{fontSize : "80%", color :"red"}}>
                {"Der eingegeben Wert darf "+props.max+" nicht überschreiten."}</div>: null}
            {minError? <div style={{fontSize : "80%", color :"red"}}>
                {"Der eingegeben Wert darf "+props.min+" nicht unterschreiten."}</div> : null}
            <FormControl
                tabIndex={props.tabIndex? props.tabIndex : undefined}
                style={isClearable? {borderTopRightRadius :"0px", borderBottomRightRadius :"0px"} : {}}
                onBlur={() => {
                    setError(false);
                    setMinError(false)
                    setMaxError(false)}
                }
                placeholder={props.placeholder}
                value={props.value == null || props.value === undefined? "": (""+props.value)/*?.replace(".",",")*/}
                disabled={props.disabled}
                onChange={(evt) =>
                {
                  return props.isFloat? floatValidation(evt) : intValidation(evt)
                }}
            />
        </div>
        {isClearable?<InputGroup.Append>
            <Button style={{borderColor : "rgb(206, 212, 218)"}} onClick={()=> onChangeNumber(undefined)} variant={"outline-dark"}>
                <i className={"fas fa-times"} style={{color : "rgb(206, 212, 218)"}}/>
            </Button>
        </InputGroup.Append> : null}
    </InputGroup>
}


export function EducaFormControl(props) {

    const {isClearable, ...cleanedProps} = props
    return <InputGroup>
        <FormControl {...cleanedProps}/>
        {isClearable?<InputGroup.Append>
            <Button style={{borderColor : "rgb(206, 212, 218)"}} onClick={()=> props.onChange({target : {value : ""}})} variant={"outline-dark"}>
            <i className={"fas fa-times"} style={{color : "rgb(206, 212, 218)"}}/>
        </Button>
        </InputGroup.Append> : null}
    </InputGroup>
}

/**
 * @deprecated Use <DisplayPair>
 */
export function getDisplayPair(title, content, hasError)
{
    let comp =  <div style={{display :"flex", flexDirection : "column", flex : 1}} className={"m-2"}>
        <div style={{marginBottom :"-2px", fontSize :"12px", display: "flex", flexDirection :"row"}}>{title}</div>
        <div>{content}</div>
    </div>

    return hasError? <div style={{ display :"flex", flex : 1,border: "1px solid rgba(255, 0, 0, 0.58)", borderRadius : "10px", marginBottom : "2px"}}>
        {comp}
    </div> : comp
}

export const DisplayPair = ({title, hasError, children}) =>
{
    let comp =  <div style={{display :"flex", flexDirection : "column", flex : 1}} className={"m-2"}>
        <div style={{marginBottom :"-2px", fontSize :"12px", display: "flex", flexDirection :"row"}}>{title}</div>
        <div>{children}</div>
    </div>

    return hasError? <div style={{ display :"flex", flex : 1,border: "1px solid rgba(255, 0, 0, 0.58)", borderRadius : "10px", marginBottom : "2px"}}>
        {comp}
    </div> : comp
}


