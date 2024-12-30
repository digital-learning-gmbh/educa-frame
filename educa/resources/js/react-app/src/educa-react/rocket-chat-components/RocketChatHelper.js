/**
 *
 * Creator: 0x539
 * Sat 2020 Dec 5
 */

import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import getRocketChatEventManagerInstance, {resetRocketChatEventManagerInstance} from "./RocketChatEventManager";
import moment from "moment";

const API_ROOT = "/api/v1/"
export const RC_HOST = "https://chat-weiterbildung.educa-portal.de"
const BASE = RC_HOST + API_ROOT
export const COOKIE_RC_ACCESS_TOKEN = "educa_rc_token"
export const COOKIE_RC_UID_TOKEN = "educa_rc_uid"

// Endpoints

// AUTH
const API_LOGIN = "login"
const API_ME = "me"

// Instant Messaging
const API_IM_LIST = "im.list"
const API_IM_MESSAGES = "im.messages"
const API_IM_MEMBERS = "im.members"
const API_IM_CLOSE = "im.close"

// Groups
const API_GROUPS_MESSAGES = "groups.messages"
const API_GROUPS_LIST = "groups.list"
const API_GROUPS_MEMBERS = "groups.members"
const API_GROUPS_MODERATORS = "groups.moderators"
const API_GROUPS_CLOSE = "groups.close"
const API_GROUP_ROLES = "groups.roles"
const API_GROUPS_DELETE = "groups.delete"
const API_GROUPS_KICK_USER = "groups.kick"
const API_GROUPS_ADD_USER = "groups.invite"
const API_GROUPS_RENAME = "groups.rename"
// Channels
const API_CHANNEL_MESSAGES = "channels.messages"
const API_CHANNEL_LIST = "channels.list"
const API_CHANNEL_MEMBERS = "channels.members"
const API_CHANNEL_ROLES="channels.roles"

// Chat
const API_CHAT_POSTMESSAGE = "chat.postMessage"
const API_CHAT_DELETE_CHAT = "chat.delete"
const API_CHAT_UPDATE_CHAT = "chat.update"

// Rooms
const API_UPLOAD_TO_ROOM = "rooms.upload"

// Subscriptions
const API_SUBSCRIPTIONS_READ = "subscriptions.read"
const API_SUBSCRIPTIONS_GET = "subscriptions.get"

// Avatar
const API_AVATAR = "avatar/"
const EDUCA_API_AVATAR = "/api/image/rocketchat?id="

//Users

const API_SET_STATUS = "users.setStatus"
const API_USERS_PRESENCE = "users.presence"

//Permissions

const API_PERMISSIONS_LIST_ALL = "permissions.listAll"

// pin messages

const API_UNPIN_MESSAGE = "chat.unPinMessage"
const API_PIN_MESSAGE = "chat.pinMessage"
const API_GET_PINNED_MESSAGES = "chat.getPinnedMessages"

// Sounds

const NOTIFICATION_SOUND = "/audio/icq.mp3"

class RocketChatHelper {

    constructor() {
        this._get = this._get.bind(this)
        this._post = this._post.bind(this)

        this.newMessageNotificationSound = new Audio(NOTIFICATION_SOUND)

    }

    getDefaultHttpHeaders() {
        return {
            'Accept': '*/*',
            'Content-Type': 'application/json',
            'X-Auth-Token': this.getAccessToken(),
            'X-User-Id': this.getRCUserId()
        }
    }

    _get(endpoint, params = {}, base = BASE) {

        return fetch(base + endpoint + SharedHelper.concatParams(params),
            {
                headers: this.getDefaultHttpHeaders(),
                method: "GET",
            })
            .then(resp => resp.json())
    }

    _post(endpoint, payload = {}, base = BASE) {

        return fetch(base + endpoint,
            {
                headers: this.getDefaultHttpHeaders(),
                method: "POST",
                body: JSON.stringify(payload)
            })
            .then(resp => resp.json())
    }

    /**
     * AUTH
     */

    getAccessToken() {
        return SharedHelper.getCookie(COOKIE_RC_ACCESS_TOKEN)
    }

    getRCUserId() {
        return SharedHelper.getCookie(COOKIE_RC_UID_TOKEN)
    }

    getMeAndInit() {
        return this._get(API_ME)
    }

    /**
     *  IM
     */

    getIMList(count = 100, offset = 0, sort = '{"value": 1, "_id": 1}') {
        return this._get(API_IM_LIST,
            {
                count: count,
                offset: offset,
                sort: sort
            })
    }

    getIMMessagesForRoom(roomId, count = 50, offset = 0, sort = '{"_updatedAt": -1}') {
        return this._get(API_IM_MESSAGES,
            {
                roomId: roomId,
                count: count,
                offset: offset,
                sort: sort
            })
    }

    getImMembers(roomId, count = 100, offset = 0, sort = '{"value": 1, "_updatedAt": 1}') {
        return this._get(API_IM_MEMBERS,
            {
                roomId: roomId,
                count: count,
                offset: offset,
                sort: sort
            })
    }

    /**
     * Groups
     */

    getGroupMessagesForRoom(roomId, count = 50, offset = 0, sort = '{"_updatedAt": -1}') {
        return this._get(API_GROUPS_MESSAGES,
            {
                roomId: roomId,
                count: count,
                offset: offset,
                sort: sort
            })
    }

    getGroups(count = 100, offset = 0, sort = '{"value": 1, "_updatedAt": 1}') {
        return this._get(API_GROUPS_LIST,
            {
                count: count,
                offset: offset,
                sort: sort
            })
    }

