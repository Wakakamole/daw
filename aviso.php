<?php
// aviso.php - Página para avisar que se requiere autenticación
$page_title = 'INMOLINK - Acceso Denegado';
require_once __DIR__ . '/includes/cabecera.php';
?>

<main>
    <section class="aviso-tarjeta">
        <h1>AVISO</h1>
        <p><strong>Para acceder a esta información, es necesario iniciar sesión.</strong></p>
        <p>Por favor, inicie sesión para continuar.</p>
        <a href="/daw/login" class="boton-enlace">Iniciar sesión</a>
        <a href="/daw/" class="boton-enlace">Página principal</a>
    </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
