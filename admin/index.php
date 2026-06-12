<?php
session_start();
require_once '../config/database.php';

// Protección: Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header('Location: ../auth/login.php');
    exit;
}

// Obtener todos los tutoriales
try {
    $stmt = $pdo->query("
        SELECT t.*, c.nombre as categoria_nombre 
        FROM tutoriales t 
        JOIN categorias c ON t.categoria_id = c.id 
        ORDER BY t.fecha_creacion DESC
    ");
    $tutoriales = $stmt->fetchAll();
} catch (Exception $e) {
    $tutoriales = [];
}

$page_title = "Panel de Administración";
$header_title = "Admin Dashboard";

include '../includes/header.php';
?>

<div class="space-y-lg">
    <!-- Navegación Admin -->
    <div class="flex border-b border-outline-variant mb-lg overflow-x-auto">
        <a href="index.php" class="px-lg py-md border-b-2 border-primary text-primary font-label-lg text-label-lg transition-colors whitespace-nowrap">Herramientas</a>
        <a href="proyectos.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Entregas</a>
        <a href="categorias.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Categorías</a>
        <a href="matriculas.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Matrículas</a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-md mb-xl">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-surface">Gestión de Contenidos</h2>
            <p class="font-body-md text-on-surface-variant">Administra los tutoriales y guías del tutor.</p>
        </div>
        <a href="anadir_tutorial.php" class="inline-flex items-center gap-2 bg-primary-container text-on-primary font-label-lg px-lg py-3 rounded-lg hover:brightness-110 transition-all shadow-md">
            <span class="material-symbols-outlined">add</span>
            Nuevo Tutorial
        </a>
    </div>

    <div class="bg-surface-container rounded-xl border border-outline-variant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-highest border-b border-outline-variant">
                        <th class="px-lg py-md font-label-lg text-on-surface">Título</th>
                        <th class="px-lg py-md font-label-lg text-on-surface">Categoría</th>
                        <th class="px-lg py-md font-label-lg text-on-surface">Estado</th>
                        <th class="px-lg py-md font-label-lg text-on-surface">Fecha</th>
                        <th class="px-lg py-md font-label-lg text-on-surface text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    <?php if (empty($tutoriales)): ?>
                        <tr>
                            <td colspan="5" class="px-lg py-xl text-center text-on-surface-variant italic">No hay tutoriales registrados aún.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tutoriales as $tut): ?>
                            <tr class="hover:bg-surface-container-high transition-colors">
                                <td class="px-lg py-md">
                                    <div class="font-label-lg text-on-surface"><?php echo $tut['titulo']; ?></div>
                                    <div class="text-[10px] text-on-surface-variant truncate max-w-[200px]"><?php echo $tut['descripcion']; ?></div>
                                </td>
                                <td class="px-lg py-md">
                                    <span class="px-2 py-0.5 rounded bg-secondary-container text-on-secondary-container text-[11px] font-bold uppercase">
                                        <?php echo $tut['categoria_nombre']; ?>
                                    </span>
                                </td>
                                <td class="px-lg py-md">
                                    <?php if ($tut['estado'] === 'publicado'): ?>
                                        <span class="flex items-center gap-1 text-green-400 text-[12px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Publicado
                                        </span>
                                    <?php else: ?>
                                        <span class="flex items-center gap-1 text-orange-400 text-[12px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span> Pendiente
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-lg py-md text-on-surface-variant text-[12px]">
                                    <?php echo date('d/m/Y', strtotime($tut['fecha_creacion'])); ?>
                                </td>
                                <td class="px-lg py-md text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="editar_tutorial.php?id=<?php echo $tut['id']; ?>" class="p-2 text-primary hover:bg-primary/10 rounded-full transition-all" title="Editar">
                                            <span class="material-symbols-outlined">edit</span>
                                        </a>
                                        <a href="eliminar_tutorial.php?id=<?php echo $tut['id']; ?>" class="p-2 text-error hover:bg-error/10 rounded-full transition-all" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar este tutorial?')">
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
