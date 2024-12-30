import React, {Component, useEffect, useState} from 'react';
import {Button, Collapse, ListGroup, ListGroupItem, Spinner} from "react-bootstrap";
import "./SideMenu.css"
import {SideMenuHeadingStyle} from "./EducaStyles";
import {withEducaLocalizedStrings} from "../helpers/StringLocalizationHelper";
import AjaxHelper from '../helpers/EducaAjaxHelper';
import { useDispatch, useSelector } from 'react-redux';
import SharedHelper from '../../shared/shared-helpers/SharedHelper';
import { GENERAL_SET_CURRENT_CLOUD_USER } from '../reducers/GeneralReducer';
import {redux_store} from "../../../store"

const MAX_WIDTH = 300
const CLICK_TIMEOUT = 300

const CollapsibleClusterGroup = (props) =>
{
    let [open, setOpen] = useState(props.hasOwnProperty("collapsed")? !props.collapsed : true)
    const [isLoading, setIsLoading] = useState(false)

    const dispatch = useDispatch()
    const me = useSelector( s => s.currentCloudUser)
    const setMe = (meObj) => dispatch({type: GENERAL_SET_CURRENT_CLOUD_USER, payload: meObj})

    useEffect(() =>
    {
        if(props.hasOwnProperty("collapsed") && open !== props.collapsed)
            setOpen(!props.collapsed)
    },[props.collapsed])

    const onOpenClicked = () => {
        
        setIsLoading(true)
        AjaxHelper.setGroupClusterCollapsedFlag(props.id, open)
        .then( resp => {
            if(resp.status > 0)
            {
                setMe({...me, group_cluster : resp.payload.groupClusters})
                setOpen( !resp.payload.collapsed )
                return
            }
            throw new Error()
        })
        .catch( err => SharedHelper.fireErrorToast("Fehler", "Die Einstellung konnte nicht gespeichert werden."))
        .finally(() => setIsLoading(false))


    }
    return <>
        <div style={{display : "flex"}}>
            {props.heading}
            <div style={{ display: "flex", flexDirection: "column", justifyContent: "center" }}>
                {isLoading ? <Spinner className="ml-2" size="sm" animation={"grow"} /> : <div className={"ml-2"} style={{ cursor: "pointer", color: "" }} onClick={() => onOpenClicked()}>
                    {open ? <i className={"fas fa-minus"} title={"Zuklappen"} /> : <i className={"fas fa-plus"} title={"Aufklappen"} />}
                </div>}
            </div>
        </div>
        {open? props.children : ""}
    </>
}

const ClusterGroupListItem = ({item, isFavorite}) => {

    const [hovered, setHovered] = useState(false)
    const [isLoading, setIsLoading] = useState(false)

    const dispatch = useDispatch()
    const me = useSelector( s => s.currentCloudUser)
    const setMe = (meObj) => dispatch({type: GENERAL_SET_CURRENT_CLOUD_USER, payload: meObj})

    const onMouseEnter = (e) => {
        if(!hovered)
            setHovered(true)
    }

    const onMouseLeave = (e) => {
        if(hovered)
            setHovered(false)
    }

    const onFavoritesClicked = () => {

        setIsLoading(true)
        AjaxHelper.flipGroupFavorite(item.id)
        .then( resp => {
            if(resp.status > 0)
            {
                setMe({...me, group_cluster : resp.payload.groupClusters})
                SharedHelper.fireSuccessToast("Erfolg", "Favorit gespeichert.")
                return
            }
            throw new Error()
        })
        .catch( () => SharedHelper.fireErrorToast("Fehler", "Favorit konnte nicht gespeichert werden."))
        .finally( () => setIsLoading(false))

    }

    return <ListGroupItem 
    active={item.isSelected}
    style={ typeof item?.clickCallback == "function"? {cursor: "pointer"} : {}}
    onClick={item?.clickCallback}
    onMouseEnter={onMouseEnter}
    onMouseLeave={onMouseLeave}
    className={"bg-transparent sidemenu"}>
        <div className='d-flex'>
            <div className='d-flex flex-grow-1'>
                {item?.component}
            </div>
            {hovered?<div>
                <div className='d-flex flex-grow-1 justify-content-end align-items-center'>
                    {isLoading? <Spinner animation={"grow"} size="sm"/> : <i onClick={(e) => { e.preventDefault(); e.stopPropagation(); onFavoritesClicked(); }} style={{color : "grey"}} className={isFavorite? 'fas fa-star' : 'far fa-star' } />}
                </div>
            </div> : null}
        </div>

    </ListGroupItem>

}

