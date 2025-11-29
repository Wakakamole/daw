<?php
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';

// Verificar si el usuario está logueado
if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    echo "<p>No se ha identificado al usuario. Por favor, inicia sesión.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}

// Recoger el id del anuncio
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<h2>Anuncio no válido.</h2>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}

$conexion = get_db();

// Consultar anuncio con joins para tipo de anuncio, tipo de vivienda, país y usuario
$sql = "
SELECT a.*, ta.NomTAnuncio AS tipo_anuncio, tv.NomTVivienda AS tipo_vivienda,
       p.NomPais AS pais, u.NomUsuario AS usuario, u.Email AS email_usuario, u.Foto AS foto_usuario
FROM anuncios a
LEFT JOIN tiposanuncios ta ON a.TAnuncio = ta.IdTAnuncio
LEFT JOIN tiposviviendas tv ON a.TVivienda = tv.IdTVivienda
LEFT JOIN paises p ON a.Pais = p.IdPais
LEFT JOIN usuarios u ON a.Usuario = u.IdUsuario
WHERE a.IdAnuncio = ?
";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h2>No se encontró el anuncio.</h2>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}

$anuncio = $result->fetch_assoc();
$stmt->close();

// Consultar fotos adicionales (miniaturas)
$sqlFotos = "SELECT Foto FROM fotos WHERE Anuncio = ?";
$stmtFotos = $conexion->prepare($sqlFotos);
$stmtFotos->bind_param('i', $id);
$stmtFotos->execute();
$resultFotos = $stmtFotos->get_result();

$miniaturas = [];
while ($fila = $resultFotos->fetch_assoc()) {
    $miniaturas[] = $fila['Foto'];
}
$stmtFotos->close();

// Helper
function h($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

// CABECERA
$page_title = 'INMOLINK - Anuncio';
?>

<main>
    <section id="cabecera-detalle">
        <h1>DETALLE DEL ANUNCIO</h1>
    </section>

    <!-- Imagen principal -->
    <section id="imagen-principal">
        <img src="<?= h($anuncio['FPrincipal']) ?>" alt="<?= h($anuncio['Titulo']) ?>" width="500" height="350">
    </section>

    <!-- Titulo -->
    <h2><?= h($anuncio['Titulo']) ?></h2>

    <section id="info-basica">
        <p>Tipo de vivienda: <?= h($anuncio['tipo_vivienda']) ?></p>
        <p>Tipo de anuncio: <?= h($anuncio['tipo_anuncio']) ?></p>
        <p>Ciudad: <?= h($anuncio['Ciudad']) ?></p>
        <p>País: <?= h($anuncio['pais']) ?></p>
        <p>Precio: <?= h($anuncio['Precio']) ?> €</p>
        <p>Fecha de publicación: <?= h($anuncio['FRegistro']) ?></p>
        <p>Publicado por: <?= h($anuncio['usuario']) ?></p>
    </section>

    <!-- Miniaturas de fotos -->
    <?php if (!empty($miniaturas)): ?>
    <section id="galeria">
        <?php foreach ($miniaturas as $m): ?>
            <img src="<?= h($m) ?>" alt="imagen anuncio" width="100" height="70">
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <!-- Descripcion -->
    <section id="descripcion">
        <h3>Descripción</h3>
        <p><?= h($anuncio['Texto']) ?></p>
    </section>

    <!-- Caracteristicas -->
    <section id="caracteristicas">
        <h3>Características</h3>
        <ul>
            <li>Superficie: <?= h($anuncio['Superficie']) ?> m²</li>
            <li>Habitaciones: <?= h($anuncio['NHabitaciones']) ?></li>
            <li>Baños: <?= h($anuncio['NBanyos']) ?></li>
            <li>Plantas: <?= h($anuncio['Planta']) ?></li>
            <li>Año de construcción: <?= h($anuncio['Anyo']) ?></li>
        </ul>
    </section>

    <!-- Enlace a añadir foto -->
    <section id="contacto-anunciante">
        <p>
            <a class="boton-enlace" href="/daw/anadir_foto?id=<?= h($anuncio['IdAnuncio']); ?>">Añadir foto al anuncio</a>
        </p>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
