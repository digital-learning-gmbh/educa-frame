<?php

namespace App\Http\Controllers\API\V1;

use App\FavoriteFilter;
use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;

class FavoriteFilterController extends ApiController
{

    public function getFilterForKey(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        return parent::createJsonResponse("filter",false,200,["filter" =>
            FavoriteFilter::where('key','=',$request->input("key"))->where("cloudid", "=", $cloud_user->id)->get()]);
    }

    private function createFilter($cloudId, $key, $label, $config )
    {

        $filter = new FavoriteFilter();
        $filter->cloudid = $cloudId;
        $filter->key = $key;
        $filter->label = $label;
        $filter->config = json_encode($config);

        $filter->save();

        return $filter;
    }

    public function updateFilter(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $id = $request->id;

        $filter = FavoriteFilter::find($id);

        if($filter == null)
        {
            if(!$request->key || !is_array($request->config))
                return $this->createJsonResponse("Config and/or key not set", true, 400);

            $filter = $this->createFilter($cloud_user->id, $request->key, $request->label, $request->config );
            $request->key = $filter->key;
            return $this->getFilterForKey($request);
        }

        if($filter->cloudid != $cloud_user->id)
        {
            return $this->createJsonResponse("Keine Rechte", true, 400);
        }
        $filter->key = $request->input("key");
        $filter->label = $request->input("label");
        $filter->config = $request->input("config");

        $filter->save();

        return $this->getFilterForKey($request);
    }

    public function deleteFilter($id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $filter = FavoriteFilter::find($id);
        if($filter == null || $filter->cloudid != $cloud_user->id)
        {
            return $this->createJsonResponse("Keine Rechte", true, 400);
        }
        $filter->delete();

        return $this->getFilterForKey($request);
    }
}
