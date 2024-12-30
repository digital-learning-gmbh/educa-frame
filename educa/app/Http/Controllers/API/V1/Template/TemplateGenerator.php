<?php

namespace App\Http\Controllers\API\V1\Template;

class TemplateGenerator
{
    private $model;
    private $templateClass;

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function setTemplateClass(TemplateClass $templateClass)
    {
        $this->templateClass = $templateClass;
    }

    public function generate($type = "docx")
    {
        $domPdfPath = base_path( 'vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        $this->templateClass->setModel($this->model);
        return $this->templateClass->generate($type);
    }
}
