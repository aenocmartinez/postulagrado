<?php

namespace Src\shared\formato;

class FormatoString 
{
    // public static function capital(string $texto): string {
    //     return mb_convert_case(trim($texto), MB_CASE_TITLE, "UTF-8");
    // }    
    public static function capital(string $texto): string {
        $excepciones = ['de', 'la', 'y', 'en', 'del', 'a', 'el', 'los', 'las', 'un', 'una', 'por', 'con'];
        
        $palabras = explode(' ', mb_strtolower(trim($texto), 'UTF-8'));

        foreach ($palabras as $i => $palabra) {
            // Siempre capitaliza la primera palabra o si no es una excepción
            if ($i === 0 || !in_array($palabra, $excepciones)) {
                $palabras[$i] = mb_convert_case($palabra, MB_CASE_TITLE, "UTF-8");
            }
        }

        return implode(' ', $palabras);
    }       
}