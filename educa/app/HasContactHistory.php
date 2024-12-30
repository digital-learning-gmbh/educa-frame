<?php


namespace App;


interface HasContactHistory
{
    public function getDisplayNameAttribute();

    public function getEmails();
}
