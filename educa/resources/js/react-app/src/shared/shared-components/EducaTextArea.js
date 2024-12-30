import TextareaAutosize from "react-textarea-autosize";
import React from "react"

export function EducaTextArea(props)
{

    return <TextareaAutosize
    className={props.className? props.className + " form-control" : "form-control"}
        {...props}
    />
}
