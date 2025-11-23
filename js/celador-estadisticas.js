/**
 * JavaScript para la página de Estadísticas - Celador Dashboard
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

let chartPorHora = null;
let chartPorPrioridad = null;
let chartPorDia = null;
let notificacionesInterval = null;

// ============================================
// INICIALIZACIÓN
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
    inicializarNotificaciones();
    inicializarDisponibilidad();
});

// ============================================
// CARGAR ESTADÍSTICAS
// ============================================

async function cargarEstadisticas() {
    try {
        const response = await fetch('api/get_estadisticas.php');
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
            showNotification(data.message || 'Error al cargar estadísticas', 'error', 5000);
            return;
        }
        
        if (data.success) {
            mostrarResumen(data.data.resumen);
            crearGraficoPorHora(data.data.consultasPorHora);
            crearGraficoPorPrioridad(data.data.consultasPorPrioridad);
            crearGraficoPorDia(data.data.consultasPorDia);
        } else {
            showNotification(data.message || 'Error al cargar estadísticas', 'error', 5000);
        }
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
        showNotification('Error de conexión al cargar estadísticas', 'error', 5000);
    }
}

// ============================================
// MOSTRAR RESUMEN
// ============================================

function mostrarResumen(resumen) {
    document.getElementById('totalConsultas').textContent = resumen.total_consultas || 0;
    document.getElementById('totalPacientes').textContent = resumen.total_pacientes || 0;
    
    const tiempoPromedio = resumen.tiempo_promedio_atencion ? 
        Math.round(resumen.tiempo_promedio_atencion) + ' min' : 
        'N/A';
    document.getElementById('tiempoPromedio').textContent = tiempoPromedio;
}

// ============================================
// GRÁFICO: CONSULTAS POR HORA
// ============================================

function crearGraficoPorHora(datos) {
    // Preparar datos por hora (0-23)
    const horas = Array.from({length: 24}, (_, i) => i);
    const prioridades = {};
    
    // Agrupar por prioridad
    datos.forEach(item => {
        if (!prioridades[item.nombre_prioridad]) {
            prioridades[item.nombre_prioridad] = {
                nombre: item.nombre_prioridad,
                color: item.color_hex || '#64748b',
                datos: new Array(24).fill(0)
            };
        }
        prioridades[item.nombre_prioridad].datos[item.hora] = parseInt(item.total);
    });
    
    // Crear datasets para Chart.js
    const datasets = Object.values(prioridades).map(prioridad => ({
        label: prioridad.nombre,
        data: prioridad.datos,
        backgroundColor: prioridad.color + '80', // 50% opacity
        borderColor: prioridad.color,
        borderWidth: 2
    }));
    
    const ctx = document.getElementById('chartPorHora').getContext('2d');
    
    if (chartPorHora) {
        chartPorHora.destroy();
    }
    
    chartPorHora = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: horas.map(h => h.toString().padStart(2, '0') + ':00'),
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: 600
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        title: function(context) {
                            return 'Hora: ' + context[0].label;
                        },
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' consultas';
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: false,
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    stacked: false,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: '#f1f5f9'
                    }
                }
            }
        }
    });
}

// ============================================
// GRÁFICO: CONSULTAS POR PRIORIDAD
// ============================================

function crearGraficoPorPrioridad(datos) {
    const labels = datos.map(item => item.nombre_prioridad);
    const values = datos.map(item => parseInt(item.total));
    const colors = datos.map(item => item.color_hex || '#64748b');
    
    const ctx = document.getElementById('chartPorPrioridad').getContext('2d');
    
    if (chartPorPrioridad) {
        chartPorPrioridad.destroy();
    }
    
    chartPorPrioridad = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.map(c => c + 'CC'), // 80% opacity
                borderColor: colors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: 600
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// ============================================
// GRÁFICO: CONSULTAS POR DÍA DE LA SEMANA
// ============================================

function crearGraficoPorDia(datos) {
    const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const values = new Array(7).fill(0);
    
    datos.forEach(item => {
        // MySQL DAYOFWEEK: 1=Domingo, 2=Lunes, ... 7=Sábado
        values[item.dia_semana - 1] = parseInt(item.total);
    });
    
    const ctx = document.getElementById('chartPorDia').getContext('2d');
    
    if (chartPorDia) {
        chartPorDia.destroy();
    }
    
    chartPorDia = new Chart(ctx, {
        type: 'line',
        data: {
            labels: diasSemana,
            datasets: [{
                label: 'Consultas',
                data: values,
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderColor: '#2563eb',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'Consultas: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: 600
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: '#f1f5f9'
                    }
                }
            }
        }
    });
}

// ============================================
// NOTIFICACIONES (reutilizado)
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
    
    cargarNotificaciones();
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
    
    if (total > 0) {
        badge.textContent = total;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
    
    content.innerHTML = '';
    
    if (notificaciones.length === 0) {
        content.innerHTML = '<p style="text-align: center; color: var(--gray-600); padding: 2rem;">No hay notificaciones nuevas</p>';
        return;
    }
    
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
            window.location.href = `celador-dashboard.php?episodio=${notif.id_episodio}`;
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

window.addEventListener('beforeunload', function() {
    if (notificacionesInterval) {
        clearInterval(notificacionesInterval);
    }
});
