import {Breadcrumb, Button, Navbar} from "react-bootstrap";
import { EducaCircularButton } from "../../../shared/shared-components";
import React from "react";
import FliesentischZentralrat from "../../FliesentischZentralrat.js";
import {useHistory} from "react-router";
import {useEducaLocalizedStrings} from "../../helpers/StringLocalizationHelper.js";

export default function GroupBrowseNavigation(props) {
    let history = useHistory()
    const [translate] = useEducaLocalizedStrings()


    return ( <div>
            <div className={"mb-2 mt-4 d-flex justify-content-between"}>
                <div className={"d-flex"}>
                    <div className='d-flex'>
                        <Button variant="outline-secondary" className="m-1" onClick={() => history.push("/app")}>
                            <i className="fas fa-arrow-left"></i> {translate("group_view.back_home","Zur Startseite")}
                        </Button>
                    </div>
                    <div>
                        <Breadcrumb className="noPadding m-1">
                            <Breadcrumb.Item onClick={() => history.push("/app/home")}>Startseite</Breadcrumb.Item>
                            {props.group ? <Breadcrumb.Item
                                onClick={() => history.push("/app/groups/" + props.group?.id + "/feed")}>{props.group?.name}</Breadcrumb.Item> : null}
                            {props.path?.slice(-1)[0] == "settings" ?
                                <Breadcrumb.Item active={true}>Einstellungen</Breadcrumb.Item> : null }
                        </Breadcrumb>
                    </div>
                    <div>
                        {FliesentischZentralrat.groupEditGroup(props.group) && props.path?.slice(-1)[0] != "settings" ?
                            <Button onClick={() => history.push("/app/groups/" + props.group?.id + "/settings")}
                                    variant="outline-secondary" className="m-1">
                                <i className="fas fa-wrench"></i> Gruppe bearbeiten
                            </Button> : null}

                    </div>
                </div>
                <div className={"d-flex"}>
                    <Button variant="outline-secondary" className="m-1"
                            onClick={props.fullScreenHandle.enter}>
                        <i className="fas fa-expand mr-1"></i> Vollbildmodus
                    </Button>
                </div>
            </div>
        </div>
    );
}
