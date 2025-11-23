/**
 * JavaScript para la página de Buscar Pacientes - Enfermero Dashboard
 * Proyecto: PreConsulta - Centro de Triaje Digital
 * Misma funcionalidad que celador pero con acciones de enfermero
 */

let pacienteActual = null;
let notificacionesInterval = null;

// ============================================
// INICIALIZACIÓN
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    inicializarFormularioBusqueda();
    inicializarNotificaciones();
    inicializarDisponibilidad();
});

// ============================================
// BÚSQUEDA DE PACIENTE
// ============================================

function inicializarFormularioBusqueda() {
    const form = document.getElementById('formBuscarPaciente');
    const inputDNI = document.getElementById('inputDNI');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const dni = inputDNI.value.trim().toUpperCase();
        
        if (!dni) {
            showNotification('Por favor, introduce un DNI', 'warning', 4000);
            return;
        }
        
        await buscarPaciente(dni);
    });
    
    // Formatear DNI mientras se escribe
    inputDNI.addEventListener('input', function(e) {
        let value = e.target.value.toUpperCase();
        // Remover caracteres no válidos
        value = value.replace(/[^0-9A-Z]/g, '');
        // Limitar a 9 caracteres (8 números + 1 letra)
        if (value.length > 9) {
            value = value.substring(0, 9);
        }
        e.target.value = value;
    });
}

async function buscarPaciente(dni) {
    try {
        const response = await fetch(`api/buscar_paciente.php?dni=${encodeURIComponent(dni)}`);
        
        // Intentar parsear como texto primero para capturar errores HTML
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error('Error parsing JSON:', text);
            showNotification('Error del servidor. Revisa la consola para más detalles.', 'error', 6000);
            return;
        }
        
        if (!response.ok) {
            showNotification(data.message || 'Error al buscar el paciente', 'error', 5000);
            mostrarSinResultados();
            return;
        }
        
        if (data.success) {
            pacienteActual = data.data;
            mostrarPaciente(pacienteActual);
            await cargarHistorial(pacienteActual.id_paciente);
        } else {
            showNotification(data.message || 'Paciente no encontrado', 'warning', 5000);
            mostrarSinResultados();
        }
    } catch (error) {
        console.error('Error al buscar paciente:', error);
        showNotification('Error de conexión. Intenta de nuevo.', 'error', 5000);
        mostrarSinResultados();
    }
}

function mostrarPaciente(paciente) {
    // Ocultar mensaje sin resultados
    document.getElementById('mensajeSinResultados').style.display = 'none';
    
    // Mostrar sección del paciente
    document.getElementById('seccionPaciente').style.display = 'block';
    
    // Rellenar datos
    const inicial = paciente.nombre.charAt(0).toUpperCase();
    document.getElementById('pacienteAvatar').textContent = inicial;
    document.getElementById('pacienteNombre').textContent = `${paciente.nombre} ${paciente.apellidos}`;
    document.getElementById('pacienteDNI').textContent = paciente.dni;
    document.getElementById('pacienteEmail').textContent = paciente.email || 'No especificado';
    document.getElementById('pacienteTelefono').textContent = paciente.telefono || 'No especificado';
    document.getElementById('pacienteGrupo').textContent = paciente.grupo_sanguineo || 'No especificado';
    document.getElementById('pacienteAlergias').textContent = paciente.alergias || 'Ninguna';
    document.getElementById('pacienteSeguro').textContent = paciente.seguro_medico || 'No especificado';
    document.getElementById('pacienteTotalConsultas').textContent = paciente.total_consultas || 0;
}

function mostrarSinResultados() {
    // Ocultar secciones
    document.getElementById('seccionPaciente').style.display = 'none';
    document.getElementById('seccionHistorial').style.display = 'none';
    
    // Mostrar mensaje
    document.getElementById('mensajeSinResultados').style.display = 'block';
    
    pacienteActual = null;
}

// ============================================
// HISTORIAL DE CONSULTAS
// ============================================

async function cargarHistorial(idPaciente) {
    try {
        const response = await fetch(`api/get_historial_paciente.php?id_paciente=${idPaciente}`);
        
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error('Error parsing JSON:', text);
            showNotification('Error del servidor al cargar historial. Revisa la consola.', 'error', 6000);
            return;
        }
        
        if (!response.ok) {
            showNotification(data.message || 'Error al cargar el historial', 'error', 5000);
            return;
        }
        
        if (data.success) {
            mostrarHistorial(data.data.historial, data.data.total);
        } else {
            showNotification(data.message || 'Error al cargar historial', 'error', 5000);
        }
    } catch (error) {
        console.error('Error al cargar historial:', error);
        showNotification('Error de conexión al cargar historial', 'error', 5000);
    }
}

