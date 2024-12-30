
export const GENERAL_SET_CURRENT_CLOUD_USER = "GENERAL_SET_REDUCER_CURRENT_CLOUD_USER"
export const GENERAL_SET_TENANT = "GENERAL_SET_TENANT"
export const GENERAL_SET_SYSTEM_INFORMATION = "GENERAL_SET_SYSTEM_INFORMATION"
export const GENERAL_SET_ALL_CLOUD_USERS = "GENERAL_SET_ALL_CLOUD_USERS"
export const GENERAL_SET_GROUPS = "GENERAL_SET_GROUPS"
export const GENERAL_UPDATE_OR_ADD_GROUP = "GENERAL_UPDATE_OR_ADD_GROUP"
export const GENERAL_REMOVE_GROUP =  "GENERAL_REMOVE_GROUP"
export const GENERAL_SET_VIEWPORT = "GENERAL_SET_VIEWPORT"
export const GENERAL_SET_GROUPS_ALL_APPS = "GENERAL_SET_GROUPS_ALL_APPS"
export const GENERAL_SET_ROOMS = "GENERAL_SET_ROOMS"

export const GENERAL_SET_EXTERNAL_INTEGRATION_TEMPLATES = "GENERAL_SET_EXTERNAL_INTEGRATION_TEMPLATES"

export const ROCKET_CHAT_SET_ME = "ROCKET_CHAT_SET_ME"

export const ROCKET_CHAT_SET_USERS_STATUS = "ROCKET_CHAT_SET_USERS_STATUS"
export const ROCKET_CHAT_UPDATE_USERS_STATUS = "ROCKET_CHAT_UPDATE_USERS_STATUS"
export const REDUX_RESET = "REDUX_RESET"
export const SYSTEM_SETTINGS_SET_TENANTS = "SYSTEM_SETTINGS_SET_TENANTS"
export const SYSTEM_SETTINGS_SET_ROLES = "SYSTEM_SETTINGS_SET_ROLES"

const initialState = () => {
    return {
        tenant: null,
        currentCloudUser:
            {
                groups: [],
                apps: []
            },
        allCloudUsers: [],
        rooms : [],
        viewPort: {},
        groupInfo:
            {
                allApps: []
            },

        rocketChat:
            {
                me: {},
                usersStatus : []
            },
        tenants : [],
        roles : [],
        systemInformation: {},
        externalIntegrationTemplates : null
    }
}

export default function GeneralReducer(state = initialState(), action) {
    if (action.type === GENERAL_SET_TENANT)
        return {...state, tenant: action.payload}
    if (action.type === GENERAL_SET_CURRENT_CLOUD_USER)
        return {...state, currentCloudUser: action.payload}
    else if (action.type === GENERAL_SET_ALL_CLOUD_USERS)
        return {...state, allCloudUsers: action.payload}
    else if (action.type === GENERAL_SET_GROUPS)
        return {...state, currentCloudUser: {...state.currentCloudUser, groups: action.payload}}
    else if (action.type === GENERAL_UPDATE_OR_ADD_GROUP)
        return {
            ...state,
            currentCloudUser: {
                ...state.currentCloudUser,
                groups: addOrUpdateGroup(action.payload, state.currentCloudUser.groups, false)
            }
        }
    else if (action.type === GENERAL_REMOVE_GROUP)
        return {
            ...state,
            currentCloudUser: {
                ...state.currentCloudUser,
                groups: addOrUpdateGroup(action.payload, state.currentCloudUser.groups, true)
            }
        }
    else if (action.type === GENERAL_SET_VIEWPORT)
        return {...state, viewPort: action.payload}
    else if (action.type === GENERAL_SET_GROUPS_ALL_APPS)
        return {...state, groupInfo: {allApps: action.payload}}
    else if (action.type === ROCKET_CHAT_SET_ME)
        return {...state, rocketChat: {...state.rocketChat, me: action.payload}}
    else if (action.type === ROCKET_CHAT_SET_USERS_STATUS)
        return {...state, rocketChat: {...state.rocketChat, usersStatus:action.payload}}
    else if (action.type === ROCKET_CHAT_UPDATE_USERS_STATUS)
        return {...state, rocketChat: {...state.rocketChat, usersStatus: updateRocketChatUsersStatus(action.payload, state.rocketChat.usersStatus)}}
    else if (action.type === GENERAL_SET_ROOMS)
        return {...state, rooms: action.payload}
    else if (action.type === SYSTEM_SETTINGS_SET_TENANTS)
        return {...state, tenants: action.payload}
    else if (action.type === SYSTEM_SETTINGS_SET_ROLES)
        return {...state, roles: action.payload}
    else if (action.type === GENERAL_SET_SYSTEM_INFORMATION)
        return {...state, systemInformation: action.payload}
    else if (action.type === GENERAL_SET_EXTERNAL_INTEGRATION_TEMPLATES)
        return {...state, externalIntegrationTemplates: action.payload}
    else if (action.type === REDUX_RESET)
        return initialState()
    else
        return state;
}

function addOrUpdateGroup(group, currentGroups, shallDelete) {
    if (Array.isArray(currentGroups)) {
        if (currentGroups.length > 0) {
            for (let i = 0; i < currentGroups.length; i++)
                if (currentGroups[i].id === group.id) {
                    if( shallDelete )
                    {
                        currentGroups.splice(i,1)
                        return currentGroups
                    }
                    else
                    {
                        currentGroups[i] = group
                        return currentGroups
                    }
                }
        }
        //Otherwise push the group
        currentGroups.push(group)
    }
    return currentGroups
}

function updateRocketChatUsersStatus(val, current)
{
    if(Array.isArray(val) && Array.isArray(current))
    {
        let newArr = []
        current.forEach( (userObj,i) =>
        {
            let currObj = val.find( o =>  o._id && userObj._id == o._id )
            newArr.push(currObj? currObj : userObj)
        })
        return newArr
    }

    return current
}
