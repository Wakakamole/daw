<?php
session_start();
// Borrar variables de sesión
$_SESSION = array();
// Borrar cookie de sesión
if (isset($_COOKIE[session_name()])) {
    // borrar cookie de sesión en el cliente (coincidir con flags: path, httponly)
    setcookie(session_name(), '', time() - 42000, '/', '', false, true);
}
// Destruir sesión
session_destroy();

// Borrar cookies conocidas (coincidir con creación: path '/', httponly donde aplique)
setcookie('remember', '', time() - 3600, '/', '', false, true);
setcookie('last_visit', '', time() - 3600, '/', '', false, true);
setcookie('saludo', '', time() - 3600, '/', '', false, true);
setcookie('ultimos_anuncios', '', time() - 3600, '/', '', false, true);
// Borrar cookie con el nombre de usuario almacenado para "recordarme"
setcookie('remember_user', '', time() - 3600, '/', '', false, true);


// Redirigir a página pública
header('Location: index.php');
exit;
