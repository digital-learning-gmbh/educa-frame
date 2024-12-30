<?php

namespace App\Jobs;

use App\ContactHistoryEntry;
use App\Mail\CorrespondenceMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected ContactHistoryEntry $entry;

    public $tries = 2; // two tries

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, ContactHistoryEntry $entry)
    {
        $this->email = $email;
        $this->entry = $entry;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new CorrespondenceMail($this->entry));
    }
}
