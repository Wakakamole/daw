<?php
// respuestamisdatos.php — procesa la edición de datos del usuario
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/filtrado.php';
require_once __DIR__ . '/includes/basedatos.php';

if (!function_exists('h')) { function h($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); } }

// Verificar que el usuario está autenticado
$usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;
$usuario_sess = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';

if ($usuario_id <= 0 || $usuario_sess === '') {
    header("Location: http://{$_SERVER['HTTP_HOST']}/daw/login");
    exit;
}

// Obtener datos actuales del usuario
$db = get_db();
$stmt = $db->prepare("SELECT IdUsuario, NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto FROM usuarios WHERE IdUsuario = ? LIMIT 1");
if (!$stmt) {
    $_SESSION['errors'] = ['db_error'];
    header("Location: http://{$_SERVER['HTTP_HOST']}/daw/mis_datos");
    exit;
}
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$user_actual = $res ? $res->fetch_assoc() : null;
$res->free();
$stmt->close();

if (!$user_actual) {
    $_SESSION['errors'] = ['db_error'];
    header("Location: http://{$_SERVER['HTTP_HOST']}/daw/mis_datos");
    exit;
}

// Recoger y sanear campos
$password_actual = $_POST['password_actual'] ?? '';
$password_nuevo = $_POST['password'] ?? '';
$repite_nuevo = $_POST['repite'] ?? '';
$email = isset($_POST['email']) ? sanear_cadena($_POST['email']) : '';
$sexo = isset($_POST['sexo']) ? sanear_cadena($_POST['sexo']) : '';

// Gestionar fecha
$d = trim((string)($_POST['dia_nacimiento'] ?? ''));
$m = trim((string)($_POST['mes_nacimiento'] ?? ''));
$y = trim((string)($_POST['anio_nacimiento'] ?? ''));

$fecha = '';
$fecha_user_intenta_cambiar = false;
if ($d !== '' && $m !== '' && $y !== '') {
    $d = (int)$d;
    $m = (int)$m;
    $y = (int)$y;
    if ($d >= 1 && $d <= 31 && $m >= 1 && $m <= 12 && $y >= 1900 && $y <= 2100) {
        $fecha = sprintf('%04d-%02d-%02d', $y, $m, $d);
        $fecha_user_intenta_cambiar = true;
    }
}

$ciudad = isset($_POST['ciudad']) ? sanear_cadena($_POST['ciudad']) : '';
$pais = isset($_POST['pais']) ? sanear_cadena($_POST['pais']) : '';

$errors = [];

// 1. Validar contraseña actual
if (trim($password_actual) === '') {
    $errors[] = 'password_actual_empty';
} elseif (!password_verify($password_actual, $user_actual['Clave'])) {
    $errors[] = 'password_actual_invalid';
}

// 2. Validar nueva contraseña
if ($password_nuevo !== '') {
    $e = validar_contrasena($password_nuevo);
    if ($e !== null) $errors[] = $e;
    if (trim($repite_nuevo) === '') {
        $errors[] = 'repite_empty';
    } elseif ($password_nuevo !== $repite_nuevo) {
        $errors[] = 'password_mismatch';
    }
}

// 3. Validar email
if ($email !== $user_actual['Email']) {
    $e = validar_email($email);
    if ($e !== null) $errors[] = $e;
}

// 4. Validar sexo
if ($sexo !== '') {
    $e = validar_sexo($sexo);
    if ($e !== null) $errors[] = $e;
}

// 5. Validar fecha (si el usuario intenta cambiarla)
if ($fecha_user_intenta_cambiar && $fecha !== '') {
    $partes = explode('-', $fecha);
    if (count($partes) === 3) {
        list($anio, $mes, $dia) = $partes;
        if (!checkdate((int)$mes, (int)$dia, (int)$anio)) $errors[] = 'fecha_invalid';
        else {
            $fecha_nacimiento = DateTime::createFromFormat('Y-m-d', $fecha);
            $hoy = new DateTime();
            if ($hoy->diff($fecha_nacimiento)->y < 18) $errors[] = 'fecha_menor_18';
        }
    } else $errors[] = 'fecha_invalid';
}

// Si hay errores -> volver al formulario
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = [
        'usuario' => $user_actual['NomUsuario'],
        'email' => $email,
        'sexo' => $sexo,
        'fecha_nacimiento' => $fecha,
        'ciudad' => $ciudad,
        'pais' => $pais
    ];
    header("Location: http://{$_SERVER['HTTP_HOST']}/daw/mis_datos");
    exit;
}

// Comprobar unicidad del email
if ($email !== $user_actual['Email']) {
    $stmt = $db->prepare('SELECT IdUsuario FROM usuarios WHERE Email = ? AND IdUsuario != ? LIMIT 1');
    if (!$stmt) {
        $_SESSION['errors'] = ['db_error'];
        header("Location: http://{$_SERVER['HTTP_HOST']}/daw/mis_datos");
        exit;
    }
    $stmt->bind_param('si', $email, $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->fetch_assoc()) {
        $_SESSION['errors'] = ['email_exists'];
        $_SESSION['old'] = [
            'usuario' => $user_actual['NomUsuario'],
            'email' => $email,
            'sexo' => $sexo,
            'fecha_nacimiento' => $fecha,
            'ciudad' => $ciudad,
            'pais' => $pais
        ];
        $stmt->close();
        header("Location: http://{$_SERVER['HTTP_HOST']}/daw/mis_datos");
        exit;
    }
    $stmt->close();
}

