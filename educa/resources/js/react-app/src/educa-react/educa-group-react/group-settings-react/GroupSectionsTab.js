import React, { useEffect, useState } from "react";
import { Accordion } from "react-bootstrap";
import { ReactSortable } from "react-sortablejs";
import {
    CollapsePanelComponent,
    reassignOrder
} from "./EducaGroupSettingsView";
import { sortGroupSections, sortSections } from "../EducaGroupViewReact";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import SectionSettings from "./GroupSectionSettings";
import { EducaLoading } from "../../../shared-local/Loading";
import Button from "react-bootstrap/Button";
import {GROUP_VIEWS} from "../group-browse/GroupBrowse.js";
import {useTranslation} from "react-i18next";

export default function GroupSectionsTab(props) {
    const [sections, setSections] = useState(undefined);
    const t = useTranslation().t;

    useEffect(() => {
        setSections(props.group.sections);
    }, [props.group]);

    function updateSection(section) {
        const index = sections.findIndex(element => element.id === section.id);

        let newSections = sections;
        newSections[index] = section;
        setSections(sections);

        props.setGroup({ ...props.group, sections: newSections });
    }

    function removeSection(sectionId) {
        props.setGroup({
            ...props.group,
            sections: sections.filter(section => section.id !== sectionId)
        });
    }

    function createNewSection() {
        AjaxHelper.addSectionToGroup(props.group.id, "Neuer Bereich")
            .then(resp => {
                if (resp.status > 0 && resp.payload) {
                    EducaHelper.fireSuccessToast(
                        "Hinzufügen erfolgreich.",
                        t("section") + " erfolgreich hinzugefügt."
                    );

                    props.setGroup(resp.payload);
                } else throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Hinzufügen fehlgeschlagen.",
                    "Hinzufügen der " +
                    t("section") +
                    " fehlgeschlagen. Servermessage: " +
                    err.message
                );
            })
    }

    if (!sections) return <EducaLoading />;

    return (
        <div
            style={{
                width: "750px",
                marginRight: "auto",
                marginLeft: "auto"
            }}
            className={"mt-2"}
        >
            <div className={"row justify-content-end m-2"}>
                <Button variant={"primary"} onClick={createNewSection}><i className="fas fa-plus"></i> Bereich hinzufügen</Button>
            </div>
            <Accordion defaultActiveKey={sections[0].id}>
                <ReactSortable
                    list={sections}
                    setList={(elements) => {
                        let newSections = reassignOrder(elements);

                        let updates = {};

                        for (let i = 0; i < newSections.length; i++)
                            updates[elements[i].id] = i;

                        setSections(sortSections(newSections));

                        AjaxHelper.reorderSections(props.group.id, updates)
                            .then(resp => {
                                if (
                                    resp.status > 0 &&
                                    resp.payload?.group?.sections
                                ) {
                                    props.setGroup(
                                        sortGroupSections(resp.payload.group)
                                    );
                                } else throw new Error("");
                            })
                            .catch(_ => {
                                EducaHelper.fireErrorToast(
                                    "Fehler",
                                    "Die Bereichsreihenfolge konnte nicht aktualisiert werden."
                                );
                                setSections(props.group.sections);
                            });
                    }}
                >
                    {sections.map(section => {
//                        if (!FliesentischZentralrat.sectionViewSection(section))
  //                          return <div key={section.id}></div>;

                        return (
                            <div key={section.id}>
                                <Accordion.Toggle
                                    as={CollapsePanelComponent}
                                    variant="link"
                                    eventKey={section.id}
                                >
                                    <i class="fas fa-sort"></i> {" "}
                                    {section.name}
                                </Accordion.Toggle>
                                <Accordion.Collapse eventKey={section.id}>
                                    <SectionSettings
                                        key={section.id}
                                        group={props.group}
                                        section={section}
                                        removeSection={removeSection}
                                        updateSection={updateSection}
                                    />
                                </Accordion.Collapse>
                            </div>
                        );
                    })}
                </ReactSortable>
            </Accordion>
        </div>
    );
}
