<?php

namespace App\Mail;

use App\ContactHistoryEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorrespondenceMail extends Mailable
{
    use Queueable, SerializesModels;

    private $correspondez;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ContactHistoryEntry  $correspondez)
    {
        $this->correspondez = $correspondez;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject("educa-Nachricht: ".$this->correspondez->subject);
        return $this->view('emails.correspondence',["correspondez" => $this->correspondez, "displayNames" => $this->correspondez->getSenderNames()]);
    }
}
