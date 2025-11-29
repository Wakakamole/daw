<?php
require_once __DIR__ . '/includes/control_parteprivada.php';
require_once __DIR__ . '/includes/basedatos.php';
require_once __DIR__ . '/includes/cabecera.php';
require_once __DIR__ . '/includes/formulario_anuncios.php';

$conexion = get_db();
$errores = [];
$exito = false;

$id_anuncio = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_anuncio <= 0) die("Anuncio no válido.");

$stmt = $conexion->prepare("SELECT * FROM anuncios WHERE IdAnuncio=? AND Usuario=?");
$stmt->bind_param("ii",$id_anuncio,$_SESSION['usuario_id']);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) die("No tienes permiso para modificar este anuncio.");
$anuncio = $res->fetch_assoc();
$stmt->close();

$tiposAnuncios=[]; $tiposViviendas=[]; $paises=[];
foreach($conexion->query("SELECT IdTAnuncio, NomTAnuncio FROM tiposanuncios ORDER BY NomTAnuncio") as $fila) $tiposAnuncios[]=$fila;
foreach($conexion->query("SELECT IdTVivienda, NomTVivienda FROM tiposviviendas ORDER BY NomTVivienda") as $fila) $tiposViviendas[]=$fila;
foreach($conexion->query("SELECT IdPais, NomPais FROM paises ORDER BY NomPais") as $fila) $paises[]=$fila;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validar_foto = false; // no obligatorio al modificar
    require __DIR__ . '/includes/filtrado_anuncio.php';

    // manejar subida de foto
    $foto_ruta = $anuncio['FPrincipal'];
    if(isset($_FILES['foto_principal']) && $_FILES['foto_principal']['error']===UPLOAD_ERR_OK){
        $archivo=$_FILES['foto_principal'];
        $nombre_archivo='img/'.time().'_'.basename($archivo['name']);
        if(move_uploaded_file($archivo['tmp_name'],__DIR__.'/'.$nombre_archivo)) $foto_ruta=$nombre_archivo;
        else $errores[]="Error al subir la nueva foto principal.";
    }

    if(empty($errores)){
        $stmt = $conexion->prepare("
            UPDATE anuncios SET 
            TAnuncio=?, TVivienda=?, FPrincipal=?, Alternativo=?,
            Titulo=?, Precio=?, Texto=?, Ciudad=?, Pais=?,
            Superficie=?, NHabitaciones=?, NBanyos=?, Planta=?, Anyo=? 
            WHERE IdAnuncio=?"
        );

        $alternativo=$datos['descripcion'];
        $stmt->bind_param(
            "iisssdssidiiiii",
            $datos['tipo_anuncio'],$datos['tipo_vivienda'],$foto_ruta,$alternativo,
            $datos['titulo'],$datos['precio'],$datos['descripcion'],$datos['ciudad'],$datos['pais'],
            $datos['superficie'],$datos['habitaciones'],$datos['banos'],$datos['plantas'],$datos['ano'],
            $id_anuncio
        );

        if($stmt->execute()){
            $exito=true;
            header("Location: mis_anuncios.php");
            exit;
        } else {
            $errores[]="Error al actualizar el anuncio: ".$stmt->error;
        }
        $stmt->close();
    }
}
?>

<main>
<h1>Modificar anuncio</h1>

<?php if(!empty($errores)): ?>
    <div class="errores">
        <ul><?php foreach($errores as $error): ?><li><?=htmlspecialchars($error)?></li><?php endforeach;?></ul>
    </div>
<?php endif; ?>

<form action="#" method="post" enctype="multipart/form-data">
<fieldset>
<legend>Datos del anuncio</legend>

<label for="tipo_anuncio">Tipo de anuncio (*):</label>
<?php render_select_from_array($tiposAnuncios,'tipo_anuncio','tipo_anuncio','IdTAnuncio','NomTAnuncio',$datos['tipo_anuncio'] ?? $anuncio['TAnuncio']); ?><br><br>

<label for="tipo_vivienda">Tipo de vivienda (*):</label>
<?php render_select_from_array($tiposViviendas,'tipo_vivienda','tipo_vivienda','IdTVivienda','NomTVivienda',$datos['tipo_vivienda'] ?? $anuncio['TVivienda']); ?><br><br>

<label for="titulo">Título (*):</label>
<input type="text" id="titulo" name="titulo" maxlength="60" required value="<?=htmlspecialchars($datos['titulo'] ?? $anuncio['Titulo'])?>"><br><br>

<label for="ciudad">Ciudad (*):</label>
<input type="text" id="ciudad" name="ciudad" maxlength="100" required value="<?=htmlspecialchars($datos['ciudad'] ?? $anuncio['Ciudad'])?>">
<label for="pais">País (*):</label>
<?php render_select_from_array($paises,'pais','pais','IdPais','NomPais',$datos['pais'] ?? $anuncio['Pais'],true); ?><br><br>

<label for="precio">Precio (*):</label>
<input type="text" id="precio" name="precio" maxlength="9" required value="<?=htmlspecialchars($datos['precio'] ?? $anuncio['Precio'])?>"><br><br>

<label for="descripcion">Descripción (*):</label><br>
<textarea id="descripcion" name="descripcion" rows="6" cols="50" maxlength="4000" required><?=htmlspecialchars($datos['descripcion'] ?? $anuncio['Texto'])?></textarea><br><br>

<fieldset>
<legend>Características</legend>
<label for="superficie">Superficie:</label>
<input type="number" id="superficie" name="superficie" min="0" value="<?=htmlspecialchars($datos['superficie'] ?? $anuncio['Superficie'])?>"><br><br>
<label for="habitaciones">Habitaciones:</label>
<input type="number" id="habitaciones" name="habitaciones" min="0" value="<?=htmlspecialchars($datos['habitaciones'] ?? $anuncio['NHabitaciones'])?>"><br><br>
<label for="banos">Baños:</label>
<input type="number" id="banos" name="banos" min="0" value="<?=htmlspecialchars($datos['banos'] ?? $anuncio['NBanyos'])?>"><br><br>
<label for="plantas">Plantas:</label>
<input type="number" id="plantas" name="plantas" min="0" value="<?=htmlspecialchars($datos['plantas'] ?? $anuncio['Planta'])?>"><br><br>
<label for="ano">Año construcción:</label>
<input type="number" id="ano" name="ano" min="1800" max="2100" value="<?=htmlspecialchars($datos['ano'] ?? $anuncio['Anyo'])?>"><br><br>
</fieldset><br>

<label for="foto_principal">Foto principal (opcional):</label>
<input type="file" id="foto_principal" name="foto_principal"><br>
<small>Actualmente: <?=htmlspecialchars($anuncio['FPrincipal'])?></small><br><br>

<button type="submit">Modificar anuncio</button>
</fieldset>
</form>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
