import React, {createRef, useEffect, useState} from 'react';
import Select from "react-select";
import {DisplayPair, getDisplayPair} from "../../../../shared/shared-components/Inputs";
import {Form} from "react-bootstrap";
import {SelectPlaceholder} from "../../../helpers/EducaHelper";

function EducaContactEdit(props) {
    let [contact, setContact] = useState({
        name : "",
        email : "",
        role : "",
        location : "",
        telephone : "",
        roles : [],
        isMailAnonymized : false,
        cloudid : null
    })

    useEffect(() => {
        setContact(props.contact)
    }, [props.contact])

    useEffect(() => {
        props.setContact(contact)
    }, [contact])

    const users = props?.users??[]
    return <>
        <DisplayPair title={"Name"}>
            <input type={"text"} className={"form-control"}
                   value={contact?.name}
                   onChange={(evt) => setContact({...contact, name: evt.target.value})}
            />
        </DisplayPair>
        <DisplayPair title={"E-Mail"}>
            <input type={"email"} className={"form-control"}
                   value={contact?.email}
                   onChange={(evt) => setContact({...contact, email: evt.target.value})}
            />
        </DisplayPair>
        <DisplayPair title={"Position"}>
            <input type={"text"} className={"form-control"}
                   value={contact?.role}
                   onChange={(evt) => setContact({...contact, role: evt.target.value})}
            />
        </DisplayPair>
        <DisplayPair title={"Ort"}>
            <input type={"text"} className={"form-control"}
                   value={contact?.location}
                   onChange={(evt) => setContact({...contact, location: evt.target.value})}
            />
        </DisplayPair>
        <DisplayPair title={"Telefon"}>
            <input type={"text"} className={"form-control"}
                   value={contact?.telephone}
                   onChange={(evt) => setContact({...contact, telephone: evt.target.value})}
            />
        </DisplayPair>
        <DisplayPair title={"nur sichtbar für Nutzer*innen mit folgenden Rollen"}>
            <Select
                components={{SelectPlaceholder}}
                options={props.roles}
                value={contact?.roles}
                getOptionLabel={(option) => option.name}
                getOptionValue={(option) => option.id}
                isMulti={true}
                onChange={(roles) => {
                    setContact({...contact, roles: roles})
                    props.setSelectedRoles(roles)
                }}
            />
        </DisplayPair>
        <DisplayPair title={"Verlinkte Person"}>
            <Select
                isClearable={true}
                options={users}
                getOptionLabel={(option) => option.name}
                getOptionValue={(option) => option.id}
                value={users.find(s => s.id == contact?.cloudid)}
                includeCurrentUser={true}
                placeholder={"Person auswählen..."}
                onChange={(obj) => {
                    setContact({...contact, cloudid: obj?.id})
                }}
            />
        </DisplayPair>

        <DisplayPair title={"E-Mail anonym zustellen (Postkasten-Prinzip)"}>
            <Form.Check checked={contact?.isMailAnonymized} onClick={() => setContact({
                ...contact, isMailAnonymized:
                    !contact?.isMailAnonymized
            })} type="checkbox" label="E-Mail nur anonym zustellen"/>
        </DisplayPair>

    </>;
}

export default EducaContactEdit;
