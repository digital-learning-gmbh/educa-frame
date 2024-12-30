import React, {useState} from 'react';
import EducaLearnerViewReact from "./EducaLearnerViewReact";
import {ClassbookMarkWidget} from "../educa-classbook-react/widgets/ClassbookMarkWidget.js";
import {ClassbookExamList} from "../educa-classbook-react/widgets/ClassbookExamList.js";
import {ClassbookAbsenteeism} from "../educa-classbook-react/widgets/ClassbookAbsenteeism.js";
import {ClassbookReport} from "../educa-classbook-react/widgets/ClassbookReport.js";


export default function EducaHomeViewReact(props) {

    return <div style={{minHeight: "80vh"}}>
        <div class={"row mt-2"}>
            <div className={"col-6"}>
                <ClassbookMarkWidget/>
            </div>
            <div className={"col-6"}>
                <ClassbookExamList/>
            </div>
        </div>
        <div class={"row mt-2"}>
            <div className={"col-6"}>
                <ClassbookAbsenteeism/>
            </div>
            <div className={"col-6"}>
                <ClassbookReport/>
            </div>
        </div>
    </div>;
}
