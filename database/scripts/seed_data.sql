-- ============================================
--   DATOS DE PRUEBA - CENTRO DE TRIAJE DIGITAL
--   Proyecto: PreConsulta
--   Fecha: 18/11/2025
-- ============================================

USE centro_triaje_digital;

-- ============================================
-- 1. DATOS DE PRIORIDAD (PRIMERO, SON FUNDAMENTALES)
-- ============================================

INSERT INTO Prioridad (tipo_prioridad, nombre, descripcion, color_hex, tiempo_max_atencion) VALUES
('alta', 'Emergencia', 'Situación de riesgo vital inmediato', '#FF0000', 0),
('alta', 'Muy Urgente', 'Situación crítica que requiere atención inmediata', '#FF4500', 10),
('media', 'Urgente', 'Requiere atención en un tiempo razonable', '#FFA500', 30),
('media', 'Menos Urgente', 'Puede esperar sin riesgo grave', '#FFD700', 60),
('baja', 'No Urgente', 'Consulta que puede programarse', '#90EE90', 120);

-- ============================================
-- 2. BOXES
-- ============================================

INSERT INTO Box (nombre, ubicacion, estado, capacidad, equipamiento) VALUES
('Box 1', 'Planta Baja - Ala Norte', 'libre', 1, 'Monitor cardíaco, desfibrilador, toma de oxígeno'),
('Box 2', 'Planta Baja - Ala Norte', 'libre', 1, 'Monitor cardíaco, toma de oxígeno'),
('Box 3', 'Planta Baja - Ala Sur', 'libre', 1, 'Equipamiento básico'),
('Box 4', 'Planta Baja - Ala Sur', 'limpieza', 1, 'Equipamiento básico'),
('Box 5', 'Primera Planta', 'libre', 2, 'Monitor cardíaco, respirador, toma de oxígeno'),
('Sala Reanimación', 'Planta Baja - Zona Crítica', 'libre', 1, 'Equipamiento completo de UCI');

-- ============================================
-- 3. USUARIOS BASE
-- ============================================

-- Contraseña de todos los usuarios de prueba: "PreConsulta2024!" 
-- Hash generado con password_hash() en PHP: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

