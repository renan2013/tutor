<?php
session_start();
require_once '../config/database.php';

// Protección: Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header('Location: ../auth/login.php');
    exit;
}

// Obtener todos los proyectos
try {
    $stmt = $pdo->query("
        SELECT p.*, c.nombre as categoria_nombre, u.nombre as autor_nombre 
        FROM proyectos p 
        JOIN categorias c ON p.categoria_id = c.id 
        JOIN usuarios u ON p.autor_id = u.id
        ORDER BY FIELD(p.estado, 'revision', 'publicado', 'rechazado', 'borrador'), p.fecha_creacion DESC
    ");
    $proyectos = $stmt->fetchAll();
} catch (Exception $e) {
    $proyectos = [];
}

$page_title = "Gestión de Proyectos";
$header_title = "Admin Dashboard";

include '../includes/header.php';
?>

<div class="space-y-lg">
    <!-- Navegación Admin -->
    <div class="flex border-b border-outline-variant mb-lg overflow-x-auto">
        <a href="index.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Herramientas</a>
        <a href="proyectos.php" class="px-lg py-md border-b-2 border-primary text-primary font-label-lg text-label-lg transition-colors whitespace-nowrap">Entregas</a>
        <a href="categorias.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Categorías</a>
        <a href="matriculas.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Matrículas</a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-md mb-xl">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-surface">Revisión de Proyectos</h2>
            <p class="font-body-md text-on-surface-variant">Evalúa los videos tutoriales enviados por los estudiantes.</p>
        </div>
    </div>

    <div class="bg-surface-container rounded-xl border border-outline-variant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-highest border-b border-outline-variant">
                        <th class="px-lg py-md font-label-lg text-on-surface">Proyecto y Estudiante</th>
                        <th class="px-lg py-md font-label-lg text-on-surface">Categoría</th>
                        <th class="px-lg py-md font-label-lg text-on-surface">Estado / Nota</th>
                        <th class="px-lg py-md font-label-lg text-on-surface text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    <?php if (empty($proyectos)): ?>
                        <tr>
                            <td colspan="4" class="px-lg py-xl text-center text-on-surface-variant italic">No hay entregas registradas aún.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($proyectos as $proj): ?>
                            <tr class="hover:bg-surface-container-high transition-colors <?php echo ($proj['estado'] == 'revision') ? 'bg-primary/5' : ''; ?>">
                                <td class="px-lg py-md">
                                    <div class="font-label-lg text-on-surface"><?php echo htmlspecialchars($proj['titulo']); ?></div>
                                    <div class="text-[12px] text-on-surface-variant mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">person</span> <?php echo htmlspecialchars($proj['autor_nombre']); ?>
                                    </div>
                                </td>
                                <td class="px-lg py-md">
                                    <span class="text-[11px] text-on-surface-variant bg-secondary-container inline-block px-2 py-0.5 rounded uppercase font-bold">
                                        <?php echo $proj['categoria_nombre']; ?>
                                    </span>
                                </td>
                                <td class="px-lg py-md">
                                    <?php if ($proj['estado'] === 'revision'): ?>
                                        <span class="flex items-center gap-1 text-orange-400 text-[12px] font-bold">
                                            <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span> PENDIENTE
                                        </span>
                                    <?php elseif ($proj['estado'] === 'publicado'): ?>
                                        <div class="flex flex-col">
                                            <span class="flex items-center gap-1 text-green-400 text-[12px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Publicado
                                            </span>
                                            <span class="font-label-md text-on-surface mt-1"><?php echo $proj['calificacion']; ?>/100</span>
                                        </div>
                                    <?php elseif ($proj['estado'] === 'rechazado'): ?>
                                        <span class="flex items-center gap-1 text-error text-[12px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-error"></span> Rechazado
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-lg py-md text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="calificar_proyecto.php?id=<?php echo $proj['id']; ?>" class="inline-flex items-center gap-1 <?php echo ($proj['estado'] == 'revision') ? 'bg-primary-container text-on-primary' : 'bg-surface-variant text-on-surface'; ?> px-3 py-1.5 rounded text-sm hover:brightness-110 transition-all">
                                            <?php if ($proj['estado'] === 'revision'): ?>
                                                <span class="material-symbols-outlined text-[18px]">grading</span> Evaluar
                                            <?php else: ?>
                                                <span class="material-symbols-outlined text-[18px]">visibility</span> Ver
                                            <?php endif; ?>
                                        </a>
                                        <a href="eliminar_proyecto.php?id=<?php echo $proj['id']; ?>" class="inline-flex items-center gap-1 bg-error/10 text-error px-2 py-1.5 rounded text-sm hover:bg-error/20 transition-all" onclick="return confirm('¿Estás seguro de que quieres eliminar este proyecto?');" title="Eliminar">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
