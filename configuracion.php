<?php
require_once __DIR__ . '/includes/cabecera.php';

if (empty($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    echo "<p>Debes iniciar sesión para cambiar la configuración.</p>";
    require __DIR__ . '/includes/pie.php';
    exit;
}

// Guardar estilo seleccionado
if (isset($_POST['estilo'])) {
    $nuevo_estilo_id = intval($_POST['estilo']);
    $_SESSION['estilo'] = $nuevo_estilo_id;
    setcookie('estilo_usuario', $nuevo_estilo_id, time() + 60*60*24*30, "/"); // 30 días
    echo "<p>Estilo actualizado correctamente.</p>";
}

$estilos = [
    1 => 'Normal',
    2 => 'Alto contraste grande',
    3 => 'Alto contraste',
    4 => 'Noche',
    5 => 'Texto grande',
    6 => 'Dislexia'
];

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
