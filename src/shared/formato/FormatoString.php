<?php

namespace Src\shared\formato;

class FormatoString 
{
    public static function capital(string $texto): string {
        return mb_convert_case(trim($texto), MB_CASE_TITLE, "UTF-8");
    }    
}