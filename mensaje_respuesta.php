

<!-- PÁGINA DE RESPUESTA UNA VEZ SE ENVÍA EL MENSAJE -->
 <!-- 
    Inserta los datos recibidos y valida mínimamente
    
 -->

<?php
function h($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
$texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$anuncio_id = isset($_POST['anuncio_id']) ? intval($_POST['anuncio_id']) : 1;

$allowed = ['consulta','interes','otro','informacion','cita','oferta'];
$errors = [];
if ($tipo === '' || !in_array($tipo, $allowed, true)) {
    $errors[] = 'Tipo de mensaje no válido';
}
if ($texto === '') {
    $errors[] = 'El texto del mensaje no puede estar vacío';
}

?>
<?php

$page_title = 'INMOLINK - Confirmación Mensaje';
require_once __DIR__ . '/includes/cabecera.php';
?>

    <main>
        <article class="mensaje-contenedor">
            <section>
            <h1>CONFIRMACIÓN: Mensaje enviado</h1>
            <?php if (!empty($errors)): ?>
                <p><strong>Se han encontrado errores en el formulario:</strong></p>
                <ul>
                    <?php foreach($errors as $e): ?>
                        <li><?php echo h($e); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><strong>Su mensaje ha sido recibido y almacenado correctamente. A continuación se muestran los datos enviados:</strong></p>
            <?php endif; ?>
            <br>
            </section>

            <section aria-labelledby="datos-envio">
                <h3 id="datos-envio">Datos del mensaje</h3>
                <dl>
                <dt><strong> Tipo de consulta: </strong></dt>
                    <dd><output id="out-tipo-mensaje"><?php echo !empty($errors) ? '—' : h($tipo); ?></output></dd>

                    <dt><strong> Remitente:</strong></dt>
                    <dd><output id="out-destinatario"><?php echo !empty($errors) ? '—' : h($nombre); ?></output></dd>

                    <dt><strong> Mensaje: </strong></dt>
                    <dd><output id="out-mensaje"><?php echo !empty($errors) ? '—' : h($texto); ?></output></dd>
                </dl>
            </section>
            <section>
                <p>
                    <a href="detalle_anuncio.php?id=<?php echo $anuncio_id; ?>"  class="boton-enlace" >Ok</a>
                </p>
            </section>
        </article>
    </main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>

