import React, {useRef, useState} from 'react';
import {Card, ListGroup} from "react-bootstrap";
import AjaxHelper from "../../helpers/EducaAjaxHelper.js";
import EducaModal from "../../../shared/shared-components/EducaModal.js";
import {useHistory} from "react-router";
import {BASE_ROUTES} from "../../App.js";
import EducaPass from "./EducaPass.js";


export default function ProfilCardImageTop({isLoading, title, image, subtitle, headline, cloudUser, onClick}) {

    let modal = useRef()
    let history = useHistory()

    return <div onClick={onClick} className="card m-1" style={{borderRadius: "1rem", cursor: "pointer"}}>
        { isLoading ?   <div className="box shine card-img-top" style={{
                    height: "150px",
                    width: "100%",
                    borderTopLeftRadius: "calc(1rem - 1px)", borderTopRightRadius: "calc(1rem - 1px)"
                }}/> :
        <img className="card-img-top" src={AjaxHelper.getCloudUserAvatarUrl(
            cloudUser?.id,
            200,
            cloudUser?.image
        )} loading={"lazy"} alt="Card image" style={{
            borderTopLeftRadius: "calc(1rem - 1px)", borderTopRightRadius: "calc(1rem - 1px)",
                objectFit: "cover",
                width: "100%",
                height: "200px"
        }}></img> }
            <div className="card-body">
            { isLoading ? <>
                            <div className="linesHolder">
                <div className="line shine" style={{width: "200px"}}/>
                <div className="line shine"  style={{width: "200px"}}/>
            </div>
                        </> : <>
                <h4 className="card-title" style={{fontWeight: 600}}>{cloudUser?.name}</h4>
                <p className="card-text">{cloudUser?.email}</p></> }


                <ListGroup variant={"flush"}>
                    <ListGroup.Item onClick={() => history.push(BASE_ROUTES.ROOT_SETTINGS)}><i className="fas fa-tools"></i> Einstellungen</ListGroup.Item>
                    <ListGroup.Item onClick={() => modal.current.open(() => {}, "educa-Pass",<EducaPass cloudUser={cloudUser}/>)}><i className="fas fa-passport"></i> educa-Pass</ListGroup.Item>
                </ListGroup>
            </div>

        <EducaModal size={"lg"} ref={modal}/>
    </div>;
}
