import React from "react"
import {RCHelper} from "../rocket-chat-components/RocketChatHelper";
import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import moment from "moment";


function prepareDate(date) {

    if (!date)
        return ""

    //Today
    if (SharedHelper.isToday(date)) {
        if (moment().diff(date, "minute") < 1)
            return "gerade eben"
        return "Heute " + date.format('HH:mm')
    }
    return date.format("DD.MM.YYYY")

}

export function getEducaChatMessageListGroupContent(header, message, date, avatarURL, amountNewMessages, squareAvatar) {

    return <div
        style={{
            display: "flex",
            flex: 1,
            width: "100%",
            flexDirection: "column",
        }}
    >
        <div
            style={{
                display: "flex",
                flex: 1,
                width: "100%",
                flexDirection: "row"
            }}
        >
            <div
                style={{
                    display: "flex",
                    justifyContent: "center",
                    marginRight: "10px",
                    minWidth: " 40px",
                    flexDirection: "column"
                }}>
                <img src={avatarURL + ""} height="40px" alt={""} style={squareAvatar ? {} : {borderRadius: "50%"}}
                     onError={(e) => {
                         e.target.onerror = null;
                         e.target.src = RCHelper.getAvatarForUserOrGroup("u")
                     }}/>
            </div>
            <div
                style={{
                    display: "flex",
                    flex: 1,
                    flexDirection: "column"
                }}>

                <div
                    style={{
                        fontSize: "15px",
                        lineHeight: "1.5em",
                        marginRight: "5px",
                        height: "1.5em",
                        maxWidth: "275px",
                        textOverflow: "ellipsis",
                        whiteSpace: "nowrap",
                        overflow: "hidden",
                    }}>
                    {header}
                </div>

                <div
                    className={"grey"}
                    style={{
                        display: "flex",
                        flex: 1,
                        flexDirection: "row",
                        maxWidth: "300px"
                    }}>
                    <div
                        className={"grey"}
                        style={{
                            fontSize: "15px",
                            lineHeight: "1.5em",
                            marginRight: "5px",
                            height: "1.5em",
                            maxWidth: "275px",
                            textOverflow: "ellipsis",
                            whiteSpace: "nowrap",
                            overflow: "hidden",
                        }}>
                        {message}
                    </div>

                    <div
                        className={"grey"}
                        style={{
                            display: "flex",
                            flex: 1,
                            flexDirection: "row",
                            justifyContent: "flex-end"
                        }}>
                        <div
                            className={"grey"}
                            style={{
                                display: "flex",
                                justifySelf: "flex-end",
                                fontSize: "11px",
                                lineHeight: "1.5em",
                                marginRight: "5px",
                                height: "1.5em",
                                maxWidth: "275px",
                                textOverflow: "ellipsis",
                                whiteSpace: "nowrap",
                                overflow: "hidden",
                            }}>
                            {prepareDate(date)}
                        </div>
                    </div>
                </div>
            </div>
            <div
                style={{
                    display: "flex",
                    flexDirection: "column",
                    justifyContent: "center",
                }}>
                {amountNewMessages !== 0 ? <div
                    style={{
                        display: "table-cell",
                        verticalAlign: "middle",
                        width: "20px",
                        textAlign: "center",
                        height: "20px",
                        borderRadius: "50%",
                        background: "red",
                        color: "white",

                    }}

                >{amountNewMessages}</div> : null}
            </div>

        </div>
    </div>
}
