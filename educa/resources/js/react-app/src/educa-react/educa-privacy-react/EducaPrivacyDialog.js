import React, {Component, useEffect, useState} from 'react';
import { Button, Card, Col, Container, Row } from 'react-bootstrap';
import { useDispatch, useSelector } from 'react-redux';
import { EducaLoading } from '../../shared-local/Loading';
import AjaxHelper from '../helpers/EducaAjaxHelper';
import EducaHelper from '../helpers/EducaHelper';
import { GENERAL_SET_CURRENT_CLOUD_USER } from '../reducers/GeneralReducer';


export default function EducaPrivacyDialog(props){

    let [loading, setLoading] = useState(false);
    let [privacyText, setPrivacyText] = useState("");
    let [headline, setHeadline] = useState("Datenschutzerklärung");

    let tenant = useSelector(s => s.tenant)
    
    const dispatch = useDispatch()
    const setMe = (currentUser) => {
        dispatch({
            type: GENERAL_SET_CURRENT_CLOUD_USER,
            payload: currentUser
        })
    } 
    

    useEffect(() => {
        loadPrivacySettings();
    },[])

    let loadPrivacySettings = () => {
       setLoading(true)
        AjaxHelper.loadPrivacy()
            .then(resp => {
                if (resp.status > 0) {
                    setPrivacyText(resp.payload.html_text)
                    setHeadline(resp.payload.title)
                } else
                    throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Die Datenschutzerklärung konnte nicht geladen werden." + err.message)
            }).finally(() => {
                setLoading(false)
            })
    }

    let acceptPrivacy = () => {
        setLoading(true)
        AjaxHelper.acceptPrivacy()
        .then(resp => {
            if (resp.status > 0 && resp.payload) {
                EducaHelper.fireSuccessToast("Erfolg", "Die Datenschutzerklärung wurde akzeptiert.")
                setMe(resp.payload.user)
            } else
                throw new Error(resp.message)
        })
        .catch(err => {
            EducaHelper.fireErrorToast("Fehler", "Die Datenschutzerklärung konnte nicht akzeptiert werden." + err.message)
        }).finally(() => {
            setLoading(false)
        })
    }

    return loading ? <EducaLoading/> :
    <div className='educa-main-container'>
    <Row>
    <Col md={2} lg={2} xl={4}  className={"d-none d-md-block"}>
    <img
                            src={tenant  && tenant.logo ? "/storage/images/tenants/" + tenant.logo : "/images/neural.svg"}
                            height="150"
                            className="d-inline-block align-top"
                            alt=""
                        />{" "}
                        <h2>{headline}</h2>

        <p className="text-muted">Bitte lesen Sie die Datenschutzerklärung durch und akzeptieren diese, bevor Sie '{tenant ? (tenant?.hideLogoText ? "" : tenant.name) : (tenant?.hideLogoText == true ? "" : "educa")}' nutzen können.</p>
    </Col>
            <Col  md={10} lg={10} xl={8} style={{backgroundColor: "#fff"}}>
   
  
        <div style={{minHeight: "40vh", marginTop: "100px", maxHeight: "calc(100vh - 300px)", overflowY:"scroll"}} className='animate__fadeIn animate__animated' dangerouslySetInnerHTML={{__html: privacyText}}>
        </div>
        <div className='d-flex justify-content-end'>
        <Button className='m-1' variant="outline-dark" onClick={() => {
              AjaxHelper.logout();
              EducaHelper.fireInfoToast(
                  "Logout",
                  "Erfolgreich ausgeloggt"
              );
        }}>{headline} ablehnen</Button>
        <Button className='m-1' variant="primary" onClick={() => acceptPrivacy()}>{headline} akzeptieren</Button>
    </div>
    </Col>
    </Row></div>
}