import React, { Suspense, useEffect, useState } from "react";
import { EducaLoading } from "../../shared-local/Loading.js";
import EducaAjaxHelper from "../helpers/EducaAjaxHelper.js";
import ClassbookMarkWidget from "../educa-classbook-react/widgets/ClassbookMarkWidget.js";
import ClassbookExamList from "../educa-classbook-react/widgets/ClassbookExamList.js";
import ReportSummaryWidget from "../educa-classbook-react/widgets/ReportSummaryWidget.js";
import RecentActivitiesWidget from "../educa-classbook-react/widgets/RecentActivitiesWidget.js";
import UserSettingsWidget from "../educa-classbook-react/widgets/UserSettingsWidget.js";
import {
    createRemoteComponent,
    createRequires,
} from "@paciolan/remote-component";

const requires = createRequires(() => ({
    react: React,
}));

export const RemoteComponent = createRemoteComponent({ requires });

// Explicit map for resolving components
const componentMap = {
    ClassbookMarkWidget,
    ClassbookExamList,
    ReportSummaryWidget,
    RecentActivitiesWidget,
    UserSettingsWidget,
};

function EducaFrameViewReact() {
    const [pageConfig, setPageConfig] = useState(null);
    const [currentPage, setCurrentPage] = useState(null);

    useEffect(() => {
        EducaAjaxHelper.loadFrameConfiguration()
            .then((data) => {
                const urlParts = window.location.pathname.split("/");
                const pageKey = urlParts[urlParts.length - 1];
                const selectedPage = data.pages.find((page) => page.key === pageKey);

                if (selectedPage) {
                    setCurrentPage(selectedPage);
                } else {
                    setCurrentPage(data.pages[0]); // Default to the first page
                }

                setPageConfig(data);
            })
            .catch((error) => console.error("Error fetching page config:", error));
    }, []);

    if (!pageConfig || !currentPage) {
        return <EducaLoading />;
    }

    return (
        <div style={{ minHeight: "80vh" }}>
            <h1>{currentPage.display_name}</h1>
            {currentPage.layout.map((row, rowIndex) => (
                <div className="row mt-2" key={rowIndex}>
                    {row.map((widget, colIndex) => (
                        <div className={`col-${widget.size}`} key={colIndex}>
                            {widget.type === "local" ? (
                                <Suspense fallback={<div>Loading component...</div>}>
                                    {React.createElement(componentMap[widget.component])}
                                </Suspense>
                            ) : null}
                            {widget.type === "url" ? (
                                <iframe
                                    src={widget.url}
                                    frameBorder="0"
                                    style={{ width: "100%", height: widget.height ?? "400px" }}
                                ></iframe>
                            ) : null}
                            {widget.type === "customComponent" ? (
                                <RemoteComponent url={widget.url} />
                            ) : null}
                        </div>
                    ))}
                </div>
            ))}
        </div>
    );
}

export default EducaFrameViewReact;
