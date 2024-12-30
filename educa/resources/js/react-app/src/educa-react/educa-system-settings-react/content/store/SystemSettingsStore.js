import React, {createRef, useEffect, useState} from 'react';
import {EducaLoading} from "../../../../shared-local/Loading";
import Card from "react-bootstrap/Card";
import {EducaCircularButton, EducaDefaultTable} from "../../../../shared/shared-components";
import Button from "react-bootstrap/Button";
import {Alert} from "react-bootstrap";
function SystemSettingsStore(props) {

    let [loading, setLoading] = useState(false);

    return (
        loading ? <EducaLoading/> :
            <Card style={{backgroundColor: "white"}}>
                <Card.Header>
                    <div style={{flex : 1, display : "flex", flexDirection :"row"}}>
                        <h5 className="card-title">
                            <b><i className="fas fa-shopping-basket"></i> Produkte</b>
                        </h5>
                    </div>
                </Card.Header>
                <Card.Body>
                    <Alert variant={"info"}>Der Store wurde durch Administrator-Richtlinien deaktiviert.</Alert>
                </Card.Body>
            </Card>
    )
}
export default SystemSettingsStore;
