import i18n from "i18next";
import { initReactI18next } from 'react-i18next'

import defaultConf from "./i18n-configs/customizations/default.json"
import customConf from "./i18n-configs/customizations/custom.json"

const resources = {
    default: { translation: defaultConf },
    custom : { translation: customConf }
}

const educaI18n = i18n.createInstance();

educaI18n
    .use(initReactI18next) // passes i18n down to react-i18next
    .init({
        resources,
        load: 'unspecific',
        lng: "custom",
        fallbackLng: 'default',
        //debug: true,
        nonExplicitSupportedLngs : true,
        detection : false,
        keySeparator: false, // we do not use keys in form messages.welcome
        interpolation: {
            escapeValue: false // react already safes from xss
        }
    })

export default educaI18n;
