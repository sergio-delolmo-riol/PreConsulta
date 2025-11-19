-- ============================================
--   BASE DE DATOS CENTRO DE TRIAJE DIGITAL
--   Proyecto: PreConsulta
--   Fecha creación: 18/11/2025
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS centro_triaje_digital
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE centro_triaje_digital;

-- ============================================
-- 1. TABLA USUARIO
--    Almacena información básica de todos los usuarios del sistema
-- ============================================

CREATE TABLE Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    dni VARCHAR(20) UNIQUE,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME NULL,
    estado ENUM('activo', 'inactivo', 'bloqueado') DEFAULT 'activo',
    INDEX idx_email (email),
    INDEX idx_dni (dni)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. TABLA PACIENTE
--    Extiende Usuario con información específica del paciente
-- ============================================

CREATE TABLE Paciente (
    id_paciente INT PRIMARY KEY,
    fecha_nacimiento DATE,
    direccion VARCHAR(255),
    seguro_medico VARCHAR(100),
    contacto_familiar VARCHAR(150),
    telefono_emergencia VARCHAR(20),
    alergias TEXT,
    condiciones_medicas TEXT,
    grupo_sanguineo ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'),
    FOREIGN KEY (id_paciente) REFERENCES Usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. TABLA ENFERMERO (INCLUYE DOCTORES)
--    Extiende Usuario para personal de enfermería y médicos
-- ============================================

CREATE TABLE Enfermero (
    id_enfermero INT PRIMARY KEY,
    numero_colegiado VARCHAR(50) UNIQUE NOT NULL,
    especialidad VARCHAR(100),
    turno_actual ENUM('mañana','tarde','noche') NULL,
    disponible BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_enfermero) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    INDEX idx_numero_colegiado (numero_colegiado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. TABLA CELADOR
--    Extiende Usuario para personal de celadores
-- ============================================

CREATE TABLE Celador (
    id_celador INT PRIMARY KEY,
    area_asignada VARCHAR(100),
    turno ENUM('mañana','tarde','noche','rotativo') DEFAULT 'rotativo',
    estado ENUM('activo','inactivo','ocupado') DEFAULT 'activo',
    FOREIGN KEY (id_celador) REFERENCES Usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. TABLA PRIORIDAD
--    Define los niveles de prioridad del triaje
-- ============================================

CREATE TABLE Prioridad (
    id_prioridad INT AUTO_INCREMENT PRIMARY KEY,
    tipo_prioridad ENUM('alta','media','baja') NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    color_hex CHAR(7) NOT NULL,
    tiempo_max_atencion INT NOT NULL COMMENT 'Tiempo máximo en minutos',
    INDEX idx_tipo (tipo_prioridad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. TABLA BOX
--    Salas o boxes de atención médica
-- ============================================

CREATE TABLE Box (
    id_box INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    ubicacion VARCHAR(100),
    estado ENUM('libre','ocupado','limpieza','mantenimiento') DEFAULT 'libre',
    capacidad INT DEFAULT 1,
    equipamiento TEXT,
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. TABLA EPISODIO DE URGENCIA
--    Registro de cada visita a urgencias de un paciente
-- ============================================

CREATE TABLE Episodio_Urgencia (
    id_episodio INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    fecha_llegada DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_alta DATETIME NULL,
    box_asignado INT NULL,
    prioridad_actual INT NULL,
    tiempo_estimado_espera INT NULL COMMENT 'Tiempo en minutos',
    estado ENUM('espera_triaje','en_triaje','espera_atencion','en_atencion','alta','derivado') DEFAULT 'espera_triaje',
    motivo_consulta TEXT,
    notas_adicionales TEXT,
    
    FOREIGN KEY (id_paciente) REFERENCES Paciente(id_paciente),
    FOREIGN KEY (box_asignado) REFERENCES Box(id_box),
    FOREIGN KEY (prioridad_actual) REFERENCES Prioridad(id_prioridad),
    INDEX idx_estado (estado),
    INDEX idx_fecha_llegada (fecha_llegada),
    INDEX idx_paciente (id_paciente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. TABLA TRIAJE
--    Registro de la evaluación inicial del paciente
-- ============================================

CREATE TABLE Triaje (
    id_triaje INT AUTO_INCREMENT PRIMARY KEY,
    id_episodio INT NOT NULL,
    id_enfermero INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    prioridad_asignada INT NOT NULL,
    nivel_consciencia VARCHAR(50),
    presion_arterial VARCHAR(20),
    frecuencia_cardiaca INT,
    temperatura DECIMAL(4,2),
    saturacion_oxigeno INT,
    sintomas_texto TEXT,
    sintomas_audio_url VARCHAR(255),
    observaciones TEXT,
    
    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio),
    FOREIGN KEY (id_enfermero) REFERENCES Enfermero(id_enfermero),
    FOREIGN KEY (prioridad_asignada) REFERENCES Prioridad(id_prioridad),
    INDEX idx_episodio (id_episodio),
    INDEX idx_fecha (fecha_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. TABLA HISTORIAL CLÍNICO
--    Registro histórico de consultas y tratamientos
-- ============================================

CREATE TABLE Historial_Clinico (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_enfermero INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_consulta ENUM('urgencia','consulta','seguimiento','revision') DEFAULT 'urgencia',
    descripcion TEXT,
    diagnostico TEXT,
    tratamiento TEXT,
    pruebas_realizadas TEXT,
    medicacion_prescrita TEXT,
    
    FOREIGN KEY (id_paciente) REFERENCES Paciente(id_paciente),
    FOREIGN KEY (id_enfermero) REFERENCES Enfermero(id_enfermero),
    INDEX idx_paciente (id_paciente),
    INDEX idx_fecha (fecha_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. TABLA ASIGNACIÓN DE CELADOR
--     Gestión de traslados y asignaciones de celadores
-- ============================================

CREATE TABLE Asignacion_Celador (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_celador INT NOT NULL,
    id_episodio INT NOT NULL,
    tipo_tarea ENUM('traslado','admision','alta','apoyo') DEFAULT 'traslado',
    ubicacion_origen VARCHAR(100),
    ubicacion_destino VARCHAR(100),
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_inicio DATETIME NULL,
    fecha_finalizacion DATETIME NULL,
    estado ENUM('pendiente','en_curso','finalizado','cancelado') DEFAULT 'pendiente',
    notas TEXT,
    
    FOREIGN KEY (id_celador) REFERENCES Celador(id_celador),
    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio),
    INDEX idx_celador (id_celador),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_asignacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. TABLA ATENCIÓN MÉDICA
--     Registro de atenciones médicas realizadas
-- ============================================

CREATE TABLE Atencion_Medica (
    id_atencion INT AUTO_INCREMENT PRIMARY KEY,
    id_episodio INT NOT NULL,
    id_enfermero INT NOT NULL,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_fin DATETIME NULL,
    tipo_atencion ENUM('evaluacion','tratamiento','procedimiento','seguimiento') DEFAULT 'evaluacion',
    diagnostico TEXT,
    tratamiento TEXT,
    procedimientos TEXT,
    resultados TEXT,
    
    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio),
    FOREIGN KEY (id_enfermero) REFERENCES Enfermero(id_enfermero),
    INDEX idx_episodio (id_episodio),
    INDEX idx_fecha (fecha_inicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. TABLA NOTIFICACIONES
--     Sistema de notificaciones para usuarios
-- ============================================

CREATE TABLE Notificacion (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    id_episodio INT NULL,
    tipo ENUM('estado','prioridad','turno','movimiento','alerta','otro') NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    leida BOOLEAN DEFAULT FALSE,
    fecha_lectura DATETIME NULL,
    
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio),
    INDEX idx_usuario (id_usuario),
    INDEX idx_leida (leida),
    INDEX idx_fecha (fecha_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 13. TABLA LOG DE ACCIONES
--     Auditoría de acciones en el sistema
-- ============================================

CREATE TABLE Log_Acciones (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    id_episodio INT NULL,
    accion VARCHAR(255) NOT NULL,
    detalles TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio),
    INDEX idx_usuario (id_usuario),
    INDEX idx_fecha (fecha_hora),
    INDEX idx_accion (accion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista de pacientes en espera
CREATE OR REPLACE VIEW v_pacientes_espera AS
SELECT 
    e.id_episodio,
    u.nombre,
    u.apellidos,
    p.tipo_prioridad,
    p.color_hex,
    e.fecha_llegada,
    e.tiempo_estimado_espera,
    e.estado,
    TIMESTAMPDIFF(MINUTE, e.fecha_llegada, NOW()) as tiempo_espera_real
FROM Episodio_Urgencia e
JOIN Paciente pac ON e.id_paciente = pac.id_paciente
JOIN Usuario u ON pac.id_paciente = u.id_usuario
LEFT JOIN Prioridad p ON e.prioridad_actual = p.id_prioridad
WHERE e.estado IN ('espera_triaje', 'espera_atencion')
ORDER BY p.tipo_prioridad DESC, e.fecha_llegada ASC;

-- Vista de boxes disponibles
CREATE OR REPLACE VIEW v_boxes_disponibles AS
SELECT 
    b.id_box,
    b.nombre,
    b.ubicacion,
    b.estado,
    b.equipamiento
FROM Box b
WHERE b.estado = 'libre'
ORDER BY b.nombre;

-- Vista de celadores disponibles
CREATE OR REPLACE VIEW v_celadores_disponibles AS
SELECT 
    c.id_celador,
    u.nombre,
    u.apellidos,
    u.telefono,
    c.area_asignada,
    c.turno
FROM Celador c
JOIN Usuario u ON c.id_celador = u.id_usuario
WHERE c.estado = 'activo'
ORDER BY u.nombre;

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger para actualizar último acceso del usuario
DELIMITER $$
CREATE TRIGGER tr_update_ultimo_acceso
AFTER INSERT ON Log_Acciones
FOR EACH ROW
BEGIN
    IF NEW.id_usuario IS NOT NULL AND NEW.accion = 'login' THEN
        UPDATE Usuario SET ultimo_acceso = NEW.fecha_hora WHERE id_usuario = NEW.id_usuario;
    END IF;
END$$
DELIMITER ;

-- Trigger para actualizar estado del box cuando se asigna un episodio
DELIMITER $$
CREATE TRIGGER tr_update_box_ocupado
AFTER UPDATE ON Episodio_Urgencia
FOR EACH ROW
BEGIN
    IF NEW.box_asignado IS NOT NULL AND OLD.box_asignado IS NULL THEN
        UPDATE Box SET estado = 'ocupado' WHERE id_box = NEW.box_asignado;
    END IF;
    IF NEW.box_asignado IS NULL AND OLD.box_asignado IS NOT NULL THEN
        UPDATE Box SET estado = 'libre' WHERE id_box = OLD.box_asignado;
    END IF;
END$$
DELIMITER ;

-- ============================================
-- COMENTARIOS FINALES
-- ============================================

/*
NOTAS DE IMPLEMENTACIÓN:

1. Seguridad:
   - Las contraseñas deben almacenarse hasheadas (usar password_hash() en PHP)
   - Implementar validación de entrada en la capa de aplicación
   - Usar prepared statements para prevenir SQL injection

2. Rendimiento:
   - Se han añadido índices en campos frecuentemente consultados
   - Considerar particionamiento de tablas grandes (Log_Acciones, Historial_Clinico)
   - Implementar caché para consultas frecuentes

3. Escalabilidad:
   - Considerar replicación para lecturas
   - Implementar archivado de episodios antiguos
   - Monitorizar tamaño de tablas de log

4. Backup:
   - Implementar backup automático diario
   - Mantener backups incrementales
   - Probar restauración periódicamente

5. Accesibilidad:
   - Todos los campos de texto soportan UTF-8
   - Los ENUMs facilitan validación pero limitan flexibilidad
   - Considerar i18n para mensajes de notificación
*/
