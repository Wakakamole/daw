<?php
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$errors = [];

// Preferir errores de sesión (flash) cuando vengan de un submit; esto unifica comportamiento con inicio_sesion
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
// Priorizar repoblado desde sesión ($_SESSION['old']) si existe
if (!empty($_SESSION['old'])) {
    $old = $_SESSION['old'];
    foreach ($fields as $f) { $vals[$f] = isset($old[$f]) ? $old[$f] : ''; }
    unset($_SESSION['old']);
} else {
    foreach ($fields as $f) { $vals[$f] = isset($_GET[$f]) ? $_GET[$f] : ''; }
}

// Si hay fecha_nacimiento la parseamos para rellenar día/mes/año por separado
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
    'password_mismatch' => 'Las contraseñas no coinciden.'
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
            </section>
        <?php endif; ?>

    <?php
    // Preparar datos para el formulario compartido
    $modo = 'registro';
    $usuario = [
        'usuario' => $vals['usuario'] ?? '',
        'email' => $vals['email'] ?? '',
        'sexo' => $vals['sexo'] ?? '',
        'fecha_nacimiento' => $vals['fecha_nacimiento'] ?? '',
        'ciudad' => $vals['ciudad'] ?? '',
        'pais' => $vals['pais'] ?? '',
        'foto' => ''
    ];
    $action = 'respuestaregistro.php';
    $submitText = 'Registrarme';
    require __DIR__ . '/includes/formulario_user.php';
    ?>

        <p class="registro-link"><strong>¿Ya tienes cuenta? </strong>
            <a href="inicio_sesion.php" class="boton-enlace">Inicia sesion aqui</a>
        </p>

    </main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
