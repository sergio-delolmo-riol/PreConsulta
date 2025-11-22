-- ============================================
-- DATOS DE PRUEBA: Sistema de Enfermeros
-- Descripción: Datos iniciales para probar el portal de enfermeros
-- ============================================

USE centro_triaje_digital;

-- ============================================
-- 1. CREAR USUARIOS ENFERMEROS
-- ============================================

-- Enfermero de prueba 1: María González (General)
-- Password: enfermero123 (hash generado con password_hash('enfermero123', PASSWORD_DEFAULT))
INSERT IGNORE INTO Usuario (nombre, apellidos, dni, email, password, telefono, fecha_nacimiento)
VALUES 
('María', 'González López', '45678901B', 'maria.gonzalez@hospital.com', '$2y$10$vLMcHTf4iK.faNZjnCPBIu4bO/etaS8ciSQWcCMWyHTCqjaW1E7Ca', '612345678', '1985-03-15');

-- Obtener el ID (ya sea recién insertado o existente)
SET @id_enfermero1 = LAST_INSERT_ID();
SET @id_enfermero1 = IF(@id_enfermero1 = 0, (SELECT id_usuario FROM Usuario WHERE email = 'maria.gonzalez@hospital.com'), @id_enfermero1);

INSERT IGNORE INTO Enfermero (id_enfermero, numero_colegiado, especialidad, turno_actual, disponible, id_box)
VALUES (@id_enfermero1, 'ENF-2024-001', 'Enfermería General', 'mañana', 1, 1);

-- Enfermero de prueba 2: Carlos Martínez (Urgencias)
-- Password: enfermero123
INSERT IGNORE INTO Usuario (nombre, apellidos, dni, email, password, telefono, fecha_nacimiento)
VALUES 
('Carlos', 'Martínez Ruiz', '56789012C', 'carlos.martinez@hospital.com', '$2y$10$vLMcHTf4iK.faNZjnCPBIu4bO/etaS8ciSQWcCMWyHTCqjaW1E7Ca', '623456789', '1988-07-22');

-- Obtener el ID (ya sea recién insertado o existente)
SET @id_enfermero2 = LAST_INSERT_ID();
SET @id_enfermero2 = IF(@id_enfermero2 = 0, (SELECT id_usuario FROM Usuario WHERE email = 'carlos.martinez@hospital.com'), @id_enfermero2);

INSERT IGNORE INTO Enfermero (id_enfermero, numero_colegiado, especialidad, turno_actual, disponible, id_box)
VALUES (@id_enfermero2, 'ENF-2024-002', 'Urgencias', 'tarde', 1, 2);

-- Enfermero de prueba 3: Ana Fernández (Pediatría)
-- Password: enfermero123
INSERT IGNORE INTO Usuario (nombre, apellidos, dni, email, password, telefono, fecha_nacimiento)
VALUES 
('Ana', 'Fernández Sánchez', '67890123D', 'ana.fernandez@hospital.com', '$2y$10$vLMcHTf4iK.faNZjnCPBIu4bO/etaS8ciSQWcCMWyHTCqjaW1E7Ca', '634567890', '1990-11-10');

-- Obtener el ID (ya sea recién insertado o existente)
SET @id_enfermero3 = LAST_INSERT_ID();
SET @id_enfermero3 = IF(@id_enfermero3 = 0, (SELECT id_usuario FROM Usuario WHERE email = 'ana.fernandez@hospital.com'), @id_enfermero3);

INSERT IGNORE INTO Enfermero (id_enfermero, numero_colegiado, especialidad, turno_actual, disponible, id_box)
VALUES (@id_enfermero3, 'ENF-2024-003', 'Pediatría', 'mañana', 0, NULL);

-- ============================================
-- 2. ASIGNAR PACIENTES A ENFERMEROS (EJEMPLO)
-- ============================================

-- Obtener un episodio existente (asumiendo que hay episodios en el sistema)
-- Asignar episodio al enfermero María (estado: atendiendo)
INSERT INTO Asignacion_Enfermero (id_enfermero, id_episodio, fecha_asignacion, fecha_inicio_atencion, estado)
SELECT @id_enfermero1, id_episodio, NOW() - INTERVAL 30 MINUTE, NOW() - INTERVAL 20 MINUTE, 'atendiendo'
FROM Episodio_Urgencia
WHERE estado IN ('espera_atencion', 'en_atencion')
LIMIT 1;

-- ============================================
-- 3. CREAR RECETAS DE EJEMPLO
-- ============================================

