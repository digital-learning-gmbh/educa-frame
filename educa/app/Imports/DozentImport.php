<?php

namespace App\Imports;

use App\AdditionalInfo;
use App\Fach;
use App\Klasse;
use App\Lehrer;
use App\Merkmal;
use App\Schuler;
use DateTime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use StuPla\CloudSDK\formular\models\Formular;

class DozentImport implements ToModel, WithHeadingRow
{
    private $fachLeadList = [
        "Referendarin mit den Fächern",
        "RS=",
        "BIG=",
        "RS+BIG="
    ];

    private $importedRows = 0;
    private $school = null;

    public function __construct($school)
    {
        $this->school = $school;
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
        //Manche Excel-Felder scheinen Wertzuweisungen zu enthalten
        //(z.B. Inhalt des PLZ-Feldes: ="74939" anstatt einfach nur 74939
        foreach($row as $key=>$value)
        {
            if(substr($value, 0, 1) == '=')
            {
                $row[$key] = substr($value, 2, strlen($value) - 3);
            }
        }

        $dozent = new Lehrer();

        $student = new Schuler();
        $infos = new AdditionalInfo();


        $infos->personalnummer = $row["personalnr"];
        $infos->anrede = strtolower($row["anrede"]);
        $infos->birthdate = new DateTime($row["geburtsdatum"]);
        $infos->street = $row["adresse"];
        $infos->plz = $row["plz"];
        $infos->city = $row["ort"];
        $infos->tel_private = $row["telefon_privat"];
        $infos->tel_business = $row["telefon_geschaeft"];
        $infos->mobile = $row["mobil"];
        $infos->email_private = $row["e_mail"];
        $infos->email = $row["fu_e_mail"];
        $infos->schulabschluss = $row["berufsqualifikation"];
        $infos->notes = $row["notizen"];

        if(str_contains($row["status"], "Dozent"))
        {
            $infos->position = "Dozent*in";
        }
        if(str_contains($row["status"], "Mitarbeiter"))
        {
            $infos->position = "Mitarbeiter*in";
        }
        if(str_contains($row["status"], "Schulassistent"))
        {
            $infos->position = "Schulassistent*in";
        }
        if(str_contains($row["status"], "Referendar/in"))
        {
            $infos->position = "Referendar*in";
        }
        if(str_contains($row["status"], "Praktikant/in"))
        {
            $infos->position = "Praktikant*in";
        }
        if(str_contains($row["status"], "Verwaltung"))
        {
            $infos->position = "Verwaltung";
        }

        $infos->save();

        $dozent->firstname = $row["vorname"];
        $dozent->lastname = $row["nachname"];
        $dozent->status = $row["inaktiv"] == "Nein" ? "active" : "inactive";
        $dozent->week_hours = $row["deputatsstunden_uewoche"];
        $dozent->info_id = $infos->id;
        $dozent->save();

        DB::table("lehrer_schule")->insert([
            "schule_id" => $this->school->id,
            "lehrer_id" => $dozent->id
        ]);

        // Fächer mappen.
        // wegen der anscheinend grenzenlosen verschiedenen Möglichkeiten des Altsystems
        // unmöglich, das hier perfekt zu implementieren
        foreach(preg_split('/(,|\+|\n)/', $row["faecherschwerpunkt"]) as $fach)
        {
            $fach = $this->removeLeading($this->fachLeadList, $fach);
            $fachfound = Fach::where("schule_id", "=", $this->school->id)->where("name", "LIKE", $fach)->first();

            if($fachfound != null && DB::table("lehrer_fach")->where("fach_id", "=", $fachfound->id)->where("lehrer_id", "=", $dozent->id)->count() == 0)
            {
                DB::table("lehrer_fach")->insert([
                    "fach_id" => $fachfound->id,
                    "lehrer_id" => $dozent->id
                ]);
            }
        }

        $this->importedRows++;

        return $student;
    }

    // Hilfsfunktion, um zumindest die meisten Beispieldaten map-bar zu machen (s.o.)
    private function removeLeading(array $leadList, string $inputString) : string
    {
        foreach($leadList as $leadString)
        {
            $inputString = str_replace($leadString, "", $inputString);
        }
        return trim($inputString);
    }

    public function getImportedRows() : int
    {
        return $this->importedRows;
    }
}
