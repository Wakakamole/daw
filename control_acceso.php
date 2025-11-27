<?php
// control_acceso.php - procesa login en servidor usando la tabla Usuarios
// Recibe usuario y clave vía POST desde inicio_sesion.php
// Nota: session_start() ya se ejecuta en index.php (router)

function redirect_to($path, $params = []) {
    $host = $_SERVER['HTTP_HOST'];
    $qs = '';
    if (!empty($params)) { $qs = '?' . http_build_query($params); }
    // Convertir rutas antiguas a rutas del router
    $route_map = [
        'inicio_sesion.php' => '/daw/login',
        'index_user.php' => '/daw/inicio_user',
        'index.php' => '/daw/',
    ];
    $target_path = isset($route_map[$path]) ? $route_map[$path] : '/daw/' . str_replace('.php', '', $path);
    header("Location: http://$host$target_path$qs");
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
$stmt = $db->prepare('SELECT IdUsuario, NomUsuario, Clave, Estilo, Foto FROM usuarios WHERE NomUsuario = ? LIMIT 1');
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
    // Marcar que la sesión se ha creado por un login reciente (evita mostrar el mensaje "bienvenido de vuelta" inmediatamente)
    $_SESSION['just_logged_in'] = true;

    if ($remember) {
        $expire = time() + 90 * 24 * 60 * 60; // 90 días
        // Extender la cookie de sesión para persistir el login (booleana "remember me")
        setcookie(session_name(), session_id(), $expire, '/', '', false, true);
        // Marcar cookie booleana para indicar que el usuario pidió ser recordado
        setcookie('remember', '1', $expire, '/', '', false, true);
        // Guardar nombre de usuario en cookie para mostrar mensajes de bienvenida al volver
        setcookie('remember_user', $row['NomUsuario'], $expire, '/', '', false, true);
        // Guardar última visita
        setcookie('last_visit', date('d/m/Y H:i:s'), $expire, '/', '', false, true);
    }

    redirect_to('index_user.php');
} else {
    $_SESSION['flash'] = 'Usuario o contraseña incorrectos.';
    redirect_to('inicio_sesion.php');
}
