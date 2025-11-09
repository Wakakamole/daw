<?php

require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/cabecera.php';

?>

<main class="mis-mensajes-main">

    <section>
    <h1>MIS MENSAJES</h1>
    <p>Consulta los mensajes enviados y recibidos en la plataforma.</p>
    </section>

    <!-- Sección de mensajes enviados -->
    <section class="columna-1">
        <h2>Mensajes Enviados</h2>

        <article class="mensaje-enviado">
            <header>
                <p><strong>Tipo:</strong> Más información</p>
                <p><strong>Enviado a:</strong> anunciante@inmolink.com</p>
            </header>

            <p>Buenos días, me gustaría conocer más detalles sobre la ubicación exacta de la casa.</p>

            <footer>
                <p><time datetime="2025-09-19">19 sept 2025</time></p>
            </footer>
        </article>
    </section>

    <!-- Sección de mensajes recibidos -->
    <section class="columna-2">
        <h2>Mensajes Recibidos</h2>

        <article class="mensaje-recibido">
            <header>
                <p><strong>Tipo:</strong> Oferta</p>
                <p><strong>Recibido de:</strong> comprador@correo.com</p>
            </header>

            <p>Buenos días, estoy dispuesto a ofrecer 150.000€ por la casa si aún está disponible.</p>

            <footer>
                <p><time datetime="2025-09-19">19 sept 2025</time></p>
            </footer>
        </article>
    </section>

</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
