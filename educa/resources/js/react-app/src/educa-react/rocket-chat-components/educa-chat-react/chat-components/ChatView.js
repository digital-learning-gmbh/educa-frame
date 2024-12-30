/**
 *
 * Creator: 0x539
 * Sat 2020 Dec 5
 */

import React, {Component, useState} from 'react';
import {RC_HOST, RCHelper} from "../../RocketChatHelper";
import 'emoji-mart/css/emoji-mart.css'
import {Picker} from 'emoji-mart'
import ChatEntry from "./ChatEntry";
import TextInputComponent from "./TextInputComponent";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import ReactTooltip from "react-tooltip";
import {Dropdown, Navbar} from "react-bootstrap";
import FileUploadDialogRC from "./FileUploadDialogRC";
import Card from "react-bootstrap/Card";
import {ROCKET_CHAT_SET_ME} from "../../../reducers/GeneralReducer";
import {connect} from "react-redux";
import {EducaCircularButton} from "../../../../shared/shared-components/Buttons";
import EducaHelper from "../../../helpers/EducaHelper";
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import EducaReportModal from "../../../educa-messages-react/EducaChatReportModal";
import EducaModal, {MODAL_BUTTONS} from "../../../../shared/shared-components/EducaModal";
import _ from "lodash";
import {DndProvider, DropTarget} from "react-dnd";
import {NativeTypes} from "react-dnd-html5-backend";
import Button from "react-bootstrap/Button";
import SafeDeleteModal from "../../../../shared/shared-components/SafeDeleteModal";
import GroupChatIMEditModal from "./GroupChatIMEditModal";
import moment from "moment";

const emojiPickerStyles = {
    overflow: "visible",
    display: "flex",
    zIndex: "9999",
    marginTop: "-350px",
    backgroundColor: "#fff"
}


const styles =
    {
        heading:
            {
                margin: "10px",
                fontSize: "15",
                fontWeight: "15",
            },
        divider:
            {
                container: {
                    margin: "15px 0px 15px 0px",
                    display: "flex",
                    alignItems: "center",
                },

                border: {
                    borderBottom: "1px solid lightgray",
                    width: "100%",
                },

                content: {
                    display: "flex",
                    fontStyle: " italic",
                    color: "grey",
                    padding: "0 10px 0 10px",
                    minWidth: "120px",
                    justifyContent: "center",
                    flexDirection: "row"
                },
            },
        overlay :
            {
                position: "absolute",
                width: "100%",
                height: "100%",
                top: "0",
                left: "0",
                right: "0",
                bottom: "0",
                backgroundColor: "rgba(0, 0, 0, 0.5)",
                zIndex : 9999
            }
    }

const Divider = ({children}) => {
    return (
        <div style={styles.divider.container}>
            <div style={styles.divider.border}/>
            <span style={styles.divider.content}>
        {children}
      </span>
            <div style={styles.divider.border}/>
        </div>
    );
};

class ChatView extends Component {

    constructor(props) {
        super(props);
        this.state =
            {

                currentGroup: {},
                currentMembers: [],
                currentMembersCloudUsers: [],
                groupRolesArray : [],

                currentMessagesChunk:
                    {
                        messages: [],
                        offset: 0,
                        count: 0,
                        total: 0,
                    },
                currentPreparedMessages: [],
                pinnedMessages : [],
                showEmojiPicker: false,

                me: {},

                componentLoadFailed: false,
                currentCitationMessage : null,

                registrationIdPrefixUserStats: "chatview_userstats_",
                registrationIdStreamRoomMessages: "chatview_msgs_"

            }
        this.fileUploadRef = React.createRef();
        this.messagesEnd = React.createRef()
        this.reportModalRef = React.createRef()
        this.educaModalRef = React.createRef()
        this.deleteModalRef = React.createRef()
        this.editChatModalRef = React.createRef()

        this.autoScroll = true
    }


    setAutoScroll(state) {
        this.autoScroll = state
    }

    componentDidMount() {
        this.setAutoScroll(true)
        this._ismounted = true;
        if (this.props.store.rocketChat.me._id) // if the store has a user object
        {
            this.setState({isLoggedIn: true, me: this.props.store.rocketChat.me}, () => {
                //Subscribe, after me status is set. If the user refreshes the page, he is always "offline", since the /me object loads after
                RCHelper.getRcEventManager()?.subscribeToStreamRoomMessages(this.state.registrationIdStreamRoomMessages + this.getRoomId(), this.getRoomId(), (data) => {
                    this.webSocketNewMessages(data)
                })
                RCHelper.getRcEventManager()?.subscribeToNotifyLoggedUserStatus(this.state.registrationIdPrefixUserStats + this.getRoomId(), (data) => {
                    this.webSocketNotifyLogged(data)
                })

            })
            // console.log("me", this.props.store.rocketChat.me)
            if (this.props.type === "group")
                this.RCInitGroup();
            else
                this.RCInitDirect();

            RCHelper.markRoomAsRead(this.getRoomId())
                .then(resp => {
                    if (!resp.success)
                        SharedHelper.logError("ChatView: Could not set room to as read")
                })
                .catch(err => {
                    this.setState({componentLoadFailed: true})
                    EducaHelper.fireErrorToast("Fehler", err.message)
                })
        }

    }

