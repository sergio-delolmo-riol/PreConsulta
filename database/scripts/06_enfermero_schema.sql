-- ============================================
-- SCHEMA: Sistema de Enfermeros
-- Descripción: Tablas para gestión de enfermeros, asignaciones, recetas e informes médicos
-- Fecha: 2025-11-21
-- ============================================

USE centro_triaje_digital;

-- ============================================
-- 1. TABLA ASIGNACION_ENFERMERO
--    Controla qué enfermero atiende qué paciente (uno a la vez)
-- ============================================

CREATE TABLE IF NOT EXISTS Asignacion_Enfermero (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_enfermero INT NOT NULL,
    id_episodio INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_inicio_atencion DATETIME NULL,
    fecha_fin_atencion DATETIME NULL,
    estado ENUM('asignado','atendiendo','finalizado','cancelado') DEFAULT 'asignado',
    notas_enfermero TEXT,
    
    FOREIGN KEY (id_enfermero) REFERENCES Enfermero(id_enfermero) ON DELETE CASCADE,
    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio) ON DELETE CASCADE,
    
    INDEX idx_enfermero (id_enfermero),
    INDEX idx_episodio (id_episodio),
    INDEX idx_estado (estado),
    INDEX idx_fecha_asignacion (fecha_asignacion),
    
    -- Un enfermero solo puede tener una asignación activa a la vez
    UNIQUE KEY unique_enfermero_activo (id_enfermero, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. TABLA INFORME_MEDICO
--    Informes médicos creados por enfermeros sobre pacientes
-- ============================================

CREATE TABLE IF NOT EXISTS Informe_Medico (
    id_informe INT AUTO_INCREMENT PRIMARY KEY,
    id_episodio INT NOT NULL,
    id_enfermero INT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    diagnostico_preliminar TEXT,
    tratamiento_aplicado TEXT,
    observaciones TEXT,
    evolucion TEXT,
    recomendaciones TEXT,
    requiere_seguimiento BOOLEAN DEFAULT FALSE,
    derivado_a VARCHAR(100) NULL COMMENT 'Especialista o servicio al que se deriva',
    
    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio) ON DELETE CASCADE,
    FOREIGN KEY (id_enfermero) REFERENCES Enfermero(id_enfermero) ON DELETE CASCADE,
    
    INDEX idx_episodio (id_episodio),
    INDEX idx_enfermero (id_enfermero),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. TABLA RECETA
--    Recetas de fármacos prescritas por enfermeros
-- ============================================

CREATE TABLE IF NOT EXISTS Receta (
    id_receta INT AUTO_INCREMENT PRIMARY KEY,
    id_episodio INT NOT NULL,
    id_enfermero INT NOT NULL,
    fecha_prescripcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    nombre_farmaco VARCHAR(200) NOT NULL,
    principio_activo VARCHAR(200),
    dosis VARCHAR(100) NOT NULL COMMENT 'Ej: 500mg, 2 comprimidos, 10ml',
    via_administracion ENUM('oral','intravenosa','intramuscular','subcutanea','topica','inhalada','rectal','otra') NOT NULL,
    frecuencia VARCHAR(100) NOT NULL COMMENT 'Ej: cada 8 horas, 3 veces al día',
    duracion VARCHAR(100) NOT NULL COMMENT 'Ej: 7 días, 2 semanas, hasta mejoría',
    indicaciones TEXT,
    contraindicaciones TEXT,
    efectos_secundarios TEXT,
    estado ENUM('activa','completada','suspendida') DEFAULT 'activa',
    
    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio) ON DELETE CASCADE,
    FOREIGN KEY (id_enfermero) REFERENCES Enfermero(id_enfermero) ON DELETE CASCADE,
    
    INDEX idx_episodio (id_episodio),
    INDEX idx_enfermero (id_enfermero),
    INDEX idx_fecha_prescripcion (fecha_prescripcion),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. ACTUALIZAR TABLA CELADOR - Añadir disponibilidad y box
-- ============================================

-- Añadir columna disponible si no existe (verificar primero)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'centro_triaje_digital' 
    AND TABLE_NAME = 'Celador' 
    AND COLUMN_NAME = 'disponible');

SET @query = IF(@col_exists = 0,
    'ALTER TABLE Celador ADD COLUMN disponible ENUM(''si'',''no'') DEFAULT ''si'' AFTER estado',
    'SELECT "La columna disponible ya existe en Celador" AS mensaje');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Añadir columna id_box si no existe
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'centro_triaje_digital' 
    AND TABLE_NAME = 'Celador' 
    AND COLUMN_NAME = 'id_box');

SET @query = IF(@col_exists = 0,
    'ALTER TABLE Celador ADD COLUMN id_box INT NULL AFTER disponible',
    'SELECT "La columna id_box ya existe en Celador" AS mensaje');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Añadir constraint de foreign key si no existe
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = 'centro_triaje_digital' 
    AND TABLE_NAME = 'Celador' 
    AND CONSTRAINT_NAME = 'fk_celador_box');

SET @query = IF(@fk_exists = 0,
    'ALTER TABLE Celador ADD CONSTRAINT fk_celador_box FOREIGN KEY (id_box) REFERENCES Box(id_box) ON DELETE SET NULL',
    'SELECT "La foreign key fk_celador_box ya existe" AS mensaje');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- 5. ACTUALIZAR TABLA ENFERMERO - Añadir box asignado
-- ============================================

-- Añadir columna id_box si no existe
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'centro_triaje_digital' 
    AND TABLE_NAME = 'Enfermero' 
    AND COLUMN_NAME = 'id_box');

SET @query = IF(@col_exists = 0,
    'ALTER TABLE Enfermero ADD COLUMN id_box INT NULL AFTER disponible',
    'SELECT "La columna id_box ya existe en Enfermero" AS mensaje');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Añadir constraint de foreign key si no existe
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = 'centro_triaje_digital' 
    AND TABLE_NAME = 'Enfermero' 
    AND CONSTRAINT_NAME = 'fk_enfermero_box');

SET @query = IF(@fk_exists = 0,
    'ALTER TABLE Enfermero ADD CONSTRAINT fk_enfermero_box FOREIGN KEY (id_box) REFERENCES Box(id_box) ON DELETE SET NULL',
    'SELECT "La foreign key fk_enfermero_box ya existe" AS mensaje');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================

-- Índice compuesto para buscar asignaciones activas de enfermeros
CREATE INDEX idx_enfermero_estado_activo ON Asignacion_Enfermero(id_enfermero, estado, fecha_asignacion);

-- Índice para búsqueda de informes por paciente
CREATE INDEX idx_informe_paciente ON Informe_Medico(id_episodio, fecha_creacion DESC);

-- Índice para búsqueda de recetas activas
CREATE INDEX idx_receta_activa ON Receta(id_episodio, estado, fecha_prescripcion DESC);

-- ============================================
-- COMENTARIOS Y DOCUMENTACIÓN
-- ============================================

ALTER TABLE Asignacion_Enfermero COMMENT = 'Gestiona la asignación de pacientes a enfermeros - solo uno a la vez por enfermero';
ALTER TABLE Informe_Medico COMMENT = 'Informes médicos elaborados por enfermeros durante la atención';
ALTER TABLE Receta COMMENT = 'Recetas de fármacos prescritas por enfermeros a pacientes';
