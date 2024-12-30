import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import {RCHelper} from "./RocketChatHelper";
import EventManager from "../EducaEventManager";

const WSS_HOST = "wss://chat-weiterbildung.educa-portal.de/websocket"

const DEBUG = 0

let instance = null
let isUserLoggedIn = true

EventManager.registerLoginEventHandler("RocketChatEventManager", () => {
    if (DEBUG) console.log("RocketChatEventManager: Received Login Event")
    isUserLoggedIn = true
})

EventManager.registerLogoutEventHandler("RocketChatEventManager", () => {
    if (DEBUG) console.log("RocketChatEventManager: Received Logout Event")
    isUserLoggedIn = false
    resetRocketChatEventManagerInstance()
})

//Singleton
export default function getRocketChatEventManagerInstance(forceRenewal) {
    if (!isUserLoggedIn)
        return null
    if (!instance || forceRenewal) {
        instance = new RocketChatEventManager()
    }
    return instance
}

export function resetRocketChatEventManagerInstance() {
    instance?.reset()
    instance = null
}

const STREAM_ROOM_MESSAGE_SUBSCRIPTION_KEY = "stream-room-messages"

const NOTIFY_LOGGED_SUBSCRIPTION_KEY = "stream-notify-logged"

const NOTIFY_USER_SUBSCRIPTION_KEY = "stream-notify-user"


const EVENT_SUBSCRIPTIONS_CHANGED_KEY = "subscriptions-changed"
const EVENT_USER_STATUS_CHANGED_KEY = "users-status"
const EVENT_MESSAGE_CHANGED_KEY = "message" // NOT SUPPORTED https://github.com/RocketChat/Rocket.Chat/issues/12202


/*
        HOW IT WORKS

There are multiple events available. You can register an event handler by calling the subscribeToXYZ Methods.
A minimum of 2 parameters are necessary. One of them is the registration ID which has to be unique. In some
scenarios you might use a roomID with a prefix for instance. The other parameter is the callback function,
that is executed each time the subscription messages from the WebSocket match you subscription type. However,
this is not the case for e.g. STREAM_ROOM_MESSAGE_SUBSCRIPTION, since you have to define a roomId for it. So
all subsequent events will only call your callback function when ever the subscription type AND the room Id match.

Note: Please unregister the event handlers if possible


 */
class RocketChatEventManager {

    constructor() {
        if (DEBUG) console.log("RocketChatEventManager: Constructing")
        this.amountUnreadMessages = 0

        this._wsMessageReceiveHandler = this._wsMessageReceiveHandler.bind(this)
        this._wsReconnect = this._wsReconnect.bind(this)
        this._wsOnOpen = this._wsOnOpen.bind(this)
        this.subscribeToStreamRoomMessages = this.subscribeToStreamRoomMessages.bind(this)

        this._initWebSocket()
        // If a subscription is requested, but the websocket is not open yet, this queue will be executed ASAP
        this.onWebSocketOpenFunctionQueue = []

        //array of all StreamRoomMessage subscriptions
        this.currentStreamRoomMessageSubscriptionIds = []

        this.streamRoomMessageHandlers = []

        this.notifyLoggedUserStatusHandlers = []

        this.notifyUserSubscriptionsChanged = []
    }

    reset() {
        if (DEBUG) console.log("RocketChatEventManager: Resetting ")

        // Unsubscribing is not necessary, since the WSS gets closed
        /*
        //Unsubscribe rooms
        this.streamRoomMessageHandlers.forEach( handler =>
        {
            this.unSubscribeToStreamRoomMessages(handler.registrationId)
        })

         this.onWebSocketOpenFunctionQueue = []
         this.currentStreamRoomMessageSubscriptionIds = []
         this.streamRoomMessageHandlers = []
         this.notifyLoggedUserStatusHandlers = []
         this.notifyUserSubscriptionsChanged = []
        */
        this.rcWebSocket.onclose = () => {
        } // remove old handler that auto reconnects
        this.rcWebSocket.close()
    }

    _initWebSocket() {
        this.rcWebSocket = new WebSocket(WSS_HOST)
        this.rcWebSocket.onmessage = this._wsMessageReceiveHandler
        this.rcWebSocket.onopen = () => this._wsOnOpen()
        this.rcWebSocket.onclose = () => this._wsReconnect()
    }


