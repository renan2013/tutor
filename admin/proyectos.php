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
        SELECT p.*, c.nombre as categoria_nombre 
        FROM proyectos p 
        JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.fecha_creacion DESC
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
    <div class="flex border-b border-outline-variant mb-lg">
        <a href="index.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors">Herramientas (Tutoriales)</a>
        <a href="proyectos.php" class="px-lg py-md border-b-2 border-primary text-primary font-label-lg text-label-lg transition-colors">Proyectos Prácticos</a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-md mb-xl">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-surface">Gestión de Proyectos</h2>
            <p class="font-body-md text-on-surface-variant">Administra los proyectos prácticos y sus retos.</p>
        </div>
        <a href="anadir_proyecto.php" class="inline-flex items-center gap-2 bg-primary-container text-on-primary font-label-lg px-lg py-3 rounded-lg hover:brightness-110 transition-all shadow-md">
            <span class="material-symbols-outlined">add</span>
            Nuevo Proyecto
        </a>
    </div>

    <div class="bg-surface-container rounded-xl border border-outline-variant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-highest border-b border-outline-variant">
                        <th class="px-lg py-md font-label-lg text-on-surface">Proyecto</th>
                        <th class="px-lg py-md font-label-lg text-on-surface">Dificultad / Tiempo</th>
                        <th class="px-lg py-md font-label-lg text-on-surface">Estado</th>
                        <th class="px-lg py-md font-label-lg text-on-surface text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    <?php if (empty($proyectos)): ?>
                        <tr>
                            <td colspan="4" class="px-lg py-xl text-center text-on-surface-variant italic">No hay proyectos registrados aún.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($proyectos as $proj): ?>
                            <tr class="hover:bg-surface-container-high transition-colors">
                                <td class="px-lg py-md">
                                    <div class="font-label-lg text-on-surface"><?php echo $proj['titulo']; ?></div>
                                    <div class="text-[11px] text-on-surface-variant bg-secondary-container inline-block px-1 rounded mt-1"><?php echo $proj['categoria_nombre']; ?></div>
                                </td>
                                <td class="px-lg py-md">
                                    <div class="font-label-md text-on-surface capitalize"><?php echo $proj['dificultad']; ?></div>
                                    <div class="text-[12px] text-on-surface-variant flex items-center gap-1 mt-1">
                                        <span class="material-symbols-outlined text-[14px]">schedule</span> <?php echo $proj['tiempo_estimado']; ?> min
                                    </div>
                                </td>
                                <td class="px-lg py-md">
                                    <?php if ($proj['estado'] === 'publicado'): ?>
                                        <span class="flex items-center gap-1 text-green-400 text-[12px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Publicado
                                        </span>
                                    <?php else: ?>
                                        <span class="flex items-center gap-1 text-orange-400 text-[12px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span> Borrador
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-lg py-md text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="#" class="p-2 text-primary hover:bg-primary/10 rounded-full transition-all" title="Editar (Próximamente)">
                                            <span class="material-symbols-outlined">edit</span>
                                        </a>
                                        <a href="#" class="p-2 text-error hover:bg-error/10 rounded-full transition-all" title="Eliminar (Próximamente)">
                                            <span class="material-symbols-outlined">delete</span>
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
