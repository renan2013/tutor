<?php
session_start();
require_once '../config/database.php';

// Protección: Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header('Location: ../auth/login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Obtener detalles del proyecto y del autor
try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre, u.nombre as autor_nombre 
        FROM proyectos p 
        JOIN categorias c ON p.categoria_id = c.id 
        JOIN usuarios u ON p.autor_id = u.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $proyecto = $stmt->fetch();

    if (!$proyecto) {
        header('Location: proyectos.php');
        exit;
    }

    // Obtener herramientas vinculadas
    $stmt_herr = $pdo->prepare("
        SELECT t.titulo 
        FROM proyecto_herramientas ph
        JOIN tutoriales t ON ph.tutorial_id = t.id
        WHERE ph.proyecto_id = ?
    ");
    $stmt_herr->execute([$id]);
    $herramientas = $stmt_herr->fetchAll(PDO::FETCH_COLUMN);

} catch (Exception $e) {
    header('Location: proyectos.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $calificacion = (int)$_POST['calificacion'];
    $feedback_tutor = trim($_POST['feedback_tutor']);
    $estado = $_POST['estado'];
    $dificultad = $_POST['dificultad'];
    $tiempo_estimado = (int)$_POST['tiempo_estimado'];

    try {
        $stmt = $pdo->prepare("
            UPDATE proyectos 
            SET calificacion = ?, feedback_tutor = ?, estado = ?, dificultad = ?, tiempo_estimado = ?
            WHERE id = ?
        ");
        $stmt->execute([$calificacion, $feedback_tutor, $estado, $dificultad, $tiempo_estimado, $id]);
        
        $success = 'Evaluación guardada correctamente.';
        
        // Recargar datos
        $stmt->execute([$calificacion, $feedback_tutor, $estado, $dificultad, $tiempo_estimado, $id]); // re-bind is not strictly needed for fetch next, but just to refresh variables if we wanted.
        // Actually, just fetch again:
        $stmt_reload = $pdo->prepare("SELECT * FROM proyectos WHERE id = ?");
        $stmt_reload->execute([$id]);
        $proyecto = $stmt_reload->fetch();

    } catch (Exception $e) {
        $error = 'Error al guardar la evaluación: ' . $e->getMessage();
    }
}

$page_title = "Evaluar Proyecto";
$header_title = "Revisión del Tutor";

include '../includes/header.php';
?>

<div class="max-w-5xl mx-auto">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-lg">
        <a href="index.php" class="hover:text-primary transition-colors">Admin</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <a href="proyectos.php" class="hover:text-primary transition-colors">Entregas</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface">Evaluar</span>
    </nav>

    <?php if ($success): ?>
        <div class="bg-primary-container/20 text-primary-container p-md rounded-lg mb-lg flex items-center gap-md border border-primary-container/30">
            <span class="material-symbols-outlined">check_circle</span>
            <p class="font-body-sm"><?php echo $success; ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-xl">
        <!-- Columna Izquierda: El Proyecto del Alumno -->
        <div class="lg:col-span-2 space-y-lg">
            <div class="bg-surface-container rounded-xl p-lg border border-outline-variant shadow-lg">
                <div class="flex items-center gap-sm mb-md">
                    <span class="material-symbols-outlined text-3xl text-on-surface-variant">person</span>
                    <div>
                        <h2 class="font-headline-md text-on-surface"><?php echo htmlspecialchars($proyecto['autor_nombre']); ?></h2>
                        <span class="text-xs text-on-surface-variant bg-secondary-container px-2 py-0.5 rounded"><?php echo $proyecto['categoria_nombre']; ?></span>
                    </div>
                </div>
                
                <h1 class="font-headline-xl text-primary mb-sm"><?php echo htmlspecialchars($proyecto['titulo']); ?></h1>
                <p class="font-body-md text-on-surface-variant mb-lg"><?php echo htmlspecialchars($proyecto['descripcion_corta']); ?></p>
                
                <?php if(!empty($herramientas)): ?>
                <div class="mb-lg">
                    <h4 class="font-label-md text-on-surface-variant mb-2 uppercase tracking-widest">Herramientas Aplicadas:</h4>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach($herramientas as $h): ?>
                            <span class="px-2 py-1 border border-outline-variant rounded-md text-xs text-on-surface bg-surface-container-highest flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">build</span> <?php echo htmlspecialchars($h); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-lg">
                    <!-- Aquí se renderiza el video de YouTube -->
                    <?php 
                        $html_video = $proyecto['contenido_html'];
                        if (strpos($html_video, 'allowfullscreen') === false) {
                            $html_video = str_replace('<iframe ', '<iframe allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" ', $html_video);
                        }
                        echo $html_video; 
                    ?>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Panel de Evaluación -->
        <div class="space-y-lg">
            <div class="bg-surface-container-low rounded-xl p-lg border border-primary-container/30 shadow-lg sticky top-24">
                <h3 class="font-headline-md text-primary mb-md flex items-center gap-2 border-b border-outline-variant pb-2">
                    <span class="material-symbols-outlined">grading</span> Evaluación
                </h3>
                
                <form method="POST" class="space-y-md">
                    <div>
                        <label for="calificacion" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Calificación (0-100)</label>
                        <input type="number" id="calificacion" name="calificacion" required min="0" max="100" value="<?php echo $proyecto['calificacion'] ?? ''; ?>"
                            class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 text-2xl font-bold text-center focus:outline-none focus:border-primary-container transition-all">
                    </div>

                    <div>
                        <label for="feedback_tutor" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Feedback para el alumno</label>
                        <textarea id="feedback_tutor" name="feedback_tutor" rows="4" required placeholder="Excelente uso de la pluma, pero cuida las proporciones..."
                            class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all text-sm"><?php echo htmlspecialchars($proyecto['feedback_tutor'] ?? ''); ?></textarea>
                    </div>

                    <hr class="border-outline-variant my-md">

                    <h4 class="font-label-md text-on-surface-variant mb-2 uppercase tracking-widest">Ajustes para Publicación</h4>
                    <div class="grid grid-cols-2 gap-sm mb-md">
                        <div>
                            <label for="dificultad" class="block text-xs text-on-surface-variant mb-1">Nivel</label>
                            <select id="dificultad" name="dificultad" class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-md px-2 py-1 text-sm focus:outline-none">
                                <option value="principiante" <?php echo ($proyecto['dificultad']=='principiante')?'selected':''; ?>>Principiante</option>
                                <option value="intermedio" <?php echo ($proyecto['dificultad']=='intermedio')?'selected':''; ?>>Intermedio</option>
                                <option value="avanzado" <?php echo ($proyecto['dificultad']=='avanzado')?'selected':''; ?>>Avanzado</option>
                            </select>
                        </div>
                        <div>
                            <label for="tiempo_estimado" class="block text-xs text-on-surface-variant mb-1">Tiempo (min)</label>
                            <input type="number" id="tiempo_estimado" name="tiempo_estimado" value="<?php echo $proyecto['tiempo_estimado']; ?>"
                                class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-md px-2 py-1 text-sm focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="estado" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Decisión Final</label>
                        <select id="estado" name="estado" class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none">
                            <option value="revision" <?php echo ($proyecto['estado']=='revision')?'selected':''; ?>>Mantener en Revisión</option>
                            <option value="publicado" <?php echo ($proyecto['estado']=='publicado')?'selected':''; ?>>Aprobar y Publicar en Galería</option>
                            <option value="rechazado" <?php echo ($proyecto['estado']=='rechazado')?'selected':''; ?>>Rechazar (Pedir correcciones)</option>
                        </select>
                        <p class="text-[10px] text-on-surface-variant mt-1">Si apruebas, el video será visible para todos los estudiantes en el muro de la categoría.</p>
                    </div>

                    <button type="submit" class="w-full bg-primary-container text-on-primary font-label-lg py-3 rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md mt-md">
                        Guardar Evaluación
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
