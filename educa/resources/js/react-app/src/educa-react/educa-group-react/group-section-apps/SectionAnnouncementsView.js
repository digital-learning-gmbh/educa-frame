import React, {Component, useRef} from 'react';
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import {connect} from "react-redux";
import {EducaLoading} from "../../../shared-local/Loading";
import SharedHelper, {EducaCKEditorDefaultConfig} from "../../../shared/shared-helpers/SharedHelper";
import {CKEditor} from "@ckeditor/ckeditor5-react";
import Button from "react-bootstrap/Button";
import {EducaCircularButton} from "../../../shared/shared-components/Buttons";
import AttachedFileComponent from "../../../shared/shared-components/AttachedFileComponent";
import EducaHelper, {LIMITS} from "../../helpers/EducaHelper";
import FliesentischZentralrat from "../../FliesentischZentralrat";
import Switch from "react-switch";
import AnnouncementCard from "../../educa-components/EducaFeedCards/AnnouncementCard";
import Table from 'react-bootstrap/Table';
import _ from "lodash";
import EducaModal, {MODAL_BUTTONS} from "../../../shared/shared-components/EducaModal";
import LetterCounter from "../../../shared/shared-components/LetterCounter";
import DatePickerBox from "../../../shared/shared-components/DatePickerBox";
import moment from "moment";
import {withRouter} from "react-router";
import EducaAICKEditor from "../../educa-components/EducaAICKEditor";

const unchecked = <div style={{paddingTop :"6px", paddingLeft : "6px"}}>
    <i style={{color :"#f8f8f8"}} className={"fas fa-times"}/>
</div>

const checked = <div style={{paddingTop :"6px", paddingLeft : "6px"}}>
    <i style={{color :"#f8f8f8"}} className={"fas fa-check"}/>
</div>