    componentWillUnmount() {
        RCHelper.getRcEventManager()?.unSubscribeToStreamRoomMessages(this.getRoomId())
        RCHelper.getRcEventManager()?.unSubscribeToNotifyLogged(this.state.registrationIdPrefixUserStats + this.getRoomId())
        this._ismounted = false;
    }

    componentDidUpdate(prevProps, prevState, snapshot) {

        if (this.autoScroll && prevState.showEmojiPicker === this.state.showEmojiPicker // prevent scroll event when user clicked the emoji button
            /*&& this.state.currentMessagesChunk.count > 10*/ // being to auto scroll at this amount of messages
        ) {
            window.setTimeout(() => {
                this.scrollToBottom()
            }, 750)
        }


        //If this view should view another chat / Switch to another chat
        if (prevProps.roomId != this.props.roomId) {
            this.setState({currentCitationMessage : null, currentGroup : null})
            let roomId = (prevProps.type === "group" ? prevState?.currentGroup?._id : prevProps.roomId)
            RCHelper.getRcEventManager()?.unSubscribeToStreamRoomMessages(this.state.registrationIdStreamRoomMessages + roomId)
            RCHelper.getRcEventManager()?.unSubscribeToNotifyLogged(this.state.registrationIdPrefixUserStats + roomId)
            this.componentDidMount()
        }

    }


    getRoomId() {
        return this.props.roomId
    }

    /**
     * Updates the status of the currently online members
     */

    userLoginChangedHandler(users) {
        let newCurrentMembers = this.state.currentMembers
        users.forEach(userArray => {

            let status = RCHelper.integerToStatusText(userArray[2])

            let id = userArray[0]

            for (let key in this.state.currentMembers) {
                if (this.state.currentMembers[key]._id === id)
                    newCurrentMembers[key].status = status
            }
            if (this.state.me._id === id) // update me object if the user changed his status
            {
                this.setState({me: {...this.state.me, status: status}})
            }

        })
        if (this._ismounted) this.setState({currentMembers: newCurrentMembers})
    }

    /**
     * Adds new messages to the current list of messages
     * @param msgs
     */
    newMessagesAdder(msgs) {
        let addPreparedMessages = []
        let hasPlayedSound = false
        msgs.forEach(msg => {
            if (msg.u._id !== this.state.me._id && !hasPlayedSound) {
                RCHelper.playNewMessageNotification()
                hasPlayedSound = true
            }
            msg._updatedAt = msg._updatedAt["$date"] ? msg._updatedAt["$date"] : msg._updatedAt // sometimes there is a key with "$date"
            msg.ts = Number.isInteger(msg.ts["$date"])? msg.ts["$date"] : undefined
                this.setAutoScroll(true)
            addPreparedMessages.push(this.preparedMessageFromRawMessage(msg, true))
        })

        //Check whether messages got updated
        let indicesToRemove = []
        addPreparedMessages.forEach( (prepMsg, i) =>
        {
            let index = this.state.currentPreparedMessages.findIndex( m => m.id == prepMsg.id)
            //Message updates
            if(index >= 0)
            {
                let oldMsg = this.state.currentPreparedMessages[index]
                prepMsg.updatedAt = oldMsg.updatedAt
                this.state.currentPreparedMessages[index] = prepMsg
                indicesToRemove.push(i)
            }
        })
        addPreparedMessages = addPreparedMessages.filter( (m,i) => !(indicesToRemove.find(index => i == index) >= 0)  )
        if (this._ismounted) this.setState(
            {
                currentPreparedMessages: [...this.state.currentPreparedMessages, ...addPreparedMessages],
                currentMessagesChunk: {
                    ...this.state.currentMessagesChunk,
                    count: this.state.currentMessagesChunk.count + addPreparedMessages.length,
                    total: this.state.currentMessagesChunk.total + addPreparedMessages.length
                }
            })
    }


    webSocketNewMessages(data) {
        // careful recheck
        if (data.msg === "changed" && data.collection === "stream-room-messages" && data.fields && Array.isArray(data.fields.args)) {
            this.newMessagesAdder(data.fields.args)
        }
    }

    webSocketNotifyLogged(data) {
        if (data.msg === "changed" && data.collection === "stream-notify-logged" && data.fields && Array.isArray(data.fields.args)) {
            this.userLoginChangedHandler(data.fields.args)
        }
    }


    addEmojiToText(emoji, id) {
        $('#textarea_' + id).val($('#textarea_' + id).val() + emoji.native + " ");
        if (this._ismounted) this.setState({showEmojiPicker: false})
    }

