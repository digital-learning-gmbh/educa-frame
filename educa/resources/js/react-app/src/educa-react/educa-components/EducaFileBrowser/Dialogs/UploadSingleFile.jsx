import {Badge, ListGroup, ProgressBar} from "react-bootstrap";
import Button from "react-bootstrap/Button";
import {useEffect, useState} from "react";
import AjaxHelper from "../../../helpers/EducaAjaxHelper.js";
import SharedHelper, {MODELS} from "../../../../shared/shared-helpers/SharedHelper.js";


function UploadSingleFile({file, modelId, modelType, parentId,cancelUpload, setFinishUpload, setErrorUpload, overrideNames})
{
    let [progress, setProgress] = useState(0);
    let [isExecuted, setIsExecuted] = useState(false);

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

    let uploadFile = () => {
        AjaxHelper.createAxiosDocument(
            modelId,
            modelType,
            parentId,
            file,
            false,
            overrideNames,
            file.path,
            (progress) =>
                setProgress(Math.round(
                        (progress.loaded * 100) / progress.total
                    ))
        ).then((documentResponse) => {
            if (
                documentResponse.status <= 0 ||
                !documentResponse.payload?.document?.id
            ) {
                let addText = ""
                if(documentResponse.status == 400)
                    addText = "Möglicherweise existiert bereits eine Datei mit diesem Namen."
                if(documentResponse.status == 413)
                    addText = "Diese Datei ist zu groß."
                if(documentResponse.status == 500)
                    addText = "Kritischer Serverfehler."
                SharedHelper.fireErrorToast("Fehler", "Die Datei "+file.name + " konnte nicht hochgeladen werden. "+addText)
                file.detailsError = addText;
                setErrorUpload(file)
                setIsExecuted(true)
                return;
            } else {
                setFinishUpload(file)
                setIsExecuted(true)
            }

        }).catch((err) => {
            let documentResponse = err.response;
            let addText = ""
            if(documentResponse.status == 400 || documentResponse.status == 401)
                addText = "Möglicherweise existiert bereits eine Datei mit diesem Namen."
            if(documentResponse.status == 413)
                addText = "Diese Datei ist zu groß."
            if(documentResponse.status == 500)
                addText = "Kritischer Serverfehler."
            SharedHelper.fireErrorToast("Fehler", "Die Datei "+file.name + " konnte nicht hochgeladen werden. "+addText)
            file.detailsError = addText;
            setErrorUpload(file)
            setIsExecuted(true)
            return;
        });
    }

    useEffect(() => {
        uploadFile()
    }, [file]);

    return isExecuted ? <></> : <ListGroup.Item><div className={"d-flex"}>
        <div style={{width: "calc(100% - 20px)"}}>
            {file.path} <Badge variant="secondary">{humanFileSize(file.size)}</Badge>
            <ProgressBar now={progress} label={`${progress}%`}/>
        </div>
        <Button variant={"danger"} onClick={() =>
            setIsExecuted(true)} className={"ml-2"}>
            <i className="fas fa-trash"></i></Button>
    </div></ListGroup.Item>
}

export default UploadSingleFile;
