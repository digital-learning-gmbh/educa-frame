<?php

namespace App\Imports;

use App\Fach;
use App\Lehrer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class A5SubjectTeacher implements WithHeadingRow,ToModel
{

    public function model(array $collection)
    {
        $teacher = Lehrer::where("firstname",'=',trim($collection["vorname"]))
            ->where("lastname",'=',trim($collection["nachname"]))
            ->first();

        $fach = Fach::where('lecture_number','=',trim($collection["fach_nr"]))->first();

        if($teacher != null && $fach != null)
        {
            DB::table('lehrer_fach')->insert(
                [
                   'lehrer_id' => $teacher->id,
                   'fach_id' => $fach->id
                ]);
        }

    }
}
