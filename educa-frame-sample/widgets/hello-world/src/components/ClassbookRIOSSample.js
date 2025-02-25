import React, { useState } from "react";
import { Card } from "react-bootstrap";
import Button from "react-bootstrap/Button";
import Form from "react-bootstrap/Form";
import SharedHelper from "educa-react-commons/dist/SharedHelper";
import EducaRIOSHelper from "educa-react-commons/dist/EducaRIOSHelper";

function ClassbookRIOSSample() {
  let [selfService, setSelfService] = useState();
  let [content, setContent] = useState();
  let [data, setData] = useState();

  let sendRIOSCommand = () => {
    EducaRIOSHelper.callRIOSCommand(selfService, content)
      .then(function (resp) {
        if (resp.payload?.data) {
          setData(resp.payload?.data);
          return;
        }
        throw new Error(resp?.message);
      })
      .catch(() => {
        SharedHelper.fireErrorToast(
          "Fehler",
          "Der Aufruf zu RIOS ist fehlgeschlagen."
        );
      });
  };

  return (
    <Card>
      <Card.Header style={{ backgroundColor: "#fff" }}>
        <h5 className="card-title">
          <b> RIOS Test</b>
        </h5>
      </Card.Header>
      <Card.Body>
        <Form.Group controlId="selfServiceName">
          <Form.Label>SelfService Name</Form.Label>
          <Form.Control
            type="text"
            value={selfService}
            onChange={e => setSelfService(e.target.value)}
            placeholder="Enter self-service name"
          />
        </Form.Group>

        <Form.Group controlId="content" className="mt-3">
          <Form.Label>Content</Form.Label>
          <Form.Control
            as="textarea"
            rows={3}
            value={content}
            onChange={e => setContent(e.target.value)}
            placeholder="Enter content"
          />
        </Form.Group>
        <pre>{JSON.stringify(data, null, 2)}</pre>
      </Card.Body>
      <Card.Footer>
        <Button className={"m-1"} onClick={sendRIOSCommand}>
          Absenden
        </Button>
      </Card.Footer>
    </Card>
  );
}

export default ClassbookRIOSSample;
