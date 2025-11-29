<?php
function mostrar_bloque_fotos_comun($anuncio, $fotos, $propietario = false) {
?>
    <h2><?= htmlspecialchars($anuncio['Titulo']) ?></h2>

    <p class="total-fotos"><strong>Total de fotos:</strong> <?= count($fotos) ?></p>

    <!-- Información básica -->
    <section class="info-basica-fotos">
        <p><strong>Tipo de vivienda:</strong> <?= htmlspecialchars($anuncio['tipo_vivienda']) ?></p>
        <p><strong>Tipo de anuncio:</strong> <?= htmlspecialchars($anuncio['tipo_anuncio']) ?></p>
        <p><strong>Ciudad:</strong> <?= htmlspecialchars($anuncio['Ciudad']) ?></p>
        <p><strong>País:</strong> <?= htmlspecialchars($anuncio['pais']) ?></p>
        <p><strong>Precio:</strong> <?= htmlspecialchars($anuncio['Precio']) ?> €</p>
    </section>

    <!-- fotos -->
    <section id="galeria-fotos">
        <?php foreach ($fotos as $foto): ?>
            <div class="foto-contenedor" style="margin-bottom:20px;">
                <img class="foto-grande" src="<?= htmlspecialchars($foto) ?>" alt="foto anuncio">
                <?php if ($propietario): ?>
                    <form action="eliminar_foto.php" method="post" style="margin-top:10px;">
                        <input type="hidden" name="foto" value="<?= htmlspecialchars($foto) ?>">
                        <input type="hidden" name="id_anuncio" value="<?= (int)$anuncio['IdAnuncio'] ?>">
                        <button type="submit" style="padding:5px 10px; background:red; color:white; border:none; border-radius:5px;"
                        onclick="return confirm('¿Seguro que quieres eliminar esta foto?');">Eliminar foto</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
<?php
}
?>
