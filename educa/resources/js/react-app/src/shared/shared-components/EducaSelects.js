import React, {useState} from "react"
import Select from "react-select";
import {useSelector} from "react-redux";
import makeAnimated from "react-select/animated/dist/react-select.esm";
import SharedHelper, {DEFAULT_GROUP_COLOR} from "../shared-helpers/SharedHelper";
import {SelectPlaceholder} from "./Selects";
const animatedComponents = makeAnimated();

/**
 *
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */

let allCloudUsersWithoutCurrentUser = []
let lastCurrentCloudUser = null

/**
 * Recalculates the allCloudUsersWithoutCurrentUser Array if needed
 * @param allCloudUsers
 * @param currentCloudUser
 */
function reCalculateAllCloudUsersWithoutCurrentUser(allCloudUsers, currentCloudUser){

    if(currentCloudUser?.id !== lastCurrentCloudUser?.id || allCloudUsersWithoutCurrentUser.length !== allCloudUsers.length -1) // recalculate all cloudusers if length is not sufficient
    {
        allCloudUsersWithoutCurrentUser = []
        allCloudUsers.map(usr => {
            if (currentCloudUser.id !== usr.id)
                allCloudUsersWithoutCurrentUser.push(usr)
        })
        lastCurrentCloudUser = currentCloudUser
    }
}

export function CloudIdSelectSingle(props) {
    const store = useSelector(state => state) // redux hook

    if (!Array.isArray(store.allCloudUsers)) {
        return <></>
    }

    //prepare cloud users object if necessary
    reCalculateAllCloudUsersWithoutCurrentUser(store.allCloudUsers, store.currentCloudUser)

    let selectOptions;
    if( props.includeCurrentUser ) // if the current user shall be displayed
        selectOptions = Array.from( store.allCloudUsers)
    else
        selectOptions =  Array.from( allCloudUsersWithoutCurrentUser )

    return <Select
        components={{ SelectPlaceholder }}
        styles={{
            // Fixes the overlapping problem of the component
            menu: provided => ({...provided, zIndex: 9999})
        }}
        {...props}
        getOptionLabel ={(option)=>option.name}
        getOptionValue ={(option)=>option.id}
        noOptionsMessage={() => "Keine Optionen"}
        options={selectOptions}
        onChange={(currentlySelected) => {
            currentlySelected = !currentlySelected? {} : currentlySelected
            props.cloudUserChanged(currentlySelected);
        }}/>

}



export function CloudIdSelectMultiple(props) {

    const store = useSelector(state => state) // redux hook

    if (!Array.isArray(store.allCloudUsers)) {
        return <></>
    }
    //prepare cloud users object if necessary
    reCalculateAllCloudUsersWithoutCurrentUser(store.allCloudUsers, store.currentCloudUser)

    let selectOptions;
    if( props.includeCurrentUser ) // if the current user shall be displayed
        selectOptions = Array.from( store.allCloudUsers)
    else
        selectOptions =  Array.from( allCloudUsersWithoutCurrentUser )

    if( props.onlyWithRcAccount )
    {
        selectOptions = selectOptions?.filter( u => !!u?.rcUser?.uid)
    }

    //check for exclusions
    let options = selectOptions
    if( props.exclude && Array.isArray(props.exclude)) {
        options = []
        selectOptions.forEach(option => {
            if (!props.exclude.find(obj => obj.id === option.id) && option.id !== store.currentCloudUser.id )
                options.push(option)
                })
    }



    return <Select
       // components={{ SelectPlaceholder }}
        styles={{
            // Fixes the overlapping problem of the component
            menu: provided => ({...provided, zIndex: 9999})
        }}
        {...props}
        isMulti
        getOptionLabel ={(option)=>option.name}
        getOptionValue ={(option)=>option.id}
        options={options}
        noOptionsMessage={() => "Keine Optionen"}
        closeMenuOnSelect={false}
        components={animatedComponents}
        onChange={(currentlySelected) => {
            currentlySelected = !currentlySelected ? [] : currentlySelected
            props.cloudUserListChangedCallback(currentlySelected);
        }}/>

}


export function GroupSelectSingle(props) {
    const store = useSelector(state => state) // redux hook

    return <Select
        components={{ SelectPlaceholder }}
        styles={{
            // Fixes the overlapping problem of the component
            menu: provided => ({...provided, zIndex: 9999})
        }}
        {...props}
        getOptionLabel ={(option)=>option.name}
        getOptionValue ={(option)=>option.id}
        noOptionsMessage={() => "Keine Optionen"}
        options={store.currentCloudUser.groups}
        onChange={(currentlySelected) => {
            currentlySelected = !currentlySelected? {} : currentlySelected
            props.groupChanged(currentlySelected);
        }}/>

}



