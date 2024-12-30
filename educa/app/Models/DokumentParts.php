<?php

namespace App\Models;

use App\Dokument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumentParts extends Model
{
    use HasFactory;

    public function document()
    {
        return $this->belongsTo(Dokument::class,"dokument_id");
    }
}
