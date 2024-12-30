import React from "react";
import {FormControl, InputGroup} from "react-bootstrap";
import TextareaAutosize from "react-textarea-autosize";
import Select from "react-select";
import DatePickerBox from "../shared-components/DatePickerBox";
import Form from "react-bootstrap/Form";
import SharedHelper from "./SharedHelper";
import ReactSwitch from "react-switch";
import {getDisplayPair, NumberInput} from "../shared-components/Inputs";
import {CountrySelect, SelectPlaceholder} from "../shared-components/Selects";

const _required = <div style={{color : "red"}} className={"ml-1"}>*</div>
export const WIDGET_TYPES =
    {
        DATE : "date",
        TEXT : "text",
        NUMBER : "number",
        SELECT : "select",
        SELECT_MULTI : "select-multi",
        TEXTAREA : "textarea",
        CHECKBOX_GROUP : "checkbox-group",
        RADIO_GROUP : "radio-group",
        FILE : "file",
        HEADER : "header",
        PARAGRAPH : "paragraph",
        VIDEO : "video",
        PICTURE : "picture",
        STAR_RATING : "star-rating",
        SIGNATURE : "signature",
        SWITCH : "switch",

        SUPPORT_TABLE_COUNTRIES_WORLD: "sp_countries_world"
    }

class KompostFabrikKlasse
{

    parseComponent(object, onChange, hasError) {
        if (object.type === WIDGET_TYPES.DATE) {
            return this._parseDateComponent(object, onChange, hasError)
        } else if (!object.type || object.type === WIDGET_TYPES.TEXT) {
            return this._parseTextComponent(object, onChange, false, hasError)
        } else if (object.type === WIDGET_TYPES.SELECT_MULTI || (!!object.multiple && object.type === WIDGET_TYPES.SELECT)) {
            return this._parseSelectComponent(object, onChange, hasError,true)
        } else if (object.type === WIDGET_TYPES.SELECT) {
            return this._parseSelectComponent(object, onChange, hasError)
        } else if (object.type === WIDGET_TYPES.NUMBER) {
            return this._parseNumberComponent(object, onChange, hasError)
        } else if (object.type === WIDGET_TYPES.TEXTAREA) {
            return this._parseTextArea(object, onChange, hasError)
        } else if (object.type === WIDGET_TYPES.CHECKBOX_GROUP) {
            return this._parseCheckOrRadio(object, onChange, true, hasError)
        } else if (object.type === WIDGET_TYPES.RADIO_GROUP) {
            return this._parseCheckOrRadio(object, onChange, false, hasError)
        } else if (object.type === WIDGET_TYPES.FILE) {
            return this._parseFile(object, onChange, hasError)
        } else if (object.type === WIDGET_TYPES.HEADER) {
            return this._parseHeaderOrParagraph(object, false, hasError)
        } else if (object.type === WIDGET_TYPES.PARAGRAPH) {
            return this._parseHeaderOrParagraph(object, true, hasError)
        } else if (object.type === WIDGET_TYPES.SUPPORT_TABLE_COUNTRIES_WORLD) {
            return this._parseSelectCountriesComponent(object, onChange, hasError)
        } else if (object.type === WIDGET_TYPES.VIDEO) {
            return  this._parseVideo(object, hasError)
        } else if (object.type === WIDGET_TYPES.PICTURE) {
            return  this._parsePicture(object, hasError)
        } else if (object.type === WIDGET_TYPES.STAR_RATING) {
            return  this._parseStarRating(object, hasError)
        } else if (object.type === WIDGET_TYPES.SIGNATURE) {
            return  this._parseSignature(object, hasError)
        } else if (object.type === WIDGET_TYPES.SWITCH) {
            return this._parseSwitch(object, onChange, hasError)
        }


    }

    _parseComponentFromFormbuilderConfig( object, onChange, hasError )
    {
        object.options = object.values
        return this.parseComponent( object, onChange, hasError )
    }

    _parseSelectComponent( object, onChange, hasError, isMulti )
    {
        const options = object.options? object.options : []
        let value
        if(isMulti)
        {
            value = Array.isArray(object.value)? object.value.map( val => options?.find( op => op.value === val) ).filter( e => !!e) : []
            if(!Array.isArray(object.value))
            {
                try {
                    value = [options?.find( op => op.value === object.value)];
                } catch (e) {
                    //
                }
            }
        }
        else
        {
            value = options?.find( op => op.value === object.value)
            value = value? value : null
        }
        let select = <Select
            isClearable={object.isClearable}
            closeMenuOnSelect={!isMulti}
            isMulti={isMulti}
            components={{ SelectPlaceholder }}
            styles={
                {   menuPortal: base => ({ ...base, zIndex: 9999 }),
                    menu: base => ({ ...base, zIndex: 9999 }) }}
            placeholder={object.placeholder? object.placeholder : "Auswahl..."}
            isDisabled={object.readOnly}
            noOptionsMessage={() => object.noOptionsText?object.noOptionsText : "Keine Optionen" }
            options={options}
            value={value}
            onChange={ (newValue) => {
                if( isMulti)
                  onChange(Array.isArray(newValue)? newValue.map( e => e.value) : null)
                else
                  onChange(newValue? newValue.value : null)
            }}
        />
        return this._getDisplayPair(object.label,select, object.required, hasError  )
    }


