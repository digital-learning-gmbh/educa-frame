<?php

namespace App\Imports;

use App\Fach;
use App\Lehrplan;
use App\LehrplanEinheit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ModulImport implements ToModel, WithHeadingRow
{

    private $lehrplan = null;
    private $importedRows = 0;

    public function __construct($lehrplan)
    {
        $this->lehrplan = $lehrplan;
    }

    public function headingRow() : int
    {
        return 1;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $modul = new LehrplanEinheit;
        $modul->lehrplan_id = $this->lehrplan->id;
        $modul->name = is_numeric(substr($row["themengebiet"], 0, 1)) ? substr($row["themengebiet"], 4) : $row["themengebiet"]; //etwaige Nummerierung abschneiden

        if(is_numeric($row["soll_u_einheitenfach"]))
        {
            $modul->anzahl = $row["soll_u_einheitenfach"];
        }
        else
        {
            $modul->anzahl = 0;
        }

        //Gibt es das Fach schon?
        $fach = Fach::where("name", "=", $row["name_des_faches"])->where('schule_id','=', $this->lehrplan->schule_id);
        if($fach->count() > 0)
        {
            $fach = $fach->first();
        }
        else //Fach erstellen
        {
            $fach = new Fach;
            $fach->name = $row["name_des_faches"];
            $fach->schule_id = $this->lehrplan->schule_id;
            $fach->save();
        }

        $modul->fach_id = $fach->id;
        $modul->save();

        $this->importedRows++;
        return $modul;
    }

    public function getImportedRows() : int
    {
        return $this->importedRows;
    }
}
