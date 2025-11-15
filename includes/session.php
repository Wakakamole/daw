<?php

// Antes de session_start(): si existe cookie 'remember' alargar cookie y GC
$rememberLifetime = 90 * 24 * 60 * 60;
// Si existe cookie 'remember' al arrancar, ajustar parámetros de la cookie de sesión
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

// Si hay cookie 'remember', refrescar las cookies relevantes para mantener la sesión
if (isset($_COOKIE['remember']) && $_COOKIE['remember'] === '1') {
    $expire = time() + $rememberLifetime;
    // refrescar cookie de sesión en el cliente (mantener misma id)
    if (session_id() !== '') {
        setcookie(session_name(), session_id(), $expire, '/', '', false, true);
    }
    // refrescar cookie booleana 'remember'
    setcookie('remember', '1', $expire, '/', '', false, true);
    // actualizar última visita para mostrar cuándo estuvo el usuario
    setcookie('last_visit', date('d/m/Y H:i:s'), $expire, '/', '', false, true);
}

$is_logged = !empty($_SESSION['login']) && $_SESSION['login'] === 'ok';

// Preparar mensaje de "bienvenido de vuelta" usando la cookie 'last_visit'

if (isset($_COOKIE['last_visit'])) {
    // Determinar nombre de usuario a mostrar (preferir sesión)
    $usuario_msg = '';
    if ($is_logged && !empty($_SESSION['usuario'])) {
        $usuario_msg = $_SESSION['usuario'];
    } elseif (isset($_COOKIE['remember_user']) && $_COOKIE['remember_user'] !== '') {
        $usuario_msg = $_COOKIE['remember_user'];
    }

    if ($usuario_msg !== '') {
        // No crear el mensaje si la sesión se ha creado hace nada (just_logged_in=true)
        if (empty($_SESSION['just_logged_in'])) {
            // Evitar sobrescribir si ya se creó en la sesión actual
            if (empty($_SESSION['remember_message']) && empty($_SESSION['remember_message_shown'])) {
                $last = $_COOKIE['last_visit'];
                $_SESSION['remember_message'] = "Bienvenido de vuelta " . htmlspecialchars($usuario_msg, ENT_QUOTES, 'UTF-8') . ". Última visita: " . htmlspecialchars($last, ENT_QUOTES, 'UTF-8');
                // Marcar para no volver a crear el mensaje en esta sesión
                $_SESSION['remember_message_shown'] = true;
            }
        }
    }
}

?>
