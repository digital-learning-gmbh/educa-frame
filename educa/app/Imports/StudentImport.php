<?php

namespace App\Imports;

use App\AdditionalInfo;
use App\Klasse;
use App\Merkmal;
use App\Schuler;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use StuPla\CloudSDK\formular\models\Formular;

class StudentImport implements ToModel, WithHeadingRow
{
    private $importedRows = 0;
    private $school = null;
    private $wahl_prefix = "";
    private $school_year = null;

    public function __construct($school, $schoolyear, $prefix)
    {
        $this->school = $school;
        $this->school_year = $schoolyear;
        $this->wahl_prefix = $prefix;
    }

    //Heading-Row ist Zeile 2, da in Zeile 1 der Timestamp des Exports steht
    //Zählen beginnt hier genialerweise bei 1
    public function headingRow() : int
    {
        return 2;
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

        $student = new Schuler();
        $infos = new AdditionalInfo();

        $infos->anrede = strtolower($row["anrede"]);
        $infos->birthdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["geburtsdatum"]);
        $infos->birthplace = $row["geburtsort"];
        $infos->birthname = $row["geburtsname"];
        $infos->nationalitaet = $row["staatsangehoerigkeit"];
        $infos->tel_private = $row["telefonnummer"];
        $infos->mobile = $row["mobilnummer"];
        $infos->email = $row["fuu_e_mail"];
        $infos->email_private = $row["e_mail"];
        $infos->street = $row["strasse"];
        $infos->plz = $row["plz"];
        $infos->city = $row["ort"];
        $infos->country = $row["land"];

        $religion = "";
        if($row["evangelisch"] == 1)
            $religion = "Evangelisch";
        elseif($row["katholisch"] == 1)
            $religion = "Katholisch";
        elseif($row["andere_konfession"] == 1)
            $religion = "Andere Konfession";
        elseif($row["ohne_konfession"] == 1)
            $religion = "Ohne Konfession";

        $infos->religion = $religion;

        $infos->save();

        $student->firstname = $row["vorname"];
        $student->lastname = $row["nachname"];
        $student->info_id = $infos->id;
        $student->save();

        DB::table("schuler_schule")->insert([
            "schule_id" => $this->school->id,
            "schuler_id" => $student->id
        ]);

//        if($row["notizen"] != "")
//        {
//            $notizen = new Merkmal();
//            $notizen->model_type = "schuler";
//            $notizen->model_id = $student->id;
//            $notizen->technical = false;
//            $notizen->key = "Notizen";
//            $notizen->value = $row["notizen"];
//            $notizen->save();
//        }
//
//        if($row["telefon_vater"] != "")
//        {
//            $telvater = new Merkmal();
//            $telvater->model_type = "schuler";
//            $telvater->model_id = $student->id;
//            $telvater->technical = false;
//            $telvater->key = "Telefon Vater";
//            $telvater->value = $row["telefon_vater"];
//            $telvater->save();
//        }
//
//        if($row["mobil_vater"] != "")
//        {
//            $mobilvater = new Merkmal();
//            $mobilvater->model_type = "schuler";
//            $mobilvater->model_id = $student->id;
//            $mobilvater->technical = false;
//            $mobilvater->key = "Mobil Vater";
//            $mobilvater->value = $row["mobil_vater"];
//            $mobilvater->save();
//        }
//
//        if($row["e_mail_vater"] != "")
//        {
//            $emailvater = new Merkmal();
//            $emailvater->model_type = "schuler";
//            $emailvater->model_id = $student->id;
//            $emailvater->technical = false;
//            $emailvater->key = "E-Mail Vater";
//            $emailvater->value = $row["e_mail_vater"];
//            $emailvater->save();
//        }
//
//        if($row["telefon_mutter"] != "")
//        {
//            $telmutter = new Merkmal();
//            $telmutter->model_type = "schuler";
//            $telmutter->model_id = $student->id;
//            $telmutter->technical = false;
//            $telmutter->key = "Telefon Mutter";
//            $telmutter->value = $row["telefon_mutter"];
//            $telmutter->save();
//        }
//
//        if($row["mobil_mutter"] != "")
//        {
//            $mobilmutter = new Merkmal();
//            $mobilmutter->model_type = "schuler";
//            $mobilmutter->model_id = $student->id;
//            $mobilmutter->technical = false;
//            $mobilmutter->key = "Mobil Mutter";
//            $mobilmutter->value = $row["mobil_mutter"];
//            $mobilmutter->save();
//        }
//
//        if($row["e_mail_mutter"] != "")
//        {
//            $emailmutter = new Merkmal();
//            $emailmutter->model_type = "schuler";
//            $emailmutter->model_id = $student->id;
//            $emailmutter->technical = false;
//            $emailmutter->key = "E-Mail Mutter";
//            $emailmutter->value = $row["e_mail_mutter"];
//            $emailmutter->save();
//        }

