<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /auth/login.php');
    exit;
}

$page_title = "Inicio - Aprende Diseño";
$header_title = "Creative Suite";

// Obtener categorías de la base de datos
try {
    $stmt = $pdo->query("SELECT * FROM categorias");
    $categorias = $stmt->fetchAll();
} catch (Exception $e) {
    $categorias = [];
}

include 'includes/header.php';
?>

<div class="space-y-lg">
    <section class="text-center md:text-left mb-xl">
        <h2 class="font-headline-xl text-headline-xl mb-sm text-on-surface">Bienvenido a tu Academia de Diseño</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Elige un programa para comenzar tu camino creativo o consulta a nuestro asistente IA.</p>
    </section>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
        <?php foreach ($categorias as $cat): ?>
            <a href="/views/lista_herramientas.php?id=<?php echo $cat['id']; ?>" class="group">
                <div class="bg-surface-container rounded-xl p-lg border border-outline-variant hover:border-primary-container transition-all duration-300 transform group-hover:-translate-y-1 shadow-lg">
                    <div class="w-16 h-16 rounded-xl mb-md flex items-center justify-center" style="background-color: <?php echo $cat['color_hex']; ?>20; border: 1px solid <?php echo $cat['color_hex']; ?>40;">
                        <span class="material-symbols-outlined text-4xl" style="color: <?php echo $cat['color_hex']; ?>; font-variation-settings: 'FILL' 1;"><?php echo $cat['icono']; ?></span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-xs"><?php echo $cat['nombre']; ?></h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Explora herramientas y técnicas maestras.</p>
                    <div class="mt-lg flex items-center text-primary font-label-lg text-label-lg group-hover:gap-2 transition-all">
                        Empezar <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- AI Promo Section -->
    <section class="tool-card-gradient rounded-xl p-xl mt-xl border border-primary-container/30 shadow-lg flex flex-col md:flex-row items-center gap-xl">
        <div class="w-24 h-24 bg-surface-container rounded-full flex items-center justify-center border border-primary-container">
            <span class="material-symbols-outlined text-5xl text-primary-container">psychology</span>
        </div>
        <div class="flex-grow text-center md:text-left">
            <h3 class="font-headline-lg text-headline-lg text-primary mb-xs">¿Tienes dudas técnicas?</h3>
            <p class="font-body-md text-body-md text-on-surface-variant mb-md">Nuestro asistente IA está entrenado en Adobe Creative Suite para ayudarte en tiempo real.</p>
            <a href="/views/asistente_ia.php" class="inline-block bg-primary-container text-on-primary font-label-lg text-label-lg px-xl py-3 rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md">
                Consultar Asistente
            </a>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
