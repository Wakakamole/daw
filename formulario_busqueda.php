<?php
// Usar la cabecera común
$page_title = 'INMOLINK - Búsqueda';
require __DIR__ . '/includes/cabecera.php';

// Arrays para los selects
$tiposAnuncios = [
    1 => 'Venta',
    2 => 'Alquiler'
];

$tiposViviendas = [
    1 => 'Vivienda',
    2 => 'Piso',
    3 => 'Oficina',
    4 => 'Local',
    5 => 'Garaje',
    6 => 'Chalet',
    7 => 'Ático'
];

$paises = [
    1 => 'España',
    2 => 'Reino Unido',
    3 => 'Francia',
    4 => 'Alemania',
    5 => 'Estados Unidos'
];

// Valores previos (para mantener selección después de enviar)
$prev_tipo_anuncio  = $_POST['tipo_anuncio'] ?? '';
$prev_tipo_vivienda = $_POST['tipo_vivienda'] ?? '';
$prev_ciudad        = $_POST['ciudad'] ?? '';
$prev_pais          = $_POST['pais'] ?? '';
$prev_precio        = $_POST['precio'] ?? '';
$prev_fecha         = $_POST['fecha_publicacion'] ?? '';

// Función simple para renderizar select
function render_select($array, $name, $selected = '') {
    echo "<select name='$name' id='$name'>";
    echo "<option value=''>-- Seleccione --</option>";
    foreach ($array as $key => $value) {
        $sel = ($key == $selected) ? 'selected' : '';
        echo "<option value='$key' $sel>$value</option>";
    }
    echo "</select>";
}
?>

<main>
    <section>   
        <h1>BÚSQUEDA AVANZADA</h1>
    </section>

    <form action="/daw/buscar" method="post">
        <fieldset>
            <legend>Filtros de búsqueda</legend>
            <br>

            <label for="tipo_anuncio">Tipo de anuncio: </label>
            <?php render_select($tiposAnuncios, 'tipo_anuncio', $prev_tipo_anuncio); ?>
            <br><br>

            <label for="tipo_vivienda">Tipo de vivienda: </label>
            <?php render_select($tiposViviendas, 'tipo_vivienda', $prev_tipo_vivienda); ?>
            <br><br>

            <label for="ciudad">Ciudad: </label>
            <input type="text" id="ciudad" name="ciudad" placeholder="Ciudad de residencia" value="<?= htmlspecialchars($prev_ciudad) ?>">
            <br><br>

            <label for="pais">País: </label>
            <?php render_select($paises, 'pais', $prev_pais); ?>
            <br><br>

            <label for="precio">Precio máximo: </label>
            <input type="number" id="precio" name="precio" placeholder="0€" value="<?= htmlspecialchars($prev_precio) ?>">
            <br><br>

            <label for="fecha_publicacion">Fecha de publicación: </label>
            <input type="date" id="fecha_publicacion" name="fecha_publicacion" value="<?= htmlspecialchars($prev_fecha) ?>">
            <br><br>

            <button type="submit">Buscar</button>
        </fieldset>
    </form>
</main>

<?php
// Incluir pie común
require __DIR__ . '/includes/pie.php';
?>
