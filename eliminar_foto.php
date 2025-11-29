<?php
require_once __DIR__ . '/includes/basedatos.php';
session_start();

if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    die('Debes iniciar sesión.');
}

$usuario_id = $_SESSION['usuario_id'] ?? 0;

$foto = $_POST['foto'] ?? '';
$id_anuncio = intval($_POST['id_anuncio'] ?? 0);

if (!$foto || $id_anuncio <= 0) {
    die('Datos inválidos.');
}

$conexion = get_db();

//comprobamos que el anuncio pertenece al usuario
$stmt = $conexion->prepare("SELECT * FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?");
$stmt->bind_param("ii", $id_anuncio, $usuario_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<div style='text-align:center; margin-top:50px;'>";
    echo "<h2>No tienes permiso para eliminar fotos de este anuncio.</h2>";
    echo "<p><a href='mis_anuncios.php'>Volver</a></p>";
    echo "</div>";
    exit;
}

//eliminar la foto de la tabla fotos si existe
$stmt = $conexion->prepare("DELETE FROM fotos WHERE Foto = ? AND Anuncio = ?");
$stmt->bind_param("si", $foto, $id_anuncio);
$stmt->execute();

//si la foto eliminada era la principal, poner FPrincipal = NULL
$stmt2 = $conexion->prepare("SELECT FPrincipal FROM anuncios WHERE IdAnuncio = ?");
$stmt2->bind_param("i", $id_anuncio);
$stmt2->execute();
$res2 = $stmt2->get_result();
$anuncio = $res2->fetch_assoc();
if ($anuncio['FPrincipal'] === $foto) {
    $stmt3 = $conexion->prepare("UPDATE anuncios SET FPrincipal = NULL WHERE IdAnuncio = ?");
    $stmt3->bind_param("i", $id_anuncio);
    $stmt3->execute();
    $stmt3->close();
}
$stmt2->close();
$stmt->close();

header("Location: ver_fotos_privadas.php?id=$id_anuncio");
exit;
?>
