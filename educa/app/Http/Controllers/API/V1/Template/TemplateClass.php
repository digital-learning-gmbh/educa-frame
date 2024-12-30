<?php

namespace App\Http\Controllers\API\V1\Template;

interface TemplateClass
{

    function documentName();

    function setModel($model);

    function getModel();

    function generate($format = "docx"); // Should return a path with path, should support pdf, docx, html
}
