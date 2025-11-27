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
$stmt = $db->prepare("SELECT IdUsuario, NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais FROM usuarios WHERE IdUsuario = ? LIMIT 1");
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
$password_actual = isset($_POST['password_actual']) ? $_POST['password_actual'] : '';
$password_nuevo = isset($_POST['password']) ? $_POST['password'] : '';
$repite_nuevo = isset($_POST['repite']) ? $_POST['repite'] : '';
$email = isset($_POST['email']) ? sanear_cadena($_POST['email']) : '';
$sexo = isset($_POST['sexo']) ? sanear_cadena($_POST['sexo']) : '';

// Gestionar fecha: 
// - Si el usuario rellenó día/mes/año (intenta cambiar), validar
// - Si dejó los campos vacíos, no validar ni actualizar
$d = trim((string)($_POST['dia_nacimiento'] ?? ''));
$m = trim((string)($_POST['mes_nacimiento'] ?? ''));
$y = trim((string)($_POST['anio_nacimiento'] ?? ''));

$fecha = '';
$fecha_user_intenta_cambiar = false;

// Solo si el usuario rellenó TODOS los campos de fecha (intenta cambiarla)
if ($d !== '' && $m !== '' && $y !== '') {
    // Sanitizar: quitar espacios y asegurar formato numérico
    $d = (int)$d;
    $m = (int)$m;
    $y = (int)$y;
    
    // Validar rangos básicos
    if ($d >= 1 && $d <= 31 && $m >= 1 && $m <= 12 && $y >= 1900 && $y <= 2100) {
        // Reconstruir con formato YYYY-MM-DD
        $fecha = sprintf('%04d-%02d-%02d', $y, $m, $d);
        $fecha_user_intenta_cambiar = true;
    }
}

$ciudad = isset($_POST['ciudad']) ? sanear_cadena($_POST['ciudad']) : '';
$pais = isset($_POST['pais']) ? sanear_cadena($_POST['pais']) : '';

$errors = [];

// 1. Validar que la contraseña actual es correcta
if (trim($password_actual) === '') {
    $errors[] = 'password_actual_empty';
} elseif (!password_verify($password_actual, $user_actual['Clave'])) {
    $errors[] = 'password_actual_invalid';
}

// 2. Si hay nueva contraseña, validar
if ($password_nuevo !== '') {
    $e = validar_contrasena($password_nuevo);
    if ($e !== null) $errors[] = $e;
    if (trim($repite_nuevo) === '') {
        $errors[] = 'repite_empty';
    } elseif ($password_nuevo !== $repite_nuevo) {
        $errors[] = 'password_mismatch';
    }
}

// 3. Validar email (si cambió)
if ($email !== $user_actual['Email']) {
    $e = validar_email($email);
    if ($e !== null) $errors[] = $e;
}

// 4. Validar sexo
if ($sexo !== '') {
    $e = validar_sexo($sexo);
    if ($e !== null) $errors[] = $e;
}

// 5. Validar fecha (OPCIONAL en edición, pero si el usuario intenta cambiarla debe ser válida y mayor de 18)
if ($fecha_user_intenta_cambiar && $fecha !== '') {
    // Validar que la fecha sea correcta usando checkdate
    $partes = explode('-', $fecha);
    if (count($partes) === 3) {
        list($anio, $mes, $dia) = $partes;
        $anio = (int)$anio;
        $mes = (int)$mes;
        $dia = (int)$dia;
        
        // checkdate devuelve false si la fecha es inválida
        if (checkdate($mes, $dia, $anio)) {
            // Fecha válida, comprobar que es mayor de 18 años
            $fecha_nacimiento = DateTime::createFromFormat('Y-m-d', $fecha);
            $hoy = new DateTime();
            $diff = $hoy->diff($fecha_nacimiento);
            if ($diff->y < 18) {
                $errors[] = 'fecha_menor_18';
            }
        } else {
            $errors[] = 'fecha_invalid';
        }
    } else {
        $errors[] = 'fecha_invalid';
    }
}

