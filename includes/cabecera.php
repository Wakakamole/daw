<?php
// Cabecera común: DOCTYPE, head, apertura de body y header/nav/search
if (!isset($page_title)) {
    $page_title = 'INMOLINK';
}
function h_title($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

require_once __DIR__ . '/session.php';

// Mapeo IdEstilo -> nombre archivo CSS
$mapa_estilos = [
    1 => 'inmolink',
    2 => 'alto_contraste_grande',
    3 => 'alto_contraste',
    4 => 'noche',
    5 => 'texto_grande',
    6 => 'texto_grande_dislexia'
];

// Obtener el estilo desde sesión o cookie
$estilo_id = $_SESSION['estilo'] ?? $_COOKIE['estilo_usuario'] ?? 1;
$estilo_css = $mapa_estilos[$estilo_id] ?? 'inmolink';
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

    <!-- Estilos alternativos (solo referencia) -->
    <link rel="alternate stylesheet" type="text/css" href="css/noche.css" title="noche">
    <link rel="alternate stylesheet" type="text/css" href="css/alto_contraste.css" title="alto_contraste">
    <link rel="alternate stylesheet" type="text/css" href="css/texto_grande.css" title="texto_grande">
    <link rel="alternate stylesheet" type="text/css" href="css/texto_grande_dislexia.css" title="texto_grande_dislexia">
    <link rel="alternate stylesheet" type="text/css" href="css/alto_contraste_grande.css" title="alto_contraste_grande">

    <!-- Estilo para impresión -->
    <link rel="stylesheet" type="text/css" href="css/imprimir.css" media="print">

    <!-- Estilo seleccionado por el usuario -->
    <link rel="stylesheet" href="css/<?php echo htmlspecialchars($estilo_css, ENT_QUOTES, 'UTF-8'); ?>.css">

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
                    <li><a href="formulario_busqueda.php"><i class="fa-solid fa-magnifying-glass"></i> Buscar Propiedades</a></li>
                    <li><a href="crear_anuncio.php"><i class="fa-regular fa-square-plus"></i>  Subir anuncio</a></li>
                    <li>
                        <details>
                            <summary>
                                <?php
                                // Usar la foto guardada en sesión.
                                $avatar_src = 'img/user.webp';
                                $avatar_alt = 'Foto de perfil';
                                if (!empty($_SESSION['foto'])) {
                                    $avatar_src = $_SESSION['foto'];
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($avatar_src, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($avatar_alt, ENT_QUOTES, 'UTF-8'); ?>" class="avatar" width="40" height="40">
                            </summary>
                            <ul>
                                <li>
                                            <a href="mi_perfil.php">
                                        <?php
                                            if (isset($_SESSION['usuario'])) {
                                                echo 'Perfil de ' . htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
                                            } else {
                                                echo 'Mi Perfil';
                                            }
                                        ?>
                                    </a>
                                </li>
                                <li><a href="logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </details>
                    </li>
                <?php else: ?>
                    <li><a href="index.php"><i class="fa-solid fa-house-chimney"></i> Inicio</a></li>
                    <li><a href="formulario_busqueda.php"><i class="fa-solid fa-magnifying-glass"></i> Buscar Propiedades</a></li>
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





<?php
//PANEL DE ÚLTIMOS ANUNCIOS VISITADOS
$cookie_name = 'ultimos_anuncios';
$ultimos_anuncios_panel = [];

//leemos la cookie y la convierto en array
if (isset($_COOKIE[$cookie_name])) {
    $ultimos_ids = json_decode($_COOKIE[$cookie_name], true);
    if (is_array($ultimos_ids)) {
        // Cargar todos los anuncios
        $anuncios_data = file_exists(__DIR__ . '/../data/anuncios.php') ? require __DIR__ . '/../data/anuncios.php' : [];

        foreach ($ultimos_ids as $aid) {
            if (isset($anuncios_data[$aid])) {
                $ultimos_anuncios_panel[] = $anuncios_data[$aid];
            }
        }
    }
}
?>
<?php if (!empty($ultimos_anuncios_panel)): ?>
    <aside id="ultimos-anuncios">
        <h2>Últimos anuncios visitados</h2>
        <ul class="lista-anuncios">
            <?php foreach ($ultimos_anuncios_panel as $a): ?>
                <li>
                    <article class="anuncio-item mini-anuncio">
                        <h3><?= htmlspecialchars($a['titulo']) ?></h3>
                        <a href="detalle_anuncio.php?id=<?= $a['id'] ?>">
                            <img src="<?= htmlspecialchars($a['foto']) ?>" alt="<?= htmlspecialchars($a['titulo']) ?>">
                        </a>
                        <footer>
                            <p><strong>Ciudad:</strong> <?= htmlspecialchars($a['ciudad']) ?></p>
                            <p><strong>País:</strong> <?= htmlspecialchars($a['pais']) ?></p>
                            <p><strong>Precio:</strong> <?= htmlspecialchars($a['precio']) ?></p>
                        </footer>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>
<?php endif; ?>
