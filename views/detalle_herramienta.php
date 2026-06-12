<?php
session_start();
require_once '../config/database.php';

$tutorial_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($tutorial_id <= 0) {
    header('Location: /index.php');
    exit;
}

// Obtener detalles del tutorial y su categoría
try {
    $stmt = $pdo->prepare("
        SELECT t.*, c.nombre as categoria_nombre, c.id as categoria_id 
        FROM tutoriales t 
        JOIN categorias c ON t.categoria_id = c.id 
        WHERE t.id = ?
    ");
    $stmt->execute([$tutorial_id]);
    $tutorial = $stmt->fetch();

    if (!$tutorial) {
        header('Location: /index.php');
        exit;
    }

} catch (Exception $e) {
    header('Location: /index.php');
    exit;
}

$page_title = $tutorial['titulo'];
$header_title = $tutorial['titulo'];

include '../includes/header.php';
?>

<div class="space-y-lg">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-lg">
        <a href="/index.php" class="hover:text-primary transition-colors">Inicio</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <a href="/views/lista_herramientas.php?id=<?php echo $tutorial['categoria_id']; ?>" class="hover:text-primary transition-colors"><?php echo $tutorial['categoria_nombre']; ?></a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface"><?php echo $tutorial['titulo']; ?></span>
    </nav>

    <!-- El contenido_html de la base de datos se inyecta aquí -->
    <?php echo $tutorial['contenido_html']; ?>

</div>

<?php include '../includes/footer.php'; ?>
