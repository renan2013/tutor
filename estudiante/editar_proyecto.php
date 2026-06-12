<?php
session_start();
require_once '../config/database.php';

// Protección
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$proyecto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$usuario_id = $_SESSION['usuario_id'];
$error = '';
$success = '';

if ($proyecto_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Obtener datos del proyecto asegurando que pertenece al usuario
try {
    $stmt = $pdo->prepare("SELECT * FROM proyectos WHERE id = ? AND autor_id = ?");
    $stmt->execute([$proyecto_id, $usuario_id]);
    $proyecto = $stmt->fetch();

    if (!$proyecto) {
        header('Location: dashboard.php');
        exit;
    }

    // Extraer el iframe_code del contenido_html (solo el iframe)
    preg_match('/<iframe.*?<\/iframe>/i', $proyecto['contenido_html'], $iframe_match);
    $iframe_code_actual = isset($iframe_match[0]) ? $iframe_match[0] : '';

    // Obtener categorías y tutoriales para el select
    $stmt = $pdo->query("SELECT * FROM categorias");
    $categorias = $stmt->fetchAll();
    
    $stmt = $pdo->query("SELECT id, titulo, categoria_id FROM tutoriales WHERE estado = 'publicado' ORDER BY titulo");
    $tutoriales_disponibles = $stmt->fetchAll();

    // Obtener herramientas seleccionadas actualmente
    $stmt = $pdo->prepare("SELECT tutorial_id FROM proyecto_herramientas WHERE proyecto_id = ?");
    $stmt->execute([$proyecto_id]);
    $herramientas_actuales = $stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (Exception $e) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion_corta = trim($_POST['descripcion_corta']);
    $categoria_id = (int)$_POST['categoria_id'];
    $iframe_code = trim($_POST['iframe_code']);
    $herramientas_seleccionadas = isset($_POST['herramientas']) ? $_POST['herramientas'] : [];

    if (empty($titulo) || empty($descripcion_corta) || empty($iframe_code)) {
        $error = 'Por favor, completa todos los campos obligatorios, incluyendo el código iframe de tu video.';
    } elseif (strpos($iframe_code, '<iframe') === false) {
        $error = 'El código proporcionado no parece ser un iframe válido. Asegúrate de copiar el código de inserción (embed) correctamente.';
    } else {
        // Asegurar que el iframe permita pantalla completa
        if (strpos($iframe_code, 'allowfullscreen') === false) {
            $iframe_code = str_replace('<iframe ', '<iframe allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" ', $iframe_code);
        }

        // Envolver el iframe en un contenedor responsivo
        $contenido_html = '
<div class="aspect-video w-full rounded-xl overflow-hidden shadow-lg border border-outline-variant my-lg flex items-center justify-center bg-black">
    ' . $iframe_code . '
</div>';

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                UPDATE proyectos 
                SET categoria_id = ?, titulo = ?, descripcion_corta = ?, contenido_html = ?, estado = 'revision' 
                WHERE id = ? AND autor_id = ?
            ");
            $stmt->execute([
                $categoria_id, 
                $titulo, 
                $descripcion_corta, 
                $contenido_html, 
                $proyecto_id,
                $usuario_id
            ]);

            // Actualizar herramientas (eliminar e insertar)
            $stmt_del = $pdo->prepare("DELETE FROM proyecto_herramientas WHERE proyecto_id = ?");
            $stmt_del->execute([$proyecto_id]);

            if (!empty($herramientas_seleccionadas)) {
                $stmt_herr = $pdo->prepare("INSERT INTO proyecto_herramientas (proyecto_id, tutorial_id) VALUES (?, ?)");
                foreach ($herramientas_seleccionadas as $tut_id) {
                    $stmt_herr->execute([$proyecto_id, (int)$tut_id]);
                }
            }

            $pdo->commit();
            $success = '¡Tu proyecto ha sido actualizado y enviado nuevamente a revisión!';
            
            // Recargar datos
            $iframe_code_actual = $iframe_code;
            $proyecto['titulo'] = $titulo;
            $proyecto['descripcion_corta'] = $descripcion_corta;
            $proyecto['categoria_id'] = $categoria_id;
            $herramientas_actuales = $herramientas_seleccionadas;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error al actualizar el proyecto: ' . $e->getMessage();
        }
    }
}

$page_title = "Editar Proyecto";
$header_title = "Editar Envío";

include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-lg">
        <a href="dashboard.php" class="hover:text-primary transition-colors">Mi Progreso</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface">Editar Proyecto</span>
    </nav>

    <div class="bg-surface-container rounded-xl p-xl border border-outline-variant shadow-lg">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-sm">Editar Proyecto</h2>
        <p class="font-body-md text-on-surface-variant mb-xl">Modifica los datos de tu entrega. Al guardar, el proyecto volverá al estado "En Revisión".</p>

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
                    <input type="text" id="titulo" name="titulo" required value="<?php echo htmlspecialchars($proyecto['titulo']); ?>"
                        class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
                </div>
                <div>
                    <label for="categoria_id" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Programa Principal *</label>
                    <select id="categoria_id" name="categoria_id" required
                        class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $proyecto['categoria_id']) ? 'selected' : ''; ?>><?php echo $cat['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="iframe_code" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Código Iframe del Video (Drive/YouTube) *</label>
                <textarea id="iframe_code" name="iframe_code" rows="3" required
                    class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all font-mono text-xs"><?php echo htmlspecialchars($iframe_code_actual); ?></textarea>
                <p class="text-[11px] text-on-surface-variant mt-1">Copia y pega el código HTML "embed" o "insertar" de tu video.</p>
            </div>

            <div>
                <label for="descripcion_corta" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Descripción del Proceso *</label>
                <textarea id="descripcion_corta" name="descripcion_corta" rows="3" required
                    class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all"><?php echo htmlspecialchars($proyecto['descripcion_corta']); ?></textarea>
            </div>

            <div>
                <label class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">¿Qué herramientas aplicaste en tu video?</label>
                <div class="bg-surface-container-highest border border-outline-variant rounded-lg p-md max-h-48 overflow-y-auto custom-scrollbar grid grid-cols-1 md:grid-cols-2 gap-2">
                    <?php if(empty($tutoriales_disponibles)): ?>
                        <p class="text-on-surface-variant text-sm italic col-span-2">No hay herramientas registradas.</p>
                    <?php else: ?>
                        <?php foreach($tutoriales_disponibles as $tut): ?>
                            <?php $checked = in_array($tut['id'], $herramientas_actuales) ? 'checked' : ''; ?>
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-surface-variant p-2 rounded transition-colors">
                                <input type="checkbox" name="herramientas[]" value="<?php echo $tut['id']; ?>" <?php echo $checked; ?> class="rounded border-outline-variant text-primary focus:ring-primary bg-surface-container">
                                <span class="text-on-surface text-sm"><?php echo htmlspecialchars($tut['titulo']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center gap-md pt-md">
                <button type="submit" class="bg-primary-container text-on-primary font-label-lg text-label-lg px-xl py-3 rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md">
                    Guardar Cambios
                </button>
                <a href="dashboard.php" class="text-on-surface-variant font-label-lg hover:text-on-surface transition-colors">Cancelar</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>