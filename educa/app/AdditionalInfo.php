<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AdditionalInfo extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    //for additional information about lehrers, schulers etc.

    protected $fillable = ['position', 'notes','personalnummer','title', 'anrede' , 'displayname' , 'personalnr' , 'street' , 'plz' , 'city' , 'tel_business', 'tel_private', 'tel_other' , 'mobile' , 'fax' , 'email', 'email_private', 'email_other', 'homepage' , 'birthdate' , 'birthplace' , 'birthname' , 'gender' , 'religion' , 'familienstand' , 'schulabschluss' , 'bundesland' , 'blz', 'bank', 'kontoinhaber' ,'kontonummer' , 'kontonr' , 'iban' , 'bic','nationalitaet'];
}
