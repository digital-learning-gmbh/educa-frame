import React, {Component, useEffect, useState} from 'react';
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {EducaLoading} from "../../../shared-local/Loading";
import {Card, Container} from "react-bootstrap";
import EducaHelper from "../../helpers/EducaHelper";
import {EducaShimmer} from "../../../shared/shared-components/EducaShimmer";
import QRCode from "react-qr-code";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper.js";

export default function SectionAccessCodePreview({group, section, app, openApp}) {

    let [isReady, setIsReady] = useState(false);
    let [currentCode, setCurrentCode] = useState(null);

    const [translate] = useEducaLocalizedStrings()

    let init = () => {
        setIsReady(false)
        AjaxHelper.getGroupAccessCode(group.id)
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.code) {
                    setCurrentCode(resp.payload.code)
                }
                else
                    throw new Error("")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "QR Code konnte nicht geladen werden.")
            })
            .finally(() => {
                setIsReady(true)
            })
    }

    useEffect(() => {
        init()
    }, [group,section]);



    return !isReady || !currentCode ?<EducaShimmer/> : <div>

                <Card>
                    <Card.Body>
                        <div className={"row"}>
                            <div className={"col-4"}>
                                <QRCode
                                    className={"m-2"}
                                    renderAs={"svg"}
                                    bgColor={"#FFFF"}
                                    size={120}
                                    value={"educa://join/" + currentCode.code}></QRCode>
                            </div>
                            <div className="col-8 text-center">

                                <h4>{translate("access_code","Zugangscode:")}</h4>
                                <h2>{currentCode.code}</h2>
                                <p>{translate("access_code.info","Gebe diesen Code ein, um dich bei educa zu registrieren. Falls du bereits einen Account hast, kannst du über diesen Code weitere Personen in die Gruppe einladen.")}</p>
                            </div>
                        </div>
                    </Card.Body>
                </Card>
                </div>
}

