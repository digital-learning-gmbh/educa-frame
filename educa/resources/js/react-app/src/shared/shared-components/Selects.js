import {useSelector} from "react-redux";
import Select, {components} from "react-select";
import React from "react";

export const SelectPlaceholder = props => {
    return <components.Placeholder {...props} />;
};

export const getSelectErrorStyle = (isError) => ({
    control: (base, state) => ({
        ...base,
        // state.isFocused can display different borderColor if you need it
        borderColor: state.isFocused ?
            '#ddd' : !isError ?
                '#ddd' : 'red',
        // overwrittes hover style
        '&:hover': {
            borderColor: state.isFocused ?
                '#ddd' : !isError ?
                    '#ddd' : 'red'
        }
    })
})

/**
 * SUPPORT TABLE SELECTS
 */

export function CountrySelect(props)
{

    let countries = useSelector(state => state?.supportTables?.countries)

    let value = countries?.find( c => c.key === props.value)
    return <Select
        components={{ SelectPlaceholder }}
        styles={{
            // Fixes the overlapping problem of the component
            menu: provided => ({...provided, zIndex: 9999}),
        }}
        isMulti={props.isMulti}
        placeholder={props.isMulti? "Länder..." : "Land..."}
        isDisabled={props.isDisabled}
        noOptionsMessage={() => "Keine Optionen"}
        options={countries}
        getOptionLabel={(option) => option.value}
        getOptionValue={(option) => option.key}
        value={value}
        isClearable={props.isClearable}
        onChange={(val) => props?.onChange(val?.key)}
        style={{display: "flex"}}/>

}
