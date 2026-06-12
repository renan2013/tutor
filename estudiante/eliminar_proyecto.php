<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$usuario_id = $_SESSION['usuario_id'];

if ($id > 0) {
    try {
        // Asegurar que el usuario solo pueda eliminar sus propios proyectos
        $stmt = $pdo->prepare("DELETE FROM proyectos WHERE id = ? AND autor_id = ?");
        $stmt->execute([$id, $usuario_id]);
    } catch (Exception $e) {
        // Manejo de errores
    }
}

header('Location: dashboard.php');
exit;
?>