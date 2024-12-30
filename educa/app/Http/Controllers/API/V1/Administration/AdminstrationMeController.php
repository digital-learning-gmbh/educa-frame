<?php

namespace App\Http\Controllers\API\V1\Administration;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\AppController;
use Illuminate\Http\Request;

class AdminstrationMeController extends AppController
{

    public function me(Request $request) {
        $cloudUser = parent::getCloudUser();
        $adminUser = null;
        if($cloudUser != null)
            $adminUser = $cloudUser->administrationUser();

        if($adminUser == null)
            return ApiController::createJsonResponseStatic("no admin user :(", true, 401, []);

        $boards = $adminUser->boards;
        if(count($boards) == 0)
        {
            \BoardSeeder::basicStartBoard("Dashboard", $adminUser);
            $boards = $adminUser->boards;
        }
        $sharedBoards = $adminUser->boardsGeteilt;
        $systemMessage = \App\SystemEinstellung::getEinstellungen("system.message","");

        return ApiController::createJsonResponseStatic("thats me", false, 200, [
            "cloud_user" => $cloudUser,
            "admin_user" => $adminUser,
            "boards" => $boards,
            "shared_boards" => $sharedBoards,
            "systemMessage" => $systemMessage,
            "jwt" => \Illuminate\Support\Facades\Session::get("jwt_token"),
            "school_id" => parent::getSchool()->id,
            "year_id" => parent::getSchoolYear()->id,
            "draft_id" => parent::getEntwurf()->id,
            "permissions_global" => $cloudUser->getAllPermissions()->pluck('name')
        ]);
    }

}
