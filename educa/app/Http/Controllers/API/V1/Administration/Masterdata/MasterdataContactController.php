<?php

namespace App\Http\Controllers\API\V1\Administration\Masterdata;

use App\AdditionalInfo;
use App\FormularAbgeschickt;
use App\FormularAnhang;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\IBACompanyExtension;
use App\IBAEmployeeExtension;
use App\Kontakt;
use App\KontaktBeziehung;
use App\Merkmal;
use App\Schule;
use App\Schuler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use StuPla\CloudSDK\formular\models\Formular;

class MasterdataContactController extends AdministationApiController
{
    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/schools/{school_id}/contacts",
     *     description="",
     *     @OA\Parameter(
     *     name="school_id",
     *     required=true,
     *     in="path",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all contacts of a school in the system with additional information (masterdata)")
     * )
     */
    public function listContacts($school_id, Request $request)
    {
        if ($request->all) {
            $contactsNewQuery = DB::table('kontakts')->select(["kontakts.id as real_id", "kontakts.*",
                "additional_infos.tel_business as tel_business",
                "additional_infos.street as street",
                "additional_infos.plz as plz",
                "additional_infos.city as city"])->leftJoin(
                'additional_infos', 'kontakts.info_id', '=', 'additional_infos.id'
            )->groupBy("real_id")->get();
        } else
            $contactsNewQuery = DB::table('kontakts')->select(["kontakts.id as real_id", "kontakts.*", "additional_infos.*"])->leftJoin(
                'additional_infos', 'kontakts.info_id', '=', 'additional_infos.id'
            )->join(
                'kontakt_schule', 'kontakts.id', '=', 'kontakt_schule.kontakt_id'
            )->where('kontakt_schule.schule_id', '=', $school_id)->orderBy("real_id")->get();

        $contacts = [];
        foreach ($contactsNewQuery as $c) {
                $c->id = $c->real_id;
                if($c->type == "person")
                {
                    $c->connections = DB::table('kontakt_beziehungs')->select(["type as type", "kontakt2 as contact_id", "bemerkung as note"])->where('kontakt1','=',$c->id)->where('kontakt_beziehungs.type','=','arbeitet_bei')->limit(100)->get();
                } else {
                    $c->connections = DB::table('kontakt_beziehungs')->select(["type as type", "kontakt2 as contact_id", "bemerkung as note"])->where('kontakt2','=',$c->id)->where('kontakt_beziehungs.type','=','arbeitet_bei')->limit(100)->get();
                }
                $c->schulen = DB::table('kontakt_schule')->select(["schule_id as id"])->where('kontakt_id','=',$c->id)->get();
                $contacts[] = $c;

            //  foreach (
            //      DB::table("kontakt_beziehungs")->where('kontakt1', '=', $c->id)->orWhere('kontakt2', '=', $c->id)->get() as $connection)
            //   {
            //      $connections[] = [
            //          "type" => $connection->type,
            //       "contact_id" => $connection->kontakt1 == $c->id ? $connection->kontakt2 : $connection->kontakt1,
            //           "note" => $connection->bemerkung
            ///            ];
            // }
        }

        return parent::createJsonResponse("contacts", false, 200, ["contacts" => $contacts]);
    }

    public function contactDetails($contact_id, Request $request)
    {
        $contact = Kontakt::findOrFail($contact_id);

        $addInfo = $contact->getAddInfo();
        foreach ($addInfo->toArray() as $key => $value) {
            if ($key !== "id" && $key !== "email")
                $contact->$key = $value;
        }

        $contact->load('schulen');

        $connections = [];
        foreach ($contact->getBeziehungen() as $connection) {
            $connections[] = [
                "type" => $connection->type,
                "contact_id" => $connection->kontakt1 == $contact->id ? $connection->kontakt2 : $connection->kontakt1,
                "note" => $connection->bemerkung
            ];
        }
        $contact->connections = $connections;

        $settings = IBACompanyExtension::where('kontakt_id', '=', $contact->id)->first();
        if ($settings != null) {
            $settings->study_ids = $contact->studium->pluck("id");
            $settings->studies = $contact->studium;

            $settings->kontoinhaber = $addInfo->kontoinhaber;
            $settings->bank = $addInfo->bank;
            $settings->iban = $addInfo->iban;
            $settings->bic = $addInfo->bic;
            $settings->status = $contact->status;
        } else {
            $settings = [];
            $settings["status"] = $contact->status;
        }

        return parent::createJsonResponse("contacts", false, 200, ["contact" => $contact, "settings" => $settings]);
    }

