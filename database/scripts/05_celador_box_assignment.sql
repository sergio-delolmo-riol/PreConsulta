-- ============================================
-- Script de migración: Agregar box asignado a Celador
-- Fecha: 2025-11-20
-- ============================================

USE centro_triaje_digital;

-- Agregar campo id_box a la tabla Celador
ALTER TABLE Celador
ADD COLUMN id_box INT NULL,
ADD COLUMN disponible ENUM('si','no') DEFAULT 'no',
ADD FOREIGN KEY (id_box) REFERENCES Box(id_box) ON DELETE SET NULL;

-- Crear algunos boxes si no existen
INSERT IGNORE INTO Box (nombre, ubicacion, estado, capacidad, equipamiento) VALUES
('Box 1', 'Planta Baja - Ala A', 'libre', 1, 'Monitor vital, camilla, desfibrilador'),
('Box 2', 'Planta Baja - Ala A', 'libre', 1, 'Monitor vital, camilla'),
('Box 3', 'Planta Baja - Ala B', 'libre', 1, 'Monitor vital, camilla, equipo de oxígeno'),
('Box 4', 'Planta Baja - Ala B', 'libre', 1, 'Monitor vital, camilla'),
('Box 5', 'Planta 1 - Ala A', 'libre', 1, 'Monitor vital, camilla, equipo de trauma');

-- Actualizar el celador existente para que esté inactivo sin box
UPDATE Celador SET disponible = 'no', id_box = NULL WHERE id_celador = 6;
