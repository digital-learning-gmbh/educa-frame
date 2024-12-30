import SharedHelper from "../../shared/shared-helpers/SharedHelper";
import EducaHelper from "../helpers/EducaHelper";


let BASE = "/api/v1/xAPI/"

const XAPI_CREATE                                                     = "create"
const XAPI_CREATE_MULTI                                               = "createMulti"

class EducaxAPIProvider {

    constructor() {
        this.eventHandler = []
    }

    _getAuthHeaderObj()
    {
        return {'Authorization' : "Bearer "+SharedHelper.getJwt()}
    }

    _get(endpoint, params = {}) {

        if( !SharedHelper.getJwt() )
            return Promise.resolve()

        return fetch(BASE + endpoint + SharedHelper.concatParams(params),
            {
                method: "GET",
                headers:
                    {
                        'Accept': '*/*',
                        'Content-Type': 'application/json',
                        ...this._getAuthHeaderObj()
                    },
            })
            .then(resp => {
                if (resp.status == 499) {
                    //  if (isUserLoggedIn())
                    //       SharedHelper.fireInfoToast("Automatischer Logout", "Bitte logge Dich erneut ein. ")
                    this.logout()
                }

                return resp
            })
            .then(resp => resp.json())
    }

    _post(endpoint, payload = {}) {
        if( !SharedHelper.getJwt() )
            return Promise.resolve()

        return fetch(BASE + endpoint,
            {
                headers:
                    {
                        'Accept': '*/*',
                        'Content-Type': 'application/json',
                        ...this._getAuthHeaderObj()
                    },
                method: "POST",
                body: JSON.stringify(payload)
            })
            .then(resp => {
                return resp
            })
            .then(resp => resp.json())
    }

    create(context, verb, object, result = null)
    {
        this._fireEvent({
            context : context,
            verb: verb,
            object: object,
            result: result,
            created_at: Date.now() / 1000,
        })

        return this._post(XAPI_CREATE,{
            context : context,
            verb: verb,
            object: object,
            result: result,
            created_at: Date.now() / 1000
        })
            .then(resp => {
                if (resp.status < 0 || resp.payload == null)
                    throw new Error("Server has errors")
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "xAPI Statement not possible.")
            });
    }

    _getHandlerList() {
        return this.eventHandler;
    }

    _fireEvent(statement) {
        let handlerList = this._getHandlerList()
        handlerList.forEach(handler => handler.callback(statement))
    }

    registerHandler(registrationId, callback) {
        let handlerList = this._getHandlerList()
        for (let i = 0; i < handlerList; i++)
            if (handlerList[i].registrationId === registrationId)
                return handlerList[i].callback = callback
        handlerList.push({registrationId: registrationId, callback: callback})
    }

    _unregisterHandler(registrationId) {
        let handlerList = this._getHandlerList()
        for (let i = 0; i < handlerList.length; i++)
            if (handlerList[i].registrationId === registrationId)
                handlerList.splice(i, 1)
    }

    unregisterEventHandler(registrationId) {
        this._unregisterHandler(registrationId)
    }
}

export const XAPI_VERBS =
    {
        //Permanent
        LOGIN: {
            'id': 'http://adlnet.gov/expapi/verbs/attempted',
            'display': {'en-US': 'attempted'}
        },
        REGISTER: {
            'id': 'http://adlnet.gov/expapi/verbs/attempted',
            'display': {'en-US': 'attempted'}
        },
        OPEN: {
            'id': 'http://activitystrea.ms/schema/1.0/open',
            'display': {'en-US': 'open'}
        }
    }


let xAPIProvider = new EducaxAPIProvider();

export default xAPIProvider
