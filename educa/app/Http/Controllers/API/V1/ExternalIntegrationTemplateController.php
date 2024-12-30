<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\ApiController;
use App\Models\ExternalIntegrationTemplate;
use Illuminate\Http\Request;

class ExternalIntegrationTemplateController extends ApiController
{
    public function getTemplates(Request $request)
    {
        return parent::createJsonResponse("templates",false,200,["templates" => ExternalIntegrationTemplate::all()]);
    }
}
