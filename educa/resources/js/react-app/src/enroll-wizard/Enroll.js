import React, { Component, lazy, Suspense } from "react";
import Welcome from "./steps/Welcome";
import history from "../../history";
import {EducaLoading} from "../shared-local/Loading";
import {Router, Switch} from "react-router";
import {Route} from "react-router-dom";
import {Toaster} from "react-hot-toast";

export const ENROLL_ROUTES = {
    ROOT: "/enroll",
};

export default class Enroll extends Component {
    render() {
        return (
            <div>
                <Toaster
                    position="top-center"
                    reverseOrder={false}
                />
                <Router history={history}>
                    <Suspense fallback={<EducaLoading />}>
                        <Switch>
                            <Route
                                path={ENROLL_ROUTES.ROOT}
                                render={props => <Welcome />}
                            />
                        </Switch>
                    </Suspense>
                </Router>
            </div>
        )
    }
}
