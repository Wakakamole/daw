
<!-- PÁGINA DE CONTROL DE ACCESO -->
 <!-- control_acceso.php - procesa login en servidor -----CREAR----- 
      Recibe usuario y clave vía POST desde inicio_sesion.html
      Si usuario y clave correctos, redirige a index_user.php
      Si no, redirige a inicio_sesion.html con error en query string
 -->
<?php
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

if ($usuario === '' || $clave === '') {
    redirect_to('inicio_sesion.html', ['error' => 'empty']);
}

$usersFile = __DIR__ . '/data/usuarios.php';
if (!file_exists($usersFile)) {
    // si no existe fichero de usuarios, denegar acceso
    redirect_to('inicio_sesion.html', ['error' => 'no_users']);
}

$usuarios = require $usersFile; // devuelve array usuario=>clave
if (isset($usuarios[$usuario]) && $usuarios[$usuario] === $clave) {
    // acceso correcto
    redirect_to('index_user.php');
} else {
    redirect_to('inicio_sesion.html', ['error' => 'invalid']);
}
