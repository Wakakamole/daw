<?php

require_once __DIR__ . '/includes/control_parteprivada.php';

require_once __DIR__ . '/includes/cabecera.php';
// Cargar datos maestros para selects (include reutilizable)
require_once __DIR__ . '/includes/formulario_anuncios.php';
?>

<main>
    <section>
        <h1>Crear nuevo anuncio</h1>

        <form action="#" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Datos del anuncio</legend>
                <!-- Tipo de anuncio -->
                <label for="tipo_anuncio">Tipo de anuncio (*):</label>
                <?php render_select_from_array($tiposAnuncios, 'tipo_anuncio', 'tipo_anuncio', 'IdTAnuncio', 'NomTAnuncio', null, true); ?><br><br>

                <!-- Tipo de vivienda -->
                <label for="tipo_vivienda">Tipo de vivienda (*):</label>
                <?php render_select_from_array($tiposViviendas, 'tipo_vivienda', 'tipo_vivienda', 'IdTVivienda', 'NomTVivienda', null, true); ?><br><br>

                <!-- Título -->
                <label for="titulo">Título del anuncio (*):</label>
                <input type="text" id="titulo" name="titulo" maxlength="60" required><br><br>

                <!-- Ciudad y país -->
                <label for="ciudad">Ciudad (*):</label>
                <input type="text" id="ciudad" name="ciudad" maxlength="100" required>
                <label for="pais">País (*):</label>
                <?php render_select_from_array($paises, 'pais', 'pais', 'IdPais', 'NomPais', null, true); ?><br><br>

                <!-- Preico -->
                <label for="precio">Precio (*):</label>
                <input type="text" id="precio" name="precio" maxlength="9" required><br><br>

                <!--Descripción -->
                <label for="descripcion">Descripción (*):</label><br>
                <textarea id="descripcion" name="descripcion" rows="6" cols="50" maxlength="4000" required></textarea><br><br>

                <!--Características -->
                <fieldset>
                    <legend>Características</legend>
                    <label for="superficie">Superficie (m²):</label>
                    <input type="number" id="superficie" name="superficie" min="0"><br><br>

                    <label for="habitaciones">Habitaciones:</label>
                    <input type="number" id="habitaciones" name="habitaciones" min="0"><br><br>

                    <label for="banos">Baños:</label>
                    <input type="number" id="banos" name="banos" min="0"><br><br>

                    <label for="plantas">Plantas:</label>
                    <input type="number" id="plantas" name="plantas" min="0"><br><br>

                    <label for="ano">Año de construcción:</label>
                    <input type="number" id="ano" name="ano" min="1800" max="2100"><br><br>
                </fieldset><br>

                <!--Foto-->
                <label for="foto_principal">Foto principal (*):</label>
                <input type="file" id="foto_principal" name="foto_principal" required><br><br>

                <button type="submit">Crear anuncio</button>
            </fieldset>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
