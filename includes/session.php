<?php
// Inicialización de sesión y auto-login.
if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}

$is_logged = !empty($_SESSION['login']) && $_SESSION['login'] === 'ok';

// Auto-login desde cookies 'remember' (no renovamos remember_user/pass)
if (!$is_logged && isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_pass'])) {
    $usersFile = __DIR__ . '/../data/usuarios.php';
    if (file_exists($usersFile)) {
        $usuarios = require $usersFile;
        $u = $_COOKIE['remember_user'];
        $p = $_COOKIE['remember_pass'];
        if (isset($usuarios[$u]) && $usuarios[$u]['clave'] === $p) {
            $_SESSION['login'] = 'ok';
            $_SESSION['usuario'] = $u;
            $_SESSION['estilo'] = $usuarios[$u]['estilo'];
            $is_logged = true;
            $lastVisitMsg = isset($_COOKIE['last_visit']) ? $_COOKIE['last_visit'] : '';
            if ($lastVisitMsg) {
                $_SESSION['remember_message'] = "Usuario recordado: $u. Última visita: $lastVisitMsg";
            } else {
                $_SESSION['remember_message'] = "Usuario recordado: $u.";
            }
            // actualizar last_visit
            $expire = time() + 90 * 24 * 60 * 60;
                    setcookie('last_visit', date('d/m/Y H:i:s'), $expire, '/', '', false, true);
                }
            }
        }

        ?>
