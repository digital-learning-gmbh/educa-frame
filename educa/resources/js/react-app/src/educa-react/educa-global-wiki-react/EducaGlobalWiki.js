import React from 'react';
import EducaWiki from "../educa-group-react/group-section-apps/EducaWiki";
import FliesentischZentralrat from "../FliesentischZentralrat";
import {BASE_ROUTES} from "../App";

function EducaGlobalWiki(props) {

    return (
        <div className={"mt-2"}>
            <EducaWiki
                pathTrail={BASE_ROUTES.ROOT_WIKI.split("/").pop()}
                canCreatePage={FliesentischZentralrat.globalWikiEdit()}
                canEdit={FliesentischZentralrat.globalWikiEdit()}
                canOpen={FliesentischZentralrat.globalWikiOpen()}
                modelType={"global"}
                modelId={null}
                menuText={"Hilfe"}
            />

        </div>
    );
}

export default EducaGlobalWiki;
