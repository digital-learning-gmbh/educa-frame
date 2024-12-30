import React, {memo, useEffect, useState,useRef} from 'react';
import Sidebar from "react-sidebar";
import {DRAWER_DEFAULT_STYLES} from "../administration-react/AdministrationHelper";
import {Card, Container, ListGroup, Navbar, Row, Tab, Tabs} from "react-bootstrap";
import Button from "react-bootstrap/Button";
import EdbCopyModal from "./EDBCopyModal";
import {MODELS} from "../shared/shared-helpers/SharedHelper";
import AdministrationFileBrowser from "../administration-react/administration-components/AdminstrationFileBrowser";
import {getFormContainer} from "../shared/shared-helpers/KompostFabrik";
import baseDataJson from "../../configs/administration/test_data_default.json"
import Select from "react-select";
import {getDisplayPair} from "../shared/shared-components/Inputs";

function VedabEventEditorC(props) {


    let [currentTab, setCurrentTab] = useState("base")
    let [event, setEvent] = useState(null)
    let [selectedMenu, setSelectedMenu] = useState("general")


    const copyModalRef = useRef()

    useEffect(() =>
    {
        unmount()
        return () => unmount()
    },[])

    useEffect(() =>
    {
        setEvent(event)
    },[props.event])

    useEffect( () =>
    {
        setCurrentTab(0)
    },[selectedMenu])

    const unmount = () => {

        setCurrentTab("base")
        setEvent(null)

    }

    const getContent = () =>
    {
        if(selectedMenu == "general")
            return getGeneralContent()
        if( selectedMenu == "details" )
            return getDetailsContent()
        if( selectedMenu == "appointments" )
            return getAppointmentContent()
        if( selectedMenu == "documents" )
            return getDocumentsContent()
        if( selectedMenu == "participants" )
            return getParticipantsContent()
        if( selectedMenu == "workshops" )
            return getWorkshopsContent()
        return <div>Bitte wählen Sie ein Element aus dem Menü.</div>
    }

    const getGeneralContent = () =>
    {
        return <Tabs activeKey={currentTab} onSelect={ (k) => setCurrentTab(k)} defaultActiveKey={0}>
            <Tab title={<><i className="fas fa-database"></i> Grunddaten</>} eventKey={0}>
                <BaseData />
            </Tab>

            <Tab title={<><i className="fas fa-list"></i> Inhalt</>} eventKey={1}>
                <Content />
            </Tab>

        </Tabs>
    }


    const getDetailsContent = () =>
    {
        return <Tabs activeKey={currentTab} onSelect={ (k) => setCurrentTab(k)} defaultActiveKey={"detail"}>

            <Tab title={<><i className="fas fa-table"></i> Detailinformationen</>} eventKey={0}>
                <DetailInformation />
            </Tab>

            <Tab title={<><i className="fas fa-address-card"></i> Adressaten</>} eventKey={1}>
                <AddressData />
            </Tab>

            <Tab title={<><i className="fas fa-coins"></i> Finanzierung</>} eventKey={2}>
                <Financial />
            </Tab>

            <Tab title={<><i className="fas fa-check-double"></i> Genehmigung</>} eventKey={3}>
                <Approval />
            </Tab>
            <Tab title={<><i className="fas fa-bullhorn"></i> Veröffentlichung</>} eventKey={4}>
                <Publication />
            </Tab>

        </Tabs>
    }

    const getAppointmentContent = () =>
    {
        return <Tabs activeKey={currentTab} onSelect={ (k) => setCurrentTab(k)} defaultActiveKey={0}>
            <Tab title={<><i className="fas fa-calendar-check"></i> Bearbeitungstermine</>} eventKey={0}>
                <EditingAppointments />
            </Tab>

        </Tabs>
    }

    const getParticipantsContent = () =>
    {
        return <>
                <TeamAndCorporation />
            </>
    }

    const getDocumentsContent = () =>
    {
        return <div>
            <h5>Dokumente</h5>
            <AdministrationFileBrowser
                modelType={MODELS.STUDENT}
                modelId={1}
                canUserUpload={true}
                canUserEdit={true}
            />
        </div>
    }

    const getWorkshopsContent = () =>
    {
        return <Workshops />
    }


    const scaffold = !props.open? <></> : <div style={{backgroundColor : "#f2f3f5"}}>
        <Navbar variant="dark" style={{ backgroundColor: "#de003d", color: "#506690"}}>
            <div style={{display :"flex", flexDirection : "row",flex : 1,}}>
                <Navbar.Brand>{"Neue Veranstaltung"}</Navbar.Brand>
                <div style={{flex : 1, display :"flex", flexDirection : "row", justifyContent :"flex-end"}}>
                    <Button
                        style={{backgroundColor : "transparent"}}
                        onClick={() => props.closeCallback()}>
                        <i className={"fa fa-times"}/></Button></div>
            </div>
        </Navbar>

        <Container fluid={true} className={"mt-2"}>

        <Row>
            <Card
                bg={"light"}
                text={'dark'}
                style={{ width: '18rem' }}
                className="mb-2  ml-2"
            >
                <Card.Header><i className="fas fa-tasks"></i> Status</Card.Header>
                <Card.Body>
                    <Card.Title className={"text-center"}><b>In Planung</b></Card.Title>
                    <Card.Text className={"text-center"}>
                        Der nächste Status ist "ausgeschrieben".
                    </Card.Text>
                </Card.Body>
            </Card>

            <Card
                bg={"success"}
                text={'white'}
                style={{ width: '18rem' }}
                className="mb-2  ml-2"
            >
                <Card.Header><i className="fas fa-user-edit"></i> Bearbeiter</Card.Header>
                <Card.Body>
                    <Card.Title className={"text-center"}><b>Max Mustermann</b></Card.Title>
                    <Card.Text className={"text-center"}>
                        zugewiesen am { moment().format("DD.MM.YYYY HH:mm") }
                    </Card.Text>
                </Card.Body>
            </Card>

            <Card
                bg={"info"}
                text={'white'}
                style={{ width: '18rem' }}
                className="mb-2 ml-2"
            >
                <Card.Header><i className="fas fa-users"></i> Teilnehmer*innen</Card.Header>
                <Card.Body>
                    <Card.Title className={"text-center"}><b>10 / 100</b></Card.Title>
                    <Card.Text className={"text-center"}>
                        Anmeldeschluss am { moment().format("DD.MM.YYYY HH:mm") }
                    </Card.Text>
                </Card.Body>
            </Card>
            <div style={{display : "flex", flex  : 1, justifyContent :"flex-end"}}>
                <div className={"m-2"}>
                    <Button onClick={ () => copyModalRef?.current?.open()}><i className={"fas fa-copy"}/> Kopieren</Button>
                </div>

            </div>
        </Row>
    <Row>
        <div style={{display : "flex", flex : 1}}>
            <div className={"col col-2"}>
                <ListGroup variant="flush">
                    <ListGroup.Item active={selectedMenu == "general"}      style={{cursor :"pointer"}} onClick={() => setSelectedMenu("general")}><i className="fas fa-database"/>  Allgemein</ListGroup.Item>
                    <ListGroup.Item active={selectedMenu == "details"}      style={{cursor :"pointer"}} onClick={() => setSelectedMenu("details")}><i className="fas fa-list"/> Details</ListGroup.Item>
                    <ListGroup.Item active={selectedMenu == "appointments"} style={{cursor :"pointer"}} onClick={() => setSelectedMenu("appointments")}><i className="fas fa-calendar-alt"/> Termine</ListGroup.Item>
                    <ListGroup.Item active={selectedMenu == "participants"} style={{cursor :"pointer"}} onClick={() => setSelectedMenu("participants")}><i className="fas fa-users"/> Teilnehmer*innen</ListGroup.Item>
                    <ListGroup.Item active={selectedMenu == "documents"}    style={{cursor :"pointer"}} onClick={() => setSelectedMenu("documents")}><i className="fas fa-file-alt"/> Dokumente</ListGroup.Item>
                    <ListGroup.Item active={selectedMenu == "workshops"}    style={{cursor :"pointer"}} onClick={() => setSelectedMenu("workshops")}><i className="fas fa-star"/> Workshops</ListGroup.Item>
                </ListGroup>
            </div>

            <div className={"col col-10"}>
                {getContent()}
            </div>

        </div>
</Row>

            <EdbCopyModal ref={copyModalRef} />
        </Container>
    </div>

    return (
        <Sidebar
            sidebar={scaffold}
            cancelClickCallback={()=> props.closeCallback()}
            open={props.open}
            defaultOpen={false}
            styles={{...DRAWER_DEFAULT_STYLES, sidebar: {...DRAWER_DEFAULT_STYLES.sidebar, width: "90vw"}}}
        ><></></Sidebar>
    );
}


