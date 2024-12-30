import React, {useState} from 'react';
import MainHeading from "./educa-learner-components/MainHeading";
import HomeBadgeCard from "./HomeBadgeCard";
import MutedParagraph from "./educa-learner-components/MutedParagraph";
import FilterPanel from "./educa-learner-components/FilterPanel";
import Carousel from "./educa-learner-components/Carousel";
import HomeBadgeLearner from "./educa-learner-components/HomeBadgeLearner";
import CardSectionImageTop from "./educa-learner-components/CardSectionImageTop";
import CardSectionImageLeft from "./educa-learner-components/CardSectionImageLeft";
import HomeFeedLearner from "./educa-learner-components/HomeFeedLearner";
import HomeViewTaskLearnerCard from "./educa-learner-components/HomeViewTaskLearnerCard";
import HomeViewEventLearnerCard from "./educa-learner-components/HomeViewEventLearnerCard";
import HomeViewSectionsCard from './educa-learner-components/HomeViewSectionsCard';
import HomeViewLastSectionsCard from "./educa-learner-components/HomeViewLastSectionsCard";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper.js";

export default function EducaLearnerViewReact(props) {

    const [translate] = useEducaLocalizedStrings()
    let [filter, setFilter] = useState(null);

    return <div className={"container-fluid"}> <div className={"row"}>
        <div className={"col-8 mt-4"}>
            <FilterPanel filter={filter} setFilter={setFilter}/>
            <HomeViewSectionsCard filter={filter}/>
            <HomeViewLastSectionsCard filter={filter}/>

            <MainHeading>{translate("appointments_and_tasks","Termine & Aufgaben")}</MainHeading>
            <div className={"row"}>
                <div className={"col-6"}>
                    <HomeViewEventLearnerCard/>
                </div>
                <div className={"col-6"}>
                    <HomeViewTaskLearnerCard/>
                </div>
            </div>
        </div>
        <div className={"col-4 mt-4"}>
            <HomeBadgeLearner/>
            <HomeFeedLearner/>
        </div>
    </div>
    </div>
}
