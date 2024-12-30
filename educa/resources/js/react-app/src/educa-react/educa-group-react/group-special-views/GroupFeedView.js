import EducaFeed from "../../educa-components/EducaFeed";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import React, {useEffect, useState} from "react";
import MainHeading from "../../educa-home-react/educa-learner-components/MainHeading.js";
import MutedParagraph from "../../educa-home-react/educa-learner-components/MutedParagraph.js";
import {Card} from "react-bootstrap";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper.js";
import HomeFeedLearner from "../../educa-home-react/educa-learner-components/HomeFeedLearner.js";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper.js";
import CardSectionImageLeft from "../../educa-home-react/educa-learner-components/CardSectionImageLeft.js";
import CardSectionImageTop from "../../educa-home-react/educa-learner-components/CardSectionImageTop.js";
import {useHistory} from "react-router";

export default function GroupFeedView(props) {

    let history = useHistory()
    const [translate] = useEducaLocalizedStrings()
    const [isLoading, setIsLoading] = useState(false);
    const [useColor, setUseColor] = useState(false);
    const [group, setGroup] = useState(props.group);
    useEffect(() => setGroup(props.group), [props.group]);

    return (
        <div className={"row"}>
            <div className={"col-8"}>
                <Card className="m-1"  text={
                    useColor?   SharedHelper.isColorTooDark(group.color) ? "light" : "dark"  : "dark"
                } style={{borderRadius: "1rem", cursor: "pointer",
                    backgroundColor: useColor ? SharedHelper.hexToRgbA(group.color, 0.5) : "white", }}>
                    <div className="row no-gutters">
                        <div className="col-auto">
                            {isLoading ? <div className="box shine" style={{
                                    height: "150px",
                                    width: "150px",
                                    borderTopLeftRadius: "calc(1rem - 1px)", borderBottomLeftRadius: "calc(1rem - 1px)"
                                }}/> :
                                <img src={AjaxHelper.getGroupAvatarUrl(
                                    group.id,
                                    300,
                                    group.image
                                )} className="img-fluid" alt="" style={{
                                    objectFit: "cover",
                                    width: "150px",
                                    height: "150px",
                                    borderTopLeftRadius: "calc(1rem - 1px)", borderBottomLeftRadius: "calc(1rem - 1px)"
                                }}/>}
                        </div>
                        <div className="col">
                            <div className="card-block px-2" style={{
                                top: "50%",
                                position: "relative",
                                transform: "translateY(-50%)"
                            }}>
                                {isLoading ? <>
                                    <div className="linesHolder">
                                        <div className="line shine" style={{width: "200px"}}/>
                                        <div className="line shine" style={{width: "200px"}}/>
                                        <div className="line shine" style={{width: "200px"}}/>
                                    </div>
                                </> : <>
                                    <h4 className="card-title" style={{fontWeight: 600}}>{props?.group?.name}</h4>
                                    <p className="card-text">{props?.group?.description}</p></>}
                            </div>
                        </div>
                    </div>
                </Card>

                <div className={"row"}>
                    {props.group?.sections?.map((el) => <div className={"col-12 col-md-6 col-xl-4"}><CardSectionImageTop onClick={() => history.push("/app/learner/" +  el?.group?.id +"/section/" + el?.id)} isLoading={false} title={el?.name}  image={AjaxHelper.getSectionAvatarUrl(
                        el.id,
                        300,
                        el.image
                    )} subtitle={el?.group?.name} headline={el?.group?.tenant?.name} headlineImage={AjaxHelper.getTenantLogoUrl(el?.group?.tenant?.logo)} /></div>) }
                </div>
            </div>
            <div className={"col-4"}>
                <HomeFeedLearner
                    title={translate("group.personal_feed", "Lernfeed der Gruppe")}
                    key={"group_" + props.group.id}
                    feedGetterFunc={timestamp => {
                        return AjaxHelper.getGroupFeed(timestamp, props.group.id);
                    }}
                />
            </div>
        </div>
    );
}
