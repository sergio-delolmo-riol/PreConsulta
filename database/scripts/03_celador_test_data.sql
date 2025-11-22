-- Script para agregar datos de prueba para celadores
-- Proyecto: PreConsulta - Centro de Triaje Digital

USE centro_triaje_digital;

-- Insertar celador de prueba
INSERT INTO Usuario (nombre, apellidos, dni, email, telefono, password, fecha_registro, estado)
VALUES 
('José', 'Celador González', '77788899Z', 'jose.celador@hospital.com', '666777888', '$2y$10$Y0UutTMxHwMddhR67LtSjO6HNvm5EIm5EpJkDcaqzUyNMkuVXmW5y', NOW(), 'activo');

SET @celador_id = LAST_INSERT_ID();

INSERT INTO Celador (id_celador, turno, estado, area_asignada)
VALUES (@celador_id, 'mañana', 'activo', 'Urgencias General');

-- Crear algunos episodios de urgencia con consultas asignadas al celador
INSERT INTO Episodio_Urgencia (id_paciente, fecha_llegada, motivo_consulta, estado, prioridad_actual)
VALUES 
-- Paciente 1 - Juan Pérez
(1, DATE_SUB(NOW(), INTERVAL 30 MINUTE), 'Dolor agudo en el pecho y dificultad para respirar, especialmente al realizar esfuerzos mínimos como caminar. Los síntomas comenzaron hace aproximadamente tres horas.', 'en_triaje', 2),

-- Paciente 2 - María García  
(2, DATE_SUB(NOW(), INTERVAL 1 HOUR), 'Revisión de resultados de análisis de sangre. Control rutinario programado.', 'espera_triaje', 4),

-- Paciente 3 - Carlos López
(3, DATE_SUB(NOW(), INTERVAL 2 HOUR), 'Consulta de seguimiento por hipertensión. Necesito renovar mi medicación habitual.', 'espera_triaje', 5);

-- Obtener los IDs de los episodios recién creados
SET @episodio1 = LAST_INSERT_ID();
SET @episodio2 = @episodio1 + 1;
SET @episodio3 = @episodio1 + 2;

-- Asignar consultas al celador
INSERT INTO Asignacion_Celador (id_celador, id_episodio, fecha_asignacion, estado)
VALUES 
(@celador_id, @episodio1, DATE_SUB(NOW(), INTERVAL 25 MINUTE), 'pendiente'),
(@celador_id, @episodio2, DATE_SUB(NOW(), INTERVAL 55 MINUTE), 'pendiente'),
(@celador_id, @episodio3, DATE_SUB(NOW(), INTERVAL 115 MINUTE), 'pendiente');

-- Confirmación
SELECT 'Datos de prueba insertados correctamente' as resultado;
SELECT CONCAT('Celador ID: ', @celador_id) as info;
SELECT CONCAT('Email: jose.celador@hospital.com, Password: password123') as credenciales;