function mostrarHistorial(consultas, total) {
    const seccion = document.getElementById('seccionHistorial');
    const lista = document.getElementById('historialLista');
    const countElement = document.getElementById('historialCount');
    
    // Mostrar sección
    seccion.style.display = 'block';
    
    // Actualizar contador
    countElement.textContent = `${total} consulta${total !== 1 ? 's' : ''}`;
    
    // Limpiar lista
    lista.innerHTML = '';
    
    if (consultas.length === 0) {
        lista.innerHTML = '<p style="text-align: center; color: var(--color-text-secondary); padding: 2rem;">No hay consultas registradas</p>';
        return;
    }
    
    // Renderizar cada consulta
    consultas.forEach(consulta => {
        const item = crearItemHistorial(consulta);
        lista.appendChild(item);
    });
}

function crearItemHistorial(consulta) {
    const div = document.createElement('div');
    div.className = 'history-item';
    
    // Color del borde según prioridad
    if (consulta.color_hex) {
        div.style.borderLeftColor = consulta.color_hex;
    }
    
    // Formatear fechas
    const fechaLlegada = formatearFechaCompleta(consulta.fecha_llegada);
    const fechaAlta = consulta.fecha_alta ? formatearFechaCompleta(consulta.fecha_alta) : 'En curso';
    
    // Formatear estado para el badge
    const estadoLabel = formatearEstado(consulta.estado);
    const estadoClass = consulta.estado.replace(/_/g, '-');
    
    div.innerHTML = `
        <div class="history-item-header">
            <div class="history-item-date">
                <div class="fecha-llegada">📅 ${fechaLlegada}</div>
                ${consulta.fecha_alta ? `<div class="fecha-alta">✓ Alta: ${fechaAlta}</div>` : ''}
            </div>
            <div class="history-badges">
                ${consulta.nombre_prioridad ? `
                    <span class="badge-prioridad" style="background-color: ${consulta.color_hex}; color: white;">
                        ${consulta.nombre_prioridad}
                    </span>
                ` : ''}
                <span class="badge-estado ${estadoClass}">${estadoLabel}</span>
            </div>
        </div>
        
        <div class="history-item-content">
            ${consulta.nombre_box ? `
                <div class="history-field">
                    <div class="history-field-label">Box</div>
                    <div class="history-field-value">${consulta.nombre_box}</div>
                </div>
            ` : ''}
            
            ${consulta.nombre_celador ? `
                <div class="history-field">
                    <div class="history-field-label">Celador Asignado</div>
                    <div class="history-field-value">${consulta.nombre_celador}</div>
                </div>
            ` : ''}
            
            ${consulta.frecuencia_cardiaca ? `
                <div class="history-field">
                    <div class="history-field-label">Frecuencia Cardíaca</div>
                    <div class="history-field-value">${consulta.frecuencia_cardiaca} bpm</div>
                </div>
            ` : ''}
            
            ${consulta.presion_arterial ? `
                <div class="history-field">
                    <div class="history-field-label">Presión Arterial</div>
                    <div class="history-field-value">${consulta.presion_arterial}</div>
                </div>
            ` : ''}
            
            ${consulta.temperatura ? `
                <div class="history-field">
                    <div class="history-field-label">Temperatura</div>
                    <div class="history-field-value">${consulta.temperatura}°C</div>
                </div>
            ` : ''}
            
            ${consulta.saturacion_oxigeno ? `
                <div class="history-field">
                    <div class="history-field-label">Saturación O₂</div>
                    <div class="history-field-value">${consulta.saturacion_oxigeno}%</div>
                </div>
            ` : ''}
            
            ${consulta.motivo_consulta ? `
                <div class="motivo-consulta">
                    <div class="history-field-label">Motivo de Consulta</div>
                    <div class="history-field-value">${consulta.motivo_consulta}</div>
                </div>
            ` : ''}
            
            ${consulta.notas_adicionales ? `
                <div class="motivo-consulta">
                    <div class="history-field-label">Notas Adicionales</div>
                    <div class="history-field-value">${consulta.notas_adicionales}</div>
                </div>
            ` : ''}
        </div>
    `;
    
    return div;
}

// ============================================
// UTILIDADES
// ============================================

function formatearFechaCompleta(fecha) {
    const date = new Date(fecha);
    const opciones = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('es-ES', opciones);
}

function formatearEstado(estado) {
    const estados = {
        'espera_triaje': 'Espera Triaje',
        'en_triaje': 'En Triaje',
        'espera_atencion': 'Espera Atención',
        'en_atencion': 'En Atención',
        'alta': 'Alta',
        'derivado': 'Derivado',
        'pendiente': 'Pendiente',
        'en_curso': 'En Curso',
        'finalizado': 'Finalizado',
        'cancelado': 'Cancelado'
    };
    return estados[estado] || estado;
}

// ============================================
// NOTIFICACIONES (reutilizado del dashboard)
// ============================================

