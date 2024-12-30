import React, {Component, useEffect, useRef, useState} from 'react';
import MainHeading from "../educa-home-react/educa-learner-components/MainHeading.js";
import MutedParagraph from "../educa-home-react/educa-learner-components/MutedParagraph.js";
import {useSelector} from "react-redux";
import {Card} from "react-bootstrap";
import ProfilBadgeLearner from "./components/ProfilBadgeLearner.js";
import HomeViewSectionsCard from "../educa-home-react/educa-learner-components/HomeViewSectionsCard.js";
import HomeViewLastSectionsCard from "../educa-home-react/educa-learner-components/HomeViewLastSectionsCard.js";
import ProfilCardImageTop from "./components/ProfilCardImageTop.js";
import EducaModal from "../../shared/shared-components/EducaModal.js";


export default function EducaProfilReact(props) {

    let user = useSelector(s => s.currentCloudUser)

    return <div className={"container-fluid"}>
        <div className={"mt-4"}>
            <MainHeading>Profil: {user?.name}</MainHeading>
            <MutedParagraph>In deinem Profil findest du alle besuchten Kurse, Abzeichen und deinen persönlichen educa-Pass.</MutedParagraph>
        </div>
        <div className={"row"}>
            <div className={"col-2 mt-4"}>
                <ProfilCardImageTop cloudUser={user}/>
            </div>
            <div className={"col-10 mt-4"}>
                <ProfilBadgeLearner/>
                <HomeViewSectionsCard/>

                <MainHeading>Deine Aktivitäten</MainHeading>
                <MutedParagraph>Hier findest du eine Liste deiner letzten Aktivitäten auf der Plattform</MutedParagraph>

            </div>
        </div>
    </div>

}
