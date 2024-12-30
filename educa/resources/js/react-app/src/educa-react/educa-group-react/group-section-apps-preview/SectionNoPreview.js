import React, {Component, useEffect, useState} from 'react';
import {Card} from "react-bootstrap";
import MutedParagraph from "../../educa-home-react/educa-learner-components/MutedParagraph";
import Button from "react-bootstrap/Button";
import {useHistory} from "react-router";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper.js";

export default function SectionNoPreview({openApp, app})
{
    const [translate] = useEducaLocalizedStrings()

    return <Card className={"m-2 p-2"}>
        <div>
            <MutedParagraph><i className="fas fa-info-circle"></i>{translate("group_view.no_videoconference","Diese App hat leider keine Vorschau. Bitte klicke hier, um die App zu \u00f6ffnen.")}</MutedParagraph>
        <Button variant={"outline-dark"} onClick={() => openApp(app)}>App "{translate(`group.view.${app?.name}`, app?.name)}" {translate("group_view.open_app","\u00f6ffnen")}</Button>
        </div>
    </Card>
}
