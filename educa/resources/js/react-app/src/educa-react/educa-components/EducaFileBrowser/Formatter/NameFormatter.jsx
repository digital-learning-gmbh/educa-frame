import {createRoot} from "react-dom/client";
import React from "react";

export const NameFormatter = (
    cell,
    _formatterParams,
    _onRendered
) => {
    const reactNode = document.createElement("div");
    const reactRoot = createRoot(reactNode);
    let img = null;

    let data = cell.getData();
    if(data.type === "folder")
    {
        img = "/filemanager-icons/folder.svg";
    } else if(data.file_type === "pdf") {
        img = "/filemanager-icons/pdf.svg";
    } else if(data.file_type === "xlsx" || data.file_type === "xls") {
        img = "/filemanager-icons/xlsx.svg";
    } else if(data.file_type === "doc" || data.file_type === "docx") {
        img = "/filemanager-icons/docx.svg";
    } else if(data.file_type === "png" || data.file_type === "jpeg" || data.file_type === "jpg" || data.file_type === "gif") {
        img = "/filemanager-icons/img.svg";
    } else {
        img = "/filemanager-icons/file.svg";
    }

    reactRoot.render(
        <div>{img ? <img src={img} width="16" height="16"/> : null } {cell.getValue()}</div>
    );
    return reactNode;
};
