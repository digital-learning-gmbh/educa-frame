import React, {useEffect, useState} from 'react';
import {Button, Collapse, FormControl} from "react-bootstrap";
import Select from "react-select";
import {EducaFormControl, EducaInputConfirm, getDisplayPair, NumberInput} from "./Inputs";
import SharedHelper from "../shared-helpers/SharedHelper";
import _ from "lodash";
import {SelectPlaceholder} from "../../administration-react/administration-components/Selects";
import DatePickerBox from "./DatePickerBox";
import moment from "moment";
import ErrorsRamazotti from "./ErrorsRamazotti";
import {EducaCircularButton} from "./Buttons";
import "./table-filter.css"

export const EDUCA_FILTER_TYPES =
    {
        SELECT: "select",
        TEXT: "text",
        MIN_MAX: "min_max",
        DATE : "date"
    }

export const EDUCA_FILTER_TEXT_MATCHING =
    {
        INCLUDES : "includes",
        STARTS_WITH : "starts_with"
    }


export const EDUCA_FILTER_FUNCTION =
    {
        AND : (a,b) => a&&b,
        OR : (a,b) => a||b
    }


function EducaTableFilter(props) {


    let [config, setConfig] = useState([])
    let [rows, setRows] = useState([])
    let [filterMapping, setFilterMapping] = useState({})
    let [lastFilteredRows, setLastFilteredRows] = useState(null)

    let [favoritesEditMode, setFavoritesEditMode] = useState(false)
    let [newFilterEditMode, setNewFilterEditMode] = useState(false)
    let [newFilterFavorite, setNewFilterFavorite] = useState(null)

    const FilterComponentFactory = new FilterComponentFactoryClass(true);

    const filterFunction = props.options?.filterFunction? props.options?.filterFunction : EDUCA_FILTER_FUNCTION.AND
    const filterDefaultFlag = props.options?.filterFunction == EDUCA_FILTER_FUNCTION.OR? false : true

    useEffect(() => {


    }, [])

    useEffect(() => {
        setFilterMapping({})
        setConfig(_prepareConfig(props.config))
    }, [props.config])

    useEffect(() => {
        setFilterMapping({})
        setRows(_prepareRows(props.rows))
    }, [props.rows])

    useEffect(() =>
    {
        setNewFilterFavorite(null)
    },[setNewFilterEditMode])



    useEffect( () =>
    {
        if(props.favoriteFilters)
            setFavoritesEditMode(false)
    },[props.favoriteFilters])

    const _prepareConfig = (rawConfig) => {
        return rawConfig ? rawConfig.map(c => ({...c, id: c?.id? c?.id : SharedHelper.createUUID() })) : []
    }
    const _prepareRows = (rawRows) => {
        return rawRows ? rawRows : []
    }

    /**
     */
    function _filterReduce(array, compareFunc) {
        const getNodes = (result, object) => {
            if ( compareFunc(object) ) {
                result.push(object);
                return result;
            }
            if (Array.isArray(object._children)) {
                const nodes = object._children.reduce(getNodes, []);
                if (nodes.length) result.push({ ...object, _children : nodes });
            }
            return result;
        };

        return array.reduce(getNodes, []);
    }


    let _filterAlternative = (customFilterMapping) =>
    {
        let mapping = customFilterMapping? customFilterMapping : filterMapping
        if(Object.keys(mapping).length == 0)
            return true // No filter set; let all pass

        let nuRows = _.cloneDeep(rows)

        let keys = Object.keys( mapping )
        for( let i = 0; i < keys.length; i++)
        {
            let key = keys[i]
            let correspondingConfigObj = config?.find( conf => conf.id == keys[i])
            if(!correspondingConfigObj)
                continue

            let compareFunc = (o) => {}
            if( correspondingConfigObj.type === EDUCA_FILTER_TYPES.MIN_MAX && mapping)
            {
                compareFunc = (row) =>  _filterMinMax(correspondingConfigObj, mapping, row)
            }
            else if( correspondingConfigObj.type === EDUCA_FILTER_TYPES.DATE && mapping)
            {
                compareFunc = (row) =>  _filterString(correspondingConfigObj, mapping, key, row, true)
            }
            else
            {
                compareFunc = (row) =>  _filterString(correspondingConfigObj, mapping, key, row, false)
            }
            nuRows=_filterReduce(nuRows, compareFunc)
        }

        props.onFilter(nuRows)
        setLastFilteredRows(nuRows)

    }

    let _filter = (customFilterMapping) => {
      //  let rowsCopy = _.cloneDeep(rows)
        let mapping = customFilterMapping? customFilterMapping : filterMapping
        if(Object.keys(mapping).length == 0)
            return true // No filter set; let all pass

        let nuRows = _.cloneDeep(rows)
       nuRows = nuRows.filter( row => {
            let keys = Object.keys( mapping )
            // process filter mapping
            let filterFlag = filterDefaultFlag // filter default

            for( let i = 0; i < keys.length; i++)
            {
                let key = keys[i]
                let correspondingConfigObj = config?.find( conf => conf.id == keys[i])
                if(!correspondingConfigObj)
                    continue

                if( correspondingConfigObj.type === EDUCA_FILTER_TYPES.MIN_MAX && mapping)
                {
                    filterFlag = filterFunction(filterFlag, _filterMinMax(correspondingConfigObj, mapping, key, row))
                }
                else if( correspondingConfigObj.type === EDUCA_FILTER_TYPES.DATE && mapping)
                {
                    filterFlag = filterFunction(filterFlag, _filterString(correspondingConfigObj, mapping, key, row, true))
                }
                else
                {
                    filterFlag = filterFunction(filterFlag, _filterString(correspondingConfigObj, mapping, key, row, false))
                }

            }

            return filterFlag
        })

        setLastFilteredRows(nuRows)
        props.onFilter(nuRows)
    }

    const _filterString = (correspondingConfigObj, mapping, key, row, isDate) =>
    {
        let rowString
        let filterValue = mapping[key]

        let checkString = (rowStr) =>
        {
            if(!rowStr || !filterValue)
                return false // filter default
            rowStr = rowStr.toLocaleLowerCase()
            filterValue = filterValue.toLocaleLowerCase()

            if( !props.options?.textMatching || props.options?.textMatching === EDUCA_FILTER_TEXT_MATCHING.INCLUDES)
                return !!rowStr.includes(filterValue)
            else if( props.options?.textMatching === EDUCA_FILTER_TEXT_MATCHING.STARTS_WITH)
                return !!rowStr.startsWith(filterValue)
        }

        let checkDate = (rowStr) =>
        {
            if(!rowStr || !filterValue /*|| typeof filterValue !== "object"*/)
                return false // filter default
            let rowDate = moment(rowStr,correspondingConfigObj.dateMatcher? correspondingConfigObj.dateMatcher : "DD.MM.YYYY" )
            if( !rowDate.isValid() )
                return false
            console.log(filterValue)
            let filterDate = Number.isInteger(filterValue)? moment.unix(filterValue) : filterValue
           return  rowDate.day() == filterDate.day()
               &&  rowDate.month() == filterDate.month()
               &&  rowDate.year() == filterDate.year()
        }

        let check = isDate? checkDate : checkString

        if(!correspondingConfigObj.childrenAccessor)
        {
            rowString = row[correspondingConfigObj.accessor]+""
            return check(rowString)
        }
        else // dig into structure
        {
            let childrenAccessor = correspondingConfigObj.childrenAccessor
            let dig = (recRow, value) =>
            {
                if(value == true)
                    return value // break recursion

                if( Array.isArray(recRow) ) //continue for each entry
                {
                    return recRow.reduce( (prev, current) =>
                    {
                        return prev || dig(current, value)
                    }, value)
                }
                else{
                    if(recRow.hasOwnProperty(correspondingConfigObj.accessor) && check(recRow[correspondingConfigObj.accessor]))
                        return true
                    if(recRow.hasOwnProperty(childrenAccessor))
                        return dig(recRow[childrenAccessor])
                    else
                        return check(recRow[correspondingConfigObj.accessor])
                }
            }
            return dig(row, false /*filter default*/ )

        }


    }

    const _filterMinMax = (correspondingConfigObj, mapping, key, row) =>
    {
        if((typeof mapping[key]).toLowerCase() !== "object")
            return false // filter default

        let min = mapping[key].min >= 0? mapping[key].min : undefined
        let max = mapping[key].max >= 0? mapping[key].max : undefined

        let check = (value) =>
        {
            if( Number.isNaN(value) )
                return false // filter default
            if(min >= 0 && max >= 0)
                return value >= min && value <= max
            if(min >= 0)
                return value >= min
            if(max >= 0)
                return value <= max
            return false
        }

        if(!correspondingConfigObj.childrenAccessor)
        {
            return check(row[correspondingConfigObj.accessor])
        }
        else // dig into structure
        {
            let childrenAccessor = correspondingConfigObj.childrenAccessor
            let dig = (recRow, value) =>
            {
                if(value == true)
                    return value // break recursion

                if( Array.isArray(recRow) ) //continue for each entry
                {
                    return recRow.reduce( (prev, current) =>
                    {
                        return prev || dig(current, value)
                    }, value)
                }
                else{
                    if(recRow.hasOwnProperty(correspondingConfigObj.accessor) && check(recRow[correspondingConfigObj.accessor]))
                        return true
                    if(recRow.hasOwnProperty(childrenAccessor))
                        return dig(recRow[childrenAccessor])
                    else
                        return check(recRow[correspondingConfigObj.accessor])
                }
            }
            return dig(row, false /*filter default*/ )

        }

    }

    let _reset = () => {
        setFilterMapping({})
        setLastFilteredRows(null)
        props.onFilter(props.rows)
    }

    let _buildFilter = () => {

        if (rows?.length > 0)
            return config.map(confObj => {

                return <div key={confObj.id}>

                    {FilterComponentFactory.build(
                        rows,
                        {...confObj, value: filterMapping[confObj.id]},
                        (val) => {
                            let map = _.cloneDeep(filterMapping);
                            if( (val == undefined || val == null || val == "") && map.hasOwnProperty(confObj.id))
                                delete map[confObj.id]
                            else if(val == undefined || val == null || val == "")
                                return
                            else
                                map[confObj.id] = val
                            setFilterMapping(map)
                        }
                    )}
                </div>
            })

        else
            return <i>Keine Daten verfügbar</i>
    }

    const removeFavorite = (obj) =>
    {
        let favs = _.cloneDeep(props.favoriteFilters)
        favs = favs.filter( e => e.id != obj.id)
        props.onFavoritesChange(favs)
    }

    let filterMappingLength = Object.keys(filterMapping).length
  // console.log(filterMapping)

    try{
    return (
        <div style={{display: "flex", flexDirection: "column"}}>
            <div style={{display: "flex", flexDirection: "column", width: "550px"}}>
                {_buildFilter()}
            </div>
            <div style={{display: "flex", flexDirection: "row"}}>
                <Button disabled={filterMappingLength == 0} className={"mr-2"} onClick={() => _filter()}>{"Filter anwenden " + (filterMappingLength == 0? "" : "("+filterMappingLength+")")}</Button>
                <Button className={"mr-2"}  onClick={() => _reset()}>Filter zurücksetzen</Button>
                <Button disabled={filterMappingLength == 0} variant={newFilterEditMode? "danger": "success"}
                        onClick={() => {setNewFilterEditMode(!newFilterEditMode); setFavoritesEditMode(false); setNewFilterFavorite(null) }}>
                    {newFilterEditMode? "Abbrechen": <><i className="fas fa-star"/> Speichern</>}</Button>
            </div>
            <div>
                {!Array.isArray(lastFilteredRows) ? null : lastFilteredRows?.length == 0 ? <i>Keine Treffer</i> :
                    <i>{lastFilteredRows?.length} Treffer</i>}
            </div>
                <div style={{display : "flex"}} className={"mt-2"}>
                    <label>
                        <b><i className="fas fa-star"/> Meine Favoriten</b>
                    </label>
                    {props.favoriteFilters?.length > 0? <EducaCircularButton size={"small"}
                                         className={"ml-2"}
                                         variant={favoritesEditMode? "danger" : "primary"}
                                         onClick={() => {setFavoritesEditMode(!favoritesEditMode); setNewFilterEditMode(false)}}>{favoritesEditMode? <i className={"fas fa-times"}/> : <i className={"fas fa-pencil-alt"}/> }
                    </EducaCircularButton> : null}
                </div>
            <Collapse in={newFilterEditMode} unmountOnExit={true}>
                <div>
                    {getDisplayPair("Name", <div style={{width : "250px"}}>
                        <EducaInputConfirm
                                      value={newFilterFavorite?.label? newFilterFavorite?.label: ""}
                                       onChange={ (evt)=> {setNewFilterFavorite({id : SharedHelper.createUUID(), label : evt.target.value, mapping : filterMapping})}}
                                       onConfirmClick={ () => {props.onFavoritesChange(props.favoriteFilters?props.favoriteFilters.concat([newFilterFavorite]) : [newFilterFavorite] ); setNewFilterEditMode(false) }}
                                       maxLetters={25}/></div>)}
                </div>
            </Collapse>
                {props.favoriteFilters?.length > 0 ? <>
                <div>
                    {favoritesEditMode?
                        <div style={{color: "#597EAA"}}><i className={"fa fa-info-circle"}/>
                            Clicke auf einen Favoriten um ihn zu entfernen.
                    </div> : null}
                    <div style={{display : "flex"}}  className={favoritesEditMode? "db-shaking" : ""}>
                        {props.favoriteFilters.map( (m, i) =>
                                <Button key={i}
                                        variant={favoritesEditMode? "outline-danger m-1" : "outline-dark m-1"}
                                        onClick={() => {
                                            if(favoritesEditMode)
                                                return removeFavorite(m)
                                            setFilterMapping(m.mapping); _filter(m.mapping)}}>{m.label}
                                </Button>
                           )}
                    </div>

                </div>
            </> : <i>Noch keine Favoriten. Erstelle einen Filter um ihn als Favorit zu speichern.</i>}
        </div>
    );
    }
    catch
    {
        return <ErrorsRamazotti/>
    }
}


