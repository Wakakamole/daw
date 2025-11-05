<?php
// Página principal pública
$page_title = 'INMOLINK';
// incluir la cabecera (auto-login ya se ejecuta ahí antes de salida)
require_once __DIR__ . '/includes/cabecera.php';

// Si tras el auto-login hay sesión activa, redirigir al área privada
if (!empty($_SESSION['login']) && $_SESSION['login'] === 'ok') {
    header('Location: index_user.php');
    exit;
}

?>

<main>
    <section>
    <h1>PÁGINA PRINCIPAL </h1>
    <h2>Últimos anuncios publicados</h2>
    </section>
    <ul>
        <li>
        <article>
            <h3>Foto 1</h3>
            <a href="aviso.html">
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
            <a href="aviso.html">
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
            <a href="aviso.html">
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
            <a href="aviso.html">
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
            <a href="aviso.html">
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

<footer>
    <p>&copy; 2025 INMOLINK Proyecto DAW Ingeniería Multimedia | 
    <a href="accesibilidad.html">Accesibilidad</a></p>
</footer>

</body>
</html>