    /**
     * Called when the websocket opens
     * @private
     */
    _wsOnOpen() {
        this._wsRcConnectAndLogin();
        this._initNotifyLoggedUserStatus();
        this._initNotifyUserSubscriptionsChanged();
        //Loop over queue and execute all functions
        this.onWebSocketOpenFunctionQueue.forEach(func => {
            func();
        })
        this.onWebSocketOpenFunctionQueue = [] // flush
    }

    _wsRcConnectAndLogin() {
        this.rcWebSocket.send(JSON.stringify({
                "msg": "connect",
                "version": "1",
                "support": ["1"]
            })
        )
        this._wsRcLogin()
    }

    _wsRcLogin() {
        this.rcWebSocket.send(JSON.stringify({
                "msg": "method",
                "method": "login",
                "id": "" + "login_id_" + Date.now(),
                "params": [{"resume": RCHelper.getAccessToken()}] // changes according to the auth used
            })
        )
    }

    /**
     * Each ping from the WSS connection needs to be answered by pong
     * @param webSocket
     */
    _wsPong() {
        if (DEBUG) console.log("RocketChatEventManager: Sending Pong")
        this.rcWebSocket.send(JSON.stringify({"msg": "pong",}))
    }


    _wsMessageReceiveHandler(evt) {
        if (DEBUG) console.log("RocketChatEventManager: Received Event : " + evt.data)
        if (!evt.data) {
            SharedHelper.logError("Websocket: Malformed Websocket data")
            return
        }
        let data = null
        try {
            data = JSON.parse(evt.data)
        } catch {
            SharedHelper.logError("Websocket: Cannot parse JSON data")
        }

        if (!data) return
        // console.log(data)
        //Check if a response id matches to handlers
        if (data.msg === "ping") {
            return this._wsPong()
        } else if (data.msg === "error") {
            return SharedHelper.logError("WS error: " + data.reason)
        } else if (data.collection === STREAM_ROOM_MESSAGE_SUBSCRIPTION_KEY) {
            if (data.fields && data.fields.args && data.fields.args.length > 0 && data.fields.args[0].rid) {
                this.streamRoomMessageHandlers.forEach(handlerObj => {
                    if (handlerObj.roomId === data.fields.args[0].rid)
                        handlerObj.callback(data)
                })
            } else
                //else something went wrong
                SharedHelper.logError("Websocket: Changed Handler: Fields are malformed.")
        } else if (data.collection === NOTIFY_LOGGED_SUBSCRIPTION_KEY) {

            //TODO better distinguishing
            this.notifyLoggedUserStatusHandlers.forEach(handlerObj => {
                handlerObj.callback(data);
            })
        } else if (data.collection === NOTIFY_USER_SUBSCRIPTION_KEY) {

            if (data.fields && data.fields.eventName.includes(EVENT_SUBSCRIPTIONS_CHANGED_KEY))
                return this.notifyUserSubscriptionsChanged.forEach(handlerObj => {
                    handlerObj.callback(data);
                })
        }


    }

    /**
     *
     *  NotifyLoggesUserStatus is a unique universal broadcast event
     */

    _initNotifyLoggedUserStatus() {
        this.rcWebSocket.send(JSON.stringify({
                "msg": "sub",
                "id": EVENT_USER_STATUS_CHANGED_KEY/*+ Date.now()*/,
                "name": NOTIFY_LOGGED_SUBSCRIPTION_KEY,
                "params": [
                    "user-status",
                    false
                ]
            })
        )
    }


    subscribeToNotifyLoggedUserStatus(registrationId, onUserStatusChangedCallback) {

        //Check whether the subscription is already set. If yes, print error (this really shouldn't happen)
        for (let i = 0; i < this.notifyLoggedUserStatusHandlers.length; i++) {
            if (this.notifyLoggedUserStatusHandlers[i].registrationId === registrationId) {
                this.notifyLoggedUserStatusHandlers[i].callback = onUserStatusChangedCallback
                return SharedHelper.logWarning("RocketChatEventManager: You tried to add 2 event handlers for 1 id. I will replace the first one, with the last one.")

            }
        }

        this.notifyLoggedUserStatusHandlers.push(
            {
                registrationId: registrationId,
                callback: onUserStatusChangedCallback
            })

    }

    unSubscribeToNotifyLogged(registrationId) {
        for (let i = 0; i < this.notifyLoggedUserStatusHandlers.length; i++) {
            if (this.notifyLoggedUserStatusHandlers[i].registrationId === registrationId) {
                this.notifyLoggedUserStatusHandlers.splice(i, 1)
            }
        }
    }


