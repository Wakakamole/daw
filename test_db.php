<?php
require 'includes/basedatos.php';

try {
    $db = get_db();
    echo "¡Conexión exitosa a pbid!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
