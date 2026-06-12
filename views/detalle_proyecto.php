<?php
session_start();
require_once '../config/database.php';

$proyecto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($proyecto_id <= 0) {
    header('Location: /tutor/index.php');
    exit;
}

try {
    // Obtener detalles del proyecto y su autor
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre, u.nombre as autor_nombre 
        FROM proyectos p 
        JOIN categorias c ON p.categoria_id = c.id 
        JOIN usuarios u ON p.autor_id = u.id
        WHERE p.id = ?
    ");
    $stmt->execute([$proyecto_id]);
    $proyecto = $stmt->fetch();

    if (!$proyecto) {
        header('Location: /tutor/index.php');
        exit;
    }

    // Obtener herramientas vinculadas (tutoriales)
    $stmt_herr = $pdo->prepare("
        SELECT t.id, t.titulo, t.descripcion 
        FROM proyecto_herramientas ph
        JOIN tutoriales t ON ph.tutorial_id = t.id
        WHERE ph.proyecto_id = ?
    ");
    $stmt_herr->execute([$proyecto_id]);
    $herramientas = $stmt_herr->fetchAll();

} catch (Exception $e) {
    header('Location: /tutor/index.php');
    exit;
}

// Lógica para procesar un nuevo comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario']) && isset($_SESSION['usuario_id'])) {
    $nuevo_comentario = trim($_POST['comentario']);
    if (!empty($nuevo_comentario)) {
        try {
            $stmt_com = $pdo->prepare("INSERT INTO proyecto_comentarios (proyecto_id, usuario_id, comentario) VALUES (?, ?, ?)");
            $stmt_com->execute([$proyecto_id, $_SESSION['usuario_id'], $nuevo_comentario]);
            // Redirigir para evitar reenvío del formulario
            header("Location: detalle_proyecto.php?id=" . $proyecto_id);
            exit;
        } catch (Exception $e) {
            $error_comentario = "No se pudo publicar el comentario.";
        }
    }
}

// Obtener los comentarios del proyecto
try {
    $stmt_comentarios = $pdo->prepare("
        SELECT pc.*, u.nombre as autor_comentario 
        FROM proyecto_comentarios pc
        JOIN usuarios u ON pc.usuario_id = u.id
        WHERE pc.proyecto_id = ?
        ORDER BY pc.fecha_creacion DESC
    ");
    $stmt_comentarios->execute([$proyecto_id]);
    $comentarios = $stmt_comentarios->fetchAll();
} catch (Exception $e) {
    $comentarios = [];
}

// Si no está publicado, solo el autor o un administrador pueden verlo
$puede_ver = false;
if (in_array($proyecto['estado'], ['publicado', 'revision'])) {
    $puede_ver = true;
} elseif (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['usuario_rol'] === 'administrador' || $_SESSION['usuario_id'] == $proyecto['autor_id']) {
        $puede_ver = true;
    }
}

if (!$puede_ver) {
    // Podrías redirigir a una página de "Acceso Denegado"
    header('Location: /tutor/index.php');
    exit;
}

$page_title = $proyecto['titulo'];
$header_title = "Proyecto Estudiantil";

include '../includes/header.php';
?>

