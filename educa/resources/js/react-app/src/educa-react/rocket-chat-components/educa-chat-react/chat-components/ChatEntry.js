import React, {Component, useEffect, useState} from 'react';
import emojione from "emojione";
import {RCHelper} from "../../RocketChatHelper";
import Linkify from 'react-linkify';
import {connect} from "react-redux";
import EducaHelper from "../../../helpers/EducaHelper";
import Button from "react-bootstrap/Button";
import {EducaCircularButton} from "../../../../shared/shared-components";
import {FormControl} from "react-bootstrap";
import moment from "moment";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";

const styles = {
    root: {
        marginTop: "10px",
        display: "flex",
        width: "100%",
        flex: 1,
        flexDirection: "row"
    },
    wrapper: {
        padding: "10px",
        display: "flex",
        flex: 1,
        flexDirection: "column"
    },

    from: {
        display: "flex",
        flexDirection: "row",
        fontWeight: "bold",
        marginRight: "10px",
        fontSize: "15px",
    },
    time: {
        display: "flex",
        justifyContent: "center",
        flexDirection: "column",
        fontStyle: "italic",
        fontSize: "12px",
    },
    message: {
        display: "flex",
        flexDirection: "row",
        whiteSpace: "pre-wrap",
        fontSize: "15px",
        width: "100%"
    },
    imageWrapper:
        {
            display: "flex",
            flexDirection: "column",
            justifyContent: "start",
            marginTop: "10px"
        }
}


const FILE_TYPES =
    {
        AUDIO: "audio",
        STRING: "string",
        IMAGE: "image",
        PDF: "pdf",
        UNKNOWN: "unknown",
    }

let lastUsernameUsed = ""
let dateOfLastMessage = moment.unix(0)

class ChatEntryBase extends Component {

    constructor(props) {
        super(props);
    }


    formatMessage(message) {
        return emojione.shortnameToUnicode(message)
    }


    getChatEntryType() {
        if (!this.props.file)
            return FILE_TYPES.STRING

        else if (typeof this.props.file === "object") {
            if (this.props.file.type.includes("audio"))
                return FILE_TYPES.AUDIO
            if (this.props.file.type.includes("image"))
                return FILE_TYPES.IMAGE
            if (this.props.file.type.includes("pdf"))
                return FILE_TYPES.PDF
        }
        return FILE_TYPES.UNKNOWN

    }

    getCompleteChatEntry() {
        if (this.getChatEntryType() === FILE_TYPES.STRING)
            return this.getTextEntry()
        else if (this.getChatEntryType() === FILE_TYPES.AUDIO)
            return this.getAudioEntry()
        else if (this.getChatEntryType() === FILE_TYPES.IMAGE)
            return this.getImageEntry()
        else if (this.getChatEntryType() === FILE_TYPES.PDF)
            return this.getPdfEntry()
        else
            return this.getSimpleDownloadEntry()
    }


    getPdfEntry() {
        return this.getSimpleDownloadEntry()
    }

    startDownload(link) {
        window.open(link, "_blank")
    }

    getSimpleDownloadEntry() {
        return this.getScaffold(<div>

            <div style={styles.time}>{this.props.file.name ? this.props.file.name : ""}</div>
            <button className={"btn btn-primary"}
                    style={{width: "150px", height: "35px"}}
                    title={this.props.file.name}
                    onClick={() => {
                        this.startDownload(RCHelper.getFileLink(this.props.file._id, this.props.file.name))
                    }}>
                <div style={{display: "flex", flex: 1, flexDirection: "row"}}>
                    <div style={{display: "flex", flex: 1, flexDirection: "column", justifyContent: "center"}}>
                        <i className="fas fa-file"/>
                    </div>
                    <div style={{marginLeft: "5px"}}>Herunterladen</div>
                </div>
            </button>
            <div style={styles.message}>{this.props.message ? this.props.message : this.props.file.name}</div>
        </div>)
    }