    /**
     * GROUP
     */
    RCInitGroup() {
        RCHelper.getGroups()
            .then(resp => {
                //Find current Group
                if (!resp.groups || !Array.isArray(resp.groups))
                    throw new Error("Group rooms not available")
                let group = resp.groups.find((a) => a._id === this.props.roomId)
                if (this._ismounted) this.setState({currentGroup: group})
                // console.log("group", group)
                return RCHelper.getGroupMembers(this.props.roomId)
            })
            .then(resp => {
                if (!resp.success)
                    throw new Error("Could not receive group members for group")
                //console.log("members", resp)
                if (this._ismounted)
                {
                    let cloudUsers = []
                    resp.members?.forEach(mem =>
                    {
                        let usr = this.props.store.allCloudUsers?.find(u => u.email == mem.username)
                        if(usr) cloudUsers.push({...usr, _id : mem._id, status : mem.status})
                    })
                    this.setState({currentMembersCloudUsers : cloudUsers, currentMembers: resp.members,groupRolesArray : []})
                }
                return RCHelper.getGroupMessagesForRoom(this.props.roomId, 50)
            })
            .then(resp => {
                if (!resp.success)
                    throw new Error("Could not receive messages for group")
                return resp
            })
            .then(messageObj => {
                if (this._ismounted) this.setState({currentMessagesChunk: messageObj}, () => this.prepareMessages())
                if (this._ismounted)  this.setState({componentLoadFailed: false})
            })
            .then( resp =>
            {
                return RCHelper.getGroupRoles(this.props.roomId)
            })
            .then( resp =>
            {
                if(resp.success)
                    return this.setState({groupRolesArray : resp.roles})
            })
            .then( () =>
            {
                return RCHelper.getPinnedMessages(this.props.roomId)
            })
            .then( (resp) =>
            {
                return this.setState({pinnedMessages : resp.messages.map( m => this.preparedMessageFromRawMessage(m))})
            })
            .catch(err => {
                this.setState({componentLoadFailed: true})
                EducaHelper.fireErrorToast("Fehler", err.message)
            })
    }

    sendVoiceMessage(blob) {


        return RCHelper.uploadFileToRoom(
            this.props.type === "group" ? this.state.currentGroup._id : this.props.roomId,
            blob,
            "Sprachnotiz",
            "")
            .then(resp => {
                //console.log(resp)
            })
            .catch(err => {
                console.log(err)
            })

    }

    /**
     * Callback function for file upload.
     * @param objs processed object coming from FileUploadModal
     */
    uploadFiles(objs) {
        if (objs.length > 10)
            return console.error("You cannot upload that amount of files")

        let allPromises = objs.map((obj, id) => {
            return RCHelper.uploadFileToRoom(
                this.props.type === "group" ? this.state.currentGroup._id : this.props.roomId,
                obj.file,
                "",
                obj.message)
        })

        Promise.all(allPromises)
            .then(resp => {
                // console.log(resp)
            })
            .catch(err => {
                console.log(err)
            })

    }


    /**
     * DIRECT
     */

    RCInitDirect() {
        RCHelper.getIMList()
            .then(resp => {
                //Find current Group
                if (!resp.ims || !Array.isArray(resp.ims))
                    throw new Error("Instant Messaging rooms not available")
                let room = resp.ims.find((a) => a._id === this.props.roomId)
                if (this._ismounted) this.setState({currentRoom: room})
                // console.log("room", room)
                return RCHelper.getImMembers(this.props.roomId)
            })
            .then(resp => {
                if (!resp.success)
                    throw new Error("Could not receive IM members for room")
                // console.log("members", resp)
                if (this._ismounted)
                {
                    let cloudUsers = []
                    resp.members?.forEach(mem =>
                    {
                        let usr = this.props.store.allCloudUsers?.find(u => u.email == mem.username)
                        if(usr) cloudUsers.push({...usr, _id : mem._id, status : mem.status})
                    })
                    this.setState({currentMembersCloudUsers : cloudUsers, currentMembers: resp.members,groupRolesArray : []})
                }
                return RCHelper.getIMMessagesForRoom(this.props.roomId, 50)
            })
            .then(resp => {
                if (!resp.success)
                    throw new Error("Could not receive messages for room")
                return resp
            })
            .then(messageObj => {
              if (this._ismounted) this.setState({currentMessagesChunk: messageObj}, () => this.prepareMessages())
              if (this._ismounted)  this.setState({componentLoadFailed: false})
            })
            .catch(err => {
                this.setState({componentLoadFailed: true})
                EducaHelper.fireErrorToast("Fehler", err.message)
            })
            .finally(() =>
            {
                this.setState({pinnedMessages : []})
            })
    }

    emojiMenuClickCallback() {
        if (this._ismounted) this.setState({showEmojiPicker: !this.state.showEmojiPicker})
    }

    /**
     * Loads old messages when scrolled to top
     */
    topMessageLoadTrigger() {
        if (this.state.currentMessagesChunk.total - this.state.currentMessagesChunk.count > 0) {
            let promise = this.props.type === "group" ?
                RCHelper.getGroupMessagesForRoom(this.props.roomId, 25, this.state.currentMessagesChunk.count === 0 ? this.state.currentMessagesChunk.offset : this.state.currentMessagesChunk.count) // Load 25 messages;old count = new offset if count == 0
                :
                RCHelper.getIMMessagesForRoom(this.props.roomId, 25, this.state.currentMessagesChunk.count === 0 ? this.state.currentMessagesChunk.offset : this.state.currentMessagesChunk.count) // Load 25 messages;old count = new offset if count == 0

            promise.then(resp => {
                if (!resp.success)
                    throw new Error("Could not receive messages for group")

                let newMessageChunk = this.state.currentMessagesChunk
                newMessageChunk.count += resp.count //raise the whole count of the current Chunk
                newMessageChunk.offset += resp.count
                newMessageChunk.total = resp.total
                newMessageChunk.messages = resp.messages.concat(this.state.currentMessagesChunk.messages)
                this.setAutoScroll(false)
                if (this._ismounted) this.setState({currentMessagesChunk: newMessageChunk}, () => this.prepareMessages(() => {
                    if (this.ele)
                        $(this.ele).scrollTop(200) // scroll back a little
                }))
            })
                .catch(err => {
                    console.error("ChatView: " + err)
                })
        }

    }


