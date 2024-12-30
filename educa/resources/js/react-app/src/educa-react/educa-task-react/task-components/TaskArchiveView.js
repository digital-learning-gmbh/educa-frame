import React, {Component} from 'react';
import {TaskDetailSplitView} from "./TaskDetailSplitView";


const archiveHeader = () => <h4 style={{color: "rgb(108, 117, 125)", margin: "0px"}}><b><i
    className="fas fa-envelope-open-text"></i> {"Archiv"}</b></h4>


class TaskArchiveView extends Component {


    constructor(props) {
        super(props);

        this.state =
            {
                tasks: props.tasks,
                recentlySelectedTask: null
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
            this.setState({recentlySelectedTask: task})
        }
    }

    taskSelectionChanged(task) {
        if (this.props.selectionChanged && task.id)
            this.props.selectionChanged(task.id)
    }

    onTaskChanged(task) {
        this.props.taskChangedCallback(task, () => this.prepareTasks())
    }

    prepareTasks() {
        if (!this.props.tasks || !Array.isArray(this.props.tasks)) {
            return
        }
        this.setState({tasks: this.props.tasks})
    }

    getColumnView(type) {
        let headerFunc = archiveHeader;
        let header = <div
            style={{cursor: "pointer"}}
            onClick={() => this.setState( {
                recentlySelectedTask: null
            })} > {headerFunc()} </div>
        let tasks = this.state.tasks
        let hasTasks = tasks && tasks.length !== 0

        let noTasks
        if (!hasTasks)
            noTasks = <div className={"mt-2 mb-2"} style={{fontWeight :"bold", color: "rgb(108, 117, 125)"}}><i
                className="fas fa-info-circle"></i> Keine Aufgaben im Archiv</div>

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
                        tasks={tasks}
                    />
                    : noTasks}
            </div>
        </div>
    }



    render() {
        return (
            <div className="row mt-2">

                {this.getColumnView()}

            </div>
        );
    }
}

export default TaskArchiveView;
