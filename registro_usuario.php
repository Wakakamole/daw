<?php
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$errors = [];

// Preferir errores de sesión (flash) cuando vengan de un submit; esto unifica comportamiento con inicio_sesion
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!empty($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}
// Si no hay errores en sesión, conservar compatibilidad con la versión anterior que usaba GET
elseif (!empty($_GET['errors'])) {
    $errors = array_filter(explode(',', $_GET['errors']));
}

$vals = [];
$fields = ['usuario','email','sexo','fecha_nacimiento','ciudad','pais'];
// Priorizar repoblado desde sesión ($_SESSION['old']) si existe
if (!empty($_SESSION['old'])) {
    $old = $_SESSION['old'];
    foreach ($fields as $f) { $vals[$f] = isset($old[$f]) ? $old[$f] : ''; }
    unset($_SESSION['old']);
} else {
    foreach ($fields as $f) { $vals[$f] = isset($_GET[$f]) ? $_GET[$f] : ''; }
}

// Si hay fecha_nacimiento la parseamos para rellenar día/mes/año por separado
$d = '';
$m = '';
$y = '';
if ($vals['fecha_nacimiento']) {
    $parts = explode('-', $vals['fecha_nacimiento']);
    if (count($parts) === 3) {
        $y = $parts[0];
        $m = $parts[1];
        $d = ltrim($parts[2], '0');
    }
}

// Mapa de mensajes legibles
$mensajes = [
    'usuario' => 'El nombre de usuario es obligatorio.',
    'password_empty' => 'La contraseña no puede estar vacía.',
    'repite_empty' => 'Debes repetir la contraseña.',
    'password_mismatch' => 'Las contraseñas no coinciden.'
];

?>
<?php
// Usar cabecera común
$page_title = 'INMOLINK - Registro';
require_once __DIR__ . '/includes/cabecera.php';
?>

    <main>
        <h1>REGISTRO USUARIO</h1>

        <?php if (!empty($errors)): ?>
            <section class="error-summary" role="alert" aria-live="assertive">
                <p><strong>Se han encontrado errores en el formulario:</strong></p>
                <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?php echo h(isset($mensajes[$e]) ? $mensajes[$e] : $e); ?></li>
                <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

    <form id="registerForm" action="respuestaregistro.php" method="post" novalidate>
            <fieldset>
                <legend>Datos de Registro</legend>
                <br>

                <label for="usuario">Nombre de usuario: </label>
                <input type="text" id="usuario" name="usuario" placeholder="Nombre de usuario" value="<?php echo h($vals['usuario']); ?>">
                <?php if (in_array('usuario',$errors)): ?> <small class="field-error-text"><?php echo h($mensajes['usuario']); ?></small><?php endif; ?>
                <br><br>

                <label for="contrasena">Contraseña: </label>
                <input type="password" id="contrasena" name="password" placeholder="Contraseña">
                <?php if (in_array('password_empty',$errors)): ?> <small class="field-error-text"><?php echo h($mensajes['password_empty']); ?></small><?php endif; ?>
                <br><br>

                <label for="confirmar_contrasena">Confirmar Contraseña: </label>
                <input type="password" id="confirmar_contrasena" name="repite" placeholder="Confirmar Contraseña">
                <?php if (in_array('repite_empty',$errors)): ?> <small class="field-error-text"><?php echo h($mensajes['repite_empty']); ?></small><?php endif; ?>
                <?php if (in_array('password_mismatch',$errors)): ?> <small class="field-error-text"><?php echo h($mensajes['password_mismatch']); ?></small><?php endif; ?>
                <br><br>

                <label for="email">Dirección de correo electrónico: </label>
                <input type="text" id="email" name="email" placeholder="Correo electrónico" value="<?php echo h($vals['email']); ?>">
                <br><br>

                <section class="radio-group" aria-labelledby="sexo_label">
                <h4 id="sexo_label">Sexo:</h4>
                <label for="sexo_hombre">
                    Hombre
                    <input type="radio" id="sexo_hombre" name="sexo" value="hombre" <?php echo ($vals['sexo']==='hombre') ? 'checked' : ''; ?>>
                </label>
                <label for="sexo_mujer">
                    Mujer
                    <input type="radio" id="sexo_mujer" name="sexo" value="mujer" <?php echo ($vals['sexo']==='mujer') ? 'checked' : ''; ?>>
                </label>
                <label for="sexo_otro">
                    Otro
                    <input type="radio" id="sexo_otro" name="sexo" value="otro" <?php echo ($vals['sexo']==='otro') ? 'checked' : ''; ?>>
                </label>
                </section>
                <br><br>

                <label>Fecha de nacimiento: </label>
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
                <!-- Hidden field que realmente se enviará con formato YYYY-MM-DD -->
                <input type="hidden" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo h($vals['fecha_nacimiento']); ?>">
                <br><br>

                <label for="ciudad">Ciudad</label>
                <input type="text" id="ciudad" name="ciudad" placeholder="Ciudad de residencia" value="<?php echo h($vals['ciudad']); ?>">
                <br><br>

                <label for="pais">País:</label>
                <input list="lista-paises" id="pais" name="pais" placeholder="Selecciona un país" value="<?php echo h($vals['pais']); ?>">
                <datalist id="lista-paises">
                    <option value="España">
                    <option value="México">
                    <option value="Argentina">
                    <option value="Colombia">
                    <option value="Chile">
                    <option value="Perú">
                    <option value="Italia">
                    <option value="Grecia">
                    <option value="Canada">
                    <option value="Honduras">
                </datalist>
                <br><br>

                <label for="foto">Foto de perfil: </label>
                <input type="file" id="foto" name="foto" accept="image/*">
                <br><br>

                <button type="submit">Registrarme</button>
            </fieldset>
        </form>

        <p class="registro-link"><strong>¿Ya tienes cuenta? </strong>
            <a href="inicio_sesion.php" class="boton-enlace">Inicia sesion aqui</a>
        </p>

    </main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
