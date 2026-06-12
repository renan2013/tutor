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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $icono = trim($_POST['icono']);
    $color_hex = trim($_POST['color_hex']);

    // Validación básica de color hex
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $color_hex)) {
        $error = 'El color debe estar en formato HEX válido (ej. #FF9A00).';
    } elseif (empty($nombre) || empty($icono)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categorias (nombre, icono, color_hex) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $icono, $color_hex]);
            $success = 'Categoría creada correctamente.';
        } catch (Exception $e) {
            $error = 'Error al crear la categoría. Es posible que el nombre ya exista.';
        }
    }
}

$page_title = "Nueva Categoría";
$header_title = "Admin Dashboard";

include '../includes/header.php';
?>

<div class="max-w-2xl mx-auto space-y-lg">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-lg">
        <a href="index.php" class="hover:text-primary transition-colors">Admin</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <a href="categorias.php" class="hover:text-primary transition-colors">Categorías</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface">Añadir</span>
    </nav>

    <div class="bg-surface-container rounded-xl p-xl border border-outline-variant shadow-lg">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-md">Crear Nueva Categoría (Curso)</h2>
        <p class="font-body-md text-on-surface-variant mb-xl">Define el nombre, el icono de Google Material Symbols y el color representativo del nuevo programa de diseño.</p>

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
            <div>
                <label for="nombre" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Nombre de la Categoría *</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej: Adobe Premiere"
                    class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
                <div>
                    <label for="icono" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Nombre del Icono *</label>
                    <div class="flex items-center gap-sm bg-surface-container-highest border border-outline-variant rounded-lg px-md py-2 focus-within:border-primary-container transition-all">
                        <span class="material-symbols-outlined text-on-surface-variant">search</span>
                        <input type="text" id="icono" name="icono" required placeholder="Ej: edit, palette, movie"
                            class="w-full bg-transparent text-on-surface border-none focus:ring-0">
                    </div>
                    <p class="text-[11px] text-on-surface-variant mt-1">Busca nombres en <a href="https://fonts.google.com/icons" target="_blank" class="text-primary hover:underline">Google Material Symbols</a>.</p>
                </div>
                <div>
                    <label for="color_hex" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Color Hexadecimal *</label>
                    <div class="flex items-center gap-sm bg-surface-container-highest border border-outline-variant rounded-lg px-sm py-1.5 focus-within:border-primary-container transition-all">
                        <input type="color" id="color_picker" class="w-8 h-8 rounded cursor-pointer bg-transparent border-none p-0" onchange="document.getElementById('color_hex').value = this.value">
                        <input type="text" id="color_hex" name="color_hex" required placeholder="#FFFFFF" pattern="^#[a-fA-F0-9]{6}$"
                            class="w-full bg-transparent text-on-surface border-none focus:ring-0 font-mono text-sm uppercase" onkeyup="document.getElementById('color_picker').value = this.value">
                    </div>
                    <p class="text-[11px] text-on-surface-variant mt-1">Ejemplo: #FF9A00 para Naranja (Illustrator).</p>
                </div>
            </div>

            <div class="flex items-center gap-md pt-md border-t border-outline-variant">
                <button type="submit" class="bg-primary-container text-on-primary font-label-lg px-xl py-3 rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md">
                    Guardar Categoría
                </button>
                <a href="categorias.php" class="text-on-surface-variant font-label-lg hover:text-on-surface transition-colors">Volver</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Sincronización inicial del color picker
    document.addEventListener('DOMContentLoaded', () => {
        const hexInput = document.getElementById('color_hex');
        const picker = document.getElementById('color_picker');
        if(hexInput.value && /^#[a-fA-F0-9]{6}$/i.test(hexInput.value)) {
            picker.value = hexInput.value;
        }
    });
</script>

<?php include '../includes/footer.php'; ?>