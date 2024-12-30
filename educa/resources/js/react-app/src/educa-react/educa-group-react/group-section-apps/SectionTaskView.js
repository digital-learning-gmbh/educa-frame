import React, {Component} from 'react';
import {connect} from "react-redux";
import TaskStatesColumnView from "../../educa-task-react/task-components/TaskStatesColumnView";
import AjaxHelper from "../../helpers/EducaAjaxHelper";
import TaskEditorModal from "../../educa-task-react/task-components/TaskEditorModal";
import {Button} from "react-bootstrap";
import EducaHelper from "../../helpers/EducaHelper";
import FliesentischZentralrat from "../../FliesentischZentralrat";


class SectionDocumentsView extends Component {


    constructor(props) {
        super(props);

        this.state =
            {
                tasks: [],
                //  statistics : []
            }
        this.taskEditorRef = React.createRef()
    }


    componentDidMount() {
        this._isMounted = true
        this.loadTasks()
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    loadTasks() {
        this.setState({isLoading: true})

        AjaxHelper.getTasks(null, null, [this.props.group.id], this.state.isPersonalCheckboxChecked, [this.props.section.id])
            .then(resp => {
                if (resp.status > 0 && resp.payload?.tasks) {
                    this.setState({tasks: resp.payload.tasks})//, statistics: resp.payload?.statistics})
                    //this.prepareEasyStatistics(resp.payload.tasks)
                    return;
                }
                throw new Error(resp.message)
            })
            .catch(err => {
                EducaHelper.fireErrorToast("Fehler", "Konnte Aufgaben nicht laden. Servernachricht: " + err.message)
                this.setState({tasks: []})
            })
            .finally(() => {
                this.setState({isLoading: false})
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

                this.setState({tasks: this.state.tasks}, () => {
                    //console.log(this.state.tasks[i], task)
                    //Callback has to be fired, when the tasks here got updated, in oder to reload it in the child components
                    callback()
                    // this.prepareEasyStatistics( this.state.tasks)
                })
                break
            }
        }

    }

    render() {
        return (<>
                <div className={"col"} style={{margin: "10px"}}>
                    {FliesentischZentralrat.sectionTaskCreate(this.props.section)? <Button variant={"light"} style={{backgroundColor: "rgb(229, 230, 235)", width: "100%"}}
                            className={"mt-2 mb-2"}
                            onClick={() => {
                                this.taskEditorRef?.current.open(null)
                            }}
                    >
                        <i className="fas fa-plus"></i>
                        Aufgabe erstellen
                    </Button> : null}
                    <TaskStatesColumnView
                        taskChangedCallback={(task, callback) => this.onTaskChanged(task, callback)}
                        maxHeightDiff={400}
                        tasks={this.state.tasks}
                    />
                    <TaskEditorModal ref={this.taskEditorRef}
                                     preselectedSections={[this.props.section]}
                                     taskChangedCallback={() => this.loadTasks() /*new task. just reload*/}/>
                </div>
            </>
        );
    }

}


const mapStateToProps = state => ({store: state})

export default connect(mapStateToProps)(SectionDocumentsView);