    scrollToBottom() {
        // if(  this.messagesEnd && ( !this.ele || $(this.ele).scrollTop() !== 0) )
        this.messagesEnd.current?.scrollIntoView();
    }

    /**
     * ugly imo :/
     */
    scrollHandler() {
        if (!this.ele)
            this.ele = document.getElementById("chat_wrapper")
        if (this.ele.scrollHeight === 0)
            this.topMessageLoadTrigger()
    }


    /**
     * Prepare messages to be shown. simplifies the structure
     * @param msg Message from Rocket Chat
     */
    preparedMessageFromRawMessage(msg, fromWebSocket = false) {
        return {
            id: msg._id,
            user: {
                ...msg.u,
                avatar: RCHelper.getEducaAvatarForUser(msg.u._id),
                avatarAlt: RCHelper.getAvatarForUserOrGroup(msg.u.username)
            },
            isMe: msg.u._id === this.state.me._id,
            updatedAt: msg._updatedAt,
            ts : msg.ts,
            message: msg.msg ? msg.msg?.replace(/\[ \]\(.*\)/gm, "") : "",
            file: msg.file ? msg.file : undefined,
            editedAt : msg?.editedAt? msg?.editedAt : undefined,
            attachments : msg.attachments,
            pinned : msg.pinned
        }
    }

    /**
     * Prepares Messages that come from the Rocket Chat API
     */
    prepareMessages(callback) {
        //console.log("messages", this.state.currentMessagesChunk)
        let preparedMessages = []
        this.state.currentMessagesChunk.messages.forEach(msg => {
            preparedMessages.push(this.preparedMessageFromRawMessage(msg))
        })
        preparedMessages = preparedMessages.sort((a, b) => {
            let aM = moment(a.ts)
            let bM = moment(b.ts)
            return aM.diff(bM)
        })
        // console.log(preparedMessages)
        if (this._ismounted) this.setState({currentPreparedMessages: preparedMessages}, () => {
            if (callback) callback()
        })
    }

    getUserStatus(userId) {
        for (let key in this.state.currentMembers) {
            if (this.state.currentMembers[key]._id === userId)
                return this.state.currentMembers[key].status
        }
    }

    fileDropHandler(ev)
    {
        let files = []
            // Prevent default behavior (Prevent file from being opened)
            ev.preventDefault();
            if (ev.dataTransfer.items)
                for (let i = 0; i < ev.dataTransfer.items.length; i++)
                    if (ev.dataTransfer.items[i].kind === 'file')
                        files.push(ev.dataTransfer.items[i].getAsFile());
        if(files.length == 0)
            return
        this.fileUploadRef.current.open(files, (objs) => this.uploadFiles(objs))
    }

    /**
     * Posts a new message to the current Chat
     * @param message
     * @param clearCallback
     */
    postMessage(message, clearCallback) {

        let msg = message
        if(this.state.currentCitationMessage)
        {
            let adds = this.props.type == "im"? "[ ]("+RC_HOST+"/direct/"+this.props.roomId+"?msg="+this.state.currentCitationMessage.id+") "
                : this.props.type == "group"? "[ ]("+RC_HOST+"/group/"+this.props.roomId+"?msg="+this.state.currentCitationMessage.id+") "
                    : null
            msg = adds+msg
        }
        if (message !== "")
            RCHelper.postChatMessage(this.props.type === "group" ? this.state.currentGroup._id : this.props.roomId, msg)
                .then(resp => {
                    clearCallback();
                    return RCHelper.markRoomAsRead(this.getRoomId())
                })
                .then(resp => {
                    if (!resp.success)
                        throw new Error("Could not set room as read")
                })
                .catch(err => {
                    console.error("ChatView: Error." + err)
                })
                .finally(() =>
                {
                    this.setState({currentCitationMessage : null})
                })
    }


    /**
     * Deletes an (own) message
     * @param message
     */
    deleteMessage(message)
    {
        if(!message)
            return

        const del = () =>
        {
            RCHelper.deleteChatMessage(this.props.type === "group" ? this.state.currentGroup._id : this.props.roomId, message.id)
                .then( resp =>
                {
                    //Success
                    if(resp._id)
                        return this.deleteMessageLocally(message)
                    throw new Error("")
                })
                .catch(err => {
                    SharedHelper.fireErrorToast("Fehler", "Nachricht konnte nicht gelöscht werden." +err.message)
                    console.error("ChatView: Error." + err)
                })
        }

        this.educaModalRef?.current?.open(
            (btn) => {
                return btn === MODAL_BUTTONS.YES? del() : null
            },
            "Nachricht Löschen",
            <>
                Soll folgende Nachricht gelöscht wirklich werden?
                <div className={"mt-2"} style={{fontSize : "80%"}}>{message.message}</div>
            </>,
            [MODAL_BUTTONS.YES, MODAL_BUTTONS.NO]
            )
    }

