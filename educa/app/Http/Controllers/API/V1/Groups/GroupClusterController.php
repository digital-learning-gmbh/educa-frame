<?php

namespace App\Http\Controllers\API\V1\Groups;

use App\GroupCluster;
use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;

class GroupClusterController extends ApiController
{

    public function createCluster(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }

        $gruppenCluster = new GroupCluster();
        $gruppenCluster->name =  $request->input("name");
        $gruppenCluster->cloudid = $cloud_user->id;
        $gruppenCluster->save();


        return parent::createJsonResponse("Created cluster.", false, 200, ["cluster" => $gruppenCluster]);
    }


    public function updateCluster(Request $request, $cluster_id)
    {
        $cloud_user = parent::getUserForToken($request);

        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }
    }

    public function deleteCluster(Request $request, $cluster_id)
    {
        $cloud_user = parent::getUserForToken($request);

        if($cloud_user == null)
        {
            return parent::createJsonResponse("This token is not valid.", true, 400);
        }
    }
}
