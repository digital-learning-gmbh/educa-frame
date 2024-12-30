import React, {useEffect, useRef, useState} from 'react';
import EducaFileBrowserAdvanced from "../EducaFileBrowserAdvanced.jsx";
import Modal from "react-bootstrap/Modal";
import Button from "react-bootstrap/Button";
import {Breadcrumb, ListGroup} from "react-bootstrap";
import ReactTabulator from "react-tabulator/lib/ReactTabulator";
import {NameFormatter} from "../Formatter/NameFormatter.jsx";
import {TimeAgoFormatter} from "../Formatter/TimeAgoFormatter.jsx";
import AjaxHelper from "../../../helpers/EducaAjaxHelper.js";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper.js";
import FliesentischZentralrat from "../../../FliesentischZentralrat.js";
import SideMenu from "../../SideMenu.js";
import {useSelector} from "react-redux";
import {GroupPageElement} from "./GroupPageElement.jsx";
import {EducaLoading} from "../../../../shared-local/Loading.js";


function EducaFileFolderPicker({
                                   showPicker = false,
                                   closeCallback = null,
                                   isGlobalPicker = false,
                                   start_model_type = null,
                                   start_model_id = null,
                                   onlyFolders = true,
                                   selectCallback = null
}) {

    let [model_id, setModelId] = useState(start_model_id)
    let [model_type, setModelType] = useState(start_model_id)

    let currentUser = useSelector(s => s.currentCloudUser)
    let [ files, setFiles ] = useState([])
    let [ currentFiles, setCurrentFiles ] = useState([])
    let [ parentId, setParentId ] = useState(null)
    let [ breadcrumCache, setBreadcrumCache ] = useState([])
    let [isLoading, setLoading] = useState(false)

    let [ selectedFile, setSelectedFile ] = useState(null)

    const tableRef = useRef()

    useEffect(() => {
        if(model_id && model_type)
            loadFiles()
    }, [model_id,model_type, showPicker]);

    useEffect(() => {
        setModelId(start_model_id)
        setModelType(start_model_type)
    },[start_model_id, start_model_type])

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
        AjaxHelper.getDocumentList(model_id, model_type)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.documents) {
                    setFiles(resp.payload.documents?.filter((document) => !(onlyFolders && document?.type !== "folder")))
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

    let selectFile = () =>  {
        selectCallback(model_id,model_type,parentId)
    }

    const columns = [
        { title: 'Name', field: 'name', formatter: NameFormatter },
        { title: 'Letzte Änderung', field: 'updated_at', formatter: TimeAgoFormatter, width: 150 },
        { title: 'Ersteller', field: 'creator.name', width: 150},
    ];

    const options =
        { movableColumns: true,
            placeholder: onlyFolders ? "Keine Unterordner vorhanden" : "Keine Dateien vorhanden",
            printAsHtml: true,
            downloadDataFormatter: (data) => data,
            downloadReady: (fileContents, blob) => blob,
        };


    let rowSelectionChanged = (e, row) => {
        let data = row.getData();
        if(data.type === "folder")
        {
            setParentId(data.id)
        } else {
            setSelectedFile(data)
        }
    }

    let changeModel = (model_type, model_id) => {
        setModelType(model_type)
        setModelId(model_id)
        setParentId(null)
    }

    let getGroupMenu = () => {
        let newMenu = currentUser?.groups?.
        map( grp => ({...grp, sections : grp.sections?.filter( (sect) => !!FliesentischZentralrat.sectionFilesUpload(sect) && !!sect?.section_group_apps?.find( app => app?.group_app?.type == "files"))}))
            .filter( grp => !!grp.sections?.length)
            .sort((a, b) => a?.name.localeCompare(b?.name))
            .map((group) => {
                return <GroupPageElement group={group} onClickCallback={changeModel} model_id={model_id} model_type={model_type} />
            })
        return  <ListGroup variant={"flush"}>
            {newMenu}
        </ListGroup>
    }

    return <Modal size={"xl"} show={showPicker} onHide={closeCallback??(() => {})}>
        <Modal.Header closeButton>
            <Modal.Title>Bitte wähle einen {onlyFolders? "Ordner" : "Dokument"}</Modal.Title>
        </Modal.Header>
        <Modal.Body><div className={"row"}>
            { isGlobalPicker ?
                <div className={"col-3 border-right"} style={{ maxHeight: "70vh", overflowY: "auto"}}>
                    <h5><b>Speicherort</b></h5>
                    {/*<h6><b>Favoriten</b></h6>*/}
                    <h6><b>Gruppen</b></h6>
                    {getGroupMenu()}
                </div> : null}
            <div className={isGlobalPicker ? "col-9" : "col-12"}>
                <Breadcrumb listProps={{className: "mb-0 bg-white", style: { borderRadius: "0px"}}}>
                    <Breadcrumb.Item onClick={() => setParentId(null)}>Home</Breadcrumb.Item>
                    { breadcrumCache.map(folder => {
                        return <Breadcrumb.Item onClick={() => setParentId(folder?.id)}>
                            {folder?.name}
                        </Breadcrumb.Item>
                    })}
                </Breadcrumb>
                <ReactTabulator
                    ref={tableRef}
                    columns={columns}
                    data={currentFiles}
                    options={options}
                    events={{
                        rowClick: rowSelectionChanged,
                    }}
                    rowClick={rowSelectionChanged}
                />
                { isLoading ? <div style={{zIndex: 1000}}><EducaLoading/></div> : null }
            </div>
        </div></Modal.Body>
        <Modal.Footer>
            <Button variant="secondary" onClick={closeCallback??(() => {})}>
                Abbrechen
            </Button>
            <Button variant="primary" onClick={selectFile}>
                { onlyFolders ? "Ordner wählen" : "Dokument auswählen" }
            </Button>
        </Modal.Footer>
    </Modal>
}


export default EducaFileFolderPicker