    getImageEntry() {
        return this.getScaffold(<>
            <div style={styles.time}>{this.props.file.name ? this.props.file.name : ""}</div>
            <a href={RCHelper.getFileLink(this.props.file._id, this.props.file.name)} data-attribute="SRL">
                <img src={RCHelper.getFileLink(this.props.file._id, this.props.file.name)}
                     alt={this.props.message ? this.props.message : this.props.file.name}
                     style={{maxWidth: "350px", maxHeight: "800px"}}/>
            </a>
            <div style={styles.message}>
                {this.props.attachments?.length > 0? this.props.attachments[0].description :  this.props.file.name}
            </div>

        </>)
    }

    getAudioEntry() {
        return this.getScaffold(<div>
            <div style={styles.time}>{this.props.file.name === "blob" ? "" : this.props.file.name}</div>
            <audio controls controlsList="nodownload">
                <source src={RCHelper.getFileLink(this.props.file._id, this.props.file.name)} /*type={this.props.file.type}*//>
            </audio>
            <div style={styles.message}>{this.formatMessage(this.props.message)}</div>
        </div>)
    }

    getTextEntry() {
        lastUsernameUsed = this.props.from
        dateOfLastMessage = this.props.date
        return this.getScaffold(<div style={styles.message}>{this.formatMessage(this.props.message)}</div>)
    }

    getScaffold(content) {

        let citation = undefined
        if(this.props.citation )
        {
            let timestamp = undefined
            if(typeof this.props.citation.ts == "object" && this.props.citation.ts["$date"] > 0 )
                timestamp = moment.unix( +this.props.citation.ts["$date"] / 1000)
            if(typeof this.props.citation.ts == "string" )
                timestamp = moment(this.props.citation.ts)

            citation = <div style={{display : "flex",
                flexDirection : "column",
            ...RCHelper.getCitationStyle()}}>
                <i>
                    {this.props.citation?.cloudUser?
                    this.props.citation?.cloudUser.name +(timestamp? " ("+ timestamp.format("DD.MM.YYYY HH:mm")+")": null): null}</i>
                <i>{this.props.citation.text}</i>
            </div>
        }
        return <div key={this.props.uKey} id={this.props.id} style={styles.root}>
            <div style={styles.imageWrapper}>
                <img src={this.props.avatar} height="35px" alt={""} style={{borderRadius: "50%"}} onError={(e) => {
                    e.target.onerror = null;
                    e.target.src = this.props.avatarAlt
                }}/>
            </div>
            <div key={this.props.uKey} id={this.props.id} style={styles.wrapper}>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <div style={styles.from}>{EducaHelper.getStatusImage(this.props.userPresenceStatus)} {(this.props.isMe ? "Ich" : this.props.from)}</div>
                    <div style={styles.time}>{this.props.isPinnedOnTopMessage? this.props.date.locale("de").format("DD.MM.YYYY HH:mm") : this.props.date.locale("de").format("HH:mm")}</div>
                    <div style={{display : "flex", flexDirection : "column", justifyContent : "center"}}>
                        {this.props.isPinned? <i style={{color : "rgba(0, 0, 0, 0.3)"}} className={"ml-1 fas fa-thumbtack fa-1x"}></i> : null}
                    </div>
                </div>
                {citation? citation : null}
                {content}
            </div>
        </div>
    }

    /**
     * If the last user was the same as before, cut the avatar line
     *
     * @returns {JSX.Element}
     */
    getFollowUpEntry() {
        return <div key={this.props.uKey} id={this.props.id} style={styles.wrapper}>
            <div style={{
                ...styles.message,
                marginTop: "-20px",
                marginLeft: "35px"
            }}>{this.formatMessage(this.props.message)}</div>
        </div>
    }

    render() {
        if (this.getChatEntryType() === FILE_TYPES.STRING
            && lastUsernameUsed === this.props.from // same user as last message
            && dateOfLastMessage
            && this.props.date
            && this.props.date.diff(dateOfLastMessage, "minutes") < 2 // 2 minute treshold
            && !this.props.forceWholeMessage // If a Date Divider was drawn we mustn't use a follow up message
            && !this.props.citation // citation is always a non follow up entry
        )
            return <Linkify  componentDecorator={(decoratedHref, decoratedText, key) => ( <a target="blank" href={decoratedHref} key={key}> {decoratedText} </a> )} > {this.getCompleteChatEntry()/*this.getFollowUpEntry()*/}</Linkify>
        return <Linkify  componentDecorator={(decoratedHref, decoratedText, key) => ( <a target="blank" href={decoratedHref} key={key}> {decoratedText} </a> )} > {this.getCompleteChatEntry()} </Linkify>

    }
}

