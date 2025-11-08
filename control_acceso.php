
<!-- PÁGINA DE CONTROL DE ACCESO -->
 <!-- control_acceso.php - procesa login en servidor -----CREAR----- 
     Recibe usuario y clave vía POST desde inicio_sesion.php
     Si usuario y clave correctos, redirige a index_user.php
     Si no, redirige a inicio_sesion.php con error en flashdata
 -->
<?php
session_start();

function redirect_to($path, $params = []) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $qs = '';
    if (!empty($params)) { $qs = '?' . http_build_query($params); }
    header("Location: http://$host$uri/$path$qs");
    exit;
}

$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';
$remember = isset($_POST['remember']) ? true : false;

if ($usuario === '' || $clave === '') {
    $_SESSION['flash'] = 'Rellena usuario y contraseña.';
    redirect_to('inicio_sesion.php');
}

$usersFile = __DIR__ . '/data/usuarios.php';
if (!file_exists($usersFile)) {
    $_SESSION['flash'] = 'No hay usuarios configurados.';
    redirect_to('inicio_sesion.php');
}

$usuarios = require $usersFile; // devuelve array usuario=>clave
if (isset($usuarios[$usuario]) && $usuarios[$usuario]['clave'] === $clave) {
    // acceso correcto: crear sesión
    $_SESSION['login'] = 'ok';
    $_SESSION['usuario'] = $usuario;
    $_SESSION['estilo'] = $usuarios[$usuario]['estilo'];

    // Si ha marcado recordar, crear cookies (no accesible desde JS: httponly)
    if ($remember) {
        $expire = time() + 90 * 24 * 60 * 60; // 90 días
        // ruta '/' para que esté disponible en todo el sitio
        setcookie('remember_user', $usuario, $expire, '/');
        // marcar httponly para contraseña
        setcookie('remember_pass', $clave, $expire, '/', '', false, true);
        // almacenar la fecha/hora de esta primera visita que será mostrada la próxima vez
        setcookie('last_visit', date('d/m/Y H:i:s'), $expire, '/', '', false, true);
    }

    redirect_to('index_user.php');
} else {
    $_SESSION['flash'] = 'Usuario o contraseña incorrectos.';
    redirect_to('inicio_sesion.php');
}
