-- ============================================
-- MIGRACIÓN: Agregar campos personales a Usuario
-- Fecha: 2025-11-20
-- Descripción: Mueve fecha_nacimiento, direccion y condiciones_medicas
--              de la tabla Paciente a la tabla Usuario
-- ============================================

USE centro_triaje_digital;

-- Paso 1: Agregar nuevas columnas a la tabla Usuario
ALTER TABLE Usuario 
ADD COLUMN fecha_nacimiento DATE NULL AFTER telefono,
ADD COLUMN direccion VARCHAR(255) NULL AFTER fecha_nacimiento,
ADD COLUMN condiciones_medicas TEXT NULL AFTER direccion;

-- Paso 2: Migrar datos existentes de Paciente a Usuario
UPDATE Usuario u
INNER JOIN Paciente p ON u.id_usuario = p.id_paciente
SET 
    u.fecha_nacimiento = p.fecha_nacimiento,
    u.direccion = p.direccion,
    u.condiciones_medicas = p.condiciones_medicas
WHERE p.fecha_nacimiento IS NOT NULL 
   OR p.direccion IS NOT NULL 
   OR p.condiciones_medicas IS NOT NULL;

-- Paso 3: Eliminar las columnas de la tabla Paciente (OPCIONAL - comentado por seguridad)
-- ALTER TABLE Paciente 
-- DROP COLUMN fecha_nacimiento,
-- DROP COLUMN direccion,
-- DROP COLUMN condiciones_medicas;

-- Verificación: Mostrar usuarios con sus nuevos datos
SELECT 
    id_usuario,
    nombre,
    apellidos,
    email,
    fecha_nacimiento,
    direccion,
    condiciones_medicas
FROM Usuario
ORDER BY id_usuario;
