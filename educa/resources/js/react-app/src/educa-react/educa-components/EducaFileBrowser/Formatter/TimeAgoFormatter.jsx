import {createRoot} from "react-dom/client";
import ReactTimeAgo from "react-time-ago";
import React from "react";

export const TimeAgoFormatter = (
    cell,
    _formatterParams,
    _onRendered
) => {
    const reactNode = document.createElement("div");
    const reactRoot = createRoot(reactNode);
    reactRoot.render(
        <ReactTimeAgo date={cell.getValue()} />
    );
    return reactNode;
};
