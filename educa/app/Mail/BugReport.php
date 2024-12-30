<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BugReport extends Mailable
{
    use Queueable, SerializesModels;

    private $text;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $text)
    {
        $this->text = $text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.bugreport',["text" => $this->text]);
    }
}
