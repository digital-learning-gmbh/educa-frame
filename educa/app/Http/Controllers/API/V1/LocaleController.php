<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\ApiController;

class LocaleController extends ApiController
{
    public static $translationMap;

    public function getLocales()
    {
        $translation = self::getTranslationMap();
        return parent::createJsonResponse("locales",false, 200, [
            "translation" => $translation,
            "languages" => config('educa.languages')
        ]);
    }

    public static function getTranslationMap($force = false)
    {
        if(self::$translationMap == null || $force)
        {
            self::$translationMap = json_decode(file_get_contents(base_path("resources/lang/locale.json")));
        }
        return self::$translationMap;
    }

    public static function translate($key, $defaultValue)
    {

    }
}
