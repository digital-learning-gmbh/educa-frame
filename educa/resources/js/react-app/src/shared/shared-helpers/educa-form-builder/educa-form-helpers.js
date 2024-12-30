import KompostFabrik from "../KompostFabrik";
import SharedHelper from "../SharedHelper";
import React from "react";

/**
 * @param formTemplate
 * @param formData
 * @returns {*[]} required but not answered
 */
export const validateForm = (formTemplate, formData) =>
{
    if(formTemplate) {
        let requiredButNotAnswered = KompostFabrik.validateForm(
            formTemplate, // form template
            formData)
        if (requiredButNotAnswered.length > 0)
            SharedHelper.fireWarningToast("Achtung",
                <div style={{flexDirection: "column", display: "flex"}}>
                    <div>
                        Bitte befüllen Sie folgende Felder:
                    </div>
                    {requiredButNotAnswered.map((obj, key) => {

                        return <div key={key}>{obj.label}</div>
                    })}
                </div>, 7000)
        return requiredButNotAnswered
    }
}

/**
 * Searches the corresponding object for key and sets the value
 * @param key
 * @param value
 * @param formData
 * @returns {*[]} new Formdata
 */
export const changeFormData = (key,value, formData) =>
{
    let data = []
    if (Array.isArray(formData))
    {
        data = _.cloneDeep(formData)
        let index = data?.findIndex( kv => kv.name === key)
        if(index >= 0)
            data[index] = {name : key, value : value}
        else
            data.push({name : key, value : value} )
    }
    else
        data.push({name : key, value : value} )
    return data
}

/**
 * Inserts the value from the form data into the template object to be used by KompostFabrik
 * @param templateObj
 * @param formData
 * @returns {(*&{value: null})|(*&{value: (*|null)})}
 */
export const insertValueToFormdataObj = (templateObj, formData) => {
    if (!templateObj || !formData)
        return {...templateObj, value: null}
    let foundKeyValue = formData?.find( kv => kv.name === templateObj.name)
    return {...templateObj, value: foundKeyValue? foundKeyValue.value : null }
}
