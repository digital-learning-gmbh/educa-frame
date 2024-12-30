import React, {Component} from 'react';
import {Col, Container, Row} from "react-bootstrap";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import ReactSwitch from "react-switch";
import {connect} from "react-redux";
import {EducaInputConfirm} from "../../../shared/shared-components/Inputs";
import EducaHelper from "../../helpers/EducaHelper";
import Button from "react-bootstrap/Button";
import SafeDeleteModal from "../../../shared/shared-components/SafeDeleteModal";
import {withRouter} from "react-router";
import Card from "react-bootstrap/Card";

export const GROUP_SETTINGS_TABS =
    {
        GENERAL_GROUP_SETTINGS: "generalGroupSettings",
        APP_SETTINGS: "appSettings"
    }

class SectionSettings extends Component {


    constructor(props) {
        super(props);

        this.state =
            {
                availableSectionApps: [],
                activatedSectionApps: [],
                section: props.section,


                isComponentLocked: false,
            }
        this.deleteModalRef = React.createRef()
    }

    componentDidMount() {
        this._isMounted = true
        this.setState({section: this.props.section, availableSectionApps: this.props.availableSectionApps}, () => this.getSectionSettingsInfos())
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        //Track group or section change
        if (this.props.section && prevProps.section.id !== this.props.section.id)
            this.componentDidMount()
    }


    getSectionSettingsInfos() {
        if (this._isMounted) this.setState({activatedSectionApps: this.state.section.section_group_apps})
    }


