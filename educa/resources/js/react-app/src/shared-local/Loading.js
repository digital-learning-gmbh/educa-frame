import React from 'react'
import "./Loading.css"
import {useTranslation} from "react-i18next";
import {useSelector} from "react-redux";

export function EducaLoading(props) {


    const tenant = useSelector( s => s.tenant)

    const tr = useTranslation();
    return (<div className={"animate__animated animate__fadeIn"}>{tenant && tenant.logo && tenant.overrideLoadingAnimation == true ?   <div className={"loadImage text-center d-flex align-items-center flex-column"}><img
        src={tenant.logo ? "/storage/images/tenants/" + tenant.logo : "/images/neural.svg"}
        height="100"
        alt=""
    />
        <div className="lds-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div> : <div className="load" style={props.style}>
        <hr/>
        <hr/>
        <hr/>
        <hr/>
    </div> }
    <h2 className="loadbrand text-muted text-center">{tenant ? tenant.name : "Lade..."}</h2>
    </div> )
}
