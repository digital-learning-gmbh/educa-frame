import React, {useRef, useState} from 'react';
import AsyncSelect from 'react-select/async';
import AjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import {Spinner} from "react-bootstrap";
import {BASE_ROUTES} from "../App";
import moment from "moment";
import {withRouter} from "react-router";
import AnnouncementModalViewer from "../educa-components/ModalViewers/AnnouncementModalViewer";
import {useSelector} from "react-redux";
import EducaHelper from "../helpers/EducaHelper";
import {pageToUrl} from "../educa-group-react/group-section-apps/EducaWiki";
import Button from "react-bootstrap/Button";
import PdfViewer from "../../shared/shared-components/PdfViewer";
import SharedFileBrowser from "../../shared/shared-components/SharedFileBrowser";
import EducaModal from "../../shared/shared-components/EducaModal";
import {useEducaLocalizedStrings} from "../helpers/StringLocalizationHelper.js";

export const SEARCH_CATEGORIES =
    {
        USERS: "cloudusers",
        GROUPS: "groups",
        ANNOUNCEMENTS: "announcements",
        TASKS: "tasks",
        EVENTS: "events",
        DOCUMENTS: "documents",
        WIKI_PAGES : "wiki"
    }

function labelFromCategoryKey(cat) {
    if (cat === SEARCH_CATEGORIES.USERS)
        return "Nutzer"
    if (cat === SEARCH_CATEGORIES.GROUPS)
        return "Gruppen"
    if (cat === SEARCH_CATEGORIES.ANNOUNCEMENTS)
        return "Ankündigungen"
    if (cat === SEARCH_CATEGORIES.TASKS)
        return "Aufgaben"
    if (cat === SEARCH_CATEGORIES.EVENTS)
        return "Termine"
    if (cat === SEARCH_CATEGORIES.DOCUMENTS)
        return "Dokumente"
    if (cat === SEARCH_CATEGORIES.WIKI_PAGES)
        return "Hilfe-Seiten"

}


const customStyles = {

    dropdownIndicator: (provided, state) => {
        return {display: "none"}
    }
}


const TYPING_TIMEOUT = 500
let timeOut = null

