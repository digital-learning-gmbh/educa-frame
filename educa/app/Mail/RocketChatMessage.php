<?php

namespace App\Mail;

use App\CloudID;
use App\Providers\AppServiceProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RocketChatMessage extends Mailable
{
    use Queueable, SerializesModels;

    private $cloudID;
    private $title;
    private $content;

    private $tenant;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CloudID $cloudID,$title = "",$content = "", $tenant = null)
    {
        $this->cloudID = $cloudID;
        $this->title = $title;
        $this->content = $content;
        $this->tenant = $tenant ?? AppServiceProvider::getTenant();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $path = public_path("storage/images/tenants/" . $this->tenant->logo);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $this->subject($this->title)->view('emails.pushnotification',["title" => $this->title, "content" => $this->content, "tenant" => $this->tenant, "base64Logo" => $base64]);
    }
}
