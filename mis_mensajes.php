<?php

require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';

$db = get_db();
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

// Obtener totales
$enviados = 0; $recibidos = 0;
if ($usuario_id > 0) {
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM mensajes WHERE UsuOrigen = ?");
    if ($stmt) { $stmt->bind_param('i', $usuario_id); $stmt->execute(); $cnt = $stmt->get_result()->fetch_assoc(); $enviados = (int)$cnt['cnt']; $stmt->close(); }
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM mensajes WHERE UsuDestino = ?");
    if ($stmt) { $stmt->bind_param('i', $usuario_id); $stmt->execute(); $cnt = $stmt->get_result()->fetch_assoc(); $recibidos = (int)$cnt['cnt']; $stmt->close(); }
}

// Recuperar mensajes enviados y recibidos (detallados)
$mensajesEnviados = [];
$mensajesRecibidos = [];
if ($usuario_id > 0) {
    // Enviados: unir con usuario destino y tipo
    $sql_en = "SELECT m.IdMensaje, m.Texto, m.FRegistro, m.Anuncio, tm.NomTMensaje AS TipoNombre, udest.NomUsuario AS DestinoNombre
               FROM mensajes m
               LEFT JOIN tiposmensajes tm ON m.TMensaje = tm.IdTMensaje
               LEFT JOIN usuarios udest ON m.UsuDestino = udest.IdUsuario
               WHERE m.UsuOrigen = ?
               ORDER BY m.FRegistro DESC
               LIMIT 200";
    $stmt = $db->prepare($sql_en);
    if ($stmt) {
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) { $mensajesEnviados = $res->fetch_all(MYSQLI_ASSOC); $res->free(); }
        $stmt->close();
    }

    // Recibidos: unir con usuario origen y tipo
    $sql_rec = "SELECT m.IdMensaje, m.Texto, m.FRegistro, m.Anuncio, tm.NomTMensaje AS TipoNombre, uorig.NomUsuario AS OrigenNombre
               FROM mensajes m
               LEFT JOIN tiposmensajes tm ON m.TMensaje = tm.IdTMensaje
               LEFT JOIN usuarios uorig ON m.UsuOrigen = uorig.IdUsuario
               WHERE m.UsuDestino = ?
               ORDER BY m.FRegistro DESC
               LIMIT 200";
    $stmt = $db->prepare($sql_rec);
    if ($stmt) {
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) { $mensajesRecibidos = $res->fetch_all(MYSQLI_ASSOC); $res->free(); }
        $stmt->close();
    }
}

?>

<main class="mis-mensajes-main">

    <section>
    <h1>MIS MENSAJES</h1>
    <p>Consulta los mensajes enviados y recibidos en la plataforma.</p>
    </section>

    <section class="columna-1">
        <h2>Mensajes Enviados</h2>
        <p><strong>Total enviados:</strong> <?php echo $enviados; ?></p>
        <?php if ($enviados === 0): ?>
            <p>No has enviado mensajes todavía.</p>
        <?php else: ?>
            <ul class="lista-mensajes-enviados">
                <?php foreach ($mensajesEnviados as $m): ?>
                    <li class="mensaje-item">
                        <article>
                            <h3><?php echo htmlspecialchars($m['TipoNombre'] ?: 'Mensaje', ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="meta">A: <strong><?php echo htmlspecialchars($m['DestinoNombre'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></strong>
                                — <time><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($m['FRegistro'])), ENT_QUOTES, 'UTF-8'); ?></time></p>
                            <p class="texto"><?php echo nl2br(htmlspecialchars(mb_strimwidth($m['Texto'], 0, 300, '...'), ENT_QUOTES, 'UTF-8')); ?></p>
                            <?php if (!empty($m['Anuncio'])): ?>
                                <p><a href="/daw/detalle_anuncio?id=<?php echo urlencode($m['Anuncio']); ?>" class="boton-enlace">Ver anuncio relacionado</a></p>
                            <?php endif; ?>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="columna-2">
        <h2>Mensajes Recibidos</h2>
        <p><strong>Total recibidos:</strong> <?php echo $recibidos; ?></p>
        <?php if ($recibidos === 0): ?>
            <p>No has recibido mensajes todavía.</p>
        <?php else: ?>
            <ul class="lista-mensajes-recibidos">
                <?php foreach ($mensajesRecibidos as $m): ?>
                    <li class="mensaje-item">
                        <article>
                            <h3><?php echo htmlspecialchars($m['TipoNombre'] ?: 'Mensaje', ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="meta">De: <strong><?php echo htmlspecialchars($m['OrigenNombre'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></strong>
                                — <time><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($m['FRegistro'])), ENT_QUOTES, 'UTF-8'); ?></time></p>
                            <p class="texto"><?php echo nl2br(htmlspecialchars(mb_strimwidth($m['Texto'], 0, 300, '...'), ENT_QUOTES, 'UTF-8')); ?></p>
                            <?php if (!empty($m['Anuncio'])): ?>
                                <p><a href="/daw/detalle_anuncio?id=<?php echo urlencode($m['Anuncio']); ?>" class="boton-enlace">Ver anuncio relacionado</a></p>
                            <?php endif; ?>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
