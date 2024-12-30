import React, { useState } from "react";
import { ListGroup } from "react-bootstrap";
import Card from "react-bootstrap/Card";
import { EducaCardLinkButton } from "../../shared/shared-components/Buttons";
import { BASE_ROUTES } from "../App";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import { useEducaLocalizedStrings } from "../helpers/StringLocalizationHelper";
import EducaHelper from "../helpers/EducaHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";
import {buildStyles, CircularProgressbar} from "react-circular-progressbar";
import {useSelector} from "react-redux";
import {LocalizedLabel} from "../learncontent-components/LearnContentHelper";

const MAX_COURSE = 3;
export default function HomeInteractiveCourseCard(props) {
    let [course, setCourses] = useState([]);
    const [translate] = useEducaLocalizedStrings();

    let currentTenant = useSelector(s => s.tenant)

    let _isMounted = false;
    React.useEffect(() => {
        _isMounted = true;
        getInteractiveCourse();
        return () => {
            _isMounted = false;
        };
    }, []);

    let getInteractiveCourse = () => {
        AjaxHelper.getMainFeedInteractiveCourse()
            .then(resp => {
                if (resp.status > 0 && resp.payload?.courses)
                    return _isMounted ? setCourses(resp.payload.courses) : null;
                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    translate("error", "Fehler"),
                    translate(
                        "interactive_course.status.load_failure",
                        "Die interaktiven Kurse konnten nicht geladen werden."
                    ) + err.message
                );
            });
    };

    return (
        <Card bg={"white"} text={"dark"} className="mb-2 animate__animated animate__fadeIn">
            <Card.Body>
                <Card.Title>
                    <h4>
                        <img
                            style={{ width: "40px", height: "40px" }}
                            src="/images/edu_launcher.png"
                        />
                        {translate("interactive_courses", "Interaktive Kurse")}
                        { FliesentischZentralrat.globalCanEdu() ? <EducaCardLinkButton
                            onClick={() => props.changeRoute(BASE_ROUTES.ROOT_LEARNMATERIALS, "")}
                            className="card-link m-1" style={{fontSize: "0.9rem"}}>{translate("see_all","Alle ansehen")}</EducaCardLinkButton> : <></> }
                    </h4>
                </Card.Title>
            </Card.Body>
            <ListGroup variant={"flush"}>
                {course?.length > 0 ? (
                    course.filter((courseSingle) => courseSingle.progress < 1)?.sort((a,b) => b.progress - a.progress).map((courseSingle, index) => {
                        if (index >= MAX_COURSE) return;
                        return (
                            <ListGroup.Item
                                className={"d-flex"}
                                onClick={() =>
                                    props.changeRoute(
                                        BASE_ROUTES.ROOT_GROUPS +
                                        courseSingle.groupLink,
                                        null
                                    )
                                }
                                key={index}
                                style={{ cursor: "pointer" }}>
                                <div className="text-center" style={{width: "50px"}}>
                                    <CircularProgressbar styles={buildStyles({
                                        textColor: currentTenant?.color ? currentTenant?.color : "#202A44",
                                        pathColor: currentTenant?.color ? currentTenant?.color : "#202A44",
                                        trailColor: "#f2f3f5",
                                    })} strokeWidth={12} value={Math.round(courseSingle.progress*100)} text={`${Math
                                        .round(courseSingle.progress*100)}%`} />
                                </div>
                                <div className="ml-2 m-1" style={{overflow: "hidden"}}>
                                    <h5 style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>
                                        <b><LocalizedLabel json={courseSingle.title}></LocalizedLabel></b></h5>
                                    <div>{courseSingle.countChapters} Kapitel, {courseSingle.countTopics === 1 ? courseSingle.countTopics + " Thema" : courseSingle.countTopics + " Themen"}
                                    </div>
                                </div>
                            </ListGroup.Item>
                        );
                    })
                ) : (
                    <ListGroup.Item>
                        {translate(
                            "interactive_course.status.availability_failure",
                            "Es gibt noch keine Kurse für dich."
                        )}
                    </ListGroup.Item>
                )}
            </ListGroup>
        </Card>
    );
}
