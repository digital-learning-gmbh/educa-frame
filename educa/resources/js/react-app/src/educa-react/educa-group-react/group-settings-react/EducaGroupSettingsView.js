import React, { useEffect, useState } from "react";
import SharedHelper from "../../../shared/shared-helpers/SharedHelper";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import EducaHelper from "../../helpers/EducaHelper";
import { EducaLoading } from "../../../shared-local/Loading";
import { sortGroupSections } from "../EducaGroupViewReact";
import { Card } from "react-bootstrap";
import moment from "moment";
import GroupTabs from "./GroupTabs";

export default function EducaGroupSettingsView(props) {
    const [group, setGroup] = useState(undefined);

    useEffect(() => {
        if (!props.group)
            return SharedHelper.logError(
                "EducaGroupSettingsView: props.group is null or undefined"
            );

        AjaxHelper.getGroupSettings(props.group.id)
            .then(resp => {
                if (resp.payload?.group)
                    setGroup(sortGroupSections(resp.payload.group));
                else throw new Error("");
            })
            .catch(err => {
                EducaHelper.fireErrorToast(
                    "Fehler",
                    "Die Einstellungen konnten nicht geladen werden." +
                        err.messages
                );
            });
    }, [props.group]);

    return (
        <div
            style={{ display: "flex", flexDirection: "column", flex: 1 }}
            className={"m-2"}
        >
            <h2 style={{ color: "rgb(108, 117, 125)", fontWeight: "700" }}>
                {props.group.name}
            </h2>
            <h6 style={{ color: "rgb(108, 117, 125)", fontWeight: "500" }}>
                Erstellt am{" "}
                {moment(props.group.created_at).format("DD.MM.YYYY HH:mm")}
            </h6>
            {group ? (
                <GroupTabs group={group} setGroup={props.setGroup} />
            ) : (
                <EducaLoading />
            )}
        </div>
    );
}

export function reassignOrder(array) {
    for (let i = 0; i < array.length; i++) array[i].order = i;
    return array;
}

export function CollapsePanelComponent(props) {
    return (
        <Card.Header style={{ cursor: "pointer" }}>
            <h5 onClick={props.onClick}>
                <b> {props.children}</b>
            </h5>
        </Card.Header>
    );
}