    /**
     * Updates an (own) message
     * @param message
     */
    updateMessage(message, text)
    {
        if(!message)
            return

        RCHelper.updateChatMessage(this.props.type === "group" ? this.state.currentGroup._id : this.props.roomId, message.id, text)
            .then( resp =>
            {
                if(!resp.message)
                    throw new Error("")

            })
           .catch(err => {
                 SharedHelper.fireErrorToast("Fehler", "Nachricht konnte nicht editiert werden.")
                 console.error("ChatView: Error." + err)
             })

    }

    /**
     * Pins or Unpins a message
     * @param msg
     */
    togglePinMessage(msg)
    {
        const togglePin = (flag) =>
        {
            let newChunk = _.cloneDeep(this.state.currentMessagesChunk)
            let newPrepared = _.cloneDeep(this.state.currentPreparedMessages)
            let pinnedMessages = _.cloneDeep(this.state.pinnedMessages)
            // replace in chunk
            let index = newChunk.messages?.findIndex(m => m._id == msg.id)
            if (index >= 0)
                newChunk.messages[index].pinned = flag

            index = newPrepared?.findIndex(m => m.id == msg.id)
            if (index >= 0)
                newPrepared[index].pinned = flag

            index = pinnedMessages?.findIndex(m => m.id == msg.id)
            if (index >= 0 && flag)
                pinnedMessages[index] = msg
            else if(index >= 0 && !flag)
                pinnedMessages.splice(index,1)
            else if(index < 0 && flag)
                pinnedMessages.push(msg)

            this.setState({currentMessagesChunk: newChunk, currentPreparedMessages: newPrepared, pinnedMessages : pinnedMessages})
        }
        let isPinned = !!msg.pinned
        if(!isPinned)
            RCHelper.pinMessage(msg.id)
                .then( resp =>
                {
                    SharedHelper.fireSuccessToast("Erfolg", "Die Nachricht wurde erfolgreich pinned.")
                    togglePin(true)
                })
                .catch(() =>
                {
                    SharedHelper.fireErrorToast("Fehler", "Die Nachricht konnte nicht angepinnt werden.")
                })
        else
        RCHelper.unPinMessage(msg.id)
                .then( resp =>
                {
                    SharedHelper.fireSuccessToast("Erfolg", "Die Nachricht wurde erfolgreich unpinned.")
                    togglePin(false)
                })
                .catch(() =>
                {
                    SharedHelper.fireErrorToast("Fehler", "Die Nachricht konnte nicht unpinned werden.")
                })
    }

    deleteMessageLocally(message) {

            let newChunk = _.cloneDeep(this.state.currentMessagesChunk)
            let newPrepared = _.cloneDeep(this.state.currentPreparedMessages)

            // replace in chunk
            let index = newChunk.messages?.findIndex(msg => msg._id == message.id)
            if (index >= 0) {
                    newChunk.messages.splice(index, 1)
                    newChunk.total = newChunk.total - 1
                    newChunk.count = newChunk.count - 1
            }

            index = newPrepared?.findIndex(msg => msg.id == message.id)
            if (index >= 0)
                    newPrepared.splice(index, 1)

            SharedHelper.fireSuccessToast("Erfolg", "Die Nachricht wurde erfolgreich gelöscht.")
            this.setState({currentMessagesChunk: newChunk, currentPreparedMessages: newPrepared})
        }


    getCurrentUserRole() {
        return !Array.isArray(this.state.groupRolesArray)? null : this.state.groupRolesArray.find(o => o.u?._id == this.props.store.rocketChat?.me?._id)
    }

    /**
     * Takes the current prepared messages and returns actual DOM elements
     * @returns {*[]}
     */
    getChatContent() {
        let userRoles = Array.isArray(this.getCurrentUserRole()?.roles)? this.getCurrentUserRole().roles : []
        let lastDate = new Date(null)
        let retWidgets = []
        let newDateFound = false
        this.state.currentPreparedMessages.forEach((msg) => {
            let newDate = new Date((new Date(msg.updatedAt)).toDateString())

            if (lastDate < newDate) {
                newDateFound = true
                retWidgets.push(<Divider key={"devider_" + msg.id}>{newDate.toLocaleDateString("de-DE",)}</Divider>)
                lastDate = new Date(msg.updatedAt)
            }
            let cloudUser = this.state.currentMembersCloudUsers?.find( u => u.email == msg.user.username)

            let citation = undefined
            // Check if the message includes a citation
            if(msg.attachments?.length == 1
                && !!msg.attachments[0].message_link
                && !!msg.attachments[0].author_icon
                && !!msg.attachments[0].author_name)
            {
                citation = _.cloneDeep(msg.attachments[0])
                citation.text = citation.text? citation.text.replace(/\[ \]\(.*\)/gm, "") : ""
                citation.cloudUser = this.state.currentMembersCloudUsers?.find( u => u.email == msg.attachments[0].author_name)
            }

            retWidgets.push(<ChatEntry
                key={msg.id}
                forceWholeMessage={newDateFound}
                message={msg.message}
                from={msg.user.name}
                avatar={cloudUser? AjaxHelper.getCloudUserAvatarUrl(cloudUser.id, 35, cloudUser.image) : msg.user.avatar}
                avatarAlt={msg.user.avatarAlt}
                id={msg.id}
                file={msg.file}
                attachments={msg.attachments}
                citation={citation}
                uKey={msg.id}
                isMe={msg.isMe}
                isPinned={!!msg.pinned}
                canEdit={msg.isMe || userRoles.includes("moderator") || userRoles.includes("owner")}
                canDelete={msg.isMe || userRoles.includes("moderator") || userRoles.includes("owner")}
                canPin={ this.props.type === "group" &&  (msg.isMe || userRoles.includes("moderator") || userRoles.includes("owner")) }
                canReport={true}
                canReply={true}
                editedAt={msg.editedAt}
                userPresenceStatus={this.getUserStatus(msg.user._id)}
                date={moment(msg.ts)}
                reportButtonClickCallback={(msgProps) => this.reportModalRef.current?.open(( flag, contents  ) =>
                    {
                        if( flag )
                            return this.sendReport(contents)
                    },
                    msgProps,
                    this.state.currentMembers,
                    this.state.currentMessagesChunk)}
                deleteButtonClickCallback={ () =>
                {
                    this.deleteMessage(msg)
                }}
                messageUpdatedCallback={(text) =>
                {
                    this.updateMessage(msg, text)
                }}
                createReplyCallback={() =>
                {
                    this.setAutoScroll(false)
                    this.setState({currentCitationMessage : msg}, () =>  this.setAutoScroll(true))
                }}
                pinMessageCallback={ () =>
                {
                    this.togglePinMessage(msg)
                }}

            />)
            newDateFound = false
        })
        return retWidgets

    }

