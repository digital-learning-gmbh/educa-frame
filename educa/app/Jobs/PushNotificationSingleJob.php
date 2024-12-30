<?php

namespace App\Jobs;

use App\CloudID;
use App\Http\Controllers\API\V1\Push\PushNotificationsController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushNotificationSingleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cloudID = null;
    private $title = "";
    private $body = "";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CloudID $cloudID, $title, $body)
    {
        $this->cloudID = $cloudID;
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
        if($this->cloudID != null)
            PushNotificationsController::pushToCloudUser($this->cloudID,$this->title,html_entity_decode(strip_tags(str_replace("<"," <",$this->body))));
    }
}
