import React, { useRef, useState } from "react";
import {Alert, Card} from "react-bootstrap";
import Button from "react-bootstrap/Button";
import AjaxHelper from "../../helpers/EducaAjaxHelper";

export default function AnalyticsDataSettings(props) {

    return <div
        style={{display: "flex", flexDirection: "column"}}
        className={"container"}
    >
        <h3
            style={{
                marginBottom: "1rem"
            }}
        >
            Daten-Analyse
        </h3>
        <h5>
            Erfasste Daten</h5>
        <Card>
            <Card.Body>
            <div>
            <Button
                variant={"outline-secondary"}
                className={"ml-2"}
                onClick={() => {
                    window
                        .open(
                            AjaxHelper.analyticsDownloadxAPI(),
                            "_blank"
                        )
                        .focus();
                }}
            >
                Lerndaten exportieren (xAPI-Datei)
            </Button></div></Card.Body>
        </Card>
    </div>

}
