import PropTypes from 'prop-types';
import React, {useEffect, useState} from "react";
import Button from "react-bootstrap/Button";
import {validateForm} from "./educa-form-helpers";
import AutoFormBuilderUnmanaged from "./AutoFormBuilderUnmanaged";


const DefaultOkButton = <Button variant={"primary"}>Speichern</Button>


/**
 * Managed component that keeps form data as state
 *
 * @param cols
 * @param template
 * @param formData
 * @param onBlur
 * @returns {JSX.Element}
 * @constructor
 */
const AutoFormBuilderManaged =  ({   cols,
                                 template,
                                 formData,
                                 saveButton,
                                 onSaveClick ,
                                 readOnly
                                }) => {

    let [data, setData] = useState([])
    let [unanswered, setUnanswered] = useState([])

    useEffect( () =>
    {
        return () => unmount()
    },[])

    useEffect(() =>
    {
        if(!_.isEqual(formData, data))
        setData(formData?formData : [])
    }, [formData])

    const saveClick = () => {

    let arr =  validateForm(template, data)
    setUnanswered(arr)
    if(arr?.length > 0)
        return
    onSaveClick(data)
    }

    const unmount = () =>
    {
        setUnanswered([])
        setData([])
    }


    return <div style={{display :"flex", flexDirection : "column"}}>
        <AutoFormBuilderUnmanaged
            readOnly={readOnly}
            template={template}
            formData={data}
            unanswered={unanswered}
            onFormDataChanged={(d) => setData(d)}/>

        <div className={"mt-2"} style={{display :"flex"}}>
            {
                readOnly? null : <Button
                    {...saveButton.props}

                className={"mr-1 ml-2"}
                onClick={(evt) =>  saveClick()}
            />}
        </div>
    </div>
}

// Specifies the default values for props:
AutoFormBuilderManaged.defaultProps = {
    cols: 1,
    template : null,
    formData : [],
    saveButton : DefaultOkButton,
};

AutoFormBuilderManaged.propTypes =
    {
        cols: PropTypes.number,
        template : PropTypes.array.isRequired,
        formData : PropTypes.array,
        saveButton : PropTypes.element,
        onSaveClick : PropTypes.func.isRequired,
    }


export default AutoFormBuilderManaged
