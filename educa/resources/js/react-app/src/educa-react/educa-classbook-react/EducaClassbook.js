import React, {useEffect, useRef, useState} from 'react';
import AjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper, {MODELS} from "../../shared/shared-helpers/SharedHelper";
import AutoFormBuilderUnmanaged from "../../shared/shared-helpers/educa-form-builder/AutoFormBuilderUnmanaged";
import {validateForm} from "../../shared/shared-helpers/educa-form-builder/educa-form-helpers";
import Button from "react-bootstrap/Button";
import {Accordion, Card, Collapse, Row} from "react-bootstrap";
import {useSelector} from "react-redux";
import EducaHelper from "../helpers/EducaHelper";
import {getDisplayPair} from "../../shared/shared-components/Inputs";
import moment from "moment";
import EducaRadioSelect from "../../shared/shared-components/EducaRadioSelect";
import {EducaCircularButton} from "../../shared/shared-components/Buttons";
import TextareaAutosize from "react-textarea-autosize";
import {EducaDefaultTable} from "../../shared/shared-components/Tables";
import EducaSendMailsModal from "../educa-components/EducaSendMailsModal";

function EducaClassbook(props) {


    let [data, setData] = useState([])
    let [template, setTemplate] = useState([])
    let [unanswered, setUnanswered] = useState([])
    let [classbookInfo, setClassbookInfo] = useState({})

    let [members, setMembers] = useState([])
    let [isMeetingLoading, setIsMeetingLoading] = useState(false)
    let [activeAccordionKey, setActiveAccordionKey] = useState(null)
    let [meeting, setMeeting] = useState(null)
    let [records, setRecords] = useState([])

    const sendMailsModalRef = useRef()
    const currentCloudUser = useSelector( s => s.currentCloudUser)
    const isTeacher = !!currentCloudUser?.teacher?.id

    const uid = props.event?.unique_id
    const id = props.event?.id
    const type = props.event?.type
    const start = moment( props.event.start).unix()
    const end = moment( props.event.end).unix()

    useEffect(() =>
    {
        getClassbookInfo()
    },[props.event])

    const save = () =>
    {
        if(!isTeacher)
            return

        let arr =  validateForm(template, data)
        setUnanswered(arr)
        if(arr?.length > 0)
            return setActiveAccordionKey("attendance")
        console.log(props.event)
        AjaxHelper.saveClassBookEntry(
            uid,
            id,
            type,
            classbookInfo?.form_revision_id,
            data,
            members,
            moment(props.event.start).unix(),
            moment(props.event.end).unix(),
        )
            .then( resp =>
            {
                if(resp.status > 0)
                    return SharedHelper.fireSuccessToast("Erfolg", "Der Eintrag wurde erfolgreich gespeichert.")
                throw new Error(resp.message)
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Speichern war nicht erfolgreich.")
            })
    }

    const joinMeeting = () => {
        setIsMeetingLoading(true)
        AjaxHelper.joinMeeting(type, uid)
            .then(resp => {
                if (!resp.payload?.url)
                    throw new Error(resp.message)
                window.open(resp.payload.url)
                getClassbookInfo()
            })
            .catch(err => {

                EducaHelper.fireErrorToast("Fehler", "Meeting konnte nicht gestartet werden. " + err.message)
            })
            .finally(() => {
                setIsMeetingLoading(false)
            })
    }


    const getClassbookInfo = () =>
    {
        if(!props.event)
            return


        if(!uid || !id || !type)
            return SharedHelper.fireErrorToast("Fehler", "Kursbuch konnte nicht geladen werden.")

        AjaxHelper.getClassBookInfo(uid, type, id)
            .then( resp =>
            {
                if(resp.status > 0 && resp.payload?.form )
                {
                    setTemplate(JSON.parse(resp.payload.form.data))
                    setData(resp.payload?.form_data )
                    setActiveAccordionKey("attendance")
                    setClassbookInfo(resp.payload)
                    return
                }
                throw new Error(resp.message)
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Kursbuch konnte nicht verarbeitet werden.")
            })


        // AjaxHelper.infoMeeting(type, uid)
        //     .then( resp =>
        //     {
        //         if(resp.status > 0 )
        //         {
        //             setMeeting(resp.payload?.meeting)
        //             setRecords(resp.payload?.recordings)
        //             return
        //         }
        //         throw new Error(resp.message)
        //     })
        //     .catch( err =>
        //     {
        //         SharedHelper.fireErrorToast("Fehler", "Meeting-Infos konnte nicht verarbeitet werden.")
        //     })

        if(!isTeacher)
            return

        AjaxHelper.getClassBookMembers(undefined, uid, type, id,start, end )
            .then( resp =>
            {
                if(resp.status > 0 && resp.payload?.members )
                {
                    setMembers(resp.payload.members)
                    return
                }
                throw new Error(resp.message)
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Kursbuch konnte nicht verarbeitet werden.")
            })
    }

    const columns = [
        { title: 'Start', field: 'startTime' },
        { title: 'Ende', field: 'endTime' },
        { title: 'Wiedergabe', field: 'url_button' , formatter: 'html'},
    ];

    const options =
        { movableColumns: true,
            movableRows: true,
            placeholder:" Keine Aufzeichnungen vorhanden"
        };


    const LESSON_TYPES =
        {
            PRESENCE : "presence",
            ONLINE : "online",
            HYBRID : "hybrid",
        }

    const createCorrespondence = () =>
    {
        if(props.event.klassen_id?.length > 0)
            return sendMailsModalRef?.current?.open(MODELS.COURSE, props.event.klassen_id)
        SharedHelper.fireErrorToast("Fehler", "Keine Planungsgruppen verfügbar.")
    }

    const getClassBookContent = () => {

        let form = <AutoFormBuilderUnmanaged
            cols={1}
            template={template}
            formData={data}
            unanswered={unanswered}
            readOnly={!isTeacher}
            onFormDataChanged={(d) => setData(d)}
        />

        let memberstable = null


        if(members?.length > 0)
        {
            memberstable = <EducaClassbookMembersTable
                eventUniqueId={uid}
                setMembers={setMembers}
                members={members}
            />
        }
     //   console.log(props);

        return <>
            {props.event? <Card>
                <Card.Header>
                    Unterrichtsdetails
                </Card.Header>
                <Card.Body>
                    <Row>
                        <div className={"col-6"}>
                            {getDisplayPair("Datum", props.event?.start? moment(props.event?.start).locale("de").format("DD.MM.YYYY (dddd)") : <i>Unbekannt</i>)}
                            {getDisplayPair("Zeitraum", props.event?.start? moment(props.event?.start).format("HH:mm") + " - " + moment(props.event?.end).format("HH:mm") : <i>Unbekannt</i>)}
                            {getDisplayPair("Kommentar", props.event?.description ? props.event?.description : <></>)}
                            {isTeacher? <Button onClick={() => createCorrespondence()}> <i className={"fas fa-envelope"}/> Neue Rundmail</Button> : null}
                        </div>

                        <div className={"col-6"}>
                            {getDisplayPair("Planungsgruppe(n)", props.event?.klassen_name? props.event?.klassen_name.join(", ") : <i>Keine Planungsgruppe(n)</i>)}
                            {getDisplayPair("Fach", (props.event?.fach || props.event?.fach_abk)? props.event?.fach || props.event?.fach_abk : <i>Kein Fach</i>)}
                            {getDisplayPair("Räume", props.event?.raum? props.event?.raum : <i>Keine Räume</i>)}
                            {getDisplayPair("Dozent*innen", props.event?.dozent? props.event?.dozent : <i>Keine Dozent*in</i>)}
                            {getDisplayPair("Untertitel", props.event?.subtitle? props.event?.subtitle : <></>)}
                            {getDisplayPair("Unterrichtsform", props.event?.merkmal? (props.event?.merkmal["lessonType"] === "presence" ? "Präsenz" : (props.event?.merkmal["lessonType"] === "hybrid" ? "Hybrid" : "Online")): <></>)}
                        </div>
                    </Row>
                </Card.Body>
            </Card> : null}
            <Accordion activeKey={activeAccordionKey} onSelect={(e) => setActiveAccordionKey(e)}>
                {memberstable? <Card>
                    <Card.Header>
                        <Accordion.Toggle as={Button} variant="link" eventKey="attendance">
                            {activeAccordionKey === "attendance"? <i className={"fa fa-chevron-down"}/> : <i className={"fa fa-chevron-right"}/>} Anwesenheit
                        </Accordion.Toggle>
                    </Card.Header>

                    <Accordion.Collapse eventKey="attendance"  unmountOnExit={false}>
                        <Card.Body>
                            {memberstable}
                        </Card.Body>
                    </Accordion.Collapse>
                    <EducaSendMailsModal forwardRef={sendMailsModalRef}/>
                </Card> : null}
                {form? <Card>
                    <Card.Header>
                        <Accordion.Toggle as={Button} variant="link" eventKey="form">
                            {activeAccordionKey === "form"? <i className={"fa fa-chevron-down"}/> : <i className={"fa fa-chevron-right"}/>} Kursbucheintrag
                        </Accordion.Toggle>
                    </Card.Header>

                    <Accordion.Collapse eventKey="form" unmountOnExit={false}>
                        <Card.Body>
                            {form}
                        </Card.Body>
                    </Accordion.Collapse>
                </Card> : null}


                {/*<Card>*/}
                {/*    <Card.Header>*/}
                {/*        <Accordion.Toggle as={Button} variant="link" eventKey="meeting">*/}
                {/*            {activeAccordionKey === "meeting"? <i className={"fa fa-chevron-down"}/> : <i className={"fa fa-chevron-right"}/>} Meeting-Informationen*/}
                {/*        </Accordion.Toggle>*/}
                {/*    </Card.Header>*/}

                {/*    <Accordion.Collapse eventKey="meeting"  unmountOnExit={false}>*/}
                {/*        <Card.Body>*/}
                {/*            <MeetingInformation*/}
                {/*                model_type={type}*/}
                {/*                model_id={uid}*/}
                {/*            />*/}
                {/*        </Card.Body>*/}
                {/*    </Accordion.Collapse>*/}

                {/*</Card>*/}

                {/*<Card>*/}
                {/*    <Card.Header>*/}
                {/*        <Accordion.Toggle as={Button} variant="link" eventKey="recording">*/}
                {/*            {activeAccordionKey === "recording"? <i className={"fa fa-chevron-down"}/> : <i className={"fa fa-chevron-right"}/>} Aufzeichnungen*/}
                {/*        </Accordion.Toggle>*/}
                {/*    </Card.Header>*/}

                {/*    <Accordion.Collapse eventKey="recording"  unmountOnExit={false}>*/}
                {/*        <Card.Body>*/}
                {/*            <p>Folgende Aufzeichnungen des digitalen Unterrichts sind zu dieser Sitzung vorhanden:</p>*/}
                {/*            <ReactTabulator*/}
                {/*                columns={columns}*/}
                {/*                data={records}*/}
                {/*                options={options}*/}
                {/*            />*/}
                {/*        </Card.Body>*/}
                {/*    </Accordion.Collapse>*/}

                {/*</Card>*/}
            </Accordion>
        </>
    }

    return (
        <Card style={{backgroundColor: "white"}}>
            <Card.Header>
                <div style={{flex : 1, display : "flex", flexDirection :"row"}}>
                    <h5 className="card-title">
                        <b><i className="fas fa-pencil-alt"></i> Eintrag: {props.event?.fach_abk }</b>
                    </h5>
                    <div style={{flex : 1, display : "flex", flexDirection :"row", justifyContent :"flex-end"}}>

                        {isTeacher? <Button
                            variant={"primary"}
                            onClick={()=> save()}
                        >Speichern
                        </Button> : null}
                        {props.closeCallback? <Button variant={"secondary"} className={"ml-2"} onClick={() => props.closeCallback()}>
                            Schließen
                        </Button> : null}
                        {/*id ? <Button
                            className="btn btn-success ml-2"
                            onClick={() => joinMeeting()}
                        >
                            <div style={{display: "flex", flexDirection: "row"}}>
                                Meeting
                                <div style={{
                                    display: "flex",
                                    marginLeft: "2px",
                                    flex: "1 1 0%",
                                    flexDirection: "column",
                                    justifyContent: "center"
                                }}>
                                    {isMeetingLoading ? <Spinner
                                        as="span"
                                        animation="grow"
                                        size="sm"
                                        role="status"
                                        aria-hidden="true"
                                    /> : <i className="fas fa-external-link-alt"></i>}
                                </div></div>
                        </Button> : null*/}
                    </div>
                </div>
            </Card.Header>
            <div>
                {getClassBookContent()}
            </div>
        </Card>

    );
}

