<?php

namespace App\Observers;

use App\Beitrag;
use App\Http\Controllers\API\V1\Push\PushNotificationsController;
use App\SectionGroupApp;
use Illuminate\Support\Facades\Auth;

class BeitragObserver extends FeedObserver
{
    /**
     * Handle the beitrag "created" event.
     *
     * @param  \App\Beitrag  $beitrag
     * @return void
     */
    public function created(Beitrag $beitrag)
    {
        $section = SectionGroupApp::find($beitrag->app_id);
        if($section == null)
            return;
        $group = $section->section->group;
        if($beitrag->planned_for != null)
        {
            parent::addGroupActivity($group->id, $beitrag->author, "App\CloudID", "announcement.created", $beitrag->id, [
                "announcement" => $beitrag,
                "group" => $group
            ], $beitrag->planned_for);
            return;
        }
        parent::addGroupActivity($group->id, $beitrag->author, "App\CloudID", "announcement.created", $beitrag->id, [
            "announcement" => $beitrag,
            "group" => $group
        ]);
    }

    /**
     * Handle the beitrag "updated" event.
     *
     * @param  \App\Beitrag  $beitrag
     * @return void
     */
    public function updated(Beitrag $beitrag)
    {
        $section = SectionGroupApp::find($beitrag->app_id);
        if($section == null)
            return;
        $group = $section->section->group;
        if($beitrag->planned_for != null)
        {
            parent::addGroupActivity($group->id, $beitrag->author, "App\CloudID", "announcement.updated", $beitrag->id, [
                "announcement" => $beitrag,
                "group" => $group
            ], $beitrag->planned_for);
        }
        parent::addGroupActivity($group->id, $beitrag->author, "App\CloudID", "announcement.updated", $beitrag->id, [
            "announcement" => $beitrag,
            "group" => $group
        ]);
    }

    /**
     * Handle the beitrag "deleted" event.
     *
     * @param  \App\Beitrag  $beitrag
     * @return void
     */
    public function deleted(Beitrag $beitrag)
    {
        //
    }

    /**
     * Handle the beitrag "restored" event.
     *
     * @param  \App\Beitrag  $beitrag
     * @return void
     */
    public function restored(Beitrag $beitrag)
    {
        //
    }

    /**
     * Handle the beitrag "force deleted" event.
     *
     * @param  \App\Beitrag  $beitrag
     * @return void
     */
    public function forceDeleted(Beitrag $beitrag)
    {
        //
    }
}
