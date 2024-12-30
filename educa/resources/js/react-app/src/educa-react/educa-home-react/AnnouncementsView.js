import React, {Component} from 'react';
import AjaxHelper from "../helpers/EducaAjaxHelper";
import {EducaLoading} from "../../shared-local/Loading";
import SharedHelper, {EducaCKEditorDefaultConfig} from "../../shared/shared-helpers/SharedHelper";
import Button from "react-bootstrap/Button";
import {EducaCircularButton, EducaDefaultTable, EducaFormPair} from "../../shared/shared-components";
import AttachedFileComponent from "../../shared/shared-components/AttachedFileComponent";
import EducaHelper, {LIMITS} from "../helpers/EducaHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";
import Switch from "react-switch";
import AnnouncementCard from "../educa-components/EducaFeedCards/AnnouncementCard";
import Table from 'react-bootstrap/Table';
import _ from "lodash";
import EducaModal, {MODAL_BUTTONS} from "../../shared/shared-components/EducaModal";
import LetterCounter from "../../shared/shared-components/LetterCounter";
import DatePickerBox from "../../shared/shared-components/DatePickerBox";
import moment from "moment";
import {withRouter} from "react-router";
import EducaAICKEditor from "../educa-components/EducaAICKEditor";
import Select from "react-select";
import {EducaTextArea} from "../../shared/shared-components/EducaTextArea";

const unchecked = <div style={{paddingTop :"6px", paddingLeft : "6px"}}>
    <i style={{color :"#f8f8f8"}} className={"fas fa-times"}/>
</div>

const checked = <div style={{paddingTop :"6px", paddingLeft : "6px"}}>
    <i style={{color :"#f8f8f8"}} className={"fas fa-check"}/>
</div>