class FilterComponentFactoryClass {
    constructor(debug) {
        this.debug = !!debug
    }

    build(rows, configObject, onChange) {
        const type = configObject?.type
        if (!this._validate(rows, configObject, type, onChange))
            return <></>

        if (type === EDUCA_FILTER_TYPES.SELECT)
            return this._createSelect(rows, configObject, onChange)
        if (type === EDUCA_FILTER_TYPES.TEXT)
            return this._createText(rows, configObject, onChange)
        if (type === EDUCA_FILTER_TYPES.MIN_MAX)
            return this._createMinMax(rows, configObject, onChange)
        if (type === EDUCA_FILTER_TYPES.DATE)
            return this._createDate(rows, configObject, onChange)
    }

    // SELECT

    _createSelect(rows, configObject, onChange) {
        const selectOptions = this._getSelectOptions(rows, configObject)
        return getDisplayPair(configObject.Header,
            <Select
            components={{ SelectPlaceholder }}
            noOptionsMessage={() => "Keine Optionen"}
            placeholder={configObject.Header}
            isClearable={true}
            value={configObject.value ? selectOptions?.find(o => o.value == configObject.value) : {}}
            options={selectOptions}
            onChange={(val) => onChange(val?.value)}
            />)
    }

    _getSelectOptions(rows, configObject) {
        const accessor = configObject.accessor
        let aggregation = []

        // accessor is in root object
        if (!configObject.childrenAccessor) {
            rows?.forEach(row => {
                if (row[accessor]) {
                    if (!aggregation.find(o => o == row[accessor]))
                        aggregation.push(row[accessor])
                }
            })
        } else // accessor is in some child
        {
            const childrenAccessor = configObject.childrenAccessor

            let dig = (recRow) =>
            {
                if( Array.isArray(recRow) ) //continue for each entry
                {
                    return recRow.map( (ro) =>
                    {
                        if(ro.hasOwnProperty(accessor))
                            return ro[accessor]
                        return dig(ro)
                    })
                }
                else{
                    if(recRow.hasOwnProperty(childrenAccessor))
                        return dig(recRow[childrenAccessor])
                    else
                        return recRow[accessor]
                }
            }
            let tempAggregation = dig(rows)?.flatMap( rows => rows ).filter(e => !!e)
            tempAggregation.forEach( e =>
            {
                let obj = aggregation.find( aggEle => aggEle == e)
                if(!obj)
                    aggregation.push(e)
            })
        }

        return aggregation.map(a => ({value: a, label: a}))
    }

