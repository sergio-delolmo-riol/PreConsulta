# PreConsulta - Sistema Integrado con Base de Datos

## ✅ Implementación Completada

Se ha implementado la integración completa con la base de datos MySQL. Todas las funcionalidades están operativas:

## 🎯 Funcionalidades Implementadas

### 1. Sistema de Autenticación
- ✅ **Login (login.php)**: Validación de usuarios contra la BBDD
- ✅ **Registro (registro.php)**: Creación de nuevos usuarios en tablas Usuario y Paciente
- ✅ **Sesiones**: Gestión completa de sesiones con expiración automática
- ✅ **Protección de páginas**: Solo usuarios autenticados pueden acceder

### 2. Perfil de Usuario
- ✅ **Datos dinámicos (perfil-usuario.php)**: Muestra datos reales desde la BBDD
- ✅ **Actualización de perfil**: Formulario para completar datos faltantes
- ✅ **Guardado automático**: Los datos se guardan en la tabla Paciente

### 3. Historial de Consultas
- ✅ **Lista de consultas**: Muestra todas las consultas del paciente
- ✅ **Detalle completo**: Información de síntomas, triaje, prioridad, box asignado
- ✅ **Signos vitales**: Frecuencia cardíaca, presión arterial, temperatura, saturación O₂
- ✅ **Historial clínico**: Antecedentes y medicación actual

### 4. Nueva Consulta
- ✅ **Flujo completo**: 3 páginas protegidas (motivo, evidencia, confirmación)
- ✅ **Guardado en BBDD**: Se crea un nuevo Episodio_Urgencia
- ✅ **SessionStorage**: Los datos se mantienen entre páginas
- ✅ **Validación**: No permite avanzar sin descripción de síntomas

## 📂 Archivos Creados/Modificados

### Archivos PHP Principales
```
login.php                    - Sistema de login con validación BBDD
registro.php                 - Registro de nuevos usuarios
index.php                    - Página principal protegida
perfil-usuario.php           - Perfil con datos dinámicos
perfil.php                   - Redirección a perfil-usuario
detalle-consulta.php         - Detalle completo de una consulta
consulta-digital_pag1.php    - Página 1: Motivo de consulta
consulta-digital_pag2.php    - Página 2: Evidencia
consulta-digital_pag3.php    - Página 3: Confirmación
logout.php                   - Cierre de sesión
```

### APIs REST
```
api/update_profile.php       - Actualizar datos del paciente
api/get_historial.php        - Obtener historial de consultas
api/save_consulta.php        - Guardar nueva consulta
```

### Configuración
```
config/session_manager.php   - Gestión de sesiones y autenticación
config/helpers.php           - Funciones auxiliares (formateo, validación)
```

### Actualizaciones
```
js/main.js                   - Añadido guardado en sessionStorage
CSS/style.css                - Estilos para historial de consultas
```

## 🚀 Cómo Usar el Sistema

### 1. Acceso Inicial
1. Abre tu navegador en: `http://localhost/PreConsulta/login.php`
2. Puedes usar estos usuarios de prueba:

**Pacientes:**
- Email: `juan.perez@email.com` | Password: `password123`
- Email: `maria.garcia@email.com` | Password: `password123`
- Email: `carlos.lopez@email.com` | Password: `password123`

**Enfermeros:**
- Email: `laura.enfermera@hospital.com` | Password: `password123`
- Email: `miguel.enfermero@hospital.com` | Password: `password123`

**Celadores:**
- Email: `jose.celador@hospital.com` | Password: `password123`

### 2. Registro de Nuevo Usuario
1. En la página de login, haz clic en "Regístrate"
2. Completa todos los campos:
   - Nombre (mínimo 2 caracteres)
   - Apellidos (mínimo 2 caracteres)
   - Teléfono (9 dígitos)
   - Email (formato válido con .com o .es)
   - Contraseña (mínimo 6 caracteres)
3. El nuevo usuario se guarda automáticamente como "Paciente"

### 3. Completar Perfil
1. Después de iniciar sesión, ve a "Perfil"
2. Si faltan datos, verás un botón flotante "⚠️ Completa tu perfil"
3. Haz clic y completa:
   - Dirección
   - Limitaciones/condiciones médicas
   - Fecha de nacimiento
