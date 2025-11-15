<?php
// Página principal pública
$page_title = 'INMOLINK';
// incluir la cabecera (auto-login ya se ejecuta ahí antes de salida)
require_once __DIR__ . '/includes/cabecera.php';

// Si tras el auto-login hay sesión activa, redirigir al área privada
if (!empty($_SESSION['login']) && $_SESSION['login'] === 'ok') {
    header('Location: index_user.php');
    exit;
}

?>

<main>
    <section>
    <h1>PÁGINA PRINCIPAL </h1>
    <h2>Últimos anuncios publicados</h2>
    </section>
    <?php
    // Obtener últimos 5 anuncios desde la base de datos
    require_once __DIR__ . '/includes/basedatos.php';
    $db = get_db();
    $sql = "SELECT A.IdAnuncio, A.Titulo, A.FPrincipal, A.FRegistro, A.Ciudad, A.Precio, P.NomPais
            FROM Anuncios A
            LEFT JOIN Paises P ON A.Pais = P.IdPais
            ORDER BY A.FRegistro DESC
            LIMIT 5";
    $ultimos = [];
    try {
        $res = $db->query($sql);
        if ($res) {
            $ultimos = $res->fetch_all(MYSQLI_ASSOC);
            $res->free();
        }
    } catch (Exception $e) {
        // Si falla la consulta, dejamos el array vacío y mostramos mensaje más abajo
        error_log('Error al obtener últimos anuncios: ' . $e->getMessage());
    }
    ?>

    <?php if (empty($ultimos)): ?>
        <p>No hay anuncios para mostrar.</p>
    <?php else: ?>
    <ul>
    <?php foreach ($ultimos as $an):
        $img = !empty($an['FPrincipal']) ? htmlspecialchars($an['FPrincipal'], ENT_QUOTES, 'UTF-8') : 'img/noimage.png';
        $fecha = !empty($an['FRegistro']) ? date('d/m/Y', strtotime($an['FRegistro'])) : '';
    ?>
        <li>
        <article>
            <h3><?php echo htmlspecialchars($an['Titulo'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <a href="ver_anuncio.php?id=<?php echo (int)$an['IdAnuncio']; ?>">
                <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($an['Titulo'], ENT_QUOTES, 'UTF-8'); ?>" width="200">
            </a>
            <footer>
                <p><?php echo htmlspecialchars($an['Ciudad'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><?php echo htmlspecialchars($an['NomPais'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Fecha:</strong> <?php echo $fecha; ?></p>
                <p><strong>Precio:</strong> <?php echo htmlspecialchars($an['Precio'], ENT_QUOTES, 'UTF-8'); ?></p>
            </footer>
        </article>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
