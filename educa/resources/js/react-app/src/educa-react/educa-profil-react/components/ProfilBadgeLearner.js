import React, {useState} from 'react';
import { ListGroup } from 'react-bootstrap';
import Card from "react-bootstrap/Card";
import {EducaCardLinkButton} from "../../../shared/shared-components/Buttons";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import {BASE_ROUTES} from "../../App";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import {useHistory} from "react-router";
import MainHeading from "../../educa-home-react/educa-learner-components/MainHeading.js";



export default function ProfilBadgeLearner(props) {


    let [badges, setBadges] = useState([])
    let history = useHistory()
    const [translate] = useEducaLocalizedStrings()

    return <div
        className="mb-4 animate__animated animate__fadeIn"
        >
            <div className={"d-flex justify-content-between"}><MainHeading><img style={{width: "30px", height: "30px"}}
                                     src="/images/medal.png"/> {translate("badges","Abzeichen")}</MainHeading>
            </div>
        {badges?.length > 0 ?
        <ListGroup> {
    badges.map((badge, index) => {
        if (index >= MAX_BADGES)
            return
        return <ListGroup.Item
            onClick={() => history.push(BASE_ROUTES.ROOT_TASKS, "?task_id=" + task.id)}
            key={index}
            style={{cursor: "pointer"}}>
            <h5 style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>
                <b>{task.title}</b></h5>
            <div>
                {task.end ? <> <i
                    className="fas fa-clock"></i> {SharedHelper.getFormattedDateString(task.end)}</> : null}
                {task.documentCount > 0 ? <><i
                    className="fas fa-paperclip"></i> {task.documentCount} Datei(en)</> : null}
            </div>
        </ListGroup.Item>
    })} </ListGroup>
    :
    <i> {translate("badge.no_badges","Du hast noch keine Abzeichen")}</i>}

    </div>
}
