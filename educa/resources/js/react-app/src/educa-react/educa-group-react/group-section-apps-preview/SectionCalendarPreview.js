import React, {Component, useEffect, useState} from 'react';
import {connect, useSelector} from "react-redux";
import Button from "react-bootstrap/Button";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper";
import MutedParagraph from "../../educa-home-react/educa-learner-components/MutedParagraph";
import {Card, Carousel, ListGroup} from "react-bootstrap";
import {EducaShimmer} from "../../../shared/shared-components/EducaShimmer";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";
import moment from "moment/moment";

const MAX_EVENTS = 3
const SectionCalendarPreview = ({section, app, openApp}) => {

    const [isMounted, setIsMounted] = useState(false)
    const [events, setEvents] = useState(undefined)
    const [translate] = useEducaLocalizedStrings()

    useEffect(() => {
        setIsMounted(true)
        return () => setIsMounted(false)
    }, [])

    useEffect(() => {
        if (section.id)
            loadEvents()
    }, [section]);

    function loadEvents() {

        AjaxHelper.getSectionEvents(section.id)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.events) {
                    setEvents(resp.payload.events)
                    return
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Fehler bei der Event Übertragung. Servernachricht: " + err.message)
                if (isMounted) setEvents([])
            })
    }

    if (!isMounted)
        return <></>
    if(events === undefined)
        return <EducaShimmer/>
    if(!events || events.length == 0)
        return <Card className={"m-2 p-2"}>
            <div>
                <MutedParagraph><i className="fas fa-info-circle"></i>{translate("group_view.no_events","Bisher gibt es keine Termine im Kalendar.")} </MutedParagraph>
                <Button variant={"outline-dark"} onClick={() => openApp(app)}>{translate("group_view.open_calender","Kalendar öffnen")}</Button>
            </div>
        </Card>
    return (
        <div className="mb-2 animate__animated animate__fadeIn">
            <ListGroup> {
                    events.map((event, index) => {
                        if (index >= MAX_EVENTS)
                            return
                        let end = moment(event.endDate)
                        let start = moment(event.startDate)
                        return <ListGroup.Item
                            className={"d-flex flex-row"}
                            onClick={() => openApp(app)}
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
        </div>
    );

}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(SectionCalendarPreview);
