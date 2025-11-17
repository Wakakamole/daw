<?php

require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/basedatos.php';
require_once __DIR__ . '/includes/cabecera.php';

$db = get_db();

function h($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$usuario_nombre = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';
$usuario_id = 0;
if ($usuario_nombre !== '') {
    $stmt = $db->prepare("SELECT IdUsuario FROM usuarios WHERE NomUsuario = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $usuario_nombre);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        if ($row) { $usuario_id = (int)$row['IdUsuario']; }
        if ($res) { $res->free(); }
        $stmt->close();
    }
}

$anuncio_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($anuncio_id <= 0) {
    echo "<main><p>ID de anuncio no válido.</p></main>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}

// Obtener datos básicos del anuncio y comprobar propietario
$anuncio = null;
$stmt = $db->prepare("SELECT IdAnuncio, Titulo, FPrincipal, Ciudad, Precio, Usuario FROM anuncios WHERE IdAnuncio = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param('i', $anuncio_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $anuncio = $res ? $res->fetch_assoc() : null;
    if ($res) { $res->free(); }
    $stmt->close();
}

if (!$anuncio) {
    echo "<main><p>Anuncio no encontrado.</p></main>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}

// comprobar que el usuario conectado es propietario del anuncio
if ($usuario_id <= 0 || (int)$anuncio['Usuario'] !== $usuario_id) {
    echo "<main><p>No tienes permiso para ver los mensajes de este anuncio.</p></main>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}

// Contar mensajes recibidos para este anuncio
$total_mensajes = 0;
$stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM mensajes WHERE Anuncio = ?");
if ($stmt) {
    $stmt->bind_param('i', $anuncio_id);
    $stmt->execute();
    $cnt = $stmt->get_result()->fetch_assoc();
    $total_mensajes = (int)$cnt['cnt'];
    $stmt->close();
}

// Obtener lista de mensajes (remitente, tipo, texto, fecha)
$mensajes = [];
$sql = "SELECT m.IdMensaje, m.Texto, m.FRegistro, tm.NomTMensaje AS TipoNombre, uorig.NomUsuario AS OrigenNombre
        FROM mensajes m
        LEFT JOIN tiposMensajes tm ON m.TMensaje = tm.IdTMensaje
        LEFT JOIN usuarios uorig ON m.UsuOrigen = uorig.IdUsuario
        WHERE m.Anuncio = ?
        ORDER BY m.FRegistro DESC
        LIMIT 500";
$stmt = $db->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $anuncio_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) { $mensajes = $res->fetch_all(MYSQLI_ASSOC); $res->free(); }
    $stmt->close();
}

?>

<main class="mensajes-anuncio-main">
    <section>
        <h1>Mensajes del anuncio</h1>
        <p><strong>Título:</strong> <?php echo h($anuncio['Titulo']); ?></p>
        <?php if (!empty($anuncio['FPrincipal'])): ?>
            <p><img src="<?php echo h($anuncio['FPrincipal']); ?>" alt="Imagen" width="200"></p>
        <?php endif; ?>
        <p><strong>Ciudad:</strong> <?php echo h($anuncio['Ciudad']); ?> — <strong>Precio:</strong> <?php echo h($anuncio['Precio']); ?></p>
        <p><strong>Total de mensajes:</strong> <?php echo $total_mensajes; ?></p>
        <p><a class="boton-enlace" href="detalle_anuncio.php?id=<?php echo urlencode($anuncio_id); ?>">Ver anuncio</a>
           &nbsp; <a class="boton-enlace" href="mis_mensajes.php">Volver a mis mensajes</a></p>
    </section>

    <section>
        <h2>Listado de mensajes</h2>
        <?php if (empty($mensajes)): ?>
            <p>No hay mensajes para este anuncio.</p>
        <?php else: ?>
            <ul class="lista-mensajes-anuncio">
                <?php foreach ($mensajes as $m): ?>
                    <li>
                        <article>
                            <p class="meta">De: <strong><?php echo h($m['OrigenNombre'] ?: 'Anónimo'); ?></strong>
                            — <time><?php echo h(date('d/m/Y H:i', strtotime($m['FRegistro']))); ?></time></p>
                            <p class="tipo-msg"><?php echo h($m['TipoNombre'] ?: 'Mensaje'); ?></p>
                            <p class="texto"><?php echo nl2br(h(mb_strimwidth($m['Texto'], 0, 800, '...'))); ?></p>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
