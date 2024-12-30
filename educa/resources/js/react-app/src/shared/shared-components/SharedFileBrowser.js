import React, {Component, useCallback, useEffect, useRef, useState} from 'react';
import FileBrowser from 'react-keyed-file-browser-educa/dist/react-keyed-file-browser'
import "react-keyed-file-browser-educa/dist/react-keyed-file-browser.css"
import Button from "react-bootstrap/Button";
import ReactSwitch from "react-switch";
import EducaModal, {MODAL_BUTTONS} from "./EducaModal";
import SharedHelper from "../shared-helpers/SharedHelper";
import PropTypes from "prop-types";
import Modal from "react-bootstrap/Modal";
import {getDisplayPair} from "./Inputs";
import {ProgressBar} from "react-bootstrap";
import PdfViewer from "./PdfViewer"; //    pdf-dist@2.2.228

const FILE_TYPES =
    {
        FOLDER: "folder",
        FILE: "file"
    }


const MAX_FILES_DROP = 10

// Specifies the default values for props:
SharedFileBrowser.defaultProps = {
    canUserUpload: false,
    canUserEdit : false,
    canUserMultiUpload : true,

    canUserPreview : true,
    canUserCreateFolder : true,
    canUserDownload : true,
    canUserDelete: true,

    //Standard File Upload Icon als default
    fileUploadIcon: <i className="far fa-file-alt" aria-hidden="true"/>
};

SharedFileBrowser.propTypes =
    {
        modelId: PropTypes.number.isRequired,
        modelType : PropTypes.string.isRequired,
        columns : PropTypes.array,

        canUserUpload : PropTypes.bool,
        canUserEdit : PropTypes.bool,
        canUserMultiUpload : PropTypes.bool,

        canUserPreview : PropTypes.bool,
        canUserCreateFolder : PropTypes.bool,
        canUserDownload : PropTypes.bool,
        canUserDelete : PropTypes.bool,

        ajaxUploadUrl : PropTypes.string.isRequired,
        //Ajax Calls
        ajaxGetDocumentList : PropTypes.func.isRequired,
        ajaxCreateDocument : PropTypes.func.isRequired,
        ajaxCreateDocumentFolder : PropTypes.func.isRequired,
        ajaxMoveDocument : PropTypes.func.isRequired,
        ajaxRenameDocument : PropTypes.func.isRequired,
        ajaxDeleteDocument : PropTypes.func.isRequired,
        ajaxDownloadDocumentUrl : PropTypes.func.isRequired,
        ajaxOpenDocumentUrl :  PropTypes.func.isRequired,

        //File Upload Icon - BBW hat custom icon
        fileUploadIcon : PropTypes.element
}


