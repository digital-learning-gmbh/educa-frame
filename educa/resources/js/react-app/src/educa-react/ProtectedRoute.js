import React, {Component} from 'react';
import SharedHelper from "../shared/shared-helpers/SharedHelper";
import {Redirect, Route} from "react-router-dom";
import {BASE_ROUTES} from "./App";
import {connect} from "react-redux";
import EventManager from "./EducaEventManager";
import {EducaLoading} from "../shared-local/Loading";
import {withRouter} from "react-router";

class ProtectedRoute extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isAuthenticated: null
            }
    }

    componentDidMount() {
        this.setCurrentAuthenticationState()
        EventManager.registerLoginEventHandler("ProtectedRoute", () => {
            console.log("welcome to educa!");
            this.setState({isAuthenticated: true})
        })
        EventManager.registerLogoutEventHandler("ProtectedRoute", () => {
            console.log("bye bye");
            this.forceRedirectToLogin()
        })
    }

    forceRedirectToLogin() {
        this.props.history.push({
            pathname: BASE_ROUTES.LOGIN,
        })
        //this.props.history.go(0)
    }

    componentDidUpdate() {
        this.setCurrentAuthenticationState()
    }

    componentWillUnmount() {
        EventManager.unregisterLogoutEventHandler("ProtectedRoute")
        EventManager.unregisterLoginEventHandler("ProtectedRoute")
    }

    isCookieExistent() {
        return !!SharedHelper.getJwt() ? true : false
    }

    setCurrentAuthenticationState() {
        //Fresh start; Consider currentUserObject here, since the cookie is not set fast enough
        if (this.state.isAuthenticated === null
            && this.props.store && this.props.store.currentCloudUser && this.props.store.currentCloudUser.id)
            this.setState({isAuthenticated: true})

        //Later on, only check whether the cookie is set
        else if (this.state.isAuthenticated !== this.isCookieExistent()) {
            if (!this.isCookieExistent())
                EventManager.fireLogoutEvent()

            this.setState({isAuthenticated: this.isCookieExistent()})
        }

    }

    render() {
        if (this.state.isAuthenticated === null)
            return <EducaLoading/>

        if (this.state.isAuthenticated === true) {
            return <Route {...this.props}/>
        }
   //     if(this.props?.location?.pathname === BASE_ROUTES.LOGIN)
   //         return <Redirect to={BASE_ROUTES.LOGIN}/>
        return <Redirect to={BASE_ROUTES.LOGIN}/>
        //if( this.state.isAuthenticated === false )
        //     return <Redirect to={BASE_ROUTES.LOGIN}/>
    }
}

const mapStateToProps = state => ({store: state})

export default withRouter(connect(mapStateToProps)(ProtectedRoute));

