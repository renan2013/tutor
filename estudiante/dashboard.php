<?php
session_start();
require_once '../config/database.php';

// Protección: Solo usuarios logueados (estudiantes o admins)
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener los proyectos enviados por este usuario
try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM proyectos p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.autor_id = ? 
        ORDER BY p.fecha_creacion DESC
    ");
    $stmt->execute([$usuario_id]);
    $mis_proyectos = $stmt->fetchAll();
} catch (Exception $e) {
    $mis_proyectos = [];
}

$page_title = "Mi Progreso";
$header_title = "Dashboard Estudiante";

include '../includes/header.php';
?>

<div class="space-y-lg">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-md mb-xl">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-surface">Mis Proyectos y Entregas</h2>
            <p class="font-body-md text-on-surface-variant">Sube tus videos demostrando lo que has aprendido para ser evaluado.</p>
        </div>
        <a href="subir_proyecto.php" class="inline-flex items-center gap-2 bg-primary-container text-on-primary font-label-lg px-lg py-3 rounded-lg hover:brightness-110 transition-all shadow-md">
            <span class="material-symbols-outlined">publish</span>
            Entregar Proyecto
        </a>
    </div>

    <!-- Lista de proyectos del estudiante -->
    <div class="grid grid-cols-1 gap-md">
        <?php if (empty($mis_proyectos)): ?>
            <div class="bg-surface-container rounded-xl p-xl border border-outline-variant text-center border-dashed">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-md">school</span>
                <p class="font-body-lg text-on-surface-variant mb-sm">Aún no has entregado ningún proyecto.</p>
                <p class="font-body-sm text-on-surface-variant">Aprende una herramienta, graba tu progreso y compártelo aquí.</p>
            </div>
        <?php else: ?>
            <?php foreach ($mis_proyectos as $proj): ?>
                <div class="bg-surface-container rounded-xl p-lg border border-outline-variant flex flex-col md:flex-row gap-lg items-start">
                    <!-- Estado Visual -->
                    <div class="w-full md:w-48 flex-shrink-0 flex flex-col items-center justify-center p-md rounded-lg border border-outline-variant bg-surface-container-highest">
                        <?php if ($proj['estado'] === 'revision'): ?>
                            <span class="material-symbols-outlined text-4xl text-orange-400 mb-2">hourglass_empty</span>
                            <span class="font-label-md text-orange-400 uppercase tracking-widest text-[10px]">En Revisión</span>
                            <span class="text-on-surface-variant text-xs mt-1 text-center">El tutor está evaluando tu video.</span>
                        <?php elseif ($proj['estado'] === 'publicado'): ?>
                            <span class="material-symbols-outlined text-4xl text-green-400 mb-2">verified</span>
                            <span class="font-label-md text-green-400 uppercase tracking-widest text-[10px]">Aprobado</span>
                            <span class="font-headline-lg text-on-surface mt-1"><?php echo $proj['calificacion']; ?><span class="text-sm text-on-surface-variant">/100</span></span>
                        <?php elseif ($proj['estado'] === 'rechazado'): ?>
                            <span class="material-symbols-outlined text-4xl text-error mb-2">cancel</span>
                            <span class="font-label-md text-error uppercase tracking-widest text-[10px]">Rechazado</span>
                            <span class="text-on-surface-variant text-xs mt-1 text-center">Revisa el feedback e inténtalo de nuevo.</span>
                        <?php else: ?>
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-2">edit_note</span>
                            <span class="font-label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Borrador</span>
                        <?php endif; ?>
                    </div>

                    <!-- Detalles del Proyecto -->
                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-sm">
                            <h3 class="font-headline-md text-headline-md text-primary"><?php echo htmlspecialchars($proj['titulo']); ?></h3>
                            <span class="text-xs text-on-surface-variant bg-secondary-container px-2 py-1 rounded"><?php echo $proj['categoria_nombre']; ?></span>
                        </div>
                        <p class="font-body-sm text-on-surface-variant mb-md"><?php echo htmlspecialchars($proj['descripcion_corta']); ?></p>
                        
                        <?php if ($proj['feedback_tutor']): ?>
                            <div class="bg-surface-container-low border border-outline-variant p-sm rounded-lg relative mt-md">
                                <span class="absolute -top-3 left-4 bg-surface px-1 text-[10px] font-bold text-tertiary-container uppercase tracking-widest">Feedback del Tutor</span>
                                <p class="text-sm text-on-surface italic">"<?php echo htmlspecialchars($proj['feedback_tutor']); ?>"</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-md text-xs text-on-surface-variant">
                            Enviado el: <?php echo date('d/m/Y H:i', strtotime($proj['fecha_creacion'])); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