<div class="space-y-lg max-w-5xl mx-auto">
    <nav class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-lg">
        <a href="/tutor/index.php" class="hover:text-primary transition-colors">Inicio</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <a href="/tutor/views/lista_herramientas.php?id=<?php echo $proyecto['categoria_id']; ?>" class="hover:text-primary transition-colors"><?php echo $proyecto['categoria_nombre']; ?></a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface">Proyecto</span>
    </nav>

    <!-- Alerta si está en revisión -->
    <?php if ($proyecto['estado'] === 'revision'): ?>
        <div class="bg-surface-container-low border border-orange-500/50 p-md rounded-lg flex items-center gap-md">
            <span class="material-symbols-outlined text-orange-400">pending_actions</span>
            <p class="font-body-sm text-on-surface-variant">Este proyecto es reciente y actualmente se encuentra <strong class="text-orange-400 uppercase">Pendiente de Calificación</strong> por parte del tutor.</p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-xl">
        <!-- Columna Izquierda: Video y Descripción -->
        <div class="lg:col-span-2 space-y-lg">
            <div class="bg-surface-container rounded-xl p-lg border border-outline-variant shadow-lg">
                <div class="flex items-center gap-sm mb-md">
                    <div class="w-10 h-10 rounded-full bg-surface-container-highest flex items-center justify-center border border-outline-variant">
                        <span class="material-symbols-outlined text-xl text-on-surface-variant">person</span>
                    </div>
                    <div>
                        <h2 class="font-label-lg text-on-surface">Creado por <?php echo htmlspecialchars($proyecto['autor_nombre']); ?></h2>
                        <span class="text-[11px] text-on-surface-variant bg-secondary-container px-2 py-0.5 rounded uppercase font-bold"><?php echo $proyecto['categoria_nombre']; ?></span>
                    </div>
                </div>
                
                <h1 class="font-headline-xl text-primary mb-sm"><?php echo htmlspecialchars($proyecto['titulo']); ?></h1>
                <p class="font-body-lg text-on-surface-variant mb-xl leading-relaxed"><?php echo nl2br(htmlspecialchars($proyecto['descripcion_corta'])); ?></p>
                
                <div class="mt-lg">
                    <!-- Aquí se renderiza el iframe -->
                    <?php 
                        $html_video = $proyecto['contenido_html'];
                        if (strpos($html_video, 'allowfullscreen') === false) {
                            $html_video = str_replace('<iframe ', '<iframe allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" ', $html_video);
                        }
                        echo $html_video; 
                    ?>
                </div>
            </div>

            <!-- Sección de Comentarios -->
            <div class="bg-surface-container rounded-xl p-lg border border-outline-variant shadow-lg mt-lg">
                <h3 class="font-headline-md text-on-surface mb-md flex items-center gap-2 border-b border-outline-variant pb-2">
                    <span class="material-symbols-outlined text-primary">forum</span> Feedback de Compañeros
                </h3>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <form method="POST" class="mb-lg">
                        <div class="flex gap-md items-start">
                            <div class="w-10 h-10 rounded-full bg-primary-container/20 flex-shrink-0 flex items-center justify-center border border-primary-container/30">
                                <span class="material-symbols-outlined text-xl text-primary-container">person</span>
                            </div>
                            <div class="flex-grow">
                                <textarea name="comentario" rows="2" required placeholder="Añade un comentario, observación o consejo constructivo..." class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all text-sm mb-2"></textarea>
                                <?php if(isset($error_comentario)): ?>
                                    <p class="text-error text-xs mb-2"><?php echo $error_comentario; ?></p>
                                <?php endif; ?>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-primary-container text-on-primary font-label-md px-4 py-2 rounded hover:brightness-110 active:scale-95 transition-all shadow-md">
                                        Publicar Comentario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="bg-surface-container-low border border-outline-variant p-md rounded-lg text-center mb-lg">
                        <p class="text-on-surface-variant text-sm"><a href="/tutor/auth/login.php" class="text-primary hover:underline">Inicia sesión</a> para dejar un comentario.</p>
                    </div>
                <?php endif; ?>

                <div class="space-y-md">
                    <?php if(empty($comentarios)): ?>
                        <p class="text-on-surface-variant text-sm italic text-center py-md">Aún no hay comentarios. ¡Sé el primero en dar tu opinión!</p>
                    <?php else: ?>
                        <?php foreach($comentarios as $com): ?>
                            <div class="flex gap-md items-start bg-surface-container-low p-md rounded-lg border border-outline-variant">
                                <div class="w-8 h-8 rounded-full bg-surface-container-highest flex-shrink-0 flex items-center justify-center border border-outline-variant">
                                    <span class="material-symbols-outlined text-sm text-on-surface-variant">person</span>
                                </div>
                                <div>
                                    <div class="flex items-baseline gap-2 mb-1">
                                        <span class="font-label-md text-on-surface"><?php echo htmlspecialchars($com['autor_comentario']); ?></span>
                                        <span class="text-[10px] text-on-surface-variant"><?php echo date('d M Y, H:i', strtotime($com['fecha_creacion'])); ?></span>
                                    </div>
                                    <p class="font-body-sm text-on-surface-variant leading-relaxed"><?php echo nl2br(htmlspecialchars($com['comentario'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Detalles Técnicos y Feedback -->
        <div class="space-y-lg">
            <!-- Ficha Técnica -->
            <div class="bg-surface-container-low rounded-xl p-lg border border-outline-variant shadow-lg">
                <h3 class="font-headline-md text-on-surface mb-md border-b border-outline-variant pb-2">Especificaciones</h3>
                
                <ul class="space-y-md mb-lg">
                    <li class="flex items-center justify-between">
                        <span class="text-on-surface-variant text-sm flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">trending_up</span> Nivel:</span>
                        <span class="font-label-md text-on-surface uppercase text-xs">
                            <?php 
                                if($proyecto['dificultad'] == 'principiante') echo 'Principiante 🟢';
                                if($proyecto['dificultad'] == 'intermedio') echo 'Intermedio 🟡';
                                if($proyecto['dificultad'] == 'avanzado') echo 'Avanzado 🔴';
                            ?>
                        </span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-on-surface-variant text-sm flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">schedule</span> Duración:</span>
                        <span class="font-label-md text-on-surface text-xs"><?php echo $proyecto['tiempo_estimado']; ?> min</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-on-surface-variant text-sm flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">calendar_today</span> Publicado:</span>
                        <span class="font-label-md text-on-surface text-xs"><?php echo date('d M Y', strtotime($proyecto['fecha_creacion'])); ?></span>
                    </li>
                </ul>

                <?php if ($proyecto['estado'] === 'publicado' && $proyecto['calificacion'] !== null): ?>
                    <div class="mt-md bg-surface-container-highest p-md rounded-lg border border-primary/20 text-center">
                        <p class="text-xs text-on-surface-variant uppercase tracking-widest mb-1">Calificación del Tutor</p>
                        <div class="text-3xl font-headline-xl text-primary font-bold"><?php echo $proyecto['calificacion']; ?><span class="text-sm text-on-surface-variant font-normal">/100</span></div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Feedback del Tutor -->
            <?php if ($proyecto['feedback_tutor']): ?>
                <div class="bg-primary-container/5 rounded-xl p-lg border border-primary-container/30 shadow-lg relative">
                    <span class="absolute -top-3 left-4 bg-surface px-2 text-xs font-bold text-primary-container uppercase tracking-widest flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">reviews</span> Feedback del Tutor</span>
                    <p class="font-body-sm text-on-surface-variant italic leading-relaxed mt-2">"<?php echo nl2br(htmlspecialchars($proyecto['feedback_tutor'])); ?>"</p>
                </div>
            <?php endif; ?>

            <!-- Herramientas Utilizadas -->
            <?php if(!empty($herramientas)): ?>
            <div class="bg-surface-container-low rounded-xl p-lg border border-outline-variant shadow-lg">
                <h3 class="font-headline-md text-on-surface mb-md border-b border-outline-variant pb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">build</span> Herramientas
                </h3>
                <p class="text-xs text-on-surface-variant mb-4">Para realizar este proyecto, se aplicaron las siguientes herramientas:</p>
                
                <div class="space-y-3">
                    <?php foreach($herramientas as $h): ?>
                        <a href="/tutor/views/detalle_herramienta.php?id=<?php echo $h['id']; ?>" class="block group">
                            <div class="bg-surface-container-highest border border-outline-variant p-3 rounded-lg hover:border-primary-container transition-colors">
                                <h4 class="font-label-md text-on-surface group-hover:text-primary transition-colors flex items-center justify-between">
                                    <?php echo htmlspecialchars($h['titulo']); ?>
                                    <span class="material-symbols-outlined text-[14px] text-on-surface-variant group-hover:text-primary group-hover:translate-x-1 transition-all">arrow_forward</span>
                                </h4>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
