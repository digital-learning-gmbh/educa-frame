import {ListGroup} from "react-bootstrap";
import AjaxHelper from "../../../helpers/EducaAjaxHelper.js";
import React, {useEffect, useState} from "react";

export const GroupPageElement = ({
    group,
    onClickCallback,
    model_id,
    model_type,
}) => {

    let [showElements, setShowElements] = useState(false)

    const moreThanOneSection = group?.sections?.length > 1

    useEffect(() => {
        let hasActiveElement = false;
        for (let i = 0; i < group?.sections?.length;i++)
        {
            if(model_type == "section" && model_id == group.sections[i]?.id)
            {
                hasActiveElement = true;
            }
        }
        if(hasActiveElement)
            setShowElements(true)
    }, [model_type, model_id]);


    return <ListGroup.Item active={model_type == "section" && model_id == group.sections[0]?.id && !moreThanOneSection}><div style={{cursor : moreThanOneSection? undefined : "pointer"}} className={"d-flex"} onClick={() => moreThanOneSection ? setShowElements(true) : onClickCallback("section",group.sections[0]?.id)}>
        <img style={{width: "25px", height: "25px", borderRadius: "2px"}} className={"mr-1"}
             src={AjaxHelper.getGroupAvatarUrl(group.id, 35, group.image)}/><div className={"mt-1"}>{group.name}</div></div>
        { showElements && moreThanOneSection ?
        <ListGroup className={"mt-1"} variant={"flush"}>
            {group?.sections?.map(section => {
                return <ListGroup.Item style={{padding: "0.5rem 0.75rem"}} active={model_type == "section" && model_id == section?.id} onClick={() => onClickCallback("section", section?.id)}>{section.name}</ListGroup.Item>
            })}
        </ListGroup> :null }
    </ListGroup.Item>
}
