<?php

$GLOBALS['basedatos_config'] ??= [
    'Servidor'   => '127.0.0.1',
    'Usuario'    => 'root',
    'Contrasena' => '',
    'BaseDatos'  => 'pbid',
];

function get_db()
{
    static $mysqli = null;

    if ($mysqli instanceof mysqli) {
        return $mysqli;
    }

    $cfg = $GLOBALS['basedatos_config'];

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $mysqli = new mysqli(
            $cfg['Servidor'],
            $cfg['Usuario'],
            $cfg['Contrasena'],
            $cfg['BaseDatos']
        );
        $mysqli->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $e) {
        error_log('Error conexión BD: ' . $e->getMessage());
        throw new Exception('No se pudo conectar con la base de datos.');
    }

    return $mysqli;
}

function close_db()
{
    static $closed = false;
    if ($closed) return;

    try {
        $db = get_db();
        if ($db) {
            $db->close();
            $closed = true;
        }
    } catch (Exception $e) {
        // Silenciar errores en el cierre
    }
}

// Cierre automático al finalizar
register_shutdown_function('close_db');

