import React, {lazy, useEffect, useState} from 'react';
import EventManager from "./EducaEventManager";
import SharedHelper from "../shared/shared-helpers/SharedHelper";
import AjaxHelper from "./helpers/EducaAjaxHelper";
import {useDispatch} from "react-redux";
import {GENERAL_SET_CURRENT_CLOUD_USER, REDUX_RESET} from "./reducers/GeneralReducer";
import {Switch, withRouter} from "react-router";
import {Route} from "react-router-dom";

const Search = lazy(() => import('./educa-search-frontend/EducaSearchViewReact'));
const Landing = lazy(() => import('./educa-landing-react/EducaLandingViewReact'));
const Proposal = lazy(() => import('./educa-landing-react/EducaEDBProposalView'));
const License = lazy(() => import('./educa-landing-react/EducaEDBProviderLicenseView'));
const Help = lazy(() => import('./educa-landing-react/EducaEDBHelpView'));

export const EDB_ROUTES =
    {
        SEARCH: "/app/edb/search",
        LANDING: "/app/edb/landing",
        LICENSE: "/app/edb/license",
        PROPOSAL: "/app/edb/proposal",
        HELP: "/app/edb/help",

    }

function EducaEdbWrapper(props) {

    let [loading, setLoading] = useState(false)
    const disptach = useDispatch()
    const setMe = (currentUser) => disptach({type: GENERAL_SET_CURRENT_CLOUD_USER, payload: currentUser})
    const resetRedux = () => disptach({type : REDUX_RESET})


    useEffect( () =>
    {
        EventManager.registerLogoutEventHandler("EducaLandingNavbar", () => logoutEventHandler())

        if(!SharedHelper.getJwt())
            return
        setLoading(true)
        AjaxHelper.getCurrentUser() // get the current clouduser
            .then(resp => {
                if (resp.payload && resp.payload.user) {
                    setMe(resp.payload.user)
                } else
                    throw new Error("Server Error")
                return AjaxHelper.getAllCloudUsers()
            })
            .catch( err =>
            {
                SharedHelper.fireErrorToast("Fehler", "Fehler beim Laden der Nutzerdaten.")
            })
            .finally(() =>
            {
                setLoading(false)
            })
    },[])


    const logoutEventHandler = () =>
    {
        resetRedux()
        SharedHelper.fireSuccessToast("Logout","Sie haben sich erfolgreich ausgeloggt.")
    }


    console.log(props.history)
    return (
<>
    <Switch>
        <Route
            path={EDB_ROUTES.SEARCH}
            render={(props) => <Search {...props}/>}/>
        <Route
            path={EDB_ROUTES.LANDING}
            render={(props) => <Landing {...props}/>}/>
        <Route
            path={EDB_ROUTES.PROPOSAL}
            render={(props) => <Proposal {...props}/>}/>
        <Route
            path={EDB_ROUTES.LICENSE}
            render={(props) => <License {...props}/>}/>
        <Route
            path={EDB_ROUTES.HELP}
            render={(props) => <Help {...props}/>}/>
        <Route component={Landing}/>

    </Switch>

</>
    );
}

export default withRouter(EducaEdbWrapper);