export function GroupSelectMultiple(props) {

    const store = useSelector(state => state) // redux hook

    return <Select
       // components={{ SelectPlaceholder }}
        styles={{
            // Fixes the overlapping problem of the component
            menu: provided => ({...provided, zIndex: 9999})
        }}
        {...props}
        isMulti
        getOptionLabel ={(option)=>option.name}
        getOptionValue ={(option)=>option.id}
        noOptionsMessage={() => "Keine Optionen"}
        closeMenuOnSelect={false}
        options={store.currentCloudUser.groups}
        components={animatedComponents}
        onChange={(currentlySelected) => {
            currentlySelected = !currentlySelected ? [] : currentlySelected
            props.groupListChangedCallback(currentlySelected);
        }}/>

}

/**
 * SECTIONS
 */


const sectionSelectColors = ["e2cfc4","c6def1","dbcdf0","f7d9c4","faedcb","c9e4de","f2c6de","f9c6c9","e2e2df","d2d2cf",]
let lastSectionSelectColorsColorIndex = 0

function colorShift()
{
    if( lastSectionSelectColorsColorIndex >= sectionSelectColors.length )
        lastSectionSelectColorsColorIndex = 0
    return sectionSelectColors[lastSectionSelectColorsColorIndex++]
}

let groupIdColors = []
const customStylesSectionMultiSelect = {
    menu: provided => ({...provided, zIndex: 9999}),
        multiValue: (styles, { data }) => {

            // Assign colors to a group Id
            let colorObj  = groupIdColors.find( obj => obj.id === data.group_id )
            if( !colorObj )
            {
                colorObj = { id : data.group_id, color : colorShift()}
                groupIdColors.push( colorObj)
            }

            return {
                ...styles,
                backgroundColor: data.color === DEFAULT_GROUP_COLOR?  colorObj.color : data.color,
            };
        },
    multiValueLabel: (styles, { data }) => ({
        ...styles,
        color: SharedHelper.isColorTooDark( data.color )? "white" : "black",
    }),
    multiValueRemove: (styles, { data }) => ({
        ...styles,
        color: SharedHelper.isColorTooDark( data.color )? "white" : "black",
        }),

};

export function SectionSelectMultiple(props) {

    const store = useSelector(state => state) // redux hook
    let [value, setValue] = useState( [] )

    let options = []

    //prepare shown objects
    let newValues = props.value?.map( section =>
    {
        let grp = store.currentCloudUser.groups.find( grp => grp.id === section.group_id)
        if(!grp)
            return section

        return { ...section, nameWithGroup : grp.name + ": "+section.name, color : grp.color}
    })

    value = newValues

    var permissionCallback = props.permissionCallback ? props.permissionCallback : () => { return true }

    // Build menu object
    store.currentCloudUser.groups.forEach((grp) => {
            let obj = {
                label : grp.name,
                options : grp.sections?.map( sect =>
                {
                   return {...sect, nameWithGroup : grp.name + ": "+sect.name, color : grp.color}
                }).filter(permissionCallback)
            }
        options.push( obj)
        })

    return <Select
      //  components={{ SelectPlaceholder }}
        {...props}
        value={value}
        isMulti
        styles={props.styles? {...customStylesSectionMultiSelect, ...props.styles} :  customStylesSectionMultiSelect}
        getOptionLabel={(option) => option.nameWithGroup}
        getOptionValue={(option) => option.id}
        noOptionsMessage={() => "Keine Optionen"}
        closeMenuOnSelect={false}
        options={options}
        components={animatedComponents}
        onChange={(currentlySelected) => {
            currentlySelected = !currentlySelected ? [] : currentlySelected
            props.sectionListChangedCallback(currentlySelected);
        }}

    />

}


/**
 * ROOMS
 */

export function RoomSelectMultiple(props) {

    const store = useSelector(state => state) // redux hook

    let options = []
    store.rooms?.forEach((school) =>
    {
        let ops = {label : school.name, options : []}
        school?.rooms?.forEach(r =>
        {
            ops.options.push(r)
        })
        options.push(ops )
    })

    if(!options)
        options = []


    return <Select
      //  components={{ SelectPlaceholder }}
        styles={{
            // Fixes the overlapping problem of the component
            menu: provided => ({...provided, zIndex: 9999})
        }}
        {...props}
        isMulti
        noOptionsMessage={() => "Keine Optionen"}
        getOptionLabel={(option) => option.name}
        getOptionValue={(option) => option.id}
        closeMenuOnSelect={false}
        options={options}
        components={animatedComponents}
        onChange={(currentlySelected) => {
            props.roomsChangedCallback(currentlySelected)
        }}/>

}