-- PACIENTES
INSERT INTO Usuario (nombre, apellidos, dni, email, telefono, password, estado) VALUES
('Juan', 'Torres Mena', '12345678A', 'juan.torres@email.com', '698244712', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
('María', 'García López', '23456789B', 'maria.garcia@email.com', '612345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
('Carlos', 'Rodríguez Sánchez', '34567890C', 'carlos.rodriguez@email.com', '623456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
('Ana', 'Martínez Pérez', '45678901D', 'ana.martinez@email.com', '634567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
('Pedro', 'López Fernández', '56789012E', 'pedro.lopez@email.com', '645678901', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo');

-- ENFERMEROS/MÉDICOS
INSERT INTO Usuario (nombre, apellidos, dni, email, telefono, password, estado) VALUES
('Laura', 'Sánchez Ruiz', '67890123F', 'laura.sanchez@hospital.com', '656789012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
('Miguel', 'Fernández Castro', '78901234G', 'miguel.fernandez@hospital.com', '667890123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
('Carmen', 'Jiménez Moreno', '89012345H', 'carmen.jimenez@hospital.com', '678901234', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
('David', 'Romero Ortiz', '90123456I', 'david.romero@hospital.com', '689012345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo');

-- CELADORES
INSERT INTO Usuario (nombre, apellidos, dni, email, telefono, password, estado) VALUES
('Antonio', 'Navarro Gil', '01234567J', 'antonio.navarro@hospital.com', '690123456', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
('Rosa', 'Vázquez Blanco', '12345670K', 'rosa.vazquez@hospital.com', '601234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo'),
('Francisco', 'Molina Santos', '23456701L', 'francisco.molina@hospital.com', '612345670', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo');

-- ============================================
-- 4. DATOS ESPECÍFICOS DE PACIENTES
-- ============================================

INSERT INTO Paciente (id_paciente, fecha_nacimiento, direccion, seguro_medico, contacto_familiar, telefono_emergencia, alergias, condiciones_medicas, grupo_sanguineo) VALUES
(1, '1985-03-15', 'Calle Principal 123, Madrid', 'Seguro Nacional', 'María Torres (Esposa)', '698244713', 'Penicilina', 'Hipertensión', 'O+'),
(2, '1990-07-22', 'Avenida Central 45, Madrid', 'Sanitas', 'Pedro García (Hermano)', '612345679', NULL, NULL, 'A+'),
(3, '1978-11-08', 'Plaza Mayor 12, Madrid', 'Adeslas', 'Laura Rodríguez (Esposa)', '623456788', 'Polen', 'Diabetes tipo 2', 'B+'),
(4, '1995-02-28', 'Calle Luna 67, Madrid', 'Seguro Nacional', 'José Martínez (Padre)', '634567891', NULL, 'Asma', 'AB+'),
(5, '1982-09-14', 'Avenida Sol 89, Madrid', 'DKV', 'Carmen López (Esposa)', '645678902', 'Lactosa', NULL, 'O-');

-- ============================================
-- 5. DATOS DE ENFERMEROS
-- ============================================

INSERT INTO Enfermero (id_enfermero, numero_colegiado, especialidad, turno_actual, disponible) VALUES
(6, 'ENF-28001', 'Urgencias', 'mañana', TRUE),
(7, 'MED-28002', 'Medicina de Urgencias', 'mañana', TRUE),
(8, 'ENF-28003', 'Triaje', 'tarde', TRUE),
(9, 'MED-28004', 'Traumatología', 'noche', FALSE);

-- ============================================
-- 6. DATOS DE CELADORES
-- ============================================

INSERT INTO Celador (id_celador, area_asignada, turno, estado) VALUES
(10, 'Urgencias - Planta Baja', 'mañana', 'activo'),
(11, 'Urgencias - Primera Planta', 'tarde', 'activo'),
(12, 'Admisión y Traslados', 'rotativo', 'ocupado');

-- ============================================
-- 7. EPISODIOS DE URGENCIA (EJEMPLOS)
-- ============================================

-- Episodio 1: Juan Torres - En espera de triaje
INSERT INTO Episodio_Urgencia (id_paciente, fecha_llegada, estado, motivo_consulta, prioridad_actual) VALUES
(1, NOW() - INTERVAL 15 MINUTE, 'espera_triaje', 'Dolor torácico', 2);

-- Episodio 2: María García - En triaje
INSERT INTO Episodio_Urgencia (id_paciente, fecha_llegada, estado, motivo_consulta, prioridad_actual, tiempo_estimado_espera) VALUES
(2, NOW() - INTERVAL 30 MINUTE, 'en_triaje', 'Fiebre alta y dolor de cabeza', 3, 25);

-- Episodio 3: Carlos Rodríguez - Esperando atención con prioridad media
INSERT INTO Episodio_Urgencia (id_paciente, fecha_llegada, estado, motivo_consulta, prioridad_actual, tiempo_estimado_espera, box_asignado) VALUES
(3, NOW() - INTERVAL 45 MINUTE, 'espera_atencion', 'Fractura en brazo derecho', 3, 15, 1);

-- Episodio 4: Ana Martínez - En atención en Box 2
INSERT INTO Episodio_Urgencia (id_paciente, fecha_llegada, estado, motivo_consulta, prioridad_actual, box_asignado) VALUES
(4, NOW() - INTERVAL 1 HOUR, 'en_atencion', 'Crisis asmática', 2, 2);

-- ============================================
-- 8. REGISTROS DE TRIAJE
-- ============================================

-- Triaje para María García
INSERT INTO Triaje (id_episodio, id_enfermero, prioridad_asignada, nivel_consciencia, presion_arterial, frecuencia_cardiaca, temperatura, saturacion_oxigeno, sintomas_texto, observaciones) VALUES
(2, 6, 3, 'Alerta', '130/85', 88, 38.5, 97, 'Paciente refiere fiebre de 38.5°C desde hace 24h, dolor de cabeza intenso, fotofobia leve', 'Posible proceso vírico, descartar meningitis');

-- Triaje para Carlos Rodríguez
INSERT INTO Triaje (id_episodio, id_enfermero, prioridad_asignada, nivel_consciencia, presion_arterial, frecuencia_cardiaca, temperatura, saturacion_oxigeno, sintomas_texto, observaciones) VALUES
(3, 8, 3, 'Alerta', '120/80', 75, 36.8, 98, 'Caída accidental, dolor e inflamación en brazo derecho, posible fractura de radio', 'Solicitar radiografía urgente');

-- Triaje para Ana Martínez
INSERT INTO Triaje (id_episodio, id_enfermero, prioridad_asignada, nivel_consciencia, presion_arterial, frecuencia_cardiaca, temperatura, saturacion_oxigeno, sintomas_texto, observaciones) VALUES
(4, 6, 2, 'Alerta', '140/90', 110, 37.2, 89, 'Dificultad respiratoria severa, sibilancias, antecedentes de asma', 'Crisis asmática moderada-severa, administrar salbutamol inmediato');

-- ============================================
-- 9. ASIGNACIONES DE CELADOR
-- ============================================

-- Traslado de Carlos a Box 1
INSERT INTO Asignacion_Celador (id_celador, id_episodio, tipo_tarea, ubicacion_origen, ubicacion_destino, fecha_inicio, estado) VALUES
(10, 3, 'traslado', 'Sala de espera', 'Box 1', NOW() - INTERVAL 40 MINUTE, 'finalizado');

-- Traslado pendiente de Ana a Box 2
INSERT INTO Asignacion_Celador (id_celador, id_episodio, tipo_tarea, ubicacion_origen, ubicacion_destino, estado) VALUES
(11, 4, 'traslado', 'Sala de triaje', 'Box 2', 'pendiente');

-- ============================================
-- 10. ATENCIONES MÉDICAS
-- ============================================

-- Atención a Ana Martínez (en curso)
INSERT INTO Atencion_Medica (id_episodio, id_enfermero, fecha_inicio, tipo_atencion, diagnostico, tratamiento) VALUES
(4, 7, NOW() - INTERVAL 15 MINUTE, 'tratamiento', 'Crisis asmática moderada', 'Salbutamol nebulizado 2.5mg, oxigenoterapia 3L/min, corticoides IV');

-- ============================================
-- 11. NOTIFICACIONES
-- ============================================

-- Notificación para Juan Torres (espera)
INSERT INTO Notificacion (id_usuario, id_episodio, tipo, titulo, mensaje) VALUES
(1, 1, 'estado', 'Bienvenido a Urgencias', 'Su consulta ha sido registrada. Por favor, aguarde a ser llamado para el triaje inicial.'),
(1, 1, 'turno', 'Posición en cola', 'Actualmente hay 2 personas delante de usted. Tiempo estimado de espera: 15 minutos');

-- Notificación para María García (en triaje)
INSERT INTO Notificacion (id_usuario, id_episodio, tipo, titulo, mensaje, leida, fecha_lectura) VALUES
(2, 2, 'estado', 'Triaje completado', 'Su triaje ha sido completado. Prioridad asignada: URGENTE (Nivel 3)', TRUE, NOW() - INTERVAL 5 MINUTE);

-- Notificación para celador sobre traslado pendiente
INSERT INTO Notificacion (id_usuario, tipo, titulo, mensaje) VALUES
(11, 'turno', 'Nueva asignación', 'Traslado pendiente: Paciente Ana Martínez a Box 2');

-- ============================================
-- 12. LOG DE ACCIONES
-- ============================================

INSERT INTO Log_Acciones (id_usuario, id_episodio, accion, detalles, ip_address) VALUES
(1, 1, 'login', 'Acceso desde app móvil', '192.168.1.100'),
(1, 1, 'registro_episodio', 'Paciente registra llegada a urgencias', '192.168.1.100'),
(6, 2, 'login', 'Inicio de sesión enfermera triaje', '192.168.1.50'),
(6, 2, 'triaje_completado', 'Triaje completado, prioridad media asignada', '192.168.1.50'),
(10, 3, 'traslado_iniciado', 'Inicio traslado paciente a Box 1', '192.168.1.75'),
(10, 3, 'traslado_finalizado', 'Paciente ubicado en Box 1', '192.168.1.75');

-- ============================================
-- 13. HISTORIAL CLÍNICO (EJEMPLOS ANTIGUOS)
-- ============================================

-- Historial de Juan Torres
INSERT INTO Historial_Clinico (id_paciente, id_enfermero, fecha_hora, tipo_consulta, descripcion, diagnostico, tratamiento, medicacion_prescrita) VALUES
(1, 7, NOW() - INTERVAL 6 MONTH, 'urgencia', 'Dolor torácico atípico, palpitaciones', 'Taquicardia sinusal, ansiedad', 'Técnicas de relajación, seguimiento cardiología', 'Alprazolam 0.5mg si ansiedad'),
(1, 7, NOW() - INTERVAL 3 MONTH, 'consulta', 'Revisión cardiológica', 'Hipertensión arterial grado 1', 'Modificación estilo de vida, dieta hiposódica', 'Enalapril 10mg/día');

-- Historial de Carlos Rodríguez
INSERT INTO Historial_Clinico (id_paciente, id_enfermero, fecha_hora, tipo_consulta, descripcion, diagnostico, tratamiento, medicacion_prescrita) VALUES
(3, 7, NOW() - INTERVAL 1 YEAR, 'urgencia', 'Esguince tobillo izquierdo', 'Esguince grado II ligamento lateral', 'Reposo relativo, crioterapia, vendaje compresivo', 'Ibuprofeno 600mg/8h, 7 días');

-- ============================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ============================================

-- Contar registros por tabla
SELECT 'Usuarios' as Tabla, COUNT(*) as Total FROM Usuario
UNION ALL SELECT 'Pacientes', COUNT(*) FROM Paciente
UNION ALL SELECT 'Enfermeros', COUNT(*) FROM Enfermero
UNION ALL SELECT 'Celadores', COUNT(*) FROM Celador
UNION ALL SELECT 'Prioridades', COUNT(*) FROM Prioridad
UNION ALL SELECT 'Boxes', COUNT(*) FROM Box
UNION ALL SELECT 'Episodios', COUNT(*) FROM Episodio_Urgencia
UNION ALL SELECT 'Triajes', COUNT(*) FROM Triaje
UNION ALL SELECT 'Asignaciones Celador', COUNT(*) FROM Asignacion_Celador
UNION ALL SELECT 'Atenciones Médicas', COUNT(*) FROM Atencion_Medica
UNION ALL SELECT 'Notificaciones', COUNT(*) FROM Notificacion
UNION ALL SELECT 'Log Acciones', COUNT(*) FROM Log_Acciones
UNION ALL SELECT 'Historial Clínico', COUNT(*) FROM Historial_Clinico;

-- ============================================
-- CONSULTAS DE PRUEBA ÚTILES
-- ============================================

-- Ver pacientes en espera
SELECT * FROM v_pacientes_espera;

-- Ver boxes disponibles
SELECT * FROM v_boxes_disponibles;

-- Ver celadores disponibles
SELECT * FROM v_celadores_disponibles;

-- Ver episodios activos con detalles
SELECT 
    e.id_episodio,
    CONCAT(u.nombre, ' ', u.apellidos) as paciente,
    e.fecha_llegada,
    e.estado,
    p.nombre as prioridad,
    p.color_hex,
    b.nombre as box,
    TIMESTAMPDIFF(MINUTE, e.fecha_llegada, NOW()) as minutos_espera
FROM Episodio_Urgencia e
JOIN Paciente pac ON e.id_paciente = pac.id_paciente
JOIN Usuario u ON pac.id_paciente = u.id_usuario
LEFT JOIN Prioridad p ON e.prioridad_actual = p.id_prioridad
LEFT JOIN Box b ON e.box_asignado = b.id_box
WHERE e.estado != 'alta'
ORDER BY p.tipo_prioridad DESC, e.fecha_llegada ASC;
