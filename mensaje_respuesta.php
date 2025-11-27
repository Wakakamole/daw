<?php
// mensaje_respuesta.php — procesa el envío de mensajes entre usuarios
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/includes/basedatos.php';
require_once __DIR__ . '/includes/cabecera.php';

if (!function_exists('h')) { function h($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); } }

// Validar que el usuario está autenticado (el remitente debe ser un usuario registrado)
$usuario_origen_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;
$usuario_origen_nombre = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';

// Si no hay sesión, permitir mensaje anónimo pero validar
$es_anonimo = ($usuario_origen_id <= 0 || $usuario_origen_nombre === '');

// Recoger y sanear datos del formulario
$tipo_id = isset($_POST['tipo']) ? (int)$_POST['tipo'] : 0;
$texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';
$nombre_remitente = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$anuncio_id = isset($_POST['anuncio_id']) ? (int)$_POST['anuncio_id'] : 0;

$errors = [];

// Validaciones
if ($tipo_id <= 0) {
    $errors[] = 'tipo_invalid';
}

if (strlen($texto) === 0) {
    $errors[] = 'texto_empty';
} elseif (strlen($texto) > 4000) {
    $errors[] = 'texto_long';
}

if ($anuncio_id <= 0) {
    $errors[] = 'anuncio_invalid';
}

// Si es anónimo, validar nombre
if ($es_anonimo && strlen($nombre_remitente) === 0) {
    $errors[] = 'nombre_empty';
} elseif ($es_anonimo && strlen($nombre_remitente) > 100) {
    $errors[] = 'nombre_long';
}

// Si hay errores, mostrar y volver
if (!empty($errors)) {
    try {
        $db = get_db();
        
        // Obtener nombre del tipo de mensaje para mostrar
        $tipo_nombre = '';
        $stmt = $db->prepare('SELECT NomTMensaje FROM tiposmensajes WHERE IdTMensaje = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('i', $tipo_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($r = $res->fetch_assoc()) $tipo_nombre = $r['NomTMensaje'];
            $res->free();
            $stmt->close();
        }
    } catch (Exception $e) {
        $tipo_nombre = '';
    }

    $page_title = 'INMOLINK - Error al enviar mensaje';
    ?>
    <main>
        <article class="mensaje-contenedor">
            <header>
                <h1>Error al enviar mensaje</h1>
            </header>
            
            <section role="alert" aria-live="assertive">
                <p><strong>Se han encontrado los siguientes errores:</strong></p>
                <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?php echo h($e); ?></li>
                <?php endforeach; ?>
                </ul>
            </section>

            <section>
                <p>
                    <a href="/daw/mensaje?id=<?php echo (int)$anuncio_id; ?>" class="boton-enlace">Volver</a>
                </p>
            </section>
        </article>
    </main>
    <?php
    require_once __DIR__ . '/includes/pie.php';
    exit;
}

// Procesar inserción del mensaje
try {
    $db = get_db();

    // Validar que el tipo de mensaje existe
    $stmt = $db->prepare('SELECT IdTMensaje FROM tiposmensajes WHERE IdTMensaje = ? LIMIT 1');
    if (!$stmt) throw new Exception('Error preparando validación tipo');
    $stmt->bind_param('i', $tipo_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!($res && $res->fetch_assoc())) {
        $res->free();
        $stmt->close();
        $errors[] = 'tipo_not_found';
        $_SESSION['errors'] = $errors;
        header("Location: http://{$_SERVER['HTTP_HOST']}/daw/mensaje?id=$anuncio_id");
        exit;
    }
    $res->free();
    $stmt->close();

    // Validar que el anuncio existe y obtener el propietario
    $stmt = $db->prepare('SELECT IdAnuncio, Usuario, Titulo FROM anuncios WHERE IdAnuncio = ? LIMIT 1');
    if (!$stmt) throw new Exception('Error preparando validación anuncio');
    $stmt->bind_param('i', $anuncio_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    if (!$row) {
        if ($res) $res->free();
        $stmt->close();
        $errors[] = 'anuncio_not_found';
        $_SESSION['errors'] = $errors;
        header("Location: http://{$_SERVER['HTTP_HOST']}/daw/detalle_anuncio?id=$anuncio_id");
        exit;
    }
    $usuario_destino_id = (int)$row['Usuario'];
    $titulo_anuncio = $row['Titulo'] ?? '';
    $res->free();
    $stmt->close();

    // No permitir que un usuario envíe un mensaje a su propio anuncio
    if (!$es_anonimo && $usuario_origen_id === $usuario_destino_id) {
        $errors[] = 'same_user';
        $_SESSION['errors'] = $errors;
        header("Location: http://{$_SERVER['HTTP_HOST']}/daw/mensaje?id=$anuncio_id");
        exit;
    }

    // Insertar el mensaje
    $stmt = $db->prepare('INSERT INTO mensajes (TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino, FRegistro) VALUES (?, ?, ?, ?, ?, NOW())');
    if (!$stmt) throw new Exception('Error preparando insert mensaje');
    
    // Si es anónimo, UsuOrigen es NULL
    $usuario_origen = $es_anonimo ? null : $usuario_origen_id;
    $stmt->bind_param('isiii', $tipo_id, $texto, $anuncio_id, $usuario_origen, $usuario_destino_id);
    
    if (!$stmt->execute()) throw new Exception('Error ejecutando insert: ' . $stmt->error);
    $stmt->close();

} catch (Exception $ex) {
    error_log('Error mensaje_respuesta: ' . $ex->getMessage());
    $errors[] = 'db_error';
    $_SESSION['errors'] = $errors;
    header("Location: http://{$_SERVER['HTTP_HOST']}/daw/mensaje?id=$anuncio_id");
    exit;
}

// Mostrar página de éxito
$page_title = 'INMOLINK - Mensaje enviado';
?>

<main>
    <article class="mensaje-contenedor">
        <header>
            <h1>¡Mensaje enviado!</h1>
            <p class="lead">Tu mensaje ha sido enviado correctamente al anunciante.</p>
        </header>

        <section aria-labelledby="datos-envio">
            <h2 id="datos-envio">Resumen del mensaje</h2>
            <dl>
                <dt>Remitente</dt>
                <dd><?php echo h($es_anonimo ? $nombre_remitente : $usuario_origen_nombre); ?></dd>

                <dt>Anuncio</dt>
                <dd><?php echo h($titulo_anuncio); ?></dd>

                <dt>Mensaje (primeros 100 caracteres)</dt>
                <dd><?php echo h(substr($texto, 0, 100)) . (strlen($texto) > 100 ? '...' : ''); ?></dd>
            </dl>
        </section>

        <section>
            <p>
                <a href="/daw/detalle_anuncio?id=<?php echo (int)$anuncio_id; ?>" class="boton-enlace">Volver al anuncio</a>
                &nbsp;·&nbsp;
                <a href="/daw/inicio_user" class="boton-enlace">Volver al inicio</a>
            </p>
        </section>
    </article>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>