<?php

namespace RianWlp\Libs\utils;

use DateTime;

class Data
{
    public function __construct() {}

    public static function validarMesAno($date, $format = 'm/Y'): bool
    {
        $dt = DateTime::createFromFormat($format, $date);
        return $dt && $dt->format($format) === $date;
    }

    public static function converterEnUsMesAno($data)
    {
        $date = DateTime::createFromFormat('m/Y', $data);
        return str_replace('/', '', $date->format('Y/m'));
    }

    // public static function converterEnUs($data, $format = 'Y/m/d'){
    public static function converterEnUs($data)
    {
        $date = DateTime::createFromFormat('d/m/Y', $data);
        return str_replace('/', '', $date->format('Y/m/d'));

        // $date = DateTime::createFromFormat('d/m/Y', $data);
        // return str_replace('/','',$date->format('Y/m/d'));
    }

    /**
     * Fixme
     * Esse metodo ta meio bosta
     */
    // public static function converterToFormat(string $data = null, string $format, string $to): string
    public static function converterToFormat($data, $format, $to)
    {
        if (empty($data)) {
            return;
        }

        $format = str_replace('-', '/', $format);
        $data   = str_replace('-', '/', $data);

        $date = DateTime::createFromFormat($format, $data);

        return $date->format($to);
    }

    public static function validarData($date, $format = 'd/m/Y'): bool
    {
        $dt = DateTime::createFromFormat($format, $date);
        return $dt && $dt->format($format) === $date;
    }


    // protected static function validarMesAno($validade){

    //     $dateSplit = explode('/',$validade);
    //     if(count($dateSplit) > 2){

    //         $day = $dateSplit[0];
    //         $month = $dateSplit[1];
    //         $year = $dateSplit[2];

    //     }else{

    //         $day = 1;
    //         $month = $dateSplit[0];
    //         $year = $dateSplit[1];
    //     }
    // }

    // public function validarData($validade){

    //     $dateSplit = explode('/',$validade);
    //     if(count($dateSplit) > 2){

    //         $day = $dateSplit[0];
    //         $month = $dateSplit[1];
    //         $year = $dateSplit[2];

    //     }else{

    //         $day = 1;
    //         $month = $dateSplit[0];
    //         $year = $dateSplit[1];
    //     }

    //     if(($month == '00') && ($year == '0000')){

    //         return true;

    //     }else if(!($year < 1000 || $year > 3000 || $month == 0 || $month > 12)){

    //         $monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    //         // Adjust for leap $years
    //         if($year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0))
    //         $monthLength[1] = 29;

    //         // if(($day > 0) && ($day <= $monthLength[$month - 1])){}
    //         return (($day > 0) && ($day <= $monthLength[$month - 1]));
    //     }
    //     return false;
    // }
}
