import React, {memo, useEffect, useRef, useState} from 'react';
import {
    Alert,
    Button,
    Card,
    Col, Collapse,
    Container,
    Form,
    FormControl,
    ListGroup,
    Nav,
    Navbar,
    NavDropdown,
    Row
} from "react-bootstrap";
import {EDITOR_JS_I18N, EDITOR_JS_TOOLS} from "../../../shared-local/EditorJSTools";
import {useHistory, useLocation} from "react-router";
import { createReactEditorJS } from 'react-editor-js/dist/react-editor-js.cjs';
import {DisplayPair} from "../../../shared/shared-components/Inputs";
import { ReactTree } from '@naisutech/react-tree'
import EducaModal, {MODAL_BUTTONS} from "../../../shared/shared-components/EducaModal";
import Select from "react-select";
import {SideMenuHeadingStyle} from "../../educa-components/EducaStyles";
import {EducaCircularButton} from "../../../shared/shared-components";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";
import {EducaLoading} from "../../../shared-local/Loading";
import _ from "lodash";

const ReactEditorJS = createReactEditorJS()

export const pageToUrl = (page) => encodeURIComponent((page?.name+"-"+(page?.id??"")).replace(new RegExp(" ","g"),"-"))

function EducaWiki({
                        pathTrail,
                        canCreatePage,
                        canEdit,
                        canOpen,
                        modelId,
                        modelType,
                        menuText= "Wiki"
                   }) {

    const [loading, setLoading] = useState(false)
    const [pages, setPages] = useState(null)
    const [currentPage, setCurrentPage] = useState(null)
    const [newPage, setNewPage] = useState(null)
    const [searchValue, setSearchValue] = useState("")
    const [changeMetaDataViewOpen, setChangeMetaDataViewOpen] = useState(false)
    const [searchResult, setSearchResult] = useState(null)

    const history = useHistory()
    const location = useLocation()
    const editorRef = useRef()
    const educaModalRef = useRef()

    useEffect(() => {
        init()
    },[])

    useEffect(() => {
        if(!searchResult)
        {
            if(currentPage)
                editorRef?.current?.render(getCurrentPageContent())
        }
        else
        {
            editorRef?.current?.render({
                "time": 1659529135842,
                "blocks": searchResult?.length? searchResult : [{id : "00", type : "header", data : {text : "Keine Einträge gefunden."}}],
                "version": "2.23.2"
            })
        }

    },[searchResult])

    useEffect(() => {
        if(currentPage)
        {
            if(typeof editorRef?.current.render == "function")
                editorRef.current.render(getCurrentPageContent())
        }
    },[currentPage?.id])


    useEffect(() => {
        if(pages)
        {
            setNewPage(null)
            setCurrentPage(urlToPage())
            setChangeMetaDataViewOpen(false)
        }

    },[pages])

    useEffect(() => {
        if(pages)
        {
            setCurrentPage(urlToPage())
            setChangeMetaDataViewOpen(false)
        }
    },[location])

    /**
     *
     * Ajax
     */

    const init = () => {
        if(!canOpen)
        {
            setPages([])
            return setLoading(false)
        }
        setLoading(true)
        AjaxHelper.getWikiPages(modelType, modelId)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.pages !== null)
                    return  setPages(resp.payload.pages)
                throw new Error("")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Die Wiki-Seiten konnten nicht geladen werden. " + err)
            })
            .finally(() => {
                setLoading(false)
            })
    }

    const onSave = () =>{

        editorRef.current.save().then((outputData) => {

            AjaxHelper.updateWikiPage(modelType, modelId, currentPage.id, {...currentPage, content : outputData})
                .then(resp => {
                    if (resp.status > 0 && resp.payload && resp.payload.pages)
                    {
                        setPages(resp.payload.pages)
                        EducaHelper.fireSuccessToast("Erfolg", "Die Wiki-Seiten wurde gespeichert.")
                    }
                    else
                        throw new Error("")
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Die Wiki-Seiten konnten nicht gespeichert werden.")
                })
        }).catch((error) => {
            console.log('Saving failed: ', error)
        });
    }
    const onDelete = () => {
        AjaxHelper.deleteWikiPage(modelType, modelId, currentPage.id)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.pages)
                {
                    setPages(resp.payload.pages)
                    resetPath()
                    EducaHelper.fireSuccessToast("Erfolg", "Die Wiki-Seite wurde gelöscht.")
                }
                else
                    throw new Error("")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Die Wiki-Seite konnte nicht gelöscht werden.")
            })
            .finally(() => {
                init()
            })
    }

    const onCreateNewPage = () => {
        AjaxHelper.createWikiPage(modelType, modelId, newPage.name)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.pages)
                {
                    EducaHelper.fireSuccessToast("Erfolg", "Die Wiki-Seiten wurde erstellt.")
                    setPages(resp.payload.pages)
                    return setPage(resp.payload.newPage)
                }
                else
                    throw new Error("")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Die Wiki-Seiten konnten nicht geladen werden.")
            })
    }
    const onSearch = () =>{
        AjaxHelper.searchWikiPage(modelType, modelId, searchValue)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.searchResult)
                    return setSearchResult(resp.payload.searchResult)
                else
                    throw new Error("")
            })
            .catch(err => {
                console.log(err)
                EducaHelper.fireErrorToast("Fehler", "Die Suche war nicht erfolgreich.")
            })
    }


    const setNew = () => history.push({pathname : location.pathname?.replace(new RegExp("\\/"+pathTrail+".*"), "/"+pathTrail+"/new")})
    const resetPath = () => history.push({pathname : location.pathname?.replace(new RegExp("\\/"+pathTrail+".*"), "/"+pathTrail)})
    const setPage = (page) => history.push({pathname : location.pathname?.replace(new RegExp("\\/"+pathTrail+".*"), "/"+pathTrail+"/"+pageToUrl(page))})


    const urlToPage = () => {
        setNewPage(null)

        const pathname = location?.pathname??""
        let match = pathname?.match( new RegExp("("+(pathTrail?pathTrail+"\\/" : "")+"[^\\/]+$)"))
        match = encodeURIComponent((match?.length? match[0] : "").replace(pathTrail+"/","").replace(new RegExp(" ","g"),"-"))

        if(match == "new")
            return setNewPage({name : ""})
        if(!match)
        {
            if(pages?.length)
                return setPage(pages[0])
            return setNew()
        }

        for (let page of pages)
            if(match == pageToUrl(page))
                return page
        return resetPath()
    }

    const openChangeMetadata = () => setChangeMetaDataViewOpen(true)

    const getChildren = () => pages?.filter( page => page?.parentId && page?.parentId == currentPage?.id)

    const getCurrentPageContent = () => {
        let val
        try{
            val = JSON.parse(currentPage.content)
        }
        catch{}
        return val
    }

    const getNewPageContent = () =>
    {
        if(!canOpen && !pages?.length)
            return <Alert variant={"info"}>Keine Berechtigung</Alert>

        if(!canCreatePage)
            return <Alert variant={"warning"}>Keine Berechtigung.</Alert>

        return <div>
            <Card>
                <Card.Body className={"text-center"}>
                    <div className={"ml-auto mr-auto"} style={{maxWidth: "500px"}}>
                        <DisplayPair title={"Name der Seite"}>
                            <Form.Control value={newPage?.name??""}
                                          onChange={(e) => setNewPage({name : e.target.value})}
                                          type="text"
                                          placeholder={"Neue Seite"}/>
                            <Form.Text className="text-muted">
                                Dieser Name wird im Seiten-Verzeichnis angezeigt
                            </Form.Text>
                        </DisplayPair>
                        <Button onClick={() => {setNewPage(null); onCreateNewPage()}}
                                disabled={!newPage?.name} variant="primary">
                            Erstellen
                        </Button>
                    </div>
                </Card.Body>
            </Card>
        </div>
    }

    const deletePageClicked = () => {
        educaModalRef.current.open(
            btn => btn == MODAL_BUTTONS.YES? onDelete() : null,
            "Seite Löschen",
            "Soll die Seite und alle Unterseiten wirklich gelöscht werden?",
            [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
        )
    }

    const getPageView = () =>
    {
        if(!currentPage)
            return null

        let children = getChildren()

        return <>
            <Navbar bg="light" expand="lg">
            <Navbar.Brand>{searchResult? "Suchergebnisse" : currentPage?.name}</Navbar.Brand>
            <Navbar.Toggle aria-controls="basic-navbar-nav"/>
            <Navbar.Collapse id="basic-navbar-nav">
                <Nav className="mr-auto">
                    {canEdit && !searchResult?
                        <>
                            <Nav.Link onClick={() => onSave()}><i
                                className="fas fa-save"></i> Speichern</Nav.Link>
                            <NavDropdown title="Bearbeiten" id="basic-nav-dropdown">
                                <NavDropdown.Item
                                    onClick={() => deletePageClicked()}> <i className={"fas fa-trash"}/> Seite löschen
                                </NavDropdown.Item>
                                <NavDropdown.Item
                                    onClick={() => openChangeMetadata()}><i className={"fas fa-pencil-alt"}/> Seite bearbeiten
                                </NavDropdown.Item>
                            </NavDropdown> </>
                        : <></>}
                </Nav>
                <div className={"form form-inline"}>
                    {searchResult? <Button variant={"danger"} className={"mr-1"} onClick={() => {setSearchResult(null); setSearchValue("")}}>
                        <i className={"fas fa-times"}/> Suche beenden
                        </Button> : null}
                    <FormControl onKeyDown={event => event.key === "Enter"? (searchValue? onSearch() : setSearchResult(null) ): null}
                                 value={searchValue} type="text"
                                 onChange={(e) => setSearchValue(e.target.value)}
                                 placeholder="Durchsuchen.." className="mr-sm-2"/>
                    <Button variant="outline-success" onClick={() => (searchValue? onSearch() : setSearchResult(null) )}><i
                        className="fas fa-search"></i></Button>
                </div>
            </Navbar.Collapse>
        </Navbar>
            <Collapse in={changeMetaDataViewOpen}>
                <div>
                    {getChangeMetaDataView()}
                </div>
            </Collapse>
            {children?.length?
                <>
                <div style={{width : "100%", display :"flex", justifyContent : "center"}}>
                    <Card className={"mb-1 mt-1"} style={{width : "550px"}}>
                        <Card.Header>
                            <Card.Title>
                                Unterseiten
                            </Card.Title>
                        </Card.Header>
                        <Card.Body>
                                <ListGroup variant={"flush"}>
                                    {children?.map( (p,i) => {
                                        return <ListGroup.Item style={{cursor : "pointer"}} onClick={() => setPage(p)} key={i}>
                                            <div className={"d-flex"}>
                                                <div className={"d-flex align-items-center mr-1"}>
                                                    <img width={15} src={"/images/file.svg"}/>
                                                </div>
                                                <div>
                                                    {p?.name}
                                                </div>
                                            </div>
                                        </ListGroup.Item>
                                    })
                                    }
                                </ListGroup>
                        </Card.Body>
                    </Card>
                </div>
                </> : null}

            {newPage || currentPage? <Card>
                <Card.Body>
                    <ReactEditorJS
                        defaultValue={getCurrentPageContent()}
                        readOnly={!canEdit || !!searchResult}
                        tools={EDITOR_JS_TOOLS}
                        i18n={EDITOR_JS_I18N}
                        onInitialize={(instance) => editorRef.current = instance}
                    />
                </Card.Body>
            </Card> : null}
            </>
    }

    const getChangeMetaDataView = () =>
    {
        const noneObject = {id : null, name : <i>Keine</i>}
        return <div className={"d-flex flex-column mb-2 mt-2"}>
            <DisplayPair title={"Name"}>
                <Form.Control value={currentPage?.name??""}
                              onChange={(e) => setCurrentPage({...currentPage, name : e.target.value})}
                              type="text" placeholder="Seiten-Name.."/>
                <Form.Text className="text-muted">
                    Dieser Name wird im Seiten-Verzeichnis angezeigt
                </Form.Text>
            </DisplayPair>
            <DisplayPair title={"Übergeordnete Seite"}>
                <Select
                    styles={{
                        // Fixes the overlapping problem of the component
                        menu: provided => ({...provided, zIndex: 9999}),
                    }}
                    options={[ noneObject,...pages?.filter( p => p?.id != currentPage?.id && !getChildren()?.find(p2 => p2.id == p.id) )]}
                    getOptionLabel={(option) => option.name}
                    getOptionValue={(option) => option.id}
                    placeholder={"Übergeordnete Seite"}
                    value={!currentPage?.parentId? noneObject : pages?.find( p => p?.id == currentPage?.parentId)}
                    onChange={(val) => {
                        setCurrentPage({...currentPage, parentId : val?.id})
                    }}
                />
            </DisplayPair>
            {/*category*/}
            <div>
                <Button onClick={() => {setChangeMetaDataViewOpen(false); onSave()}}>
                    <i className={"fas fa-save"}/> Speichern
                </Button>
                <Button className={"ml-1"} variant={"danger"} onClick={() => setChangeMetaDataViewOpen(false)}>
                    <i className={"fas fa-times"}/> Schließen
                </Button>
            </div>
        </div>
    }



    const getListView = () => {
        if(!pages)
            return null

        return <>
            <div
                className={"mb-1"}
                style={SideMenuHeadingStyle}
            >
                {menuText} {canCreatePage? <EducaCircularButton
                style={{marginLeft: "5px"}}
                tooltip={"Neue Seite erstellen"}
                variant={"success"}
                onClick={() => setNew()}
                size={"small"}>
                <i className="fas fa-plus"></i>
            </EducaCircularButton> : null}
            </div>
            <Tree pages={pages}
                     currentPage={currentPage}
                     newPage={newPage}
                     setNew={setNew}
                     setPage={setPage}
                     setSearchResult={setSearchResult}
                     setCurrentPage={setCurrentPage}/>
        </>
    }

    if(loading)
        return <EducaLoading/>

    if(!canOpen && !pages?.length)
        return <Alert variant={"info"}>Keine Berechtigung</Alert>

    return  <Container fluid={true} className="gedf-wrapper">
        <Row>
            <Col xs={2}>
                {getListView()}
            </Col>
            <Col xs={10}>
                {newPage? getNewPageContent() : getPageView()}
            </Col>
        </Row>
        <EducaModal ref={educaModalRef}/>
    </Container>

}

