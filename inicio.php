<?php
// Página principal pública
$page_title = 'INMOLINK';
// incluir la cabecera (auto-login ya se ejecuta ahí antes de salida)
require_once __DIR__ . '/includes/cabecera.php';

// Si tras el auto-login hay sesión activa, redirigir al área privada
if (!empty($_SESSION['login']) && $_SESSION['login'] === 'ok') {
    header('Location: /daw/inicio_user');
    exit;
}

?>

<main>
    <section>
    <h1>PÁGINA PRINCIPAL </h1>
    
    <!-- SECCIÓN: ANUNCIO ESCOGIDO -->
    <h2>Anuncio del día</h2>
    <?php
    require_once __DIR__ . '/includes/basedatos.php';
    $db = get_db();
    
    // Leer fichero de anuncios escogidos
    $fichero_escogidos = __DIR__ . '/data/anuncios_escogidos.txt';
    $anuncios_escogidos = [];
    
    if (file_exists($fichero_escogidos)) {
        $contenido = file_get_contents($fichero_escogidos);
        
        // Parsear el formato personalizado
        preg_match_all('/---INICIO ANUNCIO---(.*?)---FIN ANUNCIO---/s', $contenido, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $bloque) {
                $anuncio = [];
                
                // Extraer IdAnuncio
                if (preg_match('/IdAnuncio:\s*(\d+)/', $bloque, $m)) {
                    $anuncio['IdAnuncio'] = (int)$m[1];
                }
                // Extraer Experto
                if (preg_match('/Experto:\s*(.+?)(?:\n|$)/', $bloque, $m)) {
                    $anuncio['Experto'] = trim($m[1]);
                }
                // Extraer Comentario
                if (preg_match('/Comentario:\s*(.+?)(?:\n|$)/s', $bloque, $m)) {
                    $anuncio['Comentario'] = trim($m[1]);
                }
                
                if (isset($anuncio['IdAnuncio'])) {
                    $anuncios_escogidos[] = $anuncio;
                }
            }
        }
    }
    
    // Seleccionar uno aleatoriamente
    $anuncio_mostrado = null;
    if (!empty($anuncios_escogidos)) {
        $idx = array_rand($anuncios_escogidos);
        $anuncio_escogido = $anuncios_escogidos[$idx];
        
        // Verificar que existe en la BD
        $sql_check = "SELECT A.IdAnuncio, A.Titulo, A.FPrincipal, A.Ciudad, A.Precio, P.NomPais
                      FROM anuncios A
                      LEFT JOIN paises P ON A.Pais = P.IdPais
                      WHERE A.IdAnuncio = ?";
        $stmt = $db->prepare($sql_check);
        if ($stmt) {
            $stmt->bind_param('i', $anuncio_escogido['IdAnuncio']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $anuncio_mostrado = $result->fetch_assoc();
                $anuncio_mostrado['Experto'] = $anuncio_escogido['Experto'];
                $anuncio_mostrado['Comentario'] = $anuncio_escogido['Comentario'];
            }
            $stmt->close();
        }
    }
    ?>
    
    <?php if ($anuncio_mostrado): ?>
    <article style="border: 2px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
        <h3><?php echo htmlspecialchars($anuncio_mostrado['Titulo'], ENT_QUOTES, 'UTF-8'); ?></h3>
        <?php if (!empty($anuncio_mostrado['FPrincipal'])): ?>
            <img src="<?php echo htmlspecialchars($anuncio_mostrado['FPrincipal'], ENT_QUOTES, 'UTF-8'); ?>" 
                 alt="<?php echo htmlspecialchars($anuncio_mostrado['Titulo'], ENT_QUOTES, 'UTF-8'); ?>" 
                 width="300" style="margin-bottom: 10px;">
        <?php endif; ?>
        <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($anuncio_mostrado['Ciudad'], ENT_QUOTES, 'UTF-8'); ?>, 
           <?php echo htmlspecialchars($anuncio_mostrado['NomPais'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Precio:</strong> <?php echo htmlspecialchars($anuncio_mostrado['Precio'], ENT_QUOTES, 'UTF-8'); ?> €</p>
        <p><strong><?php echo htmlspecialchars($anuncio_mostrado['Experto'], ENT_QUOTES, 'UTF-8'); ?> opina:</strong></p>
        <p><em>"<?php echo htmlspecialchars($anuncio_mostrado['Comentario'], ENT_QUOTES, 'UTF-8'); ?>"</em></p>
        <a href="/daw/detalle_anuncio?id=<?php echo (int)$anuncio_mostrado['IdAnuncio']; ?>">Ver detalles</a>
    </article>
    <?php else: ?>
    <p>No hay anuncio del día disponible en este momento.</p>
    <?php endif; ?>
    
    <!-- SECCIÓN: CONSEJO DE COMPRA/VENTA -->
    <h2>Consejo de compra/venta</h2>
    <?php
    $fichero_consejos = __DIR__ . '/data/consejos.json';
    $consejo_mostrado = null;
    
    if (file_exists($fichero_consejos)) {
        $json_content = file_get_contents($fichero_consejos);
        $consejos = json_decode($json_content, true);
        
        if (!empty($consejos) && is_array($consejos)) {
            $idx = array_rand($consejos);
            $consejo_mostrado = $consejos[$idx];
        }
    }
    ?>
    
    <?php if ($consejo_mostrado): ?>
    <article style="border: 2px solid #009688; padding: 15px; margin-bottom: 20px; background-color: #e0f2f1;">
        <p><strong>Categoría:</strong> <?php echo htmlspecialchars($consejo_mostrado['categoria'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Importancia:</strong> 
            <span style="color: 
                <?php 
                    if ($consejo_mostrado['importancia'] === 'Baja') echo '#FFC107';
                    elseif ($consejo_mostrado['importancia'] === 'Media') echo '#FF9800';
                    elseif ($consejo_mostrado['importancia'] === 'Alta') echo '#F44336';
                ?>;">
                <?php echo htmlspecialchars($consejo_mostrado['importancia'], ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </p>
        <p><?php echo htmlspecialchars($consejo_mostrado['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
    </article>
    <?php else: ?>
    <p>No hay consejo disponible en este momento.</p>
    <?php endif; ?>
    
    <h2>Últimos anuncios publicados</h2>
    </section>
    <?php
    // Obtener últimos 5 anuncios desde la base de datos
    $sql = "SELECT A.IdAnuncio, A.Titulo, A.FPrincipal, A.FRegistro, A.Ciudad, A.Precio, P.NomPais
            FROM anuncios A
            LEFT JOIN paises P ON A.Pais = P.IdPais
            ORDER BY A.FRegistro DESC
            LIMIT 5";
    $ultimos = [];
    try {
        $res = $db->query($sql);
        if ($res) {
            $ultimos = $res->fetch_all(MYSQLI_ASSOC);
            $res->free();
        }
    } catch (Exception $e) {
        // Si falla la consulta, dejamos el array vacío y mostramos mensaje más abajo
        error_log('Error al obtener últimos anuncios: ' . $e->getMessage());
    }
    ?>

    <?php if (empty($ultimos)): ?>
        <p>No hay anuncios para mostrar.</p>
    <?php else: ?>
    <ul>
    <?php foreach ($ultimos as $an):
        $img = !empty($an['FPrincipal']) ? htmlspecialchars($an['FPrincipal'], ENT_QUOTES, 'UTF-8') : 'img/noimage.png';
        $fecha = !empty($an['FRegistro']) ? date('d/m/Y', strtotime($an['FRegistro'])) : '';
    ?>
        <li>
        <article>
            <h3><?php echo htmlspecialchars($an['Titulo'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <a href="/daw/detalle_anuncio?id=<?php echo (int)$an['IdAnuncio']; ?>">
                <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($an['Titulo'], ENT_QUOTES, 'UTF-8'); ?>" width="200">
            </a>
            <footer>
                <p><?php echo htmlspecialchars($an['Ciudad'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><?php echo htmlspecialchars($an['NomPais'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Fecha:</strong> <?php echo $fecha; ?></p>
                <p><strong>Precio:</strong> <?php echo htmlspecialchars($an['Precio'], ENT_QUOTES, 'UTF-8'); ?></p>
            </footer>
        </article>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
