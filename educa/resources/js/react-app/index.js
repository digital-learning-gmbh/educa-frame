import {Provider} from "react-redux"
import React, {lazy} from "react";
import {redux_store } from './store'
import TimeAgo from 'javascript-time-ago'
import de from 'javascript-time-ago/locale/de.json'
import "./i18n"
import App from "./src/educa-react/App";
import {createRoot} from 'react-dom/client';

import Enroll from "./src/enroll-wizard/Enroll";

import EducaTourApp, { TUTORIAL_STEPS } from "./src/educa-react/helpers/EducaTour";

TimeAgo.addDefaultLocale(de)


if (document.getElementById('react-root'))
{
    const root = createRoot(document.getElementById('react-root'))
    root.render(
        <Provider store={redux_store}>
            <EducaTourApp>
                <App/>
            </EducaTourApp>
        </Provider>, );
}

if (document.getElementById('react-enroll-root')) {
    const root = createRoot(document.getElementById('react-enroll-root'))
    root.render(
        <Provider store={redux_store}>
            <Enroll/>
        </Provider>);
}

