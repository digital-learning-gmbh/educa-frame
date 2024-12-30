import React, {Component, useEffect, useState} from 'react';
import EducaFileBrowser from "../../educa-components/EducaFileBrowser/EducaFileBrowser";
import {connect} from "react-redux";
import {MODELS} from "../../../shared/shared-helpers/SharedHelper";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import EducaFileBrowserAdvanced from "../../educa-components/EducaFileBrowser/EducaFileBrowserAdvanced.jsx";
import {Card} from "react-bootstrap";


const SectionDocumentsView = (props) =>
{

    let [isMounted, setIsMounted] = useState(false)

    useEffect(() =>
    {
        setIsMounted(true)
        return () => setIsMounted(false)
    },[])


    if(!isMounted)
        return null
    return (
        <>
            <Card>
            <EducaFileBrowserAdvanced
                modelType={MODELS.SECTION}
                modelId={props.section.id}
                canUserUpload={FliesentischZentralrat.sectionFilesUpload(props.section)}
                canUserEdit={FliesentischZentralrat.sectionFilesEdit(props.section)}
                hasNavigationbar={true}
                hasSearchbar={false}
                hasAISearchbar={true}
            />
        </Card>
</>
)
    ;
}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(SectionDocumentsView);
