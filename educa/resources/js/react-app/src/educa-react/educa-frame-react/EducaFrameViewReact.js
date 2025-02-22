import React, { Suspense, useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { EducaLoading } from "../../shared-local/Loading.js";
import EducaAjaxHelper from "../helpers/EducaAjaxHelper.js";
import ClassbookMarkWidget from "../educa-classbook-react/widgets/ClassbookMarkWidget.js";
import ClassbookExamList from "../educa-classbook-react/widgets/ClassbookExamList.js";
import { createRemoteComponent, createRequires } from "@paciolan/remote-component";
import ClassbookAbsenteeism from "../educa-classbook-react/widgets/ClassbookAbsenteeism.js";
import ClassbookReport from "../educa-classbook-react/widgets/ClassbookReport.js";
import { Row } from "react-bootstrap";

const requires = createRequires(() => ({
    react: React,
}));

export const RemoteComponent = createRemoteComponent({ requires });

// Component mapping
const componentMap = {
    ClassbookMarkWidget,
    ClassbookExamList,
    ClassbookAbsenteeism,
    ClassbookReport
};

function EducaFrameViewReact() {
    const { app, frame, frame_id } = useParams(); // Get the frame ID from the URL
    const [pageConfig, setPageConfig] = useState(null); // Store all configurations
    const [currentPage, setCurrentPage] = useState(null); // Store only the selected page
    const [key, setKey] = useState(0); // Force component re-render when route changes

    useEffect(() => {
        EducaAjaxHelper.loadFrameConfiguration()
            .then((data) => {
                setPageConfig(data);
                // Select initial page (based on route or default)
                const initialPage = data.find((page) => page.key === frame_id) || data[0];
                setCurrentPage(initialPage);
            })
            .catch((error) => console.error("Error fetching page config:", error));
    }, []);

    useEffect(() => {
        console.log("Navigated to frame_id:", frame_id);
        if (pageConfig) {
            const selectedPage = pageConfig.find((page) => page.key === frame_id) || pageConfig[0];
            setCurrentPage(selectedPage);
            setKey((prevKey) => prevKey + 1); // ✅ Force re-render
        }
    }, [frame_id, pageConfig]);

    if (!pageConfig || !currentPage) {
        return <EducaLoading />;
    }

    return (
        <div key={key} style={{ minHeight: "80vh" }}>
            <h1>{currentPage.display_name}</h1>
            {currentPage.layout.map((row, rowIndex) => (
                <Row className="mt-2" key={rowIndex}>
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
                </Row>
            ))}
        </div>
    );
}

export default EducaFrameViewReact;
