<?php
// control_acceso.php - procesa login en servidor usando la tabla Usuarios
// Recibe usuario y clave vía POST desde inicio_sesion.php

session_start();

function redirect_to($path, $params = []) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $qs = '';
    if (!empty($params)) { $qs = '?' . http_build_query($params); }
    header("Location: http://$host$uri/$path$qs");
    exit;
}

require_once __DIR__ . '/includes/basedatos.php';

$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';
$remember = isset($_POST['remember']) && $_POST['remember'];

if ($usuario === '' || $clave === '') {
    $_SESSION['flash'] = 'Rellena usuario y contraseña.';
    redirect_to('inicio_sesion.php');
}

$db = get_db();
$stmt = $db->prepare('SELECT IdUsuario, NomUsuario, Clave, Estilo, Foto FROM Usuarios WHERE NomUsuario = ? LIMIT 1');
if (!$stmt) {
    // error preparando la consulta
    $_SESSION['flash'] = 'Error de acceso (BD).';
    redirect_to('inicio_sesion.php');
}
$stmt->bind_param('s', $usuario);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();

if ($row && password_verify($clave, $row['Clave'])) {
    // acceso correcto: crear sesión
    $_SESSION['login'] = 'ok';
    $_SESSION['usuario'] = $row['NomUsuario'];
    $_SESSION['usuario_id'] = (int)$row['IdUsuario'];
    $_SESSION['estilo'] = isset($row['Estilo']) ? $row['Estilo'] : '';
    // Guardar foto en sesión para usar en la cabecera y evitar consultas adicionales
    $_SESSION['foto'] = isset($row['Foto']) ? $row['Foto'] : '';

    if ($remember) {
        $expire = time() + 90 * 24 * 60 * 60; // 90 días
        // Extender la cookie de sesión para persistir el login (booleana "remember me")
        setcookie(session_name(), session_id(), $expire, '/', '', false, true);
        // Marcar cookie booleana para indicar que el usuario pidió ser recordado
        setcookie('remember', '1', $expire, '/', '', false, true);
        // Guardar última visita
        setcookie('last_visit', date('d/m/Y H:i:s'), $expire, '/', '', false, true);
    }

    redirect_to('index_user.php');
} else {
    $_SESSION['flash'] = 'Usuario o contraseña incorrectos.';
    redirect_to('inicio_sesion.php');
}
