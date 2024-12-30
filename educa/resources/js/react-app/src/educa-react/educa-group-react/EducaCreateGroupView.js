import React, {useEffect, useState} from 'react';
import SmallSideMenu from "../educa-components/SmallSideMenu";
import EducaHelper, {DEFAULT_COLORS} from "../helpers/EducaHelper";
import GroupBrowse, {GROUP_VIEWS} from "./group-browse/GroupBrowse";
import {Card, Container, FormControl, Spinner} from "react-bootstrap";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import {EducaFormPair} from "../../shared/shared-components";
import Button from "react-bootstrap/Button";
import {CirclePicker} from "react-color";
import {EducaLoading} from "../../shared-local/Loading";
import {BASE_ROUTES} from "../App";
import {useHistory} from "react-router";
import {useDispatch} from "react-redux";
import {GENERAL_UPDATE_OR_ADD_GROUP} from "../reducers/GeneralReducer";
import SideMenu from "../educa-components/SideMenu";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper";
import MutedParagraph from "../educa-home-react/educa-learner-components/MutedParagraph.js";
import MainHeading from "../educa-home-react/educa-learner-components/MainHeading.js";

const EducaCreateGroupView = () => {

    const [group, setGroup] = useState({
        name : "",
        color : ""
    })
    const [templates, setTemplates] = useState([])
    const [selectedTemplate, setSelectedTemplate] = useState(null)
    const [templatesLoading, setTemplatesLoading] = useState(false)
    const [loading, setLoading] = useState(false)

    const dispatch = useDispatch()
    const history = useHistory()
    const [translate] = useEducaLocalizedStrings()

  const reduxRefreshGroup = group => dispatch({ type: GENERAL_UPDATE_OR_ADD_GROUP, payload: group })

    useEffect(() => {
        loadGroupTemplates()
    },[])

    function navigate(newPath, replace = false) {
        if (newPath.length <= 0) return;

        if (replace)
            history.replace(BASE_ROUTES.ROOT_GROUPS + "/" + newPath.join("/"));
        else history.push(BASE_ROUTES.ROOT_GROUPS + "/" + newPath.join("/"));
    }

    const loadGroupTemplates = () =>
    {
        setTemplatesLoading(true)
        AjaxHelper.getGroupTemplates()
            .then( resp => {
                setTemplates(resp.payload.group_templates??[])
            })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Vorlagen konnten nicht geladen werden."))
            .finally(() => setTemplatesLoading(false))
    }

    const onTemplateSelected = (ele) =>
    {
        if(!group?.name)
            setGroup({...group, name : ele?.id > 0? ele.name : "Neue Gruppe"})
        if(!group?.color)
            setGroup({...group, color : ele?.color})

        setSelectedTemplate(ele)
    }
    const createGroup = () => {

        setLoading(true)
        const promise = selectedTemplate.id > 0 ? AjaxHelper.createGroupFromGroupTemplates(selectedTemplate.id, group.name, group.color) : AjaxHelper.createGroup(group.name, group.color)
        promise.then( resp => {
            reduxRefreshGroup(resp.payload.group)
            navigate([resp.payload.group.id, GROUP_VIEWS.FEED])
            EducaHelper.fireSuccessToast("Gruppe erstellt", "Gruppe wurde erfolgreich erstellt",);
        })
            .catch(() => SharedHelper.fireErrorToast("Fehler", "Gruppe konnte nicht erstellt werden"))

            .finally(() => setLoading(false))
    }

    if(loading)
        return <EducaLoading/>

    return (
        <div className="d-flex justify-content-between">
            <div className="col mt-2 mb-5"
                 style={{ width: "calc(100% - 300px)" }}>
               <Container fluid={"xl"} className={"mt-3"}>
                   <MainHeading>Neue Gruppe erstellen</MainHeading>
                <MutedParagraph>Hier findest du angelegtes Templates für Gruppen. Gruppe-Templates enthalten Bereiche, Rechte und Rollen, die automatisch in die neue Gruppe übertragen werden. Damit musst du nicht immer alles wieder anlegen.</MutedParagraph>
                   {templatesLoading? <div style={{height :"300px"}}>
                       <Spinner animation={"grow"}/>
                   </div> : <>
                       <div className={"ml-1 mt-5"}>
                           <h5 className={"mt-2"}>Welches Template möchtest du verwenden?</h5>
                           <Grid elements={[
                               ...templates ?? [],
                               {id: -1, name: <i>Kein Template</i>, color: "#797979", image: null}
                           ].map(templ => templ?.id == selectedTemplate?.id ? {...templ, active: true} : templ)}
                                 onClick={(ele) => onTemplateSelected(ele)}/>
                       </div>
                   </>}

                   {selectedTemplate ?
                       <>
                           <h5 className={"mt-3"}>Wie soll die Gruppe heißen und welche Farbe soll sie haben?</h5>
                           <Card className={"mt-1"}>
                               <Card.Body>
                                   <EducaFormPair label={"Name"}>
                                       <FormControl value={group.name} placeholder={"Gruppenname..."}
                                                    onChange={e => setGroup({...group, name: e.target.value})}/>
                                   </EducaFormPair>
                                   <EducaFormPair label={"Farbe"}>
                                       <CirclePicker
                                           colors={DEFAULT_COLORS}
                                           color={group.color}
                                           onChangeComplete={color => setGroup({...group, color: color.hex})}
                                       />
                                   </EducaFormPair>
                               </Card.Body>
                           </Card>

                           <div className={"mt-2"}>
                               <Button className={"mt-1"} onClick={() => createGroup()}>
                                   <i className={"fas fa-plus"}/> Gruppe erstellen
                               </Button>
                           </div>
                       </> : null}
               </Container>

            </div>
        </div>
    );
};

export default EducaCreateGroupView;

const Grid = ({elements, onClick}) => {

    return <div
        style={{
            display: "flex",
            flexDirection: "row",
            columnGap: "1.5rem",
            rowGap: "0.8rem",
            flexWrap: "wrap",
        }}
    >
        {elements.map((element) => {
            return (
                <Card
                    key={element.value}
                    style={{
                        cursor: "pointer",
                        display: "flex",
                        flexDirection: "row",
                        maxWidth: "15rem",
                        width: "15rem",
                        aspectRatio: "3/1",
                        border : element?.active? "1px solid black" :""
                    }}
                    onClick={() => onClick(element)}
                >
                    { element.image != null ?
                        <div
                        style={{
                            display: "flex",
                            flexDirection: "column",
                            justifyContent: "center",
                            alignItems: "center",
                            flex: 1,
                            height: "100%",
                            aspectRatio: "1/1",
                            fontSize: "2rem",
                        }}
                    >
                    </div> : null }
                    <div
                        className={"m-1"}
                        style={{
                            flex: 1,
                            display :"flex",
                            alignItems :"center"
                        }}
                    >
                        <h6>{element.name}</h6>
                    </div>
                </Card>
            );
        })}
    </div>
}
