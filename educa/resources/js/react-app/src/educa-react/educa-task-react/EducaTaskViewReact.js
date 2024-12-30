import React, {Component} from 'react';
import {connect} from "react-redux";
import SideMenu from "../educa-components/SideMenu";
import Form from "react-bootstrap/Form";
import Button from "react-bootstrap/Button";
import {SideMenuHeadingStyle} from "../educa-components/EducaStyles";
import {EducaCardLinkButton, EducaCircularButton} from "../../shared/shared-components/Buttons";
import AjaxHelper from "../helpers/EducaAjaxHelper";
import TaskStatesColumnView from "./task-components/TaskStatesColumnView";
import TaskEditorModal from "./task-components/TaskEditorModal";
import TaskTemplateEditorModal from "./task-components/TaskTemplateEditorModal";
import TaskTemplateSelectorModal from "./task-components/TaskTemplateSelectorModal";
import EducaHelper from "../helpers/EducaHelper";
import FliesentischZentralrat from "../FliesentischZentralrat";
import {Alert} from "react-bootstrap";
import Dropdown from 'react-bootstrap/Dropdown';
import ButtonGroup from 'react-bootstrap/ButtonGroup';
import TaskArchiveView from "./task-components/TaskArchiveView";
import KompostFabrik, {WIDGET_TYPES} from "../../shared/shared-helpers/KompostFabrik";
import {getDisplayPair} from "../../shared/shared-components/Inputs";
import moment from "moment";
import {withEducaLocalizedStrings} from "../helpers/StringLocalizationHelper.js";

const CreateTemplateToggle = React.forwardRef( ({onClick}, ref) => {
    return <Button variant={"light"}
                   ref={ref}
                   style={{backgroundColor: "rgb(229, 230, 235)"}}
                   onClick={onClick}>{this.props.translate("task_view.templates","Vorlagen")} <i className="fas fa-chevron-down"></i> </Button>
})

export const TASK_STATES =
    {
        DRAFT: "draft",
        REVIEW: "review",
        COMPLETED: "completed"
    }

const options = {
    scales: {
        xAxes: [{
            display: false,
            gridLines: {}
        }],
        yAxes: [{
            display: false,
            gridLines: {}
        }]
    },
    maintainAspectRatio: false
}

class EducaTaskViewReact extends Component {

    constructor(props) {
        super(props);

        this.state =
            {
                isLoading: true,
                isPersonalCheckboxChecked: true,
                isMyTaskCheckboxChecked: true,
                isArchiveCheckboxChecked: false,
                isStatisticOpen: false,
                tasks: [],
                archivedTasks: [],
                taskTemplates : [],
                groupUnCheckedStates: {}, //careful its UNchecked,

                statistics: {},

                statsTasksDone: 0,
                statsTasksDue: 0,

                // buttons
                isShowMoreToggledOpen: false, // "open" tasks

                urlTaskId: null,

                needLoading: false,
                needSelection: false,
                viewForCloudId: null,
            }

        this.taskTemplateSelectorRef = React.createRef()
        this.taskColumnViewRef = React.createRef()
        this.taskArchiveViewRef = React.createRef()
    }

    componentDidMount() {
        this.loadTasks();
        this.loadArchivedTasks();
        this._isMounted = true
    }

    componentWillMount() {
        this.timeoutID = setTimeout(() => { // return the timeoutID
            this.setState({
                needLoading: true
            })

        }, 1000 * 60 * 5);
    }

    componentWillUnmount() {
        this._isMounted = false
        clearTimeout(this.timeoutID);
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        let params = new URLSearchParams(this.props.location.search);
        let taskId = params.get('task_id');
        if(!this.state.isLoading && this.state.needSelection)
        {
            this.decideViewSelectTask(taskId);
            this.setState({needSelection: false})
        }
        if (taskId && this.state.urlTaskId !== taskId) {
            if (this._isMounted) this.setState({urlTaskId: taskId})
            if(!this.state.isLoading)
            {
                this.decideViewSelectTask(taskId);
            }
            else {
                this.setState({needSelection: true});
            }
            //this.taskColumnViewRef.current?.selectTask(taskId)
        }
    }

    decideViewSelectTask(taskId)
    {
        let task = this.state.archivedTasks?.find(t => t.id == taskId)
        if(task)
        {
            this.setState({isArchiveCheckboxChecked: true}, ()=>{
                this.taskArchiveViewRef.current?.selectTask(taskId);
            });
        }
        else {
            this.setState({isArchiveCheckboxChecked: false}, () => {
                this.taskColumnViewRef.current?.selectTask(taskId);
            });
        }
    }