-- Receta de Paracetamol
INSERT INTO Receta (
    id_episodio,
    id_enfermero,
    nombre_farmaco,
    principio_activo,
    dosis,
    via_administracion,
    frecuencia,
    duracion,
    indicaciones,
    estado
)
SELECT 
    eu.id_episodio,
    @id_enfermero1,
    'Paracetamol 500mg',
    'Paracetamol',
    '500mg (1 comprimido)',
    'oral',
    'Cada 8 horas',
    '5 días',
    'Tomar con alimento. No exceder la dosis recomendada.',
    'activa'
FROM Episodio_Urgencia eu
LIMIT 1;

-- Receta de Ibuprofeno
INSERT INTO Receta (
    id_episodio,
    id_enfermero,
    nombre_farmaco,
    principio_activo,
    dosis,
    via_administracion,
    frecuencia,
    duracion,
    indicaciones,
    estado
)
SELECT 
    eu.id_episodio,
    @id_enfermero2,
    'Ibuprofeno 400mg',
    'Ibuprofeno',
    '400mg (1 comprimido)',
    'oral',
    'Cada 6 horas según necesidad',
    '3 días',
    'Tomar después de las comidas para evitar molestias gástricas.',
    'activa'
FROM Episodio_Urgencia eu
LIMIT 1 OFFSET 1;

-- ============================================
-- 4. CREAR INFORMES MÉDICOS DE EJEMPLO
-- ============================================

-- Informe 1: Dolor abdominal
INSERT INTO Informe_Medico (
    id_episodio,
    id_enfermero,
    diagnostico_preliminar,
    tratamiento_aplicado,
    observaciones,
    evolucion,
    requiere_seguimiento,
    derivado_a
)
SELECT 
    eu.id_episodio,
    @id_enfermero1,
    'Gastroenteritis aguda leve',
    'Hidratación oral, reposo, dieta blanda',
    'Paciente presenta dolor abdominal difuso con náuseas. No presenta fiebre. Signos vitales estables.',
    'Mejoría progresiva tras hidratación y analgesia',
    TRUE,
    'Medicina Interna (si no mejora en 48h)'
FROM Episodio_Urgencia eu
LIMIT 1;

-- Informe 2: Traumatismo leve
INSERT INTO Informe_Medico (
    id_episodio,
    id_enfermero,
    diagnostico_preliminar,
    tratamiento_aplicado,
    observaciones,
    evolucion,
    requiere_seguimiento,
    derivado_a
)
SELECT 
    eu.id_episodio,
    @id_enfermero2,
    'Contusión en rodilla derecha',
    'Aplicación de hielo, vendaje compresivo, reposo relativo',
    'Traumatismo por caída mientras practicaba deporte. No signos de fractura a la exploración. Movilidad conservada.',
    'Dolor controlado con analgesia. Se recomienda reposo 48-72h',
    FALSE,
    NULL
FROM Episodio_Urgencia eu
LIMIT 1 OFFSET 1;

-- ============================================
-- 5. VERIFICACIÓN DE DATOS
-- ============================================

-- Mostrar enfermeros creados
SELECT 
    u.nombre,
    u.apellidos,
    u.email,
    e.numero_colegiado,
    e.especialidad,
    e.disponible,
    b.nombre as box_asignado
FROM Usuario u
INNER JOIN Enfermero e ON u.id_usuario = e.id_enfermero
LEFT JOIN Box b ON e.id_box = b.id_box
ORDER BY u.nombre;

-- Mostrar asignaciones activas
SELECT 
    u_enf.nombre as enfermero,
    u_pac.nombre as paciente,
    u_pac.dni,
    ae.estado,
    ae.fecha_asignacion,
    eu.motivo_consulta
FROM Asignacion_Enfermero ae
INNER JOIN Enfermero e ON ae.id_enfermero = e.id_enfermero
INNER JOIN Usuario u_enf ON e.id_enfermero = u_enf.id_usuario
INNER JOIN Episodio_Urgencia eu ON ae.id_episodio = eu.id_episodio
INNER JOIN Usuario u_pac ON eu.id_paciente = u_pac.id_usuario
WHERE ae.estado IN ('asignado', 'atendiendo')
ORDER BY ae.fecha_asignacion DESC;

-- Contar recetas e informes
SELECT 
    (SELECT COUNT(*) FROM Receta) as total_recetas,
    (SELECT COUNT(*) FROM Informe_Medico) as total_informes,
    (SELECT COUNT(*) FROM Asignacion_Enfermero) as total_asignaciones;
