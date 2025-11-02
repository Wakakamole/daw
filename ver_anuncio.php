<?php
// ID del anuncio pasado por URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Datos ficticios de anuncios
$anuncios = [
    1 => [
        'id' => 1,
        'titulo' => 'Piso en Calle Francia Bilbao',
        'tipo_anuncio' => 'Venta',
        'tipo_vivienda' => 'Vivienda',
        'ciudad' => 'Bilbao',
        'pais' => 'España',
        'precio' => '320.000€',
        'fecha' => '10/09/2025',
        'descripcion' => 'Luminoso piso en el centro de Bilbao...',
        'foto' => 'img/noimage.png',
        'miniaturas' => ['img/noimage.png','img/noimage.png','img/noimage.png'],
        'superficie' => '120 m²',
        'habitaciones' => 3,
        'banos' => 2,
        'plantas' => 1,
        'ano' => 2010
    ],
    2 => [
        'id' => 2,
        'titulo' => 'Apartamento en Gran Vía Madrid',
        'tipo_anuncio' => 'Alquiler',
        'tipo_vivienda' => 'Apartamento',
        'ciudad' => 'Madrid',
        'pais' => 'España',
        'precio' => '450.000€',
        'fecha' => '12/09/2025',
        'descripcion' => 'Moderno apartamento en Gran Vía, cerca de transporte...',
        'foto' => 'img/noimage.png',
        'miniaturas' => ['img/noimage.png','img/noimage.png','img/noimage.png'],
        'superficie' => '80 m²',
        'habitaciones' => 2,
        'banos' => 1,
        'plantas' => 1,
        'ano' => 2015
    ],
    3 => [
        'id' => 3,
        'titulo' => 'Chalet en La Manga',
        'tipo_anuncio' => 'Venta',
        'tipo_vivienda' => 'Chalet',
        'ciudad' => 'Cartagena',
        'pais' => 'España',
        'precio' => '280.000€',
        'fecha' => '15/09/2025',
        'descripcion' => 'Chalet con piscina y jardín privado...',
        'foto' => 'img/noimage.png',
        'miniaturas' => ['img/noimage.png','img/noimage.png','img/noimage.png'],
        'superficie' => '200 m²',
        'habitaciones' => 4,
        'banos' => 3,
        'plantas' => 2,
        'ano' => 2012
    ]
];

// Selección del anuncio
$anuncio = $anuncios[$id] ?? $anuncios[1];

// Helper para escapar contenido
function h($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

require_once __DIR__ . '/includes/cabecera.php';
?>

<main>
    <section id="cabecera-detalle">
        <h1>DETALLE DEL ANUNCIO</h1>
    </section>

    <!-- Imagen -->
    <section id="imagen-principal">
        <img src="<?= h($anuncio['foto']); ?>" alt="<?= h($anuncio['titulo']); ?>" width="500" height="350">
    </section>

    <!-- Titulo -->
    <h2><?= h($anuncio['titulo']); ?></h2>

    <section id="info-basica">
        <p>Tipo de vivienda: <?= h($anuncio['tipo_vivienda']); ?></p>
        <p>Tipo de anuncio: <?= h($anuncio['tipo_anuncio']); ?></p>
        <p>Ciudad: <?= h($anuncio['ciudad']); ?></p>
        <p>País: <?= h($anuncio['pais']); ?></p>
        <p>Precio: <?= h($anuncio['precio']); ?></p>
        <p>Fecha de publicación: <?= h($anuncio['fecha']); ?></p>
    </section>

    <!--fotos -->
    <section id="galeria">
        <?php foreach ($anuncio['miniaturas'] as $m): ?>
            <img src="<?= h($m); ?>" alt="imagen anuncio" width="100" height="70">
        <?php endforeach; ?>
    </section>

    <!-- Descripcion -->
    <section id="descripcion">
        <h3>Descripción</h3>
        <p><?= h($anuncio['descripcion']); ?></p>
    </section>

    <!-- Caracteristicas -->
    <section id="caracteristicas">
        <h3>Características</h3>
        <ul> 
            <li>Superficie: <?= h($anuncio['superficie']); ?></li>
            <li>Habitaciones: <?= h($anuncio['habitaciones']); ?></li>
            <li>Baños: <?= h($anuncio['banos']); ?></li>
            <li>Plantas: <?= h($anuncio['plantas']); ?></li>
            <li>Año de construcción: <?= h($anuncio['ano']); ?></li>
        </ul>
    </section>

    <!-- Enlace a añadir foto -->
    <section id="contacto-anunciante">
        <p>
            <a class="boton-enlace" href="anadir_foto.php?id=<?= h($anuncio['id']); ?>">Añadir foto al anuncio</a>
        </p>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
