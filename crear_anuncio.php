<?php
require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/basedatos.php';
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/formulario_anuncios.php';

$conexion = get_db();
$errores = [];
$exito = false;

// cargamos datos para los selects
$tiposAnuncios = [];
$res = $conexion->query("SELECT IdTAnuncio, NomTAnuncio FROM tiposanuncios");
while ($fila = $res->fetch_assoc()) $tiposAnuncios[] = $fila;

$tiposViviendas = [];
$res = $conexion->query("SELECT IdTVivienda, NomTVivienda FROM tiposviviendas");
while ($fila = $res->fetch_assoc()) $tiposViviendas[] = $fila;

$paises = [];
$res = $conexion->query("SELECT IdPais, NomPais FROM paises");
while ($fila = $res->fetch_assoc()) $paises[] = $fila;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validar_foto = true; // obligatorio en crear anuncio
    require __DIR__ . '/includes/filtrado_anuncio.php'; // llena $datos y $errores

    if (empty($errores)) {
        $foto_principal = $_FILES['foto_principal'];
        $nombre_archivo = 'img/' . time() . '_' . basename($foto_principal['name']);

        if (!move_uploaded_file($foto_principal['tmp_name'], __DIR__ . '/' . $nombre_archivo)) {
            $errores[] = "Error al guardar la foto principal.";
        } else {
            $stmt = $conexion->prepare("
                INSERT INTO anuncios 
                (TAnuncio, TVivienda, FPrincipal, Alternativo, Titulo, Precio, Texto, Ciudad, Pais,
                 Superficie, NHabitaciones, NBanyos, Planta, Anyo, Usuario)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $alternativo = $datos['descripcion'];
            $usuario = $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? null;

            if (!$usuario) {
                $errores[] = "No se ha podido determinar el usuario que crea el anuncio.";
            } else {
                $types = "iisssdssidiiiii";

                $stmt->bind_param(
                    $types,
                    $datos['tipo_anuncio'],
                    $datos['tipo_vivienda'],
                    $nombre_archivo,
                    $alternativo,
                    $datos['titulo'],
                    $datos['precio'],
                    $datos['descripcion'],
                    $datos['ciudad'],
                    $datos['pais'],
                    $datos['superficie'],
                    $datos['habitaciones'],
                    $datos['banos'],
                    $datos['plantas'],
                    $datos['ano'],
                    $usuario
                );

                if ($stmt->execute()) {
                    $exito = true;
                    $id_anuncio_creado = $conexion->insert_id;
                } else {
                    $errores[] = "Error al insertar el anuncio: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
}
?>

<main>
<section>
    <h1>Crear nuevo anuncio</h1>

    <?php if (!empty($errores)): ?>
        <div class="errores">
            <ul><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
        </div>
    <?php elseif ($exito): ?>
        <div class="exito">
            <p>Anuncio creado correctamente. Ahora puede añadir su primera fotografía al anuncio.</p>
            <a href="anadir_foto.php?id=<?= (int)($id_anuncio_creado ?? 0) ?>">Añadir foto</a>
        </div>
    <?php endif; ?>

    <?php if (!$exito): ?>
        <form action="#" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Datos del anuncio</legend>

                <label for="tipo_anuncio">Tipo de anuncio (*):</label>
                <?php render_select_from_array($tiposAnuncios,'tipo_anuncio','tipo_anuncio','IdTAnuncio','NomTAnuncio', $datos['tipo_anuncio'] ?? null, true); ?><br><br>

                <label for="tipo_vivienda">Tipo de vivienda (*):</label>
                <?php render_select_from_array($tiposViviendas,'tipo_vivienda','tipo_vivienda','IdTVivienda','NomTVivienda', $datos['tipo_vivienda'] ?? null, true); ?><br><br>

                <label for="titulo">Título (*):</label>
                <input type="text" id="titulo" name="titulo" required maxlength="60" value="<?= htmlspecialchars($datos['titulo'] ?? '') ?>"><br><br>

                <label for="ciudad">Ciudad (*):</label>
                <input type="text" id="ciudad" name="ciudad" required maxlength="100" value="<?= htmlspecialchars($datos['ciudad'] ?? '') ?>">
                <label for="pais">País (*):</label>
                <?php render_select_from_array($paises,'pais','pais','IdPais','NomPais', $datos['pais'] ?? null, true); ?><br><br>

                <label for="precio">Precio (*):</label>
                <input type="text" id="precio" name="precio" required maxlength="9" value="<?= htmlspecialchars($datos['precio'] ?? '') ?>"><br><br>

                <label for="descripcion">Descripción (*):</label><br>
                <textarea id="descripcion" name="descripcion" rows="6" cols="50" maxlength="4000" required><?= htmlspecialchars($datos['descripcion'] ?? '') ?></textarea><br><br>

                <fieldset>
                    <legend>Características</legend>
                    <label for="superficie">Superficie:</label>
                    <input type="number" id="superficie" name="superficie" min="0" value="<?= htmlspecialchars($datos['superficie'] ?? '') ?>"><br><br>
                    <label for="habitaciones">Habitaciones:</label>
                    <input type="number" id="habitaciones" name="habitaciones" min="0" value="<?= htmlspecialchars($datos['habitaciones'] ?? '') ?>"><br><br>
                    <label for="banos">Baños:</label>
                    <input type="number" id="banos" name="banos" min="0" value="<?= htmlspecialchars($datos['banos'] ?? '') ?>"><br><br>
                    <label for="plantas">Plantas:</label>
                    <input type="number" id="plantas" name="plantas" min="0" value="<?= htmlspecialchars($datos['plantas'] ?? '') ?>"><br><br>
                    <label for="ano">Año construcción:</label>
                    <input type="number" id="ano" name="ano" min="1800" max="2100" value="<?= htmlspecialchars($datos['ano'] ?? '') ?>"><br><br>
                </fieldset><br>

                <label for="foto_principal">Foto principal (*):</label>
                <input type="file" id="foto_principal" name="foto_principal" required><br><br>

                <button type="submit">Crear anuncio</button>
            </fieldset>
        </form>
    <?php endif; ?>
</section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