const mapStateToProps = state => ({store: state})
const ChatEntryInternal = connect(mapStateToProps)(ChatEntryBase)

function ChatEntryWrapper(props)
{
    let [isHovered, setIsHovered] = useState(false)

    let [editMode, setEditMode] = useState(false)
    let [newText, setNewText] = useState("")

    useEffect(() =>
    {
        reset()
    },[])

    const reset = () =>{
        setEditMode(false)
        setNewText("")
    }

    if(!props.message)
        return null

    if(editMode)
        return <div style={{display :"flex", flex : 1}}>
            <FormControl value={newText} onChange={(evt) => setNewText(evt?.target?.value)}/>
            <Button onClick={()=> {props.messageUpdatedCallback(newText) ;reset()}} variant={"success"}><i className={"fa fa-check"}/></Button>
            <Button onClick={()=> setEditMode(false)} variant={"danger"}><i className={"fa fa-times"}/></Button>
        </div>

    const hoverStyle = isHovered? {background : "rgba(0,0,0,0.03)", borderRadius :"1.25rem", /*padding : "10px"*/} : {}

    return <div style={{display :"flex", flex : 1, flexDirection :"row", ...hoverStyle}}
        onMouseEnter={() => {if(!isHovered)setIsHovered(true)}}
        onMouseLeave={() => {if(isHovered)setIsHovered(false)}}
    >
        <div style={{display :"flex",flexDirection :"column"}}>
            <ChatEntryInternal {...props}/>
            {props.editedAt? <i style={{
                fontSize : "12px",
                marginTop : "-15px",
                marginLeft : "45px",
                marginBottom :"10px",
                color : "rgba(0,0,0,0.5)"}}>bearbeitet</i> : null}
        </div>
        <div style={{display :"flex", flexDirection :"column", justifyContent :"center"}} className={"ml-2"}>
          <div style={{display :"flex"}}>
              {!props.isMe && props.canReport? <EducaCircularButton
                size={"small"}
                className={"mr-1"}
                variant={"warning"}
                onClick={() => {props.reportButtonClickCallback(props)}}
                style={isHovered?
                    {}
                    :
                    {display:"none", maxHeight:""}}><i className="fas fa-flag"></i></EducaCircularButton> : null}
            {props.canDelete? <EducaCircularButton
                size={"small"}
                variant={"danger"}
                onClick={() => {props.deleteButtonClickCallback(props)}}
                style={isHovered?
                    {}
                    :
                    {display:"none", maxHeight:""}}><i className="fas fa-trash"></i></EducaCircularButton> : null}
              {props.canEdit? <EducaCircularButton
                  size={"small"}
                  className={"ml-1"}
                  variant={"primary"}
                  onClick={() => {setEditMode(true); setNewText(props.message)}}
                  style={isHovered?
                      {}
                      :
                      {display:"none", maxHeight:""}}><i className="fas fa-pencil-alt"></i></EducaCircularButton> : null}
              {props.canPin? <EducaCircularButton
                  size={"small"}
                  className={"ml-1"}
                  variant={props.isPinned? "danger" : "info"}
                  onClick={() =>
                  {
                      props.pinMessageCallback()
                  }}
                  style={isHovered?
                      {}
                      :
                      {display:"none", maxHeight:""}}>
                  <i className="fas fa-thumbtack"></i></EducaCircularButton> : null}
              {props.canReply? <EducaCircularButton
                  size={"small"}
                  className={"ml-1"}
                  variant={"info"}
                  onClick={() =>
                  {
                      props.createReplyCallback()
                  }}
                  style={isHovered?
                      {}
                      :
                      {display:"none", maxHeight:""}}><i className="fas fa-reply"></i></EducaCircularButton> : null}
          </div>
        </div>
    </div>
}


export default ChatEntryWrapper; //ChatEntry
