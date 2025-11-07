<?php
require_once __DIR__ . '/includes/cabecera.php';

// Datos ficticios
$anuncios = [
    1 => [
        'titulo' => 'Piso en Calle Francia Bilbao',
        'ciudad' => 'Bilbao',
        'pais' => 'España',
        'precio' => '320.000€',
        'foto' => 'img/noimage.png'
    ],
    2 => [
        'titulo' => 'Apartamento en Gran Vía Madrid',
        'ciudad' => 'Madrid',
        'pais' => 'España',
        'precio' => '450.000€',
        'foto' => 'img/noimage.png'
    ],
    3 => [
        'titulo' => 'Chalet en La Manga',
        'ciudad' => 'Cartagena',
        'pais' => 'España',
        'precio' => '280.000€',
        'foto' => 'img/noimage.png'
    ]
];
?>

<main>
    <section>
        <h1>Mis anuncios</h1>
        <h2>Listado de tus anuncios</h2>
    </section>

    <ul class="lista-anuncios">
        <?php foreach ($anuncios as $id => $anuncio): ?>
            <li>
                <article class="anuncio-item">
                    <h3><?= $anuncio['titulo'] ?></h3>
                    <a href="ver_anuncio.php?id=<?= $id ?>">
                        <img src="<?= $anuncio['foto'] ?>" alt="<?= $anuncio['titulo'] ?>" width="200">
                    </a>
                    <footer>
                        <p><strong>Ciudad:</strong> <?= $anuncio['ciudad'] ?></p>
                        <p><strong>País:</strong> <?= $anuncio['pais'] ?></p>
                        <p><strong>Precio:</strong> <?= $anuncio['precio'] ?></p>
                    </footer>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="contenedor-boton-anadir">
        <a href="anadir_foto.php" class="boton-anadir-foto">
            <i class="fa-solid fa-image"></i> Añadir foto a algún anuncio
        </a>
    </div>

</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
