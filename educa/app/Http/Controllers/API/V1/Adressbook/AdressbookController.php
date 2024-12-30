<?php

namespace App\Http\Controllers\API\V1\Adressbook;

use App\CloudID;
use App\Fach;
use App\Http\Controllers\API\ApiController;
use App\Lehrer;
use App\Lesson;
use App\LessonPlan;
use App\Mail\AdressbookContactMail;
use App\Models\AdressbookEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use StuPla\CloudSDK\Permission\Models\Role;
use StuPla\CloudSDK\Permission\Scope;

class AdressbookController extends ApiController
{
    public function getContacts(Request $request)
    {
        $user = parent::getUserForToken($request);
        if($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $personalContacts = $this->getTeacherContacts($user);
        $schoolContacts = $this->getSchoolContacts($user);
        $generalContacts = $this->getGeneralContacts($user);

        return parent::createJsonResponse("personal and general contacts for the user", false, 200,[
            "generalContacts" => $generalContacts,
            "teacherContacts" => $personalContacts,
            "schoolContacts" => $schoolContacts
        ]);
    }

    private function getGeneralContacts($user)
    {
        $roleIds = $user->roles()->pluck("id");
        $addressEntry = AdressbookEntry::whereIn("id",DB::table("adressbook_entry_role")->whereIn("role_id",$roleIds)->pluck("adressbook_entry_id"))->get();
        foreach($addressEntry as $entry)
        {
            $entry->cloudId = CloudID::find($entry->cloudid);
            $entry->identifier = "general_".$entry->id;
        }
        return $addressEntry;
    }

    private function getTeacherContacts(CloudID $user)
    {
        $dozent = [];
        $subjects_for_dozent = [];

        $schuler = $user->getSchuler();
        if($schuler == null)
            return [];

        foreach($schuler->klassenAktuell() as $klasse) {
        $lessonIds = DB::table('klasse_lesson_plan')->where('klasse_id', '=', $klasse->id)->pluck('lesson_plan_id');
        $lesson_plans = LessonPlan::find($lessonIds);
        foreach ($lesson_plans as $lesson_plan)
        {
            foreach ($lesson_plan->dozent as $singleDozent) {
                if (!in_array($singleDozent->id, $dozent)) {
                    $dozent[] = $singleDozent->id;
                    if(!array_key_exists($singleDozent->id, $subjects_for_dozent))
                    {
                        $subjects_for_dozent[$singleDozent->id] = [];
                    }
                    $subjects_for_dozent[$singleDozent->id][] = $lesson_plan->fach_id;
                }
            }
        }

        $lessonIds = DB::table('klasse_lesson')->where('klasse_id', '=', $klasse->id)->pluck('lesson_id');
        $lesson_plans = Lesson::find($lessonIds);
        foreach ($lesson_plans as $lesson_plan)
        {
            foreach ($lesson_plan->dozent as $singleDozent) {
                if (!in_array($singleDozent->id, $dozent)) {
                    $dozent[] = $singleDozent->id;
                    if(!array_key_exists($singleDozent->id, $subjects_for_dozent))
                    {
                        $subjects_for_dozent[$singleDozent->id] = [];
                    }
                    $subjects_for_dozent[$singleDozent->id][] = $lesson_plan->fach_id;
                }
            }
        }
    }

        $dozenten = Lehrer::find($dozent);
        $data = [];

        foreach ($dozenten as $singleDozent)
        {
            if($singleDozent->status == "active") {
                $node = new AdressbookEntry();
                $node->identifier = "dozent_".$singleDozent->id;
                $node->name = $singleDozent->lastname.", ".$singleDozent->firstname;
                $node->email = $singleDozent->getAddInfo()->email;
                $node->role = "Unterricht in ".Fach::whereIn("id",$subjects_for_dozent[$singleDozent->id])->pluck("name")->join(", ");
                $node->cloudId = $singleDozent->getCloudID();
                $node->location = $singleDozent->schulen->pluck("name")->join(", ");
                $data[] = $node;
            }
        }
        return $data;
    }

    private function getSchoolContacts($user)
    {
        $schuler = $user->getSchuler();

        if($schuler != null)
            return $this->prepareSchoolAsContact($schuler->schulen);


        $lehrer = $user->getLehrer();
        if($lehrer != null)
            return $this->prepareSchoolAsContact($lehrer->schulen);


        $employee = $user->getMitarbeiter();
        if($employee != null)
            return $this->prepareSchoolAsContact($employee->schulen);

        return [];
    }

    private function prepareSchoolAsContact($schools)
    {
        $schoolsAsContacts = [];
        foreach($schools as $school)
        {
            $node = new AdressbookEntry();
            $node->identifier = "school_".$school->id;
            $node->name = $school->name;
            $node->email = $school->getAddInfo()->email;
            $node->role = "Schule";
            $node->cloudId = null;
            $node->telephone = $school->getAddInfo()->tel_business;
            $node->mobil = $school->getAddInfo()->mobile;
            $node->location = $school->getAddInfo()->street." ".$school->getAddInfo()->plz." ".$school->getAddInfo()->city;

            $schoolsAsContacts[] = $node;
        }
        return $schoolsAsContacts;
    }

    public function getAllGeneralContacts(Request $request)
    {
        $entries = AdressbookEntry::all();
        foreach ($entries as $entry)
        {
            $entry->roles = Role::whereIn("id",$entry->roleIds())->get();
        }
        return parent::createJsonResponse("global adressbook entries.", false, 200,["entries" => $entries, "roles" => Role::where('guard_name','=', 'cloud')
            ->where('scope_name','=', Scope::getDefaultName())->get()]);
    }

    public function addContact(Request $request)
    {
        $contact = json_decode(json_encode($request->input("contact")));
        $entry = new AdressbookEntry;
        $entry->name = $contact->name;
        $entry->email = property_exists($contact, "email") ? $contact->email : "";
        $entry->role = property_exists($contact, "role") ? $contact->role : "";
        $entry->location =  property_exists($contact, "location") ? $contact->location : "";
        $entry->telephone = property_exists($contact, "telephone") ?  $contact->telephone : "";
        $entry->save();

        if(property_exists($contact, "roles")) {
            foreach ($contact->roles as $role) {
                DB::table("adressbook_entry_role")
                    ->updateOrInsert(
                        [
                            "role_id" => $role,
                            "adressbook_entry_id" => $entry->id
                        ]);
            }
        }
        return $this->getAllGeneralContacts($request);
    }

    public function updateContact(Request $request)
    {
        $contact = json_decode(json_encode($request->input("contact")));
        $entry = AdressbookEntry::find($contact->id);
        if($entry == null)
            return parent::createJsonResponse("This contact is not valid.", true, 400);
        $entry->name = $contact->name;
        $entry->email = property_exists($contact, "email") ? $contact->email : "";
        $entry->role = property_exists($contact, "role") ? $contact->role : "";
        $entry->location =  property_exists($contact, "location") ? $contact->location : "";
        $entry->telephone = property_exists($contact, "telephone") ?  $contact->telephone : "";
        $entry->isMailAnonymized = property_exists($contact, "isMailAnonymized") ? $contact->isMailAnonymized : false;
        $entry->save();

        DB::table("adressbook_entry_role")
            ->where(["adressbook_entry_id" => $entry->id])->delete();

        if(property_exists($contact, "roles")) {
            foreach ($contact->roles as $role) {
                DB::table("adressbook_entry_role")
                    ->updateOrInsert(
                        [
                            "role_id" => $role,
                            "adressbook_entry_id" => $entry->id
                        ]);
            }
        }
        return $this->getAllGeneralContacts($request);
    }

    public function deleteContact(Request $request)
    {
        $contact = json_decode(json_encode($request->input("contact")));
        $entry = AdressbookEntry::find($contact->id);
        if($entry == null)
            return parent::createJsonResponse("This contact is not valid.", true, 400);

        DB::table("adressbook_entry_role")
            ->where(["adressbook_entry_id" => $entry->id])->delete();
        $entry->delete();
        return $this->getAllGeneralContacts($request);
    }

    public function sendMail(Request $request)
    {
        $user = parent::getUserForToken($request);
        if($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $email = $request->input("email");

        $file1 = $request->file("file1");
        $file2 = $request->file("file2");
        $file3 = $request->file("file3");

        Mail::to($email)->send(new AdressbookContactMail($user, $request->input("isMailAnonymized",false),$request->input("subject"),$request->input("message"), $request->input("name"), $file1, $file2, $file3));

        return parent::createJsonResponse("mail send", false, 200,[
            "message" => "E-Mail was send to the user"
        ]);
    }
}
