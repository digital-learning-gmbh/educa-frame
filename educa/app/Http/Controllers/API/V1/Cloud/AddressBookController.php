<?php

namespace App\Http\Controllers\API\V1\Cloud;

use App\CloudID;
use App\Http\Controllers\API\ApiController;
use App\Mail\AdressbookContactMail;
use App\Models\AdressbookEntry;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use StuPla\CloudSDK\Permission\Models\Role;
use StuPla\CloudSDK\Permission\Scope;

class AddressBookController extends ApiController
{
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
        $entry->cloudid = property_exists($contact, "cloudid") && $contact->cloudid > 0?  $contact->cloudid : null;
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
        $entry->cloudid = property_exists($contact, "cloudid") && $contact->cloudid > 0?  $contact->cloudid : null;
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

    public function getBookingSlots(Request $request)
    {
        $cloudId = CloudID::findOrFail($request->input("cloudId"));
        $bookingSlots = BookingSlot::where(["cloudid" => $cloudId->id])->get();

        return parent::createJsonResponse("ok",false,200, ["bookingSlots" => $bookingSlots]);

    }

    public function createBookingSlot(Request $request)
    {
        $slot = new BookingSlot();
        $slot->day_week = $request->input("day_week");
        $slot->start = Carbon::createFromTimestamp($request->input("start"));
        $slot->end = Carbon::createFromTimestamp($request->input("end"));
        $slot->slot_duration = $request->input("slot_duration");
        $slot->slot_breaks = $request->input("slot_breaks");
        $slot->cloudid = $request->input("cloudId");
        $slot->save();

        return parent::createJsonResponse("created",false,200, ["slot" => $slot]);

    }

    public function updateBookingSlot($booking_slot_id, Request $request)
    {
        $slot = BookingSlot::findOrFail($booking_slot_id);
        $slot->day_week = $request->input("day_week");
        $slot->start = Carbon::createFromTimestamp($request->input("start"));
        $slot->end = Carbon::createFromTimestamp($request->input("end"));
        $slot->slot_duration = $request->input("slot_duration");
        $slot->slot_breaks = $request->input("slot_breaks");
        // $slot->cloudid = $request->input("cloudid");
        $slot->save();

        return parent::createJsonResponse("created",false,200, ["slot" => $slot]);

    }

    public function deleteBookingSlot($booking_slot_id, Request $request)
    {
        $slot = BookingSlot::findOrFail($booking_slot_id)->delete();

        return parent::createJsonResponse("deleted",false,200);

    }
}
