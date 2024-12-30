import React, {useEffect, useState} from "react";
import {Button} from "react-bootstrap";
import "./MeetingLiveInforamtion.css"
import AjaxHelper from "../helpers/EducaAjaxHelper.js";
import SharedHelper from "../../shared/shared-helpers/SharedHelper.js";
export default function MeetingLiveInformation({model_id, model_type, className})
{
    let [personCount, setPersonCount] = useState(0);

    useEffect(() =>
    {
        loadMeetingInformation()
    }, [model_type,model_id])


    let loadMeetingInformation = () =>
    {
        AjaxHelper.liveInfoMeeting(model_type, model_id)
            .then( resp =>
            {
                if(resp.status > 0 )
                {
                    setPersonCount(resp.payload?.personCount)
                    return
                }
                throw new Error(resp.message)
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Meeting-Infos konnte nicht verarbeitet werden.")
            })
    }

    return <Button className={className} variant={"outline-dark"}>
        <div className={"d-flex"}>        <div className="blob"></div>
            {personCount??0} Personen online
        </div>
    </Button>
}