    /**
     * @OA\Post(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/contacts/add",
     *     description="",
     *     @OA\Response(response="200", description="Add a student with additional info")
     * )
     */
    public function createContact(Request $request)
    {
        $contact = new Kontakt;
        $addInfo = new AdditionalInfo;
        foreach ($request->object as $key => $value) {
            if ($key != "id" && $key != "info_id" && $key != "personalnummer") {
                if (Schema::hasColumn($contact->getTable(), $key)) {
                    $contact->$key = $value;
                } elseif (Schema::hasColumn($addInfo->getTable(), $key)) {
                    $addInfo->$key = $value;
                }
            }
        }

        $addInfo->save();
        $contact->info_id = $addInfo->id;
        $contact->save();

        $contact->schulen()->sync($request->school_ids);

        if ($request->has("settings") && $contact->id != null) {
            $settings = IBACompanyExtension::where('kontakt_id', '=', $contact->id)->first();
            if ($settings == null)
                $settings = new IBACompanyExtension;
            $settings->kontakt_id = $contact->id;
            $settings->ubms_number = $request->settings["ubms_number"] ?? "";
            $settings->hrb = $request->settings["hrb"] ?? "";
            $settings->hra = $request->settings["hra"] ?? "";
            $settings->district_court = $request->settings["district_court"] ?? "";
            $settings->ceo = $request->settings["ceo"] ?? "";
            $settings->key_account = $request->settings["key_account"] ?? "";
            $settings->contact = $request->settings["contact"] ?? "";
            $settings->insolvenz_active = $request->settings["insolvenz_active"] ?? false;
            $settings->insolvenz_date = array_key_exists("insolvenz_date",$request->settings) && $request->settings["insolvenz_date"] ? Carbon::parse($request->settings["insolvenz_date"])->toDateTime() : null;
            $settings->change_of_name = $request->settings["change_of_name"] ?? "";
            $settings->takeover = $request->settings["takeover"] ?? "";

            $addInfo->kontoinhaber = $request->settings["kontoinhaber"] ?? "";
            $addInfo->bank = $request->settings["bank"] ?? "";
            $addInfo->iban = $request->settings["iban"] ?? "";
            $addInfo->bic = $request->settings["bic"] ?? "";
            $addInfo->save();

            $settings->save();
            if (array_key_exists("study_ids", $request->settings)) {
                $contact->studium()->sync($request->settings["study_ids"]);
            }
        }

        return $this->contactDetails($contact->id, $request);
    }


    /**
     * @OA\Post(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/contacts/{contact_id}/update",
     *     description="",
     *     @OA\Parameter(
     *     name="contact_id",
     *     required=true,
     *     in="path",
     *     description="id of the contact",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Update a contact with additional info")
     * )
     */
    public function updateContact($contact_id, Request $request)
    {
        $contact = Kontakt::findOrFail($contact_id);
        $addInfo = $contact->getAddInfo();

        $contact->email = $request->input("email");
        $addInfo->email = $request->input("email");
        foreach ($request->object as $key => $value) {
            if ($key != "id" && $key != "info_id" && $key != "personalnummer") {
                if ($key == "birthdate")
                    $value = $value == null ? null : Carbon::createFromTimestamp($value)->toDateTime();
                if (Schema::hasColumn($contact->getTable(), $key)) {
                    $contact->$key = $value;
                } elseif (Schema::hasColumn($addInfo->getTable(), $key)) {
                    $addInfo->$key = $value;
                }
            }
        }
        $addInfo->save();
        $contact->save();

        $contact->schulen()->sync($request->school_ids);

        if ($request->has("settings") && $contact->id != null) {
            $settings = IBACompanyExtension::where('kontakt_id', '=', $contact->id)->first();
            if ($settings == null)
                $settings = new IBACompanyExtension;
            $settings->kontakt_id = $contact->id;
            $settings->ubms_number = $request->settings["ubms_number"] ?? "";
            $settings->hrb = $request->settings["hrb"] ?? "";
            $settings->hra = $request->settings["hra"] ?? "";
            $settings->district_court = $request->settings["district_court"] ?? "";
            $settings->ceo = $request->settings["ceo"] ?? "";
            $settings->key_account = $request->settings["key_account"] ?? "";
            $settings->contact = $request->settings["contact"] ?? "";
            $settings->insolvenz_active = $request->settings["insolvenz_active"] ?? false;
            $settings->insolvenz_date = array_key_exists("insolvenz_date",$request->settings) && $request->settings["insolvenz_date"] ? Carbon::parse($request->settings["insolvenz_date"])->toDateTime() : null;
            $settings->change_of_name = $request->settings["change_of_name"] ?? "";
            $settings->takeover = $request->settings["takeover"] ?? "";

            $addInfo->kontoinhaber = $request->settings["kontoinhaber"] ?? "";
            $addInfo->bank = $request->settings["bank"] ?? "";
            $addInfo->iban = $request->settings["iban"] ?? "";
            $addInfo->bic = $request->settings["bic"] ?? "";
            $addInfo->save();

            $settings->save();

            if (array_key_exists("study_ids", $request->settings)) {
                $contact->studium()->sync($request->settings["study_ids"]);
            }
        }

        return $this->contactDetails($contact_id, $request);
    }


