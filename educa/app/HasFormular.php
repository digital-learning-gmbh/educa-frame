<?php


namespace App;


interface HasFormular
{
    public function getFormulare();
    public function getLatestFormulaDataFor($formula);
    public function getFormulaDataFor($revision);
    public function saveFormulaDataFor($revision, $data);
}
