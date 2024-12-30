import MainHeading from "../../educa-home-react/educa-learner-components/MainHeading.js";
import React from "react";


export default function EducaPassEntry({title, value}) {

    return <>
        <h6>{title}</h6>
        <MainHeading>{value}</MainHeading>
    </>
}
