import React, {useState} from 'react';
import Card from "react-bootstrap/Card";
import {ListGroup} from "react-bootstrap";
import {EducaCardLinkButton} from "../../shared/shared-components/Buttons";
import {BASE_ROUTES} from "../App";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import moment from "moment";
import EducaHelper from "../helpers/EducaHelper";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper.js";


const MAX_EVENTS = 3
export default function HomeViewAddressbookCard(props) {
    const [translate] = useEducaLocalizedStrings()

    return (
        <Card className="mb-2">
            <Card.Body style={{paddingBottom: "0px"}}>
                <Card.Title><h4><img style={{width: "40px", height: "40px"}}
                                     src="/images/phone-book.png"/> {translate("navbar.addressbook","Adressbuch")}
                    <EducaCardLinkButton
                        onClick={() => props.changeRoute(BASE_ROUTES.ROOT_CONTACTS, "")}
                        className="card-link m-1" style={{fontSize: "0.9rem"}}>Alle ansehen</EducaCardLinkButton></h4>
                </Card.Title>
                <p>{translate("home_view.addressbook","Du möchtest jemanden kontaktieren? Alle wichtigen Kontakte findest du im Adressbuch.")}</p>
            </Card.Body>
        </Card>
    );
}

