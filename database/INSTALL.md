# 🗄️ Guía de Instalación de Base de Datos - PreConsulta

## 📋 Requisitos Previos

- **MySQL** 8.0 o superior / **MariaDB** 10.5 o superior
- **PHP** 7.4 o superior
- Extensión **PDO** y **PDO_MySQL** habilitadas en PHP
- Acceso de administrador a MySQL

---

## 🚀 Instalación Rápida

### Paso 1: Crear la Base de Datos

```bash
# Desde la línea de comandos (Linux/Mac):
mysql -u root -p < database/scripts/schema.sql

# O desde Windows:
mysql -u root -p < database\scripts\schema.sql
```

### Paso 2: Cargar Datos de Prueba (Opcional)

```bash
mysql -u root -p < database/scripts/seed_data.sql
```

### Paso 3: Configurar Conexión PHP

1. Abrir el archivo `config/database.php`
2. Modificar las credenciales según tu entorno:

```php
define('DB_HOST', 'localhost');     // Servidor MySQL
define('DB_PORT', '3306');          // Puerto MySQL
define('DB_NAME', 'centro_triaje_digital');
define('DB_USER', 'tu_usuario');    // ⚠️ Cambiar
define('DB_PASS', 'tu_contraseña'); // ⚠️ Cambiar
```

### Paso 4: Probar la Conexión

```bash
php test_connection.php
```

Si todo está correcto, verás:
```
✅ Conexión establecida exitosamente!
📊 Base de datos: centro_triaje_digital
```

---

## 🔧 Instalación Manual (Paso a Paso)

### 1. Acceder a MySQL

```bash
mysql -u root -p
```

### 2. Crear Base de Datos

```sql
CREATE DATABASE centro_triaje_digital
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE centro_triaje_digital;
```

### 3. Ejecutar Script de Esquema

Copiar y pegar el contenido de `database/scripts/schema.sql` en la consola MySQL, o:

```sql
SOURCE /ruta/completa/a/database/scripts/schema.sql;
```

### 4. Verificar Tablas Creadas

```sql
SHOW TABLES;
```

Deberías ver 13 tablas:
- Usuario
- Paciente
- Enfermero
- Celador
- Prioridad
- Box
- Episodio_Urgencia
- Triaje
- Historial_Clinico
- Asignacion_Celador
- Atencion_Medica
- Notificacion
- Log_Acciones

### 5. Cargar Datos de Ejemplo

```sql
SOURCE /ruta/completa/a/database/scripts/seed_data.sql;
```

---

## 🔐 Crear Usuario MySQL Específico (Recomendado)

Por seguridad, crear un usuario específico para la aplicación:

```sql
-- Crear usuario
CREATE USER 'preconsulta_user'@'localhost' IDENTIFIED BY 'contraseña_segura';

-- Otorgar permisos
GRANT SELECT, INSERT, UPDATE, DELETE ON centro_triaje_digital.* 
TO 'preconsulta_user'@'localhost';

-- Aplicar cambios
FLUSH PRIVILEGES;
```

Luego actualizar `config/database.php`:

```php
define('DB_USER', 'preconsulta_user');
define('DB_PASS', 'contraseña_segura');
```

---

## 📊 Estructura de Archivos Creada

```
PreConsulta/
├── config/
│   └── database.php          # Configuración de conexión
├── classes/
│   └── Database.php          # Clase para gestión de BD
├── database/
│   ├── scripts/
│   │   ├── schema.sql        # Esquema de tablas
│   │   └── seed_data.sql     # Datos de prueba
│   └── README_DATABASE.md    # Documentación completa
└── test_connection.php       # Script de prueba
```

---

## 🧪 Probar la Instalación

### Desde PHP:

```php
<?php
require_once 'classes/Database.php';

try {
    $db = Database::getInstance();
    echo "✅ Conexión exitosa!\n";
    
    // Obtener estadísticas
    $total_usuarios = $db->selectOne("SELECT COUNT(*) as total FROM Usuario")['total'];
    echo "👥 Total usuarios: {$total_usuarios}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
```

### Desde MySQL:

