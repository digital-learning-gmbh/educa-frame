import React, {useEffect, useState} from 'react';
import MainHeading from './MainHeading';
import MutedParagraph from './MutedParagraph';
import CardSectionImageTop from './CardSectionImageTop';
import { useSelector } from 'react-redux';
import EducaHelper from '../../helpers/EducaHelper';
import AjaxHelper from '../../helpers/EducaAjaxHelper';
import Carousel from "./Carousel";
import { useEducaLocalizedStrings } from '../../helpers/StringLocalizationHelper';
import CardSectionImageLeft from "./CardSectionImageLeft";
import {useHistory} from "react-router";


export default function HomeViewLastSectionsCard(props) {


    const [loading, setLoading] = useState(true);
    const [sections, setSections] = useState(null);
    const [translate] = useEducaLocalizedStrings()
    const history = useHistory()

    let _isMounted = false;
    React.useEffect(() => {
        _isMounted = true;
        getSections();
        return () => {
            _isMounted = false;
        };
    }, []);



    let getSections = () => {
        setLoading(true)
        AjaxHelper.getHomeViewLastSections()
            .then(resp => {
                if (resp.status > 0 && resp.payload?.sections) {
                    if(_isMounted) {
                        setSections(resp.payload.sections)
                    }
                    return;
                }
                throw new Error(resp.message);
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    translate("error", "Fehler"),
                    translate(
                        "interactive_course.status.load_failure",
                        "Die Bereiche konnten nicht geladen werden."
                    ) + err.message
                );
            }).finally((e) => {
                setLoading(false)
            });
    };

    return <>  <MainHeading>{translate("home_view.last_sections","Zuletzt angesehene Bereiche")}</MainHeading>
        <MutedParagraph>{translate("home_view.last_sections_description","Unten siehst du eine Übersicht der zuletzt aufgerufenen Bereiche und Kurse, die für dich verfügbar sind. Wir werden kontinuierlich neue Lerninhalte freischalten, um deine Lernreise zu erweitern.")}</MutedParagraph>
        <div className={"mb-4"}>
        {
         loading ?
         <div className='d-flex'>
             <CardSectionImageLeft isLoading={true} />
             <CardSectionImageLeft isLoading={true} />
         </div>
         : !sections || sections?.length == 0 ?
         <div className='text-center'><i className="fas fa-info-circle"></i> {translate("home_view.last_sections_info","Hier werden dir Bereiche angezeigt, die du zuletzt besuchst hast oder dir empfohlen werden.")}</div>
         :
         <Carousel itemCountPerPanel={2}>
        {sections?.map((el) => <CardSectionImageLeft isLoading={false}  onClick={() => history.push("/app/learner/" +  el?.group?.id +"/section/" +   el.section.id)}  title={el?.section?.name}  image={AjaxHelper.getSectionAvatarUrl(
            el.section.id,
            300,
            el.section.image
        )} subtitle={el?.section?.group?.name} headline={el?.section?.group?.tenant?.name} headlineImage={AjaxHelper.getTenantLogoUrl(el?.section?.group?.tenant?.logo)} />) }
        </Carousel>
        }
    </div></>;
}
