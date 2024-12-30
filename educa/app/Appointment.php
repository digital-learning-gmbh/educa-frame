<?php

namespace App;

use App\Http\Controllers\API\ApiController;
use App\Models\Interfaces\IsAppointment;
use App\Models\SingleAppointment;
use App\Observers\FeedObserver;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use StuPla\CloudSDK\calendarful\Event\RecurrentEventInterface;

class Appointment extends Model implements HasDocuments, HasMeetings, RecurrentEventInterface, IsAppointment
{
    use \Spatie\Tags\HasTags;


    protected $appends = ['state', 'documentCount'];

    private $moderatorPasswort = "testdemo232";
    private $teilnehmerPasswort = "testdemo232";

    public static $FEED_INVITE = "appointment.invite";
    public static $FEED_DELETED = "appointment.deleted";
    public static $FEED_UPDATED = "appointment.update";
    public static $FEED_SINGLE_REMOVED = "appointment.single.removed";
    public static $FEED_SINGLE_MOVE = "appointment.single.moved";
    public static $FEED_SINGLE_DELETE = "appointment.single.delete";

    public function getAttendeesAttribute()
    {
        $ids = DB::table('appointment_cloud_i_d')->where('appointment_id', '=', $this->id)->where('level', '=', 0)->pluck('cloudid');
        return CloudID::find($ids);
    }

    public function getOrganisatorsAttribute()
    {
        $ids = DB::table('appointment_cloud_i_d')->where('appointment_id', '=', $this->id)->where('level', '=', 100)->pluck('cloudid');
        return CloudID::find($ids);
    }

    public function getStateAttribute()
    {
        if (ApiController::user() == null)
            return "-1";

        $status = DB::table('appointment_cloud_i_d')->where('appointment_id', '=', $this->id)->where('cloudid', '=', ApiController::user()->id)->first();
        if ($status == null)
            return "1";

        return $status->status;
    }

    public function sections()
    {
        return $this->belongsToMany('App\Section', "appointment_section", "appointment_id", "section_id");
    }

    public function dokumente($parent_id = null)
    {
        $ids = DB::table('model_dokument')
            ->where('model_id', '=', $this->id)
            ->where('model_type', '=', 'appointment')
            ->pluck('dokument_id')->toArray();
        if ($parent_id == null) {
            return Dokument::find($ids);
        }
        return Dokument::where('parent_id', '=', '0')->whereIn('id', $ids)->get();
    }

    public function getDocumentCountAttribute()
    {
        return $this->dokumente()->count();
    }

    public function isCreated()
    {
        $this->setEnvBBBServer();
        $bbb = new BigBlueButton();
        $getMeetingInfoParams = new GetMeetingInfoParameters($this->id, $this->moderatorPasswort);
        $response = $bbb->getMeetingInfo($getMeetingInfoParams);
        if ($response->getReturnCode() == 'FAILED') {
            // meeting not found or already closed
            return false;
        }
        return true;
    }

    public function setEnvBBBServer()
    {
        putenv("BBB_SECRET=7YJ51P4H1vyHIed1Trx4nGVy5jaWkm8QnJKeuPRtLk");
        putenv("BBB_SERVER_BASE_URL=https://konferenz.educa-portal.de/bigbluebutton/");
    }

    public function joinBBB($user, $name = "Teilnehmer")
    {
        $this->setEnvBBBServer();
        if (!$this->isCreated()) {
            $this->createAtBigBlueButton();
        }
        $bbb = new BigBlueButton();
        $passwort = $this->teilnehmerPasswort;
        if ($user != null) {
            if ($user->id == $this->user_id || $user->type == "mitarbeiter" || $user->type == "superadmin" || $user->type == "admin") {
                $passwort = $this->moderatorPasswort;
            }
            $name = $user->displayName;
        }
        $joinMeetingParams = new JoinMeetingParameters($this->id, $name, $passwort);
        $joinMeetingParams->setRedirect(true);
        $url = $bbb->getJoinMeetingURL($joinMeetingParams);
        return $url;
    }

