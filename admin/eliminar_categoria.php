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
        // 1. Verificar si la categoría tiene tutoriales
        $stmt_tut = $pdo->prepare("SELECT COUNT(*) FROM tutoriales WHERE categoria_id = ?");
        $stmt_tut->execute([$id]);
        $has_tutorials = $stmt_tut->fetchColumn() > 0;

        // 2. Verificar si la categoría tiene proyectos
        $stmt_proj = $pdo->prepare("SELECT COUNT(*) FROM proyectos WHERE categoria_id = ?");
        $stmt_proj->execute([$id]);
        $has_projects = $stmt_proj->fetchColumn() > 0;

        if ($has_tutorials || $has_projects) {
            // Redirigir con código de error si tiene contenido
            header('Location: categorias.php?error=en_uso');
            exit;
        }

        // Si está vacía, proceder a eliminar
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
    } catch (Exception $e) {
        // Manejo de errores
    }
}

header('Location: categorias.php');
exit;
?>