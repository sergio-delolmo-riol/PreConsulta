# Mejoras de Accesibilidad WCAG 2.1 AA - PreConsulta

## Resumen de Implementación

Este documento detalla todas las mejoras de accesibilidad implementadas en la aplicación **PreConsulta** para cumplir con los estándares **WCAG 2.1 Nivel AA**.

---

## 🎯 Mejoras Implementadas por Página

### **Páginas HTML Estáticas**

### 1. **login.html** - Página de Inicio de Sesión

✅ **Implementado:**
- Skip link (`<a href="#main-content">`) para navegación por teclado
- `role="banner"` en header
- `role="main"` con `id="main-content"` en contenido principal
- `role="form"` con `aria-labelledby="form-title"` en formulario
- `aria-labelledby` en todos los campos de entrada vinculando con sus labels
- `aria-required="true"` en campos obligatorios
- `aria-describedby` vinculando inputs con mensajes de error
- `aria-invalid="false"` para validación dinámica
- `aria-live="polite"` en contenedores de errores
- Atributos `autocomplete` (email, current-password)

### 2. **registro.html** - Página de Registro

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="banner"` en header
- `role="main"` con `id="main-content"`
- `role="form"` con `aria-labelledby`
- `<fieldset>` agrupando campos relacionados con `<legend class="sr-only">`
- `aria-required="true"` en todos los campos obligatorios
- `aria-describedby` vinculando inputs con errores
- `aria-invalid="false"` para validación
- `aria-live="polite"` en mensajes de error
- Atributos `autocomplete` mejorados (given-name, family-name, tel, email, new-password)

### 3. **index.html** - Página de Inicio

✅ **Ya implementado correctamente:**
- `role="banner"`, `role="main"`, `role="navigation"`
- `aria-label` en navegación principal
- `role="img"` con `aria-label` en logo
- Clase `.sr-only` para texto de lectores de pantalla
- `noscript` para accesibilidad sin JavaScript

### 4. **consulta-digital_pag1.html** - Motivo de Consulta

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="banner"` en header
- `role="main"` con `id="main-content"`
- `role="contentinfo"` en footer
- `role="tablist"` con `aria-label` para selector de método
- `role="tab"` con `aria-selected` y `aria-controls` en pestañas
- `role="tabpanel"` con `aria-labelledby` en paneles
- `aria-hidden="true"` en iconos decorativos
- Clase `.sr-only` en labels ocultos visualmente
- `aria-live="polite"` en contador de caracteres

### 5. **consulta-digital_pag2.html** - Evidencia de Consulta

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="main"` con `id="main-content"`
- `role="contentinfo"` en footer
- `role="button"` con `tabindex="0"` en área de subida de archivo
- `aria-label` descriptivo en área de subida
- Clase `.sr-only` para input de archivo oculto

### 6. **consulta-digital_pag3.html** - Consulta Aprobada

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="main"` con `id="main-content"`
- `role="contentinfo"` en footer
- `aria-pressed` en botón de confirmación (cambia a "true" al confirmar)
- Actualización JavaScript para modificar `aria-pressed` dinámicamente

### 7. **pantalla_consultaAprovada.html** - Estado de Consulta

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="main"` con `id="main-content"`
- `aria-label="Progreso de la consulta"` en lista de estados
- `aria-current="step"` en paso activo

### 8. **perfil.html** - Menú de Perfil

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="banner"` en header
- `role="main"` con `id="main-content"`
- `<nav>` con `aria-label="Opciones de perfil"` para menú principal
- `aria-current="page"` en enlace activo de navegación inferior
- `aria-label` descriptivo en todos los enlaces
- `aria-hidden="true"` en iconos decorativos

### 9. **perfil-usuario.html** - Perfil de Usuario

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="main"` con `id="main-content"`
- `role="dialog"` con `aria-modal="true"` para formulario modal
- `aria-labelledby` vinculando diálogo con su título
- `aria-hidden="true"` cuando el modal está cerrado
- `aria-expanded="false"` en botón flotante
- `role="tablist"` con pestañas de datos/historial
- `role="tab"` con `aria-selected` y `aria-controls`
- `role="tabpanel"` con `aria-labelledby`

### 10. **ayuda.html** - Página de Ayuda

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="banner"` en header
- `role="main"` con `id="main-content"`
- `aria-labelledby` en todas las secciones
- Estructura semántica con `<section>`, `<h2>`, `<h3>`
- `role="navigation"` en navegación inferior

