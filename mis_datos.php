<?php
require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/basedatos.php';
require_once __DIR__ . '/includes/cabecera.php';

if (!function_exists('h')) { function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); } }

$db = get_db();
$usuario_sess = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';
$usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

if ($usuario_sess === '' && $usuario_id <= 0) {
    echo "<main><p>No hay usuario autenticado.</p></main>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}

$user = null;
if ($usuario_id > 0) {
    $stmt = $db->prepare("SELECT IdUsuario, NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, Estilo FROM Usuarios WHERE IdUsuario = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $usuario_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res ? $res->fetch_assoc() : null;
        if ($res) { $res->free(); }
        $stmt->close();
    }
}

if (!$user && $usuario_sess !== '') {
    $stmt = $db->prepare("SELECT IdUsuario, NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, Estilo FROM Usuarios WHERE NomUsuario = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $usuario_sess);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res ? $res->fetch_assoc() : null;
        if ($res) { $res->free(); }
        $stmt->close();
    }
}

if (!$user) {
    echo "<main><p>Usuario no encontrado.</p></main>";
    require_once __DIR__ . '/includes/pie.php';
    exit;
}


// Mapear el valor almacenado en la BD (ej. 1/2) a las opciones del formulario ('hombre','mujer','otro')
function map_sexo_bd_a_form($val) {
    if ($val === null || $val === '') return '';
    // si viene numérico, mapear a etiquetas
    if (is_numeric($val)) {
        $n = (int)$val;
        return match($n) {
            1 => 'hombre',
            2 => 'mujer',
            3 => 'otro',
            default => ''
        };
    }
    // si ya es texto, normalizar
    $s = strtolower(trim((string)$val));
    if (in_array($s, ['hombre','mujer','otro'])) return $s;
    return '';
}

$usuario = [
    'id' => (int)$user['IdUsuario'],
    'usuario' => $user['NomUsuario'],
    'email' => $user['Email'] ?? '',
    'sexo' => map_sexo_bd_a_form($user['Sexo'] ?? ''),
    'fecha_nacimiento' => $user['FNacimiento'] ?? '',
    'ciudad' => $user['Ciudad'] ?? '',
    'pais' => $user['Pais'] ?? '',
    'foto' => $user['Foto'] ?? '',
    'estilo' => $user['Estilo'] ?? ''
];

$modo = 'edicion';
$action = 'respuestaregistro.php';
$submitText = 'Guardar cambios';

?>

<main>
    <h1>Mis datos</h1>
    <p>A continuación puedes ver tus datos.</p>

    <?php require __DIR__ . '/includes/formulario_user.php'; ?>

</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
