import React, {useEffect, useState} from "react";
import PropTypes from "prop-types";
import AutoFormBuilderManaged from "./AutoFormBuilderManaged";
import SharedHelper from "../SharedHelper";

const AutoFormBuilderAjax = ({
                                 formId,
                                 modelType,
                                 modelId,
                                 ajaxGetter,
                                 ajaxSaver,
                                 onSaveSuccessHandler,
                                 onSaveFailHandler ,
                                 onLoadFailHandler,

                                 readOnly
                            }) =>
{
    let [data, setData] = useState(null)
    let [template, setTemplate] = useState(null)
    let [revision, setRevision] = useState(null)

    useEffect( () =>
    {
        return () => unmount()
    },[])

    useEffect(() =>
    {
        if(!ajaxGetter)
            return
        loadForm()
    }, [formId, modelType, modelId])


    const unmount = () =>
    {

    }

    const loadForm = () =>
    {
        ajaxGetter(formId, modelId, modelType)
            .then( resp =>
            {
                if(resp.status > 0 && resp.payload?.revision)
                {
                    setRevision(resp.payload?.revision)
                    if(resp.payload.revision.data)
                        setTemplate(JSON.parse(resp.payload.revision.data))
                    if(resp.payload.data)
                        setData(resp.payload.data)
                    return
                }
                throw new Error(resp.message)
            })
            .catch( err =>
            {
                onLoadFailHandler(err)
            })
    }

    const save = (d) =>
    {
        if(readOnly)
            return
        setData(d)
        if(!revision)
            return onSaveFailHandler(" Revision ist nicht definiert.")
        ajaxSaver(formId, revision.id, modelId, modelType, d)
            .then( resp =>
            {
                if(resp.status > 0 )
                    return onSaveSuccessHandler()
                throw new Error(resp.message)
            })
            .catch( err =>
            {
                onSaveFailHandler(err.message)
            })
    }

    return template?.length > 0? <AutoFormBuilderManaged
            readOnly={readOnly}
            template={template}
            formData={data}
            onSaveClick={ (data) => save(data) }/> : null
}

// Specifies the default values for props:
AutoFormBuilderAjax.defaultProps = {
    onSaveSuccessHandler : () => { SharedHelper.fireSuccessToast("Erfolg", "Das Formular wurde erfoglreich gespeichert.")},
    onSaveFailHandler :  (err) => {SharedHelper.fireErrorToast("Fehler", "Das Speichern des Formulars ist fehlgeschlagen. "+err.message)},
    onLoadFailHandler :  (err) => {SharedHelper.fireErrorToast("Fehler", "Das Formular konnte nicht geladen werden. "+err.message)},
    ajaxGetter : null,
    ajaxSaver : null
};

AutoFormBuilderAjax.propTypes =
    {
        formId: PropTypes.number.isRequired,
        modelType : PropTypes.string.isRequired,
        modelId : PropTypes.number.isRequired,
        ajaxGetter : PropTypes.func.isRequired,
        ajaxSaver : PropTypes.func.isRequired,
        onSaveSuccessHandler : PropTypes.func,
        onSaveFailHandler : PropTypes.func,
    }


export default AutoFormBuilderAjax
