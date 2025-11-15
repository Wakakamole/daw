<?php

require_once __DIR__ . '/includes/control_parteprivada.php';
// Cargar conexión a la base de datos para obtener tipos de mensajes
require_once __DIR__ . '/includes/basedatos.php';
try {
    $db = get_db();
    $tiposMensajes = [];
    $res_tm = $db->query("SELECT IdTMensaje, NomTMensaje FROM tiposmensajes ORDER BY NomTMensaje");
    if ($res_tm) {
        while ($r = $res_tm->fetch_assoc()) { $tiposMensajes[] = $r; }
    }
} catch (Exception $e) {
    $tiposMensajes = [];
}

// Página de contacto convertida a PHP: rellena anuncio_id desde $_GET y usa includes
function h($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$anuncio_id = isset($_GET['id']) ? trim($_GET['id']) : '';
$page_title = 'INMOLINK - Contactar';
require_once __DIR__ . '/includes/cabecera.php';
?>

    <main>

        <section>
        <h1>CONTACTAR AL ANUNCIANTE</h1>

    <form action="mensaje_respuesta.php" method="post" novalidate>
        <!-- campo oculto para recibir el id del anuncio (rellenado por PHP) -->
        <input type="hidden" id="anuncio_id" name="anuncio_id" value="<?php echo h($anuncio_id); ?>">
            <fieldset>
                <legend>Detalles del Mensaje</legend>
                <label for="tipo">Tipo de consulta:</label>
                <select id="tipo" name="tipo">
                    <option value="">Selecciona una opción</option>
                    <?php if (!empty($tiposMensajes)): ?>
                        <?php foreach ($tiposMensajes as $tm): ?>
                            <option value="<?php echo (int)$tm['IdTMensaje']; ?>"><?php echo htmlspecialchars($tm['NomTMensaje'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <?php endif; ?>
                </select><br><br>


                <label for="nombre">Tu nombre:</label>
                <input type="text" id="nombre" name="nombre">
                <br><br>

                <label for="texto">Mensaje:</label>
                <textarea id="texto" name="texto" rows="4"></textarea>
                <br><br>
                <button type="submit" >Enviar</button>
                <button type="reset">Borrar todo</button>
                </section>

            </fieldset>
        </form>
        </section>

    </main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
