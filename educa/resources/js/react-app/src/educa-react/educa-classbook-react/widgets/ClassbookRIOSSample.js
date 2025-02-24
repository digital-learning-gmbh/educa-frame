import React, {Component, useEffect, useRef, useState} from 'react';
import {Alert, Card, Container, Row} from "react-bootstrap";
import {useSelector} from "react-redux";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import ReactTabulator from "react-tabulator/lib/ReactTabulator";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import Select from "react-select";
import Button from "react-bootstrap/Button";
import {SelectPlaceholder} from "../../helpers/EducaHelper";
import EducaAjaxHelper from "../../helpers/EducaAjaxHelper";
import Form from "react-bootstrap/Form";

function ClassbookRIOSSample() {

    let [selfService, setSelfService] = useState();
    let [content, setContent] = useState();
    let [data, setData] = useState();


    let sendRIOSCommand = () => {
        EducaAjaxHelper.callRIOSCommand(selfService, content)
            .then(function (resp)
            {
                if(resp.payload?.data)
                {
                    setData(resp.payload?.data);
                    return
                }
                throw new Error(resp?.message)
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Der Aufruf zu RIOS ist fehlgeschlagen.")
            })
    }



    return <Card>
        <Card.Header style={{ backgroundColor: "#fff"}}>
            <h5 className="card-title"><b> RIOS Test</b>
            </h5>
        </Card.Header>
        <Card.Body>
            <Form.Group controlId="selfServiceName">
                <Form.Label>SelfService Name</Form.Label>
                <Form.Control
                    type="text"
                    value={selfService}
                    onChange={(e) => setSelfService(e.target.value)}
                    placeholder="Enter self-service name"
                />
            </Form.Group>

            <Form.Group controlId="content" className="mt-3">
                <Form.Label>Content</Form.Label>
                <Form.Control
                    as="textarea"
                    rows={3}
                    value={content}
                    onChange={(e) => setContent(e.target.value)}
                    placeholder="Enter content"
                />
            </Form.Group>
            <pre>{JSON.stringify(data, null, 2)}</pre>
        </Card.Body>
        <Card.Footer>
            <Button className={"m-1"}
                    onClick={sendRIOSCommand}
            >Absenden</Button>
        </Card.Footer>
    </Card>

}

export default ClassbookRIOSSample;