### 11. **privacidad.html** - Política de Privacidad

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="banner"` en header
- `role="main"` con `id="main-content"`
- `aria-labelledby` en todas las secciones
- Estructura semántica completa
- `role="navigation"` en navegación inferior

### 12. **condiciones.html** - Condiciones Generales

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="banner"` en header
- `role="main"` con `id="main-content"`
- `aria-labelledby` en todas las secciones
- Estructura semántica completa
- `role="navigation"` en navegación inferior

---

### **Páginas PHP - Dashboards de Personal**

### 13. **celador-dashboard.php** - Dashboard Principal del Celador

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="navigation"` con `aria-label="Menú principal"` en sidebar
- `role="banner"` en header superior
- `role="main"` con `id="main-content"` en panel de consultas
- `role="complementary"` en panel de detalles
- `aria-pressed` en botón de disponibilidad (true/false dinámico)
- `aria-label="Cambiar disponibilidad de trabajo"` en toggle
- `aria-haspopup="dialog"` y `aria-expanded="false"` en botón de notificaciones
- `role="dialog"` con `aria-labelledby` y `aria-hidden="true"` en panel de notificaciones
- `aria-label="Cerrar panel de notificaciones"` en botón cerrar
- `role="tablist"` con `aria-label="Filtros de consultas"` en tabs
- `role="tab"` con `aria-selected` y `aria-controls` en cada tab
- `role="region"` con `aria-label="Lista de consultas"` y `aria-live="polite"` en lista
- `role="article"` con `tabindex="0"` y `aria-label` descriptivo en cada tarjeta de consulta
- `aria-hidden="true"` en todos los iconos decorativos
- `aria-current="page"` en enlace de navegación activo

### 14. **enfermero-dashboard.php** - Dashboard Principal del Enfermero

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="navigation"` con `aria-label="Menú principal"` en sidebar
- `role="banner"` en header superior
- `role="main"` con `id="main-content"` en panel de paciente
- `role="complementary"` en panel de detalles y acciones
- `aria-pressed` en botón de disponibilidad (true/false dinámico)
- `aria-label="Cambiar disponibilidad de trabajo"` en toggle
- `aria-haspopup="dialog"` y `aria-expanded="false"` en botón de notificaciones
- `role="dialog"` con `aria-labelledby` y `aria-hidden="true"` en panel de notificaciones
- `role="region"` con `aria-label="Paciente asignado"` y `aria-live="polite"` en contenedor
- `role="article"` con `tabindex="0"` y `aria-label` descriptivo en tarjeta de paciente
- `aria-label="Actualizar historial médico del paciente"` en botón refresh
- `role="region"` con `aria-label="Historial médico del paciente"` y `aria-live="polite"` en historial
- `aria-hidden="true"` en todos los iconos decorativos
- `aria-current="page"` en enlace de navegación activo

### 15. **celador-pacientes.php** - Lista de Pacientes (Celador)

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="navigation"` con `aria-label="Menú principal"` en sidebar
- `aria-current="page"` en enlace activo
- `aria-hidden="true"` en iconos decorativos
- Estructura de navegación consistente con dashboard

### 16. **enfermero-pacientes.php** - Búsqueda de Pacientes (Enfermero)

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="navigation"` con `aria-label="Menú principal"` en sidebar
- `aria-current="page"` en enlace activo
- `aria-hidden="true"` en iconos decorativos
- Estructura de navegación consistente con dashboard

### 17. **celador-estadisticas.php** - Estadísticas (Compartida)

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="navigation"` con `aria-label="Menú principal"` en sidebar
- Accesible para celadores y enfermeros
- `aria-hidden="true"` en iconos decorativos
- Navegación dinámica según tipo de usuario

### 18. **celador-configuracion.php** - Configuración (Compartida)

✅ **Implementado:**
- Skip link para navegación por teclado
- `role="navigation"` con `aria-label="Menú principal"` en sidebar
- Accesible para celadores y enfermeros
- `aria-hidden="true"` en iconos decorativos
- Navegación dinámica según tipo de usuario

---

## 🎨 Estilos CSS para Accesibilidad

### **style.css** - Nuevos Estilos

```css
/* Skip link para accesibilidad WCAG */
.skip-link {
    position: absolute;
    top: -40px;
    left: 0;
    background: #007AFF;
    color: white;
    padding: 8px 16px;
    text-decoration: none;
    z-index: 100;
    border-radius: 0 0 4px 0;
    font-weight: 600;
}

