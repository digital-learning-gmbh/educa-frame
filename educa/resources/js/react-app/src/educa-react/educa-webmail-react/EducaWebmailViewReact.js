import React, {Component} from 'react';
import {connect} from "react-redux";
import SideMenu from "../educa-components/SideMenu";
import {Card} from "react-bootstrap";
import Iframe from "react-iframe";

class EducaWebmailViewReact extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isLoading: true,
                events: [],
                checkedStates: {}
            }
    }

    componentDidMount() {
        this.loadEvents();

    }

    componentWillUnmount() {

    }

    loadEvents() {

    }


    getContent() {

    }

    render() {
        let url = this.props.store.currentCloudUser.webmail_url;
        console.log(this.props.store.currentCloudUser)
        return <div>
            <div className="d-flex justify-content-between">
                <div className="col mt-2">
                    <Card>
                        <Card.Body>
                            <h5 className="card-title"><img src="/images/email.png" height="50px" width="50px" /> E-Mail Postfach <a href={url} target="_blank"><i
                                className="fas fa-external-link-alt"></i></a>
                            </h5>
                            <h6 className="card-subtitle mb-2 text-muted">Hier befindet sich das E-Mail Postfach. Es können E-Mails an alle Adressen verfasst und empfangen werden.</h6>
                        </Card.Body>
                        <Iframe url={url}
                                id="learnContent"
                                className="learnIframe"
                                display="initial"
                                position="relative"/></Card>
                </div>
            </div>
        </div>
    }

}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(EducaWebmailViewReact);
