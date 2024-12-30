<?php

namespace App\Http\Controllers\API\V1\Cloud;

use App\Http\Controllers\API\ApiController;
use App\Models\Competence;
use App\Models\CompetenceCluster;
use Illuminate\Http\Request;

class CompetenceController extends ApiController
{
    public function createCluster(Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $competenceCluster = new CompetenceCluster();
        $competenceCluster->name = $request->input("name");
        $competenceCluster->description = $request->input("description");
        $competenceCluster->save();
        $competenceCluster->refresh();

        $clusters = CompetenceCluster::all();
        return parent::createJsonResponse("all clusters", false, 200, ["cluster" => $competenceCluster, "clusters" => $clusters]);
    }

    public function updateCluster($clusterId, Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $competenceCluster = CompetenceCluster::find($clusterId);
        if ($competenceCluster == null)
            return parent::createJsonResponse("not found.", true, 404);

        $competenceCluster->name = $request->input("name");
        $competenceCluster->description = $request->input("description");
        $competenceCluster->save();

        $clusters = CompetenceCluster::all();
        return parent::createJsonResponse("all clusters", false, 200, ["cluster" => $competenceCluster, "clusters" => $clusters]);
    }

    public function deleteCluster($clusterId, Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $competenceCluster = CompetenceCluster::find($clusterId);
        if ($competenceCluster == null)
            return parent::createJsonResponse("not found.", true, 404);

        $competenceCluster->delete();

        return $this->getCluster($request);
    }

    public function getCluster(Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $clusters = CompetenceCluster::all();
        return parent::createJsonResponse("all clusters", false, 200, ["clusters" => $clusters]);
    }

    public function createCompentenceForCluster($clusterId, Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $competenceCluster = CompetenceCluster::find($clusterId);
        if ($competenceCluster == null)
            return parent::createJsonResponse("not found.", true, 404);

        $competence = new Competence();
        $competence->name = $request->input("name");
        $competence->description = $request->input("description");
        $competence->competence_cluster_id = $competenceCluster->id;
        $competence->color = $request->input("color");
        $competence->save();
        $competence->refresh();

        $competenceCluster = CompetenceCluster::find($clusterId);
        if ($competenceCluster == null)
            return parent::createJsonResponse("not found.", true, 404);
        $competenceCluster->load("competences");

        return parent::createJsonResponse("cluster details", false, 200, ["competence" => $competence, "cluster" => $competenceCluster]);
    }

    public function updateCompentence($competenceId, Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $competence = Competence::find($competenceId);
        if ($competence == null)
            return parent::createJsonResponse("not found.", true, 404);

        $competence->name = $request->input("name");
        $competence->color = $request->input("description");
        $competence->color = $request->input("color");
        $competence->save();

        return $this->getCompetencesForCluster($competence->competence_cluster_id, $request);
    }

    public function getCompetencesForCluster($clusterId, Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $competenceCluster = CompetenceCluster::find($clusterId);
        if ($competenceCluster == null)
            return parent::createJsonResponse("not found.", true, 404);
        $competenceCluster->load("competences");

        return parent::createJsonResponse("cluster details", false, 200, ["cluster" => $competenceCluster]);
    }

    public function getCompetence($competenceId, Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $competence = Competence::find($competenceId);
        if ($competence == null)
            return parent::createJsonResponse("not found.", true, 404);

        return parent::createJsonResponse("competence", false, 200, ["competence" => $competence]);
    }

    public function getAllCompetences(Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $clusters = CompetenceCluster::all();
        $clusters->load('competences');
        return parent::createJsonResponse("all clusters with competences", false, 200, ["clusters" => $clusters]);
    }

    public function deleteCompentence($competenceId, Request $request)
    {
        $user = parent::getUserForToken($request);
        if ($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $competence = Competence::find($competenceId);
        if ($competence == null)
            return parent::createJsonResponse("not found.", true, 404);

        $clusterId = $competence->competence_cluster_id;

        $competence->delete();

        return $this->getCompetencesForCluster($clusterId, $request);
    }
}