    /**
     *
     *  NotyUserSubscriptionsChanged is a unique universal broadcast event
     */

    _initNotifyUserSubscriptionsChanged() {
        this.rcWebSocket.send(JSON.stringify({
                "msg": "sub",
                "id": EVENT_SUBSCRIPTIONS_CHANGED_KEY/*+ Date.now()*/,
                "name": NOTIFY_USER_SUBSCRIPTION_KEY,
                "params": [
                    RCHelper.getRCUserId() + "/subscriptions-changed",
                    false
                ]
            })
        )
    }


    subscribeToNotifyUserSubscriptionsChanged(registrationId, onNotifyUserSubscriptionsChangedCallback) {

        //Check whether the subscription is already set. If yes, print error (this really shouldn't happen)
        for (let i = 0; i < this.notifyUserSubscriptionsChanged.length; i++) {
            if (this.notifyUserSubscriptionsChanged[i].registrationId === registrationId) {
                this.notifyUserSubscriptionsChanged[i].callback = onNotifyUserSubscriptionsChangedCallback
                return SharedHelper.logWarning("RocketChatEventManager: You tried to add 2 event handlers for 1 id. I will replace the first one, with the last one.")

            }
        }

        this.notifyUserSubscriptionsChanged.push(
            {
                registrationId: registrationId,
                callback: onNotifyUserSubscriptionsChangedCallback
            })

    }

    unSubscribeToNotyUserSubscriptionsChanged(registrationId) {
        for (let i = 0; i < this.notifyUserSubscriptionsChanged.length; i++) {
            if (this.notifyUserSubscriptionsChanged[i].registrationId === registrationId) {
                this.notifyUserSubscriptionsChanged.splice(i, 1)
            }
        }
    }


    /**
     * Special case for room messages, since each room needs an own subscription
     *
     */

    _wsRcUnsubscribeRoom(roomId) {
        this.rcWebSocket.send(JSON.stringify({
                "msg": "unsub",
                "id": roomId,
            })
        )
    }


    subscribeToStreamRoomMessages(registrationId, roomId, onMessageReceiveCallback) {
        let needNewSubscription = true
        //check if there is already a subscription for this room
        this.currentStreamRoomMessageSubscriptionIds.forEach(id => {
            if (id === roomId)
                needNewSubscription = false
        })

        //Check whether the handler is already registered.
        for (let i = 0; i < this.streamRoomMessageHandlers.length; i++) {
            if (this.streamRoomMessageHandlers[i].registrationId === registrationId) {
                this.streamRoomMessageHandlers[i].callback = onMessageReceiveCallback
                return SharedHelper.logWarning("RocketChatEventManager: You tried to add 2 event handlers for 1 id. I will replace the first one, with the last one.")
            }

        }

        this.streamRoomMessageHandlers.push(
            {
                registrationId: registrationId,
                roomId: roomId,
                callback: onMessageReceiveCallback
            })
        //If we dont need a new subscription, just return after adding the handler
        if (!needNewSubscription)
            return

        //Subscribe function
        let subscr = () => {
            this.rcWebSocket.send(JSON.stringify({
                    "msg": "sub",
                    "id": roomId + "",
                    "name": STREAM_ROOM_MESSAGE_SUBSCRIPTION_KEY,
                    "params": [
                        roomId
                    ]
                })
            )
        }
        if (this.rcWebSocket.readyState !== WebSocket.OPEN)
            this.onWebSocketOpenFunctionQueue.push(subscr)
        else
            subscr()
    }

    unSubscribeToStreamRoomMessages(registrationId) {
        for (let i = 0; i < this.streamRoomMessageHandlers.length; i++) {
            if (this.streamRoomMessageHandlers[i].registrationId === registrationId) {
                let roomId = this.streamRoomMessageHandlers[i].roomId
                if (DEBUG) console.log("RocketChatEventManager: Unsubscribing room Id: " + roomId)
                this._wsRcUnsubscribeRoom(roomId) // unsubscribe manually
                this.streamRoomMessageHandlers.splice(i, 1)
            }
        }
    }

    _wsReconnect() {
        console.log("RocketChatEventManager: WSS broke connection. Reconnecting...")
        window.setTimeout(() => {
            this._initWebSocket();
        }, 1000)
    }


    setAmountOfUnreadMessages() {

    }

}
