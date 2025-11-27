<?php
// Funciones reutilizables de saneamiento y validación para el registro/edición de usuarios

function sanear_cadena($s)
{
    if ($s === null) return '';
    $s = trim($s);
    $s = stripslashes($s);
    $s = strip_tags($s);
    return $s;
}

// Devuelve null si OK, o una clave de error si falla
function validar_usuario($u)
{
    $u = (string)$u;
    if ($u === '') return 'usuario_empty';
    // Debe empezar por letra, contener solo letras y dígitos, longitud 3-15
    if (!preg_match('/^[A-Za-z][A-Za-z0-9]{2,14}$/', $u)) return 'usuario_format';
    return null;
}

function validar_contrasena($p)
{
    $p = (string)$p;
    if ($p === '') return 'password_empty';
    // permitido: letras inglesas, dígitos, guion y guion bajo; longitud 6-15
    if (!preg_match('/^[A-Za-z0-9_-]{6,15}$/', $p)) return 'password_format';
    // requerimientos: al menos una mayúscula, una minúscula y un dígito
    if (!preg_match('/[A-Z]/', $p) || !preg_match('/[a-z]/', $p) || !preg_match('/[0-9]/', $p)) {
        return 'password_requirements';
    }
    return null;
}

function validar_email($e)
{
    $e = (string)$e;
    if ($e === '') return 'email_empty';
    if (strlen($e) > 254) return 'email_long';

    // Comprobación básica con FILTER_VALIDATE_EMAIL
    if (!filter_var($e, FILTER_VALIDATE_EMAIL)) return 'email_invalid';

    // Más comprobaciones: parte-local y dominio
    $parts = explode('@', $e);
    if (count($parts) !== 2) return 'email_invalid';
    list($local, $dom) = $parts;
    if ($local === '' || $dom === '') return 'email_invalid';
    if (strlen($local) > 64) return 'email_local_long';
    // local no puede empezar/terminar por punto ni tener dos puntos seguidos
    if ($local[0] === '.' || substr($local, -1) === '.' || strpos($local, '..') !== false) return 'email_invalid_local';

    // dominio: secuencia de subdominios separados por punto, cada uno <=63
    $labels = explode('.', $dom);
    foreach ($labels as $lab) {
        if ($lab === '' || strlen($lab) > 63) return 'email_invalid_domain';
        if (!preg_match('/^[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?$/', $lab)) return 'email_invalid_domain';
    }

    return null;
}

function validar_sexo($s)
{
    if ($s === null || $s === '') return 'sexo_empty';
    $ok = ['hombre','mujer','otro'];
    if (!in_array($s, $ok, true)) return 'sexo_invalid';
    return null;
}

function validar_fecha_mayor_18($fecha)
{
    if (!$fecha) return null; // campo no obligatorio en enunciado (pero se pide comprobar si existe)
    // aceptar formatos YYYY-MM-DD
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    $errors = DateTime::getLastErrors();
    if ($d === false || !is_array($errors) || $errors['warning_count'] || $errors['error_count']) return 'fecha_invalid';
    $hoy = new DateTime();
    $diff = $hoy->diff($d);
    if ($diff->y < 18) return 'fecha_menor_18';
    return null;
}

// helper: transformar sexo textual a entero según esquema de la BD
function sexo_a_int($s)
{
    switch ($s) {
        case 'hombre': return 1;
        case 'mujer': return 2;
        case 'otro': return 3;
        default: return null;
    }
}

?>