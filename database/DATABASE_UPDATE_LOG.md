# 📊 Actualización de Base de Datos - Noviembre 2025

## 🎯 Resumen de Cambios

Se ha realizado una reestructuración de la base de datos para centralizar los datos personales básicos en la tabla `Usuario`.

---

## 📋 Cambios Aplicados

### **1. Schema (schema.sql)**

#### Tabla `Usuario` - Campos Agregados:
```sql
CREATE TABLE Usuario (
    ...
    telefono VARCHAR(20),
    fecha_nacimiento DATE,              -- ✨ NUEVO
    direccion VARCHAR(255),             -- ✨ NUEVO
    condiciones_medicas TEXT,           -- ✨ NUEVO
    password VARCHAR(255) NOT NULL,
    ...
);
```

#### Tabla `Paciente` - Campos Eliminados:
```sql
CREATE TABLE Paciente (
    id_paciente INT PRIMARY KEY,
    -- fecha_nacimiento DATE,           -- ❌ MOVIDO a Usuario
    -- direccion VARCHAR(255),          -- ❌ MOVIDO a Usuario
    -- condiciones_medicas TEXT,        -- ❌ MOVIDO a Usuario
    seguro_medico VARCHAR(100),
    contacto_familiar VARCHAR(150),
    telefono_emergencia VARCHAR(20),
    alergias TEXT,
    grupo_sanguineo ENUM(...),
    ...
);
```

---

### **2. Seed Data (seed_data.sql)**

#### Usuarios de Prueba - Datos Completos:

**Pacientes:**
| Nombre | DNI | Edad | Dirección | Condiciones |
|--------|-----|------|-----------|-------------|
| Juan Torres Mena | 12345678A | 40 años | Calle Mayor 45, Madrid | Alergia a la penicilina |
| María García López | 23456789B | 33 años | Av. Libertad 12, Madrid | Asma leve |
| Carlos Rodríguez | 34567890C | 47 años | Plaza España 3, Madrid | Hipertensión controlada |
| Ana Martínez Pérez | 45678901D | 30 años | Calle Alcalá 89, Madrid | Sin condiciones |
| Pedro López | 56789012E | 37 años | Gran Vía 25, Madrid | Diabetes tipo 2 |

**Enfermeros:**
| Nombre | DNI | Edad | Dirección |
|--------|-----|------|-----------|
| Laura Sánchez Ruiz | 67890123F | 38 años | Calle Serrano 78, Madrid |
| Miguel Fernández | 78901234G | 43 años | P. Castellana 150, Madrid |
| Carmen Jiménez | 89012345H | 35 años | Calle Goya 32, Madrid |

**Celadores:**
| Nombre | DNI | Edad | Dirección |
|--------|-----|------|-----------|
| Antonio Navarro | 01234567J | 50 años | C. Bravo Murillo 200, Madrid |
| Rosa Vázquez | 12345670K | 32 años | C. Arturo Soria 120, Madrid |
| Francisco Molina | 23456701L | 36 años | Calle Orense 68, Madrid |

---

### **3. Script de Migración (migration_add_user_fields.sql)**

Script SQL para migrar bases de datos existentes:

```sql
-- Agregar columnas a Usuario
ALTER TABLE Usuario 
ADD COLUMN fecha_nacimiento DATE NULL AFTER telefono,
ADD COLUMN direccion VARCHAR(255) NULL AFTER fecha_nacimiento,
ADD COLUMN condiciones_medicas TEXT NULL AFTER direccion;

-- Migrar datos existentes
UPDATE Usuario u
INNER JOIN Paciente p ON u.id_usuario = p.id_paciente
SET 
    u.fecha_nacimiento = p.fecha_nacimiento,
    u.direccion = p.direccion,
    u.condiciones_medicas = p.condiciones_medicas;

-- (Opcional) Eliminar columnas de Paciente
-- ALTER TABLE Paciente 
-- DROP COLUMN fecha_nacimiento,
-- DROP COLUMN direccion,
-- DROP COLUMN condiciones_medicas;
```

---

## 🔄 Archivos Modificados

| Archivo | Cambios | Estado |
|---------|---------|--------|
| `database/scripts/schema.sql` | ✅ Actualizado con nueva estructura | Completo |
| `database/scripts/seed_data.sql` | ✅ Datos de prueba actualizados | Completo |
| `database/scripts/migration_add_user_fields.sql` | ✅ Script de migración creado | Completo |
| `perfil-usuario.php` | ✅ Lee de Usuario en lugar de Paciente | Completo |
| `api/update_profile.php` | ✅ Guarda en Usuario en lugar de Paciente | Completo |

---

## 📝 Instrucciones de Uso

### **Para Base de Datos Nueva:**
```bash
# 1. Crear base de datos
mysql -u root -p < database/scripts/schema.sql

# 2. Cargar datos de prueba
mysql -u root -p < database/scripts/seed_data.sql
```

### **Para Base de Datos Existente:**
```bash
# Ejecutar script de migración
mysql -u root -p centro_triaje_digital < database/scripts/migration_add_user_fields.sql
```

---

## ✅ Beneficios

1. **Centralización**: Todos los datos personales en una sola tabla
2. **Consistencia**: Todos los tipos de usuarios pueden tener estos datos
3. **Simplicidad**: Menos JOINs necesarios
4. **Mantenibilidad**: Más fácil de gestionar y actualizar
5. **Escalabilidad**: Preparado para futuros tipos de usuarios

---

## 🔍 Verificación

Para verificar que todo está correcto:

```sql
-- Ver estructura de Usuario
DESCRIBE Usuario;

-- Ver usuarios con datos completos
SELECT 
    id_usuario, 
    nombre, 
    apellidos, 
    dni,
    fecha_nacimiento,
    YEAR(CURDATE()) - YEAR(fecha_nacimiento) AS edad,
    direccion,
    condiciones_medicas
FROM Usuario
LIMIT 10;
```

---

**Fecha de actualización:** 20 de noviembre de 2025  
**Estado:** ✅ Completado y testeado