const EducaClassbookMembersTable = (props) =>
{
    const members = props.members? props.members : []

    const changeMember = ( id, newObj) =>
    {
        let i = members?.findIndex( m => m.id == id)
        if(!(i >= 0) )
            return
        let membersNew = members.concat([]) // deep copy
        membersNew[i] = newObj
        props.setMembers(membersNew)
    }

    const print = () =>
    {
       AjaxHelper.generateReportTemplate(5,props.eventUniqueId,"lessonPlan")
           .then( resp =>
           {
                if(resp.payload?.download_cache)
                {
                    let downloadToken = resp.payload?.download_cache?.token;
                    return window.open(  AjaxHelper.getReportTemplateUrl(downloadToken),"_blank").focus();
                }
                throw new Error()
           })
           .catch( err =>
           {
                SharedHelper.fireErrorToast("Fehler", "Das Dokument konnte nicht generiert werden. "+err?.message)
           })
    }

    return <EducaClassbookTableMemo
        printCallback={() => print()}
        data={members?.map( s => {
            const TempTextArea = p => {
                let [open, setOpen] = useState(s.notes? true : false)
                let [text, setText] = useState(s.notes?s.notes : "")
                return <div style={{display: "flex", flex: 1, flexDirection: "column"}}>
                    <div style={{display: "flex", flex: 1, flexDirection: "row"}}>
                        <div style={{width : "100%"}}>
                            <EducaRadioSelect
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
                actions: <div>
                    <TempTextArea/>
                </div>
            }
        })}
        dataForMemo={members?.map( s => s)}
    />
}



const EducaClassbookTableMemo = React.memo( (props) =>
{
    return <EducaDefaultTable
        size={"lg"}
        globalFilter={true}
        pagination={true}
        customButtonBarComponents={[
            <Button
                onClick={()=> props.printCallback()}
                variant={"primary"}>
            <i className={"fas fa-print"}/> Drucken
        </Button>]}
        filename={"anwesenheit_"}
        columns={ [{Header: "Nachname", accessor: 'lastname',  filter : true, width : "25"},
            { Header: 'Vorname', accessor: 'firstname', filter : true, width : "25"},
            { Header: '', accessor: 'actions',  filter : false, width : "50" },
        ]}
        data={props.data}/>
}, (prev, next) => {

    return JSON.stringify(prev.dataForMemo) ===  JSON.stringify(next.dataForMemo)
})


export default EducaClassbook;
