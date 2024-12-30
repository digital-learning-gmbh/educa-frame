import React from "react";
import {useEffect, useState} from "react";
import LocalizedStrings from "react-localization";
import {useSelector} from "react-redux";
import EducaAjaxHelper from "./EducaAjaxHelper";
import fallbackLocalization from "../../../../../lang/locale.json";

let rawLocalization = null
let isLoading = false
let object = null
let fallbackObject = null

/**
 * usage:
 *              const [translate, strings] = useLocalizedStrings()
 *              ...
 *              <div>{translate("myKey", "myDefaultValue)}</div>
 *              <div>{translate("myKey")}</div>
 *              <div>{strings.myKey}</div>
 *              <div>{string.formatString("This is my Key: {0}", strings.myKey)}</div>
 *
 * */
export function useEducaLocalizedStrings() {

    const defaultLanguageKey = useSelector(s => s?.currentCloudUser?.language)
    const [counter, setCounter] = useState(0)

    useEffect(() => {

        if(!fallbackObject)
            initFallback()

        if (!rawLocalization && !isLoading)
            loadTranslations()
        return () => object = null
    }, [])

    useEffect(() => {
        if(defaultLanguageKey && fallbackObject && fallbackObject.getLanguage() !== defaultLanguageKey)
            initFallback()

        if(defaultLanguageKey && object && object.getLanguage() !== defaultLanguageKey)
            init()

    },[defaultLanguageKey])

    useEffect(() => {
        if(rawLocalization && !object)
            init()
        else
            forceRender()
    },[rawLocalization])

    const forceRender = () =>
    {
        setCounter(counter+1)
    }
    const initFallback = () =>
    {
        let obj = new LocalizedStrings(fallbackLocalization)
        obj.setLanguage(defaultLanguageKey??"de")
        fallbackObject = obj
        forceRender()
    }
    const init = () => {
        let obj = new LocalizedStrings(rawLocalization??{en : {}})
        obj.setLanguage(defaultLanguageKey??"de")
        object = obj
        forceRender()
    }

    const loadTranslations = () => {
        isLoading = true
        EducaAjaxHelper.getLocales()
            .then(resp => {
                if(resp.payload.translation)
                    rawLocalization = resp.payload.translation
            })
            .finally(() => isLoading = false)
    }


    const targetObject = object??fallbackObject
    const resolveKey = (key, defaultValue = "") => targetObject && key && targetObject[key]? targetObject[key] : defaultValue
    const formatStringFixed = (...params) => {
        let p = params?.map( p => p??"")
        if(targetObject)
            return targetObject.formatString(...p)
        return ""
    }

    return [resolveKey, targetObject?{...targetObject, formatString : formatStringFixed} : {formatString : formatStringFixed}]
}


export const withEducaLocalizedStrings = (Component) => (props) =>
{
    const [translate, strings] = useEducaLocalizedStrings()
    return <Component {...props} translate={translate} strings={strings}/>
}