    public function contactStudents($contact_id, Request $request)
    {
        $contact = Kontakt::findOrFail($contact_id); // security
        if ($contact->type == "person") {
            $schuler = Schuler::where('kontakt_id', '=', $contact_id)->get();
        } else {
            $schuler = Schuler::where('company_id', '=', $contact_id)->get();
        }
        foreach ($schuler as $student) {
            $addInfo = $student->getAddInfo();
            foreach ($addInfo->toArray() as $key => $value) {
                if ($key !== "id")
                    $student->$key = $value;
            }
            $student->current_study_information = $student->getDateStudyInformation(Carbon::now());
            $student->status = $student->current_study_information == null || $student->current_study_information->kohorte == null ? "inactive" : "active";
            if($student->current_study_information == null)
            {
                $student->current_study_information = $student->getLastStudyInformation();
            }
            if($student->current_study_information != null)
            {
                $student->current_study_information->loadAll();
            }
            $student->last_study_information = $student->getLastStudyInformation();
            if($student->last_study_information != null)
            {
                $student->last_study_information->loadAll();
            }
            $student->first_study_information = $student->getFirstStudyInformation();
            if($student->first_study_information != null)
            {
                $student->first_study_information->loadAll();
            }
        }
        $schuler->each->append('first_kohorte');
        $schuler->each->load('ansprechpartner');
        $schuler->each->load('company');
        $schuler->each->load('schulen');
        return parent::createJsonResponse("contact students", false, 200, ["students" => $schuler]);
    }

    public function relationships($contact_id, Request $request)
    {
        $contact = Kontakt::findOrFail($contact_id); // security
        $beziehungen = $contact->getBeziehungen();
        return parent::createJsonResponse("contact relationships.", false, 200, ["relationships" => $beziehungen]);
    }

    public function addRelationships($contact_id, Request $request)
    {
        $contact = Kontakt::findOrFail($contact_id); // security
        $zuKontakt = Kontakt::findOrFail($request->input("contact_relation_id"));
        $type = $request->input("relation_type");

        $beziehung = new KontaktBeziehung;
        $beziehung->kontakt1 = $contact->id;
        $beziehung->kontakt2 = $zuKontakt->id;
        $beziehung->type = $type;
        $beziehung->save();

        $beziehungen = $contact->getBeziehungen();
        return parent::createJsonResponse("contact relationships.", false, 200, ["relationships" => $beziehungen]);
    }

    public function deleteRelationships($contact_id, $relationship_id, Request $request)
    {
        $contact = Kontakt::findOrFail($contact_id);
        $beziehung = KontaktBeziehung::findOrFail($relationship_id);
        $beziehung->delete();
        $beziehungen = $contact->getBeziehungen();

        return parent::createJsonResponse("contact relationships.", false, 200, ["relationships" => $beziehungen]);
    }

    public function deleteContact($contactId, Request $request)
    {
        $contact = Kontakt::findOrFail($contactId);


        return parent::createJsonResponse("contact deleted.", false, 200, $contact->delete());
    }

}
