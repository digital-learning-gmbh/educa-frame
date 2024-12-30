import {Button, Card} from "react-bootstrap";
import React, {useState} from 'react'
import ReactTooltip from "react-tooltip";
import SharedHelper from "../shared-helpers/SharedHelper";

export function EducaPrimaryButton(props) {

    return (<Button
        variant={"primary"}
        {...props}>
    </Button>)
}

export function EducaPrimaryButtonWrapped(props) {

    return (
        <div
            style={{
                margin: "5px",
                display:"flex",
                flex : 1,
                width:"100%",
                justifyContent :"center",
                flexDirection : "row"
            }}
        >
            <EducaPrimaryButton {...props}/>
        </div>)
}




export function EducaSecondaryButton(props) {

    return (<Button
        variant={"secondary"}
        {...props}>
    </Button>)
}

export function EducaSecondaryButtonWrapped(props) {

    return (
        <div
            style={{
                margin: "5px",
                display:"flex",
                flex : 1,
                width:"100%",
                justifyContent :"center",
                flexDirection : "row"
            }}
        >
            <EducaSecondaryButton {...props}/>
        </div>)
}

export function EducaCircularButton(props) {

    let style = {borderRadius : "50%", maxWidth : "35px", width:"35px", maxHeight :"35px", height :"35px", padding: "5px", textAlign: "center", ...props.style} // medium
    if(props.size === "big")
        style = {borderRadius : "50%",  maxWidth : "50px", width:"50px", maxHeight :"50px", height :"50px", padding: "6px", textAlign: "center", ...props.style}
    else if(props.size === "small")
        style = {borderRadius : "50%", maxWidth : "25px",  width:"25px", maxHeight :"25px", height :"25px", padding: "0px", textAlign: "center", ...props.style}

    const tooltip = props.tooltip
    const uuid = SharedHelper.createUUID()

        return (
            <>
                <Button
                    {...props}
                    style={style}
                    data-for={tooltip? uuid : undefined}
                    data-tip={tooltip? "tooltip" : undefined}
                >
                </Button>
                {tooltip?
                <ReactTooltip
                    id={uuid}
                    place={"bottom"}>
                    {tooltip}
                </ReactTooltip> : null}
            </>
        )

}

/**
 * Card.Link without href, that appears as link
 * */
export function EducaCardLinkButton(props)
{
    let colorHover = props.colorHover?props.colorHover : "#0056b3"
    let colorDefault = props.color?props.color : "rgb(0, 123, 255)"

    let disabledStyle = props.disabled? {color:"#6c757d"} : {}
    let defaultStyle = {...props.style, color : colorDefault, cursor : "pointer", ...disabledStyle}
    let hoverStyle = {...props.style, color : colorHover, textDecoration : props.underlineOnHover? "underline" : undefined,  cursor : "pointer",...disabledStyle}

    let [isHovered, setIsHovered] = useState(false);

    let prps = Object.assign({}, props)
    delete prps.colorHover
    delete prps.underlineOnHover
    delete prps.color
    return <Card.Link
        {...prps}
        onClick={(evt) => {if(!props.disabled && props.onClick) props.onClick(evt)}}
        style={isHovered ?  hoverStyle : defaultStyle}
        onMouseEnter={ () => { !isHovered? setIsHovered(true): null}}
        onMouseLeave={ () => { isHovered? setIsHovered(false ) : null}}
    >
    </Card.Link>
}



