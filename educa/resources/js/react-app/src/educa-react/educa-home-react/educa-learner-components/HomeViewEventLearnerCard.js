import React, {useState} from 'react';
import Card from "react-bootstrap/Card";
import {ListGroup} from "react-bootstrap";
import moment from "moment";
import {EducaCardLinkButton} from "../../../shared/shared-components/Buttons";
import {BASE_ROUTES} from "../../App";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper";


const MAX_EVENTS = 3
export default function HomeViewEventLearnerCard(props) {

    let [events, setEvents] = useState([])
    const [translate] = useEducaLocalizedStrings()

    let _isMounted = false;
    React.useEffect(() => {
        _isMounted = true
        getEvents()
        return () => {
            _isMounted = false;
        };
    }, []);


    let getEvents = () => {
        AjaxHelper.getMainFeedEvents()
            .then(resp => {
                if (resp.status > 0 && resp.payload?.events)
                    return _isMounted ? setEvents(resp.payload.events) : null
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Die Termine konnten nicht geladen werden. " + err.message)
            })
    }

    return (
        <div className="mb-2 animate__animated animate__fadeIn">
            <div className={"d-flex justify-content-between"}><h4><img style={{width: "30px", height: "30px"}}
                          src="/images/kalender_launcher.png"/> {translate("appointments","Termine")}</h4>
                    <EducaCardLinkButton
                        onClick={() => props.changeRoute(BASE_ROUTES.ROOT_CALENDER, "")}
                        className="card-link m-1" style={{fontSize: "0.9rem"}}>
                        {translate("see_all","Alle ansehen")}</EducaCardLinkButton>
            </div>
            {events?.length > 0 ?
            <ListGroup> {
                    events.map((event, index) => {
                        if (index >= MAX_EVENTS)
                            return
                        let end = moment(event.endDate)
                        let start = moment(event.startDate)
                        return <ListGroup.Item
                            className={"d-flex"}
                            onClick={() => props.changeRoute(BASE_ROUTES.ROOT_CALENDER, "?event_id=" + event.id)}
                            key={index}
                            style={{cursor: "pointer"}}>
                            <div className="text-center">
                                <h2 className={"text-danger"}><b>{start.format('D')}</b></h2>
                                <h6>{start.locale('de').format('MMMM')}</h6>
                            </div>
                            <div className="ml-2 m-1" style={{overflow: "hidden"}}>
                                <h5 style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>
                                    <b>{event.title}</b></h5>
                                <div><i className="fas fa-clock"></i> {start.format("HH:mm")} - {end.format("HH:mm")}
                                </div>
                            </div>
                        </ListGroup.Item>
                    })} </ListGroup>
                    :
                    <i>{translate("appointments.no_appointments","Es gibt noch keine Termine für dich.")} </i>}
        </div>
    );
}

