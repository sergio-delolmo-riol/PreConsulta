-- Script para agregar asignaciones de consultas a celadores
USE centro_triaje_digital;

-- Obtener el ID del celador José
SET @celador_jose = (SELECT C.id_celador FROM Celador C JOIN Usuario U ON C.id_celador = U.id_usuario WHERE U.email = 'jose.celador@hospital.com');

-- Limpiar datos previos de prueba si existen
DELETE FROM Asignacion_Celador WHERE id_celador = @celador_jose;

-- Crear episodios de prueba adicionales
INSERT INTO Episodio_Urgencia (id_paciente, fecha_llegada, motivo_consulta, estado, prioridad_actual)
VALUES 
(1, DATE_SUB(NOW(), INTERVAL 30 MINUTE), 'Dolor agudo en el pecho y dificultad para respirar, especialmente al realizar esfuerzos mínimos como caminar. Los síntomas comenzaron hace aproximadamente tres horas.', 'en_triaje', 2),
(2, DATE_SUB(NOW(), INTERVAL 1 HOUR), 'Revisión de resultados de análisis de sangre. Control rutinario programado.', 'espera_triaje', 4),
(3, DATE_SUB(NOW(), INTERVAL 2 HOUR), 'Consulta de seguimiento por hipertensión. Necesito renovar mi medicación habitual.', 'espera_triaje', 5);

SET @ep1 = LAST_INSERT_ID();
SET @ep2 = @ep1 + 1;
SET @ep3 = @ep1 + 2;

-- Asignar al celador José
INSERT INTO Asignacion_Celador (id_celador, id_episodio, fecha_asignacion, estado)
VALUES 
(@celador_jose, @ep1, DATE_SUB(NOW(), INTERVAL 25 MINUTE), 'pendiente'),
(@celador_jose, @ep2, DATE_SUB(NOW(), INTERVAL 55 MINUTE), 'pendiente'),
(@celador_jose, @ep3, DATE_SUB(NOW(), INTERVAL 115 MINUTE), 'pendiente');

SELECT 'Asignaciones creadas correctamente' as resultado;
