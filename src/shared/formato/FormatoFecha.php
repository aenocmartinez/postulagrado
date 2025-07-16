<?php

namespace Src\shared\formato;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FormatoFecha 
{
    public static function ConvertirStringAObjetoCarbon(?string $fecha): ?Carbon {

        if (empty($fecha)) {
            return null;
        }
    
        try {
            return Carbon::parse($fecha);
        } catch (\Exception $e) {
            Log::error("Error al convertir string a Carbon: " . $e->getMessage());
            return null;
        }
    }    

    public static function formatearFechaLarga(?string $fecha): ?string {
        $carbon = self::ConvertirStringAObjetoCarbon($fecha);

        if (!$carbon) {
            return null;
        }
        $carbon->locale('es');

        return $carbon->translatedFormat('d \d\e F \d\e Y');
    }    

    public static function formatearFechaCorta(?string $fecha): ?string {
        $carbon = self::ConvertirStringAObjetoCarbon($fecha);

        if (!$carbon) {
            return null;
        }

        return $carbon->format('d/m/Y');
    }    
}