<?php
require_once __DIR__ . '/includes/cabecera.php';

// ---- TARIFAS Y FUNCIÓN DE CÁLCULO ----
$TARIFAS = [
    'COSTO_ENVIO' => 10.00,
    'PAGINAS' => [
        ['max' => 4, 'precio' => 2.00],
        ['max' => 10, 'precio' => 1.80],
        ['max' => INF, 'precio' => 1.60],
    ],
    'COLOR' => 0.50,
    'RESOLUCION_ALTA' => 0.20
];

function calcularCosteFolleto($numPaginas, $numFotos, $esColor, $esAltaResolucion, $tarifas) {
    $costePaginas = 0.0;
    $paginasRestantes = $numPaginas;
    $paginasPrevMax = 0;

    foreach ($tarifas['PAGINAS'] as $tarifa) {
        $paginasEnTramo = min($paginasRestantes, $tarifa['max'] - $paginasPrevMax);
        $costePaginas += $paginasEnTramo * $tarifa['precio'];
        $paginasRestantes -= $paginasEnTramo;
        $paginasPrevMax = $tarifa['max'];
        if ($paginasRestantes <= 0) break;
    }

    $costeTotal = $costePaginas + $tarifas['COSTO_ENVIO'];
    if ($esColor) $costeTotal += $numFotos * $tarifas['COLOR'];
    if ($esAltaResolucion) $costeTotal += $numFotos * $tarifas['RESOLUCION_ALTA'];

    return number_format($costeTotal, 2, ',', '') . ' €';
}
?>

<main>
    <section id="descripcion-folleto">
        <h1>SOLICITUD DE IMPRESIÓN DE FOLLETO PUBLICITARIO</h1>
        <p>
            Mediante esta opción puedes solicitar la impresión y envío de uno de tus anuncios.
            El precio variará en función del número de páginas del folleto, número de fotos, impresión en color y resolución que desees.
        </p>
    </section>

    <!-- Tabla de tarifas -->
    <section id="tabla-tarifas">
        <h3>Tarifas</h3>

        <!-- Botón para mostrar/ocultar tabla PHP -->
        <button id="mostrarTablaPHP" class="boton-tabla">Mostrar Tabla (PHP)</button>

        <!-- Tabla PHP -->
        <div id="tablaPHP" style="display:none;">
            <table class="tabla-costes-generada">
                <thead>
                    <tr>
                        <th rowspan="2">Número de páginas</th>
                        <th rowspan="2">Número de fotos</th>
                        <th colspan="2">Blanco y negro</th>
                        <th colspan="2">Color</th>
                    </tr>
                    <tr>
                        <th>150-300 dpi</th>
                        <th>450-900 dpi</th>
                        <th>150-300 dpi</th>
                        <th>450-900 dpi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 1; $i <= 15; $i++) {
                        $paginas = $i;
                        $fotos = $i * 3;
                        echo "<tr>";
                        echo "<td>{$paginas}</td>";
                        echo "<td>{$fotos}</td>";

                        $combinaciones = [
                            [false, false],
                            [false, true],
                            [true, false],
                            [true, true]
                        ];

                        foreach ($combinaciones as [$color, $alta]) {
                            $coste = calcularCosteFolleto($paginas, $fotos, $color, $alta, $TARIFAS);
                            echo "<td>{$coste}</td>";
                        }

                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <hr>

        <!-- Botón para mostrar/ocultar tabla JS -->
        <button id="mostrarTabla" class="boton-tabla">Mostrar Tabla (JavaScript)</button>
        <div id="tableContainer" style="display:none;"></div>

        <script src="js/tabla_costes.js"></script>
        <script>
        document.addEventListener("DOMContentLoaded", () => {
            const botonPHP = document.getElementById("mostrarTablaPHP");
            const tablaPHP = document.getElementById("tablaPHP");

            botonPHP.addEventListener("click", () => {
                const visible = tablaPHP.style.display === "block";
                tablaPHP.style.display = visible ? "none" : "block";
                botonPHP.textContent = visible
                    ? "Mostrar Tabla (PHP)"
                    : "Ocultar Tabla (PHP)";
            });
        });
        </script>
    </section>


    <!-- Formulario -->
    <section id="formulario">
        <h3>Formulario de solicitud</h3>
        <p>Rellena el siguiente formulario aportando todos los detalles para confeccionar tu folleto publicitario.</p>

        <form action="solicitar_folleto_respuesta.php" method="post">
            <fieldset>
                <legend>Datos Personales</legend>

                <label for="nombre">Nombre completo (*):</label>
                <input type="text" id="nombre" name="nombre" maxlength="200" required><br><br>

                <label for="email">Correo electrónico (*):</label>
                <input type="email" id="email" name="email" maxlength="200" required><br><br>

                <label for="textoAdicional">Texto adicional:</label>
                <textarea id="textoAdicional" name="textoAdicional" maxlength="4000" rows="4" cols="50"></textarea><br><br>

                <fieldset>
                    <legend>Dirección (*)</legend>
                    <label for="calle">Calle:</label>
                    <input type="text" id="calle" name="calle" required>
                    <label for="numero">Número:</label>
                    <input type="text" id="numero" name="numero" required>
                    <label for="cp">CP:</label>
                    <input type="text" id="cp" name="cp" required>
                    <label for="localidad">Localidad:</label>
                    <input type="text" id="localidad" name="localidad" required>
                    <label for="input-provincia">Provincia:</label>
                    <input list="provincias-lista" id="input-provincia" name="provincia" required>
                    <datalist id="provincias-lista">
                        <option value="Ciudad Real">
                        <option value="Albacete">
                        <option value="Toledo">
                        <option value="Cuenca">
                        <option value="Guadalajara">
                        <option value="Alicante">
                        <option value="Valencia">
                        <option value="Castellon">
                        <option value="A Coruña">
                        <option value="Lugo">
                        <option value="Ourense">
                        <option value="Pontevedra">
                    </datalist>
                    <label for="pais">País:</label>
                    <input type="text" id="pais" name="pais" required>
                </fieldset><br><br>

                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono"><br><br>

                <label for="colorPortada">Color de la portada:</label>
                <input type="color" id="colorPortada" name="colorPortada" value="#000000"><br><br>

                <label for="copias">Número de copias:</label>
                <input type="number" id="copias" name="copias" min="1" max="99" value="1"><br><br>

                <label for="resolucion">Resolución de impresión:</label>
                <select id="resolucion" name="resolucion">
                    <option value="150">150 dpi</option>
                    <option value="300">300 dpi</option>
                    <option value="600">600 dpi</option>
                </select><br><br>

                <label for="anuncio">Anuncio (*):</label>
                <select id="anuncio" name="anuncio" required>
                    <option value="anuncio1">Anuncio 1</option>
                    <option value="anuncio2">Anuncio 2</option>
                </select><br><br>

                <label for="fechaRecepcion">Fecha de recepción:</label>
                <input type="date" id="fechaRecepcion" name="fechaRecepcion"><br><br>

                <p>Color de la impresión</p>
                <input type="radio" id="aColor" name="colorImpresion" value="color">
                <label for="aColor">Color</label>
                <input type="radio" id="colorNo" name="colorImpresion" value="byn">
                <label for="colorNo">Blanco y negro</label><br><br>

                <p>¿Imprimir el precio en el folleto?</p>
                <input type="radio" id="precioSi" name="impresionPrecio" value="si">
                <label for="precioSi">Sí</label>
                <input type="radio" id="precioNo" name="impresionPrecio" value="no">
                <label for="precioNo">No</label><br><br>

                <button type="submit">Solicitar</button>
            </fieldset>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