function EducaSearchBox(props) {

    let [searchString, setSearchString] = useState("")

    let announcementModalRef = useRef()
    const modalRef = useRef()
    const pdfModalRef = useRef()

    let store = useSelector(state => state);

    const [translate] = useEducaLocalizedStrings()

    let searchFunc = (str) => {
        if (str?.length >= 3) {
            return searchAjax(str)
        }
        return Promise.resolve([])
    }


    let changeRoute = (path, search) => {
        props.history.push({
            pathname: path,
            search: search,
        })
    }

    let searchAjax = (str) => {
        return AjaxHelper.searchAnything(str, props.categories ? props.categories : null)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.search) {
                    return prepareOptions(resp.payload.search)
                }
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Suchen fehlgeschlagen. " + err.message)
            })
            .finally((opts) => {
                return opts
            })
    }
    let prepareOptions = (search) => {

        let opts = []

        Object.keys(search).forEach(categoryKey => {
            let category = search[categoryKey]
            if (category?.length > 0) {
                let catObj = {label: labelFromCategoryKey(categoryKey), options: []}
                category.forEach(entry => {
                    if (categoryKey === SEARCH_CATEGORIES.USERS)
                        catObj.options.push(getUserSearchEntry(entry))
                    else if (categoryKey === SEARCH_CATEGORIES.GROUPS)
                        catObj.options.push(getGroupSearchEntry(entry))
                    else if (categoryKey === SEARCH_CATEGORIES.ANNOUNCEMENTS)
                        catObj.options.push(getAnnouncementSearchEntry(entry))
                    else if (categoryKey === SEARCH_CATEGORIES.EVENTS)
                        catObj.options.push(getEventSearchEntry(entry))
                    else if (categoryKey === SEARCH_CATEGORIES.TASKS)
                        catObj.options.push(getTaskSearchEntry(entry))
                    else if (categoryKey === SEARCH_CATEGORIES.WIKI_PAGES)
                        catObj.options.push(getWikiSearchEntry(entry))
                    else if (categoryKey === SEARCH_CATEGORIES.DOCUMENTS)
                        catObj.options.push(getDocumentSearchEntry(entry))
                })
                opts.push({...catObj, options : catObj?.options?.filter( o => !!o)})
            }
        })

        return opts
    }

    /**
     * Entries
     */

    let getEventSearchEntry = (event) => {
        let comp = <div style={{display: "flex", flexDirection: "row"}}>
            <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}}>
                <img width={40} height={40} src="/images/kalender_launcher.png"/>
            </div>
            <div style={{display: "flex", flexDirection: "column"}}>
                <div style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>{event.title} </div>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}}
                         className={"mr-1"}>
                        <i className="fas fa-calendar-alt"></i></div>
                    {moment(event.startDate).format("DD.MM.YYYY HH:mm")} - {moment(event.endDate).format("DD.MM.YYYY HH:mm")}
                </div>

            </div>
        </div>
        return {
            category: SEARCH_CATEGORIES.EVENTS,
            label: comp,
            value: event.id,
            action: () => {
                changeRoute(BASE_ROUTES.ROOT_CALENDER, "?event_id=" + event.id)
            }
        }
    }

    const getDocumentSearchEntry = (document) => {
        let comp = <div style={{display: "flex", flexDirection: "row"}}>
            <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}} className={"mr-1"}>
                <i className="far fa-file-alt fa-2x"></i>
            </div>
            <div style={{display: "flex", flexDirection: "column", overflow: "hidden",}}>
                <div style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>{document?.name} </div>
                {document?.fullText ? <div style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}><small>Treffer im Dokument</small></div> : null }
            </div>
        </div>
        return {
            category: SEARCH_CATEGORIES.EVENTS,
            label: comp,
            value: document.id,
            action: () => {
                let mediumUrl = AjaxHelper.downloadDocumentUrl(document.id, document.access_hash)

                const download = () =>
                {
                    let win = window.open(mediumUrl, '_blank')
                    win.focus()
                }

                if(["jpg", "jpeg", "png"].includes(document.file_type))
                {
                    modalRef?.current.open(() => {}, "Datei: "+document.name,
                        <>
                            <div style={{display :"flex", flex : 1, flexDirection :"row", justifyContent :"center"}}>
                                <img style={{maxWidth : "100%", maxHeight :"70vh"}} src={mediumUrl}/>
                            </div>
                            <Button onClick={() =>download() }>Download</Button>
                        </>
                        ,[]
                    )
                }
                else if(["mp4","webm"].includes(document.file_type))
                {
                    modalRef?.current.open(() => {}, "Datei: "+document.name,
                        <>
                            <div style={{display :"flex", flex : 1, flexDirection :"row", justifyContent :"center"}}>
                                <video controls style={{maxWidth:"100%"}}>
                                    <source src={mediumUrl} />
                                </video>
                            </div>
                            <Button onClick={() =>download() }>Download</Button>
                        </>
                        ,[]
                    )
                }
                else if(["pdf"].includes(document.file_type))
                {
                    pdfModalRef?.current.open(() => {}, "Datei: "+document.name,
                        <>
                            <div>
                                <PdfViewer url={mediumUrl}/>
                            </div>
                            <Button onClick={() =>download() }>Download</Button>
                        </>
                        ,[]
                    )
                } else {
                    download()
                }
            }
        }
    }

    const getWikiSearchEntry = (page) => {
        let comp = <div style={{display: "flex", flexDirection: "row"}}>
            <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}} className={"mr-1"}>
                <i className="fa fa-atlas fa-2x"></i>
            </div>
            <div style={{display: "flex", flexDirection: "column", overflow: "hidden",}}>
                <div style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>{page?.name} </div>
            </div>
        </div>
        return {
            category: SEARCH_CATEGORIES.WIKI_PAGES,
            label: comp,
            value: page.id,
            action: () => {
                changeRoute(BASE_ROUTES.ROOT_WIKI+"/"+pageToUrl(page))
            }
        }
    }
    let getTaskSearchEntry = (task) => {
        let comp = <div style={{display: "flex", flexDirection: "row"}}>
            <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}}>
                <img width={40} height={40} src="/images/aufgaben_launcher.png"/>
            </div>
            <div style={{display: "flex", flexDirection: "column", overflow: "hidden",}}>
                <div style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>{task.title} </div>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}}
                         className={"mr-1"}>
                        <i className="fas fa-clock"></i></div>
                    {moment(task.end).format("HH:mm")}
                </div>
            </div>
        </div>
        return {
            category: SEARCH_CATEGORIES.EVENTS,
            label: comp,
            value: task.id,
            action: () => {
                changeRoute(BASE_ROUTES.ROOT_TASKS, "?task_id=" + task.id)
            }
        }
    }

    let getGroupSearchEntry = (group) => {
        if(!store?.currentCloudUser?.groups?.find(g => group.group_id == g.id))
            return null
        let comp = <div style={{display: "flex", flexDirection: "row"}}>
            <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}} className={"mr-1"}>
                <img width={40} height={40} src={AjaxHelper.getGroupAvatarUrl(group.group_id, 40, group.image)}/>
            </div>
            <div style={{display: "flex", flexDirection: "column", overflow: "hidden",}}>
                <div style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>{group.name} </div>
            </div>
        </div>
        return {
            category: SEARCH_CATEGORIES.GROUPS,
            label: comp,
            value: group.id,
            action: () => {
                changeRoute(BASE_ROUTES.ROOT_GROUPS + "/" + group.group_id)
            }
        }
    }

    let getUserSearchEntry = (user) => {
        let comp = <div style={{display: "flex", flexDirection: "row"}}>
            <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}} className={"mr-1"}>
                <img width={40} height={40} style={{borderRadius: "50%"}}
                     src={AjaxHelper.getCloudUserAvatarUrl(user.id,35, user.image)}/>
            </div>
            <div style={{display: "flex", flexDirection: "column", overflow: "hidden",}}>
                <div style={{textOverflow: "ellipsis", overflow: "hidden", whiteSpace: "nowrap"}}>{user.name} </div>
            </div>
        </div>
        return {
            category: SEARCH_CATEGORIES.USERS,
            label: comp,
            value: user.id,
            action: () => {
                changeRoute(BASE_ROUTES.ROOT_MESSAGES, "?message_to=" + user.id)
            }
        }
    }

    let getAnnouncementSearchEntry = (announcement) => {

        let creator = store.allCloudUsers.find(u => u.id === announcement.cloudid)
        let comp = <div style={{display: "flex", flexDirection: "row"}}>
            <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}} className={"mr-1"}>
                <i className="fa fa-bullhorn fa-2x"></i>
            </div>
            <div style={{display: "flex", flexDirection: "column", overflow: "hidden"}}>
                <div style={{
                    textOverflow: "ellipsis",
                    overflow: "hidden",
                    whiteSpace: "nowrap"
                }} dangerouslySetInnerHTML={SharedHelper.sanitizeHtml(announcement.content)}/>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}}
                         className={"mr-1"}>
                        <i className="fas fa-user"></i></div>
                    {creator?.name}
                </div>
            </div>
        </div>
        return {
            category: SEARCH_CATEGORIES.ANNOUNCEMENTS,
            label: comp,
            value: announcement.id,
            action: () => {
                announcementModalRef.current?.open(announcement.id)
            }
        }
    }


    return (
        <div style={{...props.style}}>
            <AsyncSelect
                //cacheOptions // TODO to catch or not to catch
                onChange={(obj) => obj.action()}
                value={null}
                loadOptions={(inputValue) => {
                    setSearchString(inputValue);
                    return searchFunc(inputValue)
                }}
                placeholder={translate("search.placeholder","Suchen...")}
                loadingMessage={() => <Spinner animation={"grow"} size={"lg"}/>}
                noOptionsMessage={() => searchString?.length < 3 ? translate("search.limit","Bitte mindestens 3 Zeichen eintippen.") : translate("search.no_results","Keine Treffer")}
            >
            </AsyncSelect>
            <EducaModal closeButton={true} noBackdrop={true} size={"lg"} ref={modalRef}/>
            <EducaModal closeButton={true} noBackdrop={true} size={"xl"} ref={pdfModalRef}/>
            <AnnouncementModalViewer ref={announcementModalRef}/>
        </div>
    );
}

export default withRouter(EducaSearchBox);
