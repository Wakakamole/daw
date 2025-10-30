<!-- PÁGINA DE DETALLE DE ANUNCIO -->
<!--
    Página que muestra el detalle completo de un anuncio (de momento los pilllamos de data/anuncios.php)

    Contiene:
        - Tipo de anuncio (venta/alquiler)
        - Tipo de vivienda (obra nueva, vivienda, oficina, local, garaje)
        - Ciudad
        - País
        - Precio
        - Fecha de publicación
-->

<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$anuncios = [];
// cargar datos
if (file_exists(__DIR__ . '/data/anuncios.php')) {
    $anuncios = require __DIR__ . '/data/anuncios.php';
}

// selección: si existe el id, tomarlo; si no, elegir según par/impar
if (isset($anuncios[$id])) {
    $anuncio = $anuncios[$id];
} else {
    $anuncio = ($id % 2 === 0) ? $anuncios[2] : $anuncios[1];
}

// helpers
function h($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<?php
// incluir cabecera común
$page_title = 'INMOLINK - Anuncio';
require_once __DIR__ . '/includes/cabecera.php';
?>

    <main>
        <section id="cabecera-detalle">
        <h1> DETALLE DEL ANUNCIO </h1>
        </section>

        <!-- Imagen principal -->
        <section id="imagen-principal">
            <img src="<?php echo h($anuncio['foto']); ?>" alt="<?php echo h($anuncio['titulo']); ?>" width="500" height="350">
        </section>

        <!-- Titulo -->
        <h2><?php echo h($anuncio['titulo']); ?></h2>

        <section id="info-basica">
            <p>Tipo de vivienda: <?php echo h($anuncio['tipo_vivienda']); ?></p>
            <p>Tipo de anuncio: <?php echo h($anuncio['tipo_anuncio']); ?></p>
            <p>Ciudad: <?php echo h($anuncio['ciudad']); ?></p>
            <p>País: <?php echo h($anuncio['pais']); ?></p>
            <p>Precio: <?php echo h($anuncio['precio']); ?></p>
            <p>Fecha de publicación: <?php echo h($anuncio['fecha']); ?></p>
        </section>

        <!-- Miniaturas de fotos -->
        <section id="galeria">
            <?php foreach ($anuncio['miniaturas'] as $m): ?>
                <img src="<?php echo h($m); ?>" alt="imagen anuncio" width="100" height="70">
            <?php endforeach; ?>
        </section>

        <!-- Descripcion -->
        <section id="descripcion">
            <h3>Descripcion</h3>
            <p>
                <?php echo h($anuncio['descripcion']); ?>
            </p>
        </section>

        <!-- Caracteristicas -->
            <section id="caracteristicas">
            <h3>Características</h3>
            <!-- lista ordenada -->
            <ul> 
                <li>Superficie: <?php echo h($anuncio['superficie']); ?></li>
                <li>Habitaciones: <?php echo h($anuncio['habitaciones']); ?></li>
                <li>Baños: <?php echo h($anuncio['banos']); ?></li>
                <li>Plantas: <?php echo h($anuncio['plantas']); ?></li>
                <li>Año de construcción: <?php echo h($anuncio['ano']); ?></li>
            </ul>
            </section>

            <section id="contacto-anunciante">
                <p>
                    <a class="boton-enlace" href="mensaje.php?id=<?php echo h($anuncio['id']); ?>">Enviar mensaje al anunciante</a>
                </p>
            </section>


    </main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
