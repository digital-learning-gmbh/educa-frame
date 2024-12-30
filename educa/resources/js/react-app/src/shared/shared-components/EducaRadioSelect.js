import React from 'react';
import SharedHelper from "../shared-helpers/SharedHelper";


const EducaRadioSelect = (props) => {

    const getOptionLabel = props.getOptionLabel? props.getOptionLabel :  (o) => o.label
    const getOptionValue = props.getOptionValue? props.getOptionValue :  (o) => o.value

    const options  = props.options? props.options : []
    const value = typeof props.value == "object"? props.value : {}

    //const uid = SharedHelper.createUUID()
    return (
        <div>
                <div style={{display :"flex", flexDirection : "column", flex : 1}} className={"m-2"}>
                    <div style={{marginBottom :"-2px", fontSize :"12px", display: "flex", flexDirection :"row"}}>
                        {props.placeholder}
                    </div>
                    <div>
                        <div style={{display : "flex", flexDirection :"column"}}>
                        {options.map((option, idx) => {
                                return <label key={idx}><input  type="radio"
                                                                disabled={!!props.isDisabled}
                                                      key={idx}
                                                      checked={getOptionValue(value) === getOptionValue(option) }
                                                      onChange={ () => props.onChange(option)}
                                /> {getOptionLabel(option)}</label>
                            }
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

export default EducaRadioSelect;