    setNewTaskId(taskId) {
        this.props.history.push({
            pathname: this.props.location.pathname,
            search: '?task_id=' + taskId,
        })
    }

    prepareEasyStatistics(tasks) {
        if (!Array.isArray(tasks))
            return

        let monday = moment().startOf('isoWeek');
        let sunday = moment().startOf('isoWeek').add(6, 'days');

        let done = 0
        let due = 0
        tasks.forEach(task => {
            if (task.state === TASK_STATES.COMPLETED)
                done++
            if (moment(task.end).isBefore(sunday) && moment(task.end).isAfter(monday))
                due++
        })
        if (this._isMounted) this.setState({statsTasksDue: due, statsTasksDone: done})
    }


    loadTasks() {
        if (this._isMounted) this.setState({isLoading: true})
        let groupIds = []
        this.props.store.currentCloudUser.groups.forEach(group => {
            if (!this.state.groupUnCheckedStates[group.id])
                groupIds.push(group.id)
        })


        AjaxHelper.getTasks(null, null, groupIds, this.state.isPersonalCheckboxChecked, null, this.state.viewForCloudId,this.state.isMyTaskCheckboxChecked)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.tasks) {
                    if (this._isMounted) this.setState({
                        tasks: resp.payload.tasks,
                        statistics: resp.payload?.statistics,
                        needLoading: false,
                        isLoading: false,
                    })
                    this.prepareEasyStatistics(resp.payload.tasks)
                    return;
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Konnte Aufgaben nicht laden. Servernachricht: " + err.message)
                if (this._isMounted) this.setState({tasks: []})
            })
            .finally(() => {
                if (this._isMounted) this.setState({isLoading: false})
            })
    }

    loadArchivedTasks() {
        if (this._isMounted) this.setState({isLoading: true})

        AjaxHelper.getArchivedTasks(null, this.state.viewForCloudId)
            .then(resp => {
                if (resp.status > 0 && resp.payload?.tasks) {
                    if (this._isMounted) this.setState({
                        archivedTasks: resp.payload.tasks,
                        needLoading: false,
                        isLoading: false,
                    })
                    return;
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Konnte Aufgabenarchiv nicht laden. Servernachricht: " + err.message)
                if (this._isMounted) this.setState({archivedTasks: []})
            })
            .finally(() => {
                if (this._isMounted) this.setState({isLoading: false})
            })
    }


    //Callback from 1000000th child...
    onTaskChanged(task, callback) {

        if (!this.state.tasks || !Array.isArray(this.state.tasks))
            return

        //find task and replace
        for (let i = 0; i < this.state.tasks.length; i++) {
            if (this.state.tasks[i].id === task.id) {
                if (task.deleteMe) // if deleteMe was set from child: delete task
                {
                    this.state.tasks.splice(i, 1)
                } else {
                    this.state.tasks[i] = task
                }

                if (this._isMounted) this.setState({tasks: this.state.tasks}, () => {
                    //console.log(this.state.tasks[i], task)
                    //Callback has to be fired, when the tasks here got updated, in oder to reload it in the child components
                    callback()
                    this.prepareEasyStatistics(this.state.tasks)
                })
                break
            }
        }

    }

    onArchivedTaskChanged(task, callback) {

        if (!this.state.archivedTasks || !Array.isArray(this.state.archivedTasks))
            return

        //find task and replace
        for (let i = 0; i < this.state.archivedTasks.length; i++) {
            if (this.state.archivedTasks[i].id === task.id) {
                if (task.deleteMe) // if deleteMe was set from child: delete task
                {
                    this.state.archivedTasks.splice(i, 1)
                } else {
                    this.state.archivedTasks[i] = task
                }

                if (this._isMounted) this.setState({archivedTasks: this.state.archivedTasks}, () => {
                    callback()
                })
                break
            }
        }

    }

    getAppsForSideMenu() {
        return {
            heading: {textAndId: this.props.translate("task_view.personal","Persönliches"), icon : <i className={"fas fa-filter"}/>, component: null}, content: [{
                component: <Form.Check
                    checked={this.state.isPersonalCheckboxChecked}
                    disabled={this.state.isLoading}
                    onChange={() => {
                        return null;
                    }}
                    type="checkbox"
                    label={this.props.translate("task_view.my_tasks","Meine Aufgaben")}/>,
                clickCallback: () => {
                    if (this._isMounted) this.setState({isPersonalCheckboxChecked: !this.state.isPersonalCheckboxChecked}, () => this.loadTasks());
                }
            },{
                component: <Form.Check
                    checked={this.state.isMyTaskCheckboxChecked}
                    disabled={this.state.isLoading}
                    onChange={() => {
                        return null;
                    }}
                    type="checkbox"
                    label={this.props.translate("task_view.commissioned_tasks","Beauftragte Aufgaben")}/>,
                clickCallback: () => {
                    if (this._isMounted) this.setState({isMyTaskCheckboxChecked: !this.state.isMyTaskCheckboxChecked}, () => this.loadTasks());
                }
            }]
        }
    }

    getTaskViewForSideMenu()
    {
        return {
            heading: {textAndId: this.props.translate("task_view.change_view","Aufgabenansicht wechseln"), component: null}, content: [
                {
                    justComponent: getDisplayPair(this.props.translate("task_view.participant_view","Ansicht für Teilnehmer*innen:"), KompostFabrik.parseComponent(
                        {type : WIDGET_TYPES.SELECT,
                            options : this.props.store?.currentCloudUser?.advisor_for.map(function (cloudId) { return {label: cloudId.name, value: cloudId.id}}),
                            value : this.state.viewForCloudId,
                            noOptionsText : this.props.translate("task_view.no_participants","Keine Teilnehmer*in zugeordnet")
                        },(value) => {if (this._isMounted) {
                            this.setState({viewForCloudId: value},() => {
                                this.loadTasks();
                                this.loadArchivedTasks();
                            });
                        }  })
                    )

                }]
        }
    }

    getArchiveToggleForSideMenu() {
        return {
            heading: {textAndId: this.props.translate("task_view.open_archive","Archiv öffnen"), component: null}, content: [
                {
                    justComponent: getDisplayPair("", KompostFabrik.parseComponent(
                    {type : WIDGET_TYPES.SWITCH,
                        labelOn : this.props.translate("yes","Ja"),
                        labelOff : this.props.translate("no","Nein"),
                        value : this.state.isArchiveCheckboxChecked,
                    },() => {if (this._isMounted) this.setState({isArchiveCheckboxChecked: !this.state.isArchiveCheckboxChecked});})
            )
            }]
        }
    }

    _areAllBoxesUnChecked() {
        let keys = Object.keys(this.state.groupUnCheckedStates);
        if (keys.length == 0)
            return false
        return keys.reduce((acc, key) => {
            return acc && this.state.groupUnCheckedStates[key]
        })
    }

    getGroupMenuObjectsForSideMenu(groups) {
        if (!groups || !Array.isArray(groups))
            return {
                heading: {textAndId: this.props.translate("groups","Gruppen"), icon : <i className={"fas fa-filter"}/>, component: null},
                content: [{component: <div>{this.props.translate("group.no_groups","Du hast noch keine Gruppen")}</div>}]
            }
        let content = []
        groups.forEach(group => {
            content.push(
                {
                    isSelected: !this.state.groupUnCheckedStates[group.id], //For list item active style
                    component: <Form.Check
                        checked={!this.state.groupUnCheckedStates[group.id]}
                        type="checkbox"
                        disabled={this.state.isLoading}
                        onChange={() => {
                            return null;
                        }}
                        label={group.name}/>,
                    clickCallback: () => {
                        let newState = this.state.groupUnCheckedStates;
                        newState[group.id] = !newState[group.id];
                        if (this._isMounted) this.setState({groupUnCheckedStates: newState}, () => {
                            this.loadTasks();
                        })

                    }
                })
        })

        let headingComponent = <div style={{display: "flex", flexDirection: "row"}}>
            <div style={{...SideMenuHeadingStyle, marginRight: "5px"}}><i className={"fas fa-filter"}/>{this.props.translate("groups","Gruppen")}</div>
            <EducaCardLinkButton
                onClick={() => {
                    if (this._areAllBoxesUnChecked()) {
                        if (this._isMounted) this.setState({groupUnCheckedStates: {}}, () => {
                            this.loadTasks()
                        })
                    } else {
                        let newGroupState = {}
                        groups.forEach(grp => {
                            newGroupState[grp.id] = true
                        })
                        if (this._isMounted) this.setState({groupUnCheckedStates: newGroupState}, () => {
                            this.loadTasks()
                        })
                    }
                }}
                disabled={this.state.isLoading}
            > {this._areAllBoxesUnChecked() ? this.props.translate("task_view.select_all","Alle auswählen") : this.props.translate("task_view.deselect_all","Alle abwählen") }
            </EducaCardLinkButton>
        </div>


        return {
            heading: {textAndId: this.props.translate("groups","Gruppen"), icon : <i className={"fas fa-filter"}/>, component: headingComponent},
            content: content
        }
    }

    getSideMenu() {
        return <SideMenu
            menus={
                [
                    this.getTaskViewForSideMenu(),
                    this.getArchiveToggleForSideMenu(),
                    this.getAppsForSideMenu(this.props.store.currentCloudUser.apps),
                    this.getGroupMenuObjectsForSideMenu(this.props.store.currentCloudUser.groups),
                ]
            }
        >
        </SideMenu>
    }

    getContent() {
        return <div>
            <div className="d-flex justify-content-between">
                <div style={{width: "300px"}} className={"m-2"}>
                    <div style={{display: "flex", flexDirection: "row"}}>
                        <div style={SideMenuHeadingStyle}>{this.props.translate("tasks","Aufgaben")}</div>
                        {FliesentischZentralrat.globalTaskCreate() ?
                            <EducaCircularButton
                                className={"mb-1"}
                                style={{marginLeft: "5px"}}
                                tooltip={this.props.translate("tasks.create","Aufgabe erstellen")}
                                onClick={() => {
                                    this.taskTemplateSelectorRef?.current.open(this.state.taskTemplates)
                                }}
                                variant={"success"}
                                size={"small"}
                            >
                                <i className="fas fa-plus"></i>
                            </EducaCircularButton>
                        : null}
                    </div>
                    {/*{FliesentischZentralrat.globalTaskCreate() ?*/}
                    {/*    <Dropdown as={ButtonGroup} className={"mt-2 mb-2"} style={{width: "100%"}}>*/}
                    {/*            <div>*/}
                    {/*                <Dropdown.Toggle*/}
                    {/*                                 as={CreateTemplateToggle}*/}
                    {/*                                 variant={"light"}*/}
                    {/*                                 style={{backgroundColor: "rgb(229, 230, 235)"}}*/}
                    {/*                                 id="dropdown-split-basic"/>*/}
                    {/*                <Dropdown.Menu>*/}
                    {/*                    <Dropdown.Item onClick={() => {*/}
                    {/*                    }}>Aus Vorlage erstellen </Dropdown.Item>*/}
                    {/*                    <Dropdown.Item onClick={() => {*/}
                    {/*                        this.taskTemplateEditorRef?.current.open(null)*/}
                    {/*                    }}>Neue Vorlage</Dropdown.Item>*/}
                    {/*                </Dropdown.Menu>*/}
                    {/*            </div>*/}
                    {/*    </Dropdown> : null}*/}
                    <div style={{marginTop :"25px"}}>
                    {this.getSideMenu()}
                    </div>
                </div>{ this.state.isArchiveCheckboxChecked ? <div className="col mt-2">
                    <TaskArchiveView
                        ref={this.taskArchiveViewRef}
                        selectedTaskId={this.state.urlTaskId /* From URL*/}
                        selectionChanged={(id) => this.setNewTaskId(id)}

                        taskChangedCallback={(task, callback) => this.onArchivedTaskChanged(task, callback)}
                        maxHeightDiff={this.state.isStatisticOpen ? 300 : 250}
                        tasks={this.state.archivedTasks}
                        />
                </div> :
                <div className="col mt-2">

                    { this.state.needLoading && !this.state.isLoading ?
                    <Alert className={"mt-1"} variant={"info"}>
                        <div style={{display :"flex", flex :1, alignItems: 'center'}}>
                        <b>Hinweis: </b>
                            <div style={{width: 4}}></div>
                            {this.props.translate("task_view.reload_info","Die Aufgaben werden nicht automatisch aktualisiert, wenn eine Aufgabe bearbeitet wird.")}
                        <div style={{display :"flex", flex :1, justifyContent :"flex-end"}}>
                            <Button  className="btn btn-primary" onClick={() => {
                                this.loadTasks();
                            }} >
                                <i className="fas fa-redo-alt"></i>  {this.props.translate("task_view.reload_tasks","Aufgaben neuladen")}
                            </Button>
                        </div>
                        </div>
                    </Alert> : <></> }
                    <TaskStatesColumnView
                        ref={this.taskColumnViewRef}
                        selectedTaskId={this.state.urlTaskId /* From URL*/}
                        selectionChanged={(id) => this.setNewTaskId(id)}
                        viewForCloudId={this.state.viewForCloudId}
                        taskChangedCallback={(task, callback) => this.onTaskChanged(task, callback)}
                        maxHeightDiff={this.state.isStatisticOpen ? 300 : 250}
                        tasks={this.state.tasks}
                    />
                </div>}
            </div>
            <TaskTemplateSelectorModal ref={this.taskTemplateSelectorRef}
                                       taskChangedCallback={() => this.loadTasks()}/>
        </div>

    }


    render() {
        return this.getContent()
    }


}

const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(withEducaLocalizedStrings(EducaTaskViewReact));
