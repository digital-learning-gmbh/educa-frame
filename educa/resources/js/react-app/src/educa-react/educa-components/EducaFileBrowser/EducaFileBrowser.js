import React  from 'react';
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import SharedFileBrowser from "../../../shared/shared-components/SharedFileBrowser";
import {BROWSER_COLUMNS} from "react-keyed-file-browser-educa"

function EducaFileBrowser(props) {

    return <SharedFileBrowser
        modelType={props.modelType}
        modelId={props.modelId}
        columns={[BROWSER_COLUMNS.FILE,BROWSER_COLUMNS.SIZE, BROWSER_COLUMNS.LAST_MODIFIED, BROWSER_COLUMNS.CREATED_AT]}
        ajaxUploadUrl={AjaxHelper.getDocumentUploadUrl()}
        canUserEdit={props.canUserEdit}
        canUserUpload={props.canUserUpload}
        ajaxCreateDocumentFolder={(modelId, modelType, parent_id, foldername) => AjaxHelper.createDocumentFolder(modelId, modelType, parent_id, foldername)}
        ajaxMoveDocument={ (documentId, newParentId) => AjaxHelper.moveDocument(documentId, newParentId)}
        ajaxDeleteDocument={ (documentId) => AjaxHelper.deleteDocument(documentId)}
        ajaxCreateDocument={ (modelId, modelType, parent_id, file) => AjaxHelper.createDocument(modelId, modelType, parent_id, file)}
        ajaxGetDocumentList={ (modelId, modelType, withPath) => AjaxHelper.getDocumentList(modelId, modelType, withPath)}
        ajaxRenameDocument={(documentId, newName) => AjaxHelper.renameDocument(documentId, newName)}
        ajaxOpenDocumentUrl={(documentId, access_hash) => AjaxHelper.openDocumentUrl(documentId,access_hash)}
        ajaxDownloadDocumentUrl={ (documentId, access_hash) => AjaxHelper.downloadDocumentUrl(documentId, access_hash)}/>

}

export default EducaFileBrowser
