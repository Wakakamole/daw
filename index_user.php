<!-- PÁGINA PRINCIPAL CUANDO EL USUARIO TIENE LA SESION INICIADA -->

<?php

require_once __DIR__ . '/includes/control_parteprivada.php';

//Saludo (calcular y establecer cookie ANTES de enviar cualquier salida)
date_default_timezone_set('Europe/Madrid');
$hora = date('H');
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'usuario';

if ($hora >= 6 && $hora < 12) {
    $saludo = "Buenos días $usuario";
} elseif ($hora >= 12 && $hora < 16) {
    $saludo = "Hola $usuario";
} elseif ($hora >= 16 && $hora < 20) {
    $saludo = "Buenas tardes $usuario";
} else {
    $saludo = "Buenas noches $usuario";
}

// Guardar el saludo en una cookie por 1 hora (debe hacerse antes de cualquier salida)
setcookie('saludo', $saludo, time() + 3600, '/');

require_once __DIR__ . '/includes/cabecera.php';
// Limpiar la marca de login reciente para evitar que persista en próximas peticiones
if (isset($_SESSION['just_logged_in'])) { unset($_SESSION['just_logged_in']); }
?>

<main>

    <?php if (!empty($_SESSION['remember_message'])): ?>
        <section class="info-recuerdo" role="status">
            <?php echo htmlspecialchars($_SESSION['remember_message'], ENT_QUOTES, 'UTF-8'); ?>
        </section>
        <?php unset($_SESSION['remember_message']); ?>
    <?php endif; ?>

<main>
    <section>
    <h1><?php echo htmlspecialchars($saludo, ENT_QUOTES, 'UTF-8'); ?></h1>
    <h2>Últimos anuncios publicados</h2>
    </section>


    <?php
    // Cargar últimos 5 anuncios desde la base de datos
    require_once __DIR__ . '/includes/basedatos.php';
    $db = get_db();
    $sql = "SELECT A.IdAnuncio, A.Titulo, A.FPrincipal, A.FRegistro, A.Ciudad, A.Precio, P.NomPais
            FROM Anuncios A
            LEFT JOIN Paises P ON A.Pais = P.IdPais
            ORDER BY A.FRegistro DESC
            LIMIT 5";
    $ultimos = [];
    try {
        $res = $db->query($sql);
        if ($res) { $ultimos = $res->fetch_all(MYSQLI_ASSOC); $res->free(); }
    } catch (Exception $e) {
        error_log('Error al obtener últimos anuncios (privado): ' . $e->getMessage());
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
            <a href="detalle_anuncio.php?id=<?php echo (int)$an['IdAnuncio']; ?>">
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