    public function createAtBigBlueButton()
    {
        $this->setEnvBBBServer();

        $bbb = new BigBlueButton();

        $createMeetingParams = new CreateMeetingParameters($this->id, $this->title);
        $createMeetingParams->setAttendeePassword($this->teilnehmerPasswort);
        $createMeetingParams->setModeratorPassword($this->moderatorPasswort);
        $createMeetingParams->setWelcomeMessage("");
        $createMeetingParams->setLogoutUrl("https://www.schule-plus.com");
        // $createMeetingParams->setEndCallbackUrl("");
        $createMeetingParams->setRecord(true);
        $createMeetingParams->setAllowStartStopRecording(true);
        $createMeetingParams->setAutoStartRecording(false);

        $response = $bbb->createMeeting($createMeetingParams);
        if ($response->getReturnCode() == 'FAILED') {
            $this->delete();
            return 'Can\'t create room! please contact our administrator.';
        }
        return "Meeting created";
    }

    public function setStatus($cloudId, $status)
    {
        \Illuminate\Support\Facades\DB::table('appointment_cloud_i_d')->where([
            'appointment_id' => $this->id,
            'cloudid' => $cloudId,
        ])->update(["status" => $status]);
    }

    public function addTeilnehmer($cloudId, $status, $level = 0)
    {
        $this->addTeilnehmerById($cloudId->id, $status, $level);
    }

    public function addTeilnehmerById($cloudId, $status, $level = 0)
    {
        if (!$this->isTeilnehmerInvited($cloudId)) {
            \Illuminate\Support\Facades\DB::table('appointment_cloud_i_d')->insert([
                'appointment_id' => $this->id,
                'cloudid' => $cloudId,
                'status' => $status,
                'level' => $level
            ]);
            if ($level < 100) {
                FeedObserver::addUserAcitivty($cloudId, Auth::user(), "cloud", self::$FEED_INVITE, $this->id, $this);
            }
        }
    }

    public function addSection($group)
    {
        $this->addSectionById($group->id);
    }

    public function addSectionById($groupId)
    {
        if (!$this->isSectionInvited($groupId)) {
            \Illuminate\Support\Facades\DB::table('appointment_section')->insert([
                'appointment_id' => $this->id,
                'section_id' => $groupId,
            ]);
            FeedObserver::addSectionActivity($groupId, Auth::user(), "App\CloudID", self::$FEED_INVITE, $this->id, $this);
        }
    }

    public function isSectionInvited($groupId)
    {
        return \Illuminate\Support\Facades\DB::table('appointment_section')->where([
            'appointment_id' => $this->id,
            'section_id' => $groupId,
        ])->exists();
    }

    public function isTeilnehmerInvited($cloudId)
    {
        return \Illuminate\Support\Facades\DB::table('appointment_cloud_i_d')->where([
            'appointment_id' => $this->id,
            'cloudid' => $cloudId,
        ])->exists();
    }

    public function delete()
    {
        foreach ($this->attendees as $attendee) {
            FeedObserver::addUserAcitivty($attendee->id, $this->creator, "App\CloudID", self::$FEED_DELETED, $this->id, $this);
        }
        foreach ($this->organisators as $attendee) {
            FeedObserver::addUserAcitivty($attendee->id, $this->creator, "App\CloudID", self::$FEED_DELETED, $this->id, $this);
        }
        \Illuminate\Support\Facades\DB::table('appointment_rooms')->where('appointment_id', '=', $this->id)->delete();
        \Illuminate\Support\Facades\DB::table('appointment_cloud_i_d')->where('appointment_id', '=', $this->id)->delete();
        \Illuminate\Support\Facades\DB::table('appointment_section')->where('appointment_id', '=', $this->id)->delete();
        foreach ($this->dokumente() as $document) $document->delete();

        // delete single appointments
        $singleAppoints = SingleAppointment::where("appointment_id","=",$this->id)->get();
        foreach ($singleAppoints as $singleAppoint)
        {
            $singleAppoint->delete();
        }

        return parent::delete();
    }

    public function notifiyFeed(Dokument $dokument)
    {
        $user = $dokument->creator;
        $id = ($user == null) ? "" : $user->id;
        foreach ($this->getAttendeesAttribute() as $attendee) {
            FeedObserver::addUserAcitivty($attendee->id, $dokument->creator, "App\CloudID", Dokument::$FEED_INFO, $this->id . "_" . $id, $dokument);
        }
        foreach ($this->getOrganisatorsAttribute() as $attendee) {
            FeedObserver::addUserAcitivty($attendee->id, $dokument->creator, "App\CloudID", Dokument::$FEED_INFO, $this->id . "_" . $id, $dokument);
        }
        foreach ($this->sections as $section) {
            FeedObserver::addSectionActivity($section->id, $dokument->creator, "App\CloudID", Dokument::$FEED_INFO, $this->id . "_" . $id, $dokument);
        }
    }

