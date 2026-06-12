-- ACTUALIZACIÓN DE BASE DE DATOS: SISTEMA DE PROYECTOS
-- Este archivo contiene las nuevas tablas necesarias para el aprendizaje basado en proyectos.
-- Importa este archivo en phpMyAdmin para actualizar tu base de datos actual.

-- 1. Tabla de Proyectos Prácticos (Estilo Envato)
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion_corta TEXT,
    contenido_html LONGTEXT, -- Aquí se guardará el iframe de YouTube y el paso a paso
    dificultad ENUM('principiante', 'intermedio', 'avanzado') DEFAULT 'principiante',
    tiempo_estimado INT, -- Duración aproximada en minutos
    imagen_portada VARCHAR(255), -- URL de la imagen miniatura
    autor_id INT NOT NULL,
    estado ENUM('publicado', 'borrador') DEFAULT 'borrador',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla Intermedia: Herramientas utilizadas en cada Proyecto
-- Esto permite vincular proyectos con los tutoriales de herramientas existentes
CREATE TABLE IF NOT EXISTS proyecto_herramientas (
    proyecto_id INT NOT NULL,
    tutorial_id INT NOT NULL,
    PRIMARY KEY (proyecto_id, tutorial_id),
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (tutorial_id) REFERENCES tutoriales(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
