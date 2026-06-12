-- SCRIPT DE INSTALACIÓN SEGURA PARA HOSTINGER
-- Ejecuta todo este contenido en la pestaña SQL de phpMyAdmin.
-- Si las tablas no existen, las creará. Si existen, las actualizará de forma segura.

-- 1. Asegurar que la tabla de categorías existe
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    icono VARCHAR(50),
    color_hex VARCHAR(7)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Asegurar que la tabla de proyectos existe con su estructura base
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion_corta TEXT,
    contenido_html LONGTEXT,
    dificultad ENUM('principiante', 'intermedio', 'avanzado') DEFAULT 'principiante',
    tiempo_estimado INT,
    imagen_portada VARCHAR(255),
    autor_id INT NOT NULL,
    estado ENUM('revision', 'publicado', 'rechazado', 'borrador') DEFAULT 'revision',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Actualización Segura: Asegurar que las columnas de calificación existan
-- Nota: Si phpMyAdmin da una advertencia aquí diciendo que la columna ya existe, puedes ignorarla.
ALTER TABLE proyectos ADD COLUMN IF NOT EXISTS calificacion INT NULL AFTER tiempo_estimado;
ALTER TABLE proyectos ADD COLUMN IF NOT EXISTS feedback_tutor TEXT NULL AFTER calificacion;

-- 4. Asegurar que la tabla intermedia exista
CREATE TABLE IF NOT EXISTS proyecto_herramientas (
    proyecto_id INT NOT NULL,
    tutorial_id INT NOT NULL,
    PRIMARY KEY (proyecto_id, tutorial_id),
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (tutorial_id) REFERENCES tutoriales(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
