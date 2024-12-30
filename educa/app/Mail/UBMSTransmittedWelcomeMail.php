<?php

namespace App\Mail;

use App\FormularTemplate;
use App\Schuler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UBMSTransmittedWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    private $schuler;
    private $formularTemplate;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( Schuler $schuler, FormularTemplate  $formularTemplate)
    {
        $this->schuler = $schuler;
        $this->formularTemplate = $formularTemplate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.welcomeToSchool', ["schuler" => $this->schuler, "formularTemlate" => $this->formularTemplate]);
    }
}
