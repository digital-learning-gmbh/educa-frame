import React, {useEffect, useState} from 'react';
import {Button, Dropdown} from 'react-bootstrap';
import EducaLabeledSwitch from '../../../shared/shared-components/EducaLabeledSwitch';
import {useSelector} from "react-redux";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import {useHistory} from "react-router";
import {BASE_ROUTES} from "../../App";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper.js";
import Form from "react-bootstrap/Form";


export default function FilterPanel({filter, setFilter}) {

    const [searchTerm, setSearchTerm] = useState("");
    let history = useHistory()
    let currentCloudUser = useSelector(s => s.currentCloudUser);
    const [translate] = useEducaLocalizedStrings()

    useEffect(() => {
        setSearchTerm("")
    },[filter])

    return <div className={"mb-2 d-flex justify-content-between"}>
        <div className='d-flex'>
            <Button disabled={true} variant="outline-secondary" className="m-1">
                <i className="fas fa-filter"></i> {translate("filters.filter","Filter")}
            </Button>

            <Dropdown>
                <Dropdown.Toggle className="m-1 bg-back-new" variant="link" id="dropdown-basic">
                    {translate("filters.select_group","Gruppe auswählen")}
                </Dropdown.Toggle>

                <Dropdown.Menu>
                    <Form.Control
                        type="text"
                        value={searchTerm}
                        placeholder={translate("search","Suche..")}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        style={{margin: "8px 10px", width: "calc(100% - 20px)", borderRadius: "5px"}}
                    />
                    {currentCloudUser?.groups?.filter((group) => group.name?.includes(searchTerm)).sort((groupA, groupB) => groupA.name?.localeCompare(groupB.name)).map((group) => <Dropdown.Item active={filter?.group?.id == group?.id}
                                                                             onClick={() => {
                                                                                 filter?.group?.id == group?.id ? setFilter({
                                                                                     ...filter,
                                                                                     group: null
                                                                                 }) : setFilter({
                                                                                     ...filter,
                                                                                     group: group
                                                                                 })
                                                                             }}>{group.name}</Dropdown.Item>)}
                </Dropdown.Menu>
            </Dropdown>


            <Dropdown>
                <Dropdown.Toggle className="m-1 bg-back-new" variant="link" id="dropdown-basic">
                    {translate("filters.select_category", "Kategorie auswählen")}
                </Dropdown.Toggle>
                <Dropdown.Menu>
                    <Dropdown.Item><i>{translate("filters.no_category", "Keine Kategorien")}</i></Dropdown.Item>
                </Dropdown.Menu>
            </Dropdown>

            {FliesentischZentralrat.globalGroupCreate() ? <Button className="m-1" onClick={() => history.push(BASE_ROUTES.ROOT_HOME + "/create-group")} variant={"success"}><i className="fas fa-plus"></i> {translate("filters.add_group","Gruppe erstellen")} </Button> : null }

        </div>
        <div className='d-flex'>
            {/*<Button className="m-1" variant={"outline-secondary"} onClick={() => history.push(BASE_ROUTES.ROOT_CREATOR_ASSISTANT + "/start")}><i className="fas fa-magic"></i> Inhalt erstellen </Button>*/}
        </div>
    </div>
}
