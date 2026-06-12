<?php
session_start();
require_once '../config/database.php';

$categoria_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($categoria_id <= 0) {
    header('Location: /tutor/index.php');
    exit;
}

// Obtener detalles de la categoría
try {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$categoria_id]);
    $categoria = $stmt->fetch();

    if (!$categoria) {
        header('Location: /tutor/index.php');
        exit;
    }

    // Obtener tutoriales de esta categoría
    $stmt = $pdo->prepare("SELECT * FROM tutoriales WHERE categoria_id = ? AND estado = 'publicado' ORDER BY fecha_creacion DESC");
    $stmt->execute([$categoria_id]);
    $tutoriales = $stmt->fetchAll();

} catch (Exception $e) {
    $categoria = null;
    $tutoriales = [];
}

$page_title = "Herramientas de " . $categoria['nombre'];
$header_title = $categoria['nombre'];

include '../includes/header.php';
?>

<div class="space-y-lg">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-lg">
        <a href="/tutor/index.php" class="hover:text-primary transition-colors">Inicio</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface"><?php echo $categoria['nombre']; ?></span>
    </nav>

    <section class="mb-xl">
        <h2 class="font-headline-xl text-headline-xl mb-sm text-on-surface">Explora las Herramientas</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Selecciona una herramienta para ver su guía detallada y consejos maestros.</p>
    </section>

    <?php if (empty($tutoriales)): ?>
        <div class="bg-surface-container rounded-xl p-xl border border-outline-variant text-center">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-md">construction</span>
            <p class="font-body-lg text-on-surface-variant">Aún no hay tutoriales disponibles para esta categoría.</p>
            <a href="/tutor/index.php" class="inline-block mt-lg text-primary font-label-lg">Volver al inicio</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
            <?php foreach ($tutoriales as $tut): ?>
                <a href="/views/detalle_herramienta.php?id=<?php echo $tut['id']; ?>" class="group">
                    <div class="bg-surface-container rounded-xl p-lg border border-outline-variant hover:border-primary-container transition-all duration-300 transform group-hover:-translate-y-1 shadow-lg flex flex-col h-full">
                        <div class="flex items-start justify-between mb-md">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-primary-container/10 border border-primary-container/20">
                                <span class="material-symbols-outlined text-2xl text-primary-container" style="font-variation-settings: 'FILL' 1;">
                                    <?php echo $categoria['icono']; ?>
                                </span>
                            </div>
                            <?php if ($tut['v24_5_compatible']): ?>
                                <span class="bg-primary-container/10 text-primary-container border border-primary-container px-2 py-0.5 rounded text-[10px] font-bold">V24.5 OK</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-headline-md text-headline-md text-on-surface mb-xs"><?php echo $tut['titulo']; ?></h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mb-lg flex-grow"><?php echo $tut['descripcion']; ?></p>
                        <div class="flex items-center text-primary font-label-lg text-label-lg group-hover:gap-2 transition-all mt-auto">
                            Ver guía completa <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
