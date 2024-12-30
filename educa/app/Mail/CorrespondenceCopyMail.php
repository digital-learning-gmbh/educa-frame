<?php

namespace App\Mail;

use App\ContactHistoryEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorrespondenceCopyMail extends Mailable
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
        $this->subject("Kopie der educa-Nachricht: ".$this->correspondez->subject);
        return $this->view('emails.correspondence_copy',["correspondez" => $this->correspondez]);
    }
}
