import React, {useEffect, useState} from 'react';
import {connect, useSelector} from "react-redux";
import Button from "react-bootstrap/Button";
import MutedParagraph from "../../educa-home-react/educa-learner-components/MutedParagraph";
import {Card} from "react-bootstrap";
import {EducaShimmer} from "../../../shared/shared-components/EducaShimmer";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";
import ReactTimeAgo from "react-time-ago";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import moment from "moment";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper.js";
import "react-multi-carousel/lib/styles.css";
import Carousel from "react-multi-carousel";

const customHtmlSanitizerDefaultOptions = {
    allowedTags: [
        'a',
        'div',
        'span',
        'ul', 'li', //list
        'strong', 'i', 'blockquote', //text styles
        'table', 'tr', 'th', 'td', 'tbody', 'figure', // table
        'h1', 'h2','h3','h4', 'h5', // Headings
        'br','p',
        'iframe','oembed'
    ],
    allowedAttributes: {
        'a': [ 'href', 'target' ],
        'div': ['id', 'class', 'style'],
        'span': ['id', 'class', 'style'],
        'strong': ['class'],
        'i': ['class'],
        'ul': ['class'],
        'li': ['class'],
        'blockquote': ['class'],
        'figure' : [ '*' ],
        'oembed' : [ '*' ],
        'iframe' : [ 'src','frameborder', 'allow', 'allowfullscreen','style' ],
        'p' : ["style"]
    }
};

const SectionAnnouncmentsPreview = ({section, app, openApp}) => {
    let [isMounted, setIsMounted] = useState(false)

    const [announcements, setAnnouncements] = useState(undefined);
    const me = useSelector(store => store.currentCloudUser)
    const [translate] = useEducaLocalizedStrings()

    const RESPONSIVE_CAROUSEL = {
        superLargeDesktop: {
            // the naming can be any, depends on you.
            breakpoint: { max: 4000, min: 3000 },
            items: 3
        },
        desktop: {
            breakpoint: { max: 3000, min: 1024 },
            items: 2
        },
        tablet: {
            breakpoint: { max: 1024, min: 464 },
            items: 1
        },
        mobile: {
            breakpoint: { max: 464, min: 0 },
            items: 1
        }
    };

    useEffect(() => {
        setIsMounted(true)
        return () => setIsMounted(false)
    }, [])

    useEffect(() => {
        AjaxHelper.getfirstAnnouncementsOfSection(section.id)
            .then((resp) => {
                if(resp.status > 0 && resp.payload) {
                    setAnnouncements(resp.payload.announcements)
                    return
                }
                throw new Error("")
            })
            .catch((err) => {
                EducaHelper.fireErrorToast("Fehler", "Vorschau der Ankündigungen konnte nicht geladen werden.")
            })
    }, [section]);

    if (!isMounted)
        return <></>
    if(announcements === undefined)
        return <EducaShimmer/>
    if(!announcements || announcements?.length == 0)
        return <Card className={"m-2 p-2"}>
            <div>
                <MutedParagraph><i className="fas fa-info-circle"></i>{translate("group_view.no_announcement","Bisher wurde noch keine Ankündigung veröffentlicht.")}</MutedParagraph>
                <Button variant={"outline-dark"} onClick={() => openApp(app)}>{translate("group_view.open_announcements","Ankündigungen öffnen")}</Button>
            </div>
        </Card>
    return (<Card>
            <Card.Body>
                <Carousel
                    infinite={true}
                    swipeable={true}
                    showDots={true}
                    slidesToSlide={2}
                    keyBoardControl={true}
                    responsive={RESPONSIVE_CAROUSEL}>
                    {announcements.map((announcement, i) => {
                        let imageUrl =  AjaxHelper.getCloudUserAvatarUrl(announcement.author?.id, "30", announcement.author?.image)
                        return <div className={"ml-2 mr-2"}>
                            <Card key={i} style={{ height: '210px', cursor: 'pointer' }}
                                     onClick={() => openApp(app)}>
                            <Card.Header style={{backgroundColor: "white"}}>
                                <div className={'d-flex flex-row align-items-center justify-content-between'}>
                                    <div className={"ml-2"}>
                                        <img className="rounded-circle" width="30" src={imageUrl}
                                             alt={"Avatar von " + announcement.author?.name}/>
                                        <span className="h5 flex-wrap"><b>{announcement.author?.name}</b></span>
                                    </div>
                                    <div className="float-right flex-wrap text-muted h7 mr-2">
                                        <i className="fa fa-clock"></i> <ReactTimeAgo
                                        date={moment(announcement.created_at).toDate()} locale="de-DE"/>
                                    </div>
                                </div>
                            </Card.Header>
                            <Card.Body className="d-flex flex-grow-1 flex-column flex-wrap">
                                {announcement.content.length <= 120 ?
                                    (
                                        <span
                                            dangerouslySetInnerHTML={SharedHelper.sanitizeHtml(
                                                announcement.content, customHtmlSanitizerDefaultOptions
                                            )}
                                        ></span>
                                    )
                                    :
                                    (
                                        <span
                                            dangerouslySetInnerHTML={SharedHelper.sanitizeHtml(
                                                announcement.content.substr(0, 120).slice(0, announcement.content.substr(0, 120).lastIndexOf(" ")) + "&hellip;",
                                                customHtmlSanitizerDefaultOptions
                                            )}
                                        ></span>
                                    )}
                            </Card.Body>
                            <Card.Footer style={{ backgroundColor: 'white' }} >
                                {announcement.likeCount}{" "}
                                {announcement.liked ?
                                    <i className="far fa-thumbs-up ml-1 mr-1 mt-1" style={{ color: "rgb(3, 102, 214)" }} title={"Gefällt dir"}/>
                                    :
                                    <i className="far fa-thumbs-up ml-1 mr-1 mt-1"/>
                                }
                                { announcement.comments_hide ?
                                    <></>
                                    :
                                    <span>{"  "}{announcement.commentCount} <i className="fa fa-comment"></i> Kommentar(e)</span>
                                }
                                {announcement.mentionsMe ?
                                    <span className={'float-right'} style={{ color: "rgb(3, 102, 214)" }} title={"Du wirst erwähnt"}><strong>@{me.name}</strong></span>
                                    :
                                    null
                                }
                            </Card.Footer>
                            </Card></div>
                    })}
                </Carousel>
            </Card.Body>
        </Card>
    );
}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(SectionAnnouncmentsPreview);
