import React, {Component, useRef, useState} from "react";
import {Alert, Badge, Card, ListGroup} from "react-bootstrap";
import xAPIProvider from "../xapi/xAPIProvider";
import {JSONTree} from "react-json-tree";
import {connect} from "react-redux";
import {withEducaLocalizedStrings} from "../helpers/StringLocalizationHelper";

class EducaxAPIView extends Component {

    constructor(props) {
        super(props);
        this.state = {
            "size" : "small",
            "statements" : []
        }
        this.messagesEnd = null;
    }

    componentDidMount() {
        xAPIProvider.registerHandler("EducaDebugView",(statement) => {
            this.setState({ statements: [...this.state.statements, statement] },() => {
                this.messagesEnd?.scrollIntoView(true);
            })
        })
    }
    componentWillUnmount() {
        xAPIProvider.unregisterEventHandler("EducaDebugView");
    }

    download() {
        var a = window.document.createElement('a');
        a.href = window.URL.createObjectURL(new Blob([JSON.stringify(this.state.statements.map((statement) => {
            return {
                ...statement,
                actor: {
                    name: this.props.store.currentCloudUser?.name,
                    account: {
                        homepage : window.location.origin,
                        name: this.props.store.currentCloudUser?.email
                    }
                }
            }
        }))], {type: 'text/json'}));
        a.download = 'educa_xapi_statements.json';

        document.body.appendChild(a);
        a.click();

        document.body.removeChild(a);
    }
    render() {

        const theme = {
            scheme: 'grayscale',
            author: 'alexandre gavioli (https://github.com/alexx2/)',
            base00: '#181818',
            base01: '#282828',
            base02: '#383838',
            base03: '#585858',
            base04: '#b8b8b8',
            base05: '#d8d8d8',
            base06: '#e8e8e8',
            base07: '#f8f8f8',
            base08: '#ab4642',
            base09: '#dc9656',
            base0A: '#f7ca88',
            base0B: '#a1b56c',
            base0C: '#86c1b9',
            base0D: '#7cafc2',
            base0E: '#ba8baf',
            base0F: '#a16946'
        }

        return <div
            style={{
                display: "flex",
                flexDirection: "column",
                position: "fixed",
                right: "50px",
                bottom: "0px",
                zIndex: 100000,
                width: "500px"
            }}
            className={"container"}
        >
            <Card>
                <Card.Header
                             style={{backgroundColor: "#fff"}}>
                    <div style={{display: "flex", flexDirection: "row"}}>
                        <h5 onClick={() => this.setState({size: this.state.size === "big" ? "small" : "big"})}
                            className="card-title"><b><i className="fas fa-robot"></i> educa Debug { this.state.statements?.length > 0 && this.state.size === "small" ? <Badge variant="danger">{this.state.statements?.length}</Badge> : null }</b></h5>
                        <div style={{display: "flex", flexDirection: "row", flex: 1, justifyContent: "flex-end"}}>
                            {this.state.size === "big" ?
                                <><i
                                    onClick={() => this.download()}      className="fas fa-download mr-3"></i><i onClick={() => this.setState({size: "small"})} className="fas fa-window-minimize"></i></> :
                                <i onClick={() => this.setState({size: "big"})} className="far fa-window-maximize"></i>}
                        </div>
                    </div>
                </Card.Header>
                {this.state.size === "big" ?
                        <ListGroup style={{maxHeight: "600px", overflow:"auto"}}>
                            { this.state.statements?.map((statement, n) => {
                             return <ListGroup.Item key={n} style={{paddingLeft: "5px", paddingRight: "5px"}}>
                                 <label>xAPI-Statement </label>
                                 <JSONTree data={statement} theme={theme}  invertTheme={true} hideRoot={true} shouldExpandNodeInitially={(keyPath, data, level) => level > 1 ? false : true} />
                                    </ListGroup.Item>
                                })
                            }
                            <div style={{ float:"left", clear: "both" }}
                                   ref={(el) => { this.messagesEnd = el; }}>
                        </div>
                        </ListGroup>
                    : null}
            </Card>
        </div>

    }
}


const mapStateToProps = (state) => ({ store: state });


export default connect(
    mapStateToProps
)(withEducaLocalizedStrings(EducaxAPIView));