        // try to find main class

        if(array_key_exists("kurs",$row) && $row["kurs"] != null)
        {
            $klasse = Klasse::where('name','=', $row["kurs"])->first();

            $from =\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["kurs_von"])->format('Y-m-d');
            $until = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["kurs_bis"])->format('Y-m-d');

            if($klasse != null)
            {
                DB::table('klasse_schuler')->insert(
                    ['klasse_id' => $klasse->id, 'schuler_id' => $student->id, 'from' => $from, 'until' => $until]
                );
            }
        }

        // load Formulare
        $formulare = Formular::whereIn('id', explode(",", $this->school->getEinstellungen("formulare", "")))->get();
        foreach ($formulare as $formular)
        {
            $array = [];
            $lastRevision = $formular->last_revision;
            $items= json_decode($lastRevision->data);
            foreach ($items as $item){
                if(!property_exists($item, "label") || !property_exists($item, "name"))
                    continue;

                foreach ($row as $key=>$value)
                {

                    if($item->type == 'checkbox-group')
                    {
                        if($this->isSameColumn($key, $item->label))
                        {
                            // Yes / No Case
                            $checked =$this->getCheckBoxValue($value);
                            foreach ($item->values as $option) {

                                if ($this->isSameColumn("ja", $option->label) && $checked) {
                                    $array[] = ["name" => $item->name . "/" . $option->value, "value" => $option->value];
                                }

                                if ($this->isSameColumn("nein", $option->label) && !$checked) {
                                    $array[] = ["name" => $item->name . "/" . $option->value, "value" => $option->value];
                                }
                            }
                        } else {
                            foreach ($item->values as $option) {
                                if ($this->isSameColumn($key, $option->label)) {
                                    if ($this->getCheckBoxValue($value)) {
                                        $array[] = ["name" => $item->name . "/" . $option->value, "value" => $option->value];
                                    }
                                }
                            }
                        }
                    } else if($item->type == 'radio-group' || $item->type == 'select')
                    {
                        if($this->isSameColumn($key, $item->label))
                        {
                            // Yes / No Case
                            $checked =$this->getCheckBoxValue($value);
                            foreach ($item->values as $option) {

                                if ($this->isSameColumn("ja", $option->label) && $checked) {
                                    $array[] = ["name" => $item->name, "value" => $option->value];
                                }

                                if ($this->isSameColumn("nein", $option->label) && !$checked) {
                                    $array[] = ["name" => $item->name, "value" => $option->value];
                                }
                            }
                        } else {
                            foreach ($item->values as $option) {
                                if ($this->isSameColumn($key, $option->label)) {
                                    if ($this->getCheckBoxValue($value)) {
                                        $array[] = ["name" => $item->name, "value" => $option->value];
                                    }
                                }
                            }
                        }
                    } else {
                        // Default
                        if($this->isSameColumn($key, $item->label))
                        {
                            $array[] = ["name" => $item->name, "value" => $value];
                        }
                    }
                }
            }
            $student->saveFormulaDataFor($lastRevision->id, json_encode($array));
        }

        // try to find Wahlpflichtklassen
        foreach ($row as $key=>$value)
        {
            $w = $this->getCheckBoxValue($value);
            $klasse = Klasse::whereRaw('LOWER(name) LIKE ?', [trim($this->wahl_prefix)." ".$key])->where('schuljahr_id', '=', $this->school_year->id)->first();
            if($w && $klasse != null) {
                $until = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["kurs_bis"])->format('Y-m-d');
                DB::table('klasse_schuler')->insert(
                    ['klasse_id' => $klasse->id, 'schuler_id' => $student->id, 'from' => date("Y-m-d"), 'until' => $until]
                );
            }
        }

        $this->importedRows++;

        return $student;
    }

    public function getImportedRows() : int
    {
        return $this->importedRows;
    }


    private function getCheckBoxValue($value)
    {
        if($value == null || trim($value) == "" || trim($value) == "0" || $value == 0)
            return false;
        return true;
    }

    private function isSameColumn($a, $b)
    {
        if(trim(strtolower($a)) == trim(strtolower($b)) ||
            trim(strtolower(str_replace(" ", "_",$a))) == trim(strtolower(str_replace(" ", "_",$b)))
            || trim(str_slug($a)) == trim(str_slug($b)))
            return true;
        return false;
    }
}
