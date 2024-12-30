<?php

namespace App\Jobs;

use App\Models\UserSectionSetting;
use App\PermissionConstants;
use App\Section;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushNotificationSectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $section;

    private $title = "";
    private $body = "";

    private $permission = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Section $section, $title, $body, $permission = null)
    {
        $this->section = $section;
        $this->title = $title;
        $this->body = $body;
        $this->permission = $permission;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $group = $this->section->group;
        $sectionUserSectionSettings = UserSectionSetting::where('section_id','=',$this->section->id)->get();
        foreach ($group->members() as $member)
        {
            $record = $sectionUserSectionSettings->filter( function($ele) use($member) { return $ele->cloud_id == $member->id; })->first();
            $notificationDisabled = $record? !!$record->notificationDisabled : false;

            if (!$notificationDisabled && $this->section->isAllowed($member, $this->permission)) {
                PushNotificationSingleJob::dispatch($member,$this->title, $this->body);
            }
        }
    }
}
