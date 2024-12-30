import React, {useEffect, useState} from 'react';
import AjaxHelper from "../../../helpers/EducaAjaxHelper";
import SharedHelper from "../../../../shared/shared-helpers/SharedHelper";
import GridLayout, { Responsive, WidthProvider } from "react-grid-layout";
const ResponsiveGridLayout = WidthProvider(Responsive);

import "react-grid-layout/css/styles.css"
import "react-resizable/css/styles.css"
import {useSelector} from "react-redux";
import NewUsersWidget from "./widgets/NewUsersWidget";
import {UsersWidget} from "./widgets/UsersWidget";
import ObjectCountWidget from "./widgets/ObjectCountWidget";
import FeedInfoWidget from "./widgets/FeedInfoWidget";
import {Card} from "react-bootstrap";
import FliesentischZentralrat from "../../../FliesentischZentralrat";
import {LearnContentCountWidget} from "./widgets/LernContentCountWidget";
import ActivityWidget from "./widgets/ActivityWidget";
import {useEducaLocalizedStrings} from "../../../helpers/StringLocalizationHelper.js";
import SpaceWidget from "./widgets/SpaceWidget.js";

const WIDGETS = {
    NEW_USERS: "new_users",
    ACTIVE_USERS: "active_users",
    OBJECT_COUNTS : "object_counts",
    FEED_COUNTS : "feed_counts",
    LEARN_CONTENT_COUNTS: "learn_content_counts",
    ACTIVITY: "activity",
    SPACES: "spaces",
}

function SystemSettingsGeneral(props) {

    const [widgets, setWidgets] = useState([])
    const viewPort = useSelector(s => s.viewPort)
    const [translate] = useEducaLocalizedStrings()

    useEffect(() => {
        getChartInfo()
    },[])


    const getChartInfo = () =>
    {
        AjaxHelper.getSystemSettingsGeneralChartInfo()
            .then( resp => {
                if(resp.payload)
                    return setWidgets(resp.payload.widgets)
                throw new Error()
            })
            .catch(err => SharedHelper.fireErrorToast("Fehler", "Die Statistiken konnten nicht geladen werden."))
    }

    const getLayout = () => {
        // init layout
        if( ! widgets?.length )
            return []
        let layout = []
        widgets.forEach( (w, index) =>
        {
            const width = w.default_width??2
            const height = w.default_height??2
            let x = w.x
            let y = w.y
            if(!x)
                x = 0
            if(!y)
                y = 0
            layout.push( { i : w.id+""+index, x : x, y : y , w : width, h : height })
        })

        return layout
    }

    const getWidget = (id) => {
        if (id == WIDGETS.NEW_USERS)
            return <NewUsersWidget key={id}/>
        if (id == WIDGETS.ACTIVE_USERS)
            return <UsersWidget key={id}/>
        if (id == WIDGETS.OBJECT_COUNTS)
            return <ObjectCountWidget key={id}/>
        if (id == WIDGETS.FEED_COUNTS)
            return <FeedInfoWidget key={id}/>
        if (id == WIDGETS.LEARN_CONTENT_COUNTS)
            return <LearnContentCountWidget key={id}/>
        if (id == WIDGETS.ACTIVITY)
            return <ActivityWidget key={id}/>
        if (id == WIDGETS.SPACES)
            return <SpaceWidget key={id}/>
    }

    return (
        <div>
            { !FliesentischZentralrat.systemSettingsManageStats() ? <Card><Card.Body><h5><i
                className="fas fa-info-circle"></i>{translate("system_settings_general.no_rights","Du hast keine Rechte für den Zugriff auf die Statistiken.")}</h5></Card.Body></Card> : <>
            <h5>{translate("system_settings_general.general_statistics","Allgemeine Statistiken")}</h5>
                <GridLayout
                    onLayoutChange={(layout) => {
                        console.log(layout)
                    }}
                    className="layout"
                    isDraggable={false}
                    isResizable={false}
                    width={(viewPort?.width > 1800 ? 1800 : viewPort?.width) - 350}
                    cols={12}
                    rowHeight={50}
                    layout={getLayout()}>
                    {
                        widgets?.map((w, index) => {
                            return <div key={w.id + "" + index}>
                                {getWidget(w.id)}
                            </div>
                        })
                    }
                </GridLayout>
                </> }
        </div>
    );
}

export default SystemSettingsGeneral;
