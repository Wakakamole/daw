<?php
require_once __DIR__ . '/includes/cabecera.php';

// ---- TARIFAS ----
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

// ---- FUNCIÓN DE CÁLCULO ----
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
$numPaginas = 8; // Valor ficticio
$numFotos = 24;   // Valor ficticio

$nombre = $_POST['nombre'] ?? 'Sin nombre';
$email = $_POST['email'] ?? 'Sin correo';
$telefono = $_POST['telefono'] ?? 'No proporcionado';
$calle = $_POST['calle'] ?? '';
$numero = $_POST['numero'] ?? '';
$cp = $_POST['cp'] ?? '';
$localidad = $_POST['localidad'] ?? '';
$provincia = $_POST['provincia'] ?? '';
$pais = $_POST['pais'] ?? '';
$colorPortada = $_POST['colorPortada'] ?? '#000000';
$copias = (int)($_POST['copias'] ?? 1);
$resolucion = $_POST['resolucion'] ?? '150';
$anuncio = $_POST['anuncio'] ?? 'Sin anuncio';
$fechaRecepcion = $_POST['fechaRecepcion'] ?? 'No especificada';
$colorImpresion = ($_POST['colorImpresion'] ?? 'byn') === 'color';
$imprimirPrecio = ($_POST['impresionPrecio'] ?? 'no') === 'si';
$textoAdicional = $_POST['textoAdicional'] ?? 'Sin texto adicional';

// ---- CALCULAR COSTE ----
$costeUnitario = calcularCosteFolleto($numPaginas, $numFotos, $colorImpresion, $resolucion >= 450, $TARIFAS);
$costeTotal = $costeUnitario * $copias;
?>

<main>
    <section id="confirmacion">
        <h1>Solicitud registrada con éxito</h1>
        <p>Tu solicitud para imprimir un folleto publicitario ha sido registrada correctamente. A continuación se muestran los detalles:</p>

        <h3>Datos del solicitante</h3>
        <ul>
            <li><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></li>
            <li><strong>Correo electrónico:</strong> <?= htmlspecialchars($email) ?></li>
            <li><strong>Teléfono:</strong> <?= htmlspecialchars($telefono) ?></li>
        </ul>

        <h3>Dirección de envío</h3>
        <ul>
            <li><strong>Calle:</strong> <?= htmlspecialchars($calle) ?></li>
            <li><strong>Número:</strong> <?= htmlspecialchars($numero) ?></li>
            <li><strong>CP:</strong> <?= htmlspecialchars($cp) ?></li>
            <li><strong>Localidad:</strong> <?= htmlspecialchars($localidad) ?></li>
            <li><strong>Provincia:</strong> <?= htmlspecialchars($provincia) ?></li>
            <li><strong>País:</strong> <?= htmlspecialchars($pais) ?></li>
        </ul>

        <h3>Detalles del folleto</h3>
        <ul>
            <li><strong>Color de la portada:</strong> <?= htmlspecialchars($colorPortada) ?></li>
            <li><strong>Número de copias:</strong> <?= $copias ?></li>
            <li><strong>Resolución:</strong> <?= htmlspecialchars($resolucion) ?> dpi</li>
            <li><strong>Anuncio seleccionado:</strong> <?= htmlspecialchars($anuncio) ?></li>
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
