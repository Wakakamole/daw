<?php
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$errors = [];

// Preferir errores de sesión (flash) cuando vengan de un submit
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!empty($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}
// Si no hay errores en sesión, conservar compatibilidad con la versión anterior que usaba GET
elseif (!empty($_GET['errors'])) {
    $errors = array_filter(explode(',', $_GET['errors']));
}

$vals = [];
$fields = ['usuario','email','sexo','fecha_nacimiento','ciudad','pais'];
// Priorizar repoblado desde sesión ($_SESSION['old'])
if (!empty($_SESSION['old'])) {
    $old = $_SESSION['old'];
    foreach ($fields as $f) { $vals[$f] = isset($old[$f]) ? $old[$f] : ''; }
    unset($_SESSION['old']);
} else {
    foreach ($fields as $f) { $vals[$f] = isset($_GET[$f]) ? $_GET[$f] : ''; }
}

// Si hay fecha_nacimiento parsea para rellenar día mes año por separado
$d = '';
$m = '';
$y = '';
if ($vals['fecha_nacimiento']) {
    $parts = explode('-', $vals['fecha_nacimiento']);
    if (count($parts) === 3) {
        $y = $parts[0];
        $m = $parts[1];
        $d = ltrim($parts[2], '0');
    }
}

// Mapa de mensajes legibles
$mensajes = [
    'usuario' => 'El nombre de usuario es obligatorio.',
    'password_empty' => 'La contraseña no puede estar vacía.',
    'repite_empty' => 'Debes repetir la contraseña.',
    'password_mismatch' => 'Las contraseñas no coinciden.',
    'password_actual_empty' => 'Debes introducir tu contraseña actual para confirmar los cambios.',
    'password_actual_invalid' => 'La contraseña actual es incorrecta.'
];

// Mensajes adicionales para validaciones servidor-side
$mensajes += [
    'usuario_empty' => 'El nombre de usuario es obligatorio.',
    'usuario_format' => 'Usuario: 3-15 caracteres, empieza por letra; solo letras y números.',
    'usuario_exists' => 'El nombre de usuario ya está en uso.',
    'password_format' => 'Contraseña: 6-15 caracteres válidos (letras, dígitos, - y _).',
    'password_requirements' => 'La contraseña debe contener mayúscula, minúscula y número.',
    'email_empty' => 'La dirección de correo no puede estar vacía.',
    'email_invalid' => 'Dirección de correo no válida.',
    'email_exists' => 'La dirección de correo ya está registrada.',
    'fecha_invalid' => 'Fecha de nacimiento no válida.',
    'fecha_menor_18' => 'Debes tener al menos 18 años para registrarte.',
    'sexo_empty' => 'Debes seleccionar un valor para el sexo.',
    'db_error' => 'Error interno. Inténtalo más tarde.'
];

?>
<?php
// Usar cabecera común
$page_title = 'INMOLINK - Registro';
require_once __DIR__ . '/includes/cabecera.php';
?>

    <main>
        <h1>REGISTRO USUARIO</h1>

        <?php if (!empty($errors)): ?>
            <section class="error-summary" role="alert" aria-live="assertive">
                <p><strong>Se han encontrado errores en el formulario:</strong></p>
                <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?php echo h(isset($mensajes[$e]) ? $mensajes[$e] : $e); ?></li>
                <?php endforeach; ?>
                </ul>
                <?php if (!empty($_SESSION['error_detalle'])): ?>
                    <p style="color: #d32f2f; margin-top: 10px;"><strong>Detalle técnico:</strong> <?php echo h($_SESSION['error_detalle']); ?></p>
                    <?php unset($_SESSION['error_detalle']); ?>
                <?php endif; ?>
            </section>
        <?php endif; ?>

    <?php
    // Preparar datos para el formulario compartido
    $modo = 'registro';
    $action = '/registro-process';
    $usuario = [
        'usuario' => $vals['usuario'] ?? '',
        'email' => $vals['email'] ?? '',
        'sexo' => $vals['sexo'] ?? '',
        'fecha_nacimiento' => $vals['fecha_nacimiento'] ?? '',
        'ciudad' => $vals['ciudad'] ?? '',
        'pais' => $vals['pais'] ?? '',
        'foto' => ''
    ];
    // usar la nueva ruta de procesamiento que implementa validación y stored en BD
    $action = '/daw/registro';
    $submitText = 'Registrarme';
    require __DIR__ . '/includes/formulario_user.php';
    ?>

        <p class="registro-link"><strong>¿Ya tienes cuenta? </strong>
            <a href="/daw/login" class="boton-enlace">Inicia sesion aqui</a>
        </p>

    </main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
