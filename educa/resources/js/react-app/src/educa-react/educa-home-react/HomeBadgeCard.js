import React, {useState} from 'react';
import { ListGroup } from 'react-bootstrap';
import Card from "react-bootstrap/Card";
import { EducaCardLinkButton } from '../../shared/shared-components/Buttons';
import { BASE_ROUTES } from '../App';
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";



const MAX_BADGES = 3
export default function HomeBadgeCard(props) {


    let [badges, setBadges] = useState([])
    const [translate] = useEducaLocalizedStrings()

    return <Card
        bg={"white"}
        text={'dark'}
        className="mb-2 animate__animated animate__fadeIn"
        >
        <Card.Body>
            <Card.Title><h4><img style={{width: "40px", height: "40px"}}
                                     src="/images/medal.png"/> {translate("badges","Abzeichen")}
                { FliesentischZentralrat.globalCanEdu() ? <EducaCardLinkButton
                    onClick={() => props.changeRoute(BASE_ROUTES.ROOT_LEARNMATERIALS, "")}
                    className="card-link m-1" style={{fontSize: "0.9rem"}}>{translate("see_all","Alle ansehen")}</EducaCardLinkButton> : <></> }</h4>
            </Card.Title>
        </Card.Body>
        <ListGroup variant={"flush"}>
        {badges?.length > 0 ?
    badges.map((badge, index) => {
        if (index >= MAX_BADGES)
            return
        return <ListGroup.Item
            onClick={() => props.changeRoute(BASE_ROUTES.ROOT_TASKS, "?task_id=" + task.id)}
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
    })
    :
    <ListGroup.Item> {translate("badge.no_badges","Du hast noch keine Abzeichen")}</ListGroup.Item>}
    </ListGroup>
    </Card>
}
