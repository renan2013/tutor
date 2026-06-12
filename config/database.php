<?php
/**
 * Configuración de conexión a la base de datos (PDO)
 * Completa los datos con la información de tu panel de Hostinger
 */

$host = 'localhost'; // En Hostinger suele ser localhost si la DB está en el mismo plan
$db   = 'u400283574_adobe';
$user = 'u400283574_adobe';
$pass = 'Diseno2026!';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
