-- ACTUALIZACIÓN DE BASE DE DATOS: SISTEMA DE COMENTARIOS ENTRE ESTUDIANTES
-- Ejecuta esto en phpMyAdmin para permitir el feedback de pares.

CREATE TABLE IF NOT EXISTS proyecto_comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proyecto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    comentario TEXT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
