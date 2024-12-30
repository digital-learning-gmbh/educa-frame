<?php

namespace App\Http\Controllers\API\V1\Cloud;

use App\Http\Controllers\API\ApiController;
use DigitalLearning\EducaWorkflow\Facades\EducaWorkflow as FacadesEducaWorkflow;
use DigitalLearning\EducaWorkflow\Models\EducaWorkflow;
use DigitalLearning\EducaWorkflow\Models\EducaWorkflowDefinition;
use DigitalLearning\EducaWorkflow\Models\EducaWorkflowInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkflowController extends ApiController
{
    public function getWorkflows(Request $request)
    {
        $numOfInstances = DB::table("educa_workflow_instances")->count();
        $numOfInstancesRun = DB::table("educa_workflow_instances")->where("status","=","run")->count();
        $numOfInstancesCrashed = DB::table("educa_workflow_instances")->where("status","=","crashed")->count();
        $numOfInstancesFinished = DB::table("educa_workflow_instances")->where("status","=","finished")->count();
        
        return parent::createJsonResponse("workflows",false,200,
            ["workflows" => \DigitalLearning\EducaWorkflow\Facades\EducaWorkflow::getAllWorkflows(), "statistics" => [
                "numOfInstances" => $numOfInstances,
                "numOfInstancesRun" => $numOfInstancesRun,
                "numOfInstancesCrashed" => $numOfInstancesCrashed,
                "numOfInstancesFinished" => $numOfInstancesFinished
            ]
            ]);
    }

    public function addWorkflow(Request $request)
    {
        $workflow = new EducaWorkflow();
        $workflow->setName("Neuer Workflow");
        $workflow->setDefinition(EducaWorkflowDefinition::createEmptyDefintion());
        $workflow->save();

        return self::getWorkflows($request);
    }

    public function detailsWorkflow($workflow_id, Request $request)
    {
        $workflow = EducaWorkflow::load($workflow_id);
        $instances = FacadesEducaWorkflow::getAllInstances();
        $filteredInstances = [];
        foreach($instances as $instance)
        {
            if($instance->getWorkflowId() == $workflow_id)
                $filteredInstances[] = $instance;
        }
        return parent::createJsonResponse("workflow",false,200,["workflow" => $workflow, "instances" => $filteredInstances]);
    }

    public function updateWorkflow($workflow_id, Request $request)
    {
        $workflow = EducaWorkflow::load($workflow_id);
        $rawDefinition = (object)json_decode(json_encode($request->input("workflow")["definition"]));
        $definition = EducaWorkflowDefinition::loadFromObject($rawDefinition);
        $workflow->setDefinition($definition);
        $workflow->save();
        return parent::createJsonResponse("workflow updated",false,200,["workflow" => $workflow]);
    }

    public function startWorkflow($workflow_id, Request $request)
    {
        $workflow = EducaWorkflow::load($workflow_id);
        $instance = $workflow->startNewInstance();
        return $this->detailsWorkflow($workflow_id,$request);

    }

    public function availableNodes(Request $request)
    {
        return parent::createJsonResponse("workflows avaible nodes",false,200,["nodes" => \DigitalLearning\EducaWorkflow\Facades\EducaWorkflow::getPossibleNodes()]);
    }

    public function detailWorkflowInstance($instance_id, Request $request)
    {
        return parent::createJsonResponse("instances",false,200,["instance" => EducaWorkflowInstance::load($instance_id)]);
    }

    public function pauseWorkflowInstance($instance_id)
    {
        $instance = EducaWorkflowInstance::load($instance_id);
        $instance->setPause();
        $instance->save();
        return parent::createJsonResponse("instances",false,200,["instance" => $instance]);
    }

    public function unpauseWorkflowInstance($instance_id)
    {
        $instance = EducaWorkflowInstance::load($instance_id);
        $instance->unPause();
        $instance->save();
        return parent::createJsonResponse("instances",false,200,["instance" => $instance]);
    }

    public function submitFormData($instance_id, Request $request)
    {
        $instance = EducaWorkflowInstance::load($instance_id);
        $instance->setFormData($request->input("form_data"));
        $instance->save();
        return parent::createJsonResponse("instances",false,200,["instance" => $instance]);
    }


}
