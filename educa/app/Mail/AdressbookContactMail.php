<?php

namespace App\Mail;

use App\CloudID;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdressbookContactMail extends Mailable
{
    use Queueable, SerializesModels;

    private $subject_param;
    private $cloudId;
    private $isMailAnonymized;
    private $message;
    private $name;


    private $file1;
    private $file2;
    private $file3;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CloudID $sender,$isMailAnonymized, $subject, $message, $name, $file1, $file2, $file3)
    {
        $this->cloudId = $sender;
        $this->subject_param = $subject;
        $this->isMailAnonymized = $isMailAnonymized;
        $this->message = $message;
        $this->name = $name;
        $this->file1 = $file1;
        $this->file2 = $file2;
        $this->file3 = $file3;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->subject("educa - ".$this->cloudId->name.": ".$this->subject_param);

        if(filter_var($this->cloudId->email, FILTER_VALIDATE_EMAIL) !== false && !$this->isMailAnonymized ) {
            $this->replyTo($this->cloudId->email);
        }

        $hasFile = false;
        if($this->file1 != null)
        {
            $email = $email->attach($this->file1->getRealPath(), array(
                    'as' => $this->file1->getClientOriginalName(), // If you want you can chnage original name to custom name
                    'mime' => $this->file1->getMimeType())
            );
            $hasFile = true;
        }

        if($this->file2 != null)
        {
            $email = $email->attach($this->file2->getRealPath(), array(
                    'as' => $this->file2->getClientOriginalName(), // If you want you can chnage original name to custom name
                    'mime' => $this->file2->getMimeType())
            );
            $hasFile = true;
        }

        if($this->file3 != null)
        {
            $email = $email->attach($this->file3->getRealPath(), array(
                    'as' => $this->file3->getClientOriginalName(), // If you want you can chnage original name to custom name
                    'mime' => $this->file3->getMimeType())
            );
            $hasFile = true;
        }

        return $email->view('emails.adressbook_contact',["hasFile" => $hasFile,
            "isMailAnonymized" => $this->isMailAnonymized,
            "text_message" => $this->message,
            "cloudId" => $this->cloudId,
            "name" => $this->name,
            "subject" => $this->subject_param]);


    }
}
