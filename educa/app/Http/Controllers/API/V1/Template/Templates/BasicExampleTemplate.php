<?php

namespace App\Http\Controllers\API\V1\Template\Templates;

class BasicExampleTemplate extends AbstractTemplate
{

    function documentName()
    {
        return "sample";
    }

    function templateFile()
    {
        return "examples.docx";
    }

    function executeTemplateGeneration($templateProcessor, $model)
    {
        // nothing
    }
}
