<?php
$page_title = 'INMOLINK - Resultados';
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';

$conexion = get_db();

// Recoger parámetros de búsqueda rápida (GET) y avanzada (POST)
$query_rapida = trim($_GET['query'] ?? '');
$tipo_anuncio  = $_POST['tipo_anuncio'] ?? '';
$tipo_vivienda = $_POST['tipo_vivienda'] ?? '';
$ciudad        = $_POST['ciudad'] ?? '';
$pais          = $_POST['pais'] ?? '';
$precio        = $_POST['precio'] ?? '';
$fecha         = $_POST['fecha_publicacion'] ?? '';

// Inicializamos la consulta
$sql = "SELECT * FROM anuncios WHERE 1=1";

// ---- Búsqueda rápida ----
if ($query_rapida !== '') {
    $stopwords = ['un','una','en','de'];
    $palabras = array_diff(explode(' ', strtolower($query_rapida)), $stopwords);

    // Diccionarios
    $tipos_viviendas_dic = [
        'obra nueva' => 1,
        'vivienda'   => 2,
        'oficina'    => 3,
        'local'      => 4,
        'garaje'     => 5,
    ];
    $tipos_anuncios_dic = [
        'venta' => 1,
        'alquiler' => 2
    ];

    $filtro_tipo_vivienda = null;
    $filtro_tipo_anuncio  = null;
    $filtro_ciudad        = null;

    foreach ($tipos_viviendas_dic as $nombre => $id) {
        if (in_array($nombre, $palabras)) {
            $filtro_tipo_vivienda = $id;
            break;
        }
    }
    foreach ($tipos_anuncios_dic as $nombre => $id) {
        if (in_array($nombre, $palabras)) {
            $filtro_tipo_anuncio = $id;
            break;
        }
    }
    foreach ($palabras as $p) {
        if (!array_key_exists($p, $tipos_viviendas_dic) && !array_key_exists($p, $tipos_anuncios_dic)) {
            $filtro_ciudad = $p;
        }
    }

    if ($filtro_tipo_vivienda)
        $sql .= " AND TVivienda = " . (int)$filtro_tipo_vivienda;
    if ($filtro_tipo_anuncio)
        $sql .= " AND TAnuncio = " . (int)$filtro_tipo_anuncio;
    if ($filtro_ciudad)
        $sql .= " AND LOWER(Ciudad) LIKE '%" . $conexion->real_escape_string($filtro_ciudad) . "%'";
}

// ---- Búsqueda avanzada ----
if ($tipo_vivienda !== '') {
    $sql .= " AND TVivienda = " . (int)$tipo_vivienda;
}
if ($tipo_anuncio !== '') {
    $sql .= " AND TAnuncio = " . (int)$tipo_anuncio;
}
if ($ciudad !== '') {
    $sql .= " AND LOWER(Ciudad) LIKE '%" . $conexion->real_escape_string(strtolower($ciudad)) . "%'";
}
if ($pais !== '') {
    $sql .= " AND Pais = '" . $conexion->real_escape_string($pais) . "'";
}
if ($precio !== '') {
    $sql .= " AND Precio <= " . (float)$precio;
}
if ($fecha !== '') {
    $sql .= " AND FRegistro >= '" . $conexion->real_escape_string($fecha) . "'";
}

$sql .= " ORDER BY FRegistro DESC";

$result = $conexion->query($sql);
?>

<main class="resultados">
    <h2>Resultados de búsqueda<?= $query_rapida ? ': "' . htmlspecialchars($query_rapida) . '"' : '' ?></h2>

    <?php if ($result->num_rows === 0): ?>
        <p>No se encontraron anuncios que coincidan.</p>
    <?php else: ?>
        <ul class="lista-anuncios">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <article class="anuncio-item">
                        <h3><?= htmlspecialchars($row['Titulo']) ?></h3>

                        <a href="detalle_anuncio.php?id=<?= $row['IdAnuncio'] ?>">
                            <img src="<?= htmlspecialchars($row['FPrincipal']) ?>" 
                                 alt="<?= htmlspecialchars($row['Alternativo']) ?>">
                        </a>

                        <footer>
                            <p><strong>Precio:</strong> <?= htmlspecialchars($row['Precio']) ?> €</p>
                            <p><strong>Ciudad:</strong> <?= htmlspecialchars($row['Ciudad']) ?></p>
                            <p><strong>Superficie:</strong> <?= htmlspecialchars($row['Superficie']) ?> m²</p>
                        </footer>
                    </article>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
