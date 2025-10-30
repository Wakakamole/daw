
<!-- PÁGINA DE RESULTADOS DE BÚSQUEDA-->
<!--
    Página que muestra los resultados de búsqueda (estáticos de momento)
-->


<?php
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$safeQuery = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
$page_title = 'INMOLINK - Resultados';
require_once __DIR__ . '/includes/cabecera.php';

// Cargar anuncios y filtrar por la query (búsqueda simple)
$anuncios = [];
if (file_exists(__DIR__ . '/data/anuncios.php')) {
    $anuncios = require __DIR__ . '/data/anuncios.php';
}
$results = [];
if ($query !== '') {
    foreach ($anuncios as $a) {
        if ((isset($a['titulo']) && mb_stripos($a['titulo'], $query, 0, 'UTF-8') !== false)
            || (isset($a['descripcion']) && mb_stripos($a['descripcion'], $query, 0, 'UTF-8') !== false)
            || (isset($a['ciudad']) && mb_stripos($a['ciudad'], $query, 0, 'UTF-8') !== false)
            || (isset($a['pais']) && mb_stripos($a['pais'], $query, 0, 'UTF-8') !== false)) {
            $results[] = $a;
        }
    }
} else {
    $results = array_values($anuncios);
}
$count = count($results);
?>

    <main>

        <section>
        <h1>RESULTADOS DE BÚSQUEDA</h1>
        <section class="busqueda-params" aria-labelledby="params-title">
                <h3 id="params-title">Parámetros de búsqueda
                <span class="contador"><?php echo $count; ?> resultados</span>
            </h3>
            <p>
                <?php if ($safeQuery !== ''): ?>
                    Resultados para: <strong>&#8220;<?php echo $safeQuery; ?>&#8221;</strong>
                <?php else: ?>
                    <strong>No se ha introducido ningún término de búsqueda.</strong>
                    <br>
                    Mostrando todos los anuncios disponibles.
                <?php endif; ?>
            </p>
            <?php if ($safeQuery !== ''): ?>
                <p class="busqueda-usuario">Usted ha buscado: <strong><?php echo $safeQuery; ?></strong></p>
            <?php endif; ?>
            <p class="acciones">
                <a href="resultado_busqueda.php" class="boton-enlace">Limpiar búsqueda</a>
                <a href="formulario_busqueda.html" class="boton-enlace" style="margin-left:0.8rem">Modificar mi búsqueda</a>
            </p>
        </section>
        </section>

        <ul>
            <?php if ($count === 0): ?>
                <li>
                    <article>
                        <h3>No se han encontrado resultados</h3>
                        <footer>
                            <p>Prueba con otro término de búsqueda o comprueba la ortografía.</p>
                        </footer>
                    </article>
                </li>
            <?php else: ?>
                <?php foreach ($results as $r): ?>
                    <li>
                        <article>
                            <h3><?php echo htmlspecialchars($r['titulo'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <a href="detalle_anuncio.php?id=<?php echo urlencode($r['id']); ?>">
                                <img src="<?php echo htmlspecialchars($r['foto'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($r['titulo'], ENT_QUOTES, 'UTF-8'); ?>" width="200">
                            </a>
                            <footer>
                                <p><?php echo htmlspecialchars($r['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><strong>Fecha:</strong> <?php echo htmlspecialchars($r['fecha'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($r['ciudad'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><strong>País:</strong> <?php echo htmlspecialchars($r['pais'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><strong>Precio:</strong> <?php echo htmlspecialchars($r['precio'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </footer>
                        </article>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

    </main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