export default EducaWiki;

const Tree = memo( ({pages, currentPage, newPage, setPage, setNew, setCurrentPage, setSearchResult}) => {

    const flatNodes = [...pages??[], ...(!pages?.length? [{ id : -1, type : "new", name : "Neue Seite", parentId : null}] : [])]
    const nodes = flatNodes//buildNodesTree()

    const hasChild = (id) => !!nodes?.find( node => node?.parentId == id)

    return <>
        <ReactTree
            containerStyles={{}}
            enableIndicatorAnimations={false}
            enableItemAnimations={true}
            selectedNodes={[currentPage?.id, (newPage? -1 : null)].filter(e => !!e)}
            defaultOpenNodes={flatNodes?.map( e => e.id)}
            openNodes={flatNodes?.map( e => e.id)/*remove me for collapsible component*/}
            messages={{
                emptyItems: '[Leer]',
                loading: 'Lade...',
                noData: 'Keine Daten vorhanden.'
            }}
            RenderNode={({   node,
                             type,
                             selected = false,
                             open = false,
                             context}) => {
                return <div style={{
                    padding: 0,
                    margin: 0,
                    color: "#333",
                    overflowX: "hidden",
                    textOverflow: "ellipsis",
                    whiteSpace: "nowrap",
                }}>
                    {node.name}
                </div>

            }}
            RenderIcon={({   node,
                             type,
                             selected = false,
                             open = false,
                             context}) => {
                const hasChildren = hasChild(node?.id)
              //  if(node?.id == -1)
             //       return <img width={17} src={"/images/plus.svg"}/>
                if(hasChildren)
                {
                    if(open)
                        return <img style={{transform : "rotate(90deg)"}} width={12} src={"/images/chevr-right.svg"}/>
                    else
                        return <img width={12} src={"/images/chevr-right.svg"}/>
                }
                return <img width={15} src={"/images/file.svg"}/>
            }}
            truncateLongText={true}
            onToggleSelectedNodes={(ids) => {
                if(!ids?.length || ids[0] == currentPage?.id)
                    return
                setSearchResult(null)
                if(ids[0] == -1)
                    return setNew()
                const page = pages?.find(p => p.id == ids[0])
                if(page)
                {
                    setCurrentPage(null) // prevent infinite loop
                    setPage(page)
                }
            }}
            nodes={nodes}  />
    </>
}, (prev,next) => {
    return prev?.currentPage?.id == next?.currentPage?.id && _.isEqual(prev.pages, next.pages)
})
