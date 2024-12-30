<?php

namespace App\Observers;

use App\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;

class GroupObserver extends FeedObserver
{
    /**
     * Handle the group "created" event.
     *
     * @param  \App\Group  $group
     * @return void
     */
    public function created(Group $group)
    {
        // Create default avatar
        $avatar = new InitialAvatar();
        $image = $avatar->name($group->name)->size(400)->rounded(false)->generate();
        $name = str_random(32);
        Storage::disk('public')->put('/images/groups/'.$name.".png", $image->stream('png', 90));
        $group->image = $name;
        $group->save();
        //

        Log::info("group created ");
        parent::addGroupActivity($group->id, Auth::user(), "App\CloudID",  "group.created", $group->id, [
            "group" => $group
        ]);
    }

    /**
     * Handle the group "updated" event.
     *
     * @param  \App\Group  $group
     * @return void
     */
    public function updated(Group $group)
    {
        Log::info("group updated ");
        parent::addGroupActivity($group->id, Auth::user(), "App\CloudID",
            "group.updated", $group->id, [
                "group" => $group
        ]);
    }

    /**
     * Handle the group "deleted" event.
     *
     * @param  \App\Group  $group
     * @return void
     */
    public function deleted(Group $group)
    {
        Log::critical("group deleted ");
    }

    /**
     * Handle the group "restored" event.
     *
     * @param  \App\Group  $group
     * @return void
     */
    public function restored(Group $group)
    {
        Log::critical("group restored ");
    }

    /**
     * Handle the group "force deleted" event.
     *
     * @param  \App\Group  $group
     * @return void
     */
    public function forceDeleted(Group $group)
    {
        Log::critical("group forceDeleted ");
    }
}
