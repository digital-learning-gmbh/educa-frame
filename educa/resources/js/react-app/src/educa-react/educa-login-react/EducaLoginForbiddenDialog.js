import React, {Component, useEffect, useState} from 'react';
import { Button, Card, Container } from 'react-bootstrap';
import { useDispatch, useSelector } from 'react-redux';
import { EducaLoading } from '../../shared-local/Loading';
import AjaxHelper from '../helpers/EducaAjaxHelper';
import EducaHelper from '../helpers/EducaHelper';
import { GENERAL_SET_CURRENT_CLOUD_USER } from '../reducers/GeneralReducer';
import "./styles.css";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper";
import {EducaPrimaryButton} from "../../shared/shared-components/Buttons";

export default function EducaLoginForbiddenDialog(props){

    let tenant = useSelector(s => s.tenant)
    const [translate] = useEducaLocalizedStrings()

    const dispatch = useDispatch()
    const setMe = (currentUser) => {
        dispatch({
            type: GENERAL_SET_CURRENT_CLOUD_USER,
            payload: currentUser
        })
    }


    let onLogoutButtonClicked = () => {
        AjaxHelper.logout();
        EducaHelper.fireInfoToast(
            "Logout",
            "Erfolgreich ausgeloggt"
        );
    }


    return <div className="loginContainer">
        <div className="container card">
            <div className="">
                <div className="row no-gutter">
                    <div className="col-md-6 d-none d-md-flex bg-image" style={{
                        backgroundImage: tenant && tenant.coverImage ? "url('/storage/images/tenants/" + tenant.coverImage + "')"  : "url('/images/nlq_background.jpg')"}}>

                    </div>
                    <div className="col-md-6 bg-light">
                        <div className="login d-flex align-items-center mx-auto">
                            <div className="form-signin">
                                <div
                                    className="h3 mb-3 font-weight-normal"
                                    style={{
                                        display: "flex",
                                        alignItems: "center"
                                    }}
                                >
                                    <img
                                        src={
                                            tenant &&
                                            tenant.logo
                                                ? "/storage/images/tenants/" +

                                                    tenant.logo
                                                : "/images/neural.svg"
                                        }
                                        height="50"
                                        className="d-inline-block align-top"
                                        alt=""
                                    />
                                    <span>
                                                {" "}
                                        {tenant
                                            ? tenant
                                                ?.hideLogoText
                                                ? ""
                                                :
                                                    tenant.name
                                            : tenant
                                                ?.hideLogoText
                                                ? ""
                                                : "educa"}
                                            </span>
                                </div>
                                <h1 className="h5 mb-3 font-weight-normal">
                                    {translate(
                                        "login.forbidden.headline",
                                        "Dein Account wurde noch nicht freigeschaltet. Versuche es später erneut."
                                    )}
                                </h1>
                                <EducaPrimaryButton
                                    onClick={() =>
                                        onLogoutButtonClicked()
                                    }
                                    className="btn-lg btn-block mt-2"
                                >
                                    {translate(
                                        "logout",
                                        "Abmelden"
                                    )}
                                </EducaPrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
}
