<?php
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';

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

    return $costeTotal;
}

// ---- CAPTURAR DATOS DEL FORMULARIO ----
$nombre = $_POST['nombre'] ?? 'Sin nombre';
$email = $_POST['email'] ?? 'Sin correo';
$textoAdicional = $_POST['textoAdicional'] ?? 'Sin texto adicional';
$anuncio_id = intval($_POST['anuncio'] ?? 0);
$fechaRecepcion = $_POST['fechaRecepcion'] ?? 'No especificada';
$colorImpresion = ($_POST['colorImpresion'] ?? 'byn') === 'color';
$imprimirPrecio = ($_POST['impresionPrecio'] ?? 'no') === 'si';

// ---- OBTENER DATOS DEL ANUNCIO ----
$conexion = get_db();
$stmt = $conexion->prepare("SELECT Titulo, NHabitaciones, NBanyos FROM anuncios WHERE IdAnuncio = ?");
$stmt->bind_param("i", $anuncio_id);
$stmt->execute();
$anuncio = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Número de páginas y fotos (puedes ajustarlo según tu lógica)
$numPaginas = 8;
$numFotos = 3; // o calcula a partir del anuncio si quieres

// ---- CALCULAR COSTE ----
$costeUnitario = calcularCosteFolleto($numPaginas, $numFotos, $colorImpresion, false, $TARIFAS);
$costeTotal = $costeUnitario; // solo una copia, no se pide múltiple

?>

<main>
    <section id="confirmacion">
        <h1>Solicitud registrada con éxito</h1>
        <p>Tu solicitud para imprimir un folleto publicitario ha sido registrada correctamente. A continuación se muestran los detalles:</p>

        <h3>Datos del solicitante</h3>
        <ul>
            <li><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></li>
            <li><strong>Correo electrónico:</strong> <?= htmlspecialchars($email) ?></li>
        </ul>

        <h3>Detalles del folleto</h3>
        <ul>
            <li><strong>Anuncio seleccionado:</strong> <?= htmlspecialchars($anuncio['Titulo'] ?? 'No encontrado') ?></li>
            <li><strong>Fecha aproximada de recepción:</strong> <?= htmlspecialchars($fechaRecepcion) ?></li>
            <li><strong>Impresión a color:</strong> <?= $colorImpresion ? 'Sí' : 'No' ?></li>
            <li><strong>Impresión del precio:</strong> <?= $imprimirPrecio ? 'Sí' : 'No' ?></li>
            <li><strong>Texto adicional:</strong> <?= htmlspecialchars($textoAdicional) ?></li>
        </ul>

        <h3>Coste del folleto</h3>
        <p><strong>Coste total:</strong> <?= number_format($costeTotal, 2, ',', '') ?> €</p>

        <a href="/daw/inicio_user"><button>Volver a la pagina principal</button></a>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
