import React, {useState} from "react";
import sanitizeHtml from "sanitize-html";
import moment from "moment";
import {HTML5Backend} from "react-dnd-html5-backend";
import toast from 'react-hot-toast';

const htmlSanitizerDefaultOptions = {
    allowedTags: [
        'a',
        'ul', 'li', //list
        'strong', 'i', 'blockquote', //text styles
        'table', 'tr', 'th', 'td', 'tbody', 'figure', // table
        'h1', 'h2','h3','h4', 'h5', // Headings
        'br','p',
        'iframe','oembed'
    ],
    allowedAttributes: {
        'a': [ 'href', 'target' ],
        'figure' : [ '*' ],
        'oembed' : [ '*' ],
        'iframe' : [ 'src','frameborder', 'allow', 'allowfullscreen','style' ],
        'p' : ["style"]
    }
};

export const MODELS =
    {
        GROUP : "group",
        SECTION : "section",
        TASK: "task",
        TASKTEMPLATE: "task_template",
        CALENDAR : "event",
        SUBMISSION : "submission",
        SUBMISSIONTEMPLATE: "submission_template",
        TASKSUBMISSIONTASKTEMPLATE: "task_template_submission_template",
        TEACHER : "teacher",
        STUDENT : "student",
        EMPLOYEE: "employee",
        CONTACT: "contact",
        COURSE : "schoolclass",
        MODULE : "module",
        LESSON : "lesson",
        LESSONPLAN : "lessonplan",
        LEARNCONTENT: "learnContent"
    }

export const AUTO_FORM_BUILDER_MODELS = {

    COURSE : "schoolclass",
    MODULE_PART_EXAM_DATES : "modul_part_exam_dates"

}

export const QUALIFICATION_MODELS =
    {
        TEACHER : "teacher",
        STUDENT : "student"
    }


export const CONTACT_TYPES =
    {
        COMPANY :"unternehmen",
        PERSON : "person"
    }

export const CONTACT_RELATION_SELECT_OPTIONS =  [
    {label : "Arbeitet bei", value : "arbeitet_bei"},
    {label : "Tochter von", value : "tochter"},
    {label : "Kooperiert mit", value : "kooperiert"},
    {label : "ist Teil von", value : "teil_von"},
    {label : "Sonstige", value : "other"},
]
export const CONTACT_RELATION_TYPES =
    {
        WORKS_AT :"arbeitet_bei",
    }

/**
 * CKEditor
 */

export const EducaCKEditorDefaultConfig = {

    toolbar: {
        items: [
            'heading',
            '|',
            'bold',
            'alignment',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            '|',
            'indent',
            'outdent',
            '|',
            'blockQuote',
            'insertTable',
            'undo',
            'redo'
        ]
    },
    mediaEmbed: {
        previewsInData : true
    },
    language: {
        // The UI will be English.
        ui: 'de',

        // But the content will be edited in Arabic.
        content: 'de'
    },
    link: { addTargetToExternalLinks: true },
    table: {
        contentToolbar: [
            'tableColumn',
            'tableRow',
            'mergeTableCells'
        ]
    },
    alignment: {
        options: [ 'left', 'right','center' ]
    },
}


const now = moment()
const yesterday = moment().subtract(1, 'days').startOf('day')
const weekAgo = moment().subtract(7, 'days').startOf('day');

class SharedHelperClass
{
    /**
     * Concats strings in a "?key1=value1&param2=value2 flavour
     * @param params
     */
    concatParams(params = {}) {
        let retString = ""
        let keys = Object.keys(params);
        for (let i = 0; i < keys.length; i++) {
            if (i === 0) // init
                retString = "?"
            else
                retString += "&"
            retString += encodeURIComponent(keys[i]) + "=" + encodeURIComponent(params[keys[i]])
        }
        return retString
    }

