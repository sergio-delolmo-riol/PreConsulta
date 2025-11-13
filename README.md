# PreConsulta - Sistema de Triaje Digital

## Descripción

PreConsulta es una aplicación web móvil de triaje digital diseñada para optimizar y simplificar el tiempo de atención en hospitales. El sistema permite a los pacientes realizar una evaluación preliminar de sus síntomas, clasificando los casos por nivel de urgencia para mejorar la gestión de recursos hospitalarios.

## 🎯 Características Principales

### Pantalla de Inicio (Home)
- **Diseño Mobile-First**: Optimizado para dispositivos móviles con diseño responsive
- **Botón Central de Acción**: Botón circular prominente para iniciar la evaluación de triaje
- **Barra de Navegación Inferior**: Navegación fija con tres botones principales
  - 🏠 **Inicio**: Pantalla principal del sistema
  - 👤 **Perfil**: Acceso al perfil de usuario (próximamente)
  - 🚨 **Emergencia**: Llamada directa al 112

### ♿ Accesibilidad WCAG 2.0 Nivel AA

#### Cumplimiento Total
- ✅ **Contraste de color**: Todos los textos cumplen ratio 4.5:1 (texto normal) o 3:1 (texto grande)
- ✅ **Navegación por teclado**: Completamente funcional con indicadores de foco visibles
- ✅ **Etiquetas ARIA**: Todos los elementos interactivos correctamente etiquetados
- ✅ **Objetivos táctiles**: Mínimo 44x44px según WCAG
- ✅ **Estructura semántica**: HTML5 semántico con landmarks apropiados
- ✅ **Soporte de lectores de pantalla**: Anuncios dinámicos con aria-live
- ✅ **Skip navigation**: Enlace para saltar al contenido principal
- ✅ **Alto contraste**: Soporte para modo de alto contraste
- ✅ **Movimiento reducido**: Soporte para preferencia de movimiento reducido

#### Características de Accesibilidad
1. **Skip Link**: Permite a usuarios de teclado saltar al contenido principal
2. **Focus Indicators**: Indicadores de foco de 3px con contraste suficiente
3. **ARIA Labels**: Etiquetas descriptivas en todos los controles
4. **Touch Targets**: Todos los botones ≥44x44px para fácil interacción
5. **Keyboard Navigation**: Navegación completa sin ratón
6. **Screen Reader Support**: Soporte completo para lectores de pantalla

## 🎨 Diseño

### Paleta de Colores (Compatible WCAG AA)
- **Primario**: #0056b3 (contraste 7.04:1 con blanco)
- **Emergencia**: #dc3545 (contraste 4.53:1 con blanco)
- **Texto Principal**: #212529 (contraste 14.63:1 con fondo)
- **Texto Secundario**: #5a6268 (contraste 5.89:1 con fondo)
- **Fondo**: #f8f9fa

### Diseño Responsive
- **Móvil**: Desde 320px
- **Tablet**: 768px+
- **Desktop**: 1024px+

## 🚀 Uso

### Instalación
1. Clona el repositorio:
```bash
git clone https://github.com/sergio-delolmo-riol/PreConsulta.git
cd PreConsulta
```

2. Abre `index.html` en tu navegador web
   - No requiere instalación de dependencias
   - No requiere servidor (puede ejecutarse localmente)

### Navegación
- **Botón "Iniciar Evaluación"**: Comienza el proceso de triaje (próximamente)
- **Botón "Inicio"**: Página principal actual
- **Botón "Perfil"**: Acceso al perfil de usuario (en desarrollo)
- **Botón "Emergencia"**: Llamada directa al 112

## 📋 Estructura del Proyecto

```
PreConsulta/
├── index.html      # Estructura HTML5 semántica con ARIA
├── styles.css      # CSS mobile-first con custom properties
├── script.js       # JavaScript para navegación y accesibilidad
└── README.md       # Documentación del proyecto
```

## 🔒 Seguridad

- ✅ **CodeQL**: 0 vulnerabilidades detectadas
- ✅ **Sin inline handlers**: Event listeners apropiados
- ✅ **Manipulación DOM segura**: Sin uso de innerHTML para contenido dinámico

## 🛠️ Tecnologías

- **HTML5**: Estructura semántica con ARIA
- **CSS3**: Mobile-first, custom properties, media queries
- **JavaScript**: Vanilla JS, event listeners, accesibilidad

## ✅ Testing Realizado

- ✓ Pruebas visuales (móvil 375px, desktop 1920px)
- ✓ Navegación por teclado (Tab, Enter, Escape)
- ✓ Verificación de indicadores de foco
- ✓ Validación de contraste de color
- ✓ Verificación de tamaño de objetivos táctiles
- ✓ Validación de estructura HTML
- ✓ Escaneo de seguridad (CodeQL)
- ✓ Compatibilidad con lectores de pantalla

## 🎯 Próximos Pasos

- [ ] Implementar formulario de evaluación de triaje
- [ ] Desarrollar página de perfil de usuario
- [ ] Crear pantalla de resultados
- [ ] Añadir páginas adicionales del sistema
- [ ] Integración con backend para procesamiento de datos

## ⚠️ Importante

**Este sistema es una herramienta de apoyo y NO sustituye la atención médica profesional ni el criterio de profesionales de la salud. En caso de emergencia real, siempre llame al 112.**

## 📱 Compatibilidad

- Chrome (últimas 2 versiones)
- Firefox (últimas 2 versiones)
- Safari (últimas 2 versiones)
- Edge (últimas 2 versiones)
- Chrome Mobile
- Safari Mobile

## 📄 Licencia

Este proyecto es de código abierto y está disponible para uso educativo y hospitalario.

## 👥 Contribución

Las contribuciones son bienvenidas. Por favor, asegúrate de:
1. Mantener la accesibilidad WCAG 2.0 AA
2. Seguir el diseño mobile-first
3. Añadir pruebas apropiadas
4. Actualizar la documentación

## 📞 Contacto

Para más información sobre el proyecto PreConsulta, por favor contacta con el equipo de desarrollo.