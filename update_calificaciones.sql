-- ACTUALIZACIÓN DE BASE DE DATOS: EVALUACIÓN DE PROYECTOS
-- Importa este archivo en phpMyAdmin para actualizar la tabla de proyectos.

-- 1. Modificar el estado para incluir 'revision' y 'rechazado'
ALTER TABLE proyectos 
MODIFY COLUMN estado ENUM('revision', 'publicado', 'rechazado', 'borrador') DEFAULT 'revision';

-- 2. Añadir columnas de calificación y feedback del tutor
ALTER TABLE proyectos 
ADD COLUMN calificacion INT NULL AFTER tiempo_estimado,
ADD COLUMN feedback_tutor TEXT NULL AFTER calificacion;
