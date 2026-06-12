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
        $stmt = $pdo->prepare("DELETE FROM tutoriales WHERE id = ?");
        $stmt->execute([$id]);
    } catch (Exception $e) {
        // Podrías manejar el error aquí si lo deseas
    }
}

header('Location: index.php');
exit;
?>
