<?php

namespace App\Models;

use App\CloudID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionToken extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(CloudID::class,"cloudid");
    }
}
