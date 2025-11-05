<?php
session_start();
// Borrar variables de sesión
$_SESSION = array();
// Borrar cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}
// Destruir sesión
session_destroy();

// Borrar cookies
setcookie('remember_user', '', time() - 3600, '/');
setcookie('remember_pass', '', time() - 3600, '/');
setcookie('last_visit', '', time() - 3600, '/');

// Redirigir a página pública
header('Location: index.php');
exit;
