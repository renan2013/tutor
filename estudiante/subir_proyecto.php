<?php
session_start();
require_once '../config/database.php';

// Protección
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$error = '';
$success = '';

// Obtener categorías y tutoriales para el select
try {
    $stmt = $pdo->query("SELECT * FROM categorias");
    $categorias = $stmt->fetchAll();
    
    $stmt = $pdo->query("SELECT id, titulo, categoria_id FROM tutoriales WHERE estado = 'publicado' ORDER BY titulo");
    $tutoriales_disponibles = $stmt->fetchAll();
} catch (Exception $e) {
    $categorias = [];
    $tutoriales_disponibles = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion_corta = trim($_POST['descripcion_corta']);
    $categoria_id = (int)$_POST['categoria_id'];
    $youtube_url = trim($_POST['youtube_url']);
    $herramientas_seleccionadas = isset($_POST['herramientas']) ? $_POST['herramientas'] : [];

    // Extraer ID de YouTube
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $youtube_url, $match);
    $youtube_id = isset($match[1]) ? $match[1] : null;

    if (empty($titulo) || empty($descripcion_corta)) {
        $error = 'Por favor, completa el título y la descripción.';
    } elseif (!$youtube_id) {
        $error = 'No pudimos reconocer el enlace de YouTube. Asegúrate de que sea un enlace válido (ej. https://www.youtube.com/watch?v=...). Enlace ingresado: ' . htmlspecialchars($youtube_url);
    } else {
        // Construir el HTML del iframe
        $contenido_html = '
<div class="aspect-video w-full rounded-xl overflow-hidden shadow-lg border border-outline-variant my-lg">
    <iframe class="w-full h-full" src="https://www.youtube.com/embed/'.$youtube_id.'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
</div>';

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO proyectos (categoria_id, titulo, descripcion_corta, contenido_html, autor_id, estado, dificultad, tiempo_estimado) 
                VALUES (?, ?, ?, ?, ?, 'revision', 'principiante', 30)
            ");
            // Se asumen valores por defecto de dificultad/tiempo para los alumnos. El tutor puede ajustarlos luego.
            $stmt->execute([
                $categoria_id, 
                $titulo, 
                $descripcion_corta, 
                $contenido_html, 
                $_SESSION['usuario_id']
            ]);
            
            $proyecto_id = $pdo->lastInsertId();

            if (!empty($herramientas_seleccionadas)) {
                $stmt_herr = $pdo->prepare("INSERT INTO proyecto_herramientas (proyecto_id, tutorial_id) VALUES (?, ?)");
                foreach ($herramientas_seleccionadas as $tut_id) {
                    $stmt_herr->execute([$proyecto_id, (int)$tut_id]);
                }
            }

            $pdo->commit();
            $success = '¡Tu proyecto ha sido enviado correctamente! El tutor lo revisará pronto.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error al enviar el proyecto: ' . $e->getMessage();
        }
    }
}

$page_title = "Entregar Proyecto";
$header_title = "Nuevo Envío";

include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-lg">
        <a href="dashboard.php" class="hover:text-primary transition-colors">Mi Progreso</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface">Subir Proyecto</span>
    </nav>

    <div class="bg-surface-container rounded-xl p-xl border border-outline-variant shadow-lg">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-sm">Demuestra lo que has aprendido</h2>
        <p class="font-body-md text-on-surface-variant mb-xl">Graba un video explicando cómo realizaste tu diseño y compártelo para ser evaluado.</p>

        <?php if ($error): ?>
            <div class="bg-error-container text-on-error-container p-md rounded-lg mb-lg flex items-center gap-md border border-error">
                <span class="material-symbols-outlined">error</span>
                <p class="font-body-sm"><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-primary-container/20 text-primary-container p-md rounded-lg mb-lg flex items-center gap-md border border-primary-container/30">
                <span class="material-symbols-outlined">check_circle</span>
                <p class="font-body-sm"><?php echo $success; ?></p>
            </div>
            <a href="dashboard.php" class="inline-block mt-4 bg-primary-container text-on-primary font-label-lg px-lg py-2 rounded-lg">Volver al Dashboard</a>
        <?php else: ?>

        <form method="POST" class="space-y-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
                <div>
                    <label for="titulo" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Título de tu Proyecto *</label>
                    <input type="text" id="titulo" name="titulo" required placeholder="Ej: Mi primer logo con Buscatrazos"
                        class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
                </div>
                <div>
                    <label for="categoria_id" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Programa Principal *</label>
                    <select id="categoria_id" name="categoria_id" required
                        class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="youtube_url" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Enlace de tu video en YouTube *</label>
                <div class="flex items-center gap-sm bg-surface-container-highest border border-outline-variant rounded-lg px-md py-2 focus-within:border-primary-container transition-all">
                    <span class="material-symbols-outlined text-error">smart_display</span>
                    <input type="url" id="youtube_url" name="youtube_url" required placeholder="https://www.youtube.com/watch?v=..."
                        class="w-full bg-transparent text-on-surface border-none focus:ring-0">
                </div>
                <p class="text-[11px] text-on-surface-variant mt-1">Asegúrate de que el video esté como Público o No Listado.</p>
            </div>

            <div>
                <label for="descripcion_corta" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Descripción del Proceso *</label>
                <textarea id="descripcion_corta" name="descripcion_corta" rows="3" required placeholder="Explica brevemente los pasos que seguiste y qué intentaste lograr..."
                    class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all"></textarea>
            </div>

            <div>
                <label class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">¿Qué herramientas aplicaste en tu video?</label>
                <div class="bg-surface-container-highest border border-outline-variant rounded-lg p-md max-h-48 overflow-y-auto custom-scrollbar grid grid-cols-1 md:grid-cols-2 gap-2">
                    <?php if(empty($tutoriales_disponibles)): ?>
                        <p class="text-on-surface-variant text-sm italic col-span-2">No hay herramientas registradas.</p>
                    <?php else: ?>
                        <?php foreach($tutoriales_disponibles as $tut): ?>
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-surface-variant p-2 rounded transition-colors">
                                <input type="checkbox" name="herramientas[]" value="<?php echo $tut['id']; ?>" class="rounded border-outline-variant text-primary focus:ring-primary bg-surface-container">
                                <span class="text-on-surface text-sm"><?php echo htmlspecialchars($tut['titulo']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center gap-md pt-md">
                <button type="submit" class="bg-primary-container text-on-primary font-label-lg text-label-lg px-xl py-3 rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md">
                    Enviar para Revisión
                </button>
                <a href="dashboard.php" class="text-on-surface-variant font-label-lg hover:text-on-surface transition-colors">Cancelar</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
