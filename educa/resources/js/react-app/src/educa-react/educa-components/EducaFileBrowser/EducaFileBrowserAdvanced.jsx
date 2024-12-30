import React, {useEffect, useRef, useState} from 'react';
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedFileBrowser from "../../../shared/shared-components/SharedFileBrowser";
import {BROWSER_COLUMNS} from "react-keyed-file-browser-educa"
import {
    Badge,
    Breadcrumb,
    Card,
    Col,
    Container,
    FormControl,
    ListGroup,
    Nav,
    Navbar,
    NavDropdown
} from "react-bootstrap";
import Form from "react-bootstrap/Form";
import Button from "react-bootstrap/Button";
import ReactTabulator from "react-tabulator/lib/ReactTabulator.js";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper.js";
import EducaModal, {MODAL_BUTTONS} from "../../../shared/shared-components/EducaModal.js";
import SafeDeleteModal from "../../../shared/shared-components/SafeDeleteModal.js";
import {DisplayPair} from "../../../shared/shared-components/Inputs.js";
import CreateFolder from "./Dialogs/CreateFolder.jsx";
import UploadFile from "./Dialogs/UploadFile.jsx";
import {createRoot} from "react-dom/client";
import ReactTimeAgo from "react-time-ago";
import FileDetails from "./Details/FileDetails.jsx";
import {EducaLoading} from "../../../shared-local/Loading.js";
import EducaFileFolderPicker from "./Picker/EducaFileFolderPicker.jsx";
import {NameFormatter} from "./Formatter/NameFormatter.jsx";
import {TimeAgoFormatter} from "./Formatter/TimeAgoFormatter.jsx";
import Accordion from "react-bootstrap/Accordion";
import AIContent from "./Contents/AIContent";
import {useEducaLocalizedStrings, withEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper.js";

function EducaFileBrowserAdvanced({
    modelType,
    modelId,
    canUserUpload = false,
    canUserEdit = false,
    hasSidebar = false,
    hasAISearchbar = false,
    hasNavigationbar = false,
                                      hasSearchbar = false

                                  }) {

    let [content, setContent] = useState("fileBrowser");
    // dialogs
    let [showNewFolder, setShowNewFolder] = useState(false);
    let [showUploadFiles, setShowUploadFiles] = useState(false);

    let [ files, setFiles ] = useState([])
    let [ currentFiles, setCurrentFiles ] = useState([])
    let [ parentId, setParentId ] = useState(null)
    let [ breadcrumCache, setBreadcrumCache ] = useState([])
    let [ selectedFile, setSelectedFile ] = useState(null)
    let [ selectedRows, setSelectedRows ] = useState([])
    let [isLoading, setLoading] = useState(false)
    let [showPicker, setShowPicker] = useState(false)
    let [copy, setCopy] = useState(true)

    let [AIQuery, setAIQuery] = useState(null)
    let [aiResponse, setAiResponse] = useState(null)

    const tableRef = useRef()
    const educaModalRef = useRef();
    const safeDeleteModalRef = useRef();
    const [translate] = useEducaLocalizedStrings();
    const translatedTitle = translate("group_view.files", "Dateien")


    useEffect(() => {
        if(modelId && modelType)
            loadFiles()
    }, [modelId,modelType]);

    useEffect(() => {
        if(selectedFile != null)
        {
            setSelectedFile(files?.find(f => f.id == selectedFile?.id))
        }
    },[files])


    useEffect(() => {
        setCurrentFiles(files?.filter(file => file.parent_id == parentId))

        let folderStack = [];
        let currentParentId = parentId;
        while (currentParentId != null)
        {
            let parentFolder = files?.find(f => f.id === currentParentId)
            if(parentFolder == null)
            {
                currentParentId = null
            } else {
                currentParentId = parentFolder?.parent_id
            }
            folderStack.push(parentFolder)
        }
        setBreadcrumCache(folderStack.reverse())
    }, [files,parentId]);

    let loadFiles = () => {
        setLoading(true)
        AjaxHelper.getDocumentList(modelId, modelType)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.documents) {
                    setFiles(resp.payload.documents)
                    return
                }
                throw new Error("")

            })
            .catch(err => {
                SharedHelper.fireErrorToast("Fehler", "Die Dateien konnten nicht vom Server abgerufen werden.")
            }).finally(() => {
                setLoading(false)
        })
    }

    let rowDblClick = (e, row) => {
        let data = row.getData();
        if(data.type === "folder")
        {
            setParentId(data.id)
        } else {
            setSelectedFile(data)
        }
    }

    let rowSelectionChanged = (data, rows) => {
        setSelectedRows(data)
    }

    function humanFileSize(bytes, si=false, dp=1) {
        const thresh = si ? 1000 : 1024;

        if (Math.abs(bytes) < thresh) {
            return bytes + ' B';
        }

        const units = si
            ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
            : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
        let u = -1;
        const r = 10**dp;

        do {
            bytes /= thresh;
            ++u;
        } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);


        return bytes.toFixed(dp) + ' ' + units[u];
    }

    let deleteMulti = () => {
        let exec = () => {
            for (let i = 0; i <= selectedRows?.length; i++)
            {
                let file = selectedRows[i];
                if(!file)
                    continue;
                AjaxHelper.deleteDocument(file.id)
                    .then(resp => {
                        if (resp.status > 0) {
                            SharedHelper.fireSuccessToast("Erfolg", "Das Löschen der Datei '" + file.name +"' war erfolgreich.")
                            return loadFiles() // necessary because the filebrowser does not recognize updates somehow...
                        }
                        throw new Error(resp.message)
                    })
                    .catch((err) => {
                        SharedHelper.fireErrorToast("Fehler", "Löschen der Datei '" + file.name + "' fehlgeschlagen. " + err.message)
                    })
            }
            setSelectedRows([]);
            return null;
        }

        educaModalRef?.current?.open( (btn) => btn === MODAL_BUTTONS.YES? exec() : null, "Dateien löschen", "Möchtest du wirklich "+ selectedRows?.length + " Datei(en) löschen?.", [MODAL_BUTTONS.NO, MODAL_BUTTONS.YES]  )

    }

    let askAI = () => {
        setContent("askAI")
        setAiResponse(null)
        AjaxHelper.queryDocument(modelId, modelType, AIQuery)
            .then(resp => {
                if (resp.status > 0) {
                    setAiResponse(resp.payload)
                    return
                }
                throw new Error(resp.message)
            })
            .catch((err) => {
                SharedHelper.fireErrorToast("Fehler", "Die Frage an educa AI war nicht erfolgreich. " + err.message)
            })
    }

    let downloadViaZip = () => {
        setLoading(true)
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        AjaxHelper.downloadViaZIPDocument(selectedRows?.map(file => file.id))
            .then(response => response.blob())
            .then((response) => {
                var blob = new Blob([response], {type: "application/zip"});
                var url = window.URL.createObjectURL(blob);
                a.href = url;
                a.download = "dokumente.zip";
                a.click();
                window.URL.revokeObjectURL(url);
            }).catch( (err) => {
            console.log(err)
            SharedHelper.fireErrorToast("Fehler", "Die Download-Datei konnten nicht geladen werden.")
        } )
            .finally(() => setLoading(false))
    }

    let doCopyOrMove = (modelId, modelType, parentId) =>
    {
        setShowPicker(false)
        for (let i = 0; i <= selectedRows?.length; i++) {
            let file = selectedRows[i];
            if(!file)
                continue;

            AjaxHelper.moveOrCopyDocument(file.id, parentId, modelType, modelId, copy ? "copy": "move")
                .then(resp => {
                    if (resp.status > 0) {
                        SharedHelper.fireSuccessToast("Erfolg", "Das " + (!copy ? "Verschieben" : "Kopieren") + " der Datei '" + file.name +"' wurde erfolgreich ausgeführt.")
                        return loadFiles() // necessary because the filebrowser does not recognize updates somehow...
                    }
                    throw new Error(resp.message)
                })
                .catch((err) => {
                    SharedHelper.fireErrorToast("Fehler", "Das " + (!copy ? "Verschieben" : "Kopieren") + " der Datei '" + file.name + "' fehlgeschlagen. " + err.message)
                })
        }
    }


    const columns = [
        {formatter:"rowSelection", titleFormatter:"rowSelection", align:"center", headerSort:false, width: 20},
        { title: translate("group_view.file_name","Name"), field: 'name', formatter: NameFormatter },
        { title: translate("group_view.file_type","Typ"), field: 'type', formatter: (cell) => {
                return cell.getData()?.type === "folder" ? "Ordner" : cell.getData()?.file_type
            }, width: 150},
        { title: translate("group_view.file_last_change","Letzte Änderung"), field: 'updated_at', formatter: TimeAgoFormatter, width: 150 },
        { title: translate("group_view.file_owner","Ersteller"), field: 'creator.name', width: 150},
        { title: translate("group_view.file_size","Größe"), field: 'size', width:  100, formatter: (cell) => {
            return cell.getData()?.type === "folder" ? "" : humanFileSize(cell.getValue())
            }},
    ];

    const options =
        { movableColumns: true,
            placeholder: translate("group_view.no_files","Keine Dateien vorhanden"),
            printAsHtml: true,
            downloadDataFormatter: (data) => data,
            downloadReady: (fileContents, blob) => blob,
          //  selectable:true,
        };

    return <div>
        { hasNavigationbar ?
        <Navbar bg="light" expand="lg">
            <Navbar.Brand href="#home">{translatedTitle}</Navbar.Brand>
            <Navbar.Toggle aria-controls="basic-navbar-nav" />
            <Navbar.Collapse id="basic-navbar-nav">
                <Nav className="mr-auto">
                    {selectedRows?.length > 0 && canUserEdit ?   <NavDropdown title={<><i className="fas fa-pen"></i> Bearbeiten</>}
                                                               id="basic-nav-dropdown">
                        <NavDropdown.Item onClick={() => deleteMulti()}>Löschen</NavDropdown.Item>
                        <NavDropdown.Item onClick={() => { setCopy(false); setShowPicker(true) }}>Verschieben</NavDropdown.Item>
                        <NavDropdown.Item  onClick={() => { setCopy(true); setShowPicker(true) }}>Kopieren</NavDropdown.Item>
                        <NavDropdown.Item onClick={() => downloadViaZip()}>Herunterladen (als ZIP)</NavDropdown.Item>
                        </NavDropdown> : null }
                    {canUserUpload ? <Nav.Link onClick={() => setShowNewFolder(true)}><i class="fas fa-folder-plus"></i> Neue Ordner</Nav.Link> : null }
                    {canUserUpload ? <Nav.Link onClick={() => setShowUploadFiles(true)}><i class="fas fa-upload"></i> Hochladen</Nav.Link> : null }
                    <NavDropdown title={<><i className="fas fa-sort-amount-down-alt"></i> {translate("group_view.sort","Sortieren")}</>} id="basic-nav-dropdown">
                        <NavDropdown.Item onClick={() => {
                            tableRef?.current?.table.setSort([{
                                column : "name", dir: "asc"
                            }])
                        }}>Nach Name (aufsteigend)</NavDropdown.Item>
                        <NavDropdown.Item onClick={() => {
                            tableRef?.current?.table.setSort([{
                                column : "name", dir: "desc"
                            }])
                        }}>Nach Name (absteigend)</NavDropdown.Item>
                        <NavDropdown.Divider />
                        <NavDropdown.Item onClick={() => {
                            tableRef?.current?.table.setSort([{
                                column : "updated_at", dir: "asc"
                            }])
                        }}>Nach Änderungsdatum (aufsteigend)</NavDropdown.Item>
                        <NavDropdown.Item onClick={() => {
                            tableRef?.current?.table.setSort([{
                                column : "updated_at", dir: "desc"
                            }])
                        }}>Nach Änderungsdatum (absteigend)</NavDropdown.Item>
                    <NavDropdown.Divider />
                    <NavDropdown.Item onClick={() => {
                        tableRef?.current?.table.setSort([{
                            column : "type", dir: "asc"
                        }])
                    }}>Nach Typ (aufsteigend)</NavDropdown.Item>
                    <NavDropdown.Item onClick={() => {
                        tableRef?.current?.table.setSort([{
                            column : "type", dir: "desc"
                        }])
                    }}>Nach Typ (absteigend)</NavDropdown.Item>
                </NavDropdown>
                    <Nav.Link onClick={() => loadFiles()}><i className="fas fa-sync"></i> {translate("group_view.update","Aktualisieren")}</Nav.Link>
                </Nav>
                { hasAISearchbar ?  <div className={"d-flex"}>
                    { content == "askAI" ? <>
                        <Button onClick={() => {
                            setAIQuery(null)
                            setContent("fileBrowser")
                        }} variant="outline-danger"> Frage abbrechen</Button>
                        </> : <>
                    <FormControl onKeyDown={(event) => {
                        if (event.key === 'Enter') {
                            askAI()
                        }}} value={AIQuery} onChange={(evt) => setAIQuery(evt.target.value)} style={{maxWidth: "100%", width: "450px"}} type="text" placeholder="Dateien per educa AI befragen" className="mr-sm-2" />
                    <Button onClick={() => askAI()} variant="outline-info"><img style={{height: "20px"}} src="/images/educa_ai_loading_indicator.gif"/> educa AI fragen</Button></>}
                </div> : null }
                { hasSearchbar ?
                <Form inline>
                    <FormControl type="text" placeholder="Dateien durchsuchen" className="mr-sm-2" />
                    <Button variant="outline-success"><i class="fas fa-search"></i> Suchen</Button>
                </Form> : null }
            </Navbar.Collapse>
        </Navbar> : null }
        {
            content == "fileBrowser" ?
            <div className={"row"}>
            { hasSidebar ?
            <div className={"col-3"}>

            </div> : null }
            <div className={hasSidebar ? "col-9" : "col-12"}>
                <Breadcrumb listProps={{className: "mb-0 bg-white", style: { borderRadius: "0px"}}}>
                    <Breadcrumb.Item onClick={() => setParentId(null)}>Home</Breadcrumb.Item>
                    { breadcrumCache.map(folder => {
                        return <Breadcrumb.Item onClick={() => setParentId(folder?.id)}>
                            {folder?.name}
                        </Breadcrumb.Item>
                    })}
                </Breadcrumb>
                <div className={"d-flex bg-white"}>
                <div className={selectedFile ? "col-lg-8" : "col-12"}>
                    <ReactTabulator
                        ref={tableRef}
                        columns={columns}
                        data={currentFiles}
                        options={options}
                        events={{
                            rowClick: rowDblClick,
                            rowSelectionChanged:  rowSelectionChanged
                        }}
                        rowClick={rowDblClick}
                        rowSelectionChanged={rowSelectionChanged}
                    />
                    { isLoading ? <div style={{zIndex: 1000}}><EducaLoading/></div> : null }
                </div>
                    { selectedFile ? <div className={"border-left col-lg-4"}>
                        <FileDetails canUserEdit={canUserEdit} file={selectedFile} close={() => setSelectedFile(null)} reloadCallback={loadFiles} />
                    </div> : null}
                </div>
            </div>
        </div> : null }
        { content == "askAI" ? <Container><AIContent query={AIQuery} aiResponse={aiResponse}
        /></Container>  : null }
        <CreateFolder show={showNewFolder} onHide={() => setShowNewFolder(false)} modelId={modelId} modelType={modelType} parentId={parentId} reloadCallback={loadFiles} />
        <UploadFile show={showUploadFiles} onHide={() => setShowUploadFiles(false)} modelId={modelId} modelType={modelType} parentId={parentId} reloadCallback={loadFiles} />
        <EducaModal ref={educaModalRef} />
        <SafeDeleteModal ref={safeDeleteModalRef} />
        <EducaFileFolderPicker
            showPicker={showPicker}
            closeCallback={() => setShowPicker(false)}
            start_model_id={modelId}
            start_model_type={modelType} isGlobalPicker={true}
            selectCallback={(modelId, modelType, parentId) => doCopyOrMove(modelId,modelType,parentId)}
        />
    </div>

}

export default EducaFileBrowserAdvanced
