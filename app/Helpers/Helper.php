<?php

namespace App\Helpers;


class Helper{

    public static function normalizeString($string)
    {
        $string = mb_strtolower($string, 'UTF-8');
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);
        $string = preg_replace('/\s+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }

}
