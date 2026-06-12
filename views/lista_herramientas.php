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

    // Obtener proyectos de esta categoría
    $stmt = $pdo->prepare("SELECT * FROM proyectos WHERE categoria_id = ? AND estado = 'publicado' ORDER BY fecha_creacion DESC");
    $stmt->execute([$categoria_id]);
    $proyectos = $stmt->fetchAll();

} catch (Exception $e) {
    $categoria = null;
    $tutoriales = [];
    $proyectos = [];
}

$page_title = $categoria['nombre'];
$header_title = $categoria['nombre'];

include '../includes/header.php';
?>

<div class="space-y-xl">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md">
        <a href="/tutor/index.php" class="hover:text-primary transition-colors">Inicio</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface"><?php echo $categoria['nombre']; ?></span>
    </nav>

    <!-- Sección de Proyectos Prácticos (Destacada) -->
    <section>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-md mb-lg">
            <div class="flex items-center gap-md">
                <span class="material-symbols-outlined text-primary text-3xl">lightbulb</span>
                <div>
                    <h2 class="font-headline-xl text-headline-xl text-on-surface">Proyectos Prácticos</h2>
                    <p class="font-body-md text-on-surface-variant">Aplica lo aprendido creando proyectos reales paso a paso.</p>
                </div>
            </div>
            <a href="/tutor/estudiante/subir_proyecto.php" class="inline-flex items-center gap-2 bg-surface-container-high border border-primary text-primary font-label-md px-4 py-2 rounded-lg hover:bg-primary/10 transition-colors">
                <span class="material-symbols-outlined text-lg">upload</span>
                Sube tu Proyecto
            </a>
        </div>

        <?php if (empty($proyectos)): ?>
            <div class="bg-surface-container rounded-xl p-lg border border-outline-variant text-center border-dashed">
                <p class="font-body-md text-on-surface-variant">Próximamente añadiremos nuevos proyectos para esta categoría.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
                <?php foreach ($proyectos as $proj): ?>
                    <a href="detalle_proyecto.php?id=<?php echo $proj['id']; ?>" class="group">
                        <div class="bg-surface-container rounded-xl overflow-hidden border border-outline-variant hover:border-primary transition-all duration-300 transform group-hover:-translate-y-1 shadow-lg flex flex-col h-full">
                            <!-- Placeholder para la imagen de portada o un gradiente si no hay -->
                            <div class="h-32 bg-surface-variant flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-primary/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <span class="material-symbols-outlined text-5xl text-on-surface-variant opacity-50">play_circle</span>
                            </div>
                            <div class="p-lg flex flex-col flex-grow">
                                <div class="flex items-center gap-sm mb-sm">
                                    <span class="px-2 py-0.5 rounded bg-surface-container-highest text-on-surface text-[11px] font-bold uppercase border border-outline-variant">
                                        <?php 
                                            if($proj['dificultad'] == 'principiante') echo 'Principiante 🟢';
                                            if($proj['dificultad'] == 'intermedio') echo 'Intermedio 🟡';
                                            if($proj['dificultad'] == 'avanzado') echo 'Avanzado 🔴';
                                        ?>
                                    </span>
                                    <span class="text-on-surface-variant text-[12px] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">schedule</span> <?php echo $proj['tiempo_estimado']; ?>m
                                    </span>
                                </div>
                                <h3 class="font-headline-md text-headline-md text-on-surface mb-xs group-hover:text-primary transition-colors"><?php echo $proj['titulo']; ?></h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant mb-lg flex-grow"><?php echo $proj['descripcion_corta']; ?></p>
                                <div class="flex items-center text-primary font-label-md text-label-md mt-auto">
                                    Comenzar proyecto <span class="material-symbols-outlined text-sm ml-1 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Separador -->
    <hr class="border-outline-variant">

    <!-- Sección de Herramientas Básicas -->
    <section>
        <div class="flex items-center gap-md mb-lg">
            <span class="material-symbols-outlined text-on-surface-variant text-3xl">build</span>
            <div>
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Diccionario de Herramientas</h2>
                <p class="font-body-sm text-on-surface-variant">Conoce el funcionamiento técnico de cada herramienta.</p>
            </div>
        </div>

        <?php if (empty($tutoriales)): ?>
            <div class="bg-surface-container rounded-xl p-lg border border-outline-variant text-center border-dashed">
                <p class="font-body-md text-on-surface-variant">Aún no hay herramientas publicadas.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-md">
                <?php foreach ($tutoriales as $tut): ?>
                    <a href="detalle_herramienta.php?id=<?php echo $tut['id']; ?>" class="group">
                        <div class="bg-surface-container-low rounded-lg p-md border border-outline-variant hover:border-on-surface-variant transition-all flex items-start gap-md h-full">
                            <div class="w-10 h-10 rounded flex-shrink-0 flex items-center justify-center bg-surface-container-highest border border-outline-variant">
                                <span class="material-symbols-outlined text-xl text-on-surface" style="font-variation-settings: 'FILL' 1;">
                                    <?php echo $categoria['icono']; ?>
                                </span>
                            </div>
                            <div>
                                <h3 class="font-label-lg text-label-lg text-on-surface group-hover:text-primary transition-colors"><?php echo $tut['titulo']; ?></h3>
                                <p class="text-[12px] text-on-surface-variant mt-1 line-clamp-2"><?php echo $tut['descripcion']; ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include '../includes/footer.php'; ?>
