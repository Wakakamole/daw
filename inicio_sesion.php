<?php
require_once __DIR__ . '/includes/session.php';

// Mostrar y consumir flashdata o errores si existen
$flash = '';
$errors = [];
if (!empty($_SESSION['flash'])) {
  $flash = $_SESSION['flash'];
  unset($_SESSION['flash']);
}
if (!empty($_SESSION['errors'])) {
  // errores ya preparados por otros controladores (registro o login)
  $errors = $_SESSION['errors'];
  unset($_SESSION['errors']);
}
// Si había flash simple, añadirlo al array de errores para mostrar el mismo bloque
if ($flash !== '') {
  // si $errors es asociativo, mantener valores; si es lista, añadir
  if (array_values($errors) === $errors) {
    $errors[] = $flash;
  } else {
    // convertir asociativo a lista de mensajes
    $errors = array_merge(array_values($errors), [$flash]);
  }
}

// Prefill del checkbox 'remember' desde cookie booleana
$checked = isset($_COOKIE['remember']) && $_COOKIE['remember'] === '1' ? 'checked' : '';

$page_title = 'INMOLINK - Inicio de Sesión';
require_once __DIR__ . '/includes/cabecera.php';
?>

<main>
  <h1>INICIAR SESIÓN</h1>

  <?php if (!empty($errors)): ?>
    <section class="error-summary" role="alert" aria-live="assertive">
        <p><strong>Se han encontrado errores en el formulario:</strong></p>
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
  <?php endif; ?>

  <?php if (!empty($_SESSION['remember_message'])): ?>
    <section class="info-recuerdo" role="status">
        <?php echo htmlspecialchars($_SESSION['remember_message'], ENT_QUOTES, 'UTF-8'); ?>
    </section>
    <?php unset($_SESSION['remember_message']); ?>
  <?php endif; ?>

  <form id="loginForm" action="control_acceso.php" method="post" novalidate>
    <fieldset>
      <legend>Datos de acceso</legend>

      <label for="usuario">Usuario:</label>
      <input type="text" id="usuario" name="usuario" placeholder="Tu usuario" value="">
      <br><br>

      <label for="contrasena">Contraseña:</label>
      <input type="password" id="contrasena" name="clave" placeholder="••••••" value="">
      <br><br>

      <label>
        <input type="checkbox" name="remember" value="1" <?php echo $checked; ?>> Recuérdame
      </label>
      <br><br>

      <button type="submit">Iniciar Sesión</button>
    </fieldset>
  </form>

  <p><strong>¿No tienes una cuenta?</strong> <a href="registro_usuario.php">Regístrate aquí</a></p>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
