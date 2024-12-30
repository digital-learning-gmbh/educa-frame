import React, {Component} from 'react';
import {connect} from "react-redux";
import SideMenu from "../educa-components/SideMenu";
import {Card} from "react-bootstrap";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import EducaHelper, {APP_NAMES} from "../helpers/EducaHelper";
import xAPIProvider, {XAPI_VERBS} from "../xapi/xAPIProvider";

class EducaAnalyticsReact extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isLoading: true,
                contentProvider: [],
                currentProvider: null
            };

    }

    componentDidMount() {
        this._isMounted = true
        this.loadEvents();
        xAPIProvider.create(null,XAPI_VERBS.OPEN, {
            'id': APP_NAMES.LEARNMATERIALS,
            "objectType": "app",
            'definition': {
                'name': {'en-US': 'Lernmaterialien'}
            }
        });
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    loadEvents() {
        AjaxHelper.getLernContentProvider()
            .then(resp => {
                if (resp.status > 0 && resp.payload && resp.payload.contentProvider) {
                    if (this._isMounted) this.setState({contentProvider: resp.payload.contentProvider})
                    return;
                }
                throw new Error(resp.message)

            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Fehler in der Bibliothek. Servernachricht: " + err.message)
                if (this._isMounted) this.setState({contentProvider: []})
            })
    }

    getAppsForSideMenu(apps) {
        if(this.state.contentProvider == null || this.state.contentProvider.length === 0)
            return {
                heading: {textAndId: "Bibliothek", component: null},
                content: [{component: <div>Du hast noch keine Inhalte</div>}]
            }

        let content = []
        this.state.contentProvider.forEach(provider => {
            content.push(
                {
                    component: <div><img src={provider.icon} height="" width="30px" /> {provider.name}</div>,
                    clickCallback: () => {
                        if (this._isMounted) this.setState({currentProvider: provider});
                    }
                })
        })
        return {
            heading: {textAndId: "Bibliothek", component: null}, content: content
        }
    }

    getSideMenu() {
        return <SideMenu
            menus={
                [
                    this.getAppsForSideMenu(this.props.store.currentCloudUser.apps),
                ]
            }
        >
        </SideMenu>
    }

    getContent() {
            return  <Card>
                <Card.Body className="text-center">
                    <Card.Img variant="top" src="/images/rocket.gif" style={{height: "400px", width: "400px"}}/>
                    <h4>Noch keine Inhalte ...</h4>
                </Card.Body>
            </Card>

    }

    render() {
        return <div>
            <div className="d-flex justify-content-between">
                <div style={{width: "300px"}} className={"m-2"}>
                    <h2>hello</h2>
                </div>
            </div>
        </div>
    }

}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(EducaAnalyticsReact);
