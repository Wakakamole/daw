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

// CARGAR ANUNCIOS DEL USUARIO
$db = get_db();
$sql = "SELECT IdAnuncio, Titulo, Ciudad, Pais, Precio, FPrincipal 
        FROM anuncios 
        WHERE Usuario = ? 
        ORDER BY FRegistro DESC";
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$mis_anuncios = [];
while ($row = $result->fetch_assoc()) {
    $mis_anuncios[] = $row;
}
$stmt->close();
$total_anuncios = count($mis_anuncios);
?>

<main>
    <section>
        <h1>Mis anuncios</h1>
        <h2>Listado de tus anuncios (Total: <?= $total_anuncios ?>)</h2>
    </section>

    <?php if ($total_anuncios === 0): ?>
        <p>No tienes anuncios publicados.</p>
    <?php else: ?>
        <ul class="lista-anuncios">
            <?php foreach ($mis_anuncios as $anuncio): ?>
                <li>
                    <article class="anuncio-item">
                        <h3><?= htmlspecialchars($anuncio['Titulo'], ENT_QUOTES) ?></h3>
                        <a href="ver_anuncio.php?id=<?= (int)$anuncio['IdAnuncio'] ?>">
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
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
