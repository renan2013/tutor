<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo isset($page_title) ? $page_title . " - Learn Design" : "Learn Design"; ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&amp;family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
          tailwind.config = {
            darkMode: "class",
            theme: {
              extend: {
                "colors": {
                        "secondary-container": "#474747",
                        "on-error": "#690005",
                        "on-primary-fixed": "#2c1600",
                        "tertiary-fixed": "#c0e8ff",
                        "tertiary-container": "#00c0fa",
                        "outline-variant": "#544434",
                        "primary": "#ffc183",
                        "surface-bright": "#393939",
                        "secondary-fixed-dim": "#c8c6c6",
                        "inverse-primary": "#8a5100",
                        "on-primary-container": "#653a00",
                        "on-background": "#e5e2e1",
                        "outline": "#a28d7a",
                        "on-secondary": "#303030",
                        "primary-fixed": "#ffdcbd",
                        "secondary-fixed": "#e4e2e1",
                        "background": "#131313",
                        "error-container": "#93000a",
                        "surface-container-lowest": "#0e0e0e",
                        "primary-container": "#ff9a00",
                        "on-secondary-fixed": "#1b1c1c",
                        "tertiary-fixed-dim": "#71d2ff",
                        "on-surface": "#e5e2e1",
                        "inverse-on-surface": "#313030",
                        "surface-container": "#20201f",
                        "surface-variant": "#353535",
                        "on-tertiary-container": "#004a63",
                        "surface": "#131313",
                        "on-secondary-fixed-variant": "#474747",
                        "error": "#ffb4ab",
                        "surface-container-low": "#1c1b1b",
                        "on-primary-fixed-variant": "#693c00",
                        "on-tertiary-fixed-variant": "#004d66",
                        "on-surface-variant": "#dac2ad",
                        "tertiary": "#89d8ff",
                        "surface-tint": "#ffb86d",
                        "on-tertiary-fixed": "#001e2b",
                        "surface-dim": "#131313",
                        "on-secondary-container": "#b6b5b4",
                        "on-primary": "#492900",
                        "on-tertiary": "#003547",
                        "secondary": "#c8c6c6",
                        "surface-container-highest": "#353535",
                        "surface-container-high": "#2a2a2a",
                        "inverse-surface": "#e5e2e1",
                        "on-error-container": "#ffdad6",
                        "primary-fixed-dim": "#ffb86d"
                },
                "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                },
                "spacing": {
                        "md": "16px",
                        "sm": "12px",
                        "lg": "24px",
                        "gutter": "16px",
                        "xl": "32px",
                        "xs": "4px",
                        "base": "8px",
                        "container-margin": "20px"
                },
                "fontFamily": {
                        "headline-lg": ["Hanken Grotesk"],
                        "label-caps": ["Inter"],
                        "label-md": ["Inter"],
                        "headline-xl": ["Hanken Grotesk"],
                        "body-lg": ["Inter"],
                        "headline-md": ["Hanken Grotesk"],
                        "body-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "label-lg": ["Inter"]
                },
                "fontSize": {
                        "headline-lg": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "label-caps": ["11px", {"lineHeight": "16px", "letterSpacing": "0.06em", "fontWeight": "700"}],
                        "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.04em", "fontWeight": "500"}],
                        "headline-xl": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "label-lg": ["14px", {"lineHeight": "20px", "letterSpacing": "0.02em", "fontWeight": "600"}]
                }
              },
            },
          }
        </script>
    <style>
        body {
            background-color: #121212;
            color: #e5e2e1;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .tool-card-gradient {
            background: linear-gradient(135deg, #1E1E1E 0%, #161616 100%);
        }
        .active-tool-stroke {
            box-shadow: inset 0 0 0 1px #ff9a00;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #131313;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #474747;
            border-radius: 10px;
        }
        body {
          min-height: max(884px, 100dvh);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen custom-scrollbar overflow-x-hidden">
    <?php if(isset($_SESSION['usuario_id'])): ?>
    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-[60] hidden backdrop-blur-sm transition-opacity duration-300"></div>

    <!-- Sidebar Drawer -->
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-surface-container-highest border-r border-outline-variant z-[70] transform -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">
        <div class="p-gutter border-b border-outline-variant flex items-center justify-between h-16">
            <div class="flex items-center gap-2">
                <img src="/tutor/assets/imgs/logo_learning.png" alt="Logo" class="h-8 object-contain">
            </div>
            <button id="close-sidebar" class="text-on-surface-variant hover:bg-surface-variant p-2 rounded-full transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <nav class="flex flex-col p-4 gap-2 overflow-y-auto custom-scrollbar flex-grow">
            <a href="/tutor/index.php" class="flex items-center gap-md p-4 rounded-xl text-on-surface hover:bg-surface-variant transition-all group">
                <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">home</span>
                <span class="font-label-lg">Learning</span>
            </a>
            <a href="/tutor/views/lista_herramientas.php" class="flex items-center gap-md p-4 rounded-xl text-on-surface hover:bg-surface-variant transition-all group">
                <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">architecture</span>
                <span class="font-label-lg">Tutoriales</span>
            </a>
            <a href="/tutor/views/asistente_ia.php" class="flex items-center gap-md p-4 rounded-xl text-on-surface hover:bg-surface-variant transition-all group">
                <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">psychology</span>
                <span class="font-label-lg">IA Assistant</span>
            </a>
            <a href="/tutor/estudiante/dashboard.php" class="flex items-center gap-md p-4 rounded-xl text-on-surface hover:bg-surface-variant transition-all group">
                <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">star</span>
                <span class="font-label-lg">Mi Progreso</span>
            </a>
            
            <div class="mt-auto pt-4 border-t border-outline-variant">
                <?php if($_SESSION['usuario_rol'] === 'administrador'): ?>
                    <a href="/tutor/admin/index.php" class="flex items-center gap-md p-4 rounded-xl text-primary-container hover:bg-primary-container/10 transition-all">
                        <span class="material-symbols-outlined">admin_panel_settings</span>
                        <span class="font-label-lg">Panel Admin</span>
                    </a>
                <?php endif; ?>
                <a href="/tutor/auth/logout.php" class="flex items-center gap-md p-4 rounded-xl text-error hover:bg-error/10 transition-all">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-label-lg">Cerrar Sesión</span>
                </a>
            </div>
        </nav>
    </aside>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('menu-btn');
            const closeBtn = document.getElementById('close-sidebar');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            }

            if (menuBtn) menuBtn.addEventListener('click', toggleSidebar);
            if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>

    <!-- TopAppBar -->
    <header class="bg-surface-container-highest border-b border-outline-variant shadow-sm fixed top-0 w-full z-50 flex justify-between items-center px-gutter h-16">
        <div class="flex items-center gap-md">
            <button id="menu-btn" class="text-on-surface-variant hover:bg-surface-variant transition-colors duration-200 active:scale-95 p-2 rounded-full">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <a href="/tutor/index.php" class="flex items-center gap-2">
                <img src="/tutor/assets/imgs/logo_learning.png" alt="Learn Design Logo" class="h-8 md:h-10 object-contain">
            </a>
        </div>
        <div class="flex items-center gap-sm">
            <span class="text-on-surface-variant font-label-md mr-2 hidden md:block"><?php echo explode(' ', $_SESSION['usuario_nombre'])[0]; ?></span>
            <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold text-sm">
                <?php echo substr($_SESSION['usuario_nombre'], 0, 1); ?>
            </div>
        </div>
    </header>
    <?php endif; ?>
    <main class="<?php echo isset($_SESSION['usuario_id']) ? 'mt-16' : 'mt-8'; ?> mb-8 flex-grow px-gutter py-lg max-w-4xl mx-auto w-full">