```sql
-- Ver pacientes en espera
SELECT * FROM v_pacientes_espera;

-- Ver boxes disponibles
SELECT * FROM v_boxes_disponibles;

-- Estadísticas rápidas
SELECT 
    'Usuarios' as Concepto, COUNT(*) as Total FROM Usuario
UNION ALL SELECT 'Pacientes', COUNT(*) FROM Paciente
UNION ALL SELECT 'Episodios activos', COUNT(*) FROM Episodio_Urgencia WHERE estado != 'alta';
```

---

## 🐛 Solución de Problemas

### Error: "Access denied for user"

**Problema:** Credenciales incorrectas.

**Solución:**
```sql
-- Verificar usuario y contraseña
mysql -u root -p

-- Resetear contraseña si es necesario
ALTER USER 'root'@'localhost' IDENTIFIED BY 'nueva_contraseña';
```

### Error: "Unknown database"

**Problema:** Base de datos no creada.

**Solución:**
```sql
CREATE DATABASE centro_triaje_digital;
```

### Error: "Table already exists"

**Problema:** Intentando crear tablas que ya existen.

**Solución:**
```sql
-- Eliminar base de datos y volver a crearla
DROP DATABASE centro_triaje_digital;
CREATE DATABASE centro_triaje_digital;
```

### Error: PDO extension not loaded

**Problema:** Extensión PDO no habilitada en PHP.

**Solución:**

**Windows (xampp/wampp):**
1. Editar `php.ini`
2. Descomentar: `extension=pdo_mysql`
3. Reiniciar Apache

**Linux:**
```bash
sudo apt-get install php-mysql
sudo systemctl restart apache2
```

### Error: "Can't connect to MySQL server"

**Problema:** MySQL no está corriendo.

**Solución:**

**Windows:**
```bash
net start MySQL80
```

**Linux:**
```bash
sudo systemctl start mysql
# o
sudo service mysql start
```

---

## 📚 Datos de Prueba Incluidos

El archivo `seed_data.sql` incluye:

### Usuarios:
- **5 Pacientes** (Juan Torres, María García, Carlos Rodríguez, Ana Martínez, Pedro López)
- **4 Enfermeros/Médicos** (Laura, Miguel, Carmen, David)
- **3 Celadores** (Antonio, Rosa, Francisco)

### Contraseña de todos los usuarios de prueba:
```
PreConsulta2024!
```

### Prioridades:
1. 🔴 Emergencia (0 min)
2. 🔴 Muy Urgente (10 min)
3. 🟠 Urgente (30 min)
4. 🟡 Menos Urgente (60 min)
5. 🟢 No Urgente (120 min)

### Boxes:
- 6 boxes configurados (Box 1-5 + Sala Reanimación)

### Episodios activos:
- 4 episodios de urgencia en diferentes estados

---

## 🔄 Actualizar Base de Datos

Si hay cambios en el esquema:

### Opción 1: Recrear (pierde datos)
```sql
DROP DATABASE centro_triaje_digital;
```
Luego ejecutar instalación desde Paso 1.

### Opción 2: Migración (conserva datos)
```sql
-- Backup primero
mysqldump -u root -p centro_triaje_digital > backup.sql

-- Aplicar cambios específicos
ALTER TABLE Paciente ADD COLUMN nuevo_campo VARCHAR(100);
```

---

## 📖 Documentación Adicional

Para información detallada sobre:
- Estructura de tablas
- Relaciones entre entidades
- Ejemplos de queries
- API PHP completa

Ver: [`database/README_DATABASE.md`](./README_DATABASE.md)

---

## ✅ Checklist de Instalación

- [ ] MySQL instalado y corriendo
- [ ] Base de datos `centro_triaje_digital` creada
- [ ] Todas las tablas creadas (13 tablas)
- [ ] Datos de prueba cargados (opcional)
- [ ] `config/database.php` configurado
- [ ] Usuario MySQL específico creado (recomendado)
- [ ] Conexión probada con `test_connection.php`
- [ ] Sin errores en logs de PHP

---

## 🆘 Soporte

Si encuentras problemas:

1. Verificar logs de error de PHP: `logs/error.log`
2. Verificar logs de MySQL: `/var/log/mysql/error.log`
3. Revisar documentación: `database/README_DATABASE.md`
4. Contactar al equipo de desarrollo

---

**Proyecto:** PreConsulta - Centro de Triaje Digital  
**Versión BD:** 1.0  
**Última actualización:** 18/11/2025
