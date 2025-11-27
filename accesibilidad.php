<?php
// accesibilidad.php - Página de información de accesibilidad
$page_title = 'INMOLINK - Accesibilidad';
require_once __DIR__ . '/includes/cabecera.php';
?>

<main>
    <article>
        <header>
            <h1>Accesibilidad</h1>
        </header>

        <section>
            <h2>Estándares de Accesibilidad Web</h2>
            <p>Nuestro sitio web INMOLINK ha sido diseñado siguiendo los estándares de accesibilidad web WCAG 2.1 (Web Content Accessibility Guidelines) para garantizar que todas las personas, independientemente de sus capacidades o limitaciones, puedan acceder y utilizar nuestro contenido.</p>
        </section>

        <section>
            <h2>Características de Accesibilidad</h2>
            <ul>
                <li><strong>Contraste de Colores:</strong> Ofrecemos un modo de alto contraste para mejorar la legibilidad.</li>
                <li><strong>Tamaño de Texto:</strong> Permite aumentar el tamaño del texto para usuarios con baja visión.</li>
                <li><strong>Modo Nocturno:</strong> Reduce la fatiga ocular en ambientes oscuros.</li>
                <li><strong>Navegación por Teclado:</strong> Toda la funcionalidad es accesible mediante teclado.</li>
                <li><strong>Lectores de Pantalla:</strong> El sitio es compatible con tecnologías de asistencia.</li>
                <li><strong>Estructura Semántica:</strong> Utilizamos HTML semántico para mejorar la comprensión del contenido.</li>
            </ul>
        </section>

        <section>
            <h2>Cómo Usar las Opciones de Accesibilidad</h2>
            <p>Puedes acceder a las opciones de accesibilidad desde el menú principal del sitio. Encontrarás opciones para:</p>
            <ul>
                <li>Cambiar el contraste</li>
                <li>Aumentar el tamaño del texto</li>
                <li>Activar el modo nocturno</li>
                <li>Habilitar la fuente dislexia-friendly</li>
            </ul>
        </section>

        <section>
            <h2>Problemas de Accesibilidad</h2>
            <p>Si encuentras problemas de accesibilidad en nuestro sitio, por favor <a href="/daw/">contacta con nosotros</a> y nos esforzaremos en resolverlos lo antes posible.</p>
        </section>
    </article>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