const EDBEventEditor = memo( (props) => <VedabEventEditorC {...props} />, (prev, next) => false)

export default EDBEventEditor;



const BaseData = memo( (props) =>
{
    let [data, setData] = useState({})

    useEffect(() =>
    {

    },[])

    return <div className={"d-flex flex-column bg-white"} style={{width : "750px"}}>
        <div className={"p-4"}>

            <div className={"m-2"}>
                {getFormContainer(baseDataJson.config,data, (key, value)=> {setData({...data, [key] : value})}, [], 750 )}
            </div>

            {getDisplayPair("Fortbildungsverantwortlich",
                <Select
                closeMenuOnSelect={false}
                isMulti={true}
                placeholder={"Personen..."}
                options={[{label : "Person 1", value :1},{label : "Person 2", value : 2} ]}

                onChange={(val) => {}}
            />) }

            {getDisplayPair("Fortbildungsverantwortlich - Vertretung",
                <Select
                closeMenuOnSelect={false}
                isMulti={true}
                placeholder={"Personen..."}
                options={[{label : "Person 1", value :1},{label : "Person 2", value : 2} ]}

                onChange={(val) => {}}
            />) }
        </div>
    </div>

}, (prev,next) => false )

const DetailInformation = memo( (props) =>
{

    useEffect(() =>
    {

    },[])

    return <div>

    </div>

}, (prev,next) => false )

const Content = memo( (props) =>
{

    useEffect(() =>
    {

    },[])

    return <div>

    </div>

}, (prev,next) => false )

const AddressData = memo( (props) =>
{

    useEffect(() =>
    {

    },[])

    return <div>

    </div>

}, (prev,next) => false )

const TeamAndCorporation = memo( (props) =>
{

    useEffect(() =>
    {

    },[])

    return <div>
        <h5>Team & Kooperation</h5>
    </div>

}, (prev,next) => false )

const Financial = memo( (props) =>
{

    useEffect(() =>
    {

    },[])

    return <div>

    </div>

}, (prev,next) => false )

const Workshops = memo( (props) =>
{

    useEffect(() =>
    {

    },[])

    return <div>
    <h5>Workshops</h5>
    </div>

}, (prev,next) => false )

const Approval = memo( (props) =>
{

    useEffect(() =>
    {

    },[])

    return <div>

    </div>

}, (prev,next) => false )

const EditingAppointments = memo( (props) =>
{

    useEffect(() =>
    {

    },[])

    return <div>

    </div>

}, (prev,next) => false )

const Publication = memo( (props) =>
{

    useEffect(() =>
    {

    },[])

    return <div>

    </div>

}, (prev,next) => false )

