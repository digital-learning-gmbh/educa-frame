<?php


namespace App\Http\Controllers\API\V1\Feed\Cards;


use App\Http\Controllers\API\V1\Feed\Card;

class AnnouncementCard extends Card
{
    private $user;
    private $beitrag;
    private $group;

    /**
     * AnnouncementCard constructor.
     * @param $user
     * @param $beitrag
     */
    public function __construct($user, $beitrag, $group, $section)
    {
        $this->type = "announcementCard";
        $this->user = $user;
        $this->beitrag = $beitrag;
        $this->group = $group;
        $this->section = $section;
    }

    function createPayload()
    {
        return [ "user" => $this->user, "announcement" => $this->beitrag, "group" => $this->group, "section" => $this->section];
    }
}
