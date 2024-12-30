import React from 'react';
import ReactSwitch from "react-switch";
import PropTypes from "prop-types";

function EducaLabeledSwitch({checked, onChange, labelLeft, labelRight, size="sm", ...props}) {

    const fontsize = !size || size === "sm"? "80%" : "100%";
    const width = !size || size === "sm"? 30 : 56;
    const height = !size || size === "sm"? 15 : 28;
    return <div style={{alignItems: "center", display: "flex", flexDirection: "row",  justifyContent: "flex-start", ...props.style}}>
        {labelLeft?<div className={"mr-1"} style={{fontSize: fontsize}}>{labelLeft}</div> : null}
        <div className={"ml-1 mr-1"} style={{display: "flex", justifyContent: "center", fontSize: "80%"}}>
            <ReactSwitch checked={checked} onChange={onChange} {...props} height={height} width={width}/>
        </div>
        {labelRight? <div className={"ml-1"} style={{display: "flex", fontSize: fontsize}}>{labelRight}</div> : null}
    </div>
}

EducaLabeledSwitch.propTypes =
    {
        checked: PropTypes.bool.isRequired,
        onChange : PropTypes.func.isRequired,
        labelLeft : PropTypes.string.isRequired,
        labelRight : PropTypes.string.isRequired,
        size : PropTypes.oneOf(["sm","lg"])
    }


export default EducaLabeledSwitch;
