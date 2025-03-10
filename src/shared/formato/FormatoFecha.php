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
}