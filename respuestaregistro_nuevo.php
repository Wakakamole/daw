<?php
// respuestaregistro_nuevo.php — procesa el formulario de registro (versión limpia)
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/includes/filtrado.php';
require_once __DIR__ . '/includes/basedatos.php';

if (!function_exists('h')) { function h($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); } }

// Recoger y sanear campos
$usuario = isset($_POST['usuario']) ? sanear_cadena($_POST['usuario']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$repite = isset($_POST['repite']) ? $_POST['repite'] : '';
$email = isset($_POST['email']) ? sanear_cadena($_POST['email']) : '';
$sexo = isset($_POST['sexo']) ? sanear_cadena($_POST['sexo']) : '';
$fecha = isset($_POST['fecha_nacimiento']) ? sanear_cadena($_POST['fecha_nacimiento']) : '';
// aceptar día/mes/año si no vino el hidden
if (empty($fecha) && isset($_POST['dia_nacimiento'], $_POST['mes_nacimiento'], $_POST['anio_nacimiento'])) {
    $d = str_pad((string)($_POST['dia_nacimiento'] ?? ''), 2, '0', STR_PAD_LEFT);
    $m = str_pad((string)($_POST['mes_nacimiento'] ?? ''), 2, '0', STR_PAD_LEFT);
    $y = trim((string)($_POST['anio_nacimiento'] ?? ''));
    if ($d !== '' && $m !== '' && $y !== '') { $fecha = "$y-$m-$d"; }
}
$ciudad = isset($_POST['ciudad']) ? sanear_cadena($_POST['ciudad']) : '';
$pais = isset($_POST['pais']) ? sanear_cadena($_POST['pais']) : '';

$errors = [];

// Validaciones con las funciones del include
$e = validar_usuario($usuario); if ($e !== null) $errors[] = $e;
$e = validar_contrasena($password); if ($e !== null) $errors[] = $e;
if (trim($repite) === '') $errors[] = 'repite_empty'; elseif ($password !== $repite) $errors[] = 'password_mismatch';
$e = validar_email($email); if ($e !== null) $errors[] = $e;
$e = validar_sexo($sexo); if ($e !== null) $errors[] = $e;
$e = validar_fecha_mayor_18($fecha); if ($e !== null) $errors[] = $e;

// Si hay errores -> devolver al formulario con mensajes y valores previos
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = [ 'usuario'=>$usuario, 'email'=>$email, 'sexo'=>$sexo, 'fecha_nacimiento'=>$fecha, 'ciudad'=>$ciudad, 'pais'=>$pais ];
    $host = $_SERVER['HTTP_HOST'];
    header("Location: http://$host/daw/registro");
    exit;
}

// Comprobar unicidad y guardar en BD
try {
    $db = get_db();
    $stmt = $db->prepare('SELECT IdUsuario, NomUsuario, Email FROM usuarios WHERE NomUsuario = ? OR Email = ? LIMIT 1');
    if (!$stmt) throw new Exception('Error preparando consulta');
    $stmt->bind_param('ss', $usuario, $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $row = $res->fetch_assoc()) {
        // Determinar si coincide usuario o email
        if (isset($row['NomUsuario']) && strtolower($row['NomUsuario']) === strtolower($usuario)) {
            $_SESSION['errors'] = ['usuario_exists'];
        } else {
            $_SESSION['errors'] = ['email_exists'];
        }
        $_SESSION['old'] = [ 'usuario'=>$usuario, 'email'=>$email, 'sexo'=>$sexo, 'fecha_nacimiento'=>$fecha, 'ciudad'=>$ciudad, 'pais'=>$pais ];
        $stmt->close();
        header("Location: http://{$_SERVER['HTTP_HOST']}/daw/registro");
        exit;
    }
    $stmt->close();

    // Insertar nuevo usuario
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sexo_int = sexo_a_int($sexo);
    $fn = ($fecha === '') ? null : $fecha;
    $pais_int = ($pais === '') ? null : ((int)$pais ?: null);
    $foto = null;

    $ins = $db->prepare('INSERT INTO usuarios (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    if (!$ins) throw new Exception('Error preparando insert: ' . $db->error);
    $ins->bind_param('sssissis', $usuario, $hash, $email, $sexo_int, $fn, $ciudad, $pais_int, $foto);
    if (!$ins->execute()) throw new Exception('Error insert: ' . $ins->error);
    $ins->close();

} catch (Exception $ex) {
    error_log('Error registro: ' . $ex->getMessage());
    $_SESSION['errors'] = ['db_error'];
    $_SESSION['old'] = [ 'usuario'=>$usuario, 'email'=>$email, 'sexo'=>$sexo, 'fecha_nacimiento'=>$fecha, 'ciudad'=>$ciudad, 'pais'=>$pais ];
    header("Location: http://{$_SERVER['HTTP_HOST']}/daw/registro");
    exit;
}

// Mostrar página de éxito
$page_title = 'INMOLINK - Registro';
require_once __DIR__ . '/includes/cabecera.php';
?>

    <main>
        <article class="mensaje-contenedor">
            <header>
                <h1>¡Registro completado!</h1>
                <p class="lead">Tu cuenta ha sido creada correctamente. Ya puedes iniciar sesión.</p>
            </header>

            <section aria-labelledby="datos-registro">
                <h2 id="datos-registro">Datos del usuario</h2>
                <dl>
                    <dt>Usuario</dt>
                    <dd><strong><?php echo h($usuario); ?></strong></dd>

                    <dt>Correo electrónico</dt>
                    <dd><?php echo h($email ?: '—'); ?></dd>

                    <dt>Sexo</dt>
                    <dd><?php echo h($sexo ?: '—'); ?></dd>

                    <dt>Fecha de nacimiento</dt>
                    <dd><?php echo h($fecha ?: '—'); ?></dd>

                    <dt>Ciudad</dt>
                    <dd><?php echo h($ciudad ?: '—'); ?></dd>

                    <dt>País</dt>
                    <dd><?php echo h($pais ?: '—'); ?></dd>
                </dl>
            </section>

            <section>
                <p><em>Por seguridad, la contraseña y la foto no se muestran ni se transfieren en esta página.</em></p>
                <p>
                    <a href="/daw/login" class="boton-enlace">Iniciar sesión</a>
                    &nbsp;·&nbsp;
                    <a href="/daw/" class="boton-enlace">Volver al inicio</a>
                </p>
            </section>
        </article>
    </main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
