import React, { Suspense, useEffect, useState } from "react";
import { EducaLoading } from "../../shared-local/Loading.js";
import EducaAjaxHelper from "../helpers/EducaAjaxHelper.js";
import ClassbookMarkWidget from "../educa-classbook-react/widgets/ClassbookMarkWidget.js";
import {
    createRemoteComponent,
    createRequires,
} from "@paciolan/remote-component";

const requires = createRequires(() => ({
    react: React,
}));

export const RemoteComponent = createRemoteComponent({ requires });

function EducaFrameViewReact() {
    const [pageConfig, setPageConfig] = useState(null);
    const [currentPage, setCurrentPage] = useState(null);


// Component map for resolving component paths
    const componentMap = {
        ClassbookMarkWidget: "../educa-classbook-react/widgets/ClassbookMarkWidget.js",
        ClassbookExamList: "../educa-classbook-react/widgets/ClassbookExamList.js",
        ReportSummaryWidget: "../educa-classbook-react/widgets/ReportSummaryWidget.js",
        RecentActivitiesWidget: "../educa-classbook-react/widgets/RecentActivitiesWidget.js",
        UserSettingsWidget: "../educa-classbook-react/widgets/UserSettingsWidget.js",
    };

    useEffect(() => {
        EducaAjaxHelper.loadFrameConfiguration()
            .then((data) => {
                const urlParts = window.location.pathname.split("/");
                const pageKey = urlParts[urlParts.length - 1];
                console.log(pageKey)
                const selectedPage = data.find((page) => page.key === pageKey);

                if (selectedPage) {
                    setCurrentPage(selectedPage);
                } else {
                    setCurrentPage(data[0]); // Default to the first page
                }

                setPageConfig(data);
            })
            .catch((error) => console.error("Error fetching page config:", error));
    }, [window.location.pathname]);

    const loadComponent = (componentName) => {
        const componentPath = componentMap[componentName];
        if (!componentPath) {
            console.error(`Component not found in map: ${componentName}`);
            return () => <div>Component not found: {componentName}</div>;
        }

        try {
            return React.lazy(() =>
                import(`${componentPath}`).then((module) => {
                    if (!module.default) {
                        throw new Error(`No default export in module: ${componentName}`);
                    }
                    return module;
                })
            );
        } catch (error) {
            console.error(`Error loading component: ${componentName}`, error);
            return () => <div>Error loading component: {componentName}</div>;
        }
    };

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
                                    {React.createElement(loadComponent(widget.component))}
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