    public function checkRights(Dokument $dokument, $cloudid, $type = "view"): bool
    {
        if ($type == "view")
            return true;
        if (DB::table('appointment_cloud_i_d')->where('appointment_id', '=', $this->id)->where('level', '=', 100)->where('cloudid', '=', $cloudid->id)->exists())
            return true;
        return false;
    }

    public function checkMeetingRights(CloudID $user): bool
    {
        // checkt, ob user level 100 (Organisator)
        return DB::table('appointment_cloud_i_d')
            ->where('appointment_id', '=', $this->id)
            ->where('level', '=', 100)
            ->where('cloudid', '=', $user->id)
            ->exists();
    }

    public function rooms()
    {
        return $this->belongsToMany("App\Raum", "appointment_rooms", "appointment_id", "room_id");
    }

    public function name()
    {
        return $this->title;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getColor(){
        return $this->color;
    }

    public function getEventClass(){
        return $this->eventClass;
    }

    public function setEventClass($eventClass){
        $this->eventClass = $eventClass;
    }

    public function getStartDate()
    {
        if($this->startDateCache == null)
        {
            $this->startDateCache = new \DateTime($this->startDate);
        }
        return $this->startDateCache;
    }

    public function setStartDate(\DateTime $startDate)
    {
        $this->startDateCache = $startDate;
    }

    public function getEndDate()
    {
        if($this->endDateCache == null)
        {
            $this->endDateCache =  new \DateTime($this->endDate);
        }
        return  $this->endDateCache;
    }

    public function setEndDate(\DateTime $endDate)
    {
        $this->endDateCache = $endDate;
    }

    public function getDuration()
    {
        return $this->getStartDate()->diff($this->getEndDate());
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function getRecurrenceType()
    {
        return $this->recurrenceType;
    }

    public function getRecurrenceStart()
    {
        return $this->reccurence_start;
    }

    public function setRecurrenceStart($reccurenceStart = null)
    {
        $this->reccurence_start = $reccurenceStart;
    }

    /**
     * Set the recurrence type of the event.
     *
     * @param string $type
     */
    public function setRecurrenceType($type = null)
    {
        if ($type === null) {
            $this->recurrenceUntil = null;
        }
        $this->recurrenceType = $type;
    }

    /**
     * Get the until date of the event.
     *
     * @return \DateTime
     */
    public function getRecurrenceUntil()
    {
        return new \DateTime($this->recurrenceUntil);
    }

    public function getOccurrenceDate()
    {
        if($this->occurrenceDate == null)
        {
            return null;
        }

        if($this->occurrenceDateCache == null)
        {
            $this->occurrenceDateCache = new \DateTime($this->occurrenceDate);
        }
        return $this->occurrenceDateCache;
    }

    public function getRecurrenceTurnus()
    {
        if ($this->recurrenceTurnus == null)
            return 1;
        return $this->recurrenceTurnus;
    }

    public function getSingleEventAtDate($occurrenceDate, $fakeIfEmpty = false)
    {
        if($occurrenceDate == null)
            return null;

        $singleAppointment = SingleAppointment::where("appointment_id","=",$this->id)
            ->where("occurrenceDate","=",Carbon::createFromTimestamp($occurrenceDate))
            ->first();
        if($singleAppointment != null)
            return $singleAppointment;

        if(!$fakeIfEmpty)
            return null;

        $singleAppointment = new SingleAppointment;
        $singleAppointment->title = $this->title;
        $singleAppointment->startDate = Carbon::createFromTimestamp($occurrenceDate);
        $singleAppointment->endDate = Carbon::createFromTimestamp($occurrenceDate)->add($this->getDuration());
        $singleAppointment->location = $this->location;
        $singleAppointment->description = $this->description;
        $singleAppointment->protocol = $this->protocol;
        $singleAppointment->color = $this->color;
        $singleAppointment->external_identifier = $this->external_identifier;
        $singleAppointment->remember_minutes = $this->remember_minutes;
        $singleAppointment->remember_sent = $this->remember_sent;
        $singleAppointment->occurrenceDate = Carbon::createFromTimestamp($occurrenceDate);
        $singleAppointment->display = $this->display;
        $singleAppointment->exception_type = "move";
        $singleAppointment->appointment_id = $this->id;
        return $singleAppointment;
    }

    public function welcomeText()
    {
        return "";
    }
}
