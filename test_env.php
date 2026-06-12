<?php
/**
 * Script de diagnóstico para errores 500
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnóstico de Entorno</h1>";

$files_to_check = [
    'config/database.php',
    'config/api_keys.php',
    'includes/header.php',
    'includes/footer.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ El archivo <b>$file</b> existe.</p>";
    } else {
        echo "<p style='color: red;'>❌ El archivo <b>$file</b> NO existe.</p>";
    }
}

echo "<h2>Intentando conectar a la base de datos...</h2>";

if (file_exists('config/database.php')) {
    try {
        require_once 'config/database.php';
        echo "<p style='color: green;'>✅ Conexión a la base de datos exitosa.</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error de base de datos: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ No se puede probar la conexión porque config/database.php no existe.</p>";
}
?>