    sendReport(contents) {
        AjaxHelper.reportMessages(contents)
            .then(resp => {
                SharedHelper.fireSuccessToast("Erfolg", "Die Meldung wurde eingereicht.")
            })
            .catch( err=>
            {
                SharedHelper.fireErrorToast("Fehler", "Die Meldung konnte nicht eingereicht werden.")
            })
    }


    getRocket(text) {
        return <div style={{justifyContent: "center", display: "flex", flexDirection: "column", flex: 1}}>
            <div style={{justifyContent: "center", display: "flex", flexDirection: "row", flex: 1}}>
                <div style={{
                    justifyContent: "center",
                    display: "flex",
                    flexDirection: "column",
                }}>
                    <img src="/images/rocket.gif" width={500}/>
                    <div style={{justifyContent: "center", display: "flex", flexDirection: "row", flex: 1}}>
                        <h4>{text}</h4></div>
                </div>
            </div>
        </div>
    }

    getChatView(id) {
        if (this.state.componentLoadFailed)
            return this.getRocket("Ein Fehler ist aufgetreten.")
        return (
            <div style={{display: "flex", flex: 1, flexDirection: "column"}}>
                <div style={{display: "flex", flex: 1}}>
                    {this.state.currentMessagesChunk.total === 0 ?
                        this.getRocket("Noch keine Nachrichten in dieser Unterhaltung.")
                        :
                        <div className="col"
                             style={{
                                 overflowY: "auto",
                                 overflowX: "hidden",
                                 wordBreak: "break-all",
                                 height: "calc(100vh - " + ((this.props.heightOffset ? this.props.heightOffset : 200) + (this.state.pinnedMessages?.length > 0? 125 : 0) )+ "px)"
                             }}
                             id={"chat_wrapper"}
                             onScroll={() => this.scrollHandler()}>
                            {this.getChatContent()}
                            <div id="dummyDiv" style={{float: "left", clear: "both"}} ref={this.messagesEnd}></div>
                        </div>}
                </div>
                <div style={{flex: "1", display: "flex", flexDirection: "column", justifyContent: "flex-end"}}>
                    <Card.Footer>
                        {this.state.showEmojiPicker ? <div style={emojiPickerStyles}>
                            <Picker
                                i18n={{
                                    search: 'Suche',
                                    clear: 'Reset', // Accessible label on "clear" button
                                    notfound: 'Kein Emoji gefunden',
                                    skintext: 'Wähle deine Standard-Hautfarbe',
                                    categories: {
                                        search: 'Suchergebnisse',
                                        recent: 'Zuletzt',
                                        smileys: 'Smileys',
                                        people: 'Personen',
                                        nature: 'Tiere & Nature',
                                        foods: 'Essen & Trinken',
                                        activity: 'Aktivitäten',
                                        places: 'Reisen & Orte',
                                        objects: 'Objekte',
                                        symbols: 'Symbole',
                                        flags: 'Flaggen',
                                        custom: '',
                                    },
                                    categorieslabel: 'Emoji Kategorien', // Accessible title for the list of categories
                                    skintones: {
                                        1: 'Default Skin Tone',
                                        2: 'Light Skin Tone',
                                        3: 'Medium-Light Skin Tone',
                                        4: 'Medium Skin Tone',
                                        5: 'Medium-Dark Skin Tone',
                                        6: 'Dark Skin Tone',
                                    }
                                }}
                                theme="dark"
                                style={{zIndex: 2}}
                                onSelect={(emoji) => this.addEmojiToText(emoji, id)}
                                showSkinTones={false}
                                showPreview={false}/>
                            <button className={"btn"}
                                    style={{marginLeft: "-25px", marginTop: "-20px", maxHeight: "50px", zIndex: 3}}><i
                                className="fa fa-times-circle" style={{fontSize: "23px"}} onClick={() => {
                                if (this._ismounted) this.setState({showEmojiPicker: false})
                            }}/></button>
                        </div> : null}
                        {this.props.canWriteMessage? <TextInputComponent
                            clickCallback={(msg, clearCallback) => this.postMessage(msg, clearCallback)}
                            recordFinishedCallback={(blob) => {
                                this.sendVoiceMessage(blob)
                            }}
                            citation={this.state.currentCitationMessage}
                            resetCitation={() =>  {
                                this.setAutoScroll(false)
                                this.setState({currentCitationMessage : null}, () =>  this.setAutoScroll(true))}
                            }
                            fileUploadOnChangeCallback={(evt) => this.fileUploadRef.current.open(evt.target.files, (objs) => this.uploadFiles(objs))}
                            emojiClickCallback={() => this.emojiMenuClickCallback()}
                            uniqueID={this.props.roomId}
                        /> : null}</Card.Footer>
                </div>
            </div>
        );
    }

