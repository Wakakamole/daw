<?php

// Antes de session_start(): si existe cookie 'remember' alargar cookie y GC
$rememberLifetime = 90 * 24 * 60 * 60;
if (isset($_COOKIE['remember']) && $_COOKIE['remember'] === '1') {
    // Ajustar cookie de sesión para que expire en $rememberLifetime
    session_set_cookie_params([
        'lifetime' => $rememberLifetime,
        'path' => '/',
        'domain' => '',  
        'secure' => false, 
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    // Aumentar tiempo de vida en el servidor (GC)
    ini_set('session.gc_maxlifetime', (string)$rememberLifetime);
}

// Inicialización de sesión y auto-login.
if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}

$is_logged = !empty($_SESSION['login']) && $_SESSION['login'] === 'ok';

?>
