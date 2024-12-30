import React, {useEffect, useRef, useState} from "react";
import ReactTimeAgo from "react-time-ago";
import {FormControl, InputGroup, ListGroup} from "react-bootstrap";
import AjaxHelper from "../../../helpers/EducaAjaxHelper.js";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper.js";
import Button from "react-bootstrap/Button";
import PdfViewer from "../../../../shared/shared-components/PdfViewer.js";
import EducaModal from "../../../../shared/shared-components/EducaModal.js";

function FileDetails({file , close, reloadCallback, canUserEdit}) {


    const modalRef = useRef()
    const pdfModalRef = useRef()

    let [editName, setEditName] = useState();
    let [editDescription, setEditDescription] = useState();
    let [newFileName, setNewFileName] = useState("")

    let [details, setDetails] = useState(null);

    let img = null;
    if(file.type === "folder")
    {
        img = "/filemanager-icons/folder.svg";
    } else if(file.file_type === "pdf") {
        img = "/filemanager-icons/pdf.svg";
    } else if(file.file_type === "xlsx" || file.file_type === "xls") {
        img = "/filemanager-icons/xlsx.svg";
    } else if(file.file_type === "doc" || file.file_type === "docx") {
        img = "/filemanager-icons/docx.svg";
    } else if(file.file_type === "png" || file.file_type === "jpeg" || file.file_type === "jpg" || file.file_type === "gif") {
        img = "/filemanager-icons/img.svg";
    } else {
        img = "/filemanager-icons/file.svg";
    }

    useEffect(() => {
        setNewFileName(file.name?.replace("." + file.file_type,""));
        loadFileDetails();
    }, [file]);

    let loadFileDetails = () =>
    {
        if(file == null)
        {
            setDetails(null)
            return;
        }
        AjaxHelper.detailsDocument(file.id)
            .then(resp => {
                if (resp.status > 0) {
                    setDetails(resp.payload.details)
                    return;
                }
                throw new Error(resp.message)
            })
            .catch((err) => {
                SharedHelper.fireErrorToast("Fehler", "Die Details der Datei '" + file.name + "' konnten nicht geladen werden. " + err.message)
            })
    }

    let downloadFile = () => {
        let mediumUrl = AjaxHelper.downloadDocumentUrl(file.id, file.access_hash)
        let win = window.open(mediumUrl, '_blank')
        win.focus()
    }

    let deleteFile = () => {
        AjaxHelper.deleteDocument(file.id)
            .then(resp => {
            if (resp.status > 0) {
                SharedHelper.fireSuccessToast("Erfolg", "Das Löschen der Datei '" + file.name +"' war erfolgreich.")
                close()
                return reloadCallback() // necessary because the filebrowser does not recognize updates somehow...
            }
            throw new Error(resp.message)
        })
            .catch((err) => {
                SharedHelper.fireErrorToast("Fehler", "Löschen der Datei '" + file.name + "' fehlgeschlagen. " + err.message)
            })
    }

    let renameFile = () => {
        AjaxHelper.renameDocument(file.id, newFileName + "." + file?.file_type)
            .then(resp => {
                if (resp.status > 0) {
                    SharedHelper.fireSuccessToast("Erfolg", "Die Datei wurde umbenannt.")
                    setEditName(false);
                    return reloadCallback() // necessary because the filebrowser does not recognize updates somehow...
                }
                throw new Error(resp.message)
            })
            .catch((err) => {
                SharedHelper.fireErrorToast("Fehler", "Die Datei konnte nicht unbenannt werden. Details: " + err.message)
            })
    }

    let handleExternalViewer = () =>
    {
        let win = window.open(AjaxHelper.openDocumentUrl(file.id, file.access_hash), '_blank');
        win.focus();
    }

    let hasPreview = () => {
        return ["jpg", "jpeg", "png","mp4","webm","pdf"].includes(file?.file_type?.toLowerCase());
    }

    let handlePreviewViewer = () => {
        let mediumUrl = AjaxHelper.downloadDocumentUrl(file.id, file.access_hash)
        if(["jpg", "jpeg", "png"].includes(file.file_type))
        {
            modalRef?.current.open(() => {}, "Datei: "+file.name,
                <>
                    <div style={{display :"flex", flex : 1, flexDirection :"row", justifyContent :"center"}}>
                        <img style={{maxWidth : "100%", maxHeight :"70vh"}} src={mediumUrl} />
                    </div>
                    <Button onClick={() =>downloadFile() }>Download</Button>
                </>
                ,[]
            )
        }
        else if(["mp4","webm"].includes(file.file_type))
        {
            modalRef?.current.open(() => {}, "Datei: "+file.name,
                <>
                    <div style={{display :"flex", flex : 1, flexDirection :"row", justifyContent :"center"}}>
                        <video controls style={{maxWidth:"100%"}}>
                            <source src={mediumUrl} />
                        </video>
                    </div>
                    <Button onClick={() =>downloadFile() }>Download</Button>
                </>
                ,[]
            )
        }
        else if(["pdf"].includes(file.file_type))
        {
            pdfModalRef?.current.open(() => {}, "Datei: "+file.name,
                <>
                    <div>
                        <PdfViewer url={mediumUrl}/>
                    </div>
                    <Button onClick={() =>downloadFile() }>Download</Button>
                </>
                ,[]
            )
        }
    }

    return <div className={"bg-white"}>
        <div className={"d-flex justify-content-end ml-2 mr-2"}>
            <button className={"btn btn-outline-dark"} onClick={() => close()}><i
                className="fas fa-times"></i></button>
        </div>
        <div className={"text-center"}>
            {img ? <img src={img} width="120" height="120"/> : null}
            { editName ? <InputGroup className="mb-3">
                <FormControl
                    placeholder="Dateiname"
                    aria-describedby="basic-addon2"
                    value={newFileName}
                    onChange={(evt) => {
                        setNewFileName(evt?.target.value)
                    }}
                />
                <InputGroup.Append>
                    <Button variant="outline-success" onClick={() => renameFile()}><i className="fas fa-save"></i></Button>
                    <Button variant="outline-secondary" onClick={() => {
                        setNewFileName(file.name?.replace("." + file.file_type,""));
                        setEditName(false)
                    }}><i className="fas fa-times"></i></Button>
                </InputGroup.Append>
            </InputGroup> :
            <h4>{file?.name} { canUserEdit ? <i className="fas fa-pen" onClick={() => {
                setEditName(true);
            }}></i> : null }</h4> }
            <h6>Erstellt <ReactTimeAgo date={file?.created_at}/> von {file?.creator?.name}</h6>
            { details?.inIndex ? <><i className="fas fa-check-circle text-success"></i> im educa AI Index, in {details?.parts?.length} Abschnitte unterteilt</> :
                <p><i className="fas fa-times-circle text-danger"></i> noch nicht im educa AI Index</p>}
            <ListGroup className={"mb-2"}>
                {hasPreview() ?
                    <ListGroup.Item style={{cursor: "pointer"}} onClick={() => handlePreviewViewer()}>
                        <i className="far fa-eye"></i> Vorschau
                    </ListGroup.Item> : null}
                <ListGroup.Item onClick={() => downloadFile()} style={{cursor:"pointer"}}>
                    <i className="fas fa-download"></i> Herunterladen
                </ListGroup.Item>
                {file?.with_external_viewer ?
                <ListGroup.Item style={{cursor:"pointer"}} onClick={() => handleExternalViewer()}>
                    <i className="fas fa-external-link-alt"></i> Öffnen
                </ListGroup.Item> : null }
                { canUserEdit ? <ListGroup.Item onClick={() => deleteFile()} variant={"danger"} style={{cursor:"pointer"}}>
                    <i className="fas fa-trash-alt"></i> Löschen
                </ListGroup.Item> : null }
            </ListGroup>
        </div>
        {/*<div className={"mt-2"}>*/}
        {/*    <h5>Kommentare</h5>*/}
        {/*</div>*/}

        <EducaModal closeButton={true} noBackdrop={true} size={"lg"} ref={modalRef}/>
        <EducaModal closeButton={true} noBackdrop={true} size={"xl"} ref={pdfModalRef}/>
    </div>
}

export default FileDetails;
