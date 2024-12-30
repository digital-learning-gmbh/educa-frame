<?php

namespace App\Http\Controllers\API\V1\Template\Templates;

use Illuminate\Support\Str;
use NcJoes\OfficeConverter\OfficeConverter;
use PhpOffice\PhpWord\TemplateProcessor;

abstract class AbstractTemplate implements \App\Http\Controllers\API\V1\Template\TemplateClass
{
    private $model;
    private $useLibreOffice = true;

    function setModel($model)
    {
        $this->model = $model;
    }

    function getModel()
    {
        return $this->model;
    }

    function generate($format = "docx")
    {
        $templateProcessor = new TemplateProcessor(base_path("transcript_templates/".$this->templateFile()));
        $processID = Str::random();
        $pathToSaveDocx = storage_path("app/transcripts/".$processID.".docx");

        $this->executeTemplateGeneration($templateProcessor,$this->model);

        $templateProcessor->saveAs($pathToSaveDocx);

        if($format == "pdf") {
            $pathToSavePdf = storage_path("app/transcripts/" . $processID . ".pdf");
            if ($this->useLibreOffice) {
                $converter = new OfficeConverter($pathToSaveDocx);
                $converter->convertTo( $processID . ".pdf");
            } else {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($pathToSaveDocx);
                $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
                $xmlWriter->save($pathToSavePdf);
            }
        }
        return $processID.".".$format;
    }

    abstract function templateFile();

    abstract function executeTemplateGeneration($templateProcessor, $model);
}
