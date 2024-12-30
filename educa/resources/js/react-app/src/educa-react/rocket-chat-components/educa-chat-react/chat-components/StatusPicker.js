import React, {useEffect, useState} from 'react';
import {Dropdown, NavDropdown} from "react-bootstrap";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import {RCHelper} from "../../RocketChatHelper";
import EducaHelper from "../../../helpers/EducaHelper";

import "react-bootstrap-submenu/dist/index.css";
import {DropdownSubmenu} from "react-bootstrap-submenu";
export const RC_STATUS = {
    online: "Online",
    busy: "Beschäftigt",
    away: "Abwesend",
    offline: "Offline"
}

function StatusPicker(props) {


    let [me, setMe] = useState(props.me)
    let [sound, setSound] = useState(false)

    useEffect(() => {
        //console.log(me)
        setMe(props.me)
        setSound(localStorage.getItem("disableSound") == 'true')
    }, [props.me])


    let changeSound = (sound) => {
        if(!sound)
        {
            localStorage.removeItem('disableSound')
        } else {
            localStorage.setItem('disableSound', 'true')
        }
        setSound(sound)
    }

    let setStatus = (status) => {
        RCHelper.setStatus(status)
            .then(resp => {
                if (!resp.success)
                    throw new Error("Could not set Status")
            })
            .catch(err => {
                SharedHelper.logError("Fehler", "Status konnte nicht gesetzt werden." + err.message)
            })
    }

    var dropDown = React.forwardRef(({children, onClick}, ref) => (

        <div className="mt-2" style={{display: "flex", flexDirection: "row", justifyContent: "center"}}
             ref={ref}
             onClick={(e) => {
                 e.preventDefault();
                 onClick(e);
             }}>
            {EducaHelper.getStatusImage(me.status)}
            <div>
            </div>
        </div>
    ));

    return (
        <DropdownSubmenu
            key={"statuspicker"}
            title={<div style={{display: "flex", flexDirection: "row", justifyContent: "start"}}>{EducaHelper.getStatusImage(me.status)} {RC_STATUS[me.status]}</div>}
        >
                {Object.keys(RC_STATUS).map((key) => {
                    return <NavDropdown.Item
                        key={key}
                        onClick={() => setStatus(key)}
                    >
                        <div style={{display: "flex", flexDirection: "row"}}>
                            {EducaHelper.getStatusImage(key)}
                            <div>
                                {RC_STATUS[key]}
                            </div>
                        </div>
                    </NavDropdown.Item>

                })}
                <NavDropdown.Divider/>
                <NavDropdown.Item
                    key={"soundOn"}
                    onClick={() => changeSound(!sound)}
                >
                    { !sound? <div style={{display: "flex", flexDirection: "row"}}>
                        <div title={"Chat-Ton ausschalten"}
                             style={{display: "flex", flexDirection: "column", justifyContent: "center", marginRight: "5px"}}>
                        <i className="fas fa-volume-mute"></i>
                        </div>
                        <div>
                            Chat-Ton ausschalten
                        </div>
                    </div> :
                        <div style={{display: "flex", flexDirection: "row"}}>
                            <div title={"Chat-Ton einschalten"}
                                 style={{display: "flex", flexDirection: "column", justifyContent: "center", marginRight: "5px"}}>
                            <i className="fas fa-volume-up"></i>

                            </div>
                            <div>
                                Chat-Ton einschalten
                            </div>
                        </div>
                    }
                </NavDropdown.Item>
        </DropdownSubmenu>
    );

}

export default StatusPicker;
