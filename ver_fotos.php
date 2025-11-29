<?php
require_once __DIR__ . '/includes/basedatos.php';
require_once __DIR__ . '/includes/ver_fotos_comun.php';
$conexion = get_db();

// Recoger id
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<h2>Anuncio no válido.</h2>";
    exit;
}

// Consultar anuncio
$sql = "SELECT a.*, ta.NomTAnuncio AS tipo_anuncio, tv.NomTVivienda AS tipo_vivienda,
        p.NomPais AS pais
        FROM anuncios a
        LEFT JOIN tiposanuncios ta ON a.TAnuncio = ta.IdTAnuncio
        LEFT JOIN tiposviviendas tv ON a.TVivienda = tv.IdTVivienda
        LEFT JOIN paises p ON a.Pais = p.IdPais
        WHERE a.IdAnuncio = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    echo "<h2>No existe el anuncio.</h2>";
    exit;
}

$anuncio = $res->fetch_assoc();

// Consultar todas las fotos
$sql2 = "SELECT Foto FROM fotos WHERE Anuncio = ?";
$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param('i', $id);
$stmt2->execute();
$res2 = $stmt2->get_result();

$fotos = [];

//foto principal
if (!empty($anuncio['FPrincipal'])) {
    $fotos[] = $anuncio['FPrincipal'];
}

//el resto
while ($f = $res2->fetch_assoc()) {
    $fotos[] = $f['Foto'];
}


$page_title = "Ver fotos del anuncio";
require_once __DIR__ . '/includes/cabecera.php';

// Mostrar bloque común
mostrar_bloque_fotos_comun($anuncio, $fotos);

require_once __DIR__ . '/includes/pie.php';
?>
