import React, {Component} from 'react';
import {TASK_STATES} from "../EducaTaskViewReact";
import {TaskDetailSplitView} from "./TaskDetailSplitView";
import {TaskCard} from "./TaskCard";
import Button from "react-bootstrap/Button";


const openHeader = () => <h4 style={{color: "rgb(108, 117, 125)", margin: "0px"}}><b><img src={"/images/task_icons/Aufgabe_Offen_neu_grau.png"} width={20}/> {"Offen"}</b></h4>
const reviewHeader = () => <h4 style={{color: "rgb(108, 117, 125)", margin: "0px"}}><b><img src={"/images/task_icons/Aufgabe_Rückmeldung_neu_grau.png"} width={20}/> {"Rückmeldung"} </b></h4>
const completedHeader = () => <h4 style={{color: "rgb(108, 117, 125)", margin: "0px"}}><b><img src={"/images/task_icons/Aufgabe_Erledigt_neu_grau.png"} width={20}/> {"Erledigt"} </b></h4>


class TaskStatesColumnView extends Component {


    constructor(props) {
        super(props);

        this.state =
            {
                activeColumn: null,
                activeComponentAfterAnimation: false,

                recentlySelectedTask: {},

                allTasks: [],
                openTasks: [], //do some caching
                reviewTasks: [],
                completedTasks: [],
            }


    }

