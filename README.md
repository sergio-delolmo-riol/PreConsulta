# PreConsulta
Proyecto: PreConsulta (Centro de Triaje Digital)

1. Introducción

PreConsulta es un proyecto para desarrollar una aplicación web progresiva (PWA) mobile-first destinada a digitalizar y optimizar el proceso de triaje en los servicios de urgencias hospitalarias.

El desarrollo de este proyecto se realizará en su mayor parte utilizando inteligencia artificial generativa de código. Este documento sirve como la guía conceptual y el conjunto de requisitos fundamentales para dirigir dicho desarrollo.

2. El Problema

El proceso de triaje en urgencias actual es a menudo ineficiente y propenso a errores. Se basa en registros manuales o semidigitales, lo que provoca:

Ineficiencia y Errores: Duplicación de tareas y errores de transcripción.

Falta de Trazabilidad: Dificultad para auditar las decisiones de prioridad.

Comunicación Deficiente: Alta ansiedad en pacientes y familiares por falta de información.

Sobrecarga del Personal: Estrés y carga cognitiva elevada para el personal sanitario.

3. La Solución: PreConsulta

PreConsulta abordará estos problemas mediante una plataforma intuitiva, accesible y centrada en el usuario que permita realizar un "pre-triaje" digital.

Objetivos Clave:

Reducir Tiempos: Agilizar el tiempo medio desde la admisión hasta la clasificación.

Mejorar la Accesibilidad: Garantizar el acceso universal, cumpliendo con las normativas más estrictas.

Aumentar la Transparencia: Ofrecer información clara al paciente y a los familiares sobre su estado y prioridad.

Reducir la Carga Cognitiva: Simplificar el flujo de trabajo para el personal (enfermería, celadores, médicos).

4. Perfiles de Usuario

El sistema está diseñado para tres perfiles de usuario principales, cada uno con necesidades y contextos distintos:

Paciente (y Familiares):

Contexto: Ansiedad, estrés, posible dolor, diversidad funcional (ej. daltonismo, baja visión, dislexia).

Necesidad: Un proceso de registro rápido, claro y tranquilizador. Información constante sobre su estado.

Enfermero/a de Triaje:

Contexto: Alta presión, multitarea, necesidad de tomar decisiones rápidas y precisas.

Necesidad: Información fiable y pre-registrada del paciente, alertas claras y una interfaz que reduzca la carga cognitiva.

Celador:

Contexto: Entorno ruidoso (70dB+), en constante movimiento, uso de dispositivos móviles.

Necesidad: Instrucciones de traslado claras, inequívocas y con confirmaciones multimodales (vibración, visual).

5. Pilar Fundamental: Accesibilidad (WCAG 2.1 Nivel AA)

La accesibilidad no es una característica opcional, sino el núcleo del diseño. El entorno de uso (estrés, ruido, diversidad funcional) exige el cumplimiento estricto de WCAG 2.1 Nivel AA.

Esto se traduce en:

Contraste Alto: Mínimo 4.5:1 para texto.

Soporte a la Diversidad Funcional: Modos específicos o diseños compatibles con daltonismo y baja visión.

Lectura Fácil: Uso de pictogramas, lenguaje claro y textos breves.

Navegación Semántica (ARIA): Uso extensivo de roles y atributos ARIA para lectores de pantalla.

Zonas Táctiles Amplias: Botones y controles de al menos 44x44 píxeles.

Feedback Multimodal: Confirmaciones visuales, sonoras y hápticas (vibración).

Captura Multimodal: Permitir la entrada de síntomas por voz, además de por texto.

6. Desarrollo con IA Generativa: Punto de Partida

El desarrollo se basará en prompts dirigidos a una IA generativa. El siguiente es el prompt base para la creación de la pantalla de inicio (Home), que podrá ser modificado y refinado en función de los requisitos de diseño y flujos de navegación identificados en la documentación.

Prompt Base:

Vamos a crear una pagina web, llamada PreConsulta, para movil sobre un triaje digital. Deberas de tener en cuenta en todo momento la accesibilidad mediante ARIA y respetar la WCAG 2 para lograr un nivel AA. Se te iran adjuntando imagenes sobre los diferentes mockups de las pantallas de la pagina web, para las que deberas de realizar el codigo de estas utilizando html, css, javascript y una Base de datos.


Análisis del Prompt Base y Siguientes Pasos

Este prompt inicial define la estructura básica de la aplicación para el paciente:

Botón Central (Aviso): Corresponde a la Tarea Crítica 1.1 (Comunicación de emergencia). Debe ser el foco principal de la página, permitiendo al paciente notificar al hospital su llegada o solicitar ayuda.

Navbar (Navegación):

Home: Vuelve a la pantalla del botón de aviso.

Perfil: Corresponde a la Tarea 1.2 (Consulta de información médica) y Tarea 1.3 (Gestión de citas).

Llamada de Emergencia: Una función de acceso rápido para contactar a los servicios de emergencia (ej. 112, 061).

El desarrollo continuará desde esta base para construir los demás flujos (captura de síntomas, visualización de estado de cola, etc.) y las interfaces para el personal sanitario (Enfermería, Celador), siempre respetando los principios de accesibilidad y el contexto de uso detallados.

En la carpeta media/pantallas se encuentran las diferentes pantallas que se deberan de implementar para la pagina web

---

## 🗄️ Base de Datos Implementada

La base de datos MySQL ha sido completamente implementada con:

### 📁 Estructura de Archivos:
- **`database/scripts/schema.sql`** - Esquema completo de 13 tablas con índices, vistas y triggers
- **`database/scripts/seed_data.sql`** - Datos de prueba (usuarios, pacientes, episodios)
- **`config/database.php`** - Configuración de conexión PHP/PDO
- **`classes/Database.php`** - Clase singleton para gestión de BD con métodos CRUD
- **`database/README_DATABASE.md`** - Documentación completa de tablas y relaciones
- **`database/INSTALL.md`** - Guía de instalación paso a paso
- **`test_connection.php`** - Script de prueba de conexión

### 🏗️ Tablas Principales:
1. **Usuario** - Tabla base para todos los usuarios
2. **Paciente, Enfermero, Celador** - Especializaciones de Usuario
3. **Prioridad** - Niveles de urgencia (5 predefinidos)
4. **Box** - Salas de atención médica
5. **Episodio_Urgencia** - Registro de visitas a urgencias
6. **Triaje** - Evaluación inicial y constantes vitales
7. **Asignacion_Celador** - Gestión de traslados
8. **Notificacion** - Sistema de alertas
9. **Log_Acciones** - Auditoría completa

### 🚀 Instalación Rápida:
```bash
# 1. Crear base de datos
mysql -u root -p < database/scripts/schema.sql

# 2. Cargar datos de prueba
mysql -u root -p < database/scripts/seed_data.sql

# 3. Configurar config/database.php con tus credenciales

# 4. Probar conexión
php test_connection.php
```

### 🔐 Usuario de Prueba:
- **Email:** juan.torres@email.com
- **Contraseña:** PreConsulta2024!

**Ver documentación completa:** [`database/README_DATABASE.md`](database/README_DATABASE.md)

---

-- ============================================
--   BASE DE DATOS CENTRO DE TRIAJE DIGITAL
-- ============================================

CREATE DATABASE IF NOT EXISTS centro_triaje_digital;
USE centro_triaje_digital;

-- ============================================
-- 1. TABLA USUARIO
-- ============================================

CREATE TABLE Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    dni VARCHAR(20) UNIQUE,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    password VARCHAR(255) NOT NULL
);

-- ============================================
-- 2. TABLA PACIENTE
-- ============================================

CREATE TABLE Paciente (
    id_paciente INT PRIMARY PRIMARY KEY,
    fecha_nacimiento DATE,
    seguro_medico VARCHAR(100),
    contacto_familiar VARCHAR(150),
    FOREIGN KEY (id_paciente) REFERENCES Usuario(id_usuario)
);

-- ============================================
-- 3. TABLA ENFERMERO (INCLUYE DOCTORES)
-- ============================================

CREATE TABLE Enfermero (
    id_enfermero INT PRIMARY KEY,
    numero_colegiado VARCHAR(50),
    especialidad VARCHAR(100),
    FOREIGN KEY (id_enfermero) REFERENCES Usuario(id_usuario)
);

-- ============================================
-- 4. TABLA CELADOR
-- ============================================

CREATE TABLE Celador (
    id_celador INT PRIMARY KEY,
    area_asignada VARCHAR(100),
    turno ENUM('mañana','tarde','noche','rotativo'),
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    FOREIGN KEY (id_celador) REFERENCES Usuario(id_usuario)
);

-- ============================================
-- 5. TABLA PRIORIDAD
-- ============================================

CREATE TABLE Prioridad (
    id_prioridad INT AUTO_INCREMENT PRIMARY KEY,
    tipo_prioridad ENUM('alta','media','baja') NOT NULL,
    color_hex CHAR(7),
    tiempo_max_atencion INT NOT NULL
);

-- ============================================
-- 6. TABLA BOX
-- ============================================

CREATE TABLE Box (
    id_box INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    estado ENUM('libre','ocupado','limpieza') DEFAULT 'libre'
);

-- ============================================
-- 7. TABLA EPISODIO DE URGENCIA
-- ============================================

CREATE TABLE Episodio_Urgencia (
    id_episodio INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    fecha_llegada DATETIME DEFAULT CURRENT_TIMESTAMP,
    box_asignado INT NULL,
    prioridad_actual INT NULL,
    tiempo_estimado_espera INT NULL,

    FOREIGN KEY (id_paciente) REFERENCES Paciente(id_paciente),
    FOREIGN KEY (box_asignado) REFERENCES Box(id_box),
    FOREIGN KEY (prioridad_actual) REFERENCES Prioridad(id_prioridad)
);

-- ============================================
-- 8. TABLA TRIAJE
-- ============================================

CREATE TABLE Triaje (
    id_triaje INT AUTO_INCREMENT PRIMARY KEY,
    id_episodio INT NOT NULL,
    id_enfermero INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    prioridad_asignada INT NOT NULL,
    nivel_consciencia VARCHAR(50),
    sintomas_texto TEXT,
    sintomas_audio_url VARCHAR(255),

    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio),
    FOREIGN KEY (id_enfermero) REFERENCES Enfermero(id_enfermero),
    FOREIGN KEY (prioridad_asignada) REFERENCES Prioridad(id_prioridad)
);

-- ============================================
-- 9. TABLA HISTORIAL CLÍNICO (SUSTITUYE REEVALUACIÓN)
-- ============================================

CREATE TABLE Historial_Clinico (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_enfermero INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    descripcion TEXT,
    diagnostico TEXT,
    tratamiento TEXT,

    FOREIGN KEY (id_paciente) REFERENCES Paciente(id_paciente),
    FOREIGN KEY (id_enfermero) REFERENCES Enfermero(id_enfermero)
);

-- ============================================
-- 10. TABLA ASIGNACIÓN DE CELADOR
-- ============================================

CREATE TABLE Asignacion_Celador (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_celador INT NOT NULL,
    id_episodio INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_finalizacion DATETIME NULL,
    estado ENUM('pendiente','en_curso','finalizado') DEFAULT 'pendiente',

    FOREIGN KEY (id_celador) REFERENCES Celador(id_celador),
    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio)
);

-- ============================================
-- 11. TABLA ATENCIÓN MÉDICA
-- ============================================

CREATE TABLE Atencion_Medica (
    id_atencion INT AUTO_INCREMENT PRIMARY KEY,
    id_episodio INT NOT NULL,
    id_enfermero INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    diagnostico TEXT,
    tratamiento TEXT,

    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio),
    FOREIGN KEY (id_enfermero) REFERENCES Enfermero(id_enfermero)
);

-- ============================================
-- 12. TABLA NOTIFICACIONES
-- ============================================

CREATE TABLE Notificacion (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_episodio INT NOT NULL,
    tipo ENUM('estado','prioridad','turno','movimiento','otro') NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio)
);

-- ============================================
-- 13. TABLA LOG DE ACCIONES
-- ============================================

CREATE TABLE Log_Acciones (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    id_episodio INT NULL,
    accion VARCHAR(255),
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_episodio) REFERENCES Episodio_Urgencia(id_episodio)
);