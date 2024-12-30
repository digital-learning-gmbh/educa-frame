import React, {Component} from 'react';
import {Card, Form, FormControl, InputGroup, ListGroup, ListGroupItem, Nav, Tab} from "react-bootstrap";
import {connect} from "react-redux";
import {
    EducaCircularButton,
    EducaPrimaryButtonWrapped,
    EducaSecondaryButton
} from "../../shared/shared-components/Buttons";
import ChatView from "../rocket-chat-components/educa-chat-react/chat-components/ChatView";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import {getEducaChatMessageListGroupContent} from "../educa-components/RCList"
import {RCHelper} from "../rocket-chat-components/RocketChatHelper";
import {CloudIdSelectMultiple} from "../../shared/shared-components/EducaSelects";
import moment from "moment";
import EducaHelper, {APP_NAMES} from "../helpers/EducaHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";
import {ROCKET_CHAT_SET_USERS_STATUS, ROCKET_CHAT_UPDATE_USERS_STATUS} from "../reducers/GeneralReducer";
import _ from "lodash";
import {ContextMenu, ContextMenuTrigger} from "react-contextmenu";
import {getDisplayPair} from "../../shared/shared-components/Inputs";
import xAPIProvider, {XAPI_VERBS} from "../xapi/xAPIProvider";
import {SupportTicketList} from "./helpdesk/SupportTicketList";
import {SupportTicketView} from "./helpdesk/SupportTicketView";


class EducaMessagesViewReact extends Component {

    constructor(props) {
        super(props);

        let params = new URLSearchParams(props.location.search);
        let roomId = params.get('room_id');
        this.state =
            {
                //General
                isNewMessageAddWidgetShown: false,
                cloudUsersSelect: [],
                currentSelectedUsers: [],
                currentSubscriptions: [],

                //Room objects
                availableRoomObjects: [],
                shownRoomObjects: [], // used for the search

                //Chatview
                chatViewRoomId: roomId,
                chatViewType: "",

                //Search
                searchString: "",

                // Viewport
                viewPort:
                    {
                        height: window.innerHeight,
                        width: window.innerWidth
                    },
                mobileViewIsMenuShown: true, // flag if the menu is shown in the mobile view

                newGroupName : "",
                ticket: null
            }
    }

    componentDidMount() {
        this._isMounted = true
        let params = new URLSearchParams(this.props.location.search);
        let roomId = params.get('room_id');
        let chatType = params.get('chat_type');
        let messageTo = params.get('message_to');
        this.setState({chatViewRoomId: roomId})
        this.setState({chatViewType: chatType})
        chatType ? this.setState({currentTab: chatType}) : this.setState({currentTab: "im"})
        //Direct message
        if (messageTo)
            this.setState({chatViewType: "im", currentSelectedUsers: [{id: messageTo}]}, () => {
                if (this.props.store?.allCloudUsers?.find(u => u.id == messageTo))
                    this.newMessageToUserClick()
                else
                    EducaHelper.fireErrorToast("Fehler", "Nutzer konnte nicht gefunden werden.")
            })
        this.prepareMessaging()


        RCHelper.getRcEventManager()?.subscribeToNotifyUserSubscriptionsChanged("messageViewSubscriptionNotify", (data) => {
            this.onSubSubscriptionsChangedHandler(data)
        })

        RCHelper.getRcEventManager()?.subscribeToNotifyLoggedUserStatus("messageViewSubscriptionNotifyUserStatusChange", (data) => {
            this.onUserStatusChangedHandler(data)
        })

        xAPIProvider.create(null,XAPI_VERBS.OPEN, {
            'id': APP_NAMES.MESSAGES,
            "objectType": "app",
            'definition': {
                'name': {'en-US': 'Chat'}
            }
        });
    }

    setRoomEventListeners(rooms)
    {
        rooms.forEach(room => {
            // register message listeners for the rooms
            RCHelper.getRcEventManager()?.subscribeToStreamRoomMessages("messagaes_view" + room._id, room._id, (data) => this.roomChangedListener(data))
        })
    }