    _parseSelectCountriesComponent( object, onChange, hasError )
    {
        let value = object.value?  object.value : null
        let select = <CountrySelect
            menuPortalTarget={document.body}
            styles={{ menuPortal: base => ({ ...base, zIndex: 9999 }) }}
            placeholder={object.placeholder? object.placeholder : "Auswahl..."}
            isDisabled={object.readOnly}
            isMulti={object.isMulti}
            value={value}
            onChange={(newValue) => onChange(newValue? newValue : null)}
        />
        return this._getDisplayPair(object.label,select, object.required, hasError  )
    }

    _parseNumberComponent( object, onChange, hasError )
    {
        return this._parseTextComponent( object, onChange, true, hasError )
    }

    _parseDateComponent( object, onChange, hasError )
    {
        let date = object.value
        if(date === null)
        {
            date = null
        }
        else if( date === "" )
        {
            onChange(null)
        }
        else if(typeof date === 'string')
        {
            date = moment(date)
            onChange(date.unix())
        }
        else if(Number.isInteger(date))
        {
            date = moment.unix(date)
        }

        /*
        if( date && !!object.hidden )
        {
            onChange(null)
        }
        */
        let dateBox = <DatePickerBox
            disabled={object.readOnly}
            showYearDropdown={true}
            showMonthDropdown={true}
            deleteButton={true}
            inputWidth={"308px"}
            onDateChange={(date) => onChange(date? date.unix() : null) }
            date={date != 0 && Number.isInteger(date)? moment.unix(date) : date? moment(date) : null}/>
        return this._getDisplayPair(object.label,dateBox, object.required, hasError, !!object.hidden )
    }

    _parseTextArea( object, onChange, hasError )
    {
        let content = <InputGroup>
            <TextareaAutosize
                className={"form-control"}
                placeholder={object.placeholder? object.placeholder : ""}
                readOnly={object.readOnly}
                style={{width : "100%"}}
                value={object.value? object.value : ""}
                minRows={object.rows? object.rows : 3}
                onChange={(evt) => onChange(evt.target.value)}>
            </TextareaAutosize>
        </InputGroup>
        return this._getDisplayPair(object.label,content, object.required, hasError )
    }

    _parseTextComponent( object, onChange, numberOnly, hasError )
    {
        let onChangeCallback = (evt) =>
        {
            let str = evt.target.value
            onChange(str)
        }

        let content = numberOnly?
            <NumberInput
                    isFloat={!!object.isFloat}
                    placeholder={object.placeholder? object.placeholder : ""}
                    disabled={object.readOnly}
                    value={object.value? object.value : ""}
                    onChangeNumber={(num) => onChange(num)}/>
            :

            <InputGroup>
            <FormControl
                placeholder={object.placeholder? object.placeholder : ""}
                disabled={object.readOnly}
                value={object.value? object.value : ""}
                onChange={(evt) => onChangeCallback(evt)}>

            </FormControl>
        </InputGroup>
        return this._getDisplayPair(object.label,content, object.required, hasError  )

    }

    _parseCheckOrRadio( object, onChange, isCheckbox, hasError )
    {
        const options = object.options? object.options : []
        let values = object.value? object.value : []

        let onChangeHandler = (key) =>
        {
            if(object.readOnly)
                return
            if(!isCheckbox)
                return onChange(key)
            let vals = values.concat([])
            let index = vals?.findIndex(v => v === key)
            if(index>=0)
                vals.splice(index,1)
            else
                vals.push(key)
            onChange(vals)
        }

        let cb = <Form.Group
            disabled={object.readOnly}
            controlId={object.name}
        >
            <Form.Label>{object.placeholder? object.placeholder : ""}</Form.Label>
            {options.map( (opt,key) => {
                return <Form.Check
                    disabled={object.readOnly}
                    key={key}
                    onChange={() => onChangeHandler(opt?.value)}
                    checked={isCheckbox?values?.includes(opt?.value) : object.value === opt.value }
                    type={isCheckbox?"checkbox": "radio"}
                    label={opt.label}
                    name={opt?.value}
                    id={object.name + opt?.value+""+key}
                />
            })
            }
        </Form.Group>

        return this._getDisplayPair(object.label,cb, object.required, hasError  )
    }

    _parseHeaderOrParagraph(object, isParagraph)
    {
        let subtype = object.subtype? object.subtype : isParagraph? "p" :"<h1>"
        return <div dangerouslySetInnerHTML={SharedHelper.sanitizeHtml("<"+subtype+">" + object.label + "</"+subtype+">" )}/>
    }

    _parseFile(object, onChange, hasError)
    {
        return <div>Entwicklung läuft...</div>
    }

    _parseVideo(object, hasError)
    {
        return <div>Entwicklung läuft...</div>
    }

