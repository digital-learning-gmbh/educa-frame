<?php

namespace App\Http\Controllers\API\V1;

use App\CloudID;
use App\Http\Controllers\API\ApiController;
use App\Mail\BugReport;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use ZammadAPIClient\Client;
use ZammadAPIClient\ResourceType;

class SupportController extends ApiController
{
    public function __construct()
    {
        if (config("zammad.enabled", false)) {
            $this->client = new Client([
                'url' => config('zammad.credentials.url'), // URL to your Zammad installation
                'username' => config('zammad.credentials.username'),  // Username to use for authentication
                'password' => config('zammad.credentials.password'),           // Password to use for authentication
            ]);
        }
    }

    public function createSupportTicket(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $img = $request->input("image");
        $text = $request->text;
        $log = $request->log;

        $supportTicket = new SupportTicket;
        $supportTicket->cloudid = $cloud_user->id;
        $supportTicket->status = "open";
        $supportTicket->title = "Supportanfrage vom " . Carbon::now()->format("d.m.Y");
        $supportTicket->body = $text;

        if (config("zammad.enabled", false)) {
            //if (rand(0, 1) == 0) {
            return $this->createZammadTicket($supportTicket, $request, $img, $text, $log, $cloud_user);
        } else {
            return $this->createMailSupportTicket($supportTicket, $request, $img, $text, $log, $cloud_user);
        }
    }

    private function createZammadTicket(SupportTicket $supportTicket, Request $request, $img, $text, $log, $cloud_user): \Illuminate\Http\Response
    {
        $zammadUser = $this->getOrCreateUserAtZammad($cloud_user);
        $ticket = $this->client->resource(ResourceType::TICKET);

        $ticket_data = [
            'customer_id' => $zammadUser->getID(),
            'group' => config("zammad.group"),
            'title' => "Supportanfrage über educa (" . $cloud_user->name . " / " . $cloud_user->email . ")",
            'article' => [
                'subject' => "Supportanfrage über educa (" . $cloud_user->name . " / " . $cloud_user->email . ")",
                'body' => $text . $log,
                'attachments' => []
            ],
        ];

        if ($request->input("fromApp") == "true") {
            if ($request->hasFile('image')) {
                foreach ($request->file("image") as $file) {
                    $data = file_get_contents($file->getPathname());
                    $ticket_data['article']['attachments'][] = [
                        'filename' => $file->getClientOriginalName(),
                        'data' => base64_encode($data),
                        'mime-type' => $file->getMimeType(),
                    ];
                }
            }

        } else {
            $ticket_data['article']['attachments'] = [];
            if($img != null && $img != "" && $img != "null") {
                $image = str_replace('data:image/jpeg;base64,', '', $img);
                $image = str_replace(' ', '+', $image);
                $ticket_data['article']['attachments'] = [
                    [
                        'filename' => 'screenshot.jpeg',
                        'data' => $image,
                        'mime-type' => 'image/jpeg',
                    ]
                ];
            }

            if ($request->hasFile('additionalFiles')) {
                foreach ($request->file("additionalFiles") as $file) {
                    $data = file_get_contents($file->getPathname());
                    $ticket_data['article']['attachments'][] = [
                        'filename' => $file->getClientOriginalName(),
                        'data' => base64_encode($data),
                        'mime-type' => $file->getMimeType(),
                    ];
                }
            }
        }

        $ticket->setValues($ticket_data);
        $ticket->save();

        if ($ticket->hasError()) {
            return parent::createJsonResponse("could not create ticket", true, 200, ["error" => $ticket->getError()]);
        }

        $supportTicket->channel = "zammad";
        $supportTicket->external_id = $ticket->getID();
        $supportTicket->is_answer_supported = true;

        $supportTicket->save();

        return parent::createJsonResponse("ok", false, 200, []);
    }

    private function getOrCreateUserAtZammad(CloudID $cloudID)
    {
        $users = $this->client->resource(ResourceType::USER)->search($cloudID->email);
        if (is_array($users) && count($users) > 0) {
            return $users[0];
        } else {
            $user_data = [
                'login' => $cloudID->email,
                'firstname' => $cloudID->name,
                'lastname' => "",
                'roles' => [
                    "Customer"
                ]
            ];

            $user = $this->client->resource(ResourceType::USER);
            $user->setValues($user_data);
            $user->save();
            if ($user->hasError()) {
                print_r($user->getError());
                die();
            }
            return $user;
        }
    }

