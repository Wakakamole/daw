<?php
/**
 * Módulo de conexión a la base de datos (mysqli)
 */

function get_db()
{
    static $mysqli = null;

    if ($mysqli instanceof mysqli) {
        return $mysqli;
    }

    $config_file = __DIR__ . '/basedatos_config.ini';
    if (!file_exists($config_file)) {
        throw new Exception("Fichero de configuración de BaseDatos no encontrado: $config_file");
    }

    $cfg = parse_ini_file($config_file, true);
    if (!isset($cfg['BaseDatos'])) {
        throw new Exception("Sección [BaseDatos] no encontrada en el fichero de configuración: $config_file");
    }

    $bd = $cfg['BaseDatos'];
    // Llaves en español definidas en basedatos_config.ini
    $servidor = $bd['Servidor'] ?? 'localhost';
    $usuario = $bd['Usuario'] ?? 'root';
    $contrasena = $bd['Contrasena'] ?? '';
    $nombreBD = $bd['BaseDatos'] ?? '';

    // Lanzar excepciones en caso de error
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $mysqli = new mysqli($servidor, $usuario, $contrasena, $nombreBD);
        // Forzar uso utf8mb4
        $mysqli->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $e) {
        // Registra el error pero no muestra credenciales en la salida al usuario
        error_log('Error conexión BaseDatos: ' . $e->getMessage());
        throw new Exception('No se pudo establecer conexión con la base de datos. Revisa el fichero de configuración o el servidor.');
    }

    return $mysqli;
}

function close_db()
{
    static $closed = false;
    if ($closed) return;
    try {
        $m = get_db();
        if ($m) {
            $m->close();
            $closed = true;
        }
    } catch (Exception $e) {
        // Silenciar en cierre
    }
}

// Alias en español
function obtenerBaseDatos()
{
    return get_db();
}

function cerrarBaseDatos()
{
    close_db();
}

//cerrar automáticamente al final del script
register_shutdown_function('close_db');