class AnnouncementsView extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                currentAnnouncements: [],
                plannedAnnouncements: [],
                hideNewAnnouncementContainer: true,
                announcementTemplates: [],
                currentSections: [],
                attachedFiles: [],
                mentions: [],
                should_push: true,
                templateFiles: [],
                existingTemplateFiles: [],
                templateFilesToDelete: [],
                validPost: false,
                validTemplate: false,
                templateId: null,
                templateTitle: "",
                templateContent: "",
                templateAppId: null,
                isReady: false,
                planned_for: moment(),
                comments_active: true, // initially set, when first ajax call succeeds to prevent the display of the loading screen
                comments_hide: false,
                plan_announcement: false,
                createTemplateActive: false,
                inputText : "",
                inputTextTemplate : "",
                isLoading : false
            }
        this.educaModal = React.createRef()
    }

    componentDidMount() {
        this._isMounted = true
        this.init()
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.sections !== prevProps.sections)
            this.init()
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    init() {
        this.setState({currentSections: this.props.sections, currentAnnouncements: []}, () => {console.log("Initialized")})
        if (this.props.groupBrowse) {
            this.getInfo()
        }
        else if (this.props.loadTemplates) {
            this.getAnnouncementTemplates()
        }
    }

    getInfo() {
        AjaxHelper.getGroupSectionAnnouncements(this.props.sections[0].id)
            .then(resp => {
                if (resp.payload && Array.isArray(resp.payload.announcements) && resp.status > 0) {
                    if (this.props.loadTemplates) {
                        let allTemplates = {id: 0, title: "Keine Vorlage", content: ""}
                        allTemplates = [allTemplates].concat(resp.payload.templates)
                        this.setState({
                            currentAnnouncements: resp.payload.announcements,
                            plannedAnnouncements: resp.payload.planned?.sort( (a,b) => moment(a?.planned_for).unix() - moment(b?.planned_for).unix() ),
                            announcementTemplates: allTemplates})
                    }
                    else
                        this.setState({currentAnnouncements: resp.payload.announcements, plannedAnnouncements: resp.payload.planned?.sort( (a,b) => moment(a?.planned_for).unix() - moment(b?.planned_for).unix() )})
                } else
                    throw new Error("")
            })
            .catch(() => {
                EducaHelper.fireErrorToast("Fehler", "Fehler beim Laden der Beiträge.")
            })
            .finally(() => {
                this.setState({isReady: true});
            })
    }

    _submitAnnouncement() {

        // create permissions for sections checked in parent. Only those sections for which user has permission get here. Theoretically

        if (!this.editor.getData())
            return EducaHelper.fireInfoToast("Die Ankündigung leer", "Bitte schreibe einen Text.")

        this.setState({isLoading: true})

        let plannedFor = this.state.planned_for ? moment(this.state.planned_for).unix() : null
        let data = this.editor.getData()
        let updatedSections = []
        let success = true
        let lastAnnouncement = null

        this.state.currentSections.map((section) => {
            AjaxHelper.addGroupSectionAnnouncement(section.id, data, this.state.attachedFiles, this.state.should_push, this.state.comments_active, plannedFor)
                .then(resp => {
                    if (resp.payload && resp.status > 0) {
                        if (!this.props.groupBrowse){
                            updatedSections.push(resp.payload.section)
                            this.props.refreshFeed()
                        }
                        else {
                            let newAnnouncement = resp.payload.announcement
                            let arr = this.state.currentAnnouncements
                            arr.unshift(resp.payload.announcement)
                            this.setState({currentAnnouncements: arr}, () => this.getInfo())
                        }
                    } else {
                        success = false
                        throw new Error("")
                    }
                })
                .catch(err => {
                    success = false
                    EducaHelper.fireErrorToast("Fehler", "Die Ankündigung konnte nicht gespeichert werden im Bereich: " + section.nameWithGroup + " - " + err.message)
                })
        })
        if (success) {
            this.setState({currentSections: updatedSections})
            this._clearAnnouncementCreation()
            this.setState({isLoading: false}, () => EducaHelper.fireSuccessToast("Erfolg", "Die Ankündigung wurde erfolgreich erstellt"))
        }
        else {
            this.setState({isLoading: false}, () => EducaHelper.fireErrorToast("Fehler", "Die Ankündigung konnte nicht oder nur teilweise erstellt werden."))
        }
        if (this.state.createTemplateActive) {
            this._submitTemplate(data)
        }
    }

    _clearAnnouncementCreation() {
        //Reset Editor and attached files
        this.editor.setData("")
        this.setState({
            templateId: null,
            templateTitle: null,
            templateAppId: null,
            hideNewAnnouncementContainer: true,
            attachedFiles: [],
            planned_for: null,
            comments_active: true, // initially set, when first ajax call succeeds to prevent the display of the loading screen
            comments_hide: false,
            plan_announcement: false})
        if (!this.props.groupBrowse)
            this.props.clearSelectedSectionsInParent()
    }

    _insertUpdatedAnnouncement(newAnnouncement) {
        if (Array.isArray(this.state.currentAnnouncements))
            for (let i = 0; i < this.state.currentAnnouncements.length; i++) {
                if (this.state.currentAnnouncements[i].id === newAnnouncement.id) {
                    let newCurrentAnnouncements = this.state.currentAnnouncements;
                    newCurrentAnnouncements[i] = newAnnouncement
                    this.setState({currentAnnouncements: newCurrentAnnouncements})
                }
            }

        if (Array.isArray(this.state.plannedAnnouncements))
            for (let i = 0; i < this.state.plannedAnnouncements.length; i++) {
                if (this.state.plannedAnnouncements[i].id === newAnnouncement.id) {
                    let newPlanned = this.state.plannedAnnouncements;
                    newPlanned[i] = newAnnouncement
                    this.setState({plannedAnnouncements: newPlanned})
                }
            }
    }

    _deleteAnnouncement(id) {
        if (Array.isArray(this.state.currentAnnouncements))
            for (let i = 0; i < this.state.currentAnnouncements.length; i++) {
                if (this.state.currentAnnouncements[i].id === id) {
                    let newCurrentAnnouncements = this.state.currentAnnouncements;
                    newCurrentAnnouncements.splice(i, 1)
                    this.setState({currentAnnouncements: newCurrentAnnouncements})
                }
            }

        if (Array.isArray(this.state.plannedAnnouncements))
            for (let i = 0; i < this.state.plannedAnnouncements.length; i++) {
                if (this.state.plannedAnnouncements[i].id === id) {
                    let newPlanned = this.state.plannedAnnouncements;
                    newPlanned.splice(i, 1)
                    this.setState({plannedAnnouncements: newPlanned})
                }
            }
    }

    addFileToAttachedFiles(objs) {
        let filesArr = Array.from(objs)
        let cleanedFilesArr = []
        //Check if the file was already added
        filesArr.forEach(file => {
            if (!this.state.attachedFiles.find(obj => obj.name === file.name && obj.size === file.size))
                cleanedFilesArr.push(file)
        })

        this.setState({attachedFiles: this.state.attachedFiles.concat(Array.from(cleanedFilesArr))}, () => console.log("Updated attached files: ", this.state.attachedFiles))
    }

    getAnnouncementTemplates() {

        AjaxHelper.getAllTemplates()
            .then(resp => {
                if (resp.payload && resp.payload.templates && resp.status > 0) {
                    let allTemplates = {id: 0, title: "Keine Vorlage", content: ""}
                    allTemplates = [allTemplates].concat(resp.payload.templates)
                    this.setState({announcementTemplates: allTemplates})

                } else
                    throw new Error("")
            })
            .catch(() => {
                EducaHelper.fireErrorToast("Fehler", "Fehler beim Laden der Vorlagen.")
            })
            .finally(() => {
                this.setState({ isReady: true })
            })
    }

    _resetTemplateState()
    {
        this.setState({
            templateFiles: [],
            existingTemplateFiles: [],
            templateFilesToDelete: [],
            validTemplate: false,
            templateId: null,
            templateTitle: "",
            templateContent: "",
            createTemplateActive: false,
            inputTextTemplate : ""
        });
    }

    async fillAnnouncementFromTemplate(template) {
        if (template.title === "Keine Vorlage"){
            if (this.editor.getData()) {
                this.setState({templateId: null, templateTitle: null, templateAppId: null, attachedFiles: []})
                this.editor.setData(template.content)
            }
            return
        }
        this.editor.setData(template.content)
        this.setState({templateId: template.id, templateTitle: template.title, templateAppId: template.appId})
        let filesFromTemplate = []
        for(const file of template.media)
        {
            await fetch('/storage/' + file.disk_name)
                .then(res => res.blob())
                .then(blob => {
                    var newFile = new File([blob], JSON.parse(file.metadata).originalName, {
                        type: blob.type
                    });
                    filesFromTemplate.push(newFile);
                })
        }
        this.setState({attachedFiles: filesFromTemplate});
        document.getElementById("posts-tab")?.click()
    }

    _deleteTemplate(template)
    {
        if (Array.isArray(this.state.announcementTemplates))
        {
            let templateId = template.id;
            AjaxHelper.deleteAnnouncementTemplate(templateId)
                .then(resp => {
                    if (resp.status > 0) {
                        for (let i = 0; i < this.state.announcementTemplates.length; i++) {
                            if (this.state.announcementTemplates[i].id === templateId) {
                                let newTemplates = this.state.announcementTemplates;
                                newTemplates.splice(i, 1)
                                this.setState({announcementTemplates: newTemplates})
                            }
                        }
                    } else
                        throw new Error("")
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Beitragsvorlage konnte nicht gelöscht werden." + err.message)
                })
        }
    }

    _submitTemplate(data) {
        if (!data)
            return EducaHelper.fireWarningToast("Die Ankündigung ist leer", "Bitte schreibe einen Text.")
        if (!this.state.templateTitle)
            return EducaHelper.fireWarningToast("Der Vorlagentitel ist leer", "Bitte Titel setzen.")

        this.setState({isLoading:true})
        if(!!this.state.templateId) // update
        {
            AjaxHelper.updateAnnouncementTemplate(this.state.templateId, this.state.attachedFiles, this.state.templateFilesToDelete, this.state.templateTitle, data)
                .then(resp => {
                    if (resp.payload && resp.status > 0) {
                        for (let i = 0; i < this.state.announcementTemplates.length; i++) {
                            if (this.state.announcementTemplates[i].id === this.state.templateId) {
                                let newTemplates = this.state.announcementTemplates;
                                newTemplates.splice(i, 1)
                                this.setState({announcementTemplates: newTemplates})
                            }
                        }
                        this.setState({announcementTemplates: this.state.announcementTemplates.concat([resp.payload.announcementTemplate])});
                        //this._resetTemplateState();
                        EducaHelper.fireSuccessToast("Erfolg", "Die Vorlage wurde erfolgreich erstellt.")
                    } else
                        throw new Error("")
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Beitragsvorlage konnte nicht gespeichert werden." + err.message)
                })
                .finally(() => this.setState({isLoading:false}))
        }
        else { // new
            let section = this.state.currentSections[0]
            AjaxHelper.addGroupSectionAnnouncementTemplate(section.id, this.state.templateTitle, data, this.state.attachedFiles)
                .then(resp => {
                    if (resp.payload && resp.status > 0) {
                        this.setState({announcementTemplates: this.state.announcementTemplates.concat([resp.payload.announcementTemplate])})
                        console.log("Template create response: ", resp.payload.announcementTemplate)
                        EducaHelper.fireSuccessToast("Erfolg", "Die Vorlage wurde erfolgreich erstellt für Bereich: " + section.nameWithGroup)
                    } else
                        throw new Error("")
                })
                .catch(err => {
                    return EducaHelper.fireErrorToast("Fehler", "Beitragsvorlage konnte nicht gespeichert werden." + err.message)
                })
                .finally(() => this.setState({isLoading:false}))
        }
    }

    getNewAnnouncementContainer() {

        return <div className="card gedf-card">
                        <div className="card-body">
                            {this.props.loadTemplates && this.state.announcementTemplates?.length > 0 ?
                                <div className={'mb-4'}>
                                    Vorlagen:
                                    <Select getOptionLabel ={(option)=>option.title}
                                            getOptionValue ={(option)=>option.id}
                                            isMulti={false}
                                            placeholder={"Keine Vorlage"}
                                            noOptionsMessage={() => "Keine Vorlagen"}
                                            options={this.state.announcementTemplates}
                                            onChange={(newSelected) => this.fillAnnouncementFromTemplate(newSelected)}
                                    />
                                </div>
                                :
                                <></>
                            }
                            <div className="form-group">
                                <EducaAICKEditor
                                    editor={window.ClassicEditor}
                                    config={{...EducaCKEditorDefaultConfig,
                                        mention: {
                                            feeds: [
                                                {
                                                    marker: '@',
                                                    feed: (queryText) => {
                                                        return AjaxHelper.searchCloudUser(queryText).then((resp) => {
                                                            if(resp.payload.cloudUser)
                                                                return resp.payload.cloudUser.map((user) => {
                                                                    return {...user, userId: user.id}
                                                                }).map((user) => {
                                                                    return {...user, id: '@' + user.email}
                                                                });
                                                        })
                                                    },
                                                    itemRenderer: ( item ) => {
                                                        const container = document.createElement( 'div' );
                                                        const itemElement = document.createElement( 'span' );

                                                        itemElement.classList.add( 'custom-item' );
                                                        container.id = `mention-list-item-id-${ item.userId }`;
                                                        itemElement.textContent = `${ item.name } `;

                                                        const usernameElement = document.createElement( 'img' );

                                                    usernameElement.classList.add("rounded-circle");
                                                    usernameElement.width = 15;
                                                    usernameElement.src = AjaxHelper.getCloudUserAvatarUrl(item?.userId, 30, item?.image);

                                                        container.appendChild( usernameElement );
                                                        container.appendChild( itemElement );

                                                    return container;
                                                }
                                                }
                                            ]
                                        }
                                    }}
                                    onReady={editor => {
                                        this.editor = editor;
                                    }}
                                    onChange={(event, editor) => {
                                        const data = editor.getData();
                                        const textarea = document.createElement('textarea');
                                        textarea.innerHTML = editor.getData()
                                        const shorText = textarea.innerText
                                            .replace(/<[^>]*>?/gm, "");
                                        this.setState({inputText : data, validPost: shorText.length <= LIMITS.ANNOUCMENT_LIMIT && shorText.length > 0});
                                        if (shorText.length > LIMITS.ANNOUCMENT_LIMIT) {
                                            EducaHelper.fireWarningToast("Hinweis", "Das Zeichenlimit für Ankündigungen liegt bei " + LIMITS.ANNOUCMENT_LIMIT + " Zeichen");
                                        }
                                    }}
                                />
                            </div>
                            <div style={{display :"flex", flexDirection :"row", justifyContent : "right" }}>
                                <LetterCounter maxLetters={LIMITS.ANNOUCMENT_LIMIT} string={this.state.inputText}/>
                            </div>
                            <div style={{display :"flex", flexDirection :"row"}}>
                                <b>Bilder, Videos und Dokumente:</b>
                                <div style={{width :"5px"}}></div>
                                <div>
                                    <input
                                        multiple
                                        type="file"
                                        id={"input_announcement_upload"}
                                        onChange={(evt) => {
                                            this.addFileToAttachedFiles(evt.target.files)
                                        }}
                                        style={{width: "0px", display: "none"}}/>
                                    <EducaCircularButton
                                        size={"medium"}
                                        title={"Bilder, Videos und Dokumente hochladen"}
                                        className="btn btn-secondary"
                                        variant={"success"}
                                        onClick={() => {
                                            document.getElementById("input_announcement_upload")?.click()
                                        }}
                                        type="button"><i className="fa fa-plus"></i>
                                    </EducaCircularButton>
                                </div>
                            </div>

                            {this.state.attachedFiles.length === 0 ? <div> Keine angehängten Medien</div>
                                :
                                this.state.attachedFiles.map((file, num) => {
                                    return <AttachedFileComponent
                                        key={num}
                                        zebra={num % 2}
                                        fileRemoveCallback={() => {
                                            let exec = () => {
                                                this.state.attachedFiles.splice(num, 1);
                                                this.setState({attachedFiles: this.state.attachedFiles})
                                            }

                                            this.educaModal?.current?.open( (btn) => btn === MODAL_BUTTONS.YES? exec() : null, "Datei löschen", "Wollen Sie wirklich diese Datei löschen (wird erst nach Speichern übernommen)?", [MODAL_BUTTONS.NO, MODAL_BUTTONS.YES]  )
                                        }}
                                        fileChangedCallback={(file) => {
                                            let arr = _.cloneDeep(this.state.attachedFiles)
                                            arr[num] = file
                                            this.setState({attachedFiles : arr})
                                        }}
                                        file={file}/>
                                })
                            }

                            <div className={"mt-3"}>
                                <b>Optionen</b>
                                <div className={"container"}>
                                    <div className={"row mt-1"}>
                                        <div className={"mr-1"}>
                                            Push-Nachricht verschicken
                                        </div>
                                        <Switch
                                            uncheckedIcon={unchecked}
                                            checkedIcon={checked}
                                            checked={this.state.should_push}
                                            onChange={(flag) => this.setState({should_push: flag})}
                                        />
                                    </div>
                                    <div className={"row mt-2"}>
                                        <div className={"mr-1"}>
                                            Kommentare zulassen
                                        </div>
                                        <Switch
                                            uncheckedIcon={unchecked}
                                            checkedIcon={checked}
                                            checked={this.state.comments_active}
                                            onChange={(flag) => this.setState({comments_active: flag})}
                                        />
                                    </div>
                                    <div className={"row mt-2"}>
                                        <div className={"mr-1 mt-2"}>Ankündigung planen</div>
                                        <Switch
                                            uncheckedIcon={unchecked}
                                            checkedIcon={checked}
                                            checked={this.state.plan_announcement}
                                            onChange={(flag) => this.setState({plan_announcement: flag, planned_for: moment()})}
                                        />
                                        { this.state.plan_announcement ?
                                            <div className={"ml-1"}>

                                                <DatePickerBox
                                                    date={this.state.planned_for ? moment(this.state.planned_for) : null /*moment().add(2, "hours")*/}
                                                    onDateChange={date => {
                                                        this.setState({planned_for : date?.toDate()});
                                                    }}
                                                    inputWidth={"375px"}
                                                    timeIntervals={10}
                                                    locale="de-DE"
                                                    className={"form-control"}
                                                    timeCaption={""}
                                                    showTime={true}
                                                    dateFormat="dd.MM.yyyy  HH:mm"
                                                    placeholder={"Datum und Uhrzeit der Veröffentlichung"}
                                                />  </div>: <></> }
                                    </div>
                                    <div className={"row mt-2"}>
                                        <div className={"mr-1 mt-2"}>Als Vorlage speichern</div>
                                        <Switch
                                            uncheckedIcon={unchecked}
                                            checkedIcon={checked}
                                            checked={this.state.createTemplateActive}
                                            onChange={(flag) => this.setState({createTemplateActive: flag})}
                                        />
                                        { this.state.createTemplateActive ?
                                            <div className={"ml-1"}>
                                                <EducaTextArea type={"name"}
                                                               placeholder={"Vorlagentitel"}
                                                               value={!!this.state.templateId ? this.state.templateTitle : null}
                                                               onChange={(evt) => this.setState({templateTitle: evt.target.value})}/>
                                            </div>
                                            :
                                            <></>
                                        }
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div className="card-footer justify-content-between" align={"right"}>
                            <div className="btn-group">
                                <Button
                                    disabled={!this.state.validPost}
                                    onClick={() => {
                                        this._clearAnnouncementCreation()
                                    }}
                                    className="btn btn-secondary mr-2"
                                >Abbrechen</Button>

                                <Button
                                    disabled={!this.state.validPost}
                                    onClick={() => {
                                        this._submitAnnouncement()
                                    }}
                                    className="tn-primary">
                                    Ankündigung erstellen
                                </Button>
                            </div>
                        </div>
                <EducaModal ref={this.educaModal}/>
            </div>
    }

    getAnnouncementsListContainer() {

        if (!this.state.currentAnnouncements || !this.state.currentAnnouncements.length)
            return <div>Noch keine Ankündigungen</div>

        return <div>
            {this.state.currentAnnouncements.map(a => {
                return <AnnouncementCard
                    canLike={FliesentischZentralrat.sectionAnnouncementLike(this.props.sections[0])}
                    canComment={FliesentischZentralrat.sectionAnnouncementComment(this.props.sections[0])}
                    key={a.id + "_announcement"}
                    announcement={a}
                    deletedAnnouncementCallback={(id) => this._deleteAnnouncement(id)}
                    updatedAnnouncementCallback={(updatedA) => this._insertUpdatedAnnouncement(updatedA)}
                    changeRouteCallback={(path, route) =>  /*this.props.history.push({
                        pathname: path,
                        search: route
                    })*/ {}}
                    section={this.props.sections[0]}
                    group={this.props.sections[0].group_id}/>
            })}

        </div>
    }

    getPlannedAnnouncements() {
        if (!this.state.plannedAnnouncements || !this.state.plannedAnnouncements.length)
            return <div></div>

        return <div className={"mt-2"}
                    style={{display: "flex", flexDirection: "row", flex: 1, justifyContent: "center"}}>
            <div style={{width: "100%", maxWidth: "800px"}}>
                <div style={{
                    display: "flex",
                    flexDirection: "row",
                    flex: 1, fontWeight: "700",
                    color: "#6c757d",
                    fontSize: "1.125rem",
                    lineHeight: "1.2"
                }}><i className="fas fa-history mr-1"></i> Geplante Ankündigungen
                </div>
                {this.getPlannedAnnouncementsListContainer()}
            </div>
        </div>
    }

    getPlannedAnnouncementsListContainer() {

        if (!this.state.plannedAnnouncements || !this.state.plannedAnnouncements.length)
            return <div>Keine geplanten Ankündigungen</div>

        return <div>
            {this.state.plannedAnnouncements.map(a => {
                return <AnnouncementCard
                    canLike={FliesentischZentralrat.sectionAnnouncementLike(this.props.section)}
                    canComment={FliesentischZentralrat.sectionAnnouncementComment(this.props.section)}
                    key={a.id + "_announcement"}
                    announcement={a}
                    deletedAnnouncementCallback={(id) => this._deleteAnnouncement(id)}
                    updatedAnnouncementCallback={(updatedA) => this._insertUpdatedAnnouncement(updatedA)}
                    group={this.props.group}
                    section={this.props.section}
                />
            })}

        </div>
    }

    render() {
        if (!this.state.isReady)
            return <EducaLoading/>
        return (this.props.groupBrowse ?
                <div className={"mt-2"}
                     style={{display: "flex", flexDirection: "column", flex: 1, justifyContent: "center"}}>
                    {FliesentischZentralrat.sectionAnnouncementCreate(this.props.sections[0]) ?
                        <div style={{display: "flex", flexDirection: "row", flex: 1, justifyContent: "center"}}>
                            <div style={{width: "100%", maxWidth: "800px", marginBottom: "10px"}}>
                                <Button onClick={() => this.setState({hideNewAnnouncementContainer:!this.state.hideNewAnnouncementContainer})}><><i className="fa fa fa-bullhorn"></i> Neue Ankündigung</></Button>
                                <div hidden={this.state.hideNewAnnouncementContainer}>
                                    {this.getNewAnnouncementContainer()}
                                </div>
                            </div>
                        </div> : <></>}
                    {this.getPlannedAnnouncements()}
                    <div className={"mt-2"}
                         style={{display: "flex", flexDirection: "row", flex: 1, justifyContent: "center"}}>
                        <div style={{width: "100%", maxWidth: "800px"}}>
                            <div style={{
                                display: "flex",
                                flexDirection: "row",
                                flex: 1, fontWeight: "700",
                                color: "#6c757d",
                                fontSize: "1.125rem",
                                lineHeight: "1.2"
                            }}>Ankündigungen
                            </div>
                            {this.getAnnouncementsListContainer()}
                        </div>
                    </div>
                </div>
                :
                <div className={"mt-2"}
                     style={{display: "flex", flexDirection: "column", flex: 1, justifyContent: "center"}}>
                    <div style={{display: "flex", flexDirection: "row", flex: 1, justifyContent: "center"}}>
                        <div style={{width: "100%", maxWidth: "800px", marginBottom: "10px"}}>
                            {this.state.isReady ? this.getNewAnnouncementContainer() : null}
                        </div>
                    </div>
                </div>
        )
    }
}

export default withRouter(AnnouncementsView);