    private function createMailSupportTicket(SupportTicket $supportTicket, Request $request, $img, $text, $log, $cloud_user): \Illuminate\Http\Response
    {
        $bugMail = new BugReport($text . $log);
        $bugMail->subject("Neue Supportanfrage über educa (" . $cloud_user->name . " / " . $cloud_user->email . ")");
        $mailAdress = "benjamin@schule-plus.com";
        $mail = Mail::to($mailAdress); // ""


        if ($request->input("fromApp") == "true") {
            if ($request->hasFile('image')) {
                foreach ($request->file("image") as $file) {
                    $bugMail->attach($file->getRealPath(), array(
                            'as' => $file->getClientOriginalName(),
                            'mime' => $file->getMimeType())
                    );
                }
            }
        } else {
            $image = str_replace('data:image/jpeg;base64,', '', $img);
            $image = str_replace(' ', '+', $image);
            $imageName = str_random(10) . '.' . 'jpeg';

            \File::put(storage_path() . '/app/' . $imageName, base64_decode($image));

            $bugMail->attachFromStorage($imageName);
        }

        $mail->send($bugMail);

        $supportTicket->channel = "email";
        $supportTicket->external_id = null;
        $supportTicket->is_answer_supported = false;

        $supportTicket->save();

        return parent::createJsonResponse("ok", false, 200, []);
    }

    public function listSupportTicket(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $tickets = SupportTicket::where("status", "=", "open")->where("cloudid", "=", $cloud_user->id)->orderByDesc("created_at")->get();

        return parent::createJsonResponse("ok", false, 200, ["tickets" => $tickets]);
    }

    public function getSupportTicket($id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $supportTicket = SupportTicket::findOrFail($id);

        $messages = [];

        if ($supportTicket->channel == "email") {
            $initalMessage = ["id" => "-1", "type" => "message", "sender" => "Ich", "title" => $supportTicket->title, "body" => $supportTicket->body, "created_at" => $supportTicket->created_at];
            $messages[] = $initalMessage;
            $initalMessage = ["id" => "-2", "type" => "note", "title" => "Hinweis", "body" => "Dieses Ticket wurde per E-Mail an den Support verschickt, eine weitere Interaktion ist nicht möglich", "created_at" => $supportTicket->created_at];
            $messages[] = $initalMessage;
        } else if ($supportTicket->channel == "zammad") {
            $messages = $this->getZammadMessages($supportTicket);
        }

        return parent::createJsonResponse("ok", false, 200, ["ticket" => $supportTicket, "messages" => $messages]);
    }

    private function getZammadMessages($supportTicket)
    {
        if ($supportTicket->channel != "zammad")
            return [];

        $zammadUser = $this->getZammadSystemUser();

        $ticket = $this->client->resource(ResourceType::TICKET)->get($supportTicket->external_id);
        if ($ticket->hasError()) {
            return [];
        }
        $ticket_articles = $ticket->getTicketArticles();
        $messages = [];
        foreach ($ticket_articles as $article) {
            $values = $article->getValues();
            if ($values["internal"])
                continue;
            $initalMessage = ["id" => $values["id"], "type" => "message", "sender" => $values["created_by_id"] == $zammadUser->getId() ? "Ich" : $values["from"], "title" => $values["subject"], "body" => $values["body"], "created_at" => $values["created_at"], "attachments" => $values["attachments"]];
            $messages[] = $initalMessage;
        }
        return $messages;
    }

    private function getZammadSystemUser()
    {
        $users = $this->client->resource(ResourceType::USER)->search(config('zammad.credentials.username'));
        if (is_array($users) && count($users) > 0) {
            return $users[0];
        }
        return null;
    }


    public function messageSupportTicket($id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $supportTicket = SupportTicket::findOrFail($id);

        // only zammad support
        if ($supportTicket->channel == "zammad") {
            $this->createArticleZammad($supportTicket, $request->input("msg"));
        }

        return $this->getSupportTicket($id, $request);
    }

