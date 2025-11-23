/**
 * JavaScript para Dashboard de Celador
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const consultaCards = document.querySelectorAll('.consulta-card');
    const detallesContent = document.getElementById('detalles-content');
    const searchInput = document.getElementById('patient-search');
    const tabButtons = document.querySelectorAll('.tab-button');
    const btnDisponibilidad = document.getElementById('toggle-disponibilidad');
    const btnNotificaciones = document.getElementById('btnNotificaciones');
    const notificacionesPanel = document.getElementById('notificacionesPanel');
    const btnCerrarNotif = document.getElementById('btnCerrarNotif');
    const notificacionesBadge = document.getElementById('notificaciones-badge');
    
    // Cargar notificaciones al inicio y cada 30 segundos
    cargarNotificaciones();
    setInterval(cargarNotificaciones, 30000);
    
    // Botón de notificaciones
    if (btnNotificaciones) {
        btnNotificaciones.addEventListener('click', function(e) {
            e.stopPropagation();
            notificacionesPanel.classList.toggle('active');
        });
    }
    
    // Cerrar panel de notificaciones
    if (btnCerrarNotif) {
        btnCerrarNotif.addEventListener('click', function() {
            notificacionesPanel.classList.remove('active');
        });
    }
    
    // Cerrar notificaciones al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!notificacionesPanel.contains(e.target) && !btnNotificaciones.contains(e.target)) {
            notificacionesPanel.classList.remove('active');
        }
    });
    
    /**
     * Cargar notificaciones no leídas
     */
    async function cargarNotificaciones() {
        try {
            const response = await fetch('api/get_notificaciones.php');
            
            if (!response.ok) {
                console.error('Error HTTP:', response.status);
                return;
            }
            
            const text = await response.text();
            let result;
            
            try {
                result = JSON.parse(text);
            } catch (e) {
                console.error('Error al parsear JSON:', text.substring(0, 200));
                return;
            }
            
            if (result.success) {
                const notificaciones = result.data.notificaciones;
                const total = result.data.total;
                
                // Actualizar badge
                if (total > 0) {
                    notificacionesBadge.textContent = total;
                    notificacionesBadge.style.display = 'block';
                } else {
                    notificacionesBadge.style.display = 'none';
                }
                
                // Renderizar notificaciones
                mostrarNotificaciones(notificaciones);
            }
        } catch (error) {
            console.error('Error al cargar notificaciones:', error);
        }
    }
    
    /**
     * Mostrar notificaciones en el panel
     */
    function mostrarNotificaciones(notificaciones) {
        const content = document.getElementById('notificacionesContent');
        
        if (notificaciones.length === 0) {
            content.innerHTML = `
                <div class="notif-empty">
                    <div class="notif-empty-icon">🔔</div>
                    <p>No hay notificaciones nuevas</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        notificaciones.forEach(notif => {
            const tiempoTranscurrido = calcularTiempoTranscurrido(notif.fecha_llegada);
            const colorPrioridad = notif.color_hex || '#6b7280';
            
            html += `
                <div class="notif-item no-leido" data-episodio="${notif.id_episodio}">
                    <div class="notif-header-info">
                        <span class="notif-paciente">${notif.nombre_completo}</span>
                        <span class="notif-tiempo">${tiempoTranscurrido}</span>
                    </div>
                    <p class="notif-mensaje">Nuevo paciente asignado - DNI: ${notif.dni}</p>
                    <span class="notif-prioridad" style="background-color: ${colorPrioridad}22; color: ${colorPrioridad}; border: 1px solid ${colorPrioridad};">
                        ⚠️ ${notif.nombre_prioridad || 'Sin prioridad'}
                    </span>
                </div>
            `;
        });
        
        content.innerHTML = html;
        
        // Agregar event listeners a las notificaciones
        content.querySelectorAll('.notif-item').forEach(item => {
            item.addEventListener('click', async function() {
                const episodioId = this.dataset.episodio;
                
                // Marcar como leído
                await marcarComoLeido(episodioId);
                
                // Cargar detalles de la consulta
                cargarDetallesConsulta(episodioId);
                
                // Cerrar panel de notificaciones
                notificacionesPanel.classList.remove('active');
                
                // Recargar notificaciones
                cargarNotificaciones();
            });
        });
    }
    
    /**
     * Marcar notificación como leída
     */
    async function marcarComoLeido(episodioId) {
        try {
            await fetch('api/marcar_leido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ episodio_id: episodioId })
            });
        } catch (error) {
            console.error('Error al marcar como leído:', error);
        }
    }
    
    /**
     * Calcular tiempo transcurrido
     */
    function calcularTiempoTranscurrido(fecha) {
        const ahora = new Date();
        const llegada = new Date(fecha);
        const diff = Math.floor((ahora - llegada) / 60000); // minutos
        
        if (diff < 1) return 'Ahora mismo';
        if (diff < 60) return `Hace ${diff} min`;
        const horas = Math.floor(diff / 60);
        if (horas < 24) return `Hace ${horas}h`;
        const dias = Math.floor(horas / 24);
        return `Hace ${dias}d`;
    }
    
    // Botón de disponibilidad
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
                    
                    // Mostrar mensaje
                    showNotification(data.message, 'success', 5000);
                    
                } else {
                    showNotification(data.message || 'Error al cambiar disponibilidad', 'error', 5000);
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al cambiar disponibilidad', 'error', 5000);
            }
        });
    }
    
    // Seleccionar consulta
    consultaCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remover selección previa
            consultaCards.forEach(c => c.classList.remove('selected'));
            
            // Marcar como seleccionada
            this.classList.add('selected');
            
            // Cargar detalles
            const episodioId = this.dataset.episodio;
            cargarDetallesConsulta(episodioId);
        });
    });
    
    // Filtros de tabs
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Actualizar tabs activos
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.setAttribute('aria-selected', 'false');
            });
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            
            // Filtrar consultas
            const filter = this.dataset.filter;
            filtrarConsultas(filter);
        });
    });
    
    // Búsqueda de pacientes
    let searchTimeout;
    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            buscarPaciente(this.value);
        }, 300);
    });
    
    /**
     * Cargar detalles de una consulta
     */
    async function cargarDetallesConsulta(episodioId) {
        try {
            detallesContent.innerHTML = '<div class="loading">Cargando...</div>';
            
            const response = await fetch(`api/get_consulta_detalle.php?id=${episodioId}`);
            const data = await response.json();
            
            console.log('Respuesta API:', data); // Debug
            
            if (data.success && data.data && data.data.consulta) {
                mostrarDetalles(data.data.consulta);
            } else if (data.success && data.consulta) {
                mostrarDetalles(data.consulta);
            } else {
                detallesContent.innerHTML = `
                    <div class="empty-state">
                        <p class="error-message">${data.error || data.message || 'Error al cargar los detalles'}</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error:', error);
            detallesContent.innerHTML = `
                <div class="empty-state">
                    <p class="error-message">Error al cargar los detalles</p>
                </div>
            `;
        }
    }
    
    /**
     * Mostrar detalles de la consulta en el panel derecho
     */
    function mostrarDetalles(consulta) {
        if (!consulta) {
            detallesContent.innerHTML = `
                <div class="empty-state">
                    <p class="error-message">No se pudieron cargar los detalles</p>
                </div>
            `;
            return;
        }
        
        let evidenciaHTML = '';
        
        // Verificar si hay notas adicionales como evidencia
        if (consulta.notas_adicionales) {
            evidenciaHTML = `<p>${sanitizeHTML(consulta.notas_adicionales)}</p>`;
        } else {
            evidenciaHTML = '<p style="color: var(--gray-500);">No hay evidencia adjunta</p>';
        }
        
        // Información de signos vitales si existe triaje
        let vitalesHTML = '';
        if (consulta.frecuencia_cardiaca || consulta.presion_arterial || consulta.temperatura || consulta.saturacion_oxigeno) {
            vitalesHTML = `
                <div class="detalle-section">
                    <h3>Signos Vitales</h3>
                    <ul>
                        ${consulta.frecuencia_cardiaca ? `<li>Frecuencia Cardíaca: ${consulta.frecuencia_cardiaca} bpm</li>` : ''}
                        ${consulta.presion_arterial ? `<li>Presión Arterial: ${consulta.presion_arterial}</li>` : ''}
                        ${consulta.temperatura ? `<li>Temperatura: ${consulta.temperatura}°C</li>` : ''}
                        ${consulta.saturacion_oxigeno ? `<li>Saturación O₂: ${consulta.saturacion_oxigeno}%</li>` : ''}
                    </ul>
                </div>
            `;
        }
        
        const html = `
            <div class="detalle-section">
                <h3>Paciente</h3>
                <p class="patient-info">${sanitizeHTML(consulta.nombre_completo || 'Sin nombre')}</p>
                ${consulta.dni ? `<p class="patient-dni">DNI: ${sanitizeHTML(consulta.dni)}</p>` : ''}
            </div>
            
            <div class="detalle-section">
                <h3>Motivo de la Consulta</h3>
                <p>${sanitizeHTML(consulta.motivo_consulta || 'Sin descripción')}</p>
            </div>
            
            <div class="detalle-section">
                <h3>Evidencia Adjunta</h3>
                ${evidenciaHTML}
            </div>
            
            ${consulta.nombre_prioridad ? `
            <div class="detalle-section">
                <h3>Prioridad</h3>
                <div class="prioridad-selector">
                    <span class="status-badge ${getPrioridadClass(consulta.codigo_prioridad)}">
                        ${sanitizeHTML(consulta.nombre_prioridad)}
                    </span>
                    <button class="btn-cambiar-prioridad" data-episodio="${consulta.id_episodio}" data-prioridad-actual="${consulta.codigo_prioridad}">
                        Cambiar
                    </button>
                </div>
                <select class="select-prioridad" id="select-prioridad-${consulta.id_episodio}" style="display:none;" data-episodio="${consulta.id_episodio}">
                    <option value="">Seleccionar nueva prioridad...</option>
                </select>
            </div>
            ` : ''}
            
            ${vitalesHTML}
            
            <div class="detalle-section">
                <h3>Fecha de Llegada</h3>
                <p>${formatearFecha(consulta.fecha_llegada)}</p>
            </div>
            
            <div class="detalle-section">
                <button class="btn-finalizar" data-episodio="${consulta.id_episodio}">
                    ✓ Finalizar Consulta
                </button>
            </div>
        `;
        
        detallesContent.innerHTML = html;
        
        // Agregar evento al botón de cambiar prioridad
        const btnCambiarPrioridad = detallesContent.querySelector('.btn-cambiar-prioridad');
        if (btnCambiarPrioridad) {
            btnCambiarPrioridad.addEventListener('click', async function() {
                const episodioId = this.dataset.episodio;
                const selectPrioridad = document.getElementById(`select-prioridad-${episodioId}`);
                
                // Cargar prioridades si no están cargadas
                if (selectPrioridad.options.length === 1) {
                    await cargarPrioridades(selectPrioridad, this.dataset.prioridadActual);
                }
                
                // Mostrar/ocultar selector
                selectPrioridad.style.display = selectPrioridad.style.display === 'none' ? 'block' : 'none';
            });
        }
        
        // Agregar evento al selector de prioridad
        const selectPrioridad = detallesContent.querySelector('.select-prioridad');
        if (selectPrioridad) {
            selectPrioridad.addEventListener('change', async function() {
                if (this.value) {
                    const episodioId = this.dataset.episodio;
                    await cambiarPrioridad(episodioId, this.value);
                }
            });
        }
        
        // Agregar evento al botón de finalizar
        const btnFinalizar = detallesContent.querySelector('.btn-finalizar');
        if (btnFinalizar) {
            btnFinalizar.addEventListener('click', async function() {
                const episodioId = this.dataset.episodio;
                
                showConfirm('¿Estás seguro de que deseas finalizar esta consulta?', async () => {
                    await finalizarConsulta(episodioId);
                });
            });
        }
    }
    
    /**
     * Finalizar una consulta
     */
    async function finalizarConsulta(episodioId) {
        try {
            const response = await fetch('api/finalizar_consulta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ episodio_id: episodioId })
            });
            
            if (!response.ok) {
                showNotification('Error al comunicarse con el servidor', 'error', 5000);
                return;
            }
            
            const text = await response.text();
            let data;
            
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Error al parsear respuesta:', text.substring(0, 200));
                showNotification('Error al procesar la respuesta del servidor', 'error', 6000);
                return;
            }
            
            if (data.success) {
                showNotification(data.message || 'Consulta finalizada correctamente', 'success', 5000);
                // Recargar la página para actualizar la lista
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.error || 'Error al finalizar la consulta', 'error', 5000);
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error al finalizar la consulta', 'error', 5000);
        }
    }
    
    /**
     * Filtrar consultas por estado
     */
    function filtrarConsultas(filter) {
        consultaCards.forEach(card => {
            const estado = card.dataset.estado;
            
            if (filter === 'todas') {
                card.style.display = 'block';
            } else if (filter === 'pendientes' && estado === 'pendiente') {
                card.style.display = 'block';
            } else if (filter === 'autorizadas' && estado === 'autorizada') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    /**
     * Buscar paciente por DNI
     */
    function buscarPaciente(query) {
        if (!query.trim()) {
            consultaCards.forEach(card => card.style.display = 'block');
            return;
        }
        
        const queryLower = query.toLowerCase();
        
        consultaCards.forEach(card => {
            const dni = card.dataset.dni ? card.dataset.dni.toLowerCase() : '';
            const patientName = card.querySelector('.patient-name').textContent.toLowerCase();
            
            // Buscar tanto en DNI como en nombre
            if (dni.includes(queryLower) || patientName.includes(queryLower)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    /**
     * Obtener clase de prioridad
     */
    function getPrioridadClass(codigo) {
        if (codigo <= 2) return 'urgencia-alta';
        if (codigo === 3) return 'pendiente';
        return 'autorizada';
    }
    
    /**
     * Cargar prioridades disponibles en el selector
     */
    async function cargarPrioridades(selectElement, prioridadActual) {
        try {
            const response = await fetch('api/get_prioridades.php');
            const result = await response.json();
            
            if (result.success && result.data) {
                result.data.forEach(prioridad => {
                    if (prioridad.id_prioridad != prioridadActual) {
                        const option = document.createElement('option');
                        option.value = prioridad.id_prioridad;
                        option.textContent = `${prioridad.nombre_prioridad} - ${prioridad.tipo_prioridad}`;
                        selectElement.appendChild(option);
                    }
                });
            }
        } catch (error) {
            console.error('Error al cargar prioridades:', error);
        }
    }
    
    /**
     * Cambiar prioridad de una consulta
     */
    async function cambiarPrioridad(episodioId, nuevaPrioridad) {
        try {
            const response = await fetch('api/cambiar_prioridad.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_episodio: episodioId,
                    nueva_prioridad: nuevaPrioridad
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Prioridad actualizada correctamente', 'success', 5000);
                // Recargar la consulta para mostrar la nueva prioridad
                cargarDetallesConsulta(episodioId);
                // Recargar la lista de consultas
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Error al cambiar prioridad: ' + result.message, 'error', 6000);
            }
        } catch (error) {
            console.error('Error al cambiar prioridad:', error);
            showNotification('Error al comunicarse con el servidor', 'error', 5000);
        }
    }
    
    /**
     * Formatear fecha
     */
    function formatearFecha(fecha) {
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
    
    /**
     * Sanitizar HTML
     */
    function sanitizeHTML(str) {
        const temp = document.createElement('div');
        temp.textContent = str;
        return temp.innerHTML;
    }
    
    // Auto-seleccionar primera consulta si existe
    if (consultaCards.length > 0) {
        consultaCards[0].click();
    }
    
    // Actualización automática cada 10 segundos
    setInterval(function() {
        location.reload();
    }, 10000);
});
