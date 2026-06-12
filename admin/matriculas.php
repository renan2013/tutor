<?php
session_start();
require_once '../config/database.php';

// Protección: Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header('Location: ../auth/login.php');
    exit;
}

// Obtener todos los estudiantes y la cantidad de cursos en los que están matriculados
try {
    $stmt = $pdo->query("
        SELECT u.id, u.nombre, u.email, COUNT(m.categoria_id) as total_cursos
        FROM usuarios u
        LEFT JOIN matriculas m ON u.id = m.usuario_id
        WHERE u.rol = 'estudiante'
        GROUP BY u.id
        ORDER BY u.nombre ASC
    ");
    $estudiantes = $stmt->fetchAll();
} catch (Exception $e) {
    $estudiantes = [];
}

$page_title = "Gestión de Matrículas";
$header_title = "Admin Dashboard";

include '../includes/header.php';
?>

<div class="space-y-lg">
    <!-- Navegación Admin -->
    <div class="flex border-b border-outline-variant mb-lg overflow-x-auto">
        <a href="index.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Herramientas</a>
        <a href="proyectos.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Entregas</a>
        <a href="categorias.php" class="px-lg py-md border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-colors whitespace-nowrap">Categorías</a>
        <a href="matriculas.php" class="px-lg py-md border-b-2 border-primary text-primary font-label-lg text-label-lg transition-colors whitespace-nowrap">Matrículas</a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-md mb-xl">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-on-surface">Gestión de Matrículas</h2>
            <p class="font-body-md text-on-surface-variant">Asigna acceso a cursos específicos a los estudiantes.</p>
        </div>
    </div>

    <div class="bg-surface-container rounded-xl border border-outline-variant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-highest border-b border-outline-variant">
                        <th class="px-lg py-md font-label-lg text-on-surface">Estudiante</th>
                        <th class="px-lg py-md font-label-lg text-on-surface text-center">Cursos Inscritos</th>
                        <th class="px-lg py-md font-label-lg text-on-surface text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    <?php if (empty($estudiantes)): ?>
                        <tr>
                            <td colspan="3" class="px-lg py-xl text-center text-on-surface-variant italic">No hay estudiantes registrados en la plataforma.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($estudiantes as $est): ?>
                            <tr class="hover:bg-surface-container-high transition-colors">
                                <td class="px-lg py-md">
                                    <div class="font-label-lg text-on-surface flex items-center gap-2">
                                        <span class="material-symbols-outlined text-on-surface-variant">person</span>
                                        <?php echo htmlspecialchars($est['nombre']); ?>
                                    </div>
                                    <div class="text-[12px] text-on-surface-variant mt-1 ml-8"><?php echo htmlspecialchars($est['email']); ?></div>
                                </td>
                                <td class="px-lg py-md text-center">
                                    <span class="bg-primary-container/20 text-primary-container font-bold px-3 py-1 rounded-full text-sm">
                                        <?php echo $est['total_cursos']; ?>
                                    </span>
                                </td>
                                <td class="px-lg py-md text-right">
                                    <a href="gestionar_matricula.php?id=<?php echo $est['id']; ?>" class="inline-flex items-center gap-1 bg-surface-variant text-on-surface px-4 py-2 rounded-lg text-sm hover:brightness-110 transition-all shadow-sm">
                                        <span class="material-symbols-outlined text-[18px]">settings</span> Administrar Accesos
                                    </a>
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