    public function fileSupportTicket($id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $supportTicket = SupportTicket::findOrFail($id);

        // only zammad support
        if ($supportTicket->channel == "zammad") {
            $this->createArticleZammad($supportTicket, $request->input("msg") ?? "Neue Datei" , $request->file("attachment"));
        }

        return $this->getSupportTicket($id, $request);
    }

    private function createArticleZammad($supportTicket, $msg, $file = null)
    {
        $ticket = $this->client->resource(ResourceType::TICKET)->get($supportTicket->external_id);
        if ($ticket->hasError()) {
            return null;
        }
        $article = $this->client->resource(ResourceType::TICKET_ARTICLE);
        if ($file == null) {
            $article_data = [
                'ticket_id' => $ticket->getId(),
                'subject' => "",
                'type' => "note",
                'body' => $msg,
            ];
        } else {
            $data = file_get_contents($file->getPathname());
            $article_data = [
                'ticket_id' => $ticket->getId(),
                'subject' => "",
                'type' => "note",
                'body' => $msg,
                'attachments' => [
                    [
                        'filename' => $file->getClientOriginalName(),
                        'data' => base64_encode($data),
                        'mime-type' => $file->getClientMimeType(),
                    ]
                ]
            ];
        }


        $article->setValues($article_data);
        $article->save();

        if ($article->hasError()) {
            print_r($article->getError());
            return null;
        }
        return $article;

    }

    public function getAttachment(Request $request, $id, $article, $attachment) {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $supportTicket = SupportTicket::findOrFail($id);

        $article = $this->client->resource(ResourceType::TICKET_ARTICLE)->get($article);
        $attachmentObj = null;
        foreach ($article->getValues()["attachments"] as $attachmentdb)
        {
            if($attachmentdb["id"] == $attachment)
            {
                $attachmentObj = $attachmentdb;
            }
        }
        $content = $article->getAttachmentContent($attachment)->getContents();

        $headers = [
            'Content-type'        => $attachmentObj["preferences"]["Mime-Type"],
            'Content-Disposition' => 'attachment; filename="'.$attachmentObj["filename"].'"',
        ];
        return \Response::make($content, 200, $headers);

    }

    public function closeSupportTicket(Request $request, $id)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $supportTicket = SupportTicket::findOrFail($id);
        $supportTicket->status = "closed";
        $supportTicket->save();

        if($supportTicket->channel == "zammad")
        {
            $ticket = $this->client->resource(ResourceType::TICKET)->get($supportTicket->external_id);
            $ticket->setValue("state","closed");
            $ticket->save();

            if ($ticket->hasError()) {
                print_r($ticket->getError());
                return null;
            }
        }

        return $this->getSupportTicket($id,$request);
    }

    public function zammadWebhook(Request $request)
    {
       $supportTicket = SupportTicket::where("external_id","=",$request->input("ticket")["id"])->first();
       if($supportTicket != null && $supportTicket->status != "closed" &&  $request->input("ticket")["updated_by"]["email"] != "educa-support-bot@digitallearning.gmbh")
       {
           $additionalInformation =  $this->client->resource(ResourceType::TICKET)->get($supportTicket->external_id)->getValues();
           FeedObserver::addUserAcitivty($supportTicket->cloudid, null, null, SupportTicket::$FEED_UPDATED,$supportTicket->id, ["supportTicket" => $supportTicket, "additionalInformation" => $additionalInformation]);
           return parent::createJsonResponse("ticket updated", false, 200, ["supportTicket" => $supportTicket]);
       }

        return parent::createJsonResponse("ticket not found or closed", false, 200, []);
    }

    public function blockUser(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $cloudIdBlock = CloudID::find($request->input("cloudId"));

        DB::table('blocked_users')->updateOrInsert([
            "cloudid" => $cloudIdBlock->id,
            "by_cloudid" => $cloud_user->id
        ]);

        return parent::createJsonResponse("blocking done", false, 200, ["blocked_user" => $cloudIdBlock ]);
    }

    public function report(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if ($cloud_user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        $content = $request->input("content");

        return parent::createJsonResponse("content was reported", false, 200, ["content" => $content ]);
    }
}
