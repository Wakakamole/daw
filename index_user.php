<!-- PÁGINA PRINCIPAL CUANDO EL USUARIO TIENE LA SESION INICIADA -->

<?php

require_once __DIR__ . '/includes/control_parteprivada.php';

require_once __DIR__ . '/includes/cabecera.php';

//Saludo
date_default_timezone_set('Europe/Madrid');
$hora = date('H');
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'usuario';  //guardamos usuario guardado en la sesion en usuario

if ($hora >= 6 && $hora < 12) {
    $saludo = "Buenos días $usuario";
} elseif ($hora >= 12 && $hora < 16) {
    $saludo = "Hola $usuario";
} elseif ($hora >= 16 && $hora < 20) {
    $saludo = "Buenas tardes $usuario";
} else {
    $saludo = "Buenas noches $usuario";
}

//Guardar el saludo en una cookie por 1 hora (me he enterado de que si no pongo tiempo la cookie se borra al cerrar el navegador)
setcookie('saludo', $saludo, time() + 3600, '/');
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
    <ul>
        <li>
        <article>
            <h3>Foto 1</h3>
            <a href="detalle_anuncio.php?id=1">
                <img src="img/noimage.png" alt="Foto 1" width="200">
            </a>
            <footer>
                <p>Descripción de la foto 1</p>
                <p><strong>Fecha:</strong> 10/09/2025</p>
                <p><strong>Ciudad:</strong> Bilbao</p>
                <p><strong>País:</strong> España</p>
                <p><strong>Precio:</strong> 320.000€</p>
            </footer>
        </article>
        </li>

                <li>
        <article>
            <h3>Foto 2</h3>
            <a href="detalle_anuncio.php?id=2">
                <img src="img/noimage.png" alt="Foto 2" width="200">
            </a>
            <footer>
                <p>Descripción de la foto 2</p>
                <p><strong>Fecha:</strong> 10/09/2025</p>
                <p><strong>Ciudad:</strong> Bilbao</p>
                <p><strong>País:</strong> España</p>
                <p><strong>Precio:</strong> 320.000€</p>
            </footer>
        </article>
        </li>

                <li>
        <article>
            <h3>Foto 3</h3>
            <a href="detalle_anuncio.php?id=3">
                <img src="img/noimage.png" alt="Foto 3" width="200">
            </a>
            <footer>
                <p>Descripción de la foto 3</p>
                <p><strong>Fecha:</strong> 10/09/2025</p>
                <p><strong>Ciudad:</strong> Bilbao</p>
                <p><strong>País:</strong> España</p>
                <p><strong>Precio:</strong> 320.000€</p>
            </footer>
        </article>
        </li>

                <li>
        <article>
            <h3>Foto 4</h3>
            <a href="detalle_anuncio.php?id=4">
                <img src="img/noimage.png" alt="Foto 4" width="200">
            </a>
            <footer>
                <p>Descripción de la foto 4</p>
                <p><strong>Fecha:</strong> 10/09/2025</p>
                <p><strong>Ciudad:</strong> Bilbao</p>
                <p><strong>País:</strong> España</p>
                <p><strong>Precio:</strong> 320.000€</p>
            </footer>
        </article>
        </li>

                <li>
        <article>
            <h3>Foto 5</h3>
            <a href="detalle_anuncio.php?id=5">
                <img src="img/noimage.png" alt="Foto 5" width="200">
            </a>
            <footer>
                <p>Descripción de la foto 5</p>
                <p><strong>Fecha:</strong> 10/09/2025</p>
                <p><strong>Ciudad:</strong> Bilbao</p>
                <p><strong>País:</strong> España</p>
                <p><strong>Precio:</strong> 320.000€</p>
            </footer>
        </article>
        </li>
    </ul>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
