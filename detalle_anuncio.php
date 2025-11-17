<?php
require_once __DIR__ . '/includes/basedatos.php';
$conexion = get_db();

// Recoger el id del anuncio
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

if ($id <= 0) {
    echo "<h2>Anuncio no válido.</h2>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}

// Consultar anuncio
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

// Helper
function h($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

// CABECERA
$page_title = 'INMOLINK - Anuncio';
require_once __DIR__ . '/includes/cabecera.php';

// PANEL DE ÚLTIMOS ANUNCIOS VISITADOS
$cookie_name = 'ultimos_anuncios';
$max_anuncios = 4;
$ultimos_anuncios = [];

if (isset($_COOKIE[$cookie_name])) {
    $ultimos_anuncios = json_decode($_COOKIE[$cookie_name], true);
    if (!is_array($ultimos_anuncios)) $ultimos_anuncios = [];
}

if (($key = array_search($id, $ultimos_anuncios)) !== false) {
    unset($ultimos_anuncios[$key]);
}
$ultimos_anuncios[] = $id;
$ultimos_anuncios = array_slice($ultimos_anuncios, -$max_anuncios);
setcookie($cookie_name, json_encode($ultimos_anuncios), time() + 7*24*60*60, "/");
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
    </section>

    <!-- Miniaturas de fotos -->
    <section id="galeria">
        <?php foreach ($miniaturas as $m): ?>
            <img src="<?= h($m) ?>" alt="imagen anuncio" width="100" height="70">
        <?php endforeach; ?>
    </section>

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

    <!-- Ver fotos del anuncio -->
    <section id="ver-fotos-anuncio">
        <p>
            <a class="boton-enlace" href="ver_fotos.php?id=<?= h($anuncio['IdAnuncio']) ?>">
                Ver todas las fotos del anuncio
            </a>
        </p>

        <p>
            <a class="boton-enlace" href="ver_fotos_privadas.php?id=<?= h($anuncio['IdAnuncio']) ?>">
                Ver todas las fotos del anuncio privada
            </a>
        </p>
        
    </section>


    <!-- Contacto -->
    <section id="contacto-anunciante">
        <p>
            <a class="boton-enlace" href="mensaje.php?id=<?= h($id) ?>">Enviar mensaje al anunciante</a>
        </p>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
