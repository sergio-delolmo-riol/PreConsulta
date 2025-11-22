# Portal del Enfermero - MediConsult

## 📋 Descripción General

El Portal del Enfermero es una interfaz completa para que el personal de enfermería pueda gestionar la atención de pacientes en el centro de triaje digital. A diferencia del celador, el enfermero tiene funcionalidades avanzadas como recetar fármacos y crear informes médicos.

## ✨ Características Principales

### 🏥 Panel de Atención
- **Paciente Único**: El enfermero solo puede atender a un paciente a la vez
- **Box Asignado**: Visualización del box donde está asignado el enfermero
- **Disponibilidad**: Botón para activar/desactivar disponibilidad
- **Historial Médico**: Visualización completa del historial del paciente

### 📝 Gestión de Recetas
- Recetar fármacos con información completa:
  - Nombre del fármaco y principio activo
  - Dosis y vía de administración (oral, IV, IM, etc.)
  - Frecuencia y duración del tratamiento
  - Indicaciones especiales para el paciente

### 📄 Informes Médicos
- Crear informes detallados con:
  - Diagnóstico preliminar
  - Tratamiento aplicado
  - Observaciones y evolución
  - Derivación a especialistas si es necesario
  - Marcador de seguimiento requerido

### 🔍 Búsqueda de Pacientes
- Búsqueda por DNI o nombre
- Visualización de historial médico completo
- Acceso a episodios anteriores, recetas e informes previos

## 🗂️ Estructura de Archivos

```
PreConsulta/
├── enfermero-dashboard.php          # Dashboard principal del enfermero
├── CSS/
│   └── enfermero-dashboard.css      # Estilos específicos del enfermero
├── js/
│   └── enfermero-dashboard.js       # Lógica del dashboard
├── api/
│   ├── toggle_estado_enfermero.php  # Cambiar disponibilidad
│   ├── get_paciente_detalle.php     # Detalles del paciente
│   ├── recetar_farmaco.php          # Crear receta
│   ├── crear_informe.php            # Crear informe médico
│   ├── get_historial_medico.php     # Obtener historial completo
│   ├── iniciar_atencion.php         # Iniciar atención del paciente
│   └── finalizar_atencion.php       # Finalizar atención
└── database/scripts/
    ├── 06_enfermero_schema.sql      # Esquema de tablas
    └── 07_enfermero_test_data.sql   # Datos de prueba
```

## 🗄️ Base de Datos

### Tablas Nuevas

#### `Asignacion_Enfermero`
Controla qué paciente está asignado a cada enfermero (uno a la vez).

```sql
- id_asignacion (PK)
- id_enfermero (FK)
- id_episodio (FK)
- fecha_asignacion
- fecha_inicio_atencion
- fecha_fin_atencion
- estado: asignado | atendiendo | finalizado | cancelado
- notas_enfermero
```

**Constraint importante**: Un enfermero solo puede tener una asignación activa (`UNIQUE KEY unique_enfermero_activo`).

#### `Receta`
Almacena las recetas de fármacos prescritas por enfermeros.

```sql
- id_receta (PK)
- id_episodio (FK)
- id_enfermero (FK)
- fecha_prescripcion
- nombre_farmaco
- principio_activo
- dosis
- via_administracion: oral | intravenosa | intramuscular | subcutanea | topica | inhalada | rectal | otra
- frecuencia
- duracion
- indicaciones
- estado: activa | completada | suspendida
```

#### `Informe_Medico`
Informes médicos elaborados por enfermeros.

```sql
- id_informe (PK)
- id_episodio (FK)
- id_enfermero (FK)
- fecha_creacion
- diagnostico_preliminar
- tratamiento_aplicado
- observaciones
- evolucion
- derivado_a
- requiere_seguimiento (BOOLEAN)
```

### Modificaciones a Tablas Existentes

#### `Enfermero`
Se añadió:
- `id_box` (FK a Box) - Box asignado al enfermero

## 🚀 Instalación y Configuración

### 1. Ejecutar Scripts SQL

```bash
# En orden:
mysql -u root -p centro_triaje_digital < database/scripts/06_enfermero_schema.sql
mysql -u root -p centro_triaje_digital < database/scripts/07_enfermero_test_data.sql
```

### 2. Verificar Usuarios de Prueba

