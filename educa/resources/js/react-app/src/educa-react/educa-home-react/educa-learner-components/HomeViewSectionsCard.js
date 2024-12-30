import React, {useEffect, useState} from 'react';
import MainHeading from './MainHeading';
import MutedParagraph from './MutedParagraph';
import CardSectionImageTop from './CardSectionImageTop';
import {useSelector} from 'react-redux';
import EducaHelper from '../../helpers/EducaHelper';
import AjaxHelper from '../../helpers/EducaAjaxHelper';
import {useEducaLocalizedStrings} from '../../helpers/StringLocalizationHelper';
import {useHistory} from "react-router";
import Carousel from "react-multi-carousel";
import {RESPONSIVE_CAROUSEL} from "./Carousel.js";
import "react-multi-carousel/lib/styles.css";

export default function HomeViewSectionsCard({filter}) {


    const [loading, setLoading] = useState(true);
    const [courses, setCourses] = useState(null);
    const [sections, setSections] = useState(null);
    const [dashboardLevel, setDashboardLevel] = useState(null);
    const [translate] = useEducaLocalizedStrings()
    const history = useHistory()

    let _isMounted = false;
    React.useEffect(() => {
        _isMounted = true;
        getSections();
        return () => {
            _isMounted = false;
        };
    }, [filter]);


    let getSections = () => {
        setLoading(true)
        AjaxHelper.getHomeViewSections(filter?.group?.id)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.groups) {
                    if (_isMounted) {
                        setCourses(resp.payload.groups)
                        setSections(resp.payload.sections)
                        setDashboardLevel(resp.payload.dashboardLevel)
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

    return <><MainHeading>{translate("home_view.sections", "Deine Bereiche")}</MainHeading>
        <MutedParagraph>{translate("home_view.sections_description", "Hier findest du alle vorgeschlagen Bereiche, denen du zugeordnet bist. Wir werden auf deiner Lernreise nach und nach weitere Inhalte freischalten.")}</MutedParagraph>
        <div className={"mb-4"}>
            {
                loading ?
                    <div className='d-flex'>
                        <CardSectionImageTop isLoading={true}/>
                        <CardSectionImageTop isLoading={true}/>
                        <CardSectionImageTop isLoading={true}/>
                    </div>
                    : !dashboardLevel || !courses || courses?.length == 0 ?
                        <div className='text-center'><i
                            className="fas fa-info-circle"></i> {translate("home_view.no_sections", "Du hast aktuell noch keine Gruppen oder Bereiche")}.
                        </div>
                        :
                        <> {dashboardLevel === "group" ? (courses?.length > 0 ?

                                <Carousel
                                    swipeable={true}
                                    showDots={true}
                                    keyBoardControl={true}
                                    responsive={RESPONSIVE_CAROUSEL}>
                                    {courses?.slice(0,50)?.map((el) => <CardSectionImageTop isLoading={false}
                                                                                                       title={el?.name}
                                                                                                       image={AjaxHelper.getGroupAvatarUrl(
                                                                                                           el.id,
                                                                                                           300,
                                                                                                           el.image
                                                                                                       )}
                                                                                                       subtitle={el?.sections?.length + " Bereiche"}/>)}</Carousel> :
                            <div className='text-center'><i
                                className="fas fa-info-circle"></i> {translate("home_view.no_sections", "Du hast aktuell noch keine Gruppen oder Bereiche. Passe den Filter an.")}.
                            </div>)
                            : (sections?.length > 0 ?
                                <Carousel
                                    swipeable={true}
                                    showDots={true}
                                    keyBoardControl={true}
                                    ssr={true}
                                    responsive={RESPONSIVE_CAROUSEL}>
                                    {sections?.slice(0,50)?.map((el) => <CardSectionImageTop
                                        onClick={() => history.push("/app/learner/" + el?.group?.id + "/section/" + el?.id)}
                                        isLoading={false} title={el?.name} image={AjaxHelper.getSectionAvatarUrl(
                                        el.id,
                                        300,
                                        el.image
                                    )} subtitle={el?.group?.name} headline={el?.group?.tenant?.name}
                                        headlineImage={AjaxHelper.getTenantLogoUrl(el?.group?.tenant?.logo)}/>)}
                                </Carousel> :
                                <div className='text-center'><i
                                    className="fas fa-info-circle"></i> {translate("home_view.no_sections", "Du hast aktuell noch keine Gruppen oder Bereiche. Passe den Filter an.")}.
                                </div>)}</>
            }
        </div>
    </>;
}
