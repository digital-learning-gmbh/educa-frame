import React, {Component, useEffect, useState} from 'react';
import {Button, Collapse, ListGroup} from "react-bootstrap";
import "./SideMenu.css"
import {SideMenuHeadingStyle} from "./EducaStyles";

const MAX_WIDTH = 300
const CLICK_TIMEOUT = 300

const CollapsibleClusterGroup = (props) =>
{
    let [open, setOpen] = useState(props.hasOwnProperty("collapsed")? !props.collapsed : true)

    useEffect(() =>
    {
        if(props.hasOwnProperty("collapsed") && open !== props.collapsed)
            setOpen(!props.collapsed)
    },[props.collapsed])

    return <>
        <div style={{display : "flex"}}>
            {props.heading}
            <div style={{display : "flex", flexDirection :"column", justifyContent :"center"}}>
                <div className={"ml-2"} style={{cursor : "pointer", color : ""}} onClick={() => setOpen(!open)}>
                    {open? <i className={"fas fa-minus"} title={"Zuklappen"}/> : <i className={"fas fa-plus"} title={"Aufklappen"}/>}
                </div>
            </div>
        </div>
        {open? props.content : ""}
    </>
}

class SmallSideMenu extends Component {

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
        let count = 0
        const createComp = (item, i, style = {} ) =>
        {
            count+=1
            let hasClickCallback = !!item.clickCallback
            return <ListGroup.Item
                active={item.isSelected}
                style={hasClickCallback ? {cursor: "pointer"} : {}}
                key={heading.textAndId + "_" + i+count}
                className={"bg-transparent sidemenu"}
                onClick={(evt) => hasClickCallback ? this.fireClickCallback(item.clickCallback, evt) : null}
            >
                <div style={style}>{item.component}</div>
            </ListGroup.Item>
        }

        let clusterGroupIds = []
        clusterComps = cluster.map( (clus,i) =>
        {
            if(clus?.groups?.length > 0)
            {
                clus.groups?.forEach(g => clusterGroupIds.push(g.id))

                return <ListGroup key={i} variant="flush" className={"m-2"}>
                    <CollapsibleClusterGroup collapsed={false}
                                             heading={<b>{clus.name}</b>}
                                             content={clus.groups.map( item => createComp(item,i, {marginLeft : "10px"}))}/>
                </ListGroup>
            }
        })
        let otherGroups = []

        if(content?.length > 0)
            otherGroups = content.filter( c => !clusterGroupIds.find( id => id == c.id))
        // console.log(clusterGroupIds, content)
        if(otherGroups?.length > 0)
            clusterComps.unshift(<ListGroup key={"misc"} variant="flush" className={"m-2"}>
                <CollapsibleClusterGroup
                    collapsed={false}
                    heading={<b>Weitere Gruppen</b>}
                    content={otherGroups.map( item => createComp(item,"misc", {marginLeft : "10px"}))}/>
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
        return <div style={{display: "flex", flexDirection: "column", marginBottom: "25px"}}
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
                            <div><i className="fas fa-chevron-up"/> Ausblenden </div> :
                            <div><i className="fas fa-chevron-down"/> {e.content.length - threshold} weitere einblenden
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

        let threshold = 800
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

        return <div style={{display: "flex", flexDirection: "column", marginBottom: "25px"}}
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
            <div style={{display: "flex", flexDirection: "column"}}>
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

export default SmallSideMenu;
