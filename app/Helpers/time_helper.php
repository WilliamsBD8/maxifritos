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

function getPeriod(){
    $periods = [
        (object) ['value' => "", 'name' => 'Personalizado', "selected" => false],
        (object) ['value' => "day", 'name' => 'Hoy', "selected" => false],
        (object) ['value' => "yesterday", 'name' => 'Ayer', "selected" => false],
        (object) ['value' => "weekend", 'name' => 'Esta semana', "selected" => false],
        (object) ['value' => "last_weekend", 'name' => 'Semana Pasada', "selected" => false],
        (object) ['value' => "month", 'name' => 'Este mes', "selected" => !false],
        (object) ['value' => "last_month", 'name' => 'Mes pasado', "selected" => false],
    ];

    foreach ($periods as $key => $period) {
        $period->dates = getPeriodDate($period->value);
    }

    return $periods;
}

function getPeriodDate($period){
    switch ($period) {
        case 'day':
        default:
            // Fecha actual
            $today = new DateTime();
            $date_init = $today->format('Y-m-d');
            $date_end = $today->format('Y-m-d');
            break;
    
        case 'yesterday':
            // Ayer
            $yesterday = new DateTime();
            $yesterday->modify('-1 day');
            $date_init = $yesterday->format('Y-m-d');
            $date_end = $yesterday->format('Y-m-d');
            break;
    
        case 'weekend':
            // Fin de semana (semana actual)
            $today = new DateTime();
            $firstDay = clone $today;
            $dayOfWeek = $today->format('N'); // Día de la semana (1 = lunes, 7 = domingo)
            $firstDay->modify('-' . ($dayOfWeek - 1) . ' days'); // Lunes de esta semana
            $lastDay = clone $firstDay;
            $lastDay->modify('+6 days'); // Domingo de esta semana
            $date_init = $firstDay->format('Y-m-d');
            $date_end = $lastDay->format('Y-m-d');
            break;
    
        case 'last_weekend':
            // Fin de semana pasado
            $today = new DateTime();
            $dayOfWeek = $today->format('N'); // Día de la semana (1 = lunes, 7 = domingo)
            $firstDayLastWeek = clone $today;
            $firstDayLastWeek->modify('-' . ($dayOfWeek + 6) . ' days'); // Lunes de la semana pasada
            $lastDayLastWeek = clone $firstDayLastWeek;
            $lastDayLastWeek->modify('+6 days'); // Domingo de la semana pasada
            $date_init = $firstDayLastWeek->format('Y-m-d');
            $date_end = $lastDayLastWeek->format('Y-m-d');
            break;
    
        case 'month':
            // Mes actual
            $today = new DateTime();
            $firstDayOfMonth = new DateTime($today->format('Y-m-01')); // Primer día del mes actual
            $lastDayOfMonth = new DateTime($today->format('Y-m-t')); // Último día del mes actual
            $date_init = $firstDayOfMonth->format('Y-m-d');
            $date_end = $lastDayOfMonth->format('Y-m-d');
            break;
    
        case 'last_month':
            // Mes pasado
            $today = new DateTime();
            $firstDayOfLastMonth = new DateTime($today->format('Y-m-01'));
            $firstDayOfLastMonth->modify('-1 month'); // Primer día del mes pasado
            $lastDayOfLastMonth = clone $firstDayOfLastMonth;
            $lastDayOfLastMonth->modify('last day of this month'); // Último día del mes pasado
            $date_init = $firstDayOfLastMonth->format('Y-m-d');
            $date_end = $lastDayOfLastMonth->format('Y-m-d');
            break;
    }
    return (object) ["date_init" => $date_init, "date_end" => $date_end];    
}

function Dayspanish($day) {
    $dias = [
        'Monday'    => 'Lunes',
        'Tuesday'   => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday'  => 'Jueves',
        'Friday'    => 'Viernes',
        'Saturday'  => 'Sábado',
        'Sunday'    => 'Domingo'
    ];
    return $dias[$day];
}