    componentDidMount() {
        this._isMounted = true
        this.prepareTasks()
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    componentDidUpdate(prevProps, prevState, snapshot) {

        if (!prevProps.tasks || !this.props.tasks || prevProps.tasks.length != this.props.tasks.length)//|| this.checkTaskStateChange() )
        {
            this.selectTask(this.props.selectedTaskId)
            this.prepareTasks()
        }

    }

    selectTask(id) {
        let task = this.props.tasks?.find(t => t.id == id)
        if (task) {
            this.setState({recentlySelectedTask: task, activeColumn: task.state})
        }
    }

    taskSelectionChanged(task) {
        if (this.props.selectionChanged)
            this.props.selectionChanged(task.id)
    }

    prepareTasks() {
        if (!this.props.tasks || !Array.isArray(this.props.tasks)) {
            return
        }
        let open = []
        let review = []
        let completed = []
        this.props.tasks.forEach(task => {
            if (task.state === TASK_STATES.DRAFT)
                open.push(task)
            else if (task.state === TASK_STATES.REVIEW)
                review.push(task)
            else if (task.state === TASK_STATES.COMPLETED)
                completed.push(task)

        })
        this.setState({openTasks: open, reviewTasks: review, completedTasks: completed, allTasks: this.props.tasks})
    }

    getTasksForType(type) {
        if (type === TASK_STATES.DRAFT)
            return this.state.openTasks
        else if (type === TASK_STATES.REVIEW)
            return this.state.reviewTasks
        else if (type === TASK_STATES.COMPLETED)
            return this.state.completedTasks
        return []
    }

    onTaskChanged(task) {
        this.props.taskChangedCallback(task, () => this.prepareTasks())
    }

    getDashboardView(type) {
        let headerFunc = type === TASK_STATES.DRAFT ? openHeader : type === TASK_STATES.COMPLETED ? completedHeader : reviewHeader;
        let header = <div style={{cursor: "pointer"}} onClick={() => this.setState({
            activeColumn: type,
            recentlySelectedTask: null
        })}> {headerFunc()} </div>
        let tasks = this.getTasksForType(type)
        let hasTasks = tasks && tasks.length !== 0
        const openTasks = tasks?.reduce( (prev,curr) => prev+(curr?.is_submission_seen? 0 : 1) ,0)

        let listContent = []
        if (!hasTasks)
            listContent =<div className={"mt-2 mb-2"} style={{fontWeight :"bold", color: "rgb(108, 117, 125)"}}><i
                className="fas fa-info-circle"></i> Keine Aufgaben</div>
        else
            listContent = tasks.map((task, i) => {
                if (i < 5)
                    return <TaskCard
                        key={task.id}
                        taskClickCallback={(task) => {
                            this.setState({recentlySelectedTask: task, activeColumn: type})
                        }}
                        task={task}/>
            })

        if (!this.state.activeColumn)
            return <div className={"col pt-2 ml-1 mr-1 card bg-light"}>

                {header}
                <div className="p-1"
                     style={{maxHeight: "calc(100vh - " + this.props.maxHeightDiff + "px)", overflow: "auto"}}>
                    {type == TASK_STATES.DRAFT? !openTasks? <b>Keine ungelesenen Aufgaben</b> : <b>Ungelesene Aufgaben: {openTasks}</b> : null}
                    {listContent}
                    {hasTasks && tasks.length > 5 ? <Button className="btn-light" style={{
                        width: "100%",
                        backgroundColor: "#e5e6eb",
                        marginTop: "5px",
                        marginBottom: "5px"
                    }}
                                                            onClick={() => {
                                                                this.setState({
                                                                    recentlySelectedTask: null,
                                                                    activeColumn: type
                                                                })
                                                            }}>
                        <div><i className="fas fa-list"/> {tasks.length - 5} weitere</div>
                    </Button> : null}
                </div>
            </div>
    }

    getColumnView(type) {
        let headerFunc = type === TASK_STATES.DRAFT ? openHeader : type === TASK_STATES.COMPLETED ? completedHeader : reviewHeader;
        let isActive = type === this.state.activeColumn
        let header = <div
            style={isActive ? {cursor: "pointer"} : {cursor: "pointer", writingMode: "vertical-lr", width: "1.6rem"}}
            onClick={() => this.setState(isActive ? {activeColumn: null} : {
                activeColumn: type,
                recentlySelectedTask: null
            })} > {headerFunc()} </div>
        let tasks = this.getTasksForType(type)
        let hasTasks = tasks && tasks.length !== 0

        let noTasks
        if (!hasTasks)
            noTasks = <div className={"mt-2 mb-2"} style={{fontWeight :"bold", color: "rgb(108, 117, 125)"}}><i
                className="fas fa-info-circle"></i> Keine Aufgaben</div>

        if (!isActive)
            return <div
                onClick={() => this.setState(isActive ? {activeColumn: null} : {
                    activeColumn: type,
                    recentlySelectedTask: null
                })}
                className={"p-1 m-2 card bg-light"}>
                {header}
            </div>

        return <div
            className={"col pt-2 ml-1 mr-1 mt-2 card bg-light"}>
            <div>
                {header}
                {hasTasks ?
                    <TaskDetailSplitView
                        selectedTaskChanged={(task) => this.taskSelectionChanged(task)}
                        taskChangedCallback={(task) => this.onTaskChanged(task)}
                        initialTask={this.state.recentlySelectedTask}
                        maxHeightDiff={this.props.maxHeightDiff}
                        viewForCloudId={this.props.viewForCloudId}
                        tasks={tasks}
                    />
                    : noTasks}
            </div>
        </div>
    }

    openColumn() {
        return this.getColumnView(TASK_STATES.DRAFT)
    }

    reviewColumn() {
        return this.getColumnView(TASK_STATES.REVIEW)
    }

    completedColumn() {
        return this.getColumnView(TASK_STATES.COMPLETED)
    }

    openList() {
        return this.getDashboardView(TASK_STATES.DRAFT)
    }

    reviewList() {
        return this.getDashboardView(TASK_STATES.REVIEW)
    }

    completedList() {
        return this.getDashboardView(TASK_STATES.COMPLETED)
    }


    render() {
        return (
            <div className="row mt-2">

                {!this.state.activeColumn ?
                    <> {this.openList()}
                        {this.reviewList()}
                        {this.completedList()}
                    </>
                    :
                    <> {this.openColumn()}
                        {this.reviewColumn()}
                        {this.completedColumn()}
                    </>
                }

            </div>
        );
    }
}

export default TaskStatesColumnView;
