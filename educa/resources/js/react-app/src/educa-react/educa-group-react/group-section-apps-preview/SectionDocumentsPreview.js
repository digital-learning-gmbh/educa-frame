import React, {Component, useEffect, useState} from 'react';
import EducaFileBrowser from "../../educa-components/EducaFileBrowser/EducaFileBrowser";
import {connect} from "react-redux";
import {MODELS} from "../../../shared/shared-helpers/SharedHelper";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import Button from "react-bootstrap/Button";
import {useHistory} from "react-router";
import EducaFileBrowserAdvanced from "../../educa-components/EducaFileBrowser/EducaFileBrowserAdvanced.jsx";
import {Card} from "react-bootstrap";


const SectionDocumentsPreview = ({section, app, openApp}) => {

    let [isMounted, setIsMounted] = useState(false)

    const history = useHistory()

    useEffect(() => {
        setIsMounted(true)
        return () => setIsMounted(false)
    }, [])


    if (!isMounted)
        return null
    return (
        <Card>
        <EducaFileBrowserAdvanced
            modelType={MODELS.SECTION}
            modelId={section.id}
            hasNavigationbar={true}
            canUserUpload={FliesentischZentralrat.sectionFilesUpload(section)}
            canUserEdit={FliesentischZentralrat.sectionFilesEdit(section)}
        /></Card>
    );
}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(SectionDocumentsPreview);