class SectionAnnouncementsView extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                currentAnnouncements: [],
                plannedAnnouncements: [],
                announcementTemplates: [],
                currentSection: {},
                attachedFiles: [],
                should_push: true,
                templateFiles: [],
                existingTemplateFiles: [],
                templateFilesToDelete: [],
                validPost: false,
                validTemplate: false,
                templateId: null,
                templateTitle: "",
                templateContent: "",
                isReady: false,
                planned_for: moment(),
                comments_active: true, // initially set, when first ajax call succeeds to prevent the display of the loading screen
                comments_hide: false,
                plan_announcement: false,
                createTemplateActive: false,
                inputText : "",
                inputTextTemplate : "",
                contentView: "create",
            }
        this.educaModal = React.createRef()
    }

    componentDidMount() {
        this._isMounted = true
        this.init()
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.section.id !== prevProps.section.id || this.props.group.id !== prevProps.group.id)
            this.init()
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    init() {
        this.setState({currentSection: this.props.section, isReady: false, currentAnnouncements: []}, () => {
            this.getInfo()
        })

    }

    getInfo() {
        AjaxHelper.getGroupSectionAnnouncements(this.props.section.id)
            .then(resp => {
                if (resp.payload && Array.isArray(resp.payload.announcements) && resp.status > 0) {
                    this.setState({currentAnnouncements: resp.payload.announcements})
                    this.setState({plannedAnnouncements: resp.payload.planned?.sort( (a,b) => moment(a?.planned_for).unix() - moment(b?.planned_for).unix() )})
                    this.setState({announcementTemplates: resp.payload.templates})
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

        if (!FliesentischZentralrat.sectionAnnouncementCreate(this.props.section))
            return

        if (!this.editor.getData())
            return EducaHelper.fireInfoToast("Die Ankündigung leer", "Bitte schreibe einen Text.")

        AjaxHelper.addGroupSectionAnnouncement(this.props.section.id, this.editor.getData(), this.state.attachedFiles, this.state.should_push, this.state.comments_active, this.state.planned_for ? moment(this.state.planned_for).unix() : null)
            .then(resp => {
                if (resp.payload && resp.status > 0) {
                    //Prepare to store the new data
                    let updatedSection = resp.payload.section
                    let arr = this.state.currentAnnouncements
                    arr.unshift(resp.payload.announcement)
                    this.setState({currentSection: updatedSection, currentAnnouncements: arr})
                    this.props.setSection(updatedSection)
                    this._clearAnnouncementCreation()
                    this.getInfo();
                    EducaHelper.fireSuccessToast("Erfolg", "Die Ankündigung wurde erfolgreich erstellt.")
                } else
                    throw new Error("")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Beitrag konnte nicht gespeichert werden." + err.message)
            })
    }


    _clearAnnouncementCreation() {
        //Reset Editor and attached files
        this.editor.setData("")
        this.setState({attachedFiles: [],
            planned_for: null,
            comments_active: true, // initially set, when first ajax call succeeds to prevent the display of the loading screen
            comments_hide: false,
            plan_announcement: false})
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

        this.setState({attachedFiles: this.state.attachedFiles.concat(Array.from(cleanedFilesArr))})
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

    async fillAnouncementFromTemplate(template) {
        this.editor.setData(template.content);
        let filesFromTemplate = [];
        for(const file of template.media)
        {
            await fetch('/storage/' + file.disk_name)
                .then(res => res.blob())
                .then(blob => {
                    var file = new File([blob], 'Datei aus Vorlage', {
                        type: blob.type
                    });
                    filesFromTemplate.push(file);
                })
        }
        this.setState({attachedFiles: filesFromTemplate});
        document.getElementById("posts-tab")?.click()
    }

    editTemplate(template)
    {
        this.setState({createTemplateActive: true, templateId: template.id,
            templateTitle: template.title, templateContent: template.content,
            existingTemplateFiles: template.media, validTemplate: true});
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

    addFileToTemplateFiles(objs) {
        let filesArr = Array.from(objs)
        let cleanedFilesArr = []
        //Check if the file was already added
        filesArr.forEach(file => {
            if (!this.state.templateFiles.find(obj => obj.name === file.name && obj.size === file.size))
                cleanedFilesArr.push(file)
        })
        this.setState({templateFiles: this.state.templateFiles.concat(Array.from(cleanedFilesArr))})
    }

    _submitTemplate() {

        if (!FliesentischZentralrat.sectionAnnouncementCreate(this.props.section))
            return

        if (!this.templateEditor.getData())
            return EducaHelper.fireWarningToast("Die Ankündigung ist leer", "Bitte schreibe einen Text.")
        if (!this.state.templateTitle)
            return EducaHelper.fireWarningToast("Der Titel ist leer", "Bitte Titel setzen.")

        // update
        if(this.state.templateId)
        {
            AjaxHelper.updateAnnouncementTemplate(this.state.templateId, this.state.templateFiles, this.state.templateFilesToDelete, this.state.templateTitle, this.templateEditor.getData())
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
                        //Reset template Editor and attached files
                        this._resetTemplateState();
                        EducaHelper.fireSuccessToast("Erfolg", "Die Vorlage wurde erfolgreich erstellt.")
                    } else
                        throw new Error("")
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Beitragsvorlage konnte nicht gespeichert werden." + err.message)
                })
        }
        // new
        else {
            AjaxHelper.addGroupSectionAnnouncementTemplate(this.props.section.id, this.state.templateTitle, this.templateEditor.getData(), this.state.templateFiles)
                .then(resp => {
                    if (resp.payload && resp.status > 0) {
                        this.setState({announcementTemplates: this.state.announcementTemplates.concat([resp.payload.announcementTemplate])});
                        //Reset template Editor and attached files
                        this._resetTemplateState();
                        EducaHelper.fireSuccessToast("Erfolg", "Die Vorlage wurde erfolgreich erstellt.")
                    } else
                        throw new Error("")
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Fehler", "Beitragsvorlage konnte nicht gespeichert werden." + err.message)
                })
        }
    }

    getNewAnnouncementContainer() {

        let templateList = this.state.announcementTemplates.map((template, i) => {
            return <tr key={template.id}>
                <td>{template.title}</td>
                <td>
                    <Button title={"Vorlage löschen"} className={"ml-1"} variant={"danger"} onClick={() => {

                        this.educaModal?.current?.open( (btn) => btn === MODAL_BUTTONS.SAVE_PRIMARY? this._deleteTemplate(template) : null, "Vorlage löschen", "Soll die Vorlage '" + template?.title + "' wirklich gelöscht werden?", [MODAL_BUTTONS.CANCEL, MODAL_BUTTONS.SAVE_PRIMARY]  )

                    }}><i className="fas fa-trash"></i></Button>
                    <Button title={"Vorlage bearbeiten"} className={"ml-1"} variant={"secondary"} onClick={() => {
                    this.editTemplate(template);
                }}><i className="fas fa-pencil-alt"></i></Button>
                    <Button title={"Ankündigung erstellen"} className={"mr-1 ml-1"} variant={"primary"} onClick={() => {
                        this.fillAnouncementFromTemplate(template)
                    }}><i className="fas fa-bullhorn"></i></Button>
                </td></tr>
        })


        if (FliesentischZentralrat.sectionAnnouncementCreate(this.props.section))
            return <div className="card gedf-card">
                <div className="card-header">
                    <ul className="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li className="nav-item">
                            <a className="nav-link active" id="posts-tab" onClick={() => this.setState({contentView: "create"})} href="#" role="tab"
                               aria-controls="posts" aria-selected="true"><i
                                className="fas fa-bullhorn"></i> Neue Ankündigung</a>
                        </li>
                        <li className="nav-item">
                            <a className="nav-link" id="templates-tab" onClick={() => this.setState({contentView: "vorlage"})} href="#" role="tab"
                               aria-controls="templates" aria-selected="false"><i
                                className="fas fa-edit"></i> Vorlagen</a>
                        </li>
                    </ul>
                </div>
                <div className="tab-content" id="myTabContent">
                    { this.state.contentView == "create" ?
                    <div className="tab-pane fade show active" id="posts" role="tabpanel"
                         aria-labelledby="posts-tab">
                        <div className="card-body">

                            <div className="form-group">
                                <EducaAICKEditor
                                    editor={window.ClassicEditor}
                                    config={{
                                        mention: {
                                        feeds: [
                                    {
                                        marker: '@',
                                        feed: [ '@Barney', '@Lily', '@Marry Ann', '@Marshall', '@Robin', '@Ted' ],
                                        minimumCharacters: 1
                                    }
                                        ]
                                    },
                                        language: {
                                        // The UI will be English.
                                        ui: 'de',

                                        // But the content will be edited in Arabic.
                                        content: 'de'
                                    }
                                    }}
                                    onReady={editor => {
                                        this.editor = editor;
                                    }}
                                    onChange={(event, editor) => {

                                        const data = editor.getData();
                                        var span = document.createElement('span');
                                        span.innerHTML = data;
                                        let shorText= span.textContent || span.innerText;
                                        shorText = shorText.replace(/<[^>]*>?/gm, '');
                                        this.setState({inputText : data, validPost: shorText.length <= LIMITS.ANNOUCMENT_LIMIT && shorText.length > 0});

                                        if (shorText.length > LIMITS.ANNOUCMENT_LIMIT) {
                                            EducaHelper.fireWarningToast("Hinweis", "Das Zeichenlimit für Ankündigungen liegt bei " + LIMITS.ANNOUCMENT_LIMIT + " Zeichen");
                                        }
                                        //    console.log({event, editor, data});
                                    }}
                                />
                            </div>
                                <div style={{display :"flex", flexDirection :"row", justifyContent : "right" }}>
                                    <LetterCounter maxLetters={LIMITS.ANNOUCMENT_LIMIT} string={this.state.inputText}/>
                                </div>
                            <div style={{display :"flex", flexDirection :"row"}}>

                                <b>Bildergalerie:</b>
                                <div style={{width :"5px"}}></div>
                                <div>
                                    <input
                                        multiple
                                        type="file"
                                        id={"input_announcement_upload"}
                                        onChange={(evt) => {
                                            let err = false
                                            evt.target.files?.forEach( file =>
                                            {
                                                if(SharedHelper.getFileType(file) !== "image")
                                                    err = true
                                            })
                                            if(err)
                                                return SharedHelper.fireErrorToast("Fehler", "Bitte wählen Sie nur Bilddateien aus.")
                                            this.addFileToAttachedFiles(evt.target.files)
                                        }}
                                        accept="image/x-png,image/gif,image/jpeg"
                                        style={{width: "0px", display: "none"}}/>
                                    <EducaCircularButton
                                        size={"medium"}
                                        title={"Bilder hochladen"}
                                        className="btn btn-secondary"
                                        variant={"success"}
                                        onClick={() => {
                                            document.getElementById("input_announcement_upload")?.click()
                                        }}
                                        type="button"><i className="fa fa-plus"></i>
                                    </EducaCircularButton>
                                </div>
                            </div>

                            {this.state.attachedFiles.length === 0 ? <div> Keine angehängten Bilder</div>
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
                    </div>
                        :
                    <div className="tab-pane fade show active" id="templates" role="tabpanel"
                         aria-labelledby="templates-tab">
                        <div className="card-body">
                            <div style={{display :"flex"}}>
                                <div className={"mr-1 d-flex flex-column"} style={{justifyContent :"center"}}>
                                    Vorlagen
                                </div>
                                <EducaCircularButton
                                    className={"mt-1 mb-1"}
                                    variant={this.state.createTemplateActive? "danger" : "success"}
                                    onClick={() => {
                                        if(this.state.createTemplateActive)
                                        {
                                            this._resetTemplateState();
                                        }
                                        else {
                                            this.setState({createTemplateActive: true});
                                        }
                                    }}
                                    tooltip={this.state.createTemplateActive? "Editor schließen" : "Vorlage erstellen" }
                                >
                                    {this.state.createTemplateActive? <i className={"fas fa-minus"}/> : <i className={"fas fa-plus"}/>}
                                </EducaCircularButton>
                            </div>
                            {this.state.createTemplateActive ?
                                <div>
                                    <div className={"form-group"}>
                                        <input type="text" value={this.state.templateTitle} className="form-control" placeholder="Titel"
                                               aria-label="Titel" onChange={(event) => {
                                            let data = event.target.value;
                                            this.setState({templateTitle: data, validTemplate: data.length > 0});
                                        } }/>
                                    </div>
                                    <div className="form-group">
                                        <EducaAICKEditor
                                            editor={window.ClassicEditor}
                                            config={EducaCKEditorDefaultConfig}
                                            data={this.state.templateContent}
                                            onReady={editor => {
                                                this.templateEditor = editor;
                                            }}
                                            onChange={(event, editor) => {

                                                const data = editor.getData();
                                                const textarea = document.createElement('textarea');
                                                textarea.innerHTML = editor.getData()
                                                const shorText = textarea.innerText
                                                    .replace(/<[^>]*>?/gm, "");
                                                this.setState({inputTextTemplate : data, validTemplate: shorText.length <= LIMITS.ANNOUCMENT_LIMIT && data.length > 0});
                                                if (shorText.length > LIMITS.ANNOUCMENT_LIMIT) {
                                                    EducaHelper.fireWarningToast("Hinweis", "Das Zeichenlimit für Ankündigungen liegt bei " + LIMITS.ANNOUCMENT_LIMIT + " Zeichen");
                                                }
                                            }}
                                        />
                                    </div>
                                    <div style={{float : "right"}}>
                                        <LetterCounter maxLetters={LIMITS.ANNOUCMENT_LIMIT} string={this.state.inputTextTemplate || this.state.templateContent}/>
                                    </div>

                                    {this.state.existingTemplateFiles && this.state.templateId ?
                                        <div>
                                            <div style={{display: "flex", flexDirection: "column"}}>
                                                <div style={{display: "flex", flexDirection: "row"}}>
                                                    {this.state.existingTemplateFiles.map((obj, index) => {
                                                        if (this.state.templateFilesToDelete.includes(obj.id)) //already added to list of deletion
                                                            return

                                                        return <div key={index}
                                                                    style={{display: "flex", flexDirection: "row", marginRight: "5px"}}>
                                                            <div style={{
                                                                display: "flex",
                                                                justifyContent: "flex-start",
                                                                flexDirection: "column"
                                                            }}>
                                                                <EducaCircularButton
                                                                    size={"small"}
                                                                    variant={"danger"}
                                                                    onClick={() => {
                                                                        let fileIdsToDelete = this.state.templateFilesToDelete ? this.state.templateFilesToDelete : []
                                                                        fileIdsToDelete.push(obj.id)
                                                                        this.setState({
                                                                            templateFilesToDelete: fileIdsToDelete
                                                                        })
                                                                    }}
                                                                >
                                                                    <i className={"fa fa-times"}>
                                                                    </i>
                                                                </EducaCircularButton>
                                                            </div>
                                                            <img width={50} src={"/storage/" + obj.disk_name}/>
                                                        </div>
                                                    })}
                                                </div>
                                            </div>
                                        </div>
                                        : null}

                                    <div style={{display :"flex", flexDirection :"row"}}>

                                        <b>Bildergalerie:</b>
                                        <div style={{width :"5px"}}></div>
                                        <div>
                                            <input
                                                multiple
                                                type="file"
                                                id={"input_announcement_upload"}
                                                onChange={(evt) => {
                                                    let err = false
                                                    evt.target.files?.forEach( file =>
                                                    {
                                                        if(SharedHelper.getFileType(file) !== "image")
                                                            err = true
                                                    })
                                                    if(err)
                                                        return SharedHelper.fireErrorToast("Fehler", "Bitte wählen Sie nur Bilddateien aus.")
                                                    this.addFileToAttachedFiles(evt.target.files)
                                                }}
                                                accept="image/x-png,image/gif,image/jpeg"
                                                style={{width: "0px", display: "none"}}/>
                                            <EducaCircularButton
                                                size={"medium"}
                                                title={"Bilder hochladen"}
                                                className="btn btn-secondary"
                                                variant={"success"}
                                                onClick={() => {
                                                    document.getElementById("input_announcement_template_upload")?.click()
                                                }}
                                                type="button"><i className="fa fa-plus"></i>
                                            </EducaCircularButton>
                                        </div>
                                    </div>

                                    <div>
                                        <input
                                            multiple
                                            type="file"
                                            id={"input_announcement_template_upload"}
                                            onChange={(evt) => {
                                                this.addFileToTemplateFiles(evt.target.files)
                                            }}
                                            accept="image/x-png,image/gif,image/jpeg"
                                            style={{width: "0px", display: "none"}}/>

                                        {this.state.templateFiles.length === 0 ? <div> Keine {this.state.templateId? "neuen " : ""}angehängten Bilder</div>
                                            :
                                            this.state.templateFiles.map((file, num) => {
                                                return <AttachedFileComponent
                                                    key={num}
                                                    zebra={num % 2}
                                                    fileRemoveCallback={() => {
                                                        this.state.templateFiles.splice(num, 1);
                                                        this.setState({templateFiles: this.state.templateFiles})
                                                    }}
                                                    fileChangedCallback={(file) => {
                                                        let arr = _.cloneDeep(this.state.templateFiles)
                                                        arr[num] = file
                                                        this.setState({templateFiles : arr})
                                                    }}
                                                    file={file}/>
                                            })
                                        }
                                    </div>

                                </div>
                                :  <div>{this.state.announcementTemplates.length > 0 ?
                                        <Table striped bordered hover>
                                            <thead>
                                            <tr>
                                                <th>Titel</th>
                                                <th>Aktion</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {templateList}
                                            </tbody></Table>
                                        : <div>Keine Vorlagen vorhanden.</div>}</div>
                                }
                        </div>
                        <div className="card-footer justify-content-between" align={"right"}>
                            {this.state.createTemplateActive ?
                                <div className="btn-group">
                                    <Button
                                        onClick={() => {
                                            this._resetTemplateState();
                                        }}
                                        className="btn btn-secondary mr-2"
                                    >Abbrechen</Button>

                                    <Button
                                        disabled={!this.state.validTemplate}
                                        onClick={() => {
                                            this._submitTemplate()
                                        }}
                                        className="tn-primary">
                                        Vorlage erstellen
                                    </Button>
                                </div> : null}
                        </div>
                    </div> }
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
                    canLike={FliesentischZentralrat.sectionAnnouncementLike(this.props.section)}
                    canComment={FliesentischZentralrat.sectionAnnouncementComment(this.props.section)}
                    key={a.id + "_announcement"}
                    announcement={a}
                    deletedAnnouncementCallback={(id) => this._deleteAnnouncement(id)}
                    updatedAnnouncementCallback={(updatedA) => this._insertUpdatedAnnouncement(updatedA)}
                    changeRouteCallback={(path, route) =>  /*this.props.history.push({
                        pathname: path,
                        search: route
                    })*/ {}}
                    section={this.props.section}
                    group={this.props.group}/>
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
        return (
            <div className={"mt-2"}
                 style={{display: "flex", flexDirection: "column", flex: 1, justifyContent: "center"}}>
                {FliesentischZentralrat.sectionAnnouncementCreate(this.props.section) ?
                    <div style={{display: "flex", flexDirection: "row", flex: 1, justifyContent: "center"}}>
                        <div style={{width: "100%", maxWidth: "800px", marginBottom: "10px"}}>
                            {this.getNewAnnouncementContainer()}
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
            </div>)
    }
}

export default withRouter(SectionAnnouncementsView);