// Si hay errores -> devolver al formulario
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

// Comprobar unicidad del email (si cambió)
if ($email !== $user_actual['Email']) {
    try {
        $stmt = $db->prepare('SELECT IdUsuario FROM usuarios WHERE Email = ? AND IdUsuario != ? LIMIT 1');
        if (!$stmt) throw new Exception('Error preparando consulta');
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
    } catch (Exception $ex) {
        error_log('Error email check: ' . $ex->getMessage());
        $_SESSION['errors'] = ['db_error'];
        header("Location: http://{$_SERVER['HTTP_HOST']}/daw/mis_datos");
        exit;
    }
}

// Actualizar en BD
try {
    // Preparar datos para actualización
    $sexo_int = ($sexo === '') ? null : sexo_a_int($sexo);
    // SOLO actualiza la fecha si el usuario intentó cambiarla (rellenó los campos)
    $actualizar_fecha = $fecha_user_intenta_cambiar && ($fecha !== '');
    $fn = $actualizar_fecha ? $fecha : null;
    $pais_int = ($pais === '') ? null : ((int)$pais ?: null);

    // Construir UPDATE dinámicamente según qué cambios hay
    $campos = ['Email = ?'];
    $valores = [$email];
    $tipos = 's';
    
    if ($sexo !== '') {
        $campos[] = 'Sexo = ?';
        $valores[] = $sexo_int;
        $tipos .= 'i';
    }
    
    if ($actualizar_fecha) {
        $campos[] = 'FNacimiento = ?';
        $valores[] = $fn;
        $tipos .= 's';
    }
    
    $campos[] = 'Ciudad = ?';
    $valores[] = $ciudad;
    $tipos .= 's';
    
    if ($pais !== '') {
        $campos[] = 'Pais = ?';
        $valores[] = $pais_int;
        $tipos .= 'i';
    }
    
    if ($password_nuevo !== '') {
        $hash = password_hash($password_nuevo, PASSWORD_DEFAULT);
        $campos[] = 'Clave = ?';
        $valores[] = $hash;
        $tipos .= 's';
    }
    
    // Agregar el ID al final
    $valores[] = $usuario_id;
    $tipos .= 'i';
    
    $query = 'UPDATE usuarios SET ' . implode(', ', $campos) . ' WHERE IdUsuario = ?';
    $stmt = $db->prepare($query);
    if (!$stmt) throw new Exception('Error preparando update: ' . $db->error);
    
    $stmt->bind_param($tipos, ...$valores);
    
    if (!$stmt->execute()) throw new Exception('Error al actualizar: ' . $stmt->error);
    $stmt->close();

    // Si se cambió la contraseña, invalidar la cookie "Recuérdame" por seguridad
    if ($password_nuevo !== '') {
        setcookie('remember', '', time() - 3600, '/', '', false, true);
        setcookie('remember_user', '', time() - 3600, '/', '', false, true);
    }

    // Re-obtener los datos actualizados de la BD para mostrar en la página de éxito
    $stmt_refresh = $db->prepare("SELECT IdUsuario, NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais FROM usuarios WHERE IdUsuario = ? LIMIT 1");
    if ($stmt_refresh) {
        $stmt_refresh->bind_param('i', $usuario_id);
        $stmt_refresh->execute();
        $res_refresh = $stmt_refresh->get_result();
        $user_actualizado = $res_refresh ? $res_refresh->fetch_assoc() : $user_actual;
        if ($res_refresh) { $res_refresh->free(); }
        $stmt_refresh->close();
    } else {
        $user_actualizado = $user_actual;
    }

    // Actualizar las variables para mostrar en la página de éxito
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

} catch (Exception $ex) {
    error_log('Error actualización datos: ' . $ex->getMessage());
    $_SESSION['errors'] = ['db_error'];
    header("Location: http://{$_SERVER['HTTP_HOST']}/daw/mis_datos");
    exit;
}

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
