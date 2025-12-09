<?php
// respuesta_baja.php — procesa la eliminación de la cuenta del usuario
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/basedatos.php';
require_once __DIR__ . '/includes/filtrado.php';

if (!function_exists('h')) { function h($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); } }

// Verificar que el usuario está autenticado
$usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;
$usuario_sess = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';

if ($usuario_id <= 0 || $usuario_sess === '') {
    header("Location: http://{$_SERVER['HTTP_HOST']}/daw/login");
    exit;
}

$db = get_db();

// PASO 2: Confirmar baja (POST con contraseña)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_baja'])) {
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $errors = [];

    // Validar contraseña
    if (trim($password) === '') {
        $errors[] = 'password_empty';
    } else {
        // Obtener contraseña actual del usuario
        try {
            $stmt = $db->prepare('SELECT Clave FROM usuarios WHERE IdUsuario = ? LIMIT 1');
            if (!$stmt) throw new Exception('Error preparando consulta');
            $stmt->bind_param('i', $usuario_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $res->free();
            $stmt->close();

            if (!$row) {
                $errors[] = 'user_not_found';
            } elseif (!password_verify($password, $row['Clave'])) {
                $errors[] = 'password_invalid';
            }
        } catch (Exception $ex) {
            error_log('Error verificando contraseña: ' . $ex->getMessage());
            $errors[] = 'db_error';
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: http://{$_SERVER['HTTP_HOST']}/daw/respuesta_baja");
        exit;
    }

    // borrar usuario y todos sus datos
    // EN BD existe ON DELETE CASCADE
    // - X usuario automaticamente se eliminan sus anuncios
    // - X anuncios automaticamente se eliminan sus fotos
    // - Los mensajes se ponen a null () ON DELETE SET NULL no se eliminan pero no tienen usuario asociado
    try {
        // ULTIMA PRÄCTICA AÑADIDO : Primero borrar ficheros, luego registros de BD
        // Obtener todos los anuncios del usuario
        $stmt = $db->prepare('SELECT IdAnuncio FROM anuncios WHERE Usuario = ?');
        if (!$stmt) throw new Exception('Error preparando select anuncios');
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        // Para cada anuncio, obtener y borrar sus fotos
        while ($anuncio = $res->fetch_assoc()) {
            $id_anuncio = $anuncio['IdAnuncio'];
            
            // Obtener todas las fotos del anuncio
            $stmt_fotos = $db->prepare('SELECT Foto FROM fotos WHERE Anuncio = ?');
            if (!$stmt_fotos) throw new Exception('Error preparando select fotos');
            $stmt_fotos->bind_param('i', $id_anuncio);
            $stmt_fotos->execute();
            $res_fotos = $stmt_fotos->get_result();
            
            // Borrar cada fichero del servidor
            while ($foto = $res_fotos->fetch_assoc()) {
                $ruta_foto = __DIR__ . '/' . $foto['Foto'];
                if (file_exists($ruta_foto)) {
                    @unlink($ruta_foto);
                }
            }
            $stmt_fotos->close();
        }
        $stmt->close();
        
        // Borrar usuario (CASCADE eliminará anuncios, y por CASCADE también fotos)
        $stmt = $db->prepare('DELETE FROM usuarios WHERE IdUsuario = ?');
        if (!$stmt) throw new Exception('Error preparando delete usuario');
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();
        $stmt->close();

        // Destruir sesión
        session_unset();
        session_destroy();

        // Mostrar página de éxito
        $page_title = 'INMOLINK - Cuenta eliminada';
        require_once __DIR__ . '/includes/cabecera.php';
        ?>

        <main>
            <article class="mensaje-contenedor">
                <header>
                    <h1>¡Cuenta eliminada!</h1>
                    <p class="lead">Tu cuenta ha sido eliminada correctamente junto con todos tus datos.</p>
                </header>

                <section>
                    <p>Lamentamos que te vayas. Si cambias de opinión, puedes registrarte de nuevo.</p>
                    <p>
                        <a href="/daw/" class="boton-enlace">Volver al inicio</a>
                    </p>
                </section>
            </article>
        </main>

        <?php require_once __DIR__ . '/includes/pie.php'; ?>
        <?php
        exit;

    } catch (Exception $ex) {
        error_log('Error al eliminar usuario: ' . $ex->getMessage());
        try { $db->rollback(); } catch (Exception $e) {}
        $_SESSION['errors'] = ['db_error'];
        header("Location: http://{$_SERVER['HTTP_HOST']}/daw/respuesta_baja");
        exit;
    }
}

// Mostrar confirmación con resumen de datos
try {
    // Obtener anuncios del usuario
    $stmt = $db->prepare('SELECT IdAnuncio, Titulo FROM anuncios WHERE Usuario = ? ORDER BY IdAnuncio');
    if (!$stmt) throw new Exception('Error preparando select anuncios');
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $anuncios = [];
    while ($row = $res->fetch_assoc()) {
        $anuncios[] = $row;
    }
    $res->free();
    $stmt->close();

    // Contar fotos por anuncio
    $anuncios_detalle = [];
    $total_fotos = 0;
    
    if (!empty($anuncios)) {
        $stmt_fotos = $db->prepare('SELECT COUNT(*) as num_fotos FROM fotos WHERE Anuncio = ?');
        if (!$stmt_fotos) throw new Exception('Error preparando count fotos');
        
        foreach ($anuncios as $anuncio) {
            $id_anuncio = (int)$anuncio['IdAnuncio'];
            $stmt_fotos->bind_param('i', $id_anuncio);
            $stmt_fotos->execute();
            $res_fotos = $stmt_fotos->get_result();
            $row_fotos = $res_fotos->fetch_assoc();
            $num_fotos = (int)($row_fotos['num_fotos'] ?? 0);
            $res_fotos->free();
            
            $anuncios_detalle[] = [
                'id' => $id_anuncio,
                'titulo' => $anuncio['Titulo'],
                'fotos' => $num_fotos
            ];
            $total_fotos += $num_fotos;
        }
        $stmt_fotos->close();
    }

    $num_anuncios = count($anuncios);

} catch (Exception $ex) {
    error_log('Error obteniendo resumen: ' . $ex->getMessage());
    $anuncios_detalle = [];
    $num_anuncios = 0;
    $total_fotos = 0;
}

// Gestionar mensajes de error
$errors = [];
if (!empty($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}

$mensajes = [
    'password_empty' => 'Debes introducir tu contraseña actual.',
    'password_invalid' => 'La contraseña actual es incorrecta.',
    'user_not_found' => 'Usuario no encontrado.',
    'db_error' => 'Error interno. Inténtalo más tarde.'
];

// Página de confirmación
$page_title = 'INMOLINK - Dar de baja';
require_once __DIR__ . '/includes/cabecera.php';
?>

<main>
    <article class="mensaje-contenedor">
        <header>
            <h1>Dar de baja tu cuenta</h1>
            <p class="lead"><strong>Advertencia:</strong> Esta acción es irreversible. Se eliminarán tu cuenta y todos tus datos.</p>
        </header>

        <?php if (!empty($errors)): ?>
            <section class="error-summary" role="alert" aria-live="assertive">
                <p><strong>Error:</strong></p>
                <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?php echo h(isset($mensajes[$e]) ? $mensajes[$e] : $e); ?></li>
                <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <section aria-labelledby="resumen-datos">
            <h2 id="resumen-datos">Resumen de datos que se eliminarán</h2>
            <dl>
                <dt>Total de anuncios</dt>
                <dd><strong><?php echo (int)$num_anuncios; ?></strong></dd>

                <dt>Total de fotos</dt>
                <dd><strong><?php echo (int)$total_fotos; ?></strong></dd>
            </dl>

            <?php if (!empty($anuncios_detalle)): ?>
                <h3>Detalles por anuncio</h3>
                <ul>
                <?php foreach ($anuncios_detalle as $ad): ?>
                    <li>
                        <strong><?php echo h($ad['titulo']); ?></strong>
                        — <?php echo (int)$ad['fotos']; ?> foto<?php echo $ad['fotos'] !== 1 ? 's' : ''; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>No tienes anuncios registrados.</em></p>
            <?php endif; ?>
        </section>

        <section aria-labelledby="confirmar-baja">
            <h2 id="confirmar-baja">Confirmar baja</h2>
            <p>Para confirmar la eliminación de tu cuenta, introduce tu contraseña actual:</p>
            
            <form action="/daw/respuesta_baja" method="post" novalidate>
                <label for="password">Contraseña actual:</label>
                <input type="password" id="password" name="password" required>
                <br><br>

                <div class="contenedor-botones-baja">
                    <button type="submit" name="confirmar_baja" value="1" class="boton-baja-peligro">
                         Eliminar mi cuenta de forma permanente
                    </button>
                    <a href="/daw/mi_perfil" class="boton-enlace">Cancelar</a>
                </div>
            </form>
        </section>
    </article>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
