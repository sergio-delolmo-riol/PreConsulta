// celador-configuracion.js
// JavaScript para la página de configuración

document.addEventListener('DOMContentLoaded', function() {
    // Sistema de notificaciones
    cargarNotificaciones();
    setInterval(cargarNotificaciones, 30000); // Actualizar cada 30 segundos

    // Event listeners para las notificaciones
    const notificationBtn = document.getElementById('btnNotificaciones');
    const notificationPanel = document.getElementById('notificationPanel');
    const closeNotifications = document.getElementById('closeNotifications');

    if (notificationBtn && notificationPanel) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationPanel.classList.toggle('show');
        });
    }

    if (closeNotifications) {
        closeNotifications.addEventListener('click', function() {
            notificationPanel.classList.remove('show');
        });
    }

    // Cerrar panel al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (notificationPanel && 
            !notificationPanel.contains(e.target) && 
            !notificationBtn.contains(e.target)) {
            notificationPanel.classList.remove('show');
        }
    });
    
    // Inicializar disponibilidad
    inicializarDisponibilidad();
});

/**
 * Carga las notificaciones del celador
 */
async function cargarNotificaciones() {
    try {
        const response = await fetch('api/get_notificaciones.php');
        
        let data;
        const text = await response.text();
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Error parsing JSON:', text);
            return;
        }

        if (data.success) {
            mostrarNotificaciones(data.data);
            actualizarBadge(data.data.length);
        }
    } catch (error) {
        console.error('Error al cargar notificaciones:', error);
    }
}

/**
 * Muestra las notificaciones en el panel
 */
function mostrarNotificaciones(notificaciones) {
    const listContainer = document.getElementById('notificationList');
    
    if (!listContainer) return;

    // Verificar que notificaciones sea un array
    if (!Array.isArray(notificaciones)) {
        listContainer.innerHTML = `
            <div class="empty-state">
                <p>No hay notificaciones nuevas</p>
            </div>
        `;
        return;
    }

    if (notificaciones.length === 0) {
        listContainer.innerHTML = `
            <div class="empty-state">
                <p>No hay notificaciones nuevas</p>
            </div>
        `;
        return;
    }

    listContainer.innerHTML = notificaciones.map(notif => `
        <div class="notification-item" data-id="${notif.id_asignacion}">
            <div class="notification-content">
                <p class="notification-title">Nuevo paciente asignado</p>
                <p class="notification-text">
                    ${notif.nombre_paciente} - ${notif.prioridad}
                </p>
                <p class="notification-time">${formatearTiempo(notif.fecha_asignacion)}</p>
            </div>
            <button class="mark-read-btn" onclick="marcarComoLeido(${notif.id_asignacion})">
                ✓
            </button>
        </div>
    `).join('');
}

/**
 * Actualiza el badge de notificaciones
 */
function actualizarBadge(count) {
    const badge = document.getElementById('notifBadge');
    if (!badge) return;

    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
}

/**
 * Marca una notificación como leída
 */
async function marcarComoLeido(idAsignacion) {
    try {
        const response = await fetch('api/marcar_leido.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_asignacion: idAsignacion })
        });

        const data = await response.json();

        if (data.success) {
            cargarNotificaciones();
        }
    } catch (error) {
        console.error('Error al marcar como leído:', error);
    }
}

/**
 * Formatea el tiempo transcurrido
 */
function formatearTiempo(fecha) {
    const ahora = new Date();
    const fechaNotif = new Date(fecha);
    const diferencia = Math.floor((ahora - fechaNotif) / 1000); // segundos

    if (diferencia < 60) {
        return 'Hace un momento';
    } else if (diferencia < 3600) {
        const minutos = Math.floor(diferencia / 60);
        return `Hace ${minutos} minuto${minutos > 1 ? 's' : ''}`;
    } else if (diferencia < 86400) {
        const horas = Math.floor(diferencia / 3600);
        return `Hace ${horas} hora${horas > 1 ? 's' : ''}`;
    } else {
        const dias = Math.floor(diferencia / 86400);
        return `Hace ${dias} día${dias > 1 ? 's' : ''}`;
    }
}

/**
 * Inicializar funcionalidad de disponibilidad
 */
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
