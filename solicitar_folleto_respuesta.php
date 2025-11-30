<?php
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';

session_start();

// Comprobar que hay datos en sesión
if (!isset($_SESSION['datosFolleto'])) {
    echo "<p>No hay datos de solicitud. Vuelve a solicitar el folleto.</p>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}

$datos = $_SESSION['datosFolleto'];

//Obtener los datos del formulario
$nombre = $datos['nombre'];
$email = $datos['email'];
$textoAdicional = $datos['textoAdicional'];
$anuncio_id = intval($datos['id_anuncio']);
$fechaRecepcion = $datos['fechaEntrega'];
$colorImpresion = $datos['colorImpresion'] === 'Color';
$imprimirPrecio = $datos['imprimirPrecio'];
$numPaginas = $datos['numPaginas'];
$numFotos = $datos['numFotos'];
$costeTotal = $datos['costeTotal'];

// Obtener detalles del anuncio
$conexion = get_db();
$stmt = $conexion->prepare("SELECT Titulo, NHabitaciones, NBanyos FROM anuncios WHERE IdAnuncio = ?");
$stmt->bind_param("i", $anuncio_id);
$stmt->execute();
$anuncio = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<main>
    <section id="confirmacion">
        <h1>Solicitud registrada con éxito</h1>
        <p>Tu solicitud para imprimir un folleto publicitario ha sido registrada correctamente. A continuación se muestran los detalles:</p>

        <h3>Datos del solicitante</h3>
        <ul>
            <li><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></li>
            <li><strong>Correo electrónico:</strong> <?= htmlspecialchars($email) ?></li>
            <li><strong>Dirección de entrega:</strong> <?= htmlspecialchars($datos['direccion']) ?></li>
        </ul>

        <h3>Detalles del folleto</h3>
        <ul>
            <li><strong>Anuncio seleccionado:</strong> <?= htmlspecialchars($anuncio['Titulo'] ?? 'No encontrado') ?></li>
            <li><strong>Fecha aproximada de recepción:</strong> <?= htmlspecialchars($fechaRecepcion) ?></li>
            <li><strong>Número de páginas:</strong> <?= $numPaginas ?></li>
            <li><strong>Número de fotos:</strong> <?= $numFotos ?></li>
            <li><strong>Impresión a color:</strong> <?= $colorImpresion ? 'Sí' : 'No' ?></li>
            <li><strong>Alta resolución:</strong> <?= $datos['altaResolucion'] ? 'Sí' : 'No' ?></li>
            <li><strong>Impresión del precio:</strong> <?= $imprimirPrecio ? 'Sí' : 'No' ?></li>
            <li><strong>Texto adicional:</strong> <?= htmlspecialchars($textoAdicional) ?></li>
        </ul>

        <h3>Coste del folleto</h3>
        <p><strong>Coste total:</strong> <?= number_format($costeTotal, 2, ',', '') ?> €</p>

        <a href="/daw/inicio_user"><button>Volver a la página principal</button></a>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
