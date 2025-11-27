<?php
// include que centraliza la comprobación de acceso a la parte privada
require_once __DIR__ . '/session.php';

// Si ya hay sesión válida, permitir acceso
if (!empty($_SESSION['login']) && $_SESSION['login'] === 'ok') {
    return;
}

// No hay sesión: informar con flashdata y redirigir a aviso
$_SESSION['flash'] = 'Debes iniciar sesión para acceder a la zona privada.';

$host = $_SERVER['HTTP_HOST'];
header("Location: http://$host/daw/aviso");
exit;
?>
