import React, {useState} from 'react';
import AjaxHelper from "../../helpers/EducaAjaxHelper.js";
import MainHeading from "../../educa-home-react/educa-learner-components/MainHeading.js";
import EducaPassEntry from "./EducaPassEntry.js";
import {Navbar} from "react-bootstrap";
import {useSelector} from "react-redux";
import Button from "react-bootstrap/Button";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper.js";

export default function EducaPass({cloudUser}) {

    let tenant = useSelector(s => s.tenant)

    return <div>
        <div className={"row"}>
            <Navbar.Brand className="tenant-logo-navbar ml-4" href="#">
                <img
                    src={
                        tenant &&
                        tenant.logo
                            ? "/storage/images/tenants/" +
                            tenant.logo
                            : "/images/neural.svg"
                    }
                    height="30"
                    className="d-inline-block align-top"
                    alt=""
                />{" "}
                {tenant
                    ? tenant?.hideLogoText
                        ? ""
                        : tenant.name
                    : tenant?.hideLogoText === true
                        ? ""
                        : "educa"}
            </Navbar.Brand>
        </div>
        <div className={"row"}>
            <div className={"col-8"}>
                <div className={"row"}>
                    <div className={"col-8"}>
                        <EducaPassEntry title={"Name"} value={cloudUser?.name}/>
                    </div>
                    <div className={"col-4"}>
                        <EducaPassEntry title={"TN-Nummer"} value={"232323"}/>
                    </div>
                </div>
                <div className={"row"}>
                    <div className={"col-8"}>
                        <EducaPassEntry title={"Geburtsdatum"} value={"24.05.1996"}/>
                    </div>
                    <div className={"col-4"}>
                        <EducaPassEntry title={"Ausweis gültig bis"} value={"232323"}/>
                    </div>
                </div>
                <EducaPassEntry title={"Maßnahme"} value={"Ausbildung Fachinformatiker"}/>
                <EducaPassEntry title={"Unterbringung"} value={"-"}/>


                <Button variant={"primary"} onClick={() => {
                    window.open("/api/v1/profile/educaPass/download/apple?token=" + SharedHelper.getJwt() , '_blank').focus();
                }}>Herunterladen Apple</Button>

                <Button variant={"primary"} onClick={() => {
                    window.open("/api/v1/profile/educaPass/download/android?token=" + SharedHelper.getJwt() , '_blank').focus();
                }}>Herunterladen Android</Button>
            </div>
            <div className={"col-4 d-flex justify-content-center"}>
                <img className="card-img-top" src={AjaxHelper.getCloudUserAvatarUrl(
                    cloudUser?.id,
                    200,
                    cloudUser?.image
                )} loading={"lazy"} alt="Card image" style={{
                    borderRadius: "calc(1rem - 1px)",
                    objectFit: "cover",
                    width: "200px",
                    height: "350px"
                }}></img>
            </div>

        </div>
    </div>
}
