import {combineReducers, createStore} from 'redux'
import GeneralReducer from "./src/educa-react/reducers/GeneralReducer";

export const redux_store = createStore(GeneralReducer)