    // TEXT
    _createText(rows, configObject, onChange) {
        return getDisplayPair(configObject.Header,
            <EducaFormControl
            isClearable={true}
            value={configObject.value?configObject.value : "" }
            onChange={(evt) => onChange(evt.target.value)}
        />)
    }

    //MIN MAX

    _createMinMax(rows, configObject, onChange) {
        return getDisplayPair(configObject.Header,
            <div style={{display : "flex"}}>
                <div style={{display :"flex", flexDirection : "column", flex : 1}} className={"mr-2"}>
                    <div style={{marginBottom :"-2px", fontSize :"12px", display: "flex", flexDirection :"row"}}>Min</div>
                    <div>
                        <NumberInput
                            isClearable={true}
                            value={configObject.value?.min ? configObject.value?.min  : null}
                            onChangeNumber={(num) =>
                            {
                                if(configObject.value?.max >= 0)
                                    return onChange({... configObject.value, min : num})
                                if( num != "" &&  num >= 0)
                                    return onChange({min : num})
                                return onChange(undefined)
                            }}
                        />
                    </div>
                </div>
                <div style={{display :"flex", flexDirection : "column", flex : 1}}>
                    <div style={{marginBottom :"-2px", fontSize :"12px", display: "flex", flexDirection :"row"}}>Max</div>
                    <div>
                        <NumberInput
                            isClearable={true}
                            value={configObject.value?.max ? configObject.value?.max  : null}
                            onChangeNumber={(num) =>
                            {
                                if(configObject.value?.min >= 0)
                                    return onChange({... configObject.value, max : num})
                                if( num != "" &&  num >= 0 )
                                    return onChange({max : num})
                                return onChange(undefined)
                            }}
                        />
                    </div>
                </div>
            </div>
        )
    }

    //Date
    _createDate(rows, configObject, onChange)
    {

        return getDisplayPair(configObject.Header,
            <DatePickerBox
                inputWidth={"535px"}
                deleteButton={true}
                date={configObject.value?configObject.value : null }
                onDateChange={(d) => onChange(d?.unix())}
            />)
    }


    /**** validation ****/

    _validate(rows, configObject, type, onChange) {
        let successFlag = true
        let err = (txt) => {
            successFlag = false;
            if (this.debug) console.error("FILTER DEBUG: " + txt);
            return successFlag
        }

        if (typeof onChange !== "function")
            return err("onChange not defined")

        if (!(rows?.length > 0))
            return err("no rows given to create component")

        return successFlag
    }
}


export default React.memo( (props) => {

    return <EducaTableFilter {...props}/>

}, (prev,next) =>
{
    return _.isEqual(prev.rows, next.rows)
        && _.isEqual(prev.config, next.config)
        && _.isEqual(prev.favoriteFilters, next.favoriteFilters)

})
