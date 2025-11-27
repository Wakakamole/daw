<?php
require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/basedatos.php';
require_once __DIR__ . '/includes/cabecera.php';

if (!function_exists('h')) { function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); } }

$db = get_db();
$usuario_sess = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';
$usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

if ($usuario_sess === '' && $usuario_id <= 0) {
    echo "<main><p>No hay usuario autenticado.</p></main>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}

$user = null;
if ($usuario_id > 0) {
    $stmt = $db->prepare("SELECT IdUsuario, NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, Estilo FROM usuarios WHERE IdUsuario = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res ? $res->fetch_assoc() : null;
        if ($res) { $res->free(); }
        $stmt->close();
    }
}

if (!$user && $usuario_sess !== '') {
    $stmt = $db->prepare("SELECT IdUsuario, NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, Estilo FROM usuarios WHERE NomUsuario = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $usuario_sess);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res ? $res->fetch_assoc() : null;
        if ($res) { $res->free(); }
        $stmt->close();
    }
}

if (!$user) {
    echo "<main><p>Usuario no encontrado.</p></main>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}


// Mapear el valor almacenado en la BD (ej. 1/2) a las opciones del formulario ('hombre','mujer','otro')
function map_sexo_bd_a_form($val) {
    if ($val === null || $val === '') return '';
    // si viene numérico, mapear a etiquetas
    if (is_numeric($val)) {
        $n = (int)$val;
        return match($n) {
            1 => 'hombre',
            2 => 'mujer',
            3 => 'otro',
            default => ''
        };
    }
    // si ya es texto, normalizar
    $s = strtolower(trim((string)$val));
    if (in_array($s, ['hombre','mujer','otro'])) return $s;
    return '';
}

$usuario = [
    'id' => (int)$user['IdUsuario'],
    'usuario' => $user['NomUsuario'],
    'email' => $user['Email'] ?? '',
    'sexo' => map_sexo_bd_a_form($user['Sexo'] ?? ''),
    'fecha_nacimiento' => $user['FNacimiento'] ?? '',
    'ciudad' => $user['Ciudad'] ?? '',
    'pais' => $user['Pais'] ?? '',
    'foto' => $user['Foto'] ?? '',
    'estilo' => $user['Estilo'] ?? ''
];

$modo = 'edicion';
$action = '/daw/mis_datos';
$submitText = 'Guardar cambios';

// Gestionar mensajes de error y valores previos
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$errors = [];
if (!empty($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}

// Mapa de mensajes legibles
$mensajes = [
    'usuario' => 'El nombre de usuario es obligatorio.',
    'password_empty' => 'La contraseña no puede estar vacía.',
    'repite_empty' => 'Debes repetir la contraseña.',
    'password_mismatch' => 'Las contraseñas no coinciden.',
    'password_actual_empty' => 'Debes introducir tu contraseña actual para confirmar los cambios.',
    'password_actual_invalid' => 'La contraseña actual es incorrecta.',
    'usuario_empty' => 'El nombre de usuario es obligatorio.',
    'usuario_format' => 'Usuario: 3-15 caracteres, empieza por letra; solo letras y números.',
    'usuario_exists' => 'El nombre de usuario ya está en uso.',
    'password_format' => 'Contraseña: 6-15 caracteres válidos (letras, dígitos, - y _).',
    'password_requirements' => 'La contraseña debe contener mayúscula, minúscula y número.',
    'email_empty' => 'La dirección de correo no puede estar vacía.',
    'email_invalid' => 'Dirección de correo no válida.',
    'email_exists' => 'La dirección de correo ya está registrada.',
    'fecha_invalid' => 'Fecha de nacimiento no válida.',
    'fecha_menor_18' => 'Debes tener al menos 18 años.',
    'sexo_empty' => 'Debes seleccionar un valor para el sexo.',
    'db_error' => 'Error interno. Inténtalo más tarde.'
];

// Si hay valores previos en sesión, actualizar usuario
if (!empty($_SESSION['old'])) {
    $old = $_SESSION['old'];
    $usuario['usuario'] = $old['usuario'] ?? $usuario['usuario'];
    $usuario['email'] = $old['email'] ?? $usuario['email'];
    $usuario['sexo'] = $old['sexo'] ?? $usuario['sexo'];
    $usuario['fecha_nacimiento'] = $old['fecha_nacimiento'] ?? $usuario['fecha_nacimiento'];
    $usuario['ciudad'] = $old['ciudad'] ?? $usuario['ciudad'];
    $usuario['pais'] = $old['pais'] ?? $usuario['pais'];
    unset($_SESSION['old']);
}

?>

<main>
    <h1>Mis datos</h1>
    <p>A continuación puedes modificar tus datos. Debes introducir tu contraseña actual para confirmar los cambios.</p>

    <?php if (!empty($errors)): ?>
        <section class="error-summary" role="alert" aria-live="assertive">
            <p><strong>Se han encontrado errores:</strong></p>
            <ul>
            <?php foreach ($errors as $e): ?>
                <li><?php echo h(isset($mensajes[$e]) ? $mensajes[$e] : $e); ?></li>
            <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php require __DIR__ . '/includes/formulario_user.php'; ?>

</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