4. Los datos se guardan automáticamente en la BBDD

### 4. Ver Historial de Consultas
1. En tu perfil, haz clic en la pestaña "Historial Consultas"
2. Verás todas tus consultas previas con:
   - Fecha y hora
   - Prioridad (con código de colores)
   - Estado actual
3. Haz clic en cualquier consulta para ver detalles completos

### 5. Crear Nueva Consulta
1. Desde el inicio, haz clic en el botón cruz roja "Iniciar consulta"
2. **Página 1**: Describe tus síntomas (por audio o texto)
3. **Página 2**: Adjunta evidencia fotográfica (opcional)
4. **Página 3**: Confirma asistencia
5. La consulta se guarda automáticamente en la BBDD

### 6. Cerrar Sesión
- Cierra sesión desde: `http://localhost/PreConsulta/logout.php`
- O simplemente cierra el navegador (la sesión expira en 2 horas)

## 🔒 Seguridad Implementada

- ✅ **Contraseñas hasheadas**: Usando `password_hash()` de PHP
- ✅ **Prepared statements**: Prevención de SQL Injection
- ✅ **Sanitización XSS**: Todas las salidas pasan por `htmlspecialchars()`
- ✅ **Validación de sesiones**: Verificación en cada página
- ✅ **Expiración automática**: Sesiones expiran después de 2 horas
- ✅ **Protección CSRF**: Regeneración de ID de sesión en login

## 📊 Estructura de la Base de Datos

El sistema utiliza estas tablas principales:

- **Usuario**: Datos básicos de todos los usuarios
- **Paciente**: Información médica extendida
- **Enfermero**: Datos de personal de enfermería
- **Celador**: Datos de personal celador
- **Episodio_Urgencia**: Consultas/episodios de urgencia
- **Triaje**: Signos vitales y prioridad asignada
- **Historial_Clinico**: Antecedentes y medicación
- **Prioridad**: Niveles de urgencia (Emergencia, Urgente, etc.)
- **Box**: Boxes de atención disponibles

## 🎨 Estética Preservada

✅ **Se ha mantenido EXACTAMENTE la estética original**:
- Todos los estilos CSS intactos
- Misma estructura HTML
- Mismos colores, fuentes y espaciados
- Mismas animaciones y transiciones
- Mismos iconos SVG

## ⚙️ Requisitos del Sistema

- PHP 7.4+
- MySQL 8.0+
- Apache con mod_rewrite
- Extensión PDO de PHP

## 🔧 Configuración

La configuración está en `config/database.php`:
```php
DB_HOST = 'localhost'
DB_PORT = '3306'
DB_NAME = 'centro_triaje_digital'
DB_USER = 'root'
DB_PASS = ''
```

## 📝 Notas Importantes

1. **Primera vez**: Usa los usuarios de prueba de `seed_data.sql`
2. **Sesiones**: Duran 2 horas de inactividad
3. **Passwords**: Todos los usuarios de prueba tienen password: `password123`
4. **Navegación**: Todas las páginas están protegidas excepto login y registro
5. **Datos faltantes**: El botón flotante aparece automáticamente si faltan datos

## 🐛 Solución de Problemas

### Error: "No se puede conectar a la base de datos"
- Verifica que MySQL esté corriendo
- Comprueba las credenciales en `config/database.php`

### Error: "Sesión expirada"
- Es normal después de 2 horas de inactividad
- Simplemente vuelve a iniciar sesión

### No se guardan los datos del perfil
- Abre la consola del navegador (F12) y verifica errores
- Comprueba que `api/update_profile.php` sea accesible

### El historial no carga
- Verifica que tengas consultas en la BBDD
- Crea una nueva consulta para probar

## ✨ Características Adicionales Implementadas

- 📱 Diseño responsive (mobile-first)
- ♿ Accesibilidad WCAG 2.1 AA
- 🔔 Notificaciones de éxito/error
- 💾 Persistencia de datos entre páginas
- 🎨 Badges de colores para prioridades
- 📊 Cards informativos en el historial
- 🔄 Actualización en tiempo real

---

**Desarrollado para el proyecto PreConsulta - Centro de Triaje Digital**