    resetRoomEventListeners(rooms)
    {
        rooms.forEach(room => {
            RCHelper.getRcEventManager()?.unSubscribeToStreamRoomMessages("messagaes_view" + room._id)
        })
    }

    componentWillUnmount() {
        this._isMounted = false
        RCHelper.getRcEventManager()?.unSubscribeToNotyUserSubscriptionsChanged("messageViewSubscriptionNotify")
        this.resetRoomEventListeners(this.state.availableRoomObjects)
    }

    /**
     * Eventhandler for changed subscriptions. (e.g. user clicked on a room and marked all messages as read)
     */
    onSubSubscriptionsChangedHandler(data) {
        if (
            data.fields
            && data.fields.eventName
            && data.fields.eventName.includes("subscriptions-changed")
            && data.fields.args
            && data.fields.args.length > 0
            && data.fields.args[0].includes("updated")
        )
            this.handleNewSubscription(data.fields.args[1])
    }

    /**
     * Eventhandler for changed user status
     */
    onUserStatusChangedHandler(data) {
        if (data.msg === "changed" && data.collection === "stream-notify-logged" && data.fields && Array.isArray(data.fields.args)) {
            data.fields.args.forEach( user =>
            {
                if(user?.length  == 4) // correct form
                {
                    let newStatus = RCHelper.integerToStatusText(user[2])
                    let storeUser = Array.isArray(this.props.store.rocketChat.usersStatus)?this.props.store.rocketChat.usersStatus.find( u => u._id == user[0]) : null
                    this.props.updateUsersStatus([{...storeUser, status : newStatus}])
                }
            })
        }
    }

    handleNewSubscription(subscriptionObj) {
        if (this.state.currentSubscriptions && this.state.currentSubscriptions.length > 0) {
            for (let i = 0; i < this.state.currentSubscriptions.length; +i++) {
                //find the subscription object that exist by the room id and set only
                // if this room ID is not equal to it
                if (this.state.currentSubscriptions[i].rid === subscriptionObj.rid) {
                    if (subscriptionObj.rid === this.state.chatViewRoomId) {
                        subscriptionObj.unread = 0
                    }
                    let temp = this.state.currentSubscriptions
                    temp[i] = subscriptionObj
                    if (this._isMounted) this.setState({currentSubscriptions: temp})
                }
            }
        }
    }

    //Route changes // viewport
    componentDidUpdate(prevProps, prevState, snapshot) {

        //Listen for reducers viewPort Updates!
        if (this.props.store
            && this.props.store.viewPort
            && (this.props.store.viewPort.height != this.state.viewPort.height
                || this.props.store.viewPort.width != this.state.viewPort.width)) {
            if (this._isMounted) this.setState({viewPort: this.props.store.viewPort})
        }

        if (prevProps.location.search != this.props.location.search) {
            let params = new URLSearchParams(this.props.location.search);
            let roomId = params.get('room_id');
            let chatType = params.get('chat_type');
            if (this._isMounted) this.setState({chatViewRoomId: roomId, chatViewType: chatType})
        }
    }