function SharedFileBrowser(props) {

    const ICONS =
        {
            File: props.fileUploadIcon,
            Image: <i className="far fa-file-image" aria-hidden="true"></i>,
            Video: <i className="far fa-file-video" aria-hidden="true"></i>,
            PDF: <i className="far fa-file-pdf" aria-hidden="true"/>,
            Rename: <i className="far fa-edit" aria-hidden="true"/>,
            Folder: <i className="far fa-folder" aria-hidden="true"/>,
            FolderOpen: <i className="far fa-folder-open" aria-hidden="true"/>,
            Delete: <i className="far fa-trash-alt" aria-hidden="true"/>,
            Loading: <i className="fas fa-spinner" aria-hidden="true"/>,
            Download: <i className="fas fa-download" aria-hidden="true"/>,
            ExternalViewer: <i className="fas fa-external-link-alt"/>,
        }

    let [documents, setDocuments] = useState([])
    let [modelId, setModelId] = useState(props.modelId)
    let [modelType, setModelType] = useState(props.modelType)
    let [editingMode, setEditingMode] = useState(false)

    let [lastUploadSelectionKey, setLastUploadSelectionKey] = useState(null)

    const canUserEdit = !!props.canUserEdit
    const canUserUpload = !!props.canUserUpload
    const canUserDelete = !!props.canUserDelete

    const modalRef = useRef()
    const pdfModalRef = useRef()
    const fileUploadModalRef = useRef()
    const uploadInputRef = useRef()

    //componentDidMount
    useEffect(() => {
        loadFiles()
    }, [])

    //Listen for props changes
    useEffect(() => {
        if (props.modelId !== modelId)
            setModelId(props.modelId)
        if (props.modelType !== modelType)
            setModelType(props.modelType)
    }, [props.modelId, props.modelType])

    //Realod if id changed
    useEffect(() => {
        loadFiles()
    }, [modelId])

    /**
     * AJAX
     */
    const ajaxGetDocumentList = (modelId, modelType, withPath ) =>
    {
        return props.ajaxGetDocumentList(modelId, modelType, withPath )
    }

    const ajaxCreateDocument = (modelId, modelType, parent_id, file) =>
    {
        return props.ajaxCreateDocument(modelId, modelType, parent_id, file)
    }

    const ajaxCreateDocumentFolder = (modelId, modelType, parent_id, foldername) =>
    {
        return props.ajaxCreateDocumentFolder(modelId, modelType, parent_id, foldername)
    }

    const ajaxMoveDocument = (documentId, newParentId) =>
    {
        return props.ajaxMoveDocument(documentId, newParentId)
    }

    const ajaxRenameDocument = (documentId, newName) =>
    {
        return props.ajaxRenameDocument(documentId, newName)
    }

    const ajaxDeleteDocument = (documentId) =>
    {
        return props.ajaxDeleteDocument(documentId)
    }

    const ajaxDownloadDocumentUrl = (documentId, documentHash) =>
    {
        return props. ajaxDownloadDocumentUrl(documentId, documentHash)
    }

    const ajaxOpenDocumentUrl = (documentId, documentHash) =>
    {
        return props.ajaxOpenDocumentUrl(documentId, documentHash)
    }

    /**
     * Util
     */
        // Converts document from DB to a valid object for keyed file browser
    let convertDocument = (document) => {
            return {
                ...document,
                key: document.type === FILE_TYPES.FOLDER ? document.parent_key + "/" : document.parent_key,
                size: document.size,
                modified: +moment(document.updated_at).toDate()
            }
        }

    let loadFiles = () => {

        ajaxGetDocumentList(modelId, modelType)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.documents) {

                    let docs = []
                    resp.payload.documents.forEach(doc => {
                        docs.push(convertDocument(doc))
                    })

                    setDocuments(docs)
                    return
                }
                throw new Error("")

            })
            .catch(err => {
                SharedHelper.fireErrorToast("Fehler", "Die Dateien konnten nicht vom Server abgerufen werden.")
            })
    }

    const findDocByKey = (key) =>
    {
        let keyToFind = key
        if (keyToFind[keyToFind.length - 1] === "/") // remove slashes
            keyToFind = keyToFind.substr(0, keyToFind.length - 1)
        return documents.find(doc => doc.parent_key === keyToFind) // find the file
    }
    const findParentId = (key) =>
    {
        if (!key)
            return SharedHelper.logError("Error. Selectionstate is null")
        let parent_id
        if (key === "root") {
            parent_id = 0
        } else {
            let keyToFind = ""
            if (key[key.length - 1] === "/") // Its a folder
            {
                keyToFind = key.substr(0, key.length - 1) // remove slashes
            } else // A file was selected. Get the Foldername
            {
                keyToFind = key.substr(0, key.lastIndexOf("/")) // remove the filename
            }
            let f = documents.find(doc => doc.type === FILE_TYPES.FOLDER && doc.parent_key === keyToFind) // only find folders
            if (!f) {
                return SharedHelper.fireErrorToast("Fehler", "Fehler bei der Vorbereitung des Hochladens.")
            } else {
                return f.id
            }
        }
        return parent_id
    }


    let createDirectory = (key) => {
        if (!key)
            return null
        let newFolderName, folderKey
        let regexName = key.match(/\/([^\/]+)\/?$/)

        if (!regexName) {
            newFolderName = key.replace("/", "")
            folderKey = ""
        } else {
            newFolderName = regexName[1].replace("/", "")
            folderKey = key.substr(0, key.length - newFolderName.length - 1)//.replace("/","")
        }

        let id = null
        if (!folderKey) // root
        {
            id = 0
        } else {
            if (folderKey[folderKey.length - 1] === "/") // remove slashes
                folderKey = folderKey.substr(0, folderKey.length - 1)
            let folder = documents.find(doc => doc.type === FILE_TYPES.FOLDER && doc.parent_key === folderKey)
            if (!folder)
                return SharedHelper.logError("Could not find folder")
            id = folder.id
        }

        ajaxCreateDocumentFolder(modelId, modelType, id, newFolderName)
            .then(resp => {
                if (resp.status > 0) {
                    SharedHelper.fireSuccessToast("Erfolg", "Der Ordner wurde erstellt.")
                    loadFiles()
                    return
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                SharedHelper.fireErrorToast("Fehler", "Der Ordner konnte nicht erstellt werden. " + err.message)
            })
    }

    let moveFile = (oldkey, newkey) => {
        if (!oldkey.length || !newkey.length)
            return SharedHelper.logError("Unable to move file. Path is empty")
        if (oldkey[oldkey.length - 1] === "/") // remove slashes
            oldkey = oldkey.substr(0, oldkey.length - 1)
        if (newkey[newkey.length - 1] === "/") // remove slashes
            newkey = newkey.substr(0, newkey.length - 1)

        //truncate the last (self) part of the newkey
        let indexOfSlash = newkey.lastIndexOf("/")
        if (!indexOfSlash)
            return SharedHelper.logError("Unable to move file.")

        newkey = newkey.substr(0, indexOfSlash)

        let docToMove = documents.find(doc => doc.parent_key === oldkey)
        let targetDoc = documents.find(doc => doc.parent_key === newkey)
        if(!targetDoc)
            targetDoc = {name : newkey, id : 0 }
        modalRef.current?.open((btn) => {
                if (btn === MODAL_BUTTONS.YES) onModalYes(docToMove, targetDoc)
            },
            "Verschieben",
            "Soll '" + docToMove.name + "' wirklich nach '" + targetDoc.name + "' verschoben werden?",
            [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
        )

        let onModalYes = (docToMove, targetDoc) => {
            if (!docToMove || !targetDoc)
                return SharedHelper.logError("Unable to move file.")

            ajaxMoveDocument(docToMove.id, targetDoc.id)
                .then(resp => {
                    if (resp.status > 0) {
                        SharedHelper.fireSuccessToast("Erfolg", "Das Verschieben wurde erfoglreich ausgeführt.")
                        return loadFiles() // necessary because the filebrowser does not recognize updates somehow...
                    }
                    throw new Error(resp.message)
                })
                .catch((err) => {
                    SharedHelper.fireErrorToast("Fehler", "Verschieben Fehlgeschlagen. " + err.message)
                })
        }
    }

    let renameFile = (oldkey, newkey) => {

        if (!oldkey || !newkey || !oldkey.length || !newkey.length)
            return SharedHelper.logError("Unable to rename file. Path is empty")
        if (oldkey[oldkey.length - 1] === "/") // remove slashes
            oldkey = oldkey.substr(0, oldkey.length - 1)
        if (newkey[newkey.length - 1] === "/") // remove slashes
            newkey = newkey.substr(0, newkey.length - 1)

        let newName = null
        if (newkey.includes("/")) {
            let regexName = newkey.match(/\/([^\/]+)\/?$/)
            if (!regexName || regexName.length < 1)
                return SharedHelper.logError("Unable to rename file. Could not obtain filename")
            newName = regexName[1]
        } else {
            newName = newkey // if there is not a slash in the name, just take the key as name
        }
        let doc = documents.find(doc => doc.parent_key === oldkey)
        if (!doc)
            return SharedHelper.logError("Unable to rename file. Could not obtain document")
        ajaxRenameDocument(doc.id, newName)
            .then(resp => {
                if (resp.status > 0) {
                    SharedHelper.fireSuccessToast("Erfolg", "Das Umbenennen war erfolgreich.")
                    return loadFiles() // necessary because the filebrowser does not recognize updates somehow...
                }
                throw new Error(resp.message)
            })
            .catch((err) => {
                SharedHelper.fireErrorToast("Fehler", "Umbennenen Fehlgeschlagen. " + err.message)
            })
    }

    let deleteFile = (keys) => {
        if (!Array.isArray(keys) || keys.length === 0)
            return SharedHelper.logError("Unable to delete file. Paths are empty")

        let key = keys[0]

        if (!key || !key.length)
            return SharedHelper.logError("Unable to delete file. Path is empty")
        if (key[key.length - 1] === "/") // remove slashes
            key = key.substr(0, key.length - 1)

        let doc = documents.find(doc => doc.parent_key === key)
        if (!doc)
            return SharedHelper.logError("Unable to delete file. Could not obtain document")

        ajaxDeleteDocument(doc.id)
            .then(resp => {
                if (resp.status > 0) {
                    SharedHelper.fireSuccessToast("Erfolg", "Das Löschen war erfolgreich.")
                    return loadFiles() // necessary because the filebrowser does not recognize updates somehow...
                }
                throw new Error(resp.message)
            })
            .catch((err) => {
                SharedHelper.fireErrorToast("Fehler", "Löschen Fehlgeschlagen. " + err.message)
            })
    }

    let onFilesDropped = (files, path) =>
    {
        if(!props.canUserMultiUpload && files.length > 1)
            return SharedHelper.fireWarningToast("Achtung", "Multi-Upload ist nicht verfügbar. Bitte wählen Sie nur eine Datei aus.")
        if(files.length > MAX_FILES_DROP)
            return SharedHelper.fireWarningToast("Achtung", "Sie haben die maximale Upload Kapazität von "+MAX_FILES_DROP+ " überschritten.")
        const p =  path? path : "root"
        fileUploadModalRef.current?.open(files, findParentId(p), p)
    }

    let handleCreateFolder = (key) => {
        createDirectory(key)
    }
    let handleCreateFiles = (files, prefix) => {
        onFilesDropped(files, prefix)
    }
    let handleMoveFolder = (oldKey, newKey) => {
        moveFile(oldKey, newKey)
    }
    let handleMoveFile = (oldKey, newKey) => {
        moveFile(oldKey, newKey)
    }
    let handleRenameFolder = (oldKey, newKey) => {
        renameFile(oldKey, newKey)
    }
    let handleRenameFile = (oldKey, newKey) => {
        renameFile(oldKey, newKey)
    }
    let handleDeleteFolder = (folderKey) => {
        deleteFile(folderKey)
    }
    let handleDeleteFile = (fileKey) => {
        deleteFile(fileKey)
    }
    let handleFileDoubleClick = (fileKey) =>
    {
        let doc = findDocByKey(fileKey)
        if (!doc)
            return SharedHelper.fireErrorToast("Fehler", "Fehler bei der Vorbereitung des Downloads.")


        let mediumUrl = ajaxDownloadDocumentUrl(doc.id, doc.access_hash)
        const download = () =>
        {
            let win = window.open(mediumUrl, '_blank')
            win.focus()
        }

        if(!props.canUserPreview)
        {
            if(props.canUserDownload)
                return download()
            return
        }


        if(["jpg", "jpeg", "png"].includes(doc.file_type))
        {
            modalRef?.current.open(() => {}, "Datei: "+doc.name,
                <>
                    <div style={{display :"flex", flex : 1, flexDirection :"row", justifyContent :"center"}}>
                        <img style={{maxWidth : "100%", maxHeight :"70vh"}} src={mediumUrl} />
                    </div>
                    <Button onClick={() =>download() }>Download</Button>
                </>
                ,[]
            )
        }
        else if(["mp4","webm"].includes(doc.file_type))
        {
            modalRef?.current.open(() => {}, "Datei: "+doc.name,
                <>
                    <div style={{display :"flex", flex : 1, flexDirection :"row", justifyContent :"center"}}>
                        <video controls style={{maxWidth:"100%"}}>
                            <source src={mediumUrl} />
                        </video>
                    </div>
                    <Button onClick={() =>download() }>Download</Button>
                </>
                ,[]
            )
        }
        else if(["pdf"].includes(doc.file_type))
        {
            pdfModalRef?.current.open(() => {}, "Datei: "+doc.name,
                <>
                    <div>
                        <PdfViewer url={mediumUrl}/>
                    </div>
                    <Button onClick={() =>download() }>Download</Button>
                </>
                ,[]
            )
        }
        else if(!!doc.with_external_viewer )
            handleExternalViewer(doc)
        else
            handleDownloadFile([fileKey])

    }
    let handleUploadFile = (selection) => {
        if (!Array.isArray(selection))
            return SharedHelper.logError("Could not upload a file. Filepath not existent")
        if (selection.length === 0) // root
            setLastUploadSelectionKey("root") // save last state of the selection
        else
            setLastUploadSelectionKey(selection[0])

        uploadInputRef?.current?.click()
    }

    let inputOnChangeHandler = (evt) => {
        if (lastUploadSelectionKey === null || evt.target?.files?.length === 0)
            return
        if(!props.canUserMultiUpload && evt.target?.files.length > 1)
            return SharedHelper.fireWarningToast("Achtung", "Multi-Upload ist nicht verfügbar. Bitte wählen Sie nur eine Datei aus.")
        fileUploadModalRef.current?.open(Array.from(evt.target?.files), findParentId(lastUploadSelectionKey), lastUploadSelectionKey)
    }

    let handleDownloadFile = (fileKeys) => {
        if (!Array.isArray(fileKeys) || fileKeys.length > 1)
            return SharedHelper.fireWarningToast("Achtung", "Bitte wählen Sie nur eine Datei zum herunterladen aus. ")

        let doc = findDocByKey(fileKeys[0])
        if (!doc)
            return SharedHelper.fireErrorToast("Fehler", "Fehler bei der Vorbereitung des Downloads.")

        let mediumUrl = ajaxDownloadDocumentUrl(doc.id, doc.access_hash)
        let win = window.open(mediumUrl, '_blank')
        win.focus()

    }

    const handleExternalViewer = (fileObj) =>
    {
        let win = window.open(ajaxOpenDocumentUrl(fileObj.id, fileObj.access_hash), '_blank');
        win.focus();
    }

    if (!modelType || !modelId)
        return <div>Wrong configuration</div>

    return <div className={"card p-2"}><FileBrowser
        icons={ICONS}
        files={documents /*documents.map( d => ({...d, with_external_viewer : true}))*/}
        locale={"de"}
        columns={props.columns}

        backend={SharedHelper.getDnDHTML5Backend()}
        detailRenderer={(props) => <DetailRenderer {...props}/>}
        filterRenderer={(props) => canUserEdit ? <FilterRenderer callback={() => setEditingMode(!editingMode)}
                                                                 checked={editingMode}></FilterRenderer> : <></>}
        confirmDeletionRenderer={(props) => canUserDelete ? <DeletionRenderer {...props}/> : <></>}
        onCreateFolder={canUserEdit && editingMode && props.canUserCreateFolder ? handleCreateFolder : null}
        onCreateFiles={canUserEdit && editingMode ? handleCreateFiles : null}
        onMoveFolder={canUserEdit && editingMode ? handleMoveFolder : null}
        onMoveFile={canUserEdit && editingMode ? handleMoveFile : null}
        onRenameFolder={canUserEdit && editingMode ? handleRenameFolder : null}
        onRenameFile={canUserEdit && editingMode ? handleRenameFile : null}
        onDeleteFolder={canUserDelete && editingMode ? handleDeleteFolder : null}
        onDeleteFile={canUserDelete  && editingMode ? handleDeleteFile : null}
        onUploadFile={canUserUpload && editingMode ? handleUploadFile : null}
        onDownloadFile={props.canUserDownload? handleDownloadFile : null}
        onExternalViewerClick={handleExternalViewer}
        onFileDoubleClick={handleFileDoubleClick}
    />
        <input
            multiple={props.canUserMultiUpload}
            type="file"
            onChange={inputOnChangeHandler}
            ref={uploadInputRef}
            style={{display: "none"}}
        />
        <EducaModal closeButton={true} noBackdrop={true} size={"lg"} ref={modalRef}/>
        <EducaModal closeButton={true} noBackdrop={true} size={"xl"} ref={pdfModalRef}/>
        <FileUploadModal modelId={props.modelId}
                         modelType={props.modelType}
                         reloadTrigger={ () => loadFiles()}
                         ajaxUploadUrl={props.ajaxUploadUrl}
                         ref={fileUploadModalRef}/>
    </div>


}

function DeletionRenderer(props) {
    return <div className="deleting">
        {props.children}
        <div>
            <Button
                onClick={props.handleDeleteSubmit}
                variant={"danger"}
            >
                Löschen
            </Button>
        </div>
    </div>
}

function FilterRenderer(props) {
    return <div style={{display: "flex"}}>
        <div className={"mr-1"}>Bearbeitungsmodus</div>
        <ReactSwitch
            checked={props.checked}
            onChange={() => props.callback()}
        />
    </div>
}

function DetailRenderer(props) {
    return <></>
}

/**
 *
 *
 */

const defaultState = {open: false, files : []}
class FileUploadModal extends Component {

    constructor(props) {
        super(props);
        this.state = defaultState
    }

    open(files, parentId, path)
    {
        if(!(files?.length>0))
            return SharedHelper.fireErrorToast("Fehler", "Keine Dateien ausgewählt.")
        this.setState(
            {
                open : true,
                files : files.map( f => ({file : f, modelType : this.props.modelType, modelId :  this.props.modelId, parentId : parentId, path : path}))
            })
    }

    close()
    {
        this.props.reloadTrigger()
        this.setState(defaultState)
    }

    render() {
        return (
            <Modal
                backdrop={"static"}
                size={"lg"}
                show={this.state.open}
                onHide={() => this.close()}
            > <Modal.Header closeButton>
                <Modal.Title>
                    Upload läuft...
                </Modal.Title>
            </Modal.Header>
                <Modal.Body>
                    <FileUpload closeTrigger={() => this.close()} ajaxUploadUrl={this.props.ajaxUploadUrl} files={this.state.files} />
                </Modal.Body>
            </Modal>
        );
    }
}


function FileUpload(props) {

    const files = props.files
    const ajaxUploadUrl = props.ajaxUploadUrl

    let [queue, setQueue] = useState([])
    let [isFinished, setIsFinished] = useState(false)

    const currentQueueRef = React.useRef(queue);

    const _setQueue = (q) =>
    {
        setQueue(q)
        currentQueueRef.current = q;
    }
    useEffect(()=>
    {
        return () => unmount()
    },[])

    useEffect(()=>
    {
        if(!!isFinished)
        {
            unmount()
            props.closeTrigger();
        }

    },[isFinished])

    useEffect(() =>
    {
        let q = files.map( (f,i) =>
        {
            const request = new XMLHttpRequest()
            const file = f.file
            if(!file)
                return
            const parentId = f.parentId
            const modelId = f.modelId
            const modelType = f.modelType
            const size = SharedHelper.bytesToSize(file.size)
            const name = file.name
            const type = file.type
            const id = SharedHelper.createUUID()+i
            return({
                key : id,
                file : file,
                progress : 0,
                name : name,
                size : size,
                type  :type,
                request : request,
                start : () => uploadFile(id, modelId, modelType, parentId, file, request)
            })
        })
        _setQueue(q)
        setIsFinished(false)
        uploadAll(q)

    },[files])


    const uploadFile = (id, modelId, modelType, parentId, file, request) =>
    {
        let req = request
        let formData = new FormData();
        formData.append("document", file)
        formData.append("parent_id", parentId)
        formData.append("model_id", modelId)
        formData.append("model_type", modelType)

        req.open("POST", props.ajaxUploadUrl);
        req.setRequestHeader("Authorization",  "Bearer "+SharedHelper.getJwt())

        req.upload.onprogress = (e) =>
        {
            if (e.lengthComputable && e.total > 0 ) {
                updateProgress(id, (e.loaded / e.total) * 100, e.loaded, e.total )
            }
        }
        req.onloadstart = function (e) {
            //   updateProgress(id, 0 )
        }
        req.onloadend = function (e) {
            if (e.lengthComputable && e.total > 0 ) {
                updateProgress(id,  100, e.total, e.total, false, true)
            }
        }
        req.onerror = function (e) {
            updateProgress(id,  100, 0, 0, true)
            SharedHelper.fireErrorToast("Fehler", "Die Datei "+file.name + " konnte nicht hochgeladen werden.")
        }
        req.onabort = function (e) {
            updateProgress(id,  100, 0, 0, true)
        }
        req.onreadystatechange = (oEvent) => {
        if (req.readyState === 4) {
            if (req.status !== 200) {
                updateProgress(id,  100, 0, 0, true)
                let addText = ""
                if(req.status == 400)
                    addText = "Möglicherweise existiert bereits eine Datei mit diesem Namen."
                if(req.status == 413)
                    addText = "Diese Datei ist zu groß."
                if(req.status == 500)
                    addText = "Kritischer Serverfehler."
                SharedHelper.fireErrorToast("Fehler", "Die Datei "+file.name + " konnte nicht hochgeladen werden. "+addText)
            }
            else
            {
                SharedHelper.fireSuccessToast("Datei Hochgeladen", "Die Datei "+file.name + " wurde erfolgreich hochgeladen.")
            }
        }
    };

        req.send(formData);
    }


    let lock = false
    const updateProgress = (key, progress, loaded, total, isError, finished) =>
    {
        if(lock)
            return
        let newQ = _.cloneDeep(currentQueueRef.current)
        let i  = newQ.findIndex(o => o.key === key)
        if(i >= 0)
        {
            newQ[i] = { ...newQ[i], progress : progress > 100? 100 : progress, loaded : loaded, total : total, error : !!isError, finished : !!finished}
            _setQueue(newQ)
        }
        lock = false
        if( newQ?.reduce( (prev,curr) => prev && (curr.progress == 100 || !!curr.finished || curr.error), true )  )
            setIsFinished(true)
    }

    const unmount = () =>
    {
        _setQueue([])
        setIsFinished(false)
    }

    const uploadAll = (q) =>
    {
        if(q)
            return q.map( obj => obj.start() )
        queue.map( obj => obj.start() )
    }

    const abortAll = () =>
    {
        queue.map( obj => obj.request?.abort() )
    }

    if(!files || !ajaxUploadUrl)
        return <></>


    const isUploading = queue?.reduce( (prev,curr) => prev || (curr.progress != 100 && curr.progress != 0), false)
    return (
        <div style={{display : "flex", flexDirection :"column"}}>
            {queue.map( (obj,i) =>
            {
                const label =  obj.finished? "Upload erfolgreich" : obj.error? "Upload abgebrochen" : obj.total? SharedHelper.bytesToSize(obj.loaded) +" / "+SharedHelper.bytesToSize(obj.total) : ""
                return <div key={i}>
                    {getDisplayPair(obj.name + " - "+obj.size,
                    <ProgressBar style={{backgroundColor : "#0050ca !important"}} variant={!obj.error? "primary" : "danger"} animated={obj.progress !== 100} now={obj.progress} label={label} />
                    )}

                </div>
            })}

        <div>
            <Button
                variant={isUploading? "danger" : "primary"}
                onClick={() => isUploading? abortAll() : uploadAll()}>
                {isUploading? "Abbrechen" : "Hochladen"}
            </Button>
        </div>
        </div>
    );
}




export default SharedFileBrowser;