function inicializarNotificaciones() {
    const btnNotificaciones = document.getElementById('btnNotificaciones');
    const panel = document.getElementById('notificacionesPanel');
    const btnCerrar = document.getElementById('btnCerrarNotif');
    
    btnNotificaciones.addEventListener('click', function(e) {
        e.stopPropagation();
        const isVisible = panel.style.display === 'block';
        panel.style.display = isVisible ? 'none' : 'block';
    });
    
    btnCerrar.addEventListener('click', function() {
        panel.style.display = 'none';
    });
    
    document.addEventListener('click', function(e) {
        if (!panel.contains(e.target) && !btnNotificaciones.contains(e.target)) {
            panel.style.display = 'none';
        }
    });
    
    // Cargar notificaciones inmediatamente
    cargarNotificaciones();
    
    // Configurar actualización automática cada 30 segundos
    notificacionesInterval = setInterval(cargarNotificaciones, 30000);
}

async function cargarNotificaciones() {
    try {
        const response = await fetch('api/get_notificaciones.php');
        const text = await response.text();
        
        let data;
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error('Error parsing notificaciones JSON:', text);
            return;
        }
        
        if (data.success) {
            mostrarNotificaciones(data.data.notificaciones, data.data.total);
        }
    } catch (error) {
        console.error('Error al cargar notificaciones:', error);
    }
}

function mostrarNotificaciones(notificaciones, total) {
    const badge = document.getElementById('notifBadge');
    const content = document.getElementById('notificacionesContent');
    
    // Actualizar badge
    if (total > 0) {
        badge.textContent = total;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
    
    // Limpiar contenido
    content.innerHTML = '';
    
    if (notificaciones.length === 0) {
        content.innerHTML = '<p style="text-align: center; color: var(--color-text-secondary); padding: 2rem;">No hay notificaciones nuevas</p>';
        return;
    }
    
    // Renderizar notificaciones
    notificaciones.forEach(notif => {
        const item = document.createElement('div');
        item.className = 'notif-item';
        if (notif.leido === 'no' || !notif.leido) {
            item.classList.add('no-leido');
        }
        
        const tiempoTranscurrido = calcularTiempoTranscurrido(notif.fecha_llegada);
        
        item.innerHTML = `
            <div class="notif-prioridad" style="background-color: ${notif.color_hex}"></div>
            <div class="notif-content">
                <div class="notif-titulo">${notif.nombre_completo}</div>
                <div class="notif-detalle">DNI: ${notif.dni}</div>
                <div class="notif-tiempo">${tiempoTranscurrido}</div>
            </div>
        `;
        
        item.addEventListener('click', () => {
            // Redirigir al dashboard principal
            window.location.href = `enfermero-dashboard.php?episodio=${notif.id_episodio}`;
        });
        
        content.appendChild(item);
    });
}

function calcularTiempoTranscurrido(fecha) {
    const ahora = new Date();
    const fechaLlegada = new Date(fecha);
    const diff = Math.floor((ahora - fechaLlegada) / 1000 / 60);
    
    if (diff < 1) return 'Hace un momento';
    if (diff < 60) return `Hace ${diff} minuto${diff !== 1 ? 's' : ''}`;
    
    const horas = Math.floor(diff / 60);
    if (horas < 24) return `Hace ${horas} hora${horas !== 1 ? 's' : ''}`;
    
    const dias = Math.floor(horas / 24);
    return `Hace ${dias} día${dias !== 1 ? 's' : ''}`;
}

// ============================================
// DISPONIBILIDAD
// ============================================

function inicializarDisponibilidad() {
    const btnDisponibilidad = document.getElementById('toggle-disponibilidad');
    
    if (btnDisponibilidad) {
        btnDisponibilidad.addEventListener('click', async function() {
            try {
                const response = await fetch('api/toggle_disponibilidad.php', {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (data.success) {
                    // Actualizar UI
                    const text = this.querySelector('#disponibilidad-text');
                    const boxAsignado = document.getElementById('box-asignado');
                    const boxBadge = document.querySelector('.box-badge');
                    
                    if (data.disponible === 'si') {
                        this.classList.add('activo');
                        text.textContent = 'Disponible';
                        boxAsignado.textContent = data.box;
                        boxBadge.classList.remove('inactive');
                    } else {
                        this.classList.remove('activo');
                        text.textContent = 'No Disponible';
                        boxAsignado.textContent = 'Sin Box Asignado';
                        boxBadge.classList.add('inactive');
                    }
                }
            } catch (error) {
                console.error('Error al cambiar disponibilidad:', error);
            }
        });
    }
}

// Limpiar intervalo al salir de la página
window.addEventListener('beforeunload', function() {
    if (notificacionesInterval) {
        clearInterval(notificacionesInterval);
    }
});