    closeChat() {
        if(!this.props.type)
            return;

        RCHelper.closeChatMessage(this.props.type === "group" ? this.state.currentGroup._id : this.props.roomId, this.props.type)
            .then( resp =>
            {
                if(!resp.success)
                    throw new Error("")

                window.location.href = "/app/messages"
            })
            .catch(err => {
                SharedHelper.fireErrorToast("Fehler", "Der Chat konnte nicht verborgen werden.")
                console.error("ChatView: Error." + err)
            })

    }

    deleteChat()
    {
        let del = () => RCHelper.deleteRoom(this.props.roomId)
            .then( resp =>
            {
                SharedHelper.fireSuccessToast("Erfolg", "Der Chat wurde gelöscht. Diese Seite wird in einigen Sekunden aktualisiert")
                setTimeout( () => window.location.href = "/app/messages", 1500);
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Der Chat konnte nicht gelöscht werden.")
            })
        const keyword = "LÖSCHEN"
        this.deleteModalRef?.current?.open( (flag) => flag? del() : null, "Chat Löschen", "Soll der Chat wirklich gelöscht werden? Alle Daten gehen unwiderruflich verloren.", keyword)
    }

    editChat()
    {
        this.editChatModalRef?.current?.open(this.state.currentGroup, this.state.currentMembersCloudUsers, this.state.currentMembers, this.state.groupRolesArray)
    }

