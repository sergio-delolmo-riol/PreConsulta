# 📋 Migración de Base de Datos - Campos Personales en Usuario

## 🎯 Objetivo
Centralizar los datos personales básicos (fecha de nacimiento, dirección y condiciones médicas) en la tabla `Usuario` para que estén disponibles para todos los tipos de usuarios, no solo pacientes.

---

## 📊 Cambios Realizados

### **Tabla Usuario - Campos Agregados:**
- ✅ `fecha_nacimiento` (DATE) - Fecha de nacimiento del usuario
- ✅ `direccion` (VARCHAR 255) - Dirección de domicilio
- ✅ `condiciones_medicas` (TEXT) - Limitaciones o condiciones médicas

### **Tabla Paciente - Campos Eliminados:**
- ❌ `fecha_nacimiento` - Movido a Usuario
- ❌ `direccion` - Movido a Usuario
- ❌ `condiciones_medicas` - Movido a Usuario

### **Campos que permanecen en Paciente:**
- ✅ `seguro_medico` - Información específica del paciente
- ✅ `contacto_familiar` - Contacto de emergencia
- ✅ `telefono_emergencia` - Teléfono de emergencia
- ✅ `alergias` - Alergias médicas
- ✅ `grupo_sanguineo` - Tipo de sangre

---

## 🔄 Archivos Modificados

### **1. Schema de Base de Datos:**
- `database/scripts/schema.sql` - Actualizado con nueva estructura
- `database/scripts/migration_add_user_fields.sql` - Script de migración

### **2. Código PHP:**
- `perfil-usuario.php` - Ahora lee de Usuario en lugar de Paciente
- `api/update_profile.php` - Guarda en Usuario en lugar de Paciente

---

## 🚀 Migración Aplicada

### **Comandos ejecutados:**
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
    u.condiciones_medicas = p.condiciones_medicas
WHERE p.fecha_nacimiento IS NOT NULL 
   OR p.direccion IS NOT NULL 
   OR p.condiciones_medicas IS NOT NULL;
```

---

## ✅ Beneficios

1. **Centralización de datos:** Todos los datos personales en una sola tabla
2. **Consistencia:** Todos los usuarios (pacientes, enfermeros, celadores) pueden tener estos datos
3. **Simplicidad:** Menos JOINs necesarios para obtener información básica
4. **Mantenibilidad:** Más fácil de gestionar y actualizar

---

## 📝 Notas

- Los datos existentes fueron migrados automáticamente
- Los campos en `Paciente` no fueron eliminados físicamente por seguridad
- Para eliminar definitivamente los campos de `Paciente`, ejecutar:
  ```sql
  ALTER TABLE Paciente 
  DROP COLUMN fecha_nacimiento,
  DROP COLUMN direccion,
  DROP COLUMN condiciones_medicas;
  ```

---

## 🔍 Verificación

Para verificar que la migración fue exitosa:

```sql
-- Ver estructura de Usuario
DESCRIBE Usuario;

-- Ver datos migrados
SELECT id_usuario, nombre, apellidos, fecha_nacimiento, direccion, condiciones_medicas
FROM Usuario
WHERE fecha_nacimiento IS NOT NULL 
   OR direccion IS NOT NULL 
   OR condiciones_medicas IS NOT NULL;
```

---

**Fecha de migración:** 20 de noviembre de 2025  
**Estado:** ✅ Completado exitosamente
