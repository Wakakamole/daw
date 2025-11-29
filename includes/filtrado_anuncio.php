<?php
/**
 * filtrado_anuncio.php
 * Recoge $_POST y devuelve [$datos, $errores]
 */

$errores = [];
$datos = [];

// Campos obligatorios
$datos['titulo'] = trim($_POST['titulo'] ?? '');
$datos['descripcion'] = trim($_POST['descripcion'] ?? '');
$datos['tipo_anuncio'] = intval($_POST['tipo_anuncio'] ?? 0);
$datos['tipo_vivienda'] = intval($_POST['tipo_vivienda'] ?? 0);
$datos['ciudad'] = trim($_POST['ciudad'] ?? '');
$datos['pais'] = intval($_POST['pais'] ?? 0);
$datos['precio'] = floatval($_POST['precio'] ?? 0);

// Campos opcionales
$datos['superficie'] = (isset($_POST['superficie']) && $_POST['superficie'] !== '') ? floatval($_POST['superficie']) : null;
$datos['habitaciones'] = (isset($_POST['habitaciones']) && $_POST['habitaciones'] !== '') ? intval($_POST['habitaciones']) : null;
$datos['banos'] = (isset($_POST['banos']) && $_POST['banos'] !== '') ? intval($_POST['banos']) : null;
$datos['plantas'] = (isset($_POST['plantas']) && $_POST['plantas'] !== '') ? intval($_POST['plantas']) : null;
$datos['ano'] = (isset($_POST['ano']) && $_POST['ano'] !== '') ? intval($_POST['ano']) : null;

// Validaciones
if ($datos['titulo'] === '') $errores[] = "El título es obligatorio.";
if ($datos['descripcion'] === '') $errores[] = "La descripción es obligatoria.";
if ($datos['tipo_anuncio'] <= 0) $errores[] = "Seleccione un tipo de anuncio.";
if ($datos['tipo_vivienda'] <= 0) $errores[] = "Seleccione un tipo de vivienda.";
if ($datos['ciudad'] === '') $errores[] = "La ciudad es obligatoria.";
if ($datos['pais'] <= 0) $errores[] = "Seleccione un país.";
if ($datos['precio'] <= 0) $errores[] = "El precio es obligatorio y debe ser mayor que 0.";

// Para crear anuncio, se puede validar foto
if ($validar_foto ?? false) {
    if (!isset($_FILES['foto_principal']) || $_FILES['foto_principal']['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Debe subir una foto principal válida.";
    }
}
