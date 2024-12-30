<?php

namespace App\Jobs;

use App\CloudID;
use App\Http\Controllers\API\V1\Push\PushNotificationsController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushNotificationBackgroundJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $members = [];
    private $title = "";
    private $body = "";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($members, $title, $body)
    {
        $this->members = $members;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->members as $member) {
            if($member instanceof CloudID)
                PushNotificationSingleJob::dispatch($member,$this->title,$this->body);
        }
    }
}
