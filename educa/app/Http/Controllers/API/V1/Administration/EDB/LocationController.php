<?php

namespace App\Http\Controllers\API\V1\Administration\EDB;

use App\EDBConferenceLocation;
use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;

class LocationController extends ApiController
{

    private function getAllLocations()
    {
        return EDBConferenceLocation::all();
    }

    public function listLocations(Request $request)
    {

        return $this->createJsonResponse("locations", false, 200, ["locations" => $this->getAllLocations()]);
    }

    public function createLocation(Request $request)
    {

        $object = (object) $request->object;

        if(!$object)
            return $this->createJsonResponse("object not set", true, 400);

        if(
             !property_exists($object, "name")
             || !property_exists($object, "street")
             || !property_exists($object, "zipcode")
             || !property_exists($object, "city")
        )
            return $this->createJsonResponse("not all mandatory properties set.", true, 400);


        $location = new EDBConferenceLocation();
        $location->name = $object->name;
        $location->description = !property_exists($object, "description")?:$object->description;
        $location->homepage = !property_exists($object, "homepage")?:$object->homepage;
        $location->shortcut = !property_exists($object, "shortcut")?:$object->shortcut;

        $location->street = !property_exists($object, "street")?:$object->street;
        $location->zipcode =!property_exists($object, "zipcode")?:$object->zipcode;
        $location->city = !property_exists($object, "city")?:$object->city;
        $location->name = !property_exists($object, "name")?:$object->name;


        $location->save();

        return $this->createJsonResponse("location created", false, 200, ["new_location" =>$location, "locations" =>$this->getAllLocations()]);

    }



    public function editLocation($location_id, Request $request)
    {
        if(!$location_id)
            return $this->createJsonResponse("location id not set", true, 400);

        $location = EDBConferenceLocation::find($location_id);

        if(!$location)
            return $this->createJsonResponse("location not found", true, 400);

        $object = (object) $request->object;
        if(!$object)
            return $this->createJsonResponse("object not set", true, 400);

        if(
            !property_exists($object, "name")
            || !property_exists($object, "street")
            || !property_exists($object, "zipcode")
            || !property_exists($object, "city")
        )
            return $this->createJsonResponse("not all mandatory properties set.", true, 400);

        $location->name = $object->name;
        $location->description = !property_exists($object, "description")?:$object->description;
        $location->homepage = !property_exists($object, "homepage")?:$object->homepage;
        $location->shortcut = !property_exists($object, "shortcut")?:$object->shortcut;

        $location->street = !property_exists($object, "street")?:$object->street;
        $location->zipcode =!property_exists($object, "zipcode")?:$object->zipcode;
        $location->city = !property_exists($object, "city")?:$object->city;
        $location->name = !property_exists($object, "name")?:$object->name;


        $location->save();

        return $this->createJsonResponse("location edited", false, 200, ["new_location" =>$location, "locations" =>$this->getAllLocations()]);

    }


    public function deleteLocation($location_id, Request $request)
    {
        if(!$location_id)
            return $this->createJsonResponse("location id not set", true, 400);

        $location = EDBConferenceLocation::find($location_id);

        if(!$location)
            return $this->createJsonResponse("location not found", true, 400);

        $location->delete();

        return $this->createJsonResponse("location deleted", false, 200, ["locations" => $this->getAllLocations()]);

    }
}
