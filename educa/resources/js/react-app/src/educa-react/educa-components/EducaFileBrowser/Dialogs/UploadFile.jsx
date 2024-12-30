import React, {useEffect, useMemo, useRef, useState} from 'react';
import Modal from "react-bootstrap/Modal";
import {MODAL_BUTTONS} from "../../../../shared/shared-components/EducaModal.js";
import {DisplayPair} from "../../../../shared/shared-components/Inputs.js";
import {FormControl, ListGroup} from "react-bootstrap";
import Button from "react-bootstrap/Button";
import {useDropzone} from 'react-dropzone';
import UploadSingleFile from "./UploadSingleFile.jsx";

const baseStyle = {
    flex: 1,
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    padding: '20px',
    borderWidth: 2,
    borderRadius: 2,
    borderColor: '#eeeeee',
    borderStyle: 'dashed',
    backgroundColor: '#fafafa',
    color: '#bdbdbd',
    outline: 'none',
    transition: 'border .24s ease-in-out'
};

const focusedStyle = {
    borderColor: '#2196f3'
};

const acceptStyle = {
    borderColor: '#00e676'
};

const rejectStyle = {
    borderColor: '#ff1744'
};

function UploadFile({show, onHide, reloadCallback,modelId, modelType, parentId}) {

    let [newName, setNewName] = useState(null);
    let [queue, setQueue] = useState([]);
    let [finished, setFinished] = useState([]);
    let [errors, setErrors] = useState([]);

    const {    getRootProps,
        getInputProps,
        isFocused,
        isDragAccept,
        isDragReject,
        acceptedFiles,
        open
    } = useDropzone({
        // Disable click and keydown behavior
        useFsAccessApi:false,
        noClick: true,
        noKeyboard: true
    });


    useEffect(() => {
        setQueue([...queue, ...acceptedFiles])
    }, [acceptedFiles]);

    useEffect(() => {
        setQueue([])
        setFinished([])
        setErrors([])
    }, [show]);

    let cancelUpload = (file) => {
       // setQueue(queue.filter(f => f !== file))
    }
    let setFinishUpload = (file) => {
       // setQueue(queue.filter(f => f !== file))
        setFinished(finished => [file, ...finished])
        reloadCallback()
    }

    let setErrorUpload = (file) => {
     //   setQueue(queue.filter(f => f !== file))
        setErrors(errors => [file, ...errors])
        reloadCallback()
    }

    const files = queue.map(file => (
            <UploadSingleFile key={file.path} setErrorUpload={setErrorUpload} setFinishUpload={setFinishUpload} cancelUpload={cancelUpload} file={file} modelId={modelId} modelType={modelType} parentId={parentId} />
    ));

    const style = useMemo(() => ({
        ...baseStyle,
        ...(isFocused ? focusedStyle : {}),
        ...(isDragAccept ? acceptStyle : {}),
        ...(isDragReject ? rejectStyle : {})
    }), [
        isFocused,
        isDragAccept,
        isDragReject
    ]);


    return <Modal
        show={show}
        onHide={onHide}
        backdrop="static"
        keyboard={false}
    >
        <Modal.Header closeButton>
            <Modal.Title>Dateien oder Ordner hochladen</Modal.Title>
        </Modal.Header>
        <Modal.Body>
            <div><p>Lege in die folgende Fläche Dateien oder gesamte Ordner ab, um diese hochzuladen</p>
                <div {...getRootProps({style})}>
                    <input {...getInputProps()} />
                    <p>Lege die Dateien oder Ordner per Drag'n'Drop hier, um diese hochzuladen</p>
                    <button type="button" className={"btn btn-secondary"} onClick={open}>
                        Dateien auswählen
                    </button>
                </div>
                { queue?.length > 0 ?
                <aside>
                    <h5 className={"mt-2"}>Warteschlange</h5>
                    <ListGroup>{files}</ListGroup>
                </aside> : null }
                { finished?.length > 0 ?
                    <aside>
                        <h5 className={"mt-2"}>Hochgeladen</h5>
                        <ListGroup>{finished.map(file =>
                            <ListGroup.Item key={file.path}>{file.path}</ListGroup.Item>)}</ListGroup>
                    </aside> : null }
                { errors?.length > 0 ?
                    <aside>
                        <h5 className={"mt-2"}>Fehlgeschlagene Uploads</h5>
                        <ListGroup>{errors.map(file =>
                            <ListGroup.Item key={file.path}>
                                <div>{file.path}</div>
                                <i>{file.detailsError}</i>
                            </ListGroup.Item>)}</ListGroup>
                    </aside> : null }
            </div>
        </Modal.Body>
        <Modal.Footer>
            <Button variant="secondary" onClick={onHide}>
                Fenster schließen
            </Button>
        </Modal.Footer>
    </Modal>
}


export default UploadFile;
