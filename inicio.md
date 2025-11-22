# 🚀 Guía de Inicio - PreConsulta

## 📖 Cómo Abrir el Proyecto en Tu Ordenador

Esta guía te ayudará a ejecutar el proyecto PreConsulta que has recibido (por USB, ZIP o carpeta compartida).

**⚡ Tiempo total:** ~10 minutos (primera vez)  
**🌐 URL final:** http://localhost:8090/login.php

---

## 📋 Paso 1: Instalar Docker Desktop

**Si ya tienes Docker instalado, salta al Paso 2.**

### Para Windows:

1. **Descarga Docker Desktop:**
   - Abre tu navegador y ve a: https://www.docker.com/products/docker-desktop
   - Haz clic en "Download for Windows"
   - Guarda el archivo `Docker Desktop Installer.exe`

2. **Instala Docker Desktop:**
   - Ejecuta el instalador descargado
   - Acepta los términos y condiciones
   - Deja marcada la opción "Use WSL 2 instead of Hyper-V" (recomendado)
   - Haz clic en "Ok" e "Install"
   - Cuando termine, reinicia tu ordenador si te lo pide

3. **Inicia Docker Desktop:**
   - Después del reinicio, busca "Docker Desktop" en el menú Inicio
   - Ábrelo y espera a que aparezca el mensaje **"Docker is running"**
   - Esto puede tardar 1-2 minutos la primera vez
   - Verás el icono de Docker 🐳 en la barra de tareas (abajo a la derecha)

4. **Verifica la instalación:**
   - Haz clic derecho en el icono de Docker → Debe decir "Docker Desktop is running"
   - Abre PowerShell: presiona `Windows + X` y selecciona "Windows PowerShell"
   - Escribe este comando y presiona Enter:
   ```powershell
   docker --version
   ```
   - Deberías ver algo como: `Docker version 24.0.7`
   - Si sale un error, reinicia Docker Desktop e intenta de nuevo

### Para Mac:

1. Descarga Docker Desktop desde: https://www.docker.com/products/docker-desktop
2. Arrastra Docker.app a la carpeta Aplicaciones
3. Abre Docker desde Aplicaciones
4. Verifica con `docker --version` en Terminal

---

## 📥 Paso 2: Copiar el Proyecto a Tu Ordenador

Tienes varias formas de recibir el proyecto:

### Opción A: Archivo ZIP (Más común)

1. **Descomprimir el ZIP:**
   - Haz clic derecho sobre el archivo `PreConsulta.zip`
   - Selecciona "Extraer todo..." o "Extract Here"
   - Elige una ubicación fácil de encontrar, por ejemplo:
     - `C:\Users\TuNombre\Documents\PreConsulta`
     - `D:\Proyectos\PreConsulta`
   - Haz clic en "Extraer"

2. **Verificar el contenido:**
   - Abre la carpeta extraída "PreConsulta"
   - Debes ver archivos como:
     - `docker-compose.yml`
     - `Dockerfile`
     - Carpetas: `database`, `config`, `api`, `classes`, etc.

### Opción B: USB o Carpeta Compartida

