<?php
// Test de conexión a BD
echo "<h1>Test de Conexión a Base de Datos</h1>";

// Configuración
$config = [
    'Servidor'   => '127.0.0.1',
    'Usuario'    => 'root',
    'Contrasena' => '',
    'BaseDatos'  => 'pbid',
];

echo "<h2>Configuración:</h2>";
echo "<pre>";
var_dump($config);
echo "</pre>";

echo "<h2>Intentando conectar...</h2>";

try {
    $mysqli = new mysqli(
        $config['Servidor'],
        $config['Usuario'],
        $config['Contrasena'],
        $config['BaseDatos']
    );
    
    echo "✅ <strong>Conexión exitosa!</strong><br>";
    echo "Server info: " . $mysqli->server_info . "<br>";
    echo "Client info: " . $mysqli->client_info . "<br>";
    
    // Probar una consulta simple
    $result = $mysqli->query("SELECT 1");
    if ($result) {
        echo "✅ Consulta de prueba exitosa<br>";
    }
    
    $mysqli->close();
    
} catch (mysqli_sql_exception $e) {
    echo "❌ <strong>Error de conexión:</strong><br>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Código: " . $e->getCode() . "<br>";
}
?>
