import React, {useCallback, useEffect, useRef, useState} from "react";
import { Worker, Viewer } from '@react-pdf-viewer/core';
// Import the main component
import { defaultLayoutPlugin } from '@react-pdf-viewer/default-layout';
// Import styles
import '@react-pdf-viewer/core/lib/styles/index.css';
import '@react-pdf-viewer/default-layout/lib/styles/index.css';
import de_DE from './de_DE.json';

function PdfViewer({url}){

    const defaultLayoutPluginInstance = defaultLayoutPlugin();
    return <div style={{
        height: "calc(100vh - 300px)"
    }}>
        <Worker
            workerUrl="https://unpkg.com/pdfjs-dist@2.2.228/build/pdf.worker.min.js">
            <Viewer fileUrl={url}
                    plugins={[
                        defaultLayoutPluginInstance,
                    ]}
                    localization={de_DE}
            />
        </Worker>
    </div>
}

export default PdfViewer;
