-- Creación de la base de datos (Opcional, Hostinger suele crearla por ti)
-- CREATE DATABASE IF NOT EXISTS apps_diseno CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE apps_diseno;

-- 1. Tabla de Categorías (Programas)
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    icono VARCHAR(50), -- Nombre de la Material Symbol o ruta al SVG
    color_hex VARCHAR(7) -- Color distintivo del programa
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar categorías iniciales
INSERT INTO categorias (nombre, icono, color_hex) VALUES 
('Adobe Photoshop', 'photo_library', '#31A8FF'),
('Adobe Illustrator', 'architecture', '#FF9A00'),
('Adobe InDesign', 'auto_stories', '#FF3366');

-- 2. Tabla de Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('estudiante', 'administrador') DEFAULT 'estudiante',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabla de Tutoriales
CREATE TABLE IF NOT EXISTS tutoriales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    contenido_html LONGTEXT, -- Para guardar el código HTML del tutorial
    autor_id INT NOT NULL,
    estado ENUM('publicado', 'pendiente', 'rechazado') DEFAULT 'publicado',
    v24_5_compatible BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabla de Contribuciones (Aportes de estudiantes)
CREATE TABLE IF NOT EXISTS contribuciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tutorial_id INT, -- Si es una mejora a uno existente
    tipo ENUM('nuevo_tutorial', 'mejora') NOT NULL,
    titulo VARCHAR(255),
    contenido_sugerido TEXT,
    estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (tutorial_id) REFERENCES tutoriales(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabla de Historial de Chats con IA
CREATE TABLE IF NOT EXISTS chats_ia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    mensaje_usuario TEXT NOT NULL,
    respuesta_ia TEXT NOT NULL,
    contexto_programa VARCHAR(50), -- Ej: 'Illustrator'
    fecha_consulta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Insertar datos iniciales de prueba
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Admin Tutor', 'admin@tutor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador');

INSERT INTO tutoriales (categoria_id, titulo, descripcion, contenido_html, autor_id, estado, v24_5_compatible) VALUES 
(2, 'Herramienta Pluma (P)', 'La herramienta más poderosa para crear trazados vectoriales precisos y escalables.', '
<!-- Hero Section: Large Icon & Header -->
<section class="tool-card-gradient rounded-xl p-xl mb-lg border border-outline-variant shadow-lg flex flex-col md:flex-row items-center gap-xl">
<div class="w-32 h-32 md:w-48 md:h-48 bg-surface-container rounded-xl border border-primary-container/20 flex items-center justify-center relative overflow-hidden group">
<div class="absolute inset-0 bg-primary-container/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
<span class="material-symbols-outlined text-[80px] md:text-[120px] text-primary-container" style="font-variation-settings: \'FILL\' 1;">edit</span>
</div>
<div class="flex-grow text-center md:text-left">
<div class="flex items-center justify-center md:justify-start gap-sm mb-base">
<span class="bg-primary-container/10 text-primary-container border border-primary-container px-2 py-0.5 rounded text-label-md font-label-md">MASTER TOOL</span>
<span class="text-on-surface-variant font-label-md text-label-md">v24.5 Compatible</span>
</div>
<h2 class="font-headline-xl text-headline-xl mb-sm text-on-surface">The Pen Tool (P)</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mb-md leading-relaxed">The single most powerful instrument for creating precise, scalable vector paths. Master the art of bezier curves to unlock professional-grade illustration.</p>
<div class="flex flex-wrap gap-sm justify-center md:justify-start">
<button class="bg-primary-container text-on-primary font-label-lg text-label-lg px-xl py-3 rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md flex items-center gap-2">
<span class="material-symbols-outlined">play_circle</span>
                        Watch Demo
                    </button>
<button class="border-1.5 border-outline-variant text-on-surface font-label-lg text-label-lg px-xl py-3 rounded-lg hover:bg-surface-variant active:scale-95 transition-all border">
                        Practice Mode
                    </button>
</div>
</div>
</section>
<!-- Tool Mechanics -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-lg mb-lg">
<div class="bg-surface-container rounded-xl p-lg border border-outline-variant">
<h3 class="font-headline-md text-headline-md text-primary mb-md flex items-center gap-2">
<span class="material-symbols-outlined">psychology</span>
                    How it Works
                </h3>
<ul class="space-y-md">
<li class="flex gap-md">
<span class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center flex-shrink-0 font-bold text-on-surface">1</span>
<p class="font-body-md text-body-md text-on-surface-variant">Click to create <strong class="text-on-surface">Anchor Points</strong>. These define the start and end of path segments.</p>
</li>
<li class="flex gap-md">
<span class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center flex-shrink-0 font-bold text-on-surface">2</span>
<p class="font-body-md text-body-md text-on-surface-variant">Click and <strong class="text-on-surface">drag</strong> to create directional handles. These define the intensity and angle of the curve.</p>
</li>
<li class="flex gap-md">
<span class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center flex-shrink-0 font-bold text-on-surface">3</span>
<p class="font-body-md text-body-md text-on-surface-variant">Hold <strong class="text-on-surface">Alt/Opt</strong> to break handles, allowing for sharp corners between smooth curves.</p>
</li>
</ul>
</div>
<div class="bg-surface-container rounded-xl p-lg border border-outline-variant flex flex-col justify-between">
<div>
<h3 class="font-headline-md text-headline-md text-primary mb-md flex items-center gap-2">
<span class="material-symbols-outlined">star</span>
                        Pro Tip: Favoriting
                    </h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-xl">Keep the Pen Tool at your fingertips. Favoriting this tool adds it to your quick-access dashboard and tracks your mastery progress.</p>
</div>
<button class="w-full border-1.5 border-primary-container text-primary-container font-label-lg text-label-lg py-4 rounded-lg flex items-center justify-center gap-md hover:bg-primary-container hover:text-on-primary transition-all border active:scale-95 group">
<span class="material-symbols-outlined group-hover:scale-125 transition-transform">favorite</span>
                    Add to Favorites
                </button>
</div>
</section>
<!-- Before and After Visual Examples -->
<section class="bg-surface-container rounded-xl p-lg border border-outline-variant mb-lg overflow-hidden">
<h3 class="font-headline-md text-headline-md text-primary mb-lg">Visual Concept: Anchors vs. Curves</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-xl">
<div class="space-y-sm">
<p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Phase 1: Anchor Placement</p>
<div class="relative aspect-video bg-[#161616] rounded-lg border border-outline-variant overflow-hidden">
<img class="w-full h-full object-cover opacity-60" data-alt="A macro close-up view of a digital interface showing geometric anchor points connected by thin gray lines. The aesthetic is ultra-technical and clean, with a dark slate background. Subtle glowing blue squares mark the vertices, representing the skeletal structure of a vector path in a professional design application." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDSEfK0273w3EMROtzeSG4fbY6Yq8P9oAk-FPseNNS9Iss2iWRYCMhuv7WAFqy536RA6-3YS-RvwdeSNK1Kpy_IH8Wn3fU29dCp3SvWEhmo2LjOeOpLEmzFD_4vZAZCFCyMciortDJchEs09bH4pFgfGF2QBxGdV_oYJ003w_X09VfmCOoHNgC2aY4zqBMbxIKSXfNaJodQVyOF-IKSPY2Ow-MHP4hdITVAUP1SZW_8vHdnPU0hfCOmBqyt9jp__b_WL8Om3p1ruzE"/>
<div class="absolute inset-0 flex items-center justify-center">
<span class="bg-surface-container/80 backdrop-blur-md px-md py-base rounded-full border border-outline-variant font-label-md text-label-md">Skeletal Path</span>
</div>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant italic text-center">Linear connections before handles are applied.</p>
</div>
<div class="space-y-sm">
<p class="font-label-caps text-label-caps text-primary uppercase">Phase 2: Curved Mastery</p>
<div class="relative aspect-video bg-[#161616] rounded-lg border border-primary-container/30 overflow-hidden active-tool-stroke">
<img class="w-full h-full object-cover" data-alt="A sophisticated digital illustration showing smooth, flowing orange neon curves against a deep black background. The curves are perfectly mathematically balanced, representing the final result of using the Pen Tool. Minimalist handle indicators are visible as subtle design elements, highlighting the precision and professional quality of the vector output." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAwwsqHAXqb-qjppM6Lhbb-48fqD_hZlnc1tF-ssPIf4-Qc8TWbdSLkJVPMxUF0Zq07cK3z-uHxfeLDcwk4pNRdWujITGH-MmH3PtjKofFIGUMx5e0qXu8benQHPiD-L7xA80Yfn2wclVhQemE2ZMSHmu9P9NLLiAO6O0WvyBcg-aa46EAo75FmVv1Bvlraq-7xryQd67uecahQJAQ_v5fK3__5hqR3NFHNSxvWa7o89kAPlB7oP0zHcnDxr18F6sjNtrFizNvnEmE"/>
<div class="absolute inset-0 flex items-center justify-center">
<span class="bg-primary-container/80 backdrop-blur-md px-md py-base rounded-full border border-primary-container text-on-primary font-label-md text-label-md">Bezier Outcome</span>
</div>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant italic text-center">Refined handles creating a smooth fluid motion.</p>
</div>
</div>
</section>
<!-- Keyboard Shortcuts Section -->
<section class="bg-surface-container-high rounded-xl p-lg border border-outline-variant">
<h3 class="font-headline-md text-headline-md text-on-surface mb-md">Essential Key Controls</h3>
<div class="grid grid-cols-2 md:grid-cols-4 gap-md">
<div class="bg-[#2D2D2D] p-md rounded-lg border-b-2 border-black flex flex-col items-center">
<span class="font-headline-md text-headline-md text-primary">P</span>
<span class="font-label-md text-label-md text-on-surface-variant mt-xs">Select Pen</span>
</div>
<div class="bg-[#2D2D2D] p-md rounded-lg border-b-2 border-black flex flex-col items-center">
<span class="font-headline-md text-headline-md text-primary">Esc</span>
<span class="font-label-md text-label-md text-on-surface-variant mt-xs">End Path</span>
</div>
<div class="bg-[#2D2D2D] p-md rounded-lg border-b-2 border-black flex flex-col items-center">
<span class="font-headline-md text-headline-md text-primary">Cmd</span>
<span class="font-label-md text-label-md text-on-surface-variant mt-xs">Temp Direct</span>
</div>
<div class="bg-[#2D2D2D] p-md rounded-lg border-b-2 border-black flex flex-col items-center">
<span class="font-headline-md text-headline-md text-primary">Shift</span>
<span class="font-label-md text-label-md text-on-surface-variant mt-xs">Snap 45°</span>
</div>
</div>
</section>
', 1, 'publicado', 1);
