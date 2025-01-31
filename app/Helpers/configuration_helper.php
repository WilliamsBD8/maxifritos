<?php

use App\Models\Configuration;

function configInfo()
{
    $config = new Configuration();
    if($data = $config->find(1)){
        return $data;
    }
    return [];
}

function hexToRgb($hex) {
    // Quitar el símbolo '#' si está presente
    $hex = ltrim($hex, '#');

    // Si el formato es abreviado (e.g., "fff"), expandirlo
    if (strlen($hex) === 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }

    // Convertir hexadecimal a decimal
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    return "$r, $g, $b";
}

function darkenColor($hex, $percent) {
    // Quitar el carácter '#' si está presente
    $hex = str_replace('#', '', $hex);

    // Convertir el valor HEX a RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    // Interpolar cada componente RGB hacia negro (0, 0, 0)
    $r = $r * (1 - $percent / 100);
    $g = $g * (1 - $percent / 100);
    $b = $b * (1 - $percent / 100);

    // Convertir de vuelta a HEX y retornar el nuevo color
    return sprintf("#%02x%02x%02x", (int)$r, (int)$g, (int)$b);
}

function lightenColor($hex, $percent) {
    // Quitar el carácter '#' si está presente
    $hex = str_replace('#', '', $hex);

    // Convertir el valor HEX a RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    // Interpolar cada componente RGB hacia blanco (255, 255, 255)
    $r = $r + (255 - $r) * ($percent / 100);
    $g = $g + (255 - $g) * ($percent / 100);
    $b = $b + (255 - $b) * ($percent / 100);

    // Convertir de vuelta a HEX y retornar el nuevo color
    return sprintf("#%02x%02x%02x", (int)$r, (int)$g, (int)$b);
}

function only_full_group(){
    $db = \Config\Database::connect();
    $db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
}

function updatedCommit(){
    $commitHash = exec('git log -1 --format=%H');
    $envFile = APPPATH . '../.env';
    $envContents = file_get_contents($envFile);
    $pattern = '/^GIT_COMMIT_HASH=(.*)$/m';
    preg_match($pattern, $envContents, $matches);
    if (!empty($matches)) {
        $storedCommitHash = $matches[1];
    } else {
        $storedCommitHash = '';
    }
    if ($commitHash !== $storedCommitHash) {
        if ($storedCommitHash) {
            $envContents = preg_replace($pattern, "GIT_COMMIT_HASH={$commitHash}", $envContents);
        } else {
            $envContents .= "\nGIT_COMMIT_HASH={$commitHash}\n";
        }
        file_put_contents($envFile, $envContents);
    }
    log_message('info', ",env: ".$envFile);
}

function getCommit(){
    return env('GIT_COMMIT_HASH', '1.0.0');
}

function validUrl(){
    return !(env('app.origin', 'produccion') == 'local');
}

function getColumnLetter($columnNumber) {
    $columnLetter = '';
    while ($columnNumber > 0) {
        $remainder = ($columnNumber - 1) % 26;
        $columnLetter = chr(65 + $remainder) . $columnLetter;
        $columnNumber = intval(($columnNumber - 1) / 26);
    }
    return $columnLetter;
}