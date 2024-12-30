<?php

namespace App;

use App\Http\Controllers\API\V1\xAPI\HasxAPIStatments;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BeitragComment extends Model
{
    use HasxAPIStatments;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName("beitrag");
    }

    protected $with = ['author'];

    public function author()
    {
        return $this->belongsTo('App\CloudID','cloudid');
    }

    function getObjectType()
    {
        return "announcement/comment";
    }
}
