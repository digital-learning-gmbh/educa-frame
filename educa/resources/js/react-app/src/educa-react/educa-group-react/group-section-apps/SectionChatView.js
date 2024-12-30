import React, {Component} from 'react';
import ChatView from "../../rocket-chat-components/educa-chat-react/chat-components/ChatView";
import {EducaLoading} from "../../../shared-local/Loading";
import FliesentischZentralrat from "../../FliesentischZentralrat";

class SectionChatView extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                roomId: null,
                educaRoomName: "",
            }
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
        if (!this.props.section || !this.props.section.section_group_apps)
            return
        this.props.section.section_group_apps.forEach(app => {
            if (app.group_app && app.group_app.type === "chat") {
                let params = JSON.parse(app.parameters)
                console.log(params)
                this.setState({
                    roomId: params.roomId,
                    educaRoomName: params.educaRoomName,
                })
            }
        })
    }


    render() {
        if (!this.state.roomId)
            return <EducaLoading/>
        return (
            <div className="card" style={{width: "100%", flex: "1", display: "flex", flexDirection: "column"}}>
                <ChatView
                    hideHeader={true}
                    canDelete={false}
                    type={"group"}
                    heightOffset={375}
                    roomId={this.state.roomId}
                    canWriteMessage={FliesentischZentralrat.sectionMessagesCreate(this.props.section)}/>
            </div>
        );
    }
}

export default SectionChatView;
