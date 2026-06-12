<?php
session_start();
require_once '../config/database.php';

// Protección: Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header('Location: ../auth/login.php');
    exit;
}

// Obtener todas las categorías
try {
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
    $categorias = $stmt->fetchAll();
} catch (Exception $e) {
    $categorias = [];
}

$page_title = "Gestión de Categorías";
$header_title = "Admin Dashboard";

include '../includes/header.php';
?>

<div class="space-y-lg">
    <!-- Navegación Admin -->
    <div class="flex border-b border-outline-variant mb-lg overflow-x-auto">
        <a href="index.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Herramientas</a>
        <a href="proyectos.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Entregas</a>
        <a href="categorias.php" class="px-lg py-md border-b-2 border-primary text-primary font-label-lg text-label-lg transition-colors whitespace-nowrap">Categorías</a>
        <a href="matriculas.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Matrículas</a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-md mb-xl">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-surface">Cursos / Categorías</h2>
            <p class="font-body-md text-on-surface-variant">Administra los programas de diseño disponibles en la plataforma.</p>
        </div>
        <a href="anadir_categoria.php" class="inline-flex items-center gap-2 bg-primary-container text-on-primary font-label-lg px-lg py-3 rounded-lg hover:brightness-110 transition-all shadow-md">
            <span class="material-symbols-outlined">add</span>
            Nueva Categoría
        </a>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'en_uso'): ?>
        <div class="bg-error-container text-on-error-container p-md rounded-lg mb-lg flex items-center gap-md border border-error">
            <span class="material-symbols-outlined">error</span>
            <p class="font-body-sm">No se puede eliminar esta categoría porque tiene tutoriales o proyectos asociados. Elimínalos o muévelos primero.</p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-lg">
        <?php if (empty($categorias)): ?>
            <div class="col-span-full bg-surface-container rounded-xl p-xl border border-outline-variant text-center border-dashed">
                <p class="font-body-md text-on-surface-variant">No hay categorías registradas.</p>
            </div>
        <?php else: ?>
            <?php foreach ($categorias as $cat): ?>
                <div class="bg-surface-container rounded-xl p-lg border border-outline-variant flex flex-col justify-between hover:border-primary/50 transition-colors group">
                    <div class="flex items-start justify-between mb-md">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: <?php echo $cat['color_hex']; ?>20; border: 1px solid <?php echo $cat['color_hex']; ?>40;">
                            <span class="material-symbols-outlined text-2xl" style="color: <?php echo $cat['color_hex']; ?>; font-variation-settings: 'FILL' 1;"><?php echo htmlspecialchars($cat['icono']); ?></span>
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="editar_categoria.php?id=<?php echo $cat['id']; ?>" class="p-1.5 text-primary hover:bg-primary/10 rounded" title="Editar">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            <a href="eliminar_categoria.php?id=<?php echo $cat['id']; ?>" class="p-1.5 text-error hover:bg-error/10 rounded" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar este curso? Solo podrás hacerlo si está vacío.');">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-headline-md text-on-surface mb-1"><?php echo htmlspecialchars($cat['nombre']); ?></h3>
                        <div class="flex items-center gap-2 text-xs text-on-surface-variant font-mono">
                            <span class="w-3 h-3 rounded-full inline-block" style="background-color: <?php echo $cat['color_hex']; ?>;"></span> <?php echo $cat['color_hex']; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>