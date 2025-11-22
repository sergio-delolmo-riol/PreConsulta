# 🐳 Docker Setup - PreConsulta

Esta guía explica cómo ejecutar el proyecto PreConsulta usando Docker.

## 📋 Prerequisitos

- **Docker Desktop** instalado ([Descargar aquí](https://www.docker.com/products/docker-desktop))
- **Git** (opcional, si clonas el repositorio)

## 🚀 Inicio Rápido

### 1. Levantar los servicios

```bash
# En la raíz del proyecto
docker-compose up -d
```

Este comando:
- ✅ Descarga las imágenes necesarias (MySQL 8.0, PHP 8.2-Apache, PhpMyAdmin)
- ✅ Crea los contenedores
- ✅ Inicializa la base de datos con los scripts en `database/scripts/`
- ✅ Levanta el servidor web

### 2. Acceder a la aplicación

Espera 20-30 segundos a que la BD se inicialice, luego accede:

- **Aplicación Web**: http://localhost:8080
- **PhpMyAdmin**: http://localhost:8081
  - Usuario: `root`
  - Contraseña: `root_password_2024`

### 3. Iniciar sesión

Usa cualquiera de estos usuarios de prueba:

**Pacientes:**
- Email: `juan.perez@email.com` | Password: `password123`
- Email: `maria.garcia@email.com` | Password: `password123`

**Enfermeros:**
- Email: `laura.enfermera@hospital.com` | Password: `password123`

## 📦 Servicios Incluidos

| Servicio | Puerto | Descripción |
|----------|--------|-------------|
| **web** | 8080 | Aplicación PHP + Apache |
| **mysql** | 3307 | Base de datos MySQL 8.0 |
| **phpmyadmin** | 8081 | Administrador web de BD |

> **Nota**: MySQL usa el puerto **3307** en el host para evitar conflictos con XAMPP (que usa 3306)

## 🛠️ Comandos Útiles

### Ver logs
```bash
# Todos los servicios
docker-compose logs -f

# Solo MySQL
docker-compose logs -f mysql

# Solo Web
docker-compose logs -f web
```

### Detener servicios
```bash
docker-compose stop
```

### Reiniciar servicios
```bash
docker-compose restart
```

### Detener y eliminar contenedores
```bash
docker-compose down
```

### Detener y eliminar TODO (incluye volúmenes/datos)
```bash
docker-compose down -v
# ⚠️ CUIDADO: Esto borra toda la base de datos
```

### Reconstruir imágenes
```bash
docker-compose build --no-cache
docker-compose up -d
```

### Acceder al contenedor MySQL
```bash
docker exec -it preconsulta_mysql mysql -u root -p
# Password: root_password_2024
```

### Ejecutar comandos SQL desde archivo
```bash
docker exec -i preconsulta_mysql mysql -u root -proot_password_2024 centro_triaje_digital < database/scripts/schema.sql
```

## 🔧 Configuración Avanzada

### Cambiar puertos

Edita `docker-compose.yml`:

```yaml
services:
  web:
    ports:
      - "8080:80"  # Cambiar 8080 por el puerto que quieras
  
  mysql:
    ports:
      - "3307:3306"  # Cambiar 3307 por el puerto que quieras
```

### Cambiar credenciales de MySQL

Edita `docker-compose.yml` en la sección `mysql > environment`:

```yaml
MYSQL_ROOT_PASSWORD: tu_nueva_password
MYSQL_USER: tu_nuevo_usuario
MYSQL_PASSWORD: tu_nueva_password_usuario
```

**⚠️ IMPORTANTE**: Si cambias las credenciales, también actualiza `config/database.php`

## 🐛 Troubleshooting

### Error: "port is already allocated"

Otro servicio está usando el puerto. Opciones:
1. Detén el servicio que usa el puerto
2. Cambia el puerto en `docker-compose.yml`

```bash
# Ver qué usa el puerto 8080
netstat -ano | findstr :8080
```

### Error: "Cannot connect to database"

1. Verifica que MySQL esté saludable:
```bash
docker-compose ps
```

2. Revisa los logs de MySQL:
```bash
docker-compose logs mysql
```

3. Espera un poco más (la inicialización puede tardar)

### La base de datos está vacía

Los scripts SQL se ejecutan solo en la primera creación. Para reinicializar:

```bash
docker-compose down -v  # Elimina volúmenes
docker-compose up -d    # Recrea todo
```

### Cambios en PHP no se reflejan

Los archivos están montados como volumen, los cambios deberían verse instantáneamente. Si no:

```bash
docker-compose restart web
```

## 📊 Backup y Restore

### Crear backup
```bash
docker exec preconsulta_mysql mysqldump -u root -proot_password_2024 centro_triaje_digital > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restaurar backup
```bash
docker exec -i preconsulta_mysql mysql -u root -proot_password_2024 centro_triaje_digital < backup_20241120_180000.sql
```

## 🌐 Producción

Para desplegar en producción:

1. Cambia `APP_ENV=production` en el archivo `docker-compose.yml`
2. Usa contraseñas seguras (mínimo 16 caracteres)
3. Considera usar secretos de Docker
4. Configura SSL/HTTPS con un proxy inverso (nginx, traefik)
5. Ajusta los límites de recursos

## 📚 Más Información

- [Docker Docs](https://docs.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [MySQL Docker Image](https://hub.docker.com/_/mysql)
- [PHP Docker Image](https://hub.docker.com/_/php)
