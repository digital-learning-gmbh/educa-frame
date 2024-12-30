import React, {useState} from "react";
import Select from "react-select";
import {EducaCircularButton} from "./Buttons";
import {Collapse} from "react-bootstrap";
import TextareaAutosize from "react-textarea-autosize";
import {EducaDefaultTable} from "./Tables";
import {SelectPlaceholder} from "../../administration-react/administration-components/Selects";
import EducaRadioSelect from "./EducaRadioSelect";

export const ClassbookMembersTable = (props) =>
{
    const members = props.members? props.members : []

    const SelectComponent = props.useRadioSelect? EducaRadioSelect : Select

    const changeMember = ( id, newObj) =>
    {
        let i = members?.findIndex( m => m.id == id)
        if(!(i >= 0) )
            return
        let membersNew = members.concat([]) // deep copy
        membersNew[i] = newObj
        props.setMembers(membersNew)
    }

    return <TableMemo
        data={members?.map( s => {
            const TempTextArea = p => {
                let [open, setOpen] = useState(s.notes? true : false)
                let [text, setText] = useState(s.notes?s.notes : "")
                return <div style={{display: "flex", flex: 1, flexDirection: "column"}}>
                    <div style={{display: "flex", flex: 1, flexDirection: "row"}}>
                        <div style={{width : "100%"}}>
                            <SelectComponent
                                components={{ SelectPlaceholder }}
                                menuPortalTarget={document.body}
                                menuPlacement="auto"
                                placeholder={"Anwesenheit"}
                                getOptionLabel={(option) => option.name}
                                getOptionValue={(option) => option.id}
                                options={s.options}
                                onChange={(opt) => {
                                    changeMember(s.id, {...s, selected: opt})
                                }}
                                value={s.selected}
                            />
                        </div>
                        <div>
                            <EducaCircularButton
                                className={"ml-2"}
                                size={"small"}
                                onClick={() => setOpen(!open)}
                            >
                                <i className={open ? "fas fa-chevron-down" : "fas fa-chevron-right"}/>
                            </EducaCircularButton>
                        </div>
                    </div>
                    <Collapse in={open} unmountOnExit={true}>
                        <TextareaAutosize
                            minRows={3}
                            className={"form-control mt-2"}
                            style={{width: "100%", fontSize: "75%"}}
                            onBlur={() => changeMember(s.id, {...s, notes: text})}
                            value={text}
                            onChange={(evt) => {
                                setText(evt.target.value)
                            }}
                        />
                    </Collapse>
                </div>
            }

            return {
                ...s,
                action: <div>
                    <TempTextArea/>
                </div>
            }
        })}
        dataForMemo={members?.map( s => s)}
    />
}



const TableMemo = React.memo( (props) =>
{
    return <EducaDefaultTable
        size={"lg"}
        globalFilter={true}
        pagination={true}
        customButtonBarComponents={[]}
        filename={"anwesenheit_"}
        columns={ [{Header: "Nachname", accessor: 'lastname',  filter : true, width : "25"},
            { Header: 'Vorname', accessor: 'firstname', filter : true, width : "25"},
            { Header: '', accessor: 'action',  filter : false, width : "50" },
        ]}
        data={props.data}/>
}, (prev, next) => {

    return JSON.stringify(prev.dataForMemo) ===  JSON.stringify(next.dataForMemo)
})
