<?php
require_once __DIR__ . '/includes/control_parteprivada.php';

require_once __DIR__ . '/includes/cabecera.php';

// Datos ficticios de anuncios (igual que en mis_anuncios)
$anuncios = [
    1 => 'Piso en Calle Francia Bilbao',
    2 => 'Apartamento en Gran Vía Madrid',
    3 => 'Chalet en La Manga'
];

// Comprobar si hay un id pasado por URL
$selected_id = isset($_GET['id']) ? intval($_GET['id']) : null;
?>

<main>
    <section>
        <h1>Añadir foto a un anuncio</h1>
        <p>Rellena los datos para añadir una nueva foto al anuncio.</p>

        <form action="#" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Datos de la foto</legend>

                <!-- Foto -->
                <label for="foto">Selecciona una foto (*):</label>
                <input type="file" id="foto" name="foto" required><br><br>

                <!-- Texto alternativo -->
                <label for="alt">Texto alternativo (*):</label>
                <input type="text" id="alt" name="alt" minlength="10" required placeholder="Describe la imagen..."><br><br>

                <!-- Título -->
                <label for="titulo">Título de la foto:</label>
                <input type="text" id="titulo" name="titulo" maxlength="200"><br><br>

                <!-- Anuncio -->
                <label for="anuncio">Anuncio (*):</label>
                <select id="anuncio" name="anuncio" required <?= $selected_id ? 'disabled' : '' ?>>
                    <?php if (!$selected_id): ?>
                        <option value="">-- Selecciona un anuncio --</option>
                    <?php endif; ?>

                    <?php foreach ($anuncios as $id => $titulo): ?>
                        <option value="<?= $id ?>" <?= $selected_id == $id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($titulo) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Si se accede desde mis_anuncios -->
                <?php if ($selected_id): ?>
                    <input type="hidden" name="anuncio" value="<?= $selected_id ?>">
                <?php endif; ?>

                <br><br>
                <button type="submit">Añadir foto</button>
            </fieldset>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