    getChatHeader() {

        if(this.props.hideHeader)
            return null

        const allRoles = Array.isArray(this.state.groupRolesArray)? this.state.groupRolesArray : []
        const userRoles = Array.isArray(this.getCurrentUserRole()?.roles)? this.getCurrentUserRole()?.roles : []

        const  PinnedMessagesViewer = (pinProps) => {

            let [collapsed, setCollapsed] = useState(true)

            if( !(this.state.pinnedMessages?.length > 0) )
                return null
            return <div style={{background : "rgba(0, 0, 0, 0.1)"}}>
                <div style={{display : "flex"}}>
                    <h5 className={"m-2"}>Gepinnte Nachrichten</h5>
                    <Button onClick={() => setCollapsed(!collapsed)}
                            className={collapsed? "btn btn-light mr-1 btn btn-primary" : "btn mr-1 btn btn-info"}>
                        <i className={"fas fa-eye"}/>
                    </Button>
                </div>

                <div className={"m-2"}>
                {this.state.pinnedMessages?.map( (msg,i) =>
                {
                    msg = {...msg, pinned  :true}
                    if(collapsed && i >= 0)
                        return
                    let cloudUser = this.state.currentMembersCloudUsers?.find( u => u.email == msg.user?.username)
                    return <ChatEntry
                        key={msg.id}
                        forceWholeMessage={true}
                        message={msg.message}
                        from={msg.user?.name}
                        avatar={cloudUser? AjaxHelper.getCloudUserAvatarUrl(cloudUser.id, 35, cloudUser.image) : msg.user?.avatar}
                        avatarAlt={msg.user?.avatarAlt}
                        id={msg.id}
                        file={msg.file}
                        attachments={msg.attachments}
                        editedAt={msg.editedAt}
                        userPresenceStatus={this.getUserStatus(msg.user?._id)}
                        date={moment(msg.ts)}

                        isPinned={true}
                        isPinnedOnTopMessage={true}
                        canPin={true}
                        pinMessageCallback={ () =>
                        {
                            this.togglePinMessage(msg)
                        }}
                    />
                })
                }
                </div>
            </div>
        }

        let membersList = this.state.currentMembersCloudUsers;
        // Fallback: if the cloud user was not found, add the RC user
        if( this.state.currentMembersCloudUsers?.length < this.state.currentMembers?.length )
        {
            this.state.currentMembers?.forEach( rcMem =>
            {
                if(!this.state.currentMembersCloudUsers?.find( c =>  rcMem._id === c._id ))
                    membersList.push( rcMem )
            })
        }

        return <><Navbar sticky="top" bg="dark" variant="dark"
                       style={{borderTopLeftRadius: "0.25rem", borderTopRightRadius: "0.25rem"}}>
            <div style={{display: "flex", flexDirection: "row"}} className={"mr-auto"}>
                <EducaCircularButton
                    data-tip={"tooltip"}
                    variant={"secondary"}
                    style={{margin: "10px", height: "40px"}}
                >
                    {membersList.length}
                </EducaCircularButton>
                <ReactTooltip
                    place={"bottom"}
                >
                    {membersList.map(mem => {
                        let memberRoles = allRoles?.find( r => r?.u?._id == mem._id )?.roles?.map( role => role =="owner"? "Besitzer": role =="moderator"? "Moderator" : "")
                        return <div key={mem._id} style={{display: "flex", flexDirection: "row"}}>
                            {EducaHelper.getStatusImage(mem.status)}
                            <div>
                                {mem._id === this.state.me._id ? "Ich" : mem.name}
                                {memberRoles?.length > 0? " ("+memberRoles.join(",")+")" : null}
                            </div>
                        </div>
                    })}
                </ReactTooltip>

                <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}}>
                    <div style={{display: "flex", flexDirection: "row", color: "white"}}>
                        {membersList.map((mem, index) => {
                            let threshold = 4
                            if (mem._id === this.state.me._id)
                                return
                            if (index == threshold)
                                return <div key={mem._id} style={{marginRight: "5px"}}>
                                    <h5>{"und "}{(membersList.length - threshold + 1) + " weitere"}</h5>
                                </div>
                            if (index > threshold)
                                return
                            return <div key={mem._id} style={{marginRight: "5px"}}>
                                <h5> {mem.name}{index == (threshold - 2) || this.props.type !== "group" ? "" : ","}</h5>
                            </div>
                        })}
                    </div>
                </div>
            </div>
            <div className={"float-right"}>
                <div className={"d-flex"}>
                    <div style={{marginTop :"5px", marginRight :"5px"}}>{userRoles.includes("owner")? <i style={{color : "white"}} className={"fas fa-user-circle fa-1x"}/> : null} </div>
                    {this.state.currentGroup?.name? <div style={{color:"white", marginRight : "5px"}}>{this.state.currentGroup.name}</div> : ""}
                    { this.props.type !== "group"
                    || (userRoles.includes("owner") /*)*/&& this.props.canDelete)?

                        <Dropdown drop={"left"} >
                        <Dropdown.Toggle variant="light" id="dropdown-basic">
                            <i className="fas fa-ellipsis-v"></i>
                        </Dropdown.Toggle>

                        <Dropdown.Menu>
                            {this.props.type !== "group"? <Dropdown.Item onClick={() => this.closeChat()}><i className={"fas fa-eye-slash"}/> Chat verbergen</Dropdown.Item> : null}
                            { /*(userRoles.includes("moderator") || */userRoles.includes("owner") /*)*/&& this.props.canDelete? <Dropdown.Item onClick={() => this.deleteChat()}><i className={"fas fa-trash"}/> Chat Löschen</Dropdown.Item> : null}
                            { /*(userRoles.includes("moderator") || */userRoles.includes("owner") /*)*/&& this.props.canDelete? <Dropdown.Item onClick={() => this.editChat()}><i className={"fas fa-pencil-alt"}/> Chat bearbeiten</Dropdown.Item> : null}
                        </Dropdown.Menu>
                    </Dropdown> : null}
                </div>
            </div>
        </Navbar>
        <PinnedMessagesViewer/>
        </>
    }

    render() {
        const {canDrop} = this.props
        return <>
            {canDrop? <div style={styles.overlay}
                           onDrop={(evt) => {

                               //Workaround for not working drop in react DnD
                                this.fileDropHandler(evt)
                           }} >
                <div style={{position : "absolute", top : "50%", left : "45%"}}>
                    <i style={{color : "white"}} className="fas fa-upload fa-9x"/>
                    <div style={{color : "white", marginLeft :"-25px"}}>Hineinziehen zum Hochladen</div>
                </div>

            </div>: null}
            <div style={{flex: "1", display: "flex", flexDirection: "column"}}>

                {this.getChatHeader()}
                {this.getChatView(this.props.roomId)}
                <FileUploadDialogRC ref={this.fileUploadRef}/>
            </div>
            <EducaReportModal size={"lg"} ref={this.reportModalRef}/>
            <EducaModal ref={this.educaModalRef}/>
            <SafeDeleteModal ref={this.deleteModalRef}/>
            <GroupChatIMEditModal reloadTrigger={() => {this.componentWillUnmount(); this.componentDidMount()}} ref={this.editChatModalRef}/>
        </>
    }
}

const mapStateToProps = state => ({store: state})

const mapDispatchToProps = dispatch => {
    return {
        setRocketChatMe: (me) => dispatch({type: ROCKET_CHAT_SET_ME, payload: me}),
    }
}
const dropSpecs = {
}

function collect(connect, monitor) {
    return {
        // Call this function inside render()
        // to let React DnD handle the drag events:
        connectDropTarget: connect.dropTarget(),
        // You can ask the monitor about the current drag state:
        isOver: monitor.isOver(),
        isOverCurrent: monitor.isOver({ shallow: true }),
        canDrop: monitor.canDrop(),
        itemType: monitor.getItemType(),
        item : monitor.getItem(),
        result :  monitor.getDropResult(),
    }
}


const ChatViewDnD = (DropTarget([NativeTypes.FILE], dropSpecs,collect)(connect(mapStateToProps, mapDispatchToProps)(ChatView)))

const ChatViewWrapper = (props) => {

    return <DndProvider backend={SharedHelper.getDnDHTML5Backend()}>
        <ChatViewDnD {...props}/>
    </DndProvider>
}



export default ChatViewWrapper;
