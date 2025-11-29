<?php
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';

// ---- TARIFAS Y FUNCIÓN DE CÁLCULO ----
$TARIFAS = [
    'COSTO_ENVIO' => 10.00,
    'PAGINAS' => [
        ['max' => 4, 'precio' => 2.00],
        ['max' => 10, 'precio' => 1.80],
        ['max' => INF, 'precio' => 1.60],
    ],
    'COLOR' => 0.50,
    'RESOLUCION_ALTA' => 0.20
];

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

    return round($costeTotal, 2);
}

// Validar sesión
if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    echo "<p>No se ha identificado al usuario. Por favor, inicia sesión.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// CARGAR ANUNCIOS DEL USUARIO
$conexion = get_db();
$sql = "SELECT IdAnuncio, Titulo, Superficie, NHabitaciones, NBanyos, FPrincipal FROM anuncios WHERE Usuario = ? ORDER BY FRegistro DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$mis_anuncios = [];
while ($row = $result->fetch_assoc()) $mis_anuncios[] = $row;
$stmt->close();

$errores = [];
$exito = false;
$costeCalculado = null;

// PROCESAR FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $textoAdicional = trim($_POST['textoAdicional'] ?? '');
    $id_anuncio = intval($_POST['anuncio'] ?? 0);
    $fechaRecepcion = $_POST['fechaRecepcion'] ?? null;
    $colorImpresion = $_POST['colorImpresion'] ?? 'byn';
    $impresionPrecio = $_POST['impresionPrecio'] ?? 'no';

    // Validaciones
    if ($nombre === '') $errores[] = "El nombre es obligatorio.";
    if ($email === '') $errores[] = "El correo electrónico es obligatorio.";
    if ($id_anuncio <= 0) $errores[] = "Debe seleccionar un anuncio válido.";

    // Comprobar que el anuncio pertenece al usuario
    $anuncioSeleccionado = null;
    foreach ($mis_anuncios as $a) {
        if ($a['IdAnuncio'] === $id_anuncio) {
            $anuncioSeleccionado = $a;
            break;
        }
    }
    if (!$anuncioSeleccionado) $errores[] = "Anuncio no válido o no pertenece al usuario.";

    // Si no hay errores, calcular coste y guardar solicitud
    if (empty($errores)) {
        // Usar datos del anuncio para cálculo real
        $numPaginas = 8; // por ejemplo, podrías personalizar
        $numFotos = 3;   // por ejemplo, o contar fotos reales si quieres
        $esColor = ($colorImpresion === 'color');
        $esAltaResolucion = true; // por defecto

        $costeCalculado = calcularCosteFolleto($numPaginas, $numFotos, $esColor, $esAltaResolucion, $TARIFAS);

        // Insertar solicitud en la BD
        $stmt = $conexion->prepare("
            INSERT INTO solicitudes_folleto 
            (Usuario, Anuncio, Nombre, Email, TextoAdicional, FechaRecepcion, Color, ImpresionPrecio, Coste)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iissssssd",
            $usuario_id,
            $id_anuncio,
            $nombre,
            $email,
            $textoAdicional,
            $fechaRecepcion,
            $colorImpresion,
            $impresionPrecio,
            $costeCalculado
        );

        if ($stmt->execute()) $exito = true;
        else $errores[] = "Error al guardar la solicitud: ".$stmt->error;
        $stmt->close();
    }
}
?>

