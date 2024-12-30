import React, {Component, useEffect, useState} from 'react';
import {Card, ListGroup, ListGroupItem} from "react-bootstrap";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {RCHelper} from "../../rocket-chat-components/RocketChatHelper";
import EducaHelper from "../../helpers/EducaHelper";
import {getEducaChatMessageListGroupContent} from "../../educa-components/RCList";
import moment from "moment";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";

export function SupportTicketList(props) {

    let [loading, setLoading] = useState(false)
    let [tickets, setTickets] = useState([]);
    let [activeTicket, setActiveTicket] = useState(null)

    let loadSupportTickets = () => {
        AjaxHelper.getSupportTickets()
            .then(resp => {
                if (resp?.payload?.tickets) {
                    setTickets(resp.payload.tickets);
                } else
                    throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler beim Laden der Supporttickets", err.message)
            })
    }

    useEffect(() => {
        loadSupportTickets();
    }, []);

    useEffect(() => {
        props.callback(activeTicket);
    }, [activeTicket]);

    return <ListGroup variant={"flush"}>
        {tickets.length === 0 ?
            <ListGroup.Item>
                <div>Noch keine Supportanfragen</div>
            </ListGroup.Item> :
            tickets.map(function (ticket) {

                return <div key={ticket.id}>
                    <ListGroupItem
                        active={activeTicket?.id === ticket.id}
                        onClick={() => {
                            setActiveTicket(ticket)
                        }}
                    >
                        <div
                            style={{
                                display: "flex",
                                flex: 1,
                                width: "100%",
                                flexDirection: "column",
                            }}
                        >
                            <div
                                style={{
                                    display: "flex",
                                    flex: 1,
                                    width: "100%",
                                    flexDirection: "row"
                                }}
                            >
                                <div
                                    style={{
                                        display: "flex",
                                        flex: 1,
                                        flexDirection: "column"
                                    }}>

                                    <div
                                        style={{
                                            fontSize: "15px",
                                            lineHeight: "1.5em",
                                            marginRight: "5px",
                                            height: "1.5em",
                                            maxWidth: "275px",
                                            textOverflow: "ellipsis",
                                            whiteSpace: "nowrap",
                                            overflow: "hidden",
                                        }}>
                                        {ticket.title}
                                    </div>

                                    <div
                                        className={"grey"}
                                        style={{
                                            display: "flex",
                                            flex: 1,
                                            flexDirection: "row",
                                            maxWidth: "300px"
                                        }}>
                                        <div
                                            className={"grey"}
                                            style={{
                                                fontSize: "15px",
                                                lineHeight: "1.5em",
                                                marginRight: "5px",
                                                height: "1.5em",
                                                maxWidth: "275px",
                                                textOverflow: "ellipsis",
                                                whiteSpace: "nowrap",
                                                overflow: "hidden",
                                            }}>
                                            {ticket.body}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ListGroupItem></div>
            })
        }
    </ListGroup>


}
