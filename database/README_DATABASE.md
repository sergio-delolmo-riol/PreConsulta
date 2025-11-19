# Documentación de Base de Datos - PreConsulta

## 📊 Centro de Triaje Digital

**Versión:** 1.0  
**Fecha:** 18/11/2025  
**Motor:** MySQL 8.0+  
**Charset:** UTF-8 (utf8mb4)

---

## 📑 Índice

1. [Estructura General](#estructura-general)
2. [Tablas Principales](#tablas-principales)
3. [Relaciones](#relaciones)
4. [Vistas](#vistas)
5. [Triggers](#triggers)
6. [Ejemplos de Uso](#ejemplos-de-uso)
7. [Instalación](#instalación)
8. [API PHP](#api-php)

---

## Estructura General

La base de datos `centro_triaje_digital` gestiona:

- **Usuarios**: Pacientes, enfermeros/médicos y celadores
- **Episodios de Urgencia**: Registro de visitas a urgencias
- **Triaje**: Evaluación inicial de los pacientes
- **Gestión de Boxes**: Asignación de salas de atención
- **Notificaciones**: Sistema de alertas para usuarios
- **Auditoría**: Log completo de acciones

### Diagrama Entidad-Relación

```
Usuario (1) ──< (N) Paciente
Usuario (1) ──< (N) Enfermero
Usuario (1) ──< (N) Celador

Paciente (1) ──< (N) Episodio_Urgencia
Episodio_Urgencia (1) ──< (N) Triaje
Episodio_Urgencia (1) ──< (N) Asignacion_Celador
Episodio_Urgencia (1) ──< (N) Atencion_Medica

Prioridad (1) ──< (N) Episodio_Urgencia
Box (1) ──< (N) Episodio_Urgencia
```

---

## Tablas Principales

### 1. Usuario
**Tabla base** para todos los usuarios del sistema.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_usuario` | INT PK AUTO | Identificador único |
| `nombre` | VARCHAR(100) | Nombre del usuario |
| `apellidos` | VARCHAR(150) | Apellidos del usuario |
| `dni` | VARCHAR(20) UNIQUE | Documento de identidad |
| `email` | VARCHAR(150) UNIQUE | Correo electrónico |
| `telefono` | VARCHAR(20) | Teléfono de contacto |
| `password` | VARCHAR(255) | Contraseña hasheada |
| `fecha_registro` | DATETIME | Fecha de registro |
| `ultimo_acceso` | DATETIME | Último acceso al sistema |
| `estado` | ENUM | activo, inactivo, bloqueado |

**Índices:**
- `idx_email` en `email`
- `idx_dni` en `dni`

---

### 2. Paciente
**Extiende Usuario** con información médica específica.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_paciente` | INT PK FK | Referencia a Usuario |
| `fecha_nacimiento` | DATE | Fecha de nacimiento |
| `direccion` | VARCHAR(255) | Dirección completa |
| `seguro_medico` | VARCHAR(100) | Compañía de seguro |
| `contacto_familiar` | VARCHAR(150) | Contacto de emergencia |
| `telefono_emergencia` | VARCHAR(20) | Teléfono de emergencia |
| `alergias` | TEXT | Alergias conocidas |
| `condiciones_medicas` | TEXT | Condiciones médicas previas |
| `grupo_sanguineo` | ENUM | A+, A-, B+, B-, AB+, AB-, O+, O- |

---

### 3. Enfermero
**Extiende Usuario** para personal sanitario.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_enfermero` | INT PK FK | Referencia a Usuario |
| `numero_colegiado` | VARCHAR(50) UNIQUE | Número de colegiado |
| `especialidad` | VARCHAR(100) | Especialidad médica |
| `turno_actual` | ENUM | mañana, tarde, noche |
| `disponible` | BOOLEAN | Disponibilidad actual |

**Índices:**
- `idx_numero_colegiado` en `numero_colegiado`

---

### 4. Celador
**Extiende Usuario** para personal de celadores.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_celador` | INT PK FK | Referencia a Usuario |
| `area_asignada` | VARCHAR(100) | Área de trabajo |
| `turno` | ENUM | mañana, tarde, noche, rotativo |
| `estado` | ENUM | activo, inactivo, ocupado |

---

### 5. Prioridad
**Define niveles de urgencia** del triaje.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_prioridad` | INT PK AUTO | Identificador único |
| `tipo_prioridad` | ENUM | alta, media, baja |
| `nombre` | VARCHAR(50) | Nombre descriptivo |
| `descripcion` | TEXT | Descripción detallada |
| `color_hex` | CHAR(7) | Color en hexadecimal |
| `tiempo_max_atencion` | INT | Tiempo máximo en minutos |

**Datos predefinidos:**
1. Emergencia (alta) - #FF0000 - 0 min
2. Muy Urgente (alta) - #FF4500 - 10 min
3. Urgente (media) - #FFA500 - 30 min
4. Menos Urgente (media) - #FFD700 - 60 min
5. No Urgente (baja) - #90EE90 - 120 min

---

### 6. Box
**Salas de atención** médica.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_box` | INT PK AUTO | Identificador único |
| `nombre` | VARCHAR(50) | Nombre del box |
| `ubicacion` | VARCHAR(100) | Ubicación física |
| `estado` | ENUM | libre, ocupado, limpieza, mantenimiento |
| `capacidad` | INT | Capacidad de pacientes |
| `equipamiento` | TEXT | Equipamiento disponible |

**Índices:**
- `idx_estado` en `estado`

---

### 7. Episodio_Urgencia
**Registro de cada visita** a urgencias.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_episodio` | INT PK AUTO | Identificador único |
| `id_paciente` | INT FK | Referencia a Paciente |
| `fecha_llegada` | DATETIME | Fecha y hora de llegada |
| `fecha_alta` | DATETIME | Fecha y hora de alta |
| `box_asignado` | INT FK | Box asignado |
| `prioridad_actual` | INT FK | Prioridad asignada |
| `tiempo_estimado_espera` | INT | Tiempo en minutos |
| `estado` | ENUM | espera_triaje, en_triaje, espera_atencion, en_atencion, alta, derivado |
| `motivo_consulta` | TEXT | Motivo de la consulta |
| `notas_adicionales` | TEXT | Notas adicionales |

**Índices:**
- `idx_estado` en `estado`
- `idx_fecha_llegada` en `fecha_llegada`
- `idx_paciente` en `id_paciente`

---

### 8. Triaje
**Evaluación inicial** del paciente.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_triaje` | INT PK AUTO | Identificador único |
| `id_episodio` | INT FK | Referencia a Episodio |
| `id_enfermero` | INT FK | Enfermero que realiza el triaje |
| `fecha_hora` | DATETIME | Fecha y hora del triaje |
| `prioridad_asignada` | INT FK | Prioridad asignada |
| `nivel_consciencia` | VARCHAR(50) | Nivel de consciencia |
| `presion_arterial` | VARCHAR(20) | Presión arterial |
| `frecuencia_cardiaca` | INT | Frecuencia cardíaca |
| `temperatura` | DECIMAL(4,2) | Temperatura corporal |
| `saturacion_oxigeno` | INT | Saturación de oxígeno (%) |
| `sintomas_texto` | TEXT | Síntomas descritos |
| `sintomas_audio_url` | VARCHAR(255) | URL del audio grabado |
| `observaciones` | TEXT | Observaciones del enfermero |

---

### 9. Asignacion_Celador
**Gestión de traslados** y asignaciones.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_asignacion` | INT PK AUTO | Identificador único |
| `id_celador` | INT FK | Celador asignado |
| `id_episodio` | INT FK | Episodio relacionado |
| `tipo_tarea` | ENUM | traslado, admision, alta, apoyo |
| `ubicacion_origen` | VARCHAR(100) | Origen del traslado |
| `ubicacion_destino` | VARCHAR(100) | Destino del traslado |
| `fecha_asignacion` | DATETIME | Fecha de asignación |
| `fecha_inicio` | DATETIME | Inicio de la tarea |
| `fecha_finalizacion` | DATETIME | Fin de la tarea |
| `estado` | ENUM | pendiente, en_curso, finalizado, cancelado |
| `notas` | TEXT | Notas adicionales |

---

### 10. Notificacion
**Sistema de notificaciones** para usuarios.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_notificacion` | INT PK AUTO | Identificador único |
| `id_usuario` | INT FK | Usuario destinatario |
| `id_episodio` | INT FK | Episodio relacionado |
| `tipo` | ENUM | estado, prioridad, turno, movimiento, alerta, otro |
| `titulo` | VARCHAR(150) | Título de la notificación |
| `mensaje` | TEXT | Mensaje completo |
| `fecha_hora` | DATETIME | Fecha y hora de creación |
| `leida` | BOOLEAN | Estado de lectura |
| `fecha_lectura` | DATETIME | Fecha de lectura |

---

### 11. Log_Acciones
**Auditoría completa** del sistema.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_log` | INT PK AUTO | Identificador único |
| `id_usuario` | INT FK | Usuario que realizó la acción |
| `id_episodio` | INT FK | Episodio relacionado |
| `accion` | VARCHAR(255) | Descripción de la acción |
| `detalles` | TEXT | Detalles adicionales |
| `ip_address` | VARCHAR(45) | Dirección IP |
| `user_agent` | VARCHAR(255) | Navegador/App |
| `fecha_hora` | DATETIME | Fecha y hora |

---

## Vistas

### v_pacientes_espera
Lista de pacientes esperando atención.

```sql
SELECT * FROM v_pacientes_espera;
```

**Columnas:**
- `id_episodio`, `nombre`, `apellidos`
- `tipo_prioridad`, `color_hex`
- `fecha_llegada`, `tiempo_estimado_espera`
- `estado`, `tiempo_espera_real` (calculado)

---

### v_boxes_disponibles
Boxes actualmente libres.

```sql
SELECT * FROM v_boxes_disponibles;
```

---

### v_celadores_disponibles
Celadores activos y disponibles.

```sql
SELECT * FROM v_celadores_disponibles;
```

---

## Triggers

### tr_update_ultimo_acceso
Actualiza `ultimo_acceso` cuando el usuario hace login.

### tr_update_box_ocupado
Cambia el estado del box cuando se asigna/libera un paciente.

---

## Ejemplos de Uso

### Consultar pacientes en espera ordenados por prioridad

```sql
SELECT 
    e.id_episodio,
    CONCAT(u.nombre, ' ', u.apellidos) AS paciente,
    p.nombre AS prioridad,
    p.color_hex,
    TIMESTAMPDIFF(MINUTE, e.fecha_llegada, NOW()) AS minutos_espera
FROM Episodio_Urgencia e
JOIN Paciente pac ON e.id_paciente = pac.id_paciente
JOIN Usuario u ON pac.id_paciente = u.id_usuario
JOIN Prioridad p ON e.prioridad_actual = p.id_prioridad
WHERE e.estado IN ('espera_triaje', 'espera_atencion')
ORDER BY p.tipo_prioridad DESC, e.fecha_llegada ASC;
```

### Registrar un nuevo paciente

```sql
-- 1. Insertar usuario
INSERT INTO Usuario (nombre, apellidos, dni, email, telefono, password)
VALUES ('Juan', 'Pérez', '12345678A', 'juan@email.com', '600123456', '<hash>');

-- 2. Insertar datos de paciente
INSERT INTO Paciente (id_paciente, fecha_nacimiento, grupo_sanguineo)
VALUES (LAST_INSERT_ID(), '1985-05-15', 'O+');
```

### Crear un episodio de urgencia

```sql
INSERT INTO Episodio_Urgencia (id_paciente, motivo_consulta, estado)
VALUES (1, 'Dolor torácico intenso', 'espera_triaje');
```

### Realizar triaje y asignar prioridad

```sql
-- 1. Registrar triaje
INSERT INTO Triaje (id_episodio, id_enfermero, prioridad_asignada, sintomas_texto)
VALUES (1, 6, 2, 'Dolor torácico opresivo irradiado a brazo izquierdo');

-- 2. Actualizar prioridad en episodio
UPDATE Episodio_Urgencia 
SET prioridad_actual = 2, estado = 'espera_atencion'
WHERE id_episodio = 1;
```

---

## Instalación

### 1. Crear la base de datos

```bash
mysql -u root -p < database/scripts/schema.sql
```

### 2. Cargar datos de prueba

```bash
mysql -u root -p < database/scripts/seed_data.sql
```

### 3. Configurar conexión PHP

Editar `config/database.php` con tus credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('DB_NAME', 'centro_triaje_digital');
```

---

## API PHP

### Uso básico de la clase Database

```php
<?php
require_once 'classes/Database.php';

// Obtener instancia
$db = Database::getInstance();

// SELECT
$pacientes = $db->select("SELECT * FROM Usuario WHERE estado = :estado", ['estado' => 'activo']);

// INSERT
$id = $db->insert('Usuario', [
    'nombre' => 'María',
    'apellidos' => 'García',
    'email' => 'maria@email.com',
    'password' => password_hash('pass123', PASSWORD_DEFAULT)
]);

// UPDATE
$affected = $db->update('Usuario', 
    ['telefono' => '600111222'], 
    'id_usuario = :id', 
    ['id' => 1]
);

// DELETE
$deleted = $db->delete('Usuario', 'id_usuario = :id', ['id' => 5]);

// TRANSACCIONES
$db->beginTransaction();
try {
    $db->insert('Episodio_Urgencia', [...]);
    $db->insert('Triaje', [...]);
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
}
?>
```

---

## Seguridad

### Contraseñas
Usar siempre `password_hash()` y `password_verify()`:

```php
// Al registrar
$hash = password_hash($password, PASSWORD_DEFAULT);

// Al validar login
if (password_verify($password, $hash)) {
    // Login correcto
}
```

### Prepared Statements
La clase Database usa automáticamente prepared statements para prevenir SQL Injection.

### Validación de Entrada
Siempre validar datos antes de insertar:

```php
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$telefono = preg_replace('/[^0-9]/', '', $_POST['telefono']);
```

---

## Mantenimiento

### Backup diario
```bash
mysqldump -u root -p centro_triaje_digital > backup_$(date +%Y%m%d).sql
```

### Limpiar logs antiguos
```sql
DELETE FROM Log_Acciones WHERE fecha_hora < DATE_SUB(NOW(), INTERVAL 6 MONTH);
```

### Archivar episodios cerrados
```sql
INSERT INTO Episodio_Urgencia_Archivo 
SELECT * FROM Episodio_Urgencia 
WHERE fecha_alta < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

---

## Contacto y Soporte

**Proyecto:** PreConsulta - Centro de Triaje Digital  
**Repositorio:** https://github.com/sergio-delolmo-riol/PreConsulta  
**Versión Base de Datos:** 1.0  
**Fecha:** 18/11/2025
