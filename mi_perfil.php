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
                <li><a href="perfil_usuario.php" class="boton-enlace perfil-menu"><i class="fa-solid fa-user"></i> Mi perfil</a></li>
                <li><a href="mis_datos.php" class="boton-enlace perfil-menu"><i class="fa-solid fa-user-pen"></i> Modificar mis datos</a></li>
                <li><a href="mis_anuncios.php" class="boton-enlace perfil-menu"><i class="fa-solid fa-list"></i> Mis anuncios</a></li>
                <li><a href="crear_anuncio.php" class="boton-enlace perfil-menu"><i class="fa-solid fa-circle-plus"></i> Nuevo anuncio</a></li>
                <li><a href="mis_mensajes.php" class="boton-enlace perfil-menu"><i class="fa-solid fa-envelope"></i> Mis mensajes</a></li>
                <li><a href="solicitar_folleto.php" class="boton-enlace perfil-menu"><i class="fa-solid fa-file-lines"></i> Solicitar folleto </a></li>
                <li><a href="configuracion.php" class="boton-enlace perfil-menu"><i class="fa-solid fa-cog"></i> Configuración</a></li>
                <li><a href="error_404_user.html" class="boton-enlace perfil-menu"><i class="fa-solid fa-user-xmark"></i> Darme de baja</a></li>
                <li><a href="index.php" class="boton-enlace perfil-menu"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a></li>
            </ul>
        </nav>
    </section>

<?php require_once __DIR__ . '/includes/pie.php'; ?>