    bytesToSize(bytes) {
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Byte';
    let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

    /**
     * Takes a hex color and tells via magic if the color is too dark for a black font
     * @param c
     * @returns {boolean}
     */
    isColorTooDark(c) {
        if(!c)
            return false
        const hex = c.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        const bright = (( (g * 586) + (b * 115)) +(r * 298) )/ 1000;
        return bright < 120;
    }

   hexToRgbA(hex, alpha){
    let c;
    if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
        c= hex.substring(1).split('');
        if(c.length== 3){
            c= [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c= '0x'+c.join('');
        return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+alpha+')';
    }
    return ""
}

    //Watch out, strings with valid numbers return true
    isValidNumber(num)
    {
        if(num == 0 || num == "0" || num == 0.0)
            return true
        if(num == "" || num == undefined || num == null)
            return false
        if(typeof num === "number")
            return true
       return !isNaN(num)
    }

    /**
     * Components
     */
    sanitizeHtml(dirtyHtml, additionalOptions){
        if(!dirtyHtml)
            return  {__html: "<div></div>"}
    return {__html: sanitizeHtml(dirtyHtml, { ...htmlSanitizerDefaultOptions, ...additionalOptions }) }
}

    /**
     * DATE
     */

    getFormattedDateString(str, withTime = false, locale="de")
    {
        let formatString = "DD.MM.YYYY"
        if( locale !== "de")
            formatString = "YYYY.MM.DD"

        if(withTime)
            formatString+= " HH:mm"

        let d = moment(str)
        if(!d.isValid())
            return ""
        return moment(str).format(formatString)
    }

    /**
     * Toasts
     */

    fireErrorToast(title, content, delay=5000)
    {
        toast.error(<><b>{title}</b><div className={"ml-1"}>{content}</div></>,{duration: delay});
    }

    fireWarningToast(title, content, delay=5000)
    {
        toast.error(<><b>{title}</b><div className={"ml-1"}>{content}</div></>,{duration: delay});
    }

    fireInfoToast(title, content, delay=5000)
    {
        toast.error(<><b>{title}</b><div className={"ml-1"}>{content}</div></>,{duration: delay});
    }

    fireSuccessToast(title, content,delay=5000)
    {
        toast.success(<><b>{title}</b><div className={"ml-1"}>{content}</div></>,{duration: delay});
    }

    logError(text)
    {
        console.error(text)
    }


    logWarning(text)
    {
        console.warn(text)
    }


    /**
     * JWT
     */

    setJwt(token)
    {
        localStorage.removeItem("jwt");
        localStorage.setItem('jwt', token);

        // JWT Verfiication Here!!

        return true
    }


    getJwt()
    {
        //Admin -> User Session Overtake
        let usrAliasJwt = localStorage.getItem("jwt_user_alias")
        if(usrAliasJwt)
            return usrAliasJwt
           return localStorage.getItem('jwt')
    }


    setDisplayToken(token)
    {
        localStorage.removeItem("displayToken");
        localStorage.setItem('displayToken', token);
        return true
    }

    getDisplayToken()
    {
        return localStorage.getItem('displayToken')
    }

    resetUserAliasJwt()
    {
        localStorage.removeItem('jwt_user_alias')
        localStorage.removeItem('educa_rc_token_user_alias')
        localStorage.removeItem('educa_rc_uid_user_alias')
    }

    isUserAliasSession()
    {
       return !!localStorage.getItem('jwt_user_alias')
    }

    resetJwt()
    {
        localStorage.removeItem('jwt')
        localStorage.removeItem('disableSound')
        this.resetUserAliasJwt()
    }

    /**
     * Misc
     */

    createUUID()
    {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        let r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);})
    }

    getDnDHTML5Backend()
    {
        return HTML5Backend
    }

    /**
     *
     * @param history
     * @param path
     * @param searchParamsObj {key1: value1, key2 : value2} transformed to-> ?key1=value1&key2=value2
     */
    changeRoute(history, path, searchParamsObj = {} )
    {
        if(!history)
            return console.error("routing error. history is not defined.")

        history.push({
            pathname: path,
            search:  this.concatParams(searchParamsObj)
        })
    }

    isEmailValid(email)
    {
        return !/\S+@\S+\.\S+/.test(email);
    }

    /**
     * Cookies
     */
    setCookie(name, value, days = 365) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setDate(date.getDate() + days);
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/;";
    }

    getCookie(name) {
        let nameEQ = name + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    eraseCookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/'
    }

    /**
     * TIME / DATES
     */

    isToday(momentDate) {
        return momentDate.isSame(now, 'd');
    }

    isYesterday(momentDate) {
        return momentDate.isSame(yesterday, 'd');
    }

    isWithinAWeek(momentDate) {
        return momentDate.isAfter(weekAgo);
    }

    isTwoWeeksOrMore(momentDate) {
        return !this.isWithinAWeek(momentDate);
    }

    // FILES

   getFileType(file) {

    if(file.type.match('image.*'))
        return 'image';

    if(file.type.match('video.*'))
        return 'video';

    if(file.type.match('audio.*'))
        return 'audio';

    return 'other';
}

}

let SharedHelper = new SharedHelperClass();

export default SharedHelper
export const DEFAULT_GROUP_COLOR = "#3490DC"
