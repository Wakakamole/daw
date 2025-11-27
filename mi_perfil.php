<?php
require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/cabecera.php';

?>

<main>
    <section>
    <h1>MI PERFIL</h1>
    </section>
    <section>
        <h2>Menú de usuario</h2>
        <nav aria-label="Opciones del usuario">
            <ul class="perfil-menu-lista">
                <li><a href="/daw/perfil_usuario" class="boton-enlace perfil-menu"><i class="fa-solid fa-user"></i> Mi perfil</a></li>
                <li><a href="/daw/mis_datos" class="boton-enlace perfil-menu"><i class="fa-solid fa-user-pen"></i> Modificar mis datos</a></li>
                <li><a href="/daw/mis_anuncios" class="boton-enlace perfil-menu"><i class="fa-solid fa-list"></i> Mis anuncios</a></li>
                <li><a href="/daw/crear_anuncio" class="boton-enlace perfil-menu"><i class="fa-solid fa-circle-plus"></i> Nuevo anuncio</a></li>
                <li><a href="/daw/mis_mensajes" class="boton-enlace perfil-menu"><i class="fa-solid fa-envelope"></i> Mis mensajes</a></li>
                <li><a href="/daw/solicitar_folleto" class="boton-enlace perfil-menu"><i class="fa-solid fa-file-lines"></i> Solicitar folleto </a></li>
                <li><a href="/daw/configuracion" class="boton-enlace perfil-menu"><i class="fa-solid fa-cog"></i> Configuración</a></li>
                <li><a href="/daw/respuesta_baja" class="boton-enlace perfil-menu"><i class="fa-solid fa-user-xmark"></i> Darme de baja</a></li>
                <li><a href="/daw/" class="boton-enlace perfil-menu"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a></li>
            </ul>
        </nav>
    </section>

<?php require_once __DIR__ . '/includes/pie.php'; ?>