    updateSectionName() {

        AjaxHelper.updateSection(this.state.section.id, this.state.section.name)
            .then(resp => {
                if (resp.status > 0 && resp?.payload?.section) {
                    EducaHelper.fireSuccessToast("Erfolg", "Der Bereichsname wurde erfolgreich geändert")
                    //update in redux store
                    this.props.sectionChangedCallback(resp.payload["section"])
                    return;
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Der Bereichsname konnte nicht geändert werden. " + err.message)
            })
    }

    toggleApp(app) {
        if (this.state.isComponentLocked)
            return
        if (this._isMounted) this.setState({isComponentLocked: true}, () => {


            let sectionApp = this.state.activatedSectionApps.find(activatedApp => activatedApp.group_app.type === app.type)
            if (sectionApp) // if activated
            {
              let deactivate = () =>
              {
                  AjaxHelper.removeSectionGroupApp(this.state.section.id, sectionApp.id)
                      .then(resp => {
                          if (resp.status > 0 && resp.payload && resp.payload["section"]) {
                              //callback to parent
                              this.props.sectionChangedCallback(resp.payload["section"])
                              //remove from list
                              let arr = this.state.activatedSectionApps
                              arr.splice(this.state.activatedSectionApps.indexOf(sectionApp), 1);
                              if (this._isMounted) this.setState({activatedSectionApps: arr})
                              return
                          }
                          throw new Error("");
                      })
                      .catch(err => {
                          EducaHelper.fireErrorToast("Fehler", "Die App konnte nicht entfernt werden. " + err.message)
                      })
                      .finally(() => {
                          window.setTimeout(() => {
                              if (this._isMounted) this.setState({isComponentLocked: false})
                          }, 500);
                      })
              }
                const word = "DEAKTIVIEREN"
              this.deleteModalRef.current?.open( (flag) => flag? deactivate() : this.setState({isComponentLocked: false}),
                  "App deaktivieren",
                  "Wenn du diese App deaktivierst gehen sämtliche App-relevanten Daten verloren. Wenn du damit einverstanden bist schreibe '"+word+"' in das Textfeld.",
                  word)
            } else {
                AjaxHelper.addSectionGroupApp(this.state.section.id, app.type)
                    .then(resp => {
                        if (resp.status > 0 && resp.payload && resp.payload["section"]) {
                            //callback to parent
                            this.props.sectionChangedCallback(resp.payload["section"])
                            //Add to list
                            if (this._isMounted) this.setState({activatedSectionApps: resp.payload["section"].section_group_apps})
                            return
                        }
                        throw new Error("");
                    })
                    .catch(err => {
                        EducaHelper.fireErrorToast("Fehler", "Die App konnte nicht aktiviert werden. " + err.message)
                    })
                    .finally(() => {
                        window.setTimeout(() => {
                            if (this._isMounted) this.setState({isComponentLocked: false})
                        }, 500);
                    })
            }

        })
    }


    removeSection()
    {
        AjaxHelper.removeSection(this.props.section.id)
            .then( resp =>
            {
                if(resp.status > 0 )
                {
                    EducaHelper.fireSuccessToast("Erfolg", "Der Bereich wurde erfolgreich gelöscht.")
                    this.props.history.go(0)
                    return
                }
                throw new Error(resp.message)
            })
            .catch( err =>
            {
                EducaHelper.fireErrorToast("Fehler", "Fehler beim löschen des Bereichs. "+err.message)
            })
    }

    openDeleteModal()
    {
        this.deleteModalRef?.current?.open( (flag)=>
        {
            if(flag)
                this.removeSection()
        },
            "Bereich löschen",
            "Soll der Bereich '"+this.props.section.name+"' wirklich gelöscht werden? Geben Sie bitte zur Bestätigung 'LÖSCHEN' in das Textfeld ein.",
            "LÖSCHEN")
    }

    getAppGrid() {
        const COLS = 3
        if (!this.props.availableSectionApps || this.props.availableSectionApps.length === 0)
            return <div>Fehler</div>

        let rows = []
        for (let i = 0; i < this.props.availableSectionApps.length; i = i) {
            let rest = this.props.availableSectionApps.length - i;
            if (rest === 0)
                break

            if (rest >= COLS) {
                let cols = []
                for (let j = i; j < i + COLS; j++) {
                    cols.push(this.createColumnComponentForGrid(this.props.availableSectionApps[j]))
                }
                rows.push(
                    <Row key={"row_gs_" + i}>
                        {cols}
                    </Row>)
                i = i + COLS;
                continue
            } else if (rest < COLS) {
                let cols = []
                for (let j = this.props.availableSectionApps.length - rest; j < this.props.availableSectionApps.length; j++) {
                    cols.push(this.createColumnComponentForGrid(this.props.availableSectionApps[i]))
                }
                rows.push(
                    <Row key={"row_gs_" + i}>
                        {cols}
                    </Row>)
                break; // finished
            }
        }
        return rows
    }

    getAppToggleStatus(app) {

        if (this.state.activatedSectionApps?.find(activatedApp => activatedApp.group_app.type === app.type))
            return true
        return false
    }

    createColumnComponentForGrid(app) {

        return <Col xs={4} key={app.id}>
            <div style={{display: "flex", flexDirection: "column", margin: "20px"}}>
                <div style={{display: "flex", flexDirection: "column", justifyContent: "center"}}>
                    <div style={{display: "flex", flexDirection: "row", justifyContent: "center"}}>
                        { app.icon?.startsWith("fa") ?
                        <i className={app.icon} style={{fontSize: "25px"}}></i>
                            :
                            <img src={app.icon} style={{ height: "25px", width: "25px" }}/>
                        }
                    </div>
                    <div style={{display: "flex", flexDirection: "row", justifyContent: "center"}}>
                        <div>{app.name}</div>
                    </div>
                </div>
                <div style={{display: "flex", flexDirection: "row", justifyContent: "center"}}>
                    <ReactSwitch
                        disabled={this.state.isComponentLocked}
                        checked={this.getAppToggleStatus(app)}
                        onChange={() => {
                            this.toggleApp(app)
                        }}/>
                </div>
            </div>
        </Col>
    }

    render() {
        return <Card>
            <Card.Body>
                <div style={{display: "flex", flexDirection: "column"}} className={"m-2"}>
                <div style={{display: "flex", flexDirection: "row"}} >
                    <h6 className={"mr-2 pt-2"} style={{minWidth: "120px"}}>
                        Bereichsname
                    </h6>
                    <div
                        style={{maxWidth: "350px"}}
                    ><EducaInputConfirm
                        placeholder={"Name des Bereichs"}
                        maxLetters={25}
                        value={this.state.section?.name}
                        onChange={evt => this.setState({
                            section: {
                                ...this.state.section,
                                name: evt.target.value
                            }
                        })}
                        onConfirmClick={() => this.updateSectionName()}
                    />
                    </div>

                    <div style={{display: "flex", flex: 1, flexDirection: "row", justifyContent: "flex-end"}}>
                        <div>
                            <Button
                                onClick={() => this.openDeleteModal()}
                                variant={"danger"}
                            ><i className="fas fa-trash"></i> Bereich löschen
                            </Button>
                        </div>
                    </div>
                </div>
                <h6 className={"mr-2 pt-2"}>
                    Apps
                </h6>
                {this.getAppGrid()}
                    <p className={"text-muted text-center"}>Weitere Apps findest du im educa-Store.</p>
                </div>
            </Card.Body>
            <SafeDeleteModal ref={this.deleteModalRef}/>
        </Card>
    }
}

const mapStateToProps = state => ({store: state})

export default withRouter(connect(mapStateToProps)(SectionSettings));