Enfermeros creados (password: usar el hash correspondiente):

| Nombre | Email | Especialidad | Box |
|--------|-------|--------------|-----|
| María González | maria.gonzalez@hospital.com | General | Box 1 |
| Carlos Martínez | carlos.martinez@hospital.com | Urgencias | Box 2 |
| Ana Fernández | ana.fernandez@hospital.com | Pediatría | Sin box |

### 3. Acceder al Portal

```
URL: http://localhost:8090/enfermero-dashboard.php
Login con credenciales de enfermero
```

## 📱 Flujo de Trabajo del Enfermero

### 1️⃣ Inicio de Sesión
- El enfermero inicia sesión con sus credenciales
- Es redirigido a `enfermero-dashboard.php`
- Ve su box asignado y estado de disponibilidad

### 2️⃣ Recibir Paciente
- El sistema asigna automáticamente un paciente cuando el enfermero está disponible
- Solo puede tener UN paciente asignado a la vez
- El paciente aparece en el panel "Mi Paciente Actual"

### 3️⃣ Iniciar Atención
- Click en el botón "Iniciar Atención"
- Cambia el estado de `asignado` a `atendiendo`
- Registra la hora de inicio

### 4️⃣ Ver Historial Médico
- Se carga automáticamente al seleccionar el paciente
- Muestra:
  - Episodios anteriores del paciente
  - Informes médicos previos
  - Recetas anteriores

### 5️⃣ Recetar Fármacos
1. Click en tab "Recetar Fármaco"
2. Rellenar formulario:
   - Nombre del fármaco (requerido)
   - Principio activo (opcional)
   - Dosis (requerido)
   - Vía de administración (requerido)
   - Frecuencia (requerido)
   - Duración (requerido)
   - Indicaciones (opcional)
3. Click en "Guardar Receta"
4. La receta se guarda y aparece en el historial

### 6️⃣ Crear Informe Médico
1. Click en tab "Crear Informe"
2. Rellenar formulario:
   - Diagnóstico preliminar (requerido)
   - Tratamiento aplicado (opcional)
   - Observaciones (opcional)
   - Evolución (opcional)
   - Derivado a (opcional)
   - Requiere seguimiento (Sí/No)
3. Click en "Guardar Informe"
4. El informe se registra en el historial

### 7️⃣ Finalizar Atención
- Click en "Finalizar Atención"
- Confirmar acción
- El paciente recibe el alta
- El enfermero queda libre para recibir otro paciente

## 🔐 Seguridad y Permisos

### Verificaciones de Seguridad
- ✅ Todas las APIs verifican autenticación (`requireAuth()`)
- ✅ Verificación de tipo de usuario (`getUserType() === 'enfermero'`)
- ✅ Validación de asignación antes de recetar o crear informes
- ✅ Solo el enfermero asignado puede acceder al paciente

### Permisos
| Acción | Enfermero | Celador | Paciente |
|--------|-----------|---------|----------|
| Ver paciente asignado | ✅ | ❌ | ❌ |
| Recetar fármacos | ✅ | ❌ | ❌ |
| Crear informes | ✅ | ❌ | ❌ |
| Ver historial médico | ✅ | ❌ | ❌* |
| Modificar prioridad | ✅ | ✅ | ❌ |

*Paciente solo ve su propio historial limitado

## 🎨 Diseño y Estética

### Colores
```css
--primary-color: #2563eb (Azul principal)
--success-color: #10b981 (Verde para acciones positivas)
--danger-color: #dc2626 (Rojo para alertas)
--warning-color: #f59e0b (Naranja para advertencias)
```

### Componentes UI
- **Sidebar**: Navegación principal con logo, perfil y menú
- **Top Bar**: Búsqueda, box asignado y notificaciones
- **Panel Izquierdo**: Paciente asignado e historial médico
- **Panel Derecho**: Detalles del paciente, tabs de receta/informe y formularios

### Responsive
- Desktop: Tres columnas (sidebar + main + detalles)
- Tablet: Dos columnas (sidebar colapsable + main)
- Mobile: Una columna (menú hamburguesa)

## 🔧 APIs Disponibles

### GET Endpoints

#### `api/get_paciente_detalle.php`
Obtiene detalles completos del paciente.

