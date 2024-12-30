import React, {useEffect, useState} from "react";
import {SideMenuHeadingStyle} from "../educa-components/EducaStyles";
import SideMenu from "../educa-components/SideMenu";
import {Card} from "react-bootstrap";
import {useSelector} from "react-redux";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import EducaFileBrowser from "../educa-components/EducaFileBrowser/EducaFileBrowser";
import {MODELS} from "../../shared/shared-helpers/SharedHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper.js";

export default function EducaDocumentsViewReact(props)
{
    let currentUser = useSelector(s => s.currentCloudUser)

    let [selectedSection, setSelectedSection] = useState(null);
    let [selectedGroup, setSelectedGroup] = useState(null);
    const [translate] = useEducaLocalizedStrings()

    let clickOnSection = (section, group) => {
        setSelectedSection(section)
        setSelectedGroup(group)
    }

    useEffect(() => {
        // select default first group
    },[])

    let getSidemenu = () => {
        let newMenu = currentUser?.groups?.
             map( grp => ({...grp, sections : grp.sections?.filter( (sect) => !!FliesentischZentralrat.sectionFilesView(sect) && !!sect?.section_group_apps?.find( app => app?.group_app?.type == "files"))}))
            .filter( grp => !!grp.sections?.length)
            .sort((a, b) => a?.name.localeCompare(b?.name))
            .map((group) => {

            const moreThanOneSection = group?.sections?.length > 1
            return {
                heading: {textAndId: group?.name, component:  <div style={{cursor : moreThanOneSection? undefined : "pointer"}} className={"d-flex"} onClick={() => clickOnSection(group.sections[0], group)}>
                        <img style={{width: "35px", height: "35px", borderRadius: "2px"}} className={"mr-1"}
                             src={AjaxHelper.getGroupAvatarUrl(group.id, 35, group.image)}/><div className={"mt-1"}>{group.name}</div></div>
                        },
                content: moreThanOneSection ? group?.sections?.map((section) => {
                    return {
                        component: (
                            <div>
                                {section.name}
                            </div>
                        ),
                            clickCallback: () => { clickOnSection(section, group) },
                    }
                }) : [],
            };
        })
        return <SideMenu hideMarign={true} menus={newMenu}></SideMenu>;
    }

    return <div>
        <div className="d-flex justify-content-between">
            <div style={{width: "300px"}} className={"m-2"}>
                <div style={SideMenuHeadingStyle} className={"mb-1"}>{translate("document_view.documents_group","Dokumente aus Gruppen")}</div>
                {getSidemenu()}
            </div>
            <div style={{width: "calc(100vw - 300px)"}} className="mt-2">
                <Card>
                    <Card.Body>
                        { selectedSection ?   <>
                                <h5>{translate("document_view.documents_from", "Dokumente von")} {selectedGroup?.name} {translate("document_view.in_section","in dem Bereich")} {selectedSection?.name}</h5>
                                <EducaFileBrowser
                                modelType={MODELS.SECTION}
                                modelId={selectedSection.id}
                                canUserUpload={FliesentischZentralrat.sectionFilesUpload(selectedSection)}
                                canUserEdit={FliesentischZentralrat.sectionFilesEdit(selectedSection)}
                            /></> :
                        <h4 className={"text-center"}><i className="fas fa-info-circle"></i> {translate("document_view.info_select_group","Bitte wähle eine Gruppe oder einen Bereich aus.")} </h4> }
                    </Card.Body>
                </Card>
            </div>
        </div>
    </div>
}
