import React, {useState} from 'react';
import {Alert, Card, Tab, Tabs} from "react-bootstrap";
import {SectionSelectMultiple} from "../../shared/shared-components/EducaSelects";
import FliesentischZentralrat from "../FliesentischZentralrat";
import AnnouncementsView from "./AnnouncementsView";
import EventEditor from "../educa-calendar-react/EventEditor";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper.js";


export default function HomeFeedCreator({refreshFeed}) {
    const [selectedSections, setSelectedSections] = useState([])
    const [translate] = useEducaLocalizedStrings()


    const checkPermissions = (chosenSections) => {
        setSelectedSections(chosenSections)
        // if (chosenSections != null && chosenSections.length > 0) {
        //     let cannotEditAllSections = false
        //     let notEditableSections = []
        //     let editableSections = []
        //     chosenSections.map((sec) => {
        //         if (!FliesentischZentralrat.sectionAnnouncementCreate(sec)) {
        //             cannotEditAllSections = true
        //             notEditableSections.push(sec.nameWithGroup)
        //         }
        //         else editableSections.push(sec)
        //     })
        //     setHideCreate(cannotEditAllSections)
        //     setNonEditableSections(notEditableSections)
        // }
    }

    const filterBasedPermission = (section) => {
        return FliesentischZentralrat.sectionAnnouncementCreate(section); // todo and app is activated
    }

    const filterBasedPermissionInverse = (section) => {
        return !FliesentischZentralrat.sectionAnnouncementCreate(section); // todo and app is activated
    }

    return <Card>
        <Card.Body>
            <div className={"float-right"} style={{ minWidth: "200px", maxWidth: "300px"}}>
                <SectionSelectMultiple isMulti={true} value={selectedSections} sectionListChangedCallback={(chosenSections) => checkPermissions(chosenSections)}/>
            </div>
            <h5><b>{translate("home_feed.new_content","Neuen Inhalt erstellen")}</b></h5>
            <h6>{translate("home_feed.create_content", "Erstelle hier einen neuen Inhalt, wie z.B. eine Ankündigung, ein Termin oder Dokument")}</h6>
            <div className={"clearfix"}></div>
            <div className={"float-left w-100"}>
            { selectedSections.length > 0 ?
            <Tabs id="creator-tabs" mountOnEnter={true}>
                <Tab eventKey="annoucements" title={<><i className="fa fa fa-bullhorn"></i> {translate("home_feed.announcement","Ank\u00fcndigungen")}</>}>
                                <div className={'mt-4'}>
                                    {      selectedSections.filter(filterBasedPermissionInverse).length > 0 ?
                                        <Alert variant={"warning"}>
                                            <p>
                                                {translate("home_feed.skip_sections", "Diese Bereiche werden übersprungen, da du für sie keine Ankündigungen erstellen darfst:")}                                           </p>
                                            {selectedSections.filter(filterBasedPermissionInverse).map(s => s.nameWithGroup).join(", ")}
                                        </Alert> : null }
                                    {!!selectedSections && selectedSections.filter(filterBasedPermission).length > 0 ?
                                    <AnnouncementsView refreshFeed={() => refreshFeed()}
                                                       sections={selectedSections.filter(filterBasedPermission)}
                                                       clearSelectedSectionsInParent={() => setSelectedSections([])}
                                                       groupBrowse={false}
                                                       loadTemplates={true}
                                    />
                                    :
                                    <p>{translate("home_feed.select_sections","Bitte wähle mindestens ein Bereiche aus, für die die Ankündigung erstellt werden soll und für den Sie Berechtigung besitzen.")}</p>}
                                </div>
                </Tab>
                <Tab eventKey="document" title={<><i className="fa fa fa-folder-open"></i>{translate("learn_content.type.document","Dokument")}</>}>
                    <div className={'mt-4'}>
                    </div>
                </Tab>
                <Tab eventKey="event" title={<><i className="fa fa fa-calendar-alt"></i>{translate("learn_content.type.event","Termin")}</>}>
                    <div className={'mt-4'}>
                        <EventEditor hideChoice={true}></EventEditor> {/*TODO: check rights &|| call on eventClass:default only*/}
                    </div>
                </Tab>
            </Tabs> : null }
            </div>
        </Card.Body>
    </Card>
}
