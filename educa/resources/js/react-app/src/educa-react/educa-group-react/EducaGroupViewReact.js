import { useHistory, useLocation } from "react-router";
import { BASE_ROUTES } from "../App";
import SmallSideMenu from "../educa-components/SmallSideMenu";
import React, {useEffect, useState} from "react";
import EducaHelper from "../helpers/EducaHelper";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import { connect } from "react-redux";
import { withTranslation } from "react-i18next";
import { GENERAL_UPDATE_OR_ADD_GROUP } from "../reducers/GeneralReducer";
import GroupBrowse, { GROUP_VIEWS } from "./group-browse/GroupBrowse";

function EducaGroupViewReact(props) {
    const history = useHistory();
    const location = useLocation();

    const [path, setPath] = useState("");
    const [plainPath, setPlainPath] = useState("");

    useEffect(() => {
        if(plainPath != location.pathname) {
            setPlainPath(location.pathname)
            setPath(location.pathname
                .replace(BASE_ROUTES.ROOT_GROUPS, "")
                .split("/")
                .filter(element => element !== ""))
        }
    },[location])

    function navigate(newPath, replace = false) {
        if (newPath.length <= 0) return;

        if (replace)
            history.replace(BASE_ROUTES.ROOT_GROUPS + "/" + newPath.join("/"));
        else history.push(BASE_ROUTES.ROOT_GROUPS + "/" + newPath.join("/"));
    }

    function groupCreateClickCallback() {
        history.push(BASE_ROUTES.ROOT_GROUPS_CREATE)
    }

    return (
                <GroupBrowse
                    path={path}
                    store={props.store}
                    t={props.t}
                    navigate={navigate}
                    history={history}
                    reduxRefreshGroup={props.reduxRefreshGroup}
                />
    );
}

const mapStateToProps = state => ({ store: state });

const mapDispatchToProps = dispatch => {
    return {
        reduxRefreshGroup: group =>
            dispatch({ type: GENERAL_UPDATE_OR_ADD_GROUP, payload: group })
    };
};

export function sortGroupSections(group) {
    return {
        ...group,
        sections: sortSections(group.sections)
    };
}

export function sortSections(sections) {
    return sections.sort((a, b) => a.order - b.order);
}

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(withTranslation()(EducaGroupViewReact));
