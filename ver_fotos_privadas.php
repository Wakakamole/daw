<?php
require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/basedatos.php';
require_once __DIR__ . '/includes/ver_fotos_comun.php';

// Comprobar login
if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    echo "<p>Debes iniciar sesión para ver las fotos privadas.</p>";
    exit;
}

$usuario_id = $_SESSION['usuario_id'] ?? 0;

$conexion = get_db();

// Recoger id del anuncio
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<h2 style='text-align:center;'>Anuncio no válido.</h2>";
    echo "<p style='text-align:center;'><a href='mis_anuncios.php'>Volver</a></p>";
    exit;
}

// Comprobar que el anuncio pertenece al usuario actual
$stmt = $conexion->prepare("SELECT * FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?");
$stmt->bind_param('ii', $id, $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    echo "<div style='text-align:center; margin-top:50px;'>";
    echo "<h2>No tienes permiso para ver las fotos privadas de este anuncio.</h2>";
    echo "<p><a href='inicio_user.php' style='padding:10px 20px; background:#007BFF; color:white; text-decoration:none; border-radius:5px;'>Volver</a></p>";
    echo "</div>";
    exit;
}

$anuncio = $res->fetch_assoc();
$stmt->close();

// Consultar todas las fotos
$sql2 = "SELECT Foto FROM fotos WHERE Anuncio = ?";
$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param('i', $id);
$stmt2->execute();
$res2 = $stmt2->get_result();

$fotos = [];

// foto principal
if (!empty($anuncio['FPrincipal'])) {
    $fotos[] = $anuncio['FPrincipal'];
}

// resto de fotos
while ($f = $res2->fetch_assoc()) {
    $fotos[] = $f['Foto'];
}

$page_title = "Ver fotos del anuncio (privado)";
require_once __DIR__ . '/includes/cabecera.php';

// Mostrar bloque común
mostrar_bloque_fotos_comun($anuncio, $fotos, true);

require_once __DIR__ . '/includes/pie.php';
?>
