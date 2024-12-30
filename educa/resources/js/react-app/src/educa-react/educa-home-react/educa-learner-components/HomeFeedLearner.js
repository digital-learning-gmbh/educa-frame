import React, {useRef, useState} from 'react';
import {ListGroup, Nav} from 'react-bootstrap';
import Card from "react-bootstrap/Card";
import {EducaCardLinkButton} from "../../../shared/shared-components/Buttons";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import {BASE_ROUTES} from "../../App";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper";
import MainHeading from "./MainHeading";
import {EducaShimmer} from "../../../shared/shared-components/EducaShimmer";
import AjaxHelper from '../../helpers/EducaAjaxHelper';
import EducaFeed from '../../educa-components/EducaFeed';
import SubHeading from "./SubHeading.js";



const MAX_BADGES = 3
export default function HomeFeedLearner(props) {

    let [filter, setFilter] = useState("all")
    const [translate] = useEducaLocalizedStrings()

    let feed = useRef();

    return <div
        className="mb-2"
    >
        <div className={"d-flex justify-content-between"}><MainHeading>
            {props.title??translate("home.personal_feed", "Persönlicher Lernfeed")}</MainHeading>
        </div>

        <Nav variant="tabs" defaultActiveKey="all"
             onSelect={(selectedKey) =>
             {
                 setFilter(selectedKey);
                 feed?.current?.refreshFeed()
             }}
        >
            <Nav.Item>
                <Nav.Link eventKey="all">{translate("home_feed.all","Alle")}</Nav.Link>
            </Nav.Item>
            <Nav.Item>
                <Nav.Link eventKey="announcement">{translate("home_feed.announcement","Ankündigungen")}</Nav.Link>
            </Nav.Item>
            <Nav.Item>
                <Nav.Link eventKey="task" >{translate("tasks", "Aufgaben")}</Nav.Link>
            </Nav.Item>
            <Nav.Item>
                <Nav.Link eventKey="event">{translate("appointments", "Termine")}</Nav.Link>
            </Nav.Item>
            <Nav.Item>
                <Nav.Link eventKey="learnContent">{translate("home_feed.learning_contents", "Lerninhalte")}</Nav.Link>
            </Nav.Item>
        </Nav>

        <div style={{ overflowY: "auto", height: "800px"}}>
            <EducaFeed
                ref={feed}
                reloadButtonStyle={{
                    position: "fixed",
                    zIndex: 100,
                    right: "50%",
                    top: "10vh",
                    fontSize: "15px",
                    fontWeight: "bold"
                }}
                showStatistics={false}
                key={"mainFeed"}
                feedGetterFunc={timestamp => {
                    return AjaxHelper.getMainFeed(timestamp,filter);
                }}
            />
        </div>
    </div>;
}