**Parámetros**:
- `id_episodio` (query string)

**Respuesta**:
```json
{
  "success": true,
  "paciente": {
    "nombre": "Juan",
    "apellidos": "Pérez",
    "dni": "12345678A",
    "motivo_consulta": "Dolor de cabeza",
    "presion_arterial": "120/80",
    "frecuencia_cardiaca": 75,
    ...
  }
}
```

#### `api/get_historial_medico.php`
Obtiene historial médico completo del paciente.

**Parámetros**:
- `id_episodio` (query string)

**Respuesta**:
```json
{
  "success": true,
  "historial": {
    "episodios_anteriores": [...],
    "informes": [...],
    "recetas": [...]
  }
}
```

### POST Endpoints

#### `api/toggle_estado_enfermero.php`
Cambia disponibilidad del enfermero.

**Body**: Ninguno

**Respuesta**:
```json
{
  "success": true,
  "disponible": true,
  "message": "Ahora estás disponible"
}
```

#### `api/recetar_farmaco.php`
Crea una nueva receta.

**Body**:
```json
{
  "id_episodio": 123,
  "nombre_farmaco": "Paracetamol 500mg",
  "principio_activo": "Paracetamol",
  "dosis": "500mg",
  "via_administracion": "oral",
  "frecuencia": "Cada 8 horas",
  "duracion": "5 días",
  "indicaciones": "Tomar con alimento"
}
```

#### `api/crear_informe.php`
Crea un informe médico.

**Body**:
```json
{
  "id_episodio": 123,
  "diagnostico_preliminar": "Gastroenteritis aguda",
  "tratamiento_aplicado": "Hidratación oral",
  "observaciones": "Paciente estable",
  "evolucion": "Mejoría progresiva",
  "derivado_a": "Medicina Interna",
  "requiere_seguimiento": true
}
```

#### `api/iniciar_atencion.php`
Inicia la atención del paciente.

**Body**:
```json
{
  "id_asignacion": 456
}
```

#### `api/finalizar_atencion.php`
Finaliza la atención y da de alta al paciente.

**Body**:
```json
{
  "id_asignacion": 456
}
```

## 🐛 Troubleshooting

### El enfermero no ve ningún paciente
- ✅ Verificar que está marcado como disponible
- ✅ Comprobar que existe una asignación en `Asignacion_Enfermero`
- ✅ Revisar que el episodio no esté finalizado

### No se pueden guardar recetas
- ✅ Verificar que el paciente está asignado al enfermero
- ✅ Comprobar que todos los campos requeridos están llenos
- ✅ Revisar logs del servidor: `error_log()`

### El historial no carga
- ✅ Verificar que existen datos previos del paciente
- ✅ Comprobar consulta SQL en `get_historial_medico.php`
- ✅ Revisar consola del navegador (F12) para errores JavaScript

## 📞 Soporte

Para dudas o problemas:
1. Revisar logs del servidor PHP
2. Inspeccionar consola del navegador (F12)
3. Verificar consultas SQL directamente en MySQL
4. Comprobar permisos de archivos PHP

## 🔄 Diferencias con el Portal del Celador

| Característica | Celador | Enfermero |
|----------------|---------|-----------|
| **Pacientes simultáneos** | Múltiples | Solo 1 |
| **Recetar fármacos** | ❌ | ✅ |
| **Crear informes** | ❌ | ✅ |
| **Ver historial completo** | ❌ | ✅ |
| **Modificar prioridad** | ✅ | ✅ |
| **Asignar a box** | ✅ | ❌ |
| **Finalizar consulta** | ✅ | ✅ (con alta) |

## 📝 Notas Adicionales

- **Constraint de un paciente**: La tabla `Asignacion_Enfermero` tiene un constraint único que impide que un enfermero tenga más de una asignación activa.
- **Historial persistente**: Todas las recetas e informes quedan registrados permanentemente en el historial del paciente.
- **Auditoría**: Cada acción registra el `id_enfermero` que la realizó y la fecha/hora.
- **Escalabilidad**: El sistema soporta múltiples enfermeros trabajando simultáneamente, cada uno con su paciente asignado.

---

**Versión**: 1.0  
**Fecha**: Noviembre 2025  
**Proyecto**: PreConsulta - Centro de Triaje Digital
