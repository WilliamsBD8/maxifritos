<?php

use CodeIgniter\I18n\Time;

function different($data)
{
    $myTime = new Time('now', 'America/Bogota', 'es_CO');
    $time = Time::parse($data, 'America/Bogota', 'es_CO');
    $diff =  $time->difference($myTime, 'America/Bogota');
    return $diff->humanize();
}

function meses($mes = null) {
    $meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    if ($mes === null) {
        return $meses;
    }
    return $meses[$mes];
}

function format_date($date) {
    $timestamp = strtotime($date);
    $month = meses(date("m", $timestamp) - 1);
    $formattedDate = $month . " " . date("d, Y", $timestamp);
    return $formattedDate;
}