    _parsePicture(object, hasError)
    {
        return <div>Entwicklung läuft...</div>
    }

    _parseStarRating(object, onChange, hasError)
    {
        return <div>Entwicklung läuft...</div>
    }

    _parseSignature(object, onChange, hasError)
    {
        return <div>Entwicklung läuft...</div>
    }


    _parseSwitch(object, onChange, hasError)
    {
        const on = object.labelOn? object.labelOn : "An"
        const off = object.labelOff? object.labelOff : "Aus"
        const label =  object.label

        let switchComp = <div style={{display :"flex", flexDirection : "row"}}>
            <div style={{display :"flex", flexDirection : "column", justifyContent : "center"}} className={"mr-1"}>{off}</div>
            <ReactSwitch checked={!!object.value} onChange={(flag) => onChange? onChange(flag) : null}/>
            <div style={{display :"flex", flexDirection : "column", justifyContent : "center"}} className={"ml-1"}>{on}</div>
        </div>

        return this._getDisplayPair(label, switchComp, object.required, hasError  )
    }

    // ...

    _getDisplayPair(title, content, isRequired, hasError, isHidden )
    {
        if(isHidden)
            return null
        return getDisplayPair(<>{title} {isRequired? _required : null}</>, content, hasError)
    }

    /**
     * Checks if answers to each given config object is given
     * @param objectArray
     * @param answersObject
     * @returns {[]}
     */
    validate(objectArray, answersObject)
    {
        let errorObjects = []

        if(!answersObject || !objectArray  )
            return [{label :"Ein unbekannter Fehler ist aufgetreten."}]
        objectArray?.forEach((configObj) => {
            if(!configObj.required)
                return
            if(!configObj.name)
                return errorObjects.push({...configObj, label : configObj.label + ". (Konfigurationsfehler)"})
            let ans = answersObject[configObj.name]
            if(!ans || ans === "")
                errorObjects.push(configObj)
        })

        return errorObjects
    }

    /**
     * The same as above, but for formbuilder forms
     * @param objectArray
     * @param answersArr
     * @returns {[]}
     */
    validateForm(objectArray, answersArr, validateAll = false)
    {
        let errorObjects = []

        if(!objectArray )
            return [{label :"Ein unbekannter Fehler ist aufgetreten."}]
        if(!answersArr)
            answersArr = []
        objectArray?.forEach((configObj) => {
            if(!configObj.required && !validateAll)
                return
            if(!configObj.name)
                return errorObjects.push({...configObj, label : configObj.label + ". (Konfigurationsfehler)"})
            let ans = answersArr.find( obj => obj.name == configObj.name )
            if(!ans || ans?.value === "")
                errorObjects.push(configObj)
        })

        return errorObjects
    }


    /**
     * Fires a warning for not-filled fields
     * @param requiredButNotAnsweredArr
     */
    fireWarnings( requiredButNotAnsweredArr )
    {
        if(requiredButNotAnsweredArr.length > 0)
        {
            SharedHelper.fireWarningToast("Achtung",
                <div style={{flexDirection :"column", display :"flex"}}><div>
                    Bitte befüllen Sie folgende Felder:
                </div>
                    {requiredButNotAnsweredArr.map( (obj, key) => {

                        return <div key={key}>{obj.label}</div>
                    })}
                </div>, 7000)
            return false
        }
        else
        {
            return true
        }
    }
}

const KompostFabrik = new KompostFabrikKlasse()

export default KompostFabrik


/**
 * Function that returns the default view for a form-template based config
 * @param config
 * @param valueObject
 * @param onChange
 * @param unansweredArr
 * @param baseWidth
 * @returns {JSX.Element}
 */
export const getFormContainer = (config, valueObject, onChange, unansweredArr = [], baseWidth = 650, injectionObject = {}) =>
{
    return <div className={"row"} style={{flexShrink: 1}}>{config?.map((menu, index1) => {
        let menuSize = menu.width ? parseInt(menu.width * baseWidth) : baseWidth
        const baseStyle = {width: (menuSize) + "px"}

        return <div key={"1_" + index1} style={baseStyle}>
            <h5>{menu.label}</h5>
            <div> {menu?.content?.map((contentObj, index2) => {
                if (!contentObj)
                    return SharedHelper.fireErrorToast("Fehler", "Ein Konfigurationsfehler ist aufgetreten.")

                return <div key={"2" + index2} style={{display: "flex", flexDirection: "row"}}>
                    {contentObj?.map((widgetObj, index3) => {
                        let isError = !!unansweredArr?.find(u => widgetObj.name === u.name)
                        return <div key={"3" + index3}
                                    style={{display: "flex", flexDirection: "column", flex: 1}}>
                            {KompostFabrik.parseComponent({
                                ...widgetObj,
                                value: valueObject[widgetObj.name],
                                ...injectionObject
                            }, (newValue) => onChange(widgetObj.name, newValue), isError)}
                        </div>
                    })}
                </div>
            })}
            </div>
        </div>


    })}</div>

}
