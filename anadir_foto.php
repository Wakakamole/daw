<?php
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';
session_start();

// Verificar login
if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    die('Debes iniciar sesión.');
}

$usuario_id = $_SESSION['usuario_id'] ?? 0;

$conexion = get_db();

// Obtenemos ID del anuncio desde GET
$id_anuncio = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_anuncio <= 0) {
    die('Anuncio no válido.');
}

// Comprobamos que el anuncio pertenece al usuario
$stmt = $conexion->prepare("SELECT * FROM anuncios WHERE IdAnuncio = ? AND Usuario = ?");
$stmt->bind_param("ii", $id_anuncio, $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die('No tienes permiso para añadir fotos a este anuncio.');
}
$anuncio = $res->fetch_assoc();
$stmt->close();


$errores = [];
$exito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $alternativo = trim($_POST['alternativo'] ?? '');
    $errores = [];
    $foto_ruta = '';

    // Validaciones de título y alternativo
    if ($titulo === '') {
        $errores[] = "El título de la foto es obligatorio.";
    }
    if ($alternativo === '') {
        $errores[] = "El texto alternativo es obligatorio.";
    } elseif (strlen($alternativo) < 10) {
        $errores[] = "El texto alternativo debe tener al menos 10 caracteres.";
    } elseif (preg_match('/^(foto|foto de|imagen|imagen de|texto)/i', $alternativo)) {
        $errores[] = "El texto alternativo no debe empezar con 'foto', 'imagen' o 'texto'.";
    }

    // Validacion de la foto
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Debes seleccionar una imagen válida.";
    } else {
        $nombre_archivo = basename($_FILES['foto']['name']);
        $foto_ruta = 'img/' . $nombre_archivo;  //muevo la imagen a la carpeta img/
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $foto_ruta)) {
            $errores[] = "Error al subir la imagen al servidor.";
        }
    }

    // Si no hay errores, insertar en DB
    if (empty($errores)) {
        $stmt = $conexion->prepare("INSERT INTO fotos (Titulo, Foto, Alternativo, Anuncio) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $titulo, $foto_ruta, $alternativo, $id_anuncio);
        if ($stmt->execute()) {
            $exito = true;
        } else {
            $errores[] = "Error al añadir la foto: " . $stmt->error;
        }
        $stmt->close();
    }
}

?>

<main>
    <h1>Añadir foto a anuncio: <?= htmlspecialchars($anuncio['Titulo']) ?></h1>

    <?php if (!empty($errores)): ?>
        <div class="errores">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($exito): ?>
        <div class="exito">
            <p>Foto añadida correctamente.</p>
            <a href="ver_fotos_privadas.php?id=<?= $id_anuncio ?>">Volver a las fotos del anuncio</a>
        </div>
    <?php endif; ?>

    <?php if (!$exito): ?>
        <form action="#" method="post" enctype="multipart/form-data">
            <label for="titulo">Título de la foto (*):</label>
            <input type="text" id="titulo" name="titulo" required value="<?= htmlspecialchars($titulo ?? '') ?>"><br><br>

            <label for="alternativo">Texto alternativo (*, mínimo 10 caracteres y sin 'foto', 'imagen' o 'texto'):</label><br>
            <textarea id="alternativo" name="alternativo" rows="3" cols="50" required><?= htmlspecialchars($alternativo ?? '') ?></textarea><br><br>

            <label for="foto">Selecciona la imagen (*):</label>
            <input type="file" id="foto" name="foto" accept="image/*" required><br><br>


            <button type="submit">Añadir foto</button>
        </form>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
