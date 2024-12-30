import React from "react";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import {Card, ListGroup} from "react-bootstrap";
import {GROUP_VIEWS} from "../GroupBrowse";


export default function GroupBrowseExternalIngetration(props) {


    return <>
        {props.group?.external_integrations?.length > 0 ? <>
                <Card.Body style={{paddingBottom: "0px"}}>
                    <Card.Title>
                        <div
                            style={{
                                display: "flex",
                                flexDirection: "row"
                            }}
                        >
                            <div
                                style={{
                                    display: "flex",
                                    flexDirection: "column",
                                    justifyContent: "center",
                                    fontWeight: "700",
                                    color: SharedHelper.isColorTooDark(
                                        props.group.color
                                    )
                                        ? "#f8f9fa !important"
                                        : "#343a40 !important",
                                    fontSize: "1.125rem"
                                }}
                            >
                                {props.t("group.integrations","Integrationen")}
                            </div>
                        </div>
                    </Card.Title>
                </Card.Body>
                <ListGroup bg={"transparent"} variant={"flush"}>
                    { props.group?.external_integrations?.map(function (external_integration) {
                    return <ListGroup.Item
                        key={external_integration.id}
                        style={{
                            cursor: "pointer"
                        }}
                        active={props.groupView === GROUP_VIEWS.INTEGRATION && props.integration?.id === external_integration?.id}
                        onClick={() =>
                            props.navigate([props.group.id, GROUP_VIEWS.INTEGRATION,external_integration?.id])
                        }
                        className={"bg-transparent border-0 d-flex"}
                    >
                        <div style={{width : "30px", height:"30px", border: "solid #fff 3px",  backgroundImage: "url('" + external_integration?.icon + "')", backgroundColor: "#fff",borderRadius: "2px",
                            backgroundSize: "contain",
                            backgroundPosition: "center", backgroundRepeat: "no-repeat"}}>
                        </div> <div className={"ml-1"}>{external_integration?.displayName}</div>
                    </ListGroup.Item> })
                    }
                </ListGroup></>
            : null}
    </>
}
