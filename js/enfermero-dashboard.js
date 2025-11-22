/**
 * Dashboard JavaScript - Enfermero
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

// Variables globales
let episodioActual = null;
let historialCargado = false;

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    inicializarDashboard();
});

function inicializarDashboard() {
    // Configurar botón de disponibilidad
    const btnDisponibilidad = document.getElementById('toggle-disponibilidad');
    if (btnDisponibilidad) {
        btnDisponibilidad.addEventListener('click', toggleDisponibilidad);
    }

    // Configurar búsqueda de pacientes
    const searchInput = document.getElementById('patient-search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(buscarPacientes, 300));
    }

    // Configurar notificaciones
    const btnNotificaciones = document.getElementById('btnNotificaciones');
    const btnCerrarNotif = document.getElementById('btnCerrarNotif');
    if (btnNotificaciones) {
        btnNotificaciones.addEventListener('click', toggleNotificaciones);
    }
    if (btnCerrarNotif) {
        btnCerrarNotif.addEventListener('click', cerrarNotificaciones);
    }

    // Configurar refresh de historial
    const btnRefreshHistorial = document.getElementById('btn-refresh-historial');
    if (btnRefreshHistorial) {
        btnRefreshHistorial.addEventListener('click', () => {
            if (episodioActual) {
                cargarHistorialMedico(episodioActual);
            }
        });
    }

    // Cargar paciente asignado si existe
    const pacienteCard = document.querySelector('.paciente-card');
    if (pacienteCard) {
        episodioActual = pacienteCard.dataset.episodio;
        pacienteCard.addEventListener('click', () => mostrarDetallesPaciente(episodioActual));
        // Cargar automáticamente al iniciar
        mostrarDetallesPaciente(episodioActual);
    }

    // Cargar notificaciones
    cargarNotificaciones();
}

// ============================================
// GESTIÓN DE DISPONIBILIDAD
// ============================================
async function toggleDisponibilidad() {
    const button = document.getElementById('toggle-disponibilidad');
    const text = document.getElementById('disponibilidad-text');
    
    try {
        const response = await fetch('api/toggle_estado_enfermero.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        const data = await response.json();
        
        if (data.success) {
            mostrarNotificacion(
                data.disponible ? 'Ahora estás disponible para recibir asignaciones' : 'Has cambiado a no disponible',
                'success'
            );
            
            // Recargar la página después de 1 segundo para actualizar el box
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            mostrarNotificacion('Error al cambiar disponibilidad: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión al cambiar disponibilidad', 'error');
    }
}

// ============================================
// MOSTRAR DETALLES DEL PACIENTE
// ============================================
async function mostrarDetallesPaciente(idEpisodio) {
    episodioActual = idEpisodio;
    const detallesContent = document.getElementById('detalles-content');
    
    detallesContent.innerHTML = '<div class="loading">Cargando detalles...</div>';
    
    try {
        const response = await fetch(`api/get_paciente_detalle.php?id_episodio=${idEpisodio}`);
        const data = await response.json();
        
        if (data.success) {
            const paciente = data.paciente;
            renderizarDetallesPaciente(paciente);
            
            // Cargar historial médico
            cargarHistorialMedico(idEpisodio);
        } else {
            detallesContent.innerHTML = `
                <div class="empty-state">
                    <p>Error al cargar detalles: ${data.message}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        detallesContent.innerHTML = `
            <div class="empty-state">
                <p>Error de conexión al cargar detalles</p>
            </div>
        `;
    }
}

function renderizarDetallesPaciente(paciente) {
    const detallesContent = document.getElementById('detalles-content');
    
    const html = `
        <div class="detalle-paciente">
            <div class="detalle-header">
                <h3>${paciente.nombre} ${paciente.apellidos}</h3>
                <span class="status-badge ${getPrioridadClass(paciente.id_prioridad)}">${paciente.nombre_prioridad || 'Sin prioridad'}</span>
            </div>
            
            <div class="detalle-info">
                <div class="info-item">
                    <span class="info-label">DNI:</span>
                    <span class="info-value">${paciente.dni}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Edad:</span>
                    <span class="info-value">${calcularEdad(paciente.fecha_nacimiento)} años</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value">${paciente.telefono || 'No disponible'}</span>
                </div>
            </div>

            <div class="detalle-section">
                <h4>Motivo de Consulta</h4>
                <p>${paciente.motivo_consulta}</p>
            </div>

            ${paciente.signos_vitales ? `
            <div class="detalle-section">
                <h4>Signos Vitales</h4>
                <div class="vitales-grid">
                    ${paciente.presion_arterial ? `<div class="vital-item"><span>PA:</span> ${paciente.presion_arterial}</div>` : ''}
                    ${paciente.frecuencia_cardiaca ? `<div class="vital-item"><span>FC:</span> ${paciente.frecuencia_cardiaca} lpm</div>` : ''}
                    ${paciente.temperatura ? `<div class="vital-item"><span>Temp:</span> ${paciente.temperatura}°C</div>` : ''}
                    ${paciente.saturacion_oxigeno ? `<div class="vital-item"><span>SpO2:</span> ${paciente.saturacion_oxigeno}%</div>` : ''}
                </div>
            </div>
            ` : ''}

            <!-- Tabs para Receta e Informe -->
            <div class="action-tabs">
                <button class="tab-button-action active" data-tab="receta">Recetar Fármaco</button>
                <button class="tab-button-action" data-tab="informe">Crear Informe</button>
            </div>

            <!-- Formulario de Receta -->
            <div id="tab-receta" class="tab-content active">
                <form id="form-receta" class="form-section">
                    <div class="form-group">
                        <label class="form-label required">Nombre del Fármaco</label>
                        <input type="text" class="form-input" id="nombre_farmaco" required placeholder="Ej: Paracetamol">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Principio Activo</label>
                        <input type="text" class="form-input" id="principio_activo" placeholder="Opcional">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Dosis</label>
                            <input type="text" class="form-input" id="dosis" required placeholder="Ej: 500mg">
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Vía</label>
                            <select class="form-select" id="via_administracion" required>
                                <option value="">Seleccionar...</option>
                                <option value="oral">Oral</option>
                                <option value="intravenosa">Intravenosa</option>
                                <option value="intramuscular">Intramuscular</option>
                                <option value="subcutanea">Subcutánea</option>
                                <option value="topica">Tópica</option>
                                <option value="inhalada">Inhalada</option>
                                <option value="rectal">Rectal</option>
                                <option value="otra">Otra</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Frecuencia</label>
                            <input type="text" class="form-input" id="frecuencia" required placeholder="Ej: cada 8 horas">
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Duración</label>
                            <input type="text" class="form-input" id="duracion" required placeholder="Ej: 7 días">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Indicaciones</label>
                        <textarea class="form-textarea" id="indicaciones" placeholder="Instrucciones especiales para el paciente..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-form-secondary" onclick="limpiarFormularioReceta()">Limpiar</button>
                        <button type="submit" class="btn-form-primary">Guardar Receta</button>
                    </div>
                </form>
            </div>

            <!-- Formulario de Informe -->
            <div id="tab-informe" class="tab-content">
                <form id="form-informe" class="form-section">
                    <div class="form-group">
                        <label class="form-label required">Diagnóstico Preliminar</label>
                        <textarea class="form-textarea" id="diagnostico_preliminar" required placeholder="Describe el diagnóstico inicial..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tratamiento Aplicado</label>
                        <textarea class="form-textarea" id="tratamiento_aplicado" placeholder="Describe el tratamiento realizado..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-textarea" id="observaciones" placeholder="Notas adicionales..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Evolución</label>
                        <textarea class="form-textarea" id="evolucion" placeholder="Describe la evolución del paciente..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Derivado a</label>
                            <input type="text" class="form-input" id="derivado_a" placeholder="Especialista o servicio">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Requiere Seguimiento</label>
                            <select class="form-select" id="requiere_seguimiento">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-form-secondary" onclick="limpiarFormularioInforme()">Limpiar</button>
                        <button type="submit" class="btn-form-primary">Guardar Informe</button>
                    </div>
                </form>
            </div>

            <!-- Botones de acción de atención -->
            <div class="form-section" style="margin-top: 20px;">
                ${paciente.estado_asignacion === 'asignado' ? `
                    <button class="btn-iniciar-atencion" onclick="iniciarAtencion(${paciente.id_asignacion})">
                        Iniciar Atención
                    </button>
                ` : ''}
                ${paciente.estado_asignacion === 'atendiendo' ? `
                    <button class="btn-finalizar-atencion" onclick="finalizarAtencion(${paciente.id_asignacion})">
                        Finalizar Atención
                    </button>
                ` : ''}
            </div>
        </div>
    `;
    
    detallesContent.innerHTML = html;
    
    // Configurar tabs
    configurarTabs();
    
    // Configurar formularios
    configurarFormularios();
}

// ============================================
// CONFIGURACIÓN DE TABS
// ============================================
function configurarTabs() {
    const tabButtons = document.querySelectorAll('.tab-button-action');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Remover active de todos
            tabButtons.forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Activar el seleccionado
            this.classList.add('active');
            document.getElementById(`tab-${tabName}`).classList.add('active');
        });
    });
}

// ============================================
// CONFIGURACIÓN DE FORMULARIOS
// ============================================
function configurarFormularios() {
    // Formulario de receta
    const formReceta = document.getElementById('form-receta');
    if (formReceta) {
        formReceta.addEventListener('submit', async function(e) {
            e.preventDefault();
            await guardarReceta();
        });
    }

    // Formulario de informe
    const formInforme = document.getElementById('form-informe');
    if (formInforme) {
        formInforme.addEventListener('submit', async function(e) {
            e.preventDefault();
            await guardarInforme();
        });
    }
}

// ============================================
// GUARDAR RECETA
// ============================================
async function guardarReceta() {
    const formData = {
        id_episodio: episodioActual,
        nombre_farmaco: document.getElementById('nombre_farmaco').value,
        principio_activo: document.getElementById('principio_activo').value,
        dosis: document.getElementById('dosis').value,
        via_administracion: document.getElementById('via_administracion').value,
        frecuencia: document.getElementById('frecuencia').value,
        duracion: document.getElementById('duracion').value,
        indicaciones: document.getElementById('indicaciones').value
    };

    try {
        const response = await fetch('api/recetar_farmaco.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        
        if (data.success) {
            mostrarNotificacion('Receta guardada correctamente', 'success');
            limpiarFormularioReceta();
            cargarHistorialMedico(episodioActual);
        } else {
            mostrarNotificacion('Error al guardar receta: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión al guardar receta', 'error');
    }
}

// ============================================
// GUARDAR INFORME
// ============================================
async function guardarInforme() {
    const formData = {
        id_episodio: episodioActual,
        diagnostico_preliminar: document.getElementById('diagnostico_preliminar').value,
        tratamiento_aplicado: document.getElementById('tratamiento_aplicado').value,
        observaciones: document.getElementById('observaciones').value,
        evolucion: document.getElementById('evolucion').value,
        derivado_a: document.getElementById('derivado_a').value,
        requiere_seguimiento: document.getElementById('requiere_seguimiento').value === '1'
    };

    try {
        const response = await fetch('api/crear_informe.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        
        if (data.success) {
            mostrarNotificacion('Informe guardado correctamente', 'success');
            limpiarFormularioInforme();
            cargarHistorialMedico(episodioActual);
        } else {
            mostrarNotificacion('Error al guardar informe: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión al guardar informe', 'error');
    }
}

// ============================================
// CARGAR HISTORIAL MÉDICO
// ============================================
async function cargarHistorialMedico(idEpisodio) {
    const historialContent = document.getElementById('historial-content');
    historialContent.innerHTML = '<div class="loading">Cargando historial...</div>';

    try {
        const response = await fetch(`api/get_historial_medico.php?id_episodio=${idEpisodio}`);
        const data = await response.json();
        
        if (data.success) {
            renderizarHistorial(data.historial);
            historialCargado = true;
        } else {
            historialContent.innerHTML = `
                <div class="empty-state-sm">
                    <p>Error al cargar historial: ${data.message}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        historialContent.innerHTML = `
            <div class="empty-state-sm">
                <p>Error de conexión</p>
            </div>
        `;
    }
}

function renderizarHistorial(historial) {
    const historialContent = document.getElementById('historial-content');
    
    if (!historial || (historial.recetas.length === 0 && historial.informes.length === 0 && historial.episodios_anteriores.length === 0)) {
        historialContent.innerHTML = `
            <div class="empty-state-sm">
                <p>No hay historial médico previo</p>
            </div>
        `;
        return;
    }

    let html = '';

    // Episodios anteriores
    if (historial.episodios_anteriores && historial.episodios_anteriores.length > 0) {
        html += '<h4 style="margin-bottom: 12px; color: var(--gray-700); font-size: 14px;">Episodios Anteriores</h4>';
        historial.episodios_anteriores.forEach(ep => {
            html += `
                <div class="historial-item">
                    <div class="historial-item-header">
                        <span class="historial-item-title">${ep.motivo_consulta}</span>
                        <span class="historial-item-date">${formatearFecha(ep.fecha_llegada)}</span>
                    </div>
                    <div class="historial-item-content">
                        Estado: ${ep.estado} | Prioridad: ${ep.nombre_prioridad || 'N/A'}
                    </div>
                </div>
            `;
        });
    }

    // Informes anteriores
    if (historial.informes && historial.informes.length > 0) {
        html += '<h4 style="margin: 16px 0 12px 0; color: var(--gray-700); font-size: 14px;">Informes Médicos</h4>';
        historial.informes.forEach(inf => {
            html += `
                <div class="historial-item" style="border-left-color: var(--success-color);">
                    <div class="historial-item-header">
                        <span class="historial-item-title">Informe Médico</span>
                        <span class="historial-item-date">${formatearFecha(inf.fecha_creacion)}</span>
                    </div>
                    <div class="historial-item-content">
                        <strong>Diagnóstico:</strong> ${inf.diagnostico_preliminar}<br>
                        ${inf.tratamiento_aplicado ? `<strong>Tratamiento:</strong> ${inf.tratamiento_aplicado}` : ''}
                    </div>
                </div>
            `;
        });
    }

    // Recetas anteriores
    if (historial.recetas && historial.recetas.length > 0) {
        html += '<h4 style="margin: 16px 0 12px 0; color: var(--gray-700); font-size: 14px;">Recetas</h4>';
        historial.recetas.forEach(rec => {
            html += `
                <div class="historial-item" style="border-left-color: var(--warning-color);">
                    <div class="historial-item-header">
                        <span class="historial-item-title">${rec.nombre_farmaco}</span>
                        <span class="historial-item-date">${formatearFecha(rec.fecha_prescripcion)}</span>
                    </div>
                    <div class="historial-item-content">
                        <strong>Dosis:</strong> ${rec.dosis} | <strong>Vía:</strong> ${rec.via_administracion}<br>
                        <strong>Frecuencia:</strong> ${rec.frecuencia} | <strong>Duración:</strong> ${rec.duracion}
                    </div>
                </div>
            `;
        });
    }

    historialContent.innerHTML = html;
}

// ============================================
// INICIAR Y FINALIZAR ATENCIÓN
// ============================================
async function iniciarAtencion(idAsignacion) {
    if (!confirm('¿Deseas iniciar la atención de este paciente?')) return;

    try {
        const response = await fetch('api/iniciar_atencion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_asignacion: idAsignacion })
        });

        const data = await response.json();
        
        if (data.success) {
            mostrarNotificacion('Atención iniciada', 'success');
            location.reload();
        } else {
            mostrarNotificacion('Error: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión', 'error');
    }
}

async function finalizarAtencion(idAsignacion) {
    if (!confirm('¿Deseas finalizar la atención de este paciente? Esta acción liberará al paciente de tu asignación.')) return;

    try {
        const response = await fetch('api/finalizar_atencion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_asignacion: idAsignacion })
        });

        const data = await response.json();
        
        if (data.success) {
            mostrarNotificacion('Atención finalizada correctamente', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarNotificacion('Error: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión', 'error');
    }
}

// ============================================
// BÚSQUEDA DE PACIENTES
// ============================================
async function buscarPacientes() {
    const query = document.getElementById('patient-search').value;
    
    if (query.length < 3) {
        return;
    }

    // Esta funcionalidad se implementará en enfermero-pacientes.php
    window.location.href = `enfermero-pacientes.php?q=${encodeURIComponent(query)}`;
}

// ============================================
// NOTIFICACIONES
// ============================================
function toggleNotificaciones() {
    const panel = document.getElementById('notificacionesPanel');
    panel.classList.toggle('active');
}

function cerrarNotificaciones() {
    const panel = document.getElementById('notificacionesPanel');
    panel.classList.remove('active');
}

async function cargarNotificaciones() {
    // Implementación futura
    const content = document.getElementById('notificacionesContent');
    content.innerHTML = `
        <div class="notif-empty">
            <span class="notif-empty-icon">🔔</span>
            <p>No tienes notificaciones nuevas</p>
        </div>
    `;
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación temporal
    const notif = document.createElement('div');
    notif.className = `notification notification-${tipo}`;
    notif.textContent = mensaje;
    notif.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        background-color: ${tipo === 'success' ? 'var(--success-color)' : tipo === 'error' ? 'var(--danger-color)' : 'var(--primary-color)'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}

// ============================================
// FUNCIONES AUXILIARES
// ============================================
function limpiarFormularioReceta() {
    document.getElementById('form-receta').reset();
}

function limpiarFormularioInforme() {
    document.getElementById('form-informe').reset();
}

function calcularEdad(fechaNacimiento) {
    if (!fechaNacimiento) return 'N/A';
    const nacimiento = new Date(fechaNacimiento);
    const hoy = new Date();
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const mes = hoy.getMonth() - nacimiento.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
        edad--;
    }
    return edad;
}

function formatearFecha(fecha) {
    const date = new Date(fecha);
    const opciones = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('es-ES', opciones);
}

function getPrioridadClass(idPrioridad) {
    switch (idPrioridad) {
        case 1:
        case 2:
            return 'urgencia-alta';
        case 3:
            return 'pendiente';
        default:
            return 'autorizada';
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Añadir estilos para animaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
