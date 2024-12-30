import React, {useEffect, useRef, useState} from 'react';
import {Button, Collapse} from "react-bootstrap";
import "./favorites-panel.css"
import _ from "lodash";
import ErrorsRamazotti, {ERROR_MODE} from "./ErrorsRamazotti";
import {EducaCircularButton} from "./Buttons";
import {EducaInputConfirm, getDisplayPair} from "./Inputs";
import SharedHelper from "../shared-helpers/SharedHelper";
import PropTypes from "prop-types";
import EducaModal, {MODAL_BUTTONS} from "./EducaModal";


class FavoritesPanel extends React.Component {
    constructor(props) {
        super(props);
        this.state = {hasError: true, lastErrorInfo: ""}
    }

    componentDidCatch(error, errorInfo) {
        this.setState({hasError: true, lastErrorInfo: errorInfo});
    }

    render() {
        if (!this.state.hasError)
            return <ErrorsRamazotti errormode={ERROR_MODE.EASY}
                                    info={this.state.lastErrorInfo}/>
        return <FavoritesPanelContent {...this.props}/>
    }

}

/**
 *
 *  Use local FilterComponent to avoid defining AjaxCalls
 *
 * @param identifier unique string per component
 * @param updateFavoritesAjax
 * @param loadFavoritesAjax
 * @param filterMapping object that is returned @ onFilterMappingChanged
 * @param onFilterMappingChanged on filter click callback
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
function FavoritesPanelContent({
                                   identifier,
                                   updateFavoritesAjax,
                                   loadFavoritesAjax,
                                   filterMapping,
                                   onFilterMappingChanged,
                                   ...props
                               }) {


    const [config, setConfig] = useState(null)
    const [favoriteObject, setFavoriteObject] = useState(null)
    const [favoritesEditMode, setFavoritesEditMode] = useState(false)
    const [newFilterEditMode, setNewFilterEditMode] = useState(false)
    const [newFilterFavorite, setNewFilterFavorite] = useState({})

    const educaModalRef = useRef()

    useEffect(() => {
        loadFavorites(identifier)
    }, [identifier])

    const loadFavorites = (k) => {
        loadFavoritesAjax(k).then(resp => {
            let obj = resp.payload?.filter?.length > 0? resp.payload?.filter[0] : null
            setFavoriteObject(obj)
            setConfig(obj?.config? JSON.parse(obj.config) : null)
            })
    }

    const updateFavorites = (favs) => updateFavoritesAjax(favoriteObject?.id, identifier, favs)
        .then(resp => {
        let obj = resp.payload?.filter?.length > 0? resp.payload?.filter[0] : null
        setFavoriteObject(obj)
        setConfig(obj?.config? JSON.parse(obj.config) : null)
        SharedHelper.fireSuccessToast("Erfolg", "Speichern der Favoriten erfolgreich.")
    })
        .catch( err =>
        {
            SharedHelper.fireErrorToast("Fehler", "Speichern der Favoriten nicht erfolgreich.")
        })



    const removeFavorite = (obj) => {

        const exec = () =>
        {
            let favs = _.cloneDeep(config)
            favs = favs.filter(e => e.id != obj.id)
            updateFavorites(favs)
        }

        educaModalRef?.current?.open( (btn) => btn == MODAL_BUTTONS.YES? exec () : null, "Favoriten Löschen", "Möchten Sie diesen Favoriten wirklich löschen?",[MODAL_BUTTONS.YES, MODAL_BUTTONS.NO] )
    }

    const filterMappingLength = typeof filterMapping == "object"? Object.keys(filterMapping).reduce( (prev,curr) => !!filterMapping[curr]? prev+1 : prev ,0) : Array.isArray(filterMapping)? filterMapping.length : null

    return (
        <>
            <div style={{display: "flex"}} className={"mt-2"}>
                <label>
                    <b><i className="fas fa-star"/> Meine Favoriten</b>
                </label>
                {config?.length > 0 ? <EducaCircularButton size={"small"}
                                                                          className={"ml-2"}
                                                                          variant={favoritesEditMode ? "danger" : "outline-secondary"}
                                                                          onClick={() => {
                                                                              setFavoritesEditMode(!favoritesEditMode);
                                                                              setNewFilterEditMode(false)
                                                                          }}>{favoritesEditMode ?
                    <i className={"fas fa-times"}/> : <i className={"fas fa-pencil-alt"}/>}
                </EducaCircularButton> : null}
            </div>
            <div className={"mt-1 mb-1"}>
                <Button disabled={!filterMappingLength} variant={newFilterEditMode? "danger": "success"}
                        onClick={() => {setNewFilterEditMode(!newFilterEditMode); setFavoritesEditMode(false); setNewFilterFavorite(null) }}>
                    {newFilterEditMode? <><i className="fas fa-times"/> Abbrechen</>: <><i className="fas fa-save"/> Favoriten Speichern {filterMappingLength>0? "("+filterMappingLength+")" : ""}</>}</Button>
            </div>
            <Collapse in={newFilterEditMode} unmountOnExit={true}>
                <div>
                    {getDisplayPair("Name", <div style={{width: "250px"}}>
                        <EducaInputConfirm
                            value={newFilterFavorite?.label ? newFilterFavorite?.label : ""}
                            onChange={(evt) => {
                                setNewFilterFavorite({
                                    id: SharedHelper.createUUID(),
                                    label: evt.target.value,
                                    mapping: filterMapping
                                })
                            }}
                            onConfirmClick={() => {
                                updateFavorites(config ? config.concat([newFilterFavorite]) : [newFilterFavorite]);
                                setNewFilterEditMode(false)
                            }}
                            maxLetters={25}/></div>)}
                </div>
            </Collapse>
            <div>
                {config?.length > 0 ? <>
                    <div>
                        {favoritesEditMode ?
                            <div style={{color: "#597EAA"}}><i className={"fa fa-info-circle"}/>
                                Clicke auf einen Favoriten um ihn zu entfernen.
                            </div> : null}
                        <div style={{display: "flex"}} className={favoritesEditMode ? "db-shaking" : ""}>
                            {config.map((m, i) =>
                                <Button key={i}
                                        variant={favoritesEditMode ? "outline-danger m-1" : "outline-dark m-1"}
                                        onClick={() => {
                                            if (favoritesEditMode)
                                                return removeFavorite(m)
                                            onFilterMappingChanged(m.mapping);
                                        }}>{m.label}
                                </Button>
                            )}
                        </div>

                    </div>
                </> : <i>Noch keine Favoriten. Erstelle einen Filter um ihn als Favorit zu speichern.</i>}
            </div>
            <EducaModal ref={educaModalRef}/>
        </>
    );
}
FavoritesPanel.propTypes = {
    identifier: PropTypes.string.isRequired,
    filterMapping: PropTypes.object.isRequired,

    updateFavoritesAjax: PropTypes.func.isRequired,
    loadFavoritesAjax: PropTypes.func.isRequired,
    onFilterMappingChanged: PropTypes.func.isRequired,
}

export default FavoritesPanel;
