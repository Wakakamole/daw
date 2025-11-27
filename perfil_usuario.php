<?php
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';

// Verificar si el usuario está logueado
if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    echo "<p>No se ha identificado al usuario. Por favor, inicia sesión.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// CARGAR DATOS DEL USUARIO
$db = get_db();
$sql = "SELECT NomUsuario, Foto, FRegistro FROM usuarios WHERE IdUsuario = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// CARGAR ANUNCIOS DEL USUARIO
$sql2 = "SELECT IdAnuncio, Titulo, Ciudad, Pais, Precio, FPrincipal 
         FROM anuncios 
         WHERE Usuario = ? 
         ORDER BY FRegistro DESC";
$stmt2 = $db->prepare($sql2);
$stmt2->bind_param('i', $usuario_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

$mis_anuncios = [];
while ($row = $result2->fetch_assoc()) {
    $mis_anuncios[] = $row;
}
$stmt2->close();
$total_anuncios = count($mis_anuncios);
?>

<main>
    <section>
        <h1>Perfil de <?= htmlspecialchars($usuario['NomUsuario'], ENT_QUOTES) ?></h1>
        <p><strong>Fecha de incorporación:</strong> <?= htmlspecialchars($usuario['FRegistro'], ENT_QUOTES) ?></p>
        <?php if (!empty($usuario['Foto'])): ?>
            <img src="<?= htmlspecialchars($usuario['Foto'], ENT_QUOTES) ?>" alt="Foto de <?= htmlspecialchars($usuario['NomUsuario'], ENT_QUOTES) ?>" width="150">
        <?php endif; ?>
    </section>

    <section>
        <h2>Mis anuncios (Total: <?= $total_anuncios ?>)</h2>

        <?php if ($total_anuncios === 0): ?>
            <p>No tienes anuncios publicados.</p>
        <?php else: ?>
            <ul class="lista-anuncios">
                <?php foreach ($mis_anuncios as $anuncio): ?>
                    <li>
                        <article class="anuncio-item">
                            <h3><?= htmlspecialchars($anuncio['Titulo'], ENT_QUOTES) ?></h3>
                            <a href="/daw/ver_anuncio?id=<?= (int)$anuncio['IdAnuncio'] ?>">
                                <img src="<?= !empty($anuncio['FPrincipal']) ? htmlspecialchars($anuncio['FPrincipal'], ENT_QUOTES) : 'img/noimage.png' ?>" 
                                     alt="<?= htmlspecialchars($anuncio['Titulo'], ENT_QUOTES) ?>" width="200">
                            </a>
                            <footer>
                                <p><strong>Ciudad:</strong> <?= htmlspecialchars($anuncio['Ciudad'], ENT_QUOTES) ?></p>
                                <p><strong>País:</strong> <?= htmlspecialchars($anuncio['Pais'], ENT_QUOTES) ?></p>
                                <p><strong>Precio:</strong> <?= htmlspecialchars($anuncio['Precio'], ENT_QUOTES) ?></p>
                            </footer>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
