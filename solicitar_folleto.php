<?php
require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/basedatos.php';

$conexion = get_db();
$errores = [];

define('FOTOS_POR_PAGINA', 3);

//Tarifas
$TARIFAS = [
    'COSTO_ENVIO' => 10.0,
    'PAGINAS' => [
        ['max' => 4, 'precio' => 2.0],
        ['max' => 10, 'precio' => 1.8],
        ['max' => INF, 'precio' => 1.6],
    ],
    'COLOR' => 0.5,
    'RESOLUCION_ALTA' => 0.2
];

// Obtener anuncios para el select
$anuncios = [];
$res = $conexion->query("SELECT IdAnuncio, Titulo FROM anuncios ORDER BY FRegistro DESC");
while ($fila = $res->fetch_assoc()) $anuncios[] = $fila;

// Función para calcular coste del folleto
function calcularCosteFolleto($numPaginas, $numFotos, $esColor, $esAltaResolucion, $tarifas) {
    $costePaginas = 0.0;
    $paginasRestantes = $numPaginas;
    $paginasPrevMax = 0;

    foreach ($tarifas['PAGINAS'] as $tarifa) {
        $paginasEnTramo = min($paginasRestantes, $tarifa['max'] - $paginasPrevMax);
        $costePaginas += $paginasEnTramo * $tarifa['precio'];
        $paginasRestantes -= $paginasEnTramo;
        $paginasPrevMax = $tarifa['max'];
        if ($paginasRestantes <= 0) break;
    }

    $costeTotal = $costePaginas + $tarifas['COSTO_ENVIO'];
    if ($esColor) $costeTotal += $numFotos * $tarifas['COLOR'];
    if ($esAltaResolucion) $costeTotal += $numFotos * $tarifas['RESOLUCION_ALTA'];

    return $costeTotal;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_anuncio = intval($_POST['anuncio'] ?? 0);
    $colorImpresion = $_POST['color'] ?? 'Blanco';
    $esAltaResolucion = isset($_POST['alta_resolucion']);
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $textoAdicional = trim($_POST['textoAdicional'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $fechaEntrega = $_POST['fechaEntrega'] ?? null;
    $imprimirPrecio = isset($_POST['impresionPrecio']) ? 1 : 0;

    // Validaciones
    if (!$id_anuncio) $errores[] = "Debe seleccionar un anuncio.";
    if (!$nombre) $errores[] = "Debe indicar su nombre.";
    if (!$email) $errores[] = "Debe indicar su correo electrónico.";
    if (!$direccion) $errores[] = "Debe indicar la dirección de entrega.";
    if (!$fechaEntrega) $errores[] = "Debe indicar la fecha de entrega.";

    if (empty($errores)) {
        // Contar fotos del anuncio
        $stmt = $conexion->prepare("SELECT COUNT(*) AS numFotos FROM fotos WHERE Anuncio = ?");
        $stmt->bind_param("i", $id_anuncio);
        $stmt->execute();
        $result = $stmt->get_result();
        $fila = $result->fetch_assoc();
        $numFotosAnuncio = intval($fila['numFotos'] ?? 0);
        $stmt->close();

        if ($numFotosAnuncio === 0) {
            $errores[] = "Este anuncio no tiene fotos para generar un folleto.";
        } else {
            // Calcular número de páginas
            $numPaginas = ceil($numFotosAnuncio / FOTOS_POR_PAGINA);

            // Calcular coste
            $costeTotal = calcularCosteFolleto($numPaginas, $numFotosAnuncio, $colorImpresion === 'Color', $esAltaResolucion, $TARIFAS);

            // Guardar datos en SESSION y redirigir
            session_start();
            $_SESSION['datosFolleto'] = [
                'nombre' => $nombre,
                'email' => $email,
                'direccion' => $direccion,
                'fechaEntrega' => $fechaEntrega,
                'textoAdicional' => $textoAdicional,
                'id_anuncio' => $id_anuncio,
                'colorImpresion' => $colorImpresion,
                'altaResolucion' => $esAltaResolucion,
                'imprimirPrecio' => $imprimirPrecio,
                'numFotos' => $numFotosAnuncio,
                'numPaginas' => $numPaginas,
                'costeTotal' => $costeTotal
            ];

            header("Location: solicitar_folleto_respuesta.php");
            exit;
        }
    }
}

require_once __DIR__ . '/includes/cabecera.php';

?>

<main>
<section>
    <h1>Solicitar folleto</h1>

    <?php if (!empty($errores)): ?>
        <div class="errores">
            <ul><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form action="#" method="post">
        <fieldset>
            <legend>Datos del solicitante</legend>

            <label for="nombre">Nombre (*):</label>
            <input type="text" name="nombre" id="nombre" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"><br><br>

            <label for="email">Correo electrónico (*):</label>
            <input type="email" name="email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"><br><br>

            <label for="direccion">Dirección de entrega (*):</label>
            <input type="text" name="direccion" id="direccion" required value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>"><br><br>

            <label for="fechaEntrega">Fecha de entrega (*):</label>
            <input type="date" name="fechaEntrega" id="fechaEntrega" required value="<?= htmlspecialchars($_POST['fechaEntrega'] ?? '') ?>"><br><br>

            <label for="textoAdicional">Texto adicional:</label><br>
            <textarea name="textoAdicional" id="textoAdicional" rows="4" cols="50"><?= htmlspecialchars($_POST['textoAdicional'] ?? '') ?></textarea><br><br>
        </fieldset>

        <fieldset>
            <legend>Datos del anuncio</legend>

            <label for="anuncio">Selecciona el anuncio (*):</label>
            <select id="anuncio" name="anuncio" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($anuncios as $a): ?>
                    <option value="<?= (int)$a['IdAnuncio'] ?>" <?= (isset($_POST['anuncio']) && $_POST['anuncio'] == $a['IdAnuncio']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['Titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="color">Color de impresión:</label>
            <select id="color" name="color">
                <option value="Blanco" <?= (isset($_POST['color']) && $_POST['color'] === 'Blanco') ? 'selected' : '' ?>>Blanco y negro</option>
                <option value="Color" <?= (isset($_POST['color']) && $_POST['color'] === 'Color') ? 'selected' : '' ?>>Color</option>
            </select><br><br>

            <label>
                <input type="checkbox" name="alta_resolucion" <?= isset($_POST['alta_resolucion']) ? 'checked' : '' ?>> Alta resolución
            </label><br><br>

            <label>
                <input type="checkbox" name="impresionPrecio" <?= isset($_POST['impresionPrecio']) ? 'checked' : '' ?>> Imprimir el precio en el folleto
            </label><br><br>

            <button type="submit">Solicitar folleto</button>
        </fieldset>
    </form>
</section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>