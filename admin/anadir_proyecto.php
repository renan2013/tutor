<?php
session_start();
require_once '../config/database.php';

// Protección: Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header('Location: ../auth/login.php');
    exit;
}

$error = '';
$success = '';

// Obtener categorías para el select
try {
    $stmt = $pdo->query("SELECT * FROM categorias");
    $categorias = $stmt->fetchAll();
    
    // Obtener herramientas (tutoriales) para vincular
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
    $contenido_html = $_POST['contenido_html'];
    $dificultad = $_POST['dificultad'];
    $tiempo_estimado = (int)$_POST['tiempo_estimado'];
    $estado = $_POST['estado'];
    $herramientas_seleccionadas = isset($_POST['herramientas']) ? $_POST['herramientas'] : [];

    if (empty($titulo) || empty($descripcion_corta)) {
        $error = 'Por favor, completa los campos obligatorios.';
    } else {
        try {
            $pdo->beginTransaction();

            // Insertar Proyecto
            $stmt = $pdo->prepare("
                INSERT INTO proyectos (categoria_id, titulo, descripcion_corta, contenido_html, dificultad, tiempo_estimado, autor_id, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $categoria_id, 
                $titulo, 
                $descripcion_corta, 
                $contenido_html, 
                $dificultad,
                $tiempo_estimado,
                $_SESSION['usuario_id'], 
                $estado
            ]);
            
            $proyecto_id = $pdo->lastInsertId();

            // Insertar Herramientas Vinculadas
            if (!empty($herramientas_seleccionadas)) {
                $stmt_herr = $pdo->prepare("INSERT INTO proyecto_herramientas (proyecto_id, tutorial_id) VALUES (?, ?)");
                foreach ($herramientas_seleccionadas as $tut_id) {
                    $stmt_herr->execute([$proyecto_id, (int)$tut_id]);
                }
            }

            $pdo->commit();
            $success = 'Proyecto creado y vinculado correctamente.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error al guardar el proyecto: ' . $e->getMessage();
        }
    }
}

$page_title = "Añadir Proyecto";
$header_title = "Nuevo Proyecto Práctico";

include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-lg">
        <a href="index.php" class="hover:text-primary transition-colors">Admin</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <a href="proyectos.php" class="hover:text-primary transition-colors">Proyectos</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface">Añadir Proyecto</span>
    </nav>

    <div class="bg-surface-container rounded-xl p-xl border border-outline-variant shadow-lg">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xl">Crear Nuevo Proyecto Práctico</h2>

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
        <?php endif; ?>

        <form method="POST" class="space-y-lg">
            <!-- Bloque 1: Información General -->
            <div class="bg-surface-container-low p-lg rounded-lg border border-outline-variant space-y-md">
                <h3 class="font-headline-md text-primary border-b border-outline-variant pb-2">Información General</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
                    <div>
                        <label for="titulo" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Título del Proyecto *</label>
                        <input type="text" id="titulo" name="titulo" required placeholder="Ej: Logo Minimalista con Pluma"
                            class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
                    </div>
                    <div>
                        <label for="categoria_id" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Programa / Categoría *</label>
                        <select id="categoria_id" name="categoria_id" required
                            class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="descripcion_corta" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Breve Descripción *</label>
                    <textarea id="descripcion_corta" name="descripcion_corta" rows="2" required placeholder="Describe el objetivo final del proyecto..."
                        class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all"></textarea>
                </div>
            </div>

            <!-- Bloque 2: Ficha Técnica -->
            <div class="bg-surface-container-low p-lg rounded-lg border border-outline-variant space-y-md">
                <h3 class="font-headline-md text-primary border-b border-outline-variant pb-2">Ficha Técnica</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
                    <div>
                        <label for="dificultad" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Dificultad</label>
                        <select id="dificultad" name="dificultad" class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
                            <option value="principiante">Principiante 🟢</option>
                            <option value="intermedio">Intermedio 🟡</option>
                            <option value="avanzado">Avanzado 🔴</option>
                        </select>
                    </div>
                    <div>
                        <label for="tiempo_estimado" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Tiempo (Minutos)</label>
                        <input type="number" id="tiempo_estimado" name="tiempo_estimado" value="30" min="5" max="300"
                            class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
                    </div>
                    <div>
                        <label for="estado" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Estado</label>
                        <select id="estado" name="estado" class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
                            <option value="publicado">Publicado</option>
                            <option value="borrador">Borrador</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-md">
                    <label class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Herramientas Necesarias (Vincula tutoriales existentes)</label>
                    <div class="bg-surface-container-highest border border-outline-variant rounded-lg p-md max-h-48 overflow-y-auto custom-scrollbar grid grid-cols-1 md:grid-cols-2 gap-2">
                        <?php if(empty($tutoriales_disponibles)): ?>
                            <p class="text-on-surface-variant text-sm italic col-span-2">No hay herramientas publicadas aún.</p>
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
            </div>

            <!-- Bloque 3: Contenido HTML y Video -->
            <div class="bg-surface-container-low p-lg rounded-lg border border-outline-variant space-y-md">
                <div class="flex justify-between items-center border-b border-outline-variant pb-2">
                    <h3 class="font-headline-md text-primary">Paso a Paso y Video (HTML)</h3>
                </div>
                
                <div class="bg-[#1e1e1e] p-sm rounded border border-outline-variant mb-sm">
                    <p class="text-xs text-on-surface-variant font-bold mb-1">💡 Snippet para Video de YouTube (Copiar y pegar abajo, cambia el ID):</p>
                    <code class="text-[10px] text-tertiary-container break-all select-all block">
&lt;div class="aspect-video w-full rounded-xl overflow-hidden shadow-lg border border-outline-variant my-lg"&gt;
    &lt;iframe class="w-full h-full" src="https://www.youtube.com/embed/TU_ID_AQUI" title="Video" frameborder="0" allowfullscreen&gt;&lt;/iframe&gt;
&lt;/div&gt;
                    </code>
                </div>

                <textarea id="contenido_html" name="contenido_html" rows="12" placeholder="Pega el código del video y los pasos de diseño aquí..."
                    class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all font-mono text-sm"></textarea>
            </div>

            <div class="flex items-center gap-md pt-md">
                <button type="submit" class="bg-primary-container text-on-primary font-label-lg text-label-lg px-xl py-3 rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md">
                    Crear Proyecto
                </button>
                <a href="proyectos.php" class="text-on-surface-variant font-label-lg hover:text-on-surface transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
