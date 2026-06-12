<?php
session_start();
require_once '../config/database.php';

// Protección: Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'administrador') {
    header('Location: ../auth/login.php');
    exit;
}

$estudiante_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success = '';
$error = '';

if ($estudiante_id <= 0) {
    header('Location: matriculas.php');
    exit;
}

try {
    // Obtener datos del estudiante
    $stmt = $pdo->prepare("SELECT id, nombre, email FROM usuarios WHERE id = ? AND rol = 'estudiante'");
    $stmt->execute([$estudiante_id]);
    $estudiante = $stmt->fetch();

    if (!$estudiante) {
        header('Location: matriculas.php');
        exit;
    }

    // Procesar formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cursos_seleccionados = isset($_POST['cursos']) ? $_POST['cursos'] : [];

        $pdo->beginTransaction();

        // Eliminar matrículas actuales
        $stmt_del = $pdo->prepare("DELETE FROM matriculas WHERE usuario_id = ?");
        $stmt_del->execute([$estudiante_id]);

        // Insertar nuevas matrículas
        if (!empty($cursos_seleccionados)) {
            $stmt_ins = $pdo->prepare("INSERT INTO matriculas (usuario_id, categoria_id) VALUES (?, ?)");
            foreach ($cursos_seleccionados as $cat_id) {
                $stmt_ins->execute([$estudiante_id, (int)$cat_id]);
            }
        }

        $pdo->commit();
        $success = 'Matrículas actualizadas correctamente.';
    }

    // Obtener todos los cursos (categorías)
    $stmt_cat = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
    $categorias = $stmt_cat->fetchAll();

    // Obtener cursos matriculados actualmente
    $stmt_mat = $pdo->prepare("SELECT categoria_id FROM matriculas WHERE usuario_id = ?");
    $stmt_mat->execute([$estudiante_id]);
    $matriculas_actuales = $stmt_mat->fetchAll(PDO::FETCH_COLUMN);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $error = "Error de base de datos: " . $e->getMessage();
    $categorias = [];
    $matriculas_actuales = [];
}

$page_title = "Gestionar Matrícula";
$header_title = "Admin Dashboard";

include '../includes/header.php';
?>

<div class="max-w-3xl mx-auto space-y-lg">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-lg">
        <a href="index.php" class="hover:text-primary transition-colors">Admin</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <a href="matriculas.php" class="hover:text-primary transition-colors">Matrículas</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface">Gestionar Accesos</span>
    </nav>

    <?php if ($success): ?>
        <div class="bg-primary-container/20 text-primary-container p-md rounded-lg mb-lg flex items-center gap-md border border-primary-container/30">
            <span class="material-symbols-outlined">check_circle</span>
            <p class="font-body-sm"><?php echo $success; ?></p>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-error-container text-on-error-container p-md rounded-lg mb-lg flex items-center gap-md border border-error">
            <span class="material-symbols-outlined">error</span>
            <p class="font-body-sm"><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-surface-container rounded-xl p-xl border border-outline-variant shadow-lg">
        <div class="flex items-center gap-md mb-xl pb-md border-b border-outline-variant">
            <div class="w-16 h-16 rounded-full bg-surface-container-highest flex items-center justify-center border border-outline-variant text-3xl text-on-surface-variant">
                <span class="material-symbols-outlined">person</span>
            </div>
            <div>
                <h2 class="font-headline-xl text-on-surface"><?php echo htmlspecialchars($estudiante['nombre']); ?></h2>
                <p class="font-body-md text-on-surface-variant"><?php echo htmlspecialchars($estudiante['email']); ?></p>
            </div>
        </div>

        <form method="POST">
            <h3 class="font-headline-md text-primary mb-md">Cursos con Acceso Autorizado</h3>
            <p class="font-body-sm text-on-surface-variant mb-lg">Selecciona las casillas de los cursos a los que este estudiante puede acceder. Los cursos no seleccionados estarán ocultos para él.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-md mb-xl">
                <?php foreach ($categorias as $cat): ?>
                    <?php $checked = in_array($cat['id'], $matriculas_actuales) ? 'checked' : ''; ?>
                    <label class="flex items-center gap-3 cursor-pointer bg-surface-container-low hover:bg-surface-container-highest p-4 rounded-lg border border-outline-variant transition-colors <?php echo $checked ? 'border-primary-container shadow-[0_0_10px_rgba(255,154,0,0.1)]' : ''; ?>">
                        <input type="checkbox" name="cursos[]" value="<?php echo $cat['id']; ?>" <?php echo $checked; ?> 
                            class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary bg-surface-container-highest">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-on-surface-variant" style="font-variation-settings: 'FILL' 1;">
                                <?php echo htmlspecialchars($cat['icono']); ?>
                            </span>
                            <span class="font-label-lg text-on-surface"><?php echo htmlspecialchars($cat['nombre']); ?></span>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="flex items-center gap-md">
                <button type="submit" class="bg-primary-container text-on-primary font-label-lg px-xl py-3 rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md">
                    Guardar Matrícula
                </button>
                <a href="matriculas.php" class="text-on-surface-variant font-label-lg hover:text-on-surface transition-colors">Volver</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