<main>
    <section id="descripcion-folleto">
        <h1>SOLICITUD DE IMPRESIÓN DE FOLLETO PUBLICITARIO</h1>
        <p>
            Mediante esta opción puedes solicitar la impresión y envío de uno de tus anuncios.
            El precio variará en función del número de páginas, fotos y opciones de impresión.
        </p>
    </section>

    <!-- Tabla de tarifas -->
    <section id="tabla-tarifas">
        <h3>Tarifas</h3>

        <button id="mostrarTablaPHP" class="boton-tabla">Mostrar Tabla (PHP)</button>

        <div id="tablaPHP" style="display:none;">
            <table class="tabla-costes-generada">
                <thead>
                    <tr>
                        <th rowspan="2">Número de páginas</th>
                        <th rowspan="2">Número de fotos</th>
                        <th colspan="2">Blanco y negro</th>
                        <th colspan="2">Color</th>
                    </tr>
                    <tr>
                        <th>150-300 dpi</th>
                        <th>450-900 dpi</th>
                        <th>150-300 dpi</th>
                        <th>450-900 dpi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 1; $i <= 15; $i++) {
                        $paginas = $i;
                        $fotos = $i * 3;
                        echo "<tr>";
                        echo "<td>{$paginas}</td>";
                        echo "<td>{$fotos}</td>";

                        $combinaciones = [
                            [false, false],
                            [false, true],
                            [true, false],
                            [true, true]
                        ];

                        foreach ($combinaciones as [$color, $alta]) {
                            $coste = calcularCosteFolleto($paginas, $fotos, $color, $alta, $TARIFAS);
                            echo "<td>".number_format($coste, 2, ',', '')." €</td>";
                        }

                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <hr>

        <button id="mostrarTabla" class="boton-tabla">Mostrar Tabla (JavaScript)</button>
        <div id="tableContainer" style="display:none;"></div>

        <script src="js/tabla_costes.js"></script>
        <script>
        document.addEventListener("DOMContentLoaded", () => {
            const botonPHP = document.getElementById("mostrarTablaPHP");
            const tablaPHP = document.getElementById("tablaPHP");

            botonPHP.addEventListener("click", () => {
                const visible = tablaPHP.style.display === "block";
                tablaPHP.style.display = visible ? "none" : "block";
                botonPHP.textContent = visible
                    ? "Mostrar Tabla (PHP)"
                    : "Ocultar Tabla (PHP)";
            });
        });
        </script>
    </section>

    <!-- Mensajes -->
    <?php if(!empty($errores)): ?>
        <div class="errores">
            <ul><?php foreach($errores as $error): ?><li><?=htmlspecialchars($error)?></li><?php endforeach;?></ul>
        </div>
    <?php elseif($exito): ?>
        <div class="exito">
            <p>Solicitud enviada correctamente. Coste calculado: <?=number_format($costeCalculado,2,',','')?> €</p>
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <section id="formulario">
        <form action="solicitar_folleto_respuesta.php" method="post">
            <fieldset>
                <legend>Datos Personales</legend>

                <label for="nombre">Nombre completo (*):</label>
                <input type="text" id="nombre" name="nombre" maxlength="200" required><br><br>

                <label for="email">Correo electrónico (*):</label>
                <input type="email" id="email" name="email" maxlength="200" required><br><br>

                <label for="textoAdicional">Texto adicional:</label>
                <textarea id="textoAdicional" name="textoAdicional" maxlength="4000" rows="4" cols="50"></textarea><br><br>

                <label for="anuncio">Anuncio (*):</label>
                <select name="anuncio" id="anuncio" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($mis_anuncios as $anuncio): ?>
                        <option value="<?= htmlspecialchars($anuncio['IdAnuncio'], ENT_QUOTES) ?>">
                            <?= htmlspecialchars($anuncio['Titulo'], ENT_QUOTES) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="fechaRecepcion">Fecha de recepción:</label>
                <input type="date" id="fechaRecepcion" name="fechaRecepcion"><br><br>

                <p>Color de la impresión</p>
                <input type="radio" id="aColor" name="colorImpresion" value="color">
                <label for="aColor">Color</label>
                <input type="radio" id="colorNo" name="colorImpresion" value="byn">
                <label for="colorNo">Blanco y negro</label><br><br>

                <p>¿Imprimir el precio en el folleto?</p>
                <input type="radio" id="precioSi" name="impresionPrecio" value="si">
                <label for="precioSi">Sí</label>
                <input type="radio" id="precioNo" name="impresionPrecio" value="no">
                <label for="precioNo">No</label><br><br>

                <button type="submit">Solicitar</button>
            </fieldset>
        </form>

    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
