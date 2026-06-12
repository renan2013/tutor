-- ACTUALIZACIÓN DE BASE DE DATOS: SISTEMA DE MATRICULACIÓN
-- Ejecuta este script en phpMyAdmin para crear la tabla de matrículas.

CREATE TABLE IF NOT EXISTS matriculas (
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    fecha_matricula TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, categoria_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
