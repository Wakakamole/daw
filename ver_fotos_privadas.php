<?php
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';

// Verificar si el usuario está logueado
if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    echo "<p>No se ha identificado al usuario. Por favor, inicia sesión.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}

// Recoger id del anuncio
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<p>Anuncio no válido.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}

// Conectar a la base de datos
$db = get_db();

// CARGAR DATOS DEL ANUNCIO Y VERIFICAR PROPIEDAD
$sql = "SELECT a.IdAnuncio, a.Titulo, a.Ciudad, a.Pais, a.Precio, a.FPrincipal,
               ta.NomTAnuncio AS tipo_anuncio, tv.NomTVivienda AS tipo_vivienda,
               a.Usuario AS propietario
        FROM anuncios a
        LEFT JOIN tiposanuncios ta ON a.TAnuncio = ta.IdTAnuncio
        LEFT JOIN tiposviviendas tv ON a.TVivienda = tv.IdTVivienda
        WHERE a.IdAnuncio = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No se encontró el anuncio.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}

$anuncio = $result->fetch_assoc();
$stmt->close();

// Verificar que el anuncio pertenece al usuario logueado
if ((int)$anuncio['propietario'] !== (int)$_SESSION['id_usuario']) {
    echo "<p>No tienes permisos para ver las fotos de este anuncio.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}


// CARGAR TODAS LAS FOTOS DEL ANUNCIO
$sqlFotos = "SELECT Foto FROM fotos WHERE Anuncio = ?";
$stmtFotos = $db->prepare($sqlFotos);
$stmtFotos->bind_param('i', $id);
$stmtFotos->execute();
$resultFotos = $stmtFotos->get_result();

$fotos = [];
while ($fila = $resultFotos->fetch_assoc()) {
    $fotos[] = $fila['Foto'];
}
$stmtFotos->close();

$total_fotos = count($fotos);

function h($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>

<main id="ver-fotos-privadas">
    <section id="info-anuncio">
        <h1>Fotos del anuncio: <?= h($anuncio['Titulo']) ?></h1>
        <p><strong>Tipo de anuncio:</strong> <?= h($anuncio['tipo_anuncio']) ?> | 
           <strong>Tipo de vivienda:</strong> <?= h($anuncio['tipo_vivienda']) ?></p>
        <p><strong>Ciudad:</strong> <?= h($anuncio['Ciudad']) ?> | 
           <strong>País:</strong> <?= h($anuncio['Pais']) ?></p>
        <p><strong>Precio:</strong> <?= h($anuncio['Precio']) ?> €</p>
        <p><strong>Total de fotos:</strong> <?= $total_fotos ?></p>
    </section>

    <?php if ($total_fotos === 0): ?>
        <p style="text-align:center; margin-top:2rem;">No hay fotos disponibles para este anuncio.</p>
    <?php else: ?>
        <!-- Foto principal -->
        <?php if (!empty($anuncio['FPrincipal'])): ?>
            <section id="foto-principal">
                <img src="<?= h($anuncio['FPrincipal']) ?>" alt="Foto principal <?= h($anuncio['Titulo']) ?>">
            </section>
        <?php endif; ?>

        <!-- Otras fotos -->
        <?php if ($total_fotos > 0): ?>
            <section id="otras-fotos">
                <?php foreach ($fotos as $foto): ?>
                    <div class="foto-item">
                        <img src="<?= h($foto) ?>" alt="Foto del anuncio <?= h($anuncio['Titulo']) ?>">
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