class SideMenu extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isCollapsedStates: {},
                canClickItem: true, // timeout if a user clicked an item
                openCluster: true,
            }
    }

    componentDidMount() {
        this._isMounted = true
        this.setState({canClickItem: true})
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    onCollapsedButtonClicked(id, state) {
        let obj = this.state.isCollapsedStates
        obj[id] = state
        if (this._isMounted) this.setState({isCollapsedStates: obj})
    }

    fireClickCallback(callback, evt) {
        if (this.state.canClickItem) {
            if (this._isMounted) this.setState({canClickItem: false}, () => {
                callback(evt)
                setTimeout(() => {
                    if (this._isMounted) this.setState({canClickItem: true})
                }, CLICK_TIMEOUT)
            })
        }

    }

    createMenuCluster(heading, cluster, content, e)
    {
        if(!cluster)
            return
        let clusterComps

        let clusterGroupIds = []
        let favoritesCluster = null
        clusterComps = cluster.map( (clus,i) =>
        {
            if(clus?.name == "Favoriten")
                favoritesCluster = clus
            if(clus?.groups?.length > 0 || clus?.always_visible)
            {
                clus.groups?.forEach(g => clusterGroupIds.push(g.id))

                return <ListGroup key={i} variant="flush" className={"m-2"}>
                    <CollapsibleClusterGroup collapsed={!!clus?.collapsed}
                        id={clus.id}
                        groups={clus?.groups}
                        heading={<b>{clus.name}</b>}>
                        {clus.groups.map( (item, i) => <ClusterGroupListItem key={i} item={item} isFavorite={favoritesCluster?.groups?.find( g => g?.id == item?.id)}/>)}
                    </CollapsibleClusterGroup>
                </ListGroup>
            }
        })
        let otherGroups = []

        //exception beacuse of legacy code, get currentCloudUser directly
        const currentCloudUser = redux_store.getState()?.currentCloudUser

        if(content?.length > 0)
            otherGroups = content.filter( c => !clusterGroupIds.find( id => id == c.id))
        // console.log(clusterGroupIds, content)
        if(otherGroups?.length > 0)
            clusterComps.push(<ListGroup key={"misc"} variant="flush" className={"m-2"}>
                <CollapsibleClusterGroup
                    collapsed={currentCloudUser?.other_groups_collapsed}
                    id={"other_groups"}
                    heading={<b>{this.props.translate("home.more_groups","Weitere Gruppen")}</b>}>
                        {otherGroups.map( (item,i) => <ClusterGroupListItem key={i} item={item}  isFavorite={favoritesCluster?.groups?.find( g => g?.id == item?.id)}/>
                        )}
                    </CollapsibleClusterGroup>
            </ListGroup>)
        let headingText = <div style={SideMenuHeadingStyle}>{heading.icon} {heading.textAndId}</div>


        let threshold = 8
        let collapsible = false;
        let listItemsAfterThreshold = []
        let listItemsCollapsed = []

        if(e.content) {
            collapsible = e.content.length > threshold
            e.content.forEach((item, number) => {
                let hasClickCallback = !!item.clickCallback
                let comp = <ListGroup.Item
                    active={item.isSelected}
                    style={hasClickCallback ? {cursor: "pointer"} : {}}
                    key={heading.textAndId + "_" + number}
                    className={"bg-transparent sidemenu"}
                    onClick={(evt) => hasClickCallback ? this.fireClickCallback(item.clickCallback, evt) : null}
                >
                    {item.component}
                </ListGroup.Item>

                if (number >= threshold)
                    listItemsAfterThreshold.push(comp)
                else
                    listItemsCollapsed.push(comp)
            })
        }
        return <div style={{display: "flex", flexDirection: "column", marginBottom: this.props.hideMarign ? "0px": "25px"}}
                    key={heading.textAndId + "_li"}>
            {heading.component ? heading.component : headingText}
            {/*e.cluster && e.content?
            <div className={"mt-2"}>
                <ButtonGroup>
                    <Button onClick={() => this.setState({ openCluster: true })} size="sm" variant="light" active={ this.state.openCluster }><i className="fas fa-th"></i></Button>
                    <Button onClick={() => this.setState({ openCluster: false })}  size="sm" variant="light" active={ !this.state.openCluster }><i className="fas fa-list"></i></Button>
                </ButtonGroup></div> : <></> */}
            {clusterComps && this.state.openCluster? clusterComps : null}
            {
                !this.state.openCluster && e.content? <>
                    <ListGroup variant="flush" className={"m-2"}>
                        {listItemsCollapsed.map(i => i)}
                    </ListGroup>
                    {collapsible ? // If the menu has too many entries, show the collapsible button
                        <Button className="btn-light" style={{width: "100%", backgroundColor: "#e5e6eb", marginTop: "-5px"}}
                                onClick={() => this.onCollapsedButtonClicked(heading.textAndId, !this.state.isCollapsedStates[heading.textAndId])}>{this.state.isCollapsedStates[heading.textAndId] ?
                            <div><i className="fas fa-chevron-up"/> {this.props.translate("hide","Ausblenden")} </div> :
                            <div><i className="fas fa-chevron-down"/> {e.content.length - threshold} {this.props.translate("show_more","weitere einblenden")}
                            </div>}</Button>
                        : null}
                    <Collapse in={this.state.isCollapsedStates[heading.textAndId]}>
                        <ListGroup variant="flush" className={"m-2"}>
                            {listItemsAfterThreshold.map(i => i)}
                        </ListGroup>
                    </Collapse></> : null
            }
        </div>

    }

    createMenu(heading, content, e) {

        let threshold = 8
        let collapsible = content.length > threshold

        let listItemsAfterThreshold = []
        let listItemsCollapsed = []
        content.forEach((item, number) => {
            let hasClickCallback = !!item.clickCallback
            let comp;
            if(item.justComponent != null)
            {
                comp = {...item.justComponent, key : number};
            } else {
                comp = <ListGroup.Item
                    active={item.isSelected}
                    style={hasClickCallback ? {cursor: "pointer"} : {}}
                    key={heading.textAndId + "_" + number}
                    className={"bg-transparent sidemenu"}
                    onClick={(evt) => hasClickCallback ? this.fireClickCallback(item.clickCallback, evt) : null}
                >
                    {item.component}
                </ListGroup.Item>
            }

            if (number >= threshold)
                listItemsAfterThreshold.push(comp)
            else
                listItemsCollapsed.push(comp)
        })

        let headingText = <div style={SideMenuHeadingStyle}>{heading.icon} {heading.textAndId}</div>

        return <div style={{display: "flex", flexDirection: "column", marginBottom: this.props.hideMarign ? "0px": "25px"}}
                    key={heading.textAndId + "_li"}>
            {heading.component ? heading.component : headingText}
            <ListGroup variant="flush" className={"m-2"}>
                {listItemsCollapsed.map(i => i)}
            </ListGroup>
            {collapsible ? // If the menu has too many entries, show the collapsible button
                <Button className="btn-light" style={{width: "100%", backgroundColor: "#e5e6eb", marginTop: "-5px"}}
                        onClick={() => this.onCollapsedButtonClicked(heading.textAndId, !this.state.isCollapsedStates[heading.textAndId])}>{this.state.isCollapsedStates[heading.textAndId] ?
                    <div><i className="fas fa-chevron-up"/> Ausblenden </div> :
                    <div><i className="fas fa-chevron-down"/> {content.length - threshold} weitere einblenden
                    </div>}</Button>
                : null}
            <Collapse in={this.state.isCollapsedStates[heading.textAndId]}>
                <ListGroup variant="flush" className={"m-2"}>
                    {listItemsAfterThreshold.map(i => i)}
                </ListGroup>
            </Collapse>
        </div>
    }


    render() {
        return (
            <div style={{display: "flex", flexDirection: "column", maxWidth: MAX_WIDTH}}>
                {this.props.menus ? this.props.menus.map( (e,i) => {
                    if(!e)
                        return null
                    return <div
                        key={i}>{!e.cluster ? this.createMenu(e.heading, e.content, e) : this.createMenuCluster(e.heading, e.cluster, e.content, e)} </div>
                }) : null}
            </div>
        );
    }
}

export default withEducaLocalizedStrings(SideMenu);
