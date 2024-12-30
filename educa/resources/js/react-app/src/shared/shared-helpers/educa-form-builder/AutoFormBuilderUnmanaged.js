import PropTypes from 'prop-types';
import KompostFabrik from "../KompostFabrik";
import React, {useEffect} from "react";
import {changeFormData, insertValueToFormdataObj} from "./educa-form-helpers";


const AutoFormBuilderUnmanaged =  ({   cols,
                               template,
                               formData,
                               unanswered,
                               onFormDataChanged,
                               readOnly}) => {

    useEffect( () =>
    {
        return () => unmount()
    },[])

    const unmount = () =>
    {

    }

    const setFormData = (formData) =>
    {
        onFormDataChanged(formData)
    }

    if(!template)
        return <></>


    const form = <div style={{display: "flex", flex: 1, flexDirection: "column"}}>
        {template?.map( (ele,i) => {
            return <div
                key={ele.name + "_"+i}>{KompostFabrik._parseComponentFromFormbuilderConfig(insertValueToFormdataObj({...ele, readOnly : readOnly? readOnly : ele.readOnly}, formData),
                (val) => setFormData(changeFormData(ele.name, val, formData)),
                !!unanswered.find(u => u.name === ele.name))}</div>
        })}</div>

    return <div>{form}</div>
}

// Specifies the default values for props:
AutoFormBuilderUnmanaged.defaultProps = {
    cols: 1,
    template : null,
    formData : [],
    unanswered : [],
    readOnly : false,
};

AutoFormBuilderUnmanaged.propTypes =
    {
        cols: PropTypes.number,
        template : PropTypes.array.isRequired,
        formData : PropTypes.array.isRequired,
        unanswered : PropTypes.array.isRequired,
        onFormDataChanged : PropTypes.func.isRequired,
        readOnly : PropTypes.bool,
    }


export default AutoFormBuilderUnmanaged
