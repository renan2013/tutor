<?php
session_start();
require_once '../config/database.php';

// Protección: Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header('Location: ../auth/login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // Al eliminar una categoría, debido a los ON DELETE CASCADE en la BD,
        // se eliminarán automáticamente los tutoriales, proyectos y matrículas asociadas a ella.
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
    } catch (Exception $e) {
        // Manejo de errores
    }
}

header('Location: categorias.php');
exit;
?>