1. **Copia la carpeta completa:**
   - Conecta el USB o accede a la carpeta compartida
   - Copia **toda la carpeta** "PreConsulta"
   - Pégala en tu ordenador (por ejemplo: `C:\Users\TuNombre\Documents\`)

### Opción C: Nube (Google Drive, OneDrive, etc.)

1. Descarga la carpeta o ZIP desde el servicio de nube
2. Extrae en tu ordenador si es un ZIP
3. Verifica que tienes todos los archivos

---

## 🚀 Paso 3: Iniciar la Aplicación

### 1. Abrir PowerShell en la carpeta del proyecto

**Forma fácil en Windows (recomendada):**

1. Abre el Explorador de Archivos (📁)
2. Navega hasta la carpeta "PreConsulta" que copiaste
3. Verifica que estás en la carpeta correcta (debes ver `docker-compose.yml`)
4. Haz clic en la **barra de direcciones** (donde dice la ruta)
5. Escribe `powershell` y presiona **Enter**
6. Se abrirá PowerShell ya ubicado en esa carpeta ✅

**Forma alternativa:**

1. Presiona `Windows + X`
2. Selecciona "Windows PowerShell"
3. Navega a la carpeta con el comando:
```powershell
cd "C:\ruta\donde\esta\PreConsulta"
```

**Ejemplo:**
```powershell
cd "C:\Users\TuNombre\Documents\PreConsulta"
```

### 2. Verificar que estás en la carpeta correcta

Escribe este comando:
```powershell
dir
```

**Debes ver estos archivos importantes:**
- ✅ `docker-compose.yml`
- ✅ `Dockerfile`
- ✅ Carpeta `database`
- ✅ Carpeta `config`
- ✅ Varios archivos `.php` y `.html`

❌ **Si no los ves:** No estás en la carpeta correcta. Vuelve al paso anterior.

### 3. Levantar la aplicación

**🎯 Copia y pega este comando en PowerShell:**

```powershell
docker-compose up -d
```

Luego presiona **Enter**.

**⏱️ ¿Qué va a pasar?**

**Si es la PRIMERA VEZ:**
- Tardará **3-5 minutos** (es normal)
- Verás mensajes como:
  ```
  [+] Pulling mysql...
  [+] Building web...
  [+] Creating preconsulta_mysql...
  [+] Creating preconsulta_web...
  ```
- Docker está descargando e instalando:
  - MySQL 8.0 (base de datos)
  - PHP 8.2 con Apache (servidor web)
  - PhpMyAdmin (administrador de BD)
  - Todas las dependencias necesarias

**Si ya lo levantaste antes:**
- Solo tardará **5-10 segundos**
- Docker reutiliza lo que ya descargó

**✅ Cuando termine con éxito verás:**
```
✔ Container preconsulta_mysql       Started
✔ Container preconsulta_web         Started  
✔ Container preconsulta_phpmyadmin  Started
```

### 4. Esperar a que la base de datos se inicialice

⏳ **IMPORTANTE:** La primera vez, espera **30-60 segundos** adicionales.

Docker está ejecutando automáticamente estos scripts:
- Crear la base de datos `centro_triaje_digital`
- Crear 12 tablas (Usuario, Paciente, Enfermero, Celador, etc.)
- Insertar datos de prueba (usuarios, prioridades, etc.)

**Puedes verificar el progreso con:**
```powershell
docker-compose logs mysql
```

Busca el mensaje: `ready for connections` ✅

### 5. ¡Abrir la aplicación!

🌐 **Abre tu navegador favorito** (Chrome, Firefox, Edge, etc.) y ve a:

```
http://localhost:8090
```

O directamente a:

```
http://localhost:8090/login.php
```

**✅ Deberías ver la pantalla de login de PreConsulta**

Si la página carga correctamente, **¡FELICIDADES! 🎉** La aplicación está funcionando.

---

## 👥 Usuarios de Prueba

Una vez que veas la pantalla de login, puedes probar la aplicación con estos usuarios:

### 🩺 Enfermeros (Personal médico):

**María González** - Enfermera de Urgencias
- 📧 Email: `maria.gonzalez@hospital.com`
- 🔑 Password: `enfermero123`
- 📦 Box asignado: Box 1
- 🏥 Especialidad: Urgencias

**Carlos Martínez** - Enfermero de Pediatría
- 📧 Email: `carlos.martinez@hospital.com`
- 🔑 Password: `enfermero123`
- 📦 Box asignado: Box 2
- 🏥 Especialidad: Pediatría

### 🚑 Celadores (Personal de apoyo):

**José Celador**
- 📧 Email: `jose.celador@hospital.com`
- 🔑 Password: `password123`

**Antonio Navarro**
- 📧 Email: `antonio.navarro@hospital.com`
- 🔑 Password: `password123`

**Francisco Molina**
- 📧 Email: `francisco.molina@hospital.com`
- 🔑 Password: `password123`

**Rosa Vázquez**
- 📧 Email: `rosa.vazquez@hospital.com`
- 🔑 Password: `password123`

### 🧑 Pacientes:

**Juan Torres**
- 📧 Email: `juan.torres@email.com`
- 🔑 Password: `password123`
- 🆔 DNI: 12345678A

**María García**
- 📧 Email: `maria.garcia@email.com`
- 🔑 Password: `password123`
- 🆔 DNI: 23456789B

**Carlos Rodríguez**
- 📧 Email: `carlos.rodriguez@email.com`
- 🔑 Password: `password123`
- 🆔 DNI: 34567890C

💡 **Tip:** Prueba con diferentes tipos de usuarios para ver las distintas interfaces de la aplicación.

---

## 🛑 Cómo Detener la Aplicación

Cuando termines de usar la aplicación y quieras detenerla:

1. Abre PowerShell en la carpeta del proyecto (igual que antes)
2. Ejecuta:

```powershell
docker-compose stop
```

Esto **detiene** los contenedores pero **mantiene todos los datos guardados** ✅

---

## 🔄 Cómo Volver a Iniciarla

La próxima vez que quieras usar la aplicación:

1. Asegúrate de que **Docker Desktop está corriendo** (icono 🐳 en la barra de tareas)
2. Abre PowerShell en la carpeta del proyecto
3. Ejecuta:
```powershell
docker-compose up -d
```
4. Espera **5-10 segundos**
5. Abre tu navegador en: `http://localhost:8090`

**¡Listo!** Todos tus datos anteriores seguirán ahí.

---

## ❓ Solución de Problemas Comunes

### ❌ Error: "docker-compose no se reconoce como comando"

**Problema:** Docker no está instalado o no está corriendo.

**Solución:**
1. Verifica que Docker Desktop esté instalado
2. Abre Docker Desktop desde el menú Inicio
3. Espera hasta que veas "Docker is running" (icono 🐳 verde)
4. Vuelve a PowerShell e intenta el comando de nuevo

---

### ❌ La página no carga (http://localhost:8090 no responde)

**Problema:** Los contenedores no se iniciaron correctamente.

**Solución paso a paso:**

1. **Verifica el estado de los contenedores:**
   ```powershell
   docker-compose ps
   ```
   
   Deberías ver 3 contenedores con estado "Up":
   - `preconsulta_mysql` → Up (healthy)
   - `preconsulta_web` → Up
   - `preconsulta_phpmyadmin` → Up

2. **Si algún contenedor no está "Up":**
   ```powershell
   docker-compose restart
   ```
   Espera 30 segundos e intenta de nuevo.

3. **Si aún no funciona, reinicia todo:**
   ```powershell
   docker-compose down
   docker-compose up -d
   ```
   Espera 60 segundos (la BD necesita tiempo para inicializarse).

4. **Si sigue sin funcionar, revisa los logs:**
   ```powershell
   docker-compose logs web
   ```
   Busca mensajes de error en rojo.

---

### ❌ Error: "port is already allocated" o "puerto ya en uso"

**Problema:** El puerto 8090, 3307 o 8091 está siendo usado por otra aplicación (probablemente XAMPP, WAMP o MAMP).

**Solución Opción 1 - Detener XAMPP/WAMP:**
1. Cierra completamente XAMPP Control Panel o WAMP
2. Detén Apache y MySQL desde esas aplicaciones
3. Vuelve a ejecutar `docker-compose up -d`

**Solución Opción 2 - Cambiar el puerto de PreConsulta:**
1. Abre el archivo `docker-compose.yml` con el Bloc de notas
2. Busca la línea que dice: `"8090:80"`
3. Cámbiala a: `"8095:80"` (o cualquier otro puerto libre)
4. Guarda el archivo
5. Ejecuta:
   ```powershell
   docker-compose down
   docker-compose up -d
   ```
6. Ahora abre: `http://localhost:8095`

**Solución Opción 3 - Verificar qué usa el puerto:**
```powershell
netstat -ano | findstr :8090
```
Esto te dirá qué aplicación está usando el puerto.

---

### ❌ Error: "Cannot connect to database" o "Connection refused"

**Problema:** MySQL no terminó de iniciarse completamente.

**Solución:**

1. **Espera un poco más (60 segundos)** - La primera vez MySQL tarda en inicializar
2. Refresca la página en el navegador (F5)
3. Si sigue el error, verifica que MySQL esté "healthy":
   ```powershell
   docker-compose ps
   ```
   Debe decir `(healthy)` junto a `preconsulta_mysql`

4. Si no dice "healthy", revisa los logs:
   ```powershell
   docker-compose logs mysql
   ```
   Busca el mensaje: `ready for connections`

5. Reinicia solo el servidor web:
   ```powershell
   docker-compose restart web
   ```

---

### ❌ Las tildes y caracteres especiales se ven mal (mar??a en vez de maría)

**Problema:** Problema de codificación UTF-8.

**Solución:**
Este problema ya está solucionado en el código. Si lo ves:
```powershell
docker-compose restart web
```

Si persiste:
```powershell
docker-compose down
docker-compose up -d
```

---

### ❌ Error: "No se puede iniciar Docker Desktop"

**Problema:** Puede ser falta de virtualización o WSL no configurado (Windows).

**Solución para Windows:**

1. **Habilitar virtualización en BIOS/UEFI:**
   - Reinicia el ordenador
   - Entra a la BIOS (generalmente F2, F10, F12 o DEL al iniciar)
   - Busca "Virtualization Technology", "VT-x" o "AMD-V"
   - Actívalo (Enable)
   - Guarda y reinicia

2. **Instalar/Actualizar WSL 2:**
   Abre PowerShell **como Administrador** y ejecuta:
   ```powershell
   wsl --install
   wsl --update
   ```
   Reinicia el ordenador después.

3. **Verificar Hyper-V (Windows Pro/Enterprise):**
   - Presiona `Windows + R`
   - Escribe: `OptionalFeatures`
   - Marca "Hyper-V" (si está disponible)
   - Reinicia

---

### ❌ Error: "docker-compose.yml not found"

**Problema:** No estás en la carpeta correcta del proyecto.

**Solución:**
1. Verifica dónde está la carpeta PreConsulta
2. Navega hasta ella con:
   ```powershell
   cd "C:\ruta\completa\a\PreConsulta"
   ```
3. Verifica con `dir` que ves el archivo `docker-compose.yml`

---

### ❌ La página muestra "503 Service Unavailable" o "Apache error"

**Problema:** Apache no se inició correctamente.

**Solución:**

1. Ver logs del contenedor web:
   ```powershell
   docker-compose logs web
   ```

2. Reiniciar el servicio web:
   ```powershell
   docker-compose restart web
   ```

3. Si no funciona, reconstruir el contenedor:
   ```powershell
   docker-compose down
   docker-compose up -d --build
   ```

---

### ❌ La página muestra código PHP (`<?php ...`) en lugar de la interfaz

**Problema:** El navegador tiene guardada en caché una versión antigua de la página (antes de que PHP se configurara correctamente).

**¿Por qué pasa esto?**
Tu navegador guardó en memoria la página cuando el servidor aún no estaba ejecutando PHP correctamente. Aunque el servidor ya funciona bien, el navegador sigue mostrando la versión en caché.

**Solución - Opción 1 (Más rápida - Modo Incógnito):**

1. Abre una ventana de incógnito/privada:
   - **Chrome/Edge:** Presiona `CTRL + SHIFT + N`
   - **Firefox:** Presiona `CTRL + SHIFT + P`
2. Ve a: `http://localhost:8090/login.php`
3. Deberías ver el formulario de login correctamente ✅

**Solución - Opción 2 (Limpiar caché):**

1. En tu navegador, presiona: `CTRL + SHIFT + DELETE`
2. Marca la opción **"Imágenes y archivos en caché"**
3. Haz clic en **"Borrar datos"** o **"Limpiar ahora"**
4. Cierra la ventana de limpieza
5. Vuelve a `http://localhost:8090/login.php`
6. Presiona `CTRL + F5` para recargar forzadamente

**Solución - Opción 3 (Recarga forzada):**

1. Ve a `http://localhost:8090/login.php`
2. Presiona `CTRL + F5` (o `CTRL + SHIFT + R`)
3. Esto fuerza al navegador a descargar la página de nuevo ignorando la caché

**Verificar que el servidor funciona correctamente:**

```powershell
# Verificar que login.php se ejecuta bien
Invoke-WebRequest http://localhost:8090/login.php -UseBasicParsing
```

Si ves HTML en el resultado (no código PHP con `<?php`), el servidor funciona bien y solo necesitas limpiar la caché del navegador.

---

## 🗑️ Empezar de Cero (Resetear Todo)

Si algo sale muy mal y quieres **eliminar todo y empezar desde cero:**

⚠️ **ADVERTENCIA:** Esto eliminará **TODOS** los datos (base de datos, configuraciones, etc.)

```powershell
# Detener y eliminar TODOS los contenedores y volúmenes
docker-compose down -v

# Volver a levantar (tardará 3-5 minutos como la primera vez)
docker-compose up -d
```

Espera 60 segundos y vuelve a abrir `http://localhost:8090`

---

## 🔧 Comandos Útiles de Mantenimiento

### Ver si está corriendo:
```powershell
docker-compose ps
```
Muestra el estado de los 3 contenedores.

### Ver los logs (qué está pasando):
```powershell
docker-compose logs
```

Ver logs en tiempo real:
```powershell
docker-compose logs -f
```
(Presiona `Ctrl + C` para salir)

### Ver logs de un servicio específico:
```powershell
docker-compose logs web      # Servidor web
docker-compose logs mysql    # Base de datos
```

### Reiniciar solo un servicio:
```powershell
docker-compose restart web      # Reinicia el servidor web
docker-compose restart mysql    # Reinicia la base de datos
```

### Ver recursos que usa Docker:
```powershell
docker stats
```
Muestra CPU, memoria y red de cada contenedor.

### Detener y eliminar (pero mantener datos):
```powershell
docker-compose down
```

### Reconstruir todo (después de cambios en código):
```powershell
docker-compose down
docker-compose up -d --build
```

---

## 🎓 Acceso a PhpMyAdmin (Administrador de Base de Datos)

Si quieres ver o editar directamente la base de datos:

**URL:** http://localhost:8091

**Credenciales:**
- 🖥️ Servidor: `mysql`
- 👤 Usuario: `preconsulta_user`
- 🔑 Contraseña: `preconsulta_pass_2024`

Desde aquí puedes:
- Ver todas las tablas
- Ejecutar consultas SQL
- Exportar/importar datos
- Ver la estructura de la base de datos

---

## 📊 Información Técnica del Proyecto

### Puertos utilizados:
| Servicio | Puerto | URL |
|----------|--------|-----|
| 🌐 Aplicación Web | 8090 | http://localhost:8090 |
| 🗄️ MySQL | 3307 | localhost:3307 (solo para conexiones externas) |
| 🛠️ PhpMyAdmin | 8091 | http://localhost:8091 |

### Tecnologías incluidas:
- 🐳 **Docker**: Contenedores para la aplicación
- 🐘 **PHP 8.2**: Lenguaje del backend
- 🌐 **Apache**: Servidor web
- 🗄️ **MySQL 8.0**: Base de datos
- 🎨 **HTML5/CSS3/JavaScript**: Frontend
- 🛠️ **PhpMyAdmin**: Administrador de BD

### Estructura de la base de datos:
- **Base de datos:** `centro_triaje_digital`
- **12 tablas:** Usuario, Paciente, Enfermero, Celador, Box, Prioridad, Episodio, Historial_Medico, Valoracion_Triaje, Anotacion, Notificacion, Asignacion_Celador
- **Charset:** UTF-8 (utf8mb4_unicode_ci)

---

## 📝 Notas Importantes

✅ **Los datos persisten:** Cuando detienes la aplicación con `docker-compose stop`, todos los datos se mantienen guardados. La próxima vez que la inicies, seguirán ahí.

✅ **Cambios en el código:** Si modificas archivos `.php`, `.html`, `.css` o `.js`, los cambios se ven inmediatamente al refrescar el navegador (no necesitas reiniciar Docker).

✅ **Base de datos automática:** La primera vez que levantas la aplicación, Docker crea automáticamente toda la base de datos con los datos de prueba. No necesitas hacer nada manual.

✅ **Múltiples ordenadores:** Puedes copiar la carpeta PreConsulta a varios ordenadores y funcionará en todos (mientras tengan Docker instalado).

✅ **Caché del navegador:** Si alguna vez ves código PHP en lugar de la página web, no es un problema del servidor. Simplemente presiona `CTRL + F5` para recargar sin caché o abre una ventana de incógnito (`CTRL + SHIFT + N` en Chrome/Edge).

⚠️ **No borres carpetas importantes:** Asegúrate de no borrar las carpetas `database`, `config`, `classes` o `api`, ya que son necesarias para que funcione la aplicación.

---

## ✅ Resumen Rápido (TL;DR)

Para alguien que ya tiene todo instalado y configurado:

```powershell
# 1. Abrir PowerShell en la carpeta del proyecto
# (Clic en barra de direcciones → escribir "powershell" → Enter)

# 2. Levantar la aplicación
docker-compose up -d

# 3. Esperar 30-60 segundos

# 4. Abrir navegador
# http://localhost:8090

# 5. Login de prueba
# Usuario: maria.gonzalez@hospital.com
# Password: enfermero123
```

**¡Eso es todo!** 🎉

---

## 🆘 ¿Necesitas Más Ayuda?

Si después de seguir esta guía y la sección de problemas comunes sigues teniendo dificultades:

### Diagnóstico completo:

```powershell
# 1. Verificar que Docker está corriendo
docker --version
docker-compose --version

# 2. Ver estado de contenedores
docker-compose ps

# 3. Ver logs completos
docker-compose logs

# 4. Ver recursos de Docker
docker stats
```

### Comprobar Docker Desktop:
1. Abre Docker Desktop
2. Ve a **Settings** → **Resources**
3. Asegúrate de tener al menos:
   - **4 GB de RAM** asignados
   - **20 GB de espacio en disco** disponible
   - **2 CPUs** asignados

### Verificar archivos del proyecto:
```powershell
# Comprobar que tienes todos los archivos necesarios
Get-ChildItem docker-compose.yml
Get-ChildItem Dockerfile
Get-ChildItem database\scripts\01_schema.sql
```

Si alguno de estos comandos da error, es posible que falten archivos en la carpeta del proyecto.

---

**📅 Última actualización:** 22 de Noviembre de 2025  
**👨‍💻 Proyecto:** PreConsulta - Centro de Triaje Digital  
**🏥 Universidad:** Ingeniería Informática - IPO  

---

**🎯 Objetivo de esta guía:** Permitir que cualquier persona, sin importar su nivel técnico, pueda ejecutar este proyecto en su ordenador siguiendo pasos simples y claros.

### Opción B: Descargar ZIP desde GitHub

1. Ve a: https://github.com/sergio-delolmo-riol/PreConsulta
2. Haz clic en el botón verde **"Code"** → **"Download ZIP"**
3. Extrae el archivo ZIP en tu carpeta deseada
4. Abre PowerShell/Terminal y navega a esa carpeta:
   ```powershell
   cd C:\Users\TuUsuario\Documents\PreConsulta
   ```

### Verificar que tienes todos los archivos

```powershell
# Listar archivos principales
dir docker-compose.yml
dir Dockerfile
dir database\scripts\schema.sql
```

Si ves estos archivos, estás listo para continuar.

---

## 🔧 Paso 1: Levantar la Aplicación

### 1. Asegúrate de estar en el directorio del proyecto

```powershell
# Windows PowerShell
cd C:\ruta\a\tu\PreConsulta

# Linux/Mac
cd /ruta/a/tu/PreConsulta
```

**Ejemplo común en Windows:**
```powershell
cd "C:\Users\TuUsuario\Documents\PreConsulta"
```

### 2. (Primera vez) Asegurar que Docker Desktop está corriendo

**Windows/Mac:**
1. Abre la aplicación **Docker Desktop**
2. Espera a que el icono de Docker en la barra de tareas/menú muestre: **"Docker is running"**
3. Si es la primera vez, Docker puede tardar 1-2 minutos en iniciar

**Linux:**
```bash
sudo systemctl start docker
sudo systemctl status docker
```

### 3. Verificar Archivos del Proyecto

Asegúrate de que existen estos archivos en el directorio:
```powershell
# Verificar archivos críticos
Get-ChildItem docker-compose.yml
Get-ChildItem Dockerfile
Get-ChildItem database\scripts\schema.sql
Get-ChildItem database\scripts\seed_data.sql
```

Si falta algún archivo, verifica que descargaste/clonaste el proyecto correctamente.

### 4. Levantar los Contenedores Docker

**Comando principal (desde el directorio del proyecto):**
```powershell
docker-compose up -d
```

**Explicación de flags:**
- `up` - Inicia los servicios definidos en docker-compose.yml
- `-d` - Ejecuta en modo detached (segundo plano)

**⏱️ Tiempo estimado de primera ejecución: 3-5 minutos**

**Este comando realizará (solo la primera vez):**
1. ⬇️ Descarga las imágenes Docker necesarias (~500MB):
   - MySQL 8.0
   - PHP 8.2 con Apache
   - PhpMyAdmin
2. 🔨 Construye la imagen personalizada desde el Dockerfile
3. 📦 Crea y arranca 3 contenedores:
   - `preconsulta_mysql` - Base de datos MySQL
   - `preconsulta_web` - Servidor web PHP/Apache
   - `preconsulta_phpmyadmin` - Interfaz de administración de BD
4. 🗄️ Ejecuta automáticamente los scripts SQL:
   - `schema.sql` - Crea las 12 tablas de la base de datos
   - `seed_data.sql` - Inserta datos de prueba (usuarios, prioridades, etc.)
5. 🌐 Configura la red interna entre contenedores

**Ejecuciones posteriores:** Solo toma 5-10 segundos (ya todo está descargado).

### 5. Verificar Estado de los Contenedores

```powershell
docker-compose ps
```

**Salida esperada:**
```
NAME                    STATUS              PORTS
preconsulta_mysql       Up (healthy)        0.0.0.0:3307->3306/tcp
preconsulta_web         Up                  0.0.0.0:8090->80/tcp
preconsulta_phpmyadmin  Up                  0.0.0.0:8091->80/tcp
```

### 5. Ver Logs (Opcional pero Recomendado)

Para verificar que todo se inició correctamente:

```powershell
# Ver todos los logs
docker-compose logs

# Ver logs en tiempo real
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs web
docker-compose logs mysql
```

**Busca estos mensajes de éxito:**
- MySQL: `ready for connections`
- Web: `Apache/2.4.x configured -- resuming normal operations`

---

## 🌐 Acceder a la Aplicación

Una vez levantados los contenedores, accede a:

### Aplicación Principal
**URL:** http://localhost:8090

**Usuarios de Prueba Disponibles:**

#### Enfermeros:
- **María González**
  - Email: `maria.gonzalez@hospital.com`
  - Password: `enfermero123`
  - Box asignado: Box 1
  - Especialidad: Urgencias

- **Carlos Martínez**
  - Email: `carlos.martinez@hospital.com`
  - Password: `enfermero123`
  - Box asignado: Box 2
  - Especialidad: Pediatría

- **Ana Fernández**
  - Email: `ana.fernandez@hospital.com`
  - Password: `enfermero123`
  - Sin box asignado
  - Especialidad: Traumatología

#### Celadores:
- **José Rodríguez**
  - Email: `jose.celador@hospital.com`
  - Password: `password123`
  - Box asignado: Box 1

- **Pedro Sánchez**
  - Email: `pedro.celador@hospital.com`
  - Password: `password123`
  - Box asignado: Box 2

#### Pacientes:
- **Juan Pérez**
  - Email: `juan.perez@email.com`
  - Password: `password123`
  - DNI: 12345678A

- **María García**
  - Email: `maria.garcia@email.com`
  - Password: `password123`
  - DNI: 23456789B

- **Carlos López**
  - Email: `carlos.lopez@email.com`
  - Password: `password123`
  - DNI: 34567890C

### PhpMyAdmin (Administración de Base de Datos)
**URL:** http://localhost:8091

**Credenciales:**
- Servidor: `mysql`
- Usuario: `preconsulta_user`
- Contraseña: `preconsulta_pass_2024`

---

## 🛠️ Comandos de Mantenimiento

### Detener los Contenedores
```powershell
docker-compose stop
```
*Detiene los contenedores pero preserva los datos*

### Detener y Eliminar Contenedores
```powershell
docker-compose down
```
*Elimina los contenedores pero mantiene los volúmenes (datos de BD)*

### Reiniciar un Servicio Específico
```powershell
docker-compose restart web
docker-compose restart mysql
```

### Ver Recursos Utilizados
```powershell
docker stats
```

### Acceder al Terminal de un Contenedor
```powershell
# Acceder al contenedor web
docker exec -it preconsulta_web bash

# Acceder al contenedor MySQL
docker exec -it preconsulta_mysql mysql -u preconsulta_user -p
# Password: preconsulta_pass_2024
```

---

## 🔄 Reconstruir la Aplicación

Si necesitas reconstruir las imágenes (después de cambios en Dockerfile o código):

### Reconstrucción Completa
```powershell
# 1. Detener y eliminar todo
docker-compose down

# 2. Reconstruir sin caché
docker-compose build --no-cache

# 3. Levantar de nuevo
docker-compose up -d
```

### Reconstrucción con Limpieza de Datos
Si también quieres resetear la base de datos:

```powershell
# Eliminar contenedores Y volúmenes
docker-compose down -v

# Levantar de nuevo (se recreará la BD desde cero)
docker-compose up -d
```

---

## 🐛 Solución de Problemas Comunes

### Error: "Port already in use"

**Problema:** El puerto 8090, 3307 o 8091 ya está en uso.

**Solución:**
```powershell
# Ver qué proceso usa el puerto
netstat -ano | findstr :8090

# Cambiar el puerto en docker-compose.yml
# Busca la línea: "8090:80" y cámbiala a "8095:80"
```

### Error: "Cannot connect to Docker daemon"

**Problema:** Docker Desktop no está corriendo.

**Solución:**
1. Abre Docker Desktop
2. Espera a que el icono de Docker en la bandeja del sistema muestre "Docker is running"
3. Vuelve a intentar el comando

### Error: "Database connection failed"

**Problema:** El contenedor web intentó conectarse antes de que MySQL estuviera listo.

**Solución:**
```powershell
# Reinicia solo el contenedor web
docker-compose restart web

# Verifica los logs
docker-compose logs mysql
```

### Error: "Unable to write to /var/www/html"

**Problema:** Problemas de permisos en el volumen compartido.

**Solución (Windows):**
1. Abre Docker Desktop → Settings → Resources → File Sharing
2. Asegúrate de que la carpeta del proyecto está compartida
3. Aplica y reinicia Docker Desktop

**Solución (Linux):**
```bash
sudo chown -R $USER:$USER .
```

### La página muestra "503 Service Unavailable"

**Problema:** Apache no se inició correctamente.

**Solución:**
```powershell
# Ver logs del contenedor web
docker-compose logs web

# Reiniciar el servicio
docker-compose restart web
```

---

## 📊 Estructura de Puertos

| Servicio | Puerto Host | Puerto Contenedor | URL |
|----------|-------------|-------------------|-----|
| Apache/PHP | 8090 | 80 | http://localhost:8090 |
| MySQL | 3307 | 3306 | mysql://localhost:3307 |
| PhpMyAdmin | 8091 | 80 | http://localhost:8091 |

---

## 🗄️ Información de la Base de Datos

### Conexión desde la Aplicación
Los contenedores se comunican internamente usando estos parámetros (ya configurados):

```
DB_HOST=mysql
DB_PORT=3306
DB_NAME=centro_triaje_digital
DB_USER=preconsulta_user
DB_PASS=preconsulta_pass_2024
```

### Conexión Externa (desde host)
Para conectarte desde herramientas como MySQL Workbench o DBeaver:

```
Host: localhost
Port: 3307
Database: centro_triaje_digital
User: preconsulta_user
Password: preconsulta_pass_2024
```

O como usuario root:
```
Host: localhost
Port: 3307
User: root
Password: root_password_2024
```

---

## ✅ Verificación de Instalación Exitosa

Después de ejecutar `docker-compose up -d`, verifica:

1. **Contenedores corriendo:**
   ```powershell
   docker-compose ps
   ```
   Todos deben mostrar estado "Up"

2. **Base de datos inicializada:**
   - Accede a http://localhost:8091 (PhpMyAdmin)
   - Login con las credenciales
   - Verifica que existe la base de datos `centro_triaje_digital`
   - Debe tener 12 tablas: Usuario, Paciente, Enfermero, Celador, Box, Prioridad, etc.

3. **Aplicación accesible:**
   - Abre http://localhost:8090
   - Debes ver la página de login de PreConsulta
   - Intenta hacer login con `maria.gonzalez@hospital.com` / `enfermero123`
   - Si accedes al dashboard del enfermero, ¡todo funciona correctamente! ✅

4. **Logs sin errores críticos:**
   ```powershell
   docker-compose logs | findstr ERROR
   ```
   No debe haber errores de conexión o PHP fatales

---

## 🤖 Prompt para IA: Instalación y Verificación Completa

Si eres una IA ejecutando estos pasos desde cero, aquí está el proceso completo:

```powershell
# ========================================
# SCRIPT COMPLETO DE INSTALACIÓN
# ========================================

# 1. Navegar a una carpeta de trabajo
cd C:\Users\$env:USERNAME\Documents

# 2. Clonar el repositorio (si no existe)
if (-not (Test-Path "PreConsulta")) {
    git clone https://github.com/sergio-delolmo-riol/PreConsulta.git
}

# 3. Entrar al directorio del proyecto
cd PreConsulta

# 4. Verificar archivos críticos
$archivos = @(
    "docker-compose.yml",
    "Dockerfile",
    "database\scripts\schema.sql",
    "database\scripts\seed_data.sql"
)

foreach ($archivo in $archivos) {
    if (Test-Path $archivo) {
        Write-Host "✅ Encontrado: $archivo"
    } else {
        Write-Host "❌ Falta: $archivo"
        exit 1
    }
}

# 5. Verificar que Docker está corriendo
$dockerRunning = docker info 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Docker no está corriendo. Inicia Docker Desktop e intenta de nuevo."
    exit 1
}
Write-Host "✅ Docker está corriendo"

# 6. Levantar servicios
Write-Host "🚀 Levantando servicios Docker..."
docker-compose up -d

# 7. Esperar a que MySQL esté listo (máximo 60 segundos)
Write-Host "⏳ Esperando a que MySQL se inicialice..."
$timeout = 60
$elapsed = 0
$ready = $false

while ($elapsed -lt $timeout -and -not $ready) {
    $health = docker inspect --format='{{.State.Health.Status}}' preconsulta_mysql 2>$null
    if ($health -eq "healthy") {
        $ready = $true
        Write-Host "✅ MySQL está listo"
    } else {
        Start-Sleep -Seconds 2
        $elapsed += 2
        Write-Host "⏳ Esperando... ($elapsed/$timeout segundos)"
    }
}

if (-not $ready) {
    Write-Host "⚠️ MySQL tardó más de lo esperado. Revisa los logs: docker-compose logs mysql"
}

# 8. Esperar 5 segundos adicionales para Apache
Start-Sleep -Seconds 5

# 9. Verificar estado de contenedores
Write-Host "`n📊 Estado de contenedores:"
docker-compose ps

# 10. Verificar logs (últimas 20 líneas, buscar errores)
Write-Host "`n🔍 Verificando logs..."
$logs = docker-compose logs --tail=20 2>&1 | Select-String "ERROR|FATAL|CRITICAL"
if ($logs) {
    Write-Host "⚠️ Se encontraron errores en los logs:"
    $logs
} else {
    Write-Host "✅ No se encontraron errores críticos"
}

# 11. Test de conectividad web
Write-Host "`n🌐 Probando conexión web..."
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8090" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Aplicación web accesible en http://localhost:8090"
    }
} catch {
    Write-Host "❌ Error al acceder a http://localhost:8090"
    Write-Host "   Verifica que el contenedor web está corriendo: docker-compose ps"
}

# 12. Test de base de datos
Write-Host "`n🗄️ Verificando base de datos..."
$tables = docker exec preconsulta_mysql mysql -u preconsulta_user -ppreconsulta_pass_2024 -D centro_triaje_digital -e "SHOW TABLES;" 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Base de datos accesible"
    Write-Host "Tablas encontradas:"
    $tables -split "`n" | Select-Object -Skip 1 | ForEach-Object { Write-Host "   - $_" }
} else {
    Write-Host "❌ Error al conectar con la base de datos"
}

# 13. Resumen final
Write-Host "`n" + "="*50
Write-Host "🎉 INSTALACIÓN COMPLETADA"
Write-Host "="*50
Write-Host "`nAccede a la aplicación:"
Write-Host "  📱 App principal:  http://localhost:8090"
Write-Host "  🗄️ PhpMyAdmin:     http://localhost:8091"
Write-Host "`nUsuarios de prueba:"
Write-Host "  👨‍⚕️ Enfermero: maria.gonzalez@hospital.com / enfermero123"
Write-Host "  🚑 Celador:    jose.celador@hospital.com / password123"
Write-Host "  🧑 Paciente:   juan.perez@email.com / password123"
Write-Host "`nComandos útiles:"
Write-Host "  • Ver logs:      docker-compose logs -f"
Write-Host "  • Detener:       docker-compose stop"
Write-Host "  • Reiniciar:     docker-compose restart"
Write-Host "  • Eliminar todo: docker-compose down -v"
Write-Host ""
```

**Criterios de éxito para la IA:**
- ✅ Todos los archivos críticos presentes
- ✅ Docker está corriendo
- ✅ Todos los contenedores en estado "Up"
- ✅ MySQL en estado "healthy"
- ✅ No hay errores FATAL/CRITICAL en logs
- ✅ HTTP 200 en http://localhost:8090
- ✅ La consulta SQL devuelve 12 tablas
- ✅ Puedes hacer login con las credenciales de prueba

---

## 🔄 Para Usuarios que Ya Tienen el Proyecto

Si ya descargaste el proyecto antes y solo quieres actualizarlo y reiniciarlo:

```powershell
# 1. Navegar al proyecto
cd C:\ruta\a\tu\PreConsulta

# 2. Actualizar código desde GitHub
git pull origin main

# 3. Detener contenedores actuales
docker-compose down

# 4. Reconstruir y levantar (con cambios)
docker-compose up -d --build

# 5. Verificar
docker-compose ps
```

---

## 📝 Notas Finales

- Los datos se persisten en volúmenes Docker, por lo que sobreviven a reinicios
- Los archivos del proyecto están montados en tiempo real, los cambios se reflejan inmediatamente
- La base de datos se inicializa solo la primera vez, los reinicios posteriores mantienen los datos
- Para resetear completamente, usa `docker-compose down -v`

---

## 🆘 Soporte

Si encuentras problemas no cubiertos en esta guía:

1. Revisa los logs completos: `docker-compose logs`
2. Verifica que Docker Desktop tiene suficiente RAM asignada (mínimo 4GB recomendado)
3. Asegúrate de que no hay otros servicios usando los puertos 8090, 3307 o 8091
4. Intenta reconstruir desde cero: `docker-compose down -v && docker-compose up -d`

---

**Última actualización:** 22 de Noviembre de 2025  
**Versión:** 1.0  
**Proyecto:** PreConsulta - Centro de Triaje Digital
