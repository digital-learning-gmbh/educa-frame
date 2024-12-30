import { Card } from "react-bootstrap";
import TimetableView from "../../educa-components/EducaTimetableView/TimetableView";
import EducaClassbook from "../../educa-classbook-react/EducaClassbook";
import React, { useState } from "react";

export default function GroupTimetableView(props) {
    const [timetableEventSelected, setTimetableEventSelected] = useState(false);
    const [eventToEdit, setEventToEdit] = useState(null);

    return (
        <Card>
            <Card.Body>
                <div>
                    <div className={"d-flex"}>
                        <div className={"col"} style={{ padding: "0px" }}>
                            <TimetableView
                                entityId={props.group.schoolclass.id}
                                entityType={"schoolclass"}
                                onEventClicked={selected => {
                                    setTimetableEventSelected(true);
                                    setEventToEdit(selected);
                                }}
                            />
                        </div>
                        {timetableEventSelected ? (
                            <div className="col-6">
                                <EducaClassbook event={eventToEdit} />
                            </div>
                        ) : null}
                    </div>
                </div>
            </Card.Body>
        </Card>
    );
}
