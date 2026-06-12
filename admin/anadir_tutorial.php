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
} catch (Exception $e) {
    $categorias = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $categoria_id = (int)$_POST['categoria_id'];
    $contenido_html = $_POST['contenido_html'];
    $estado = $_POST['estado'];
    $v24_5 = isset($_POST['v24_5']) ? 1 : 0;

    if (empty($titulo) || empty($descripcion) || empty($contenido_html)) {
        $error = 'Por favor, completa los campos obligatorios.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO tutoriales (categoria_id, titulo, descripcion, contenido_html, autor_id, estado, v24_5_compatible) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $categoria_id, 
                $titulo, 
                $descripcion, 
                $contenido_html, 
                $_SESSION['usuario_id'], 
                $estado, 
                $v24_5
            ]);
            $success = 'Tutorial creado correctamente.';
        } catch (Exception $e) {
            $error = 'Error al guardar el tutorial: ' . $e->getMessage();
        }
    }
}

$page_title = "Añadir Tutorial";
$header_title = "Nuevo Contenido";

include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-lg">
        <a href="index.php" class="hover:text-primary transition-colors">Admin</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface">Añadir Tutorial</span>
    </nav>

    <div class="bg-surface-container rounded-xl p-xl border border-outline-variant shadow-lg">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xl">Crear Nuevo Tutorial</h2>

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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
                <div>
                    <label for="titulo" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Título del Tutorial *</label>
                    <input type="text" id="titulo" name="titulo" required placeholder="Ej: Herramienta Buscatrazos"
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
                <label for="descripcion" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Breve Descripción *</label>
                <textarea id="descripcion" name="descripcion" rows="2" required placeholder="Explica brevemente qué aprenderá el estudiante..."
                    class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all"></textarea>
            </div>

            <div>
                <label for="contenido_html" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Contenido HTML (Diseño Rico) *</label>
                <textarea id="contenido_html" name="contenido_html" rows="15" required placeholder="Pega aquí el código HTML con las clases de Tailwind..."
                    class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all font-mono text-sm"></textarea>
                <p class="text-[11px] text-on-surface-variant mt-2 italic">Puedes usar secciones con la clase `tool-card-gradient` y `rounded-xl` para mantener el estilo.</p>
            </div>

            <div class="flex flex-wrap items-center gap-xl bg-surface-container-low p-md rounded-lg border border-outline-variant">
                <div>
                    <label for="estado" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Estado</label>
                    <select id="estado" name="estado" class="bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-2 focus:outline-none">
                        <option value="publicado">Publicado</option>
                        <option value="pendiente">Borrador / Pendiente</option>
                    </select>
                </div>
                <div class="flex items-center gap-md pt-6">
                    <input type="checkbox" id="v24_5" name="v24_5" checked class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary bg-surface-container-highest">
                    <label for="v24_5" class="font-label-lg text-label-lg text-on-surface">Compatible con v24.5+</label>
                </div>
            </div>

            <div class="flex items-center gap-md pt-xl">
                <button type="submit" class="bg-primary-container text-on-primary font-label-lg text-label-lg px-xl py-3 rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md">
                    Guardar Tutorial
                </button>
                <a href="index.php" class="text-on-surface-variant font-label-lg hover:text-on-surface transition-colors">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
