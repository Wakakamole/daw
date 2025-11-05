<?php
// Cabecera común: DOCTYPE, head, apertura de body y header/nav/search
if (!isset($page_title)) {
    $page_title = 'INMOLINK';
}
function h_title($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<?php
// inicializar sesión y auto-login sin salida
require_once __DIR__ . '/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Daniel Fernández Estevez, Sandra Moya del Amo">
    <meta name="description" content="Página principal para usuarios registrados en INMOLINK">

    <!-- Estilos principales -->
    <link rel="stylesheet" href="css/inmolink.css">
    <link rel="stylesheet" href="css/mediaQuery.css">
    <link rel="stylesheet" href="css/iconos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Estilos alternativos -->
    <link rel="alternate stylesheet" type="text/css" href="css/noche.css" title="modoNoche">
    <link rel="alternate stylesheet" type="text/css" href="css/alto_contraste.css" title="altoContraste">
    <link rel="alternate stylesheet" type="text/css" href="css/texto_grande.css" title="textoGrande">
    <link rel="alternate stylesheet" type="text/css" href="css/texto_grande_dislexia.css" title="textoGrandeDislexia">
    <link rel="alternate stylesheet" type="text/css" href="css/alto_contraste_grande.css" title="altoContraste+textoGrande">
    
    <!-- Estilo para impresión -->
    <link rel="stylesheet" type="text/css" href="css/imprimir.css" media="print">

    <title><?php echo h_title($page_title); ?></title>
</head>
<body>

    <header>
        <a href="<?php echo (isset($is_logged) && $is_logged) ? 'index_user.php' : 'index.php'; ?>">
            <img src="img/logo.png" alt="Logo INMOLINK" width="100" height="100" class="logo-normal">
            <img src="img/!logo.png" alt="Logo INMOLINK blanco" width="100" height="100" class="logo-contraste">
        </a>
        <nav>
            <ul>
                <?php if (!empty($is_logged) && $is_logged): ?>
                    <li><a href="index_user.php"><i class="fa-solid fa-house-chimney"></i> Inicio</a></li>
                    <li><a href="formulario_busqueda.html"><i class="fa-solid fa-magnifying-glass"></i> Buscar Propiedades</a></li>
                    <li><a href="error_404_user.html"><i class="fa-regular fa-square-plus"></i>  Subir anuncio</a></li>
                    <li>
                        <details>
                            <summary>
                                <img src="img/user.webp" alt="Foto de perfil" width="40" height="40">
                            </summary>
                            <ul>
                                <li><a href="mi_perfil.html">Mi Perfil</a></li>
                                <li><a href="logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </details>
                    </li>
                <?php else: ?>
                    <li><a href="index.php"><i class="fa-solid fa-house-chimney"></i> Inicio</a></li>
                    <li><a href="formulario_busqueda.html"><i class="fa-solid fa-magnifying-glass"></i> Buscar Propiedades</a></li>
                    <li><a href="inicio_sesion.php"><i class="fa-solid fa-user"></i> Inicio de Sesión</a></li>
                    <li><a href="registro_usuario.php"><i class="fa-solid fa-user-plus"></i> Registro de Usuario</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <search>
            <h2>Búsqueda rápida</h2>
            <form action="resultado_busqueda.php" method="GET">
                <input type="text" name="query" placeholder="Escribe aquí...">
                <button type="submit">Intro</button>
            </form>
        </search>
    </header>
    <?php if (!empty($_SESSION['remember_message'])): ?>
        <section class="info-recuerdo" role="status">
            <?php echo htmlspecialchars($_SESSION['remember_message'], ENT_QUOTES, 'UTF-8'); ?>
        </section>
        <?php unset($_SESSION['remember_message']); ?>
    <?php endif; ?>
