<?php
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/basedatos.php';

if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    echo "<p>Debes iniciar sesión para cambiar la configuración.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}

$conexion = get_db();

if (isset($_POST['estilo'])) {
    $nuevo_estilo_id = intval($_POST['estilo']);
    
    // guardo el estilo en la sesión y cookie
    $_SESSION['estilo'] = $nuevo_estilo_id;
    setcookie('estilo_usuario', $nuevo_estilo_id, time() + 60*60*24*30, "/"); // 30 días
    
    // ahora lo guardo en la base de datos
    $userId = $_SESSION['usuario_id'] ?? null;
    if ($userId) {
        $stmt = $conexion->prepare("UPDATE usuarios SET Estilo = ? WHERE IdUsuario = ?");
        $stmt->bind_param("ii", $nuevo_estilo_id, $userId);
        $stmt->execute();
        $stmt->close();
    }

    echo "<p>Estilo actualizado correctamente.</p>";
}



//cargo los estilos de la base de datos
$estilos = [];
$res = $conexion->query("SELECT IdEstilo, Nombre FROM estilos ORDER BY Nombre ASC");
while ($row = $res->fetch_assoc()) {
    $estilos[(int)$row['IdEstilo']] = $row['Nombre'];
}

$estilo_actual = $_SESSION['estilo'] ?? $_COOKIE['estilo_usuario'] ?? 1;
?>

<main>
    <h1>Configuración de estilos</h1>
    <form method="POST" id="form-estilos">
        <?php foreach ($estilos as $id => $nombre): ?>
            <a href="#" 
               class="boton-enlace perfil-menu <?= ($id == $estilo_actual) ? 'activo' : '' ?>"
               onclick="document.getElementById('estilo-<?= $id ?>').click(); return false;">
               <?= htmlspecialchars($nombre) ?>
            </a>
            <button type="submit" name="estilo" value="<?= $id ?>" id="estilo-<?= $id ?>" style="display:none;"></button>
        <?php endforeach; ?>
    </form>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
