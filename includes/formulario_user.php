<?php
// Formulario reutilizable para registro/edición de usuario

if (!isset($modo)) { $modo = 'registro'; }
if (!isset($usuario) || !is_array($usuario)) { $usuario = []; }
if (!function_exists('h')) { function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); } }

$action = isset($action) ? $action : 'respuestaregistro.php';
$submitText = isset($submitText) ? $submitText : ($modo === 'registro' ? 'Registrarme' : 'Guardar cambios');

?>

<form id="userForm" action="<?php echo h($action); ?>" method="post" enctype="multipart/form-data" novalidate>
    <fieldset>
        <legend><?php echo ($modo === 'registro') ? 'Datos de Registro' : 'Mis Datos'; ?></legend>

        <label for="usuario">Nombre de usuario: </label>
        <input type="text" id="usuario" name="usuario" placeholder="Nombre de usuario" value="<?php echo h($usuario['usuario'] ?? ''); ?>" <?php echo ($modo === 'edicion') ? 'readonly' : ''; ?>>
        <br><br>

        <label for="contrasena">Contraseña: </label>
        <input type="password" id="contrasena" name="password" placeholder="Contraseña">
        <?php if ($modo === 'registro'): ?>
            <br><br>
            <label for="confirmar_contrasena">Confirmar Contraseña: </label>
            <input type="password" id="confirmar_contrasena" name="repite" placeholder="Confirmar Contraseña">
        <?php else: ?>
            <p class="help">Dejar en blanco si no deseas cambiar la contraseña.</p>
        <?php endif; ?>
        <br><br>

        <label for="email">Dirección de correo electrónico: </label>
        <input type="text" id="email" name="email" placeholder="Correo electrónico" value="<?php echo h($usuario['email'] ?? ''); ?>">
        <br><br>

        <section class="radio-group" aria-labelledby="sexo_label">
        <h4 id="sexo_label">Sexo:</h4>
        <label for="sexo_hombre">
            Hombre
            <input type="radio" id="sexo_hombre" name="sexo" value="hombre" <?php echo (($usuario['sexo'] ?? '') === 'hombre') ? 'checked' : ''; ?>>
        </label>
        <label for="sexo_mujer">
            Mujer
            <input type="radio" id="sexo_mujer" name="sexo" value="mujer" <?php echo (($usuario['sexo'] ?? '') === 'mujer') ? 'checked' : ''; ?>>
        </label>
        <label for="sexo_otro">
            Otro
            <input type="radio" id="sexo_otro" name="sexo" value="otro" <?php echo (($usuario['sexo'] ?? '') === 'otro') ? 'checked' : ''; ?>>
        </label>
        </section>
        <br><br>

        <label>Fecha de nacimiento: </label>
        <?php
        $fn = $usuario['fecha_nacimiento'] ?? '';
        $d = $m = $y = '';
        if ($fn) {
            $parts = explode('-', $fn);
            if (count($parts) === 3) { $y = $parts[0]; $m = $parts[1]; $d = ltrim($parts[2], '0'); }
        }
        ?>
        <input type="number" id="dia_nacimiento" name="dia_nacimiento" placeholder="Día" style="width:70px" min="1" max="31" step="1" value="<?php echo h($d); ?>"> 
        <select id="mes_nacimiento" name="mes_nacimiento" aria-label="Mes">
            <?php
            $meses = [ '01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre' ];
            foreach ($meses as $valm=>$nom) {
                $sel = ($valm === $m) ? ' selected' : '';
                echo "<option value=\"$valm\"$sel>$nom</option>\n";
            }
            ?>
        </select>
        <input type="text" id="anio_nacimiento" name="anio_nacimiento" placeholder="Año" style="width:90px" maxlength="4" inputmode="numeric" value="<?php echo h($y); ?>">
        <input type="hidden" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo h($usuario['fecha_nacimiento'] ?? ''); ?>">
        <br><br>

        <label for="ciudad">Ciudad</label>
        <input type="text" id="ciudad" name="ciudad" placeholder="Ciudad de residencia" value="<?php echo h($usuario['ciudad'] ?? ''); ?>">
        <br><br>

        <label for="pais">País:</label>
        <?php
        require_once __DIR__ . '/basedatos.php';
        $db = get_db();
        $pais_selected = $usuario['pais'] ?? '';
        $paises_stmt = $db->prepare("SELECT IdPais, NomPais FROM Paises ORDER BY NomPais");
        $paises = [];
        if ($paises_stmt) {
            $paises_stmt->execute();
            $res = $paises_stmt->get_result();
            if ($res) { $paises = $res->fetch_all(MYSQLI_ASSOC); $res->free(); }
            $paises_stmt->close();
        }
        ?>
        <select id="pais" name="pais">
            <option value="">-- Selecciona --</option>
            <?php foreach ($paises as $p): ?>
                <option value="<?php echo (int)$p['IdPais']; ?>" <?php echo ($pais_selected == $p['IdPais']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['NomPais']); ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="foto">Foto de perfil: </label>
        <input type="file" id="foto" name="foto" accept="image/*">
        <?php if (!empty($usuario['foto'])): ?>
            <p>Foto actual: <img src="<?php echo h($usuario['foto']); ?>" alt="Foto" width="80"></p>
        <?php endif; ?>
        <br><br>

        <?php if ($modo === 'edicion' && !empty($usuario['id'])): ?>
            <input type="hidden" name="idusuario" value="<?php echo (int)$usuario['id']; ?>">
        <?php endif; ?>
        <input type="hidden" name="modo" value="<?php echo h($modo); ?>">

        <button type="submit"><?php echo h($submitText); ?></button>
    </fieldset>
</form>

<?php
// small script to compose hidden fecha_nacimiento from day/month/year on submit
?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var form = document.getElementById('userForm');
    if (!form) return;
    form.addEventListener('submit', function(){
        var d = document.getElementById('dia_nacimiento').value || '';
        var m = document.getElementById('mes_nacimiento').value || '';
        var y = document.getElementById('anio_nacimiento').value || '';
        var hidden = document.getElementById('fecha_nacimiento');
        if (d && m && y) {
            if (d.length === 1) d = '0' + d;
            hidden.value = y + '-' + m + '-' + d;
        }
    });
});
</script>
