<?php
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';

//verifico login
if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    echo "<p>Debes iniciar sesión.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}

$usuario_id = $_SESSION['usuario_id'] ?? 0;
$conexion = get_db();

//obtenenos ID del anuncio
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<p>Anuncio no válido.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}

//comprobar que el anuncio pertenece al usuario
$stmt = $conexion->prepare("SELECT Titulo FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?");
$stmt->bind_param('ii', $id, $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo "<p>No puedes eliminar este anuncio o no existe.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}
$anuncio = $res->fetch_assoc();
$stmt->close();

//si el usuario lo confirma, borramos todo
if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'sí') {
    //Borrar
    //las fotos
    $stmt = $conexion->prepare("DELETE FROM fotos WHERE Anuncio = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    //los mensajes
    $stmt = $conexion->prepare("DELETE FROM mensajes WHERE Anuncio = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    //las solicitudes
    $stmt = $conexion->prepare("DELETE FROM solicitudes WHERE Anuncio = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    //el anuncio
    $stmt = $conexion->prepare("DELETE FROM anuncios WHERE IdAnuncio = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    //redirigir con un mensaje
    header("Location: /daw/mis_anuncios.php?deleted=1");
    exit;
}
?>

<main>
    <h1>Eliminar anuncio</h1>
    <p>¿Estás seguro de que quieres eliminar el anuncio: <strong><?= htmlspecialchars($anuncio['Titulo'], ENT_QUOTES) ?></strong>?</p>

    <form method="POST">
        <button type="submit" name="confirmar" value="sí">Sí, eliminar</button>
        <a href="/daw/mis_anuncios.php">Cancelar</a>
    </form>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