// --- TRATAR FOTO ---
$foto = $user_actual['Foto'] ?? '';
$eliminar_foto = isset($_POST['eliminar_foto']) && $_POST['eliminar_foto'] == '1';
$foto_subida = false;
$foto_default = '/daw/img/usuarios/default.png';

$carpeta_fotos = __DIR__ . '/img/usuarios/';
$base_url_fotos = '/daw/img/usuarios/';

if (!is_dir($carpeta_fotos)) {
    mkdir($carpeta_fotos, 0755, true);
}


if ($eliminar_foto) {
    if ($foto && $foto !== $foto_default && file_exists(__DIR__ . $foto)) {
        unlink(__DIR__ . $foto);
    }
    $foto = $foto_default;
    $foto_subida = true;
} elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $archivo_tmp = $_FILES['foto']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $ext_permitidas = ['jpg','jpeg','png','gif','webp'];

    if (in_array($ext, $ext_permitidas)) {
        $nuevo_nombre = 'usuario_' . $usuario_id . '_' . time() . '.' . $ext;
        $destino = $carpeta_fotos . $nuevo_nombre;

        if (move_uploaded_file($archivo_tmp, $destino)) {
            if ($foto && $foto !== $foto_default && file_exists(__DIR__ . $foto)) {
                unlink(__DIR__ . $foto);
            }
            $foto = $base_url_fotos . $nuevo_nombre;
            $foto_subida = true;
        }
    } else {
        $errors[] = 'Formato de imagen no permitido';
    }
}

// --- CONSTRUIR UPDATE DINÁMICO ---
$campos = ['Email = ?'];
$valores = [$email];
$tipos = 's';

if ($sexo !== '') { $campos[] = 'Sexo = ?'; $valores[] = sexo_a_int($sexo); $tipos .= 'i'; }
if ($fecha_user_intenta_cambiar) { $campos[] = 'FNacimiento = ?'; $valores[] = $fecha; $tipos .= 's'; }
$campos[] = 'Ciudad = ?'; $valores[] = $ciudad; $tipos .= 's';
if ($pais !== '') { $campos[] = 'Pais = ?'; $valores[] = (int)$pais; $tipos .= 'i'; }
if ($password_nuevo !== '') { $campos[] = 'Clave = ?'; $valores[] = password_hash($password_nuevo, PASSWORD_DEFAULT); $tipos .= 's'; }
if ($foto_subida) { $campos[] = 'Foto = ?'; $valores[] = $foto; $tipos .= 's'; }

$valores[] = $usuario_id;
$tipos .= 'i';

$stmt = $db->prepare('UPDATE usuarios SET ' . implode(', ', $campos) . ' WHERE IdUsuario = ?');
$stmt->bind_param($tipos, ...$valores);
$stmt->execute();
$stmt->close();

// Invalidar cookies si cambió contraseña
if ($password_nuevo !== '') {
    setcookie('remember', '', time() - 3600, '/', '', false, true);
    setcookie('remember_user', '', time() - 3600, '/', '', false, true);
}

// Re-obtener datos actualizados
$stmt_refresh = $db->prepare("SELECT IdUsuario, NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais, Foto FROM usuarios WHERE IdUsuario = ? LIMIT 1");
$stmt_refresh->bind_param('i', $usuario_id);
$stmt_refresh->execute();
$res_refresh = $stmt_refresh->get_result();
$user_actualizado = $res_refresh ? $res_refresh->fetch_assoc() : $user_actual;
$res_refresh->free();
$stmt_refresh->close();
$_SESSION['foto'] = $user_actualizado['Foto'] ?: '/daw/img/usuarios/default.png';


// Variables para mostrar
$email = $user_actualizado['Email'] ?? $email;
$sexo = $user_actualizado['Sexo'] ?? '';
if ($sexo !== '' && is_numeric($sexo)) {
    $sexo = match((int)$sexo) {
        1 => 'hombre',
        2 => 'mujer',
        3 => 'otro',
        default => ''
    };
}
$fecha = $user_actualizado['FNacimiento'] ?? '';
$ciudad = $user_actualizado['Ciudad'] ?? $ciudad;
$pais = $user_actualizado['Pais'] ?? $pais;
$foto = $user_actualizado['Foto'] ?: $foto_default;

// Mostrar página de éxito
$page_title = 'INMOLINK - Datos actualizados';
require_once __DIR__ . '/includes/cabecera.php';
?>

<main>
    <article class="mensaje-contenedor">
        <header>
            <h1>¡Datos actualizados!</h1>
            <p class="lead">Tus datos han sido modificados correctamente.</p>
        </header>

        <section aria-labelledby="datos-actualizados">
            <h2 id="datos-actualizados">Datos del usuario</h2>
            <dl>
                <dt>Usuario</dt>
                <dd><strong><?php echo h($user_actual['NomUsuario']); ?></strong></dd>

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

                <dt>Foto</dt>
                <dd><img src="<?php echo h($foto); ?>" alt="Foto de perfil" style="max-width:150px;"></dd>
            </dl>
        </section>

        <section>
            <p><em>Por seguridad, la contraseña no se muestra en esta página.</em></p>
            <p>
                <a href="/daw/mi_perfil" class="boton-enlace">Volver al perfil</a>
                &nbsp;·&nbsp;
                <a href="/daw/mis_datos" class="boton-enlace">Editar de nuevo</a>
            </p>
        </section>
    </article>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
