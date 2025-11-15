<?php
// Cargar listas maestras para los selects (tipo anuncio / tipo vivienda / pais)
require_once 'includes/formulario_anuncios.php';
?>
<?php
// Usar la cabecera común
$page_title = 'INMOLINK - Búsqueda';
require 'includes/cabecera.php';
?>

    <main>
        <section>   
        <h1>BÚSQUEDA</h1>
        </section>
        <form action="resultado_busqueda.php" method="post">
            <fieldset>
                <legend>Filtros de búsqueda</legend>
                <br>

                <label for="tipo_anuncio">Tipo de anuncio: </label>
                <?php render_select_from_array($tiposAnuncios, 'tipo_anuncio', 'tipo_anuncio', 'IdTAnuncio', 'NomTAnuncio', null, false); ?>
                <br><br>

                <label for="tipo_vivienda">Tipo de vivienda: </label>
                <?php render_select_from_array($tiposViviendas, 'tipo_vivienda', 'tipo_vivienda', 'IdTVivienda', 'NomTVivienda', null, false); ?>
                <br><br>

                <label for="ciudad">Ciudad</label>
                <input type="text" id="ciudad" name="ciudad" placeholder="Ciudad de residencia">
                <br>
                <br>

                <label for="pais">País:</label>
                <?php
                ?>
                <?php render_select_from_array($paises, 'pais', 'pais', 'IdPais', 'NomPais', null, false); ?>
                <br>
                <br>

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="0€">
                <br><br>

                <label for="fecha">Fecha de publicacion: </label>
                <input type="date" id="fecha_publicacion" name="fecha_publicacion">
                <br>
                <br>

                <button type="submit">Buscar</button>

            </fieldset>
        </form>
    </main>


<?php
// Incluir pie común
require 'includes/pie.php';
?>