    getCurrentImListCloudIdMapping() {
        AjaxHelper.getImListCloudIdMapping()
            .then(resp => {
                if (resp?.payload?.rooms) {
                    //Filter out educa group chats
                    let rooms = resp.payload.rooms?.filter( r =>  (r.name && !r.name.includes("_internal")) || !r.name )
                    if (this._isMounted) this.setState({
                        availableRoomObjects: rooms,
                        shownRoomObjects: rooms
                    })

                    this.setRoomEventListeners(resp.payload.rooms)
                    //Get initial users status
                    return RCHelper.getUsersPresence()
                } else
                    throw new Error(resp.message)
            })
            .then( resp =>
            {
                if(Array.isArray(resp?.users))
                {
                    this.props.setUsersStatus(resp.users)
                    return
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", err.message)
            })
    }

    roomChangedListener(data) {
        if (
            data.fields
            && data.fields.args
            && data.fields.args.length > 0)
            data.fields.args.forEach(obj => {
                let arr = this.state.availableRoomObjects
                for (let i = 0; i < arr.length; i++) {
                    if (arr[i]._id === obj.rid) {
                        arr[i].lastMessage = obj
                        arr[i].lastMessage._updatedAt = obj._updatedAt["$date"]
                        if (this._isMounted) this.setState({availableRoomObjects: arr})
                        return
                    }
                }
            })
    }

    prepareMessaging() {

        this.getSubscriptions()
            .then( () =>
            {
                return this.getCurrentImListCloudIdMapping()
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", err.message)
            })
    }

    getSubscriptions()
    {
        return RCHelper.getSubscriptions()
            .then(resp => {
                if (resp.update) {
                    if (this._isMounted) this.setState({currentSubscriptions: resp.update})
                } else
                    SharedHelper.logError("EducaMessagesViewReact: Error. Subscription response does not contain objects")
            })
    }

    setNewRoomId(roomId, type) {
        this.props.history.push({
            pathname: this.props.location.pathname,
            search: '?room_id=' + roomId + "&chat_type=" + type,
        })
    }

    /**
     * Creates a new IM room
     */
    newMessageToUserClick() {
        if (this.props.store.currentCloudUser.id && this.state.currentSelectedUsers?.length == 1 && this.state.currentSelectedUsers[0].id) {
            AjaxHelper.createImChat([this.props.store.currentCloudUser.id, this.state.currentSelectedUsers[0].id])
                .then(resp => {
                    if (!resp.payload.newRoom)
                        throw new Error("Server Error: " + resp.message)
                    let roomObj = resp.payload.newRoom
                    let rooms = resp.payload.rooms
                    if (!this.state.availableRoomObjects.find((room) => room._id === roomObj._id)) {
                        // The room does not exist
                        /*if (this._isMounted)*/ this.setState({
                            availableRoomObjects: rooms,
                            shownRoomObjects: rooms,
                            chatViewType: "im"
                        })

                    } else if (resp.payload?.newRoom?.cloudUsers.length === 1) {
                        EducaHelper.fireErrorToast("Fehler", "Dieser Chat konnte nicht erstellt werden.")
                        return
                    } else {
                        //EducaHelper.fireInfoToast("Information", "Dieser Chat existiert bereits.")
                    }
                    this.setNewRoomId(roomObj._id, "im")
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Error", err.message)
                })
                .finally(() =>
                {
                    this.setState({currentSelectedUsers : []})
                    this.getSubscriptions()
                })
        }

    }
    /**
     * Creates a new private group channel
     */
    newGroupChat()
    {
        if (this.props.store.currentCloudUser.id && this.state.currentSelectedUsers?.length > 1 ) {
            AjaxHelper.createGroupChat([this.props.store.currentCloudUser.id].concat( this.state.currentSelectedUsers.map( u => u.id)), this.state.newGroupName)
                .then(resp => {
                    if (!resp.payload.newRoom)
                        throw new Error("Server Error: " + resp.message)
                    let roomObj = resp.payload.newRoom
                    let rooms = resp.payload.rooms
                    if (!this.state.availableRoomObjects.find((room) => room._id === roomObj._id))
                    {
                        this.setRoomEventListeners([roomObj])
                        // The room does not exist
                          this.setState({
                            availableRoomObjects: rooms,
                            shownRoomObjects: rooms,
                            chatViewType: "group"
                        })

                    } else if (resp.payload?.newRoom?.cloudUsers.length === 1) {
                        EducaHelper.fireErrorToast("Fehler", "Dieser Chat konnte nicht erstellt werden.")
                        return
                    } else {
                        //EducaHelper.fireInfoToast("Information", "Dieser Chat existiert bereits.")
                    }
                    this.setNewRoomId(roomObj._id, "group")
                })
                .catch(err => {
                    EducaHelper.fireErrorToast("Error", err.message)
                })
                .finally(() =>
                {
                    this.setState({currentSelectedUsers : []})
                    this.getSubscriptions()
                })
    }
    }

    getCloudUsernamesWithoutCurrentUser(arr, asString) {
        let currentUserId = this.props.store.currentCloudUser.id
        let notCurrentUser = arr.find(usr => usr.cloudUserId !== currentUserId);

        if (arr.length === 2)
        {
            let usr = notCurrentUser?.username && Array.isArray( this.props.store.rocketChat.usersStatus)? this.props.store.rocketChat.usersStatus?.find( u => u.username == notCurrentUser.username) : null
            if(asString)
                return notCurrentUser? notCurrentUser.cloudName :"Unbekannter Nutzer"
            return notCurrentUser && !asString? <div style={{display : "flex"}}><div className={"mr-1"} style={{ display:"flex", flexDirection :"column", justifyContent :"center"}}> {EducaHelper.getStatusImage(usr? usr.status : "offline")}</div>{notCurrentUser.cloudName} </div> : "Unbekannter Nutzer"
        }
        else if (arr.length > 2)
            return notCurrentUser?.cloudName + " und " + (arr.length - 1) + " weitere"
        return ""
    }

    getAvatarUrlFromCloudUsernames(arr) {
        let otherUserInChat = arr.find((u) => u.cloudUserId !== this.props.store.currentCloudUser.id)
        if (otherUserInChat && otherUserInChat.cloudUserId !== -1)
            return AjaxHelper.getCloudUserAvatarUrl(otherUserInChat.cloudUserId, 35, otherUserInChat.image)
        // return default avatar here
    }

    onMessageListItemClickHandler(roomId, type) {

        if (this._isMounted) this.setState({mobileViewIsMenuShown: false})
        this.setNewRoomId(roomId, type)
    }

    getAmountOfMessagesForRoomId(roomId) {

        let obj = this.state.currentSubscriptions.find(obj => {
            return obj.rid === roomId
        })
        return obj ? obj.unread : 0
    }

    getOpenForRoomId(roomId) {

        let obj = this.state.currentSubscriptions.find(obj => {
            return obj.rid === roomId
        })
        return obj ? obj.open : false
    }
    /**
     * Lists all available chats w/ last messages in the List
     * @returns {JSX.Element|unknown[]}
     */
    getListMessages(type) {
        let todayShown = false
        let yesterdayShown = false
        let thisweekShown = false
        let olderShown = false

        let roomObjects = _.cloneDeep(this.state.shownRoomObjects)
        if (type === "group")
            roomObjects = roomObjects.filter(room => room.type === "group").filter(room => this.getOpenForRoomId(room._id))
        else
            roomObjects = roomObjects.filter(room => room.type === "im").filter(room => this.getOpenForRoomId(room._id))

        roomObjects = roomObjects.sort((a, b) => {
            let aM = a._updatedAt ? moment(a._updatedAt) : moment.unix(0) // take 1970 if there are no messages
            let bM = b._updatedAt ? moment(b._updatedAt) : moment.unix(0) // take 1970 if there are no messages
            return bM.unix() - aM.unix()
        })


        if (type === "group" && roomObjects.length === 0)
            return <ListGroup.Item>
                <div>{this.state.searchString ? "Nichts gefunden!" : "Noch keine Gruppenchats"}</div>
            </ListGroup.Item>
        else if (type === "im" && roomObjects.length === 0)
            return <ListGroup.Item>
                <div>{this.state.searchString ? "Nichts gefunden!" : "Noch keine Nachrichten"}</div>
            </ListGroup.Item>


        return roomObjects.map( (room,index) => {

            const date = room.lastMessage ? moment(room.lastMessage._updatedAt) : null
            const isToday = date && SharedHelper.isToday(date) && !todayShown
            const isYesterday = date && SharedHelper.isYesterday(date) && !yesterdayShown
            const isThisWeek = date && SharedHelper.isWithinAWeek(date) && !SharedHelper.isYesterday(date) && !SharedHelper.isToday(date) && !thisweekShown
            const older = date && !SharedHelper.isWithinAWeek(date) && !SharedHelper.isYesterday(date) && !SharedHelper.isToday(date) && !olderShown

            if(isToday)
                todayShown = true
            if(isYesterday)
                yesterdayShown = true
            if(isThisWeek)
                thisweekShown = true
            if(older)
                olderShown = true

            return <div key={"room_"+index}>
                {room.type === "group" ?
                    //Group
                    <ListGroupItem
                        active={this.state.chatViewRoomId === room._id}
                        onClick={
                            () => this.onMessageListItemClickHandler(room._id, room.type)}>
                        {getEducaChatMessageListGroupContent(
                        room.name,
                        room.lastMessage && room.lastMessage.msg ? room.lastMessage.msg?.replace(/\[ \]\(.*\)/gm, "")  :  <div style={{fontStyle: "italic"}}>Du bist diesem Chat beigetreten</div>,
                        /*this.getCloudUsernamesWithoutCurrentUser(room.cloudUsers),*/
                        date,
                        room?.educaGroup?
                        AjaxHelper.getGroupAvatarUrl(room?.educaGroup?.id, 35, room?.educaGroup?.image) : "",
                        this.getAmountOfMessagesForRoomId(room._id),
                        true)}
                    </ListGroupItem>
                    :
                    //Im
                    <div>
                        {isToday || isYesterday || isThisWeek || older? <div
                            style={{textAlign :"center", background :"rgba(0, 0, 0, 0.125)"}}
                            className={"text-muted"}>
                            { isToday? "Heute" : isYesterday? "Gestern" : isThisWeek? "Diese Woche" : "Älter" }
                        </div>
                            : null}
                        <ListGroupItem
                            active={this.state.chatViewRoomId === room._id}
                            onClick={
                                () => this.onMessageListItemClickHandler(room._id, room.type)}>
                        {getEducaChatMessageListGroupContent(
                            this.getCloudUsernamesWithoutCurrentUser(room.cloudUsers),
                            room.lastMessage && room.lastMessage.msg ? room.lastMessage.msg?.replace(/\[ \]\(.*\)/gm, "")  :
                                <div style={{fontStyle: "italic"}}>Du bist diesem Chat beigetreten</div>,
                            date ,
                            this.getAvatarUrlFromCloudUsernames(room.cloudUsers),

                            this.getAmountOfMessagesForRoomId(room._id)
                        )}</ListGroupItem>
                    </div>
                }
            </div>
        })

    }

    getListGroupItemsMessageAddCollapsed() {

        if(!FliesentischZentralrat.globalMessagesChatCreate())
            return null
        return <EducaCircularButton
            style={{marginLeft: "5px"}}
            variant={"success"}
            onClick={() => {
                if (this._isMounted) this.setState({isNewMessageAddWidgetShown: true, newGroupName : ""})
            }}
            size={"small"}>
            <i className="fas fa-plus"></i>
        </EducaCircularButton>
    }

    getListGroupItemsMessageAddShown() {

        if(!FliesentischZentralrat.globalMessagesChatCreate())
            return null

        return <div><ListGroup.Item
            style={{height: "55px"}}
            variant="danger"
            onClick={() => {
                this.setState({isNewMessageAddWidgetShown: false, newGroupName : ""})
            }}
            action
        >
            <div style={{display: "flex", flexDirection: "column"}}>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <div style={{
                        display: "flex",
                        flexDirection: "column",
                        justifyContent: "center",
                        marginRight: "5px"
                    }}>
                        <i className="fas fa-times"></i>
                    </div>
                    <div>Abbrechen</div>
                </div>
            </div>
        </ListGroup.Item>

            <ListGroup.Item>
                <Form.Text className="text-muted">
                   Mit welchen Personen soll ein Chat erstellt werden?
                </Form.Text>
                <CloudIdSelectMultiple
                    onlyWithRcAccount={true}
                    value={this.state.currentSelectedUsers}
                    placeholder={"Gib min. 3 Buchstaben ein, um Nutzer anzuzeigen..."}
                    cloudUserListChangedCallback={(users) => this.setState({currentSelectedUsers: users?users : []})}
                />
                {this.state.currentSelectedUsers?.length > 1? getDisplayPair("Gruppenname",
                    <FormControl
                        value={this.state.newGroupName}
                        placeholder={"Neue Gruppe..."}
                        onChange={(e) => this.setState( {newGroupName : e.target.value?.replace(" ","")})}
                    />) : null}
                <EducaPrimaryButtonWrapped
                    disabled={!this.state.currentSelectedUsers?.length}
                    onClick={() => {
                        if(this.state.currentSelectedUsers?.length == 1)
                            this.newMessageToUserClick();
                        if(this.state.currentSelectedUsers?.length > 1)
                            this.newGroupChat();
                        this.setState({isNewMessageAddWidgetShown: false, newGroupName : ""})
                    }}
                > {this.state.currentSelectedUsers?.length <= 1? "Nachricht schreiben" :"Gruppenchat erstellen" }</EducaPrimaryButtonWrapped>
            </ListGroup.Item>
        </div>
    }

    searchStringChanged(searchString) {
        if (!searchString)
            searchString = ""
        let newShownRoomObjects = []

        this.state.availableRoomObjects.forEach(room => {
            if (room.type === "group") /// group search
            {
                if (this.getCloudUsernamesWithoutCurrentUser(room.cloudUsers, true).toLowerCase().includes(searchString.toLowerCase()) // users
                    || room.educaRoomName?.toLowerCase().includes(searchString.toLowerCase()) // roomname
                    || room.educaSection?.name?.toLowerCase().includes(searchString.toLowerCase())) // sectionName
                    newShownRoomObjects.push(room)
            } // IM search
            else if (this.getCloudUsernamesWithoutCurrentUser(room.cloudUsers,true).toLowerCase().includes(searchString.toLowerCase()))
                newShownRoomObjects.push(room)
        })


        if (this._isMounted) this.setState({searchString: searchString, shownRoomObjects: newShownRoomObjects})
    }

    getSideMenu(fullWidth) {
        let rootStyle = {
            display: "flex",
            overflow: "auto",
            flexDirection: "column",
            width: "400px",
            marginRight: "5px",
        }
        return <div style={
            fullWidth ? {...rootStyle, width: "100%"} : rootStyle
        }>

            <div
                className="mt-2 card"
            >
                <Card.Body>
                    <h4>Nachrichten
                        {!this.state.isNewMessageAddWidgetShown ? this.getListGroupItemsMessageAddCollapsed() : null}</h4>
                    {this.state.isNewMessageAddWidgetShown ? this.getListGroupItemsMessageAddShown() : null}
                    <InputGroup>
                        <FormControl
                            className={"mt-1"}
                            placeholder="Chats suchen"
                            value={this.state.searchString}
                            onChange={(evt) => this.searchStringChanged(evt.target.value)}
                        />
                        {this.state.searchString ?
                            <InputGroup.Append> <EducaSecondaryButton
                                onClick={() => {
                                    this.setState(
                                        {
                                            shownRoomObjects: this.state.availableRoomObjects,
                                            searchString: ""
                                        }
                                    )
                                }}
                            >
                                <i className={"fa fa-times"}></i>
                            </EducaSecondaryButton> </InputGroup.Append> : null}
                    </InputGroup>

                </Card.Body>
                <Card.Header style={{padding: "0.50rem"}}>
                    <Tab.Container defaultActiveKey="im"
                                   activeKey={this.state.currentTab}
                                   onSelect={(k) => this.setState({currentTab: k})}>
                        <Nav variant="tabs" className="border-bottom-0">
                            <Nav.Item>
                                <Nav.Link eventKey="im"><i className="fas fa-user"></i> Personen</Nav.Link>
                            </Nav.Item>
                            <Nav.Item>
                                <Nav.Link eventKey="group"><i className="fas fa-users"></i> Gruppen</Nav.Link>
                            </Nav.Item>
                            <Nav.Item>
                                <Nav.Link eventKey="helpdesk"><i className="fas fa-life-ring"/> Support</Nav.Link>
                            </Nav.Item>
                        </Nav>
                        <Tab.Content>
                            <Tab.Pane eventKey="im"
                                      style={{height: "calc(100vh - 300px)", overflowY: "auto", overflowX: "hidden"}}>
                                <ListGroup variant={"flush"}>
                                    {this.getListMessages("im")}
                                </ListGroup>
                            </Tab.Pane>
                            <Tab.Pane eventKey="group"
                                      style={{height: "calc(100vh - 300px)", overflowY: "auto", overflowX: "hidden"}}>
                                <ListGroup variant={"flush"}>
                                    {this.getListMessages("group")}
                                </ListGroup>
                            </Tab.Pane>
                            <Tab.Pane eventKey="helpdesk"
                                      style={{height: "calc(100vh - 300px)", overflowY: "auto", overflowX: "hidden"}}>
                                <SupportTicketList callback={(ticket) => { this.setState({ticket: ticket, chatViewRoomId: null, chatViewType: null});}} />
                            </Tab.Pane>
                        </Tab.Content>
                    </Tab.Container>
                </Card.Header>
            </div>


        </div>
    }

    getChatWindow() {
        if (this.state.chatViewRoomId && this.state.chatViewType)
            return <div className="mt-2 col" style={{display: "flex"}}>
                <div
                    className="card"
                    style={{display: "flex", flex: 1, flexDirection: "column"}}
                >
                    <ChatView
                        canWriteMessage={FliesentischZentralrat.globalMessagesCreate()}
                        canDelete={ !(this.state.availableRoomObjects?.find( r => r._id == this.state.chatViewRoomId)?.educaGroup?.id > 0)}
                        heightOffset={300}
                        type={this.state.chatViewType}
                        roomId={this.state.chatViewRoomId}
                    />
                </div>
            </div>
        if(this.state.ticket)
            return <SupportTicketView ticket={this.state.ticket}/>
        return <div className="mt-2 col">
            <Card style={{textAlign: "center"}}>
                <img src="/images/loading.gif" width={"400px"} className="img-fluid"
                     style={{marginLeft: "auto", marginRight: "auto"}}/>
                <Card.Body>
                    <h4>Wähle eine Unterhaltung aus oder erstelle eine Neue, um zu starten.</h4></Card.Body>
            </Card>
        </div>
    }

    getMenuButton() {
        return <EducaCircularButton
            size={"big"}
            className={"btn-secondary"}
            style={this.state.mobileViewIsMenuShown ?
                {zIndex: 1001, position: "fixed", bottom: "50px", right: "10px", opacity: 0.8} :
                {zIndex: 1001, position: "fixed", top: "75px", right: "10px", opacity: 0.8}
            }
            onClick={() => {
                if (this._isMounted) this.setState({mobileViewIsMenuShown: !this.state.mobileViewIsMenuShown})
            }}
        >
            {this.state.mobileViewIsMenuShown ? <i className={"fa fa-comment-dots fa-lg"}></i> :
                <i className={"fa fa-list fa-lg"}></i>}
        </EducaCircularButton>
    }

    render() {
        //big view
        if (this.state.viewPort.width > 1000) {
            return <div style={{display: "flex", flex: 1, flexDirection: "row"}}>
                {this.getSideMenu()}
                {this.getChatWindow()}
            </div>
        } else // mobile view
        {
            return <div>
                {this.getMenuButton()}
                {this.state.mobileViewIsMenuShown ? this.getSideMenu(true) : this.getChatWindow()}
            </div>
        }
    }
}

const mapStateToProps = state => ({store: state})

const mapDispatchToProps = dispatch => {
    return {
        setUsersStatus : (arr) =>  dispatch({type:ROCKET_CHAT_SET_USERS_STATUS , payload: arr}),
        updateUsersStatus : (arr) =>  dispatch({type:ROCKET_CHAT_UPDATE_USERS_STATUS , payload: arr}),
    }
}
export default connect(mapStateToProps,mapDispatchToProps)(EducaMessagesViewReact);