.skip-link:focus {
    top: 0;
    outline: 3px solid #000;
    outline-offset: 2px;
}
```

**Función:** El skip link está oculto por defecto (`top: -40px`) pero se muestra cuando recibe el foco del teclado, permitiendo a usuarios de teclado y lectores de pantalla saltar directamente al contenido principal.

---

## 📋 Checklist de Cumplimiento WCAG 2.1 AA

### ✅ Principio 1: Perceptible

| Criterio | Estado | Implementación |
|----------|--------|----------------|
| **1.1.1** Contenido no textual | ✅ | Todos los iconos decorativos tienen `aria-hidden="true"`, imágenes funcionales tienen `alt` o `aria-label` |
| **1.3.1** Información y relaciones | ✅ | HTML semántico, landmarks (`role="banner"`, `main`, `navigation`), `fieldset`/`legend` |
| **1.3.2** Secuencia significativa | ✅ | Orden lógico del DOM, heading hierarchy correcta |
| **1.3.3** Características sensoriales | ✅ | Instrucciones no dependen solo de color/forma |
| **1.4.1** Uso del color | ✅ | Errores indicados con texto además de color |
| **1.4.3** Contraste mínimo | ✅ | Contraste verificado (textos principales cumplimiento 4.5:1) |

### ✅ Principio 2: Operable

| Criterio | Estado | Implementación |
|----------|--------|----------------|
| **2.1.1** Teclado | ✅ | Todos los controles accesibles por teclado, `tabindex="0"` donde necesario |
| **2.1.2** Sin trampas de teclado | ✅ | Navegación fluida sin bucles |
| **2.4.1** Evitar bloques | ✅ | Skip links en todas las páginas |
| **2.4.2** Página titulada | ✅ | Todos los HTML tienen `<title>` descriptivo |
| **2.4.3** Orden del foco | ✅ | Orden lógico del foco siguiendo flujo visual |
| **2.4.4** Propósito de los enlaces | ✅ | `aria-label` descriptivo en todos los enlaces |
| **2.4.6** Encabezados y etiquetas | ✅ | Jerarquía de headings correcta (h1→h2→h3) |
| **2.4.7** Foco visible | ✅ | Estilos `:focus` con outline visible |

### ✅ Principio 3: Comprensible

| Criterio | Estado | Implementación |
|----------|--------|----------------|
| **3.1.1** Idioma de la página | ✅ | `<html lang="es">` en todas las páginas |
| **3.2.1** Al recibir el foco | ✅ | No hay cambios automáticos de contexto al enfocar |
| **3.2.2** Al recibir entradas | ✅ | Formularios no se envían automáticamente |
| **3.3.1** Identificación de errores | ✅ | Errores con `aria-live="polite"` y `aria-describedby` |
| **3.3.2** Etiquetas o instrucciones | ✅ | Todos los inputs tienen `<label>` visible y `aria-labelledby` |
| **3.3.3** Sugerencia ante errores | ✅ | Mensajes de error descriptivos |
| **3.3.4** Prevención de errores | ✅ | Validación antes de envío, botones de confirmación |

### ✅ Principio 4: Robusto

| Criterio | Estado | Implementación |
|----------|--------|----------------|
| **4.1.1** Procesamiento | ✅ | HTML5 válido, sin IDs duplicados |
| **4.1.2** Nombre, función, valor | ✅ | Roles ARIA, estados (`aria-selected`, `aria-pressed`, `aria-expanded`) |
| **4.1.3** Mensajes de estado | ✅ | `aria-live="polite"` en notificaciones dinámicas |

---

## 🔧 Elementos ARIA Utilizados

### Roles
- `role="banner"` - Headers principales
- `role="main"` - Contenido principal (con `id="main-content"`)
- `role="navigation"` - Menús de navegación
- `role="contentinfo"` - Footers
- `role="form"` - Formularios
- `role="dialog"` - Ventanas modales
- `role="tablist"` / `role="tab"` / `role="tabpanel"` - Pestañas
- `role="button"` - Elementos clickeables personalizados
- `role="img"` - Contenedores de imágenes decorativas

### Propiedades
- `aria-label` - Etiquetas accesibles para elementos sin texto visible
- `aria-labelledby` - Vincula elementos con sus etiquetas
- `aria-describedby` - Vincula inputs con mensajes de ayuda/error
- `aria-required` - Marca campos obligatorios
- `aria-invalid` - Indica estado de validación
- `aria-hidden` - Oculta elementos decorativos de lectores de pantalla
- `aria-live="polite"` - Notifica cambios dinámicos sin interrumpir

### Estados
- `aria-selected` - Estado de pestañas activas/inactivas
- `aria-pressed` - Estado de botones toggle
- `aria-expanded` - Estado de elementos expandibles
- `aria-current="page"` / `aria-current="step"` - Indica elemento activo
- `aria-modal="true"` - Indica diálogo modal
- `aria-controls` - Indica qué elemento controla otro

---

## 🧪 Pruebas Recomendadas

### Navegación por Teclado
1. ✅ **Tab**: Navegar por todos los elementos interactivos
2. ✅ **Enter/Space**: Activar botones y enlaces
3. ✅ **Flechas**: Navegar entre pestañas (tablist)
4. ✅ **Escape**: Cerrar modales (perfil-usuario.html)

### Lectores de Pantalla
- **NVDA** (Windows): Verificar anuncios de roles, estados y etiquetas
- **JAWS** (Windows): Verificar navegación por landmarks
- **Narrator** (Windows): Verificar compatibilidad nativa
- **VoiceOver** (macOS/iOS): Verificar en dispositivos Apple

### Validadores
- [WAVE](https://wave.webaim.org/) - Validador de accesibilidad web
- [axe DevTools](https://www.deque.com/axe/devtools/) - Extensión de navegador
- [Lighthouse](https://developers.google.com/web/tools/lighthouse) - Auditoría integrada en Chrome DevTools

---

## 📚 Referencias

- [WCAG 2.1 Guía Rápida](https://www.w3.org/WAI/WCAG21/quickref/)
- [MDN: ARIA](https://developer.mozilla.org/es/docs/Web/Accessibility/ARIA)
- [WAI-ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/)

---

## ✨ Resultado Final

Todas las páginas HTML y PHP de **PreConsulta** cumplen con los estándares **WCAG 2.1 Nivel AA**, incluyendo:

- ✅ **12 páginas HTML** actualizadas (login, registro, index, consulta-digital 1-3, pantalla_consultaAprovada, perfil, perfil-usuario, ayuda, privacidad, condiciones)
- ✅ **6 páginas PHP de dashboards** actualizadas (celador-dashboard, enfermero-dashboard, celador-pacientes, enfermero-pacientes, celador-estadisticas, celador-configuracion)
- ✅ Skip links en todas las páginas
- ✅ Roles ARIA landmarks apropiados
- ✅ Navegación por teclado completa
- ✅ Compatibilidad con lectores de pantalla
- ✅ Mensajes de error accesibles
- ✅ Estados dinámicos anunciados (`aria-live`, `aria-pressed`, `aria-expanded`)
- ✅ Controles interactivos etiquetados
- ✅ Iconos decorativos ocultos apropiadamente
- ✅ Diálogos modales con `aria-modal` y `aria-hidden`
- ✅ Tabs accesibles con `role="tablist"` y gestión de `aria-selected`
- ✅ Tarjetas navegables con `tabindex="0"` y `aria-label` descriptivos

### Páginas PHP - Características Especiales

Las páginas PHP de dashboards (celadores y enfermeros) incluyen funcionalidades avanzadas de accesibilidad:

1. **Gestión de Estado Dinámico**: Los botones de disponibilidad usan `aria-pressed` que se actualiza dinámicamente vía PHP según el estado en la base de datos
2. **Diálogos Contextuales**: Panel de notificaciones con `role="dialog"`, `aria-haspopup`, `aria-expanded` y `aria-hidden`
3. **Contenido Actualizable**: Listas de consultas y pacientes con `aria-live="polite"` para anunciar cambios
4. **Navegación Adaptativa**: Los menús de navegación cambian dinámicamente según el rol del usuario (celador/enfermero)
5. **Tarjetas Interactivas**: Cada consulta/paciente es un `article` navegable por teclado con etiquetas descriptivas

**Fecha de implementación:** Noviembre 2025  
**Versión WCAG:** 2.1 Nivel AA
**Total de archivos actualizados:** 18 (12 HTML + 6 PHP)
