import React, {useEffect, useState} from "react";
import PropTypes from "prop-types";
import AutoFormBuilderAjax from "../../shared/shared-helpers/educa-form-builder/AutoFormBuilderAjax";
import EducaAjaxHelper from "../helpers/EducaAjaxHelper";

const EducaAutoFormBuilderAjax = ({formId, modelType, modelId, readOnly}) =>
{

    let [ready ,setReady] = useState(false)
    useEffect(() => {
        init()
    },[formId,modelType, modelId ])

    useEffect(() =>
    {
        init()
    },[])

    const init = () => {
        if(formId > 0 && modelType != "" && modelId > 0)
            setReady(true)
        else
            setReady(false)
    }
    return ready? <AutoFormBuilderAjax
        readOnly={readOnly}
        formId={formId}
        modelType={modelType}
        modelId={modelId}
        ajaxGetter={(formId, modelId, modelType) => EducaAjaxHelper.getForm(formId, modelId, modelType)}
        ajaxSaver={(formId, revisionId, modelId, modelType, form_data) => EducaAjaxHelper.saveFormForRevision(formId, revisionId, modelId, modelType, form_data)}/>
        :
        <div>Formular konnte nicht geladen werden. Bitte kontaktiere ein*e Lehrer*in.</div>

}

EducaAutoFormBuilderAjax.propTypes =
    {
        formId: PropTypes.number.isRequired,
        modelType : PropTypes.string.isRequired,
        modelId : PropTypes.number.isRequired,
        onSaveSuccessHandler : PropTypes.func,
        onSaveFailHandler : PropTypes.func,
        readOnly : PropTypes.bool
    }


export default EducaAutoFormBuilderAjax
