<?php


namespace App;


interface HasMeetings
{
    public function checkMeetingRights(CloudID $user) : bool;
    public function name();

    public function welcomeText();
}
