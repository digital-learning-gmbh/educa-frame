import React, {Component} from 'react';
import {connect} from "react-redux";
import SideMenu from "../educa-components/SideMenu";
import {Card} from "react-bootstrap";
import {ClassbookMarkWidget} from "./widgets/ClassbookMarkWidget";
import {ClassbookAbsenteeism} from "./widgets/ClassbookAbsenteeism";
import {ClassbookReport} from "./widgets/ClassbookReport";
import {ClassbookExamList} from "./widgets/ClassbookExamList";

class EducaClassbookViewReact extends Component {


    constructor(props) {
        super(props);

        this.state =
            {
                isLoading: true,
                events: [],
                checkedStates: {},
                currentView: "marks"
            }
    }

    componentDidMount() {
        this.loadEvents();

    }

    componentWillUnmount() {

    }

    loadEvents() {

    }

    getAppsForSideMenu(apps) {
        return {
            heading: {textAndId: "Bereiche", component: null}, content: [{
                component: <div><i className="fas fa-star-half"/> Noten</div>,
                clickCallback: () => {
                    this.setState({ currentView: "marks"})
                }
            },
                {
                    component: <div><i className="far fa-calendar-check"></i> Prüfungstermine</div>,
                    clickCallback: () => {
                        this.setState({ currentView: "exam_dates"})
                    }
                },
                {
                component: <div><i className="fas fa-user-clock"></i> Fehlzeiten</div>,
                clickCallback: () => {
                    this.setState({ currentView: "absenteeism"})
                }
            },
                {
                    component: <div><i className="fas fa-file-signature"></i> Bescheinigungen</div>,
                    clickCallback: () => {
                        this.setState({ currentView: "reports"})
                    }
                },


            ]
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
        if(this.state.currentView === "marks")
        {
            return <ClassbookMarkWidget />
        }
        if(this.state.currentView === "absenteeism")
        {
            return <ClassbookAbsenteeism />
        }
        if(this.state.currentView === "reports")
        {
            return <ClassbookReport />
        }
        if(this.state.currentView === "exam_dates")
        {
            return <ClassbookExamList />
        }
        return <Card>
            <Card.Body className="text-center">
                <Card.Img variant="top" src="/images/rocket.gif" style={{height: "400px", width: "400px"}}/>
                <h4>Noch keine Inhalte ...</h4>
            </Card.Body>
        </Card>;
    }

    render() {
        return <div>
            <div className="d-flex justify-content-between">
                <div style={{width: "300px"}} className={"m-2"}>
                    {this.getSideMenu()}
                </div>
                <div className="col mt-2">
                    {this.getContent()}
                </div>
            </div>
        </div>
    }

}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(EducaClassbookViewReact);
