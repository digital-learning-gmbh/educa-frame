<?php


namespace App;


interface HasDocuments
{
    public function notifiyFeed(Dokument $dokument);

    public function checkRights(Dokument $dokument, $cloudid, $type = "view") : bool;
}