    getGroupMembers(roomId, count = 100, offset = 0, sort = '{"value": 1, "_updatedAt": 1}') {
        return this._get(API_GROUPS_MEMBERS,
            {
                roomId: roomId,
            })
    }

    getGroupRoles(roomId, roomName = undefined)
    {
        return this._get(API_GROUP_ROLES,
            {
                roomId: roomId,
            })
    }

    deleteRoom(roomId)
    {
        return this._post(API_GROUPS_DELETE, {roomId : roomId})
    }

    kickUserFromGroup(roomId, userId)
    {
        return this._post(API_GROUPS_KICK_USER, {roomId : roomId, userId : userId})
    }

    addUserToGroup(roomId, userId)
    {
        return this._post(API_GROUPS_ADD_USER, {roomId : roomId, userId : userId})
    }

    renameGroup(roomId, name)
    {
        return this._post(API_GROUPS_RENAME, {roomId : roomId, name : name})
    }


    /**
     * Channel
     */

    getChannelMessagesByRoomName(roomId, count = 100, offset = 0, sort = '{"value": 1, "_updatedAt": 1}') {
        return this._get(API_CHANNEL_MESSAGES,
            {
                roomId: roomId,
                count: count,
                offset: offset,
                sort: sort
            })
    }

    getChannels(count = 100, offset = 0, sort = '{"value": 1, "_updatedAt": 1}') {
        return this._get(API_CHANNEL_LIST,
            {
                count: count,
                offset: offset,
                sort: sort
            })
    }

    getChannelMembers(roomName, count = 100, offset = 0, sort = '{"value": 1, "_updatedAt": 1}') {
        return this._get(API_CHANNEL_MEMBERS,
            {
                roomName: roomName,
                count: count,
                offset: offset,
                sort: sort
            })
    }

    getChannelRoles(roomId, roomName = undefined)
    {
        return this._get(API_CHANNEL_ROLES,
            {
                roomId: roomId,
            })

    }

    /**
     *  Chat
     */

    postChatMessage(roomId, text) {
        //console.log(text)
        return this._post(API_CHAT_POSTMESSAGE,
            {
                roomId: roomId,
                text: text
            })
    }

    deleteChatMessage(roomId, messageId, asUser=true)
    {
        return this._post(API_CHAT_DELETE_CHAT,
            {
                roomId: roomId,
                msgId : messageId,
                asUser: asUser
            })
    }

    updateChatMessage(roomId, messageId, text)
    {
        return this._post(API_CHAT_UPDATE_CHAT,
        {
            roomId: roomId,
            msgId : messageId,
            text: text
        })

    }

    pinMessage(messageId)
    {
        return this._post(API_PIN_MESSAGE, {messageId : messageId})
    }

    unPinMessage(messageId)
    {
        return this._post(API_UNPIN_MESSAGE, {messageId : messageId})
    }

    getPinnedMessages(roomId)
    {
        return this._get(API_GET_PINNED_MESSAGES, {roomId : roomId})
    }

    /**
     * Avatar
     */

    getAvatarForUserOrGroup(subject, isGroup = false) {
        return RC_HOST + "/" + API_AVATAR + (isGroup ? "@" + subject : subject);
    }

    getEducaAvatarForUser(uid) {
        return EDUCA_API_AVATAR + uid
    }


    /**
     * Rooms
     */

    uploadFileToRoom(roomId, file, msg, description /*, tmid*/) {
        let headers = {
            'X-Auth-Token': this.getAccessToken(),
            'X-User-Id': this.getRCUserId()
        }
        let data = new FormData()
        data.append("file", file)
        data.append("msg", msg? msg : " ")
        data.append("description", description)

        return fetch(BASE + API_UPLOAD_TO_ROOM + "/" + roomId,
            {
                headers: headers,
                method: "POST",
                body: data
            }).then(resp => resp.json())
    }

    /**
     * Subscrptions
     */

    markRoomAsRead(roomId) {
        return this._post(API_SUBSCRIPTIONS_READ, {rid: roomId})
    }

    getSubscriptions() {
        return this._get(API_SUBSCRIPTIONS_GET)
    }

    /**
     * Eventmanager
     */
    getRcEventManager() {
        return getRocketChatEventManagerInstance();
    }

    destructRcEventManager() {
        resetRocketChatEventManagerInstance()
    }


    /**
     * Sounds
     */

    playNewMessageNotification() {
        if(localStorage.getItem('disableSound'))
            return; //
        this.newMessageNotificationSound.play()
            .then(resp => {
                //nix!!
            })
    }

    getFileLink(id, fileName = "") {
        return RC_HOST + "/file-upload/" + id + "/" + fileName
    }

    /**
     * Users
     */

    setStatus(status, message = "") {
        return this._post(API_SET_STATUS, {message: message, status: status})
    }

    getUsersPresence(fromIso8601Timestamp= moment.unix(0).format()) {
        return this._get(API_USERS_PRESENCE, fromIso8601Timestamp? {from : fromIso8601Timestamp} : undefined)
    }

    //Permissions


    getAllPermissions()
    {
        return this._get(API_PERMISSIONS_LIST_ALL)
    }

    closeChatMessage(id, type)
    {
        return this._post(API_IM_CLOSE,
            {
                roomId: id,
            })
    }

    integerToStatusText(int)
    {
        if (int === 1)
            return "online"
        if (int === 2)
            return "away"
        if (int === 3)
            return "busy"
        return "offline"
    }

    getCitationStyle()
    {
        return {border : "1px #00000026 solid",
            borderLeft :"3px solid",
            borderRadius : "3px",
            padding : "5px",
            background: "rgba(0, 0, 0, 0.05)"}
    }
}

export let RCHelper = new RocketChatHelper()

export default RocketChatHelper
