<?php

// ROUTER CENTRAL - Enrutador principal de la aplicación


// Iniciar sesión PHP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Extraer la ruta del servidor
$path = $_SERVER['PATH_INFO'] ?? '/';

// Limpiar barras al inicio y final
$path = trim($path, '/');

// Dividir la ruta en segmentos
$segments = explode('/', $path);

// Obtener el controlador (primer segmento, por defecto 'inicio')
$controller = $segments[0] ?? 'inicio';

// Mapeo de rutas a archivos (GET - mostrar)
$routes_get = [
    '' => __DIR__ . '/inicio.php',  // Ruta vacía redirige a inicio
    'inicio' => __DIR__ . '/inicio.php',
    'inicio_user' => __DIR__ . '/inicio_user.php',
    'login' => __DIR__ . '/inicio_sesion.php',
    'registro' => __DIR__ . '/registro_usuario.php',
    'logout' => __DIR__ . '/logout.php',
    'detalle_anuncio' => __DIR__ . '/detalle_anuncio.php',
    'buscar' => __DIR__ . '/resultado_busqueda.php',
    'crear_anuncio' => __DIR__ . '/crear_anuncio.php',
    'formulario_busqueda' => __DIR__ . '/formulario_busqueda.php',
    'mi_perfil' => __DIR__ . '/mi_perfil.php',
    'perfil_usuario' => __DIR__ . '/perfil_usuario.php',
    'mis_anuncios' => __DIR__ . '/mis_anuncios.php',
    'mis_datos' => __DIR__ . '/mis_datos.php',
    'mis_mensajes' => __DIR__ . '/mis_mensajes.php',
    'mensaje' => __DIR__ . '/mensaje.php',
    'solicitar_folleto' => __DIR__ . '/solicitar_folleto.php',
    'configuracion' => __DIR__ . '/configuracion.php',
    'respuesta_baja' => __DIR__ . '/respuesta_baja.php',
    'ver_fotos' => __DIR__ . '/ver_fotos.php',
    'ver_fotos_privadas' => __DIR__ . '/ver_fotos_privadas.php',
    'anadir_foto' => __DIR__ . '/anadir_foto.php',
    'ver_anuncio' => __DIR__ . '/ver_anuncio.php',
    'aviso' => __DIR__ . '/aviso.php',
    'accesibilidad' => __DIR__ . '/accesibilidad.php',
];

// Mapeo de controladores para POST
$routes_post = [
    'login' => __DIR__ . '/control_acceso.php',
    'registro' => __DIR__ . '/respuestaregistro_nuevo.php',
    'mis_datos' => __DIR__ . '/respuestamisdatos.php',
    'mensaje' => __DIR__ . '/mensaje_respuesta.php',
];

// Determinar qué ruta usar basado en el método HTTP
$is_post = $_SERVER['REQUEST_METHOD'] === 'POST';
$routes = $is_post ? $routes_post : $routes_get;

// Si es POST y no hay ruta POST definida, intentar usar GET
if ($is_post && !isset($routes[$controller])) {
    $routes = $routes_get;
}

// Cargar la ruta si existe
if (isset($routes[$controller]) && file_exists($routes[$controller])) {
    require $routes[$controller];
} else {
    // Mostrar página de error 404 según el estado de sesión
    http_response_code(404);
    if (!empty($_SESSION['login']) && $_SESSION['login'] === 'ok') {
        require_once __DIR__ . '/error_404_user.html';
    } else {
        require_once __DIR__ . '/error_404.html';
    }
}
?>
