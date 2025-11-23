<?php
/**
 * Consulta Digital - Página 3: Consulta Aprobada
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

require_once 'config/session_manager.php';
require_once 'config/helpers.php';

// Verificar autenticación
requireAuth();
checkSession();

// Valores por defecto mientras se carga la información
$urgencia = "CARGANDO...";
$tiempoEspera = "Calculando...";
$pacientesDelante = "-";
$celadorInfo = null;

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Aprobada - PreConsulta</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="full-height-body">

    <header class="consult-header" role="banner">
        <div class="header-content">
            <div>
                <h1 class="approved-title">
                    Consulta aprobada.
                </h1>
                <p>Su consulta ha sido aprobada, puede dirigirse a su hospital recurrente.</p>
            </div>
            <a href="index.php" class="close-button" aria-label="Cerrar y volver al inicio">×</a>
        </div>
    </header>

    <main class="consult-main approved-main" role="main">
        <hr class="status-divider">
        <div class="info-section">
            <h3 class="info-label">Grado urgencia:</h3>
            <div class="info-box urgency-box">
                <span class="info-value"><?= sanitize($urgencia) ?></span>
            </div>
        </div>

        <div class="info-section">
            <h3 class="info-label">Tiempo de espera medio en el hospital:</h3>
            <div class="info-box time-box">
                <span class="info-value"><?= sanitize($tiempoEspera) ?></span>
            </div>
        </div>

        <div class="info-section">
            <h3 class="info-label">Número de pacientes delante suya:</h3>
            <div class="info-box patients-box">
                <span class="info-value" id="pacientes-delante"><?= sanitize($pacientesDelante) ?></span>
            </div>
        </div>

        <div class="info-section" id="celador-section" style="display: none;">
            <h3 class="info-label">Celador asignado:</h3>
            <div class="info-box celador-box">
                <span class="info-value" id="celador-nombre">-</span>
            </div>
        </div>

        <div class="info-section" id="box-section" style="display: none;">
            <h3 class="info-label">Box asignado:</h3>
            <div class="info-box box-box">
                <span class="info-value" id="box-nombre">-</span>
            </div>
        </div>
    </main>

    <footer class="consult-footer" role="contentinfo">
        <button id="confirmButton" class="confirm-attendance-button"
            aria-label="Confirmar asistencia al hospital">Confirmar asistencia</button>
        <div class="step-counter" aria-label="Paso 3 de 3">
            <span class="counter-number">3/3</span>
            <span class="counter-text">pasos</span>
        </div>
    </footer>

    <script>
        const confirmButton = document.getElementById('confirmButton');

        // Cargar información de la consulta al cargar la página
        async function cargarEstadoConsulta() {
            try {
                // Timeout de 5 segundos
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 5000);
                
                const response = await fetch('api/get_estado_consulta.php', {
                    signal: controller.signal
                });
                clearTimeout(timeoutId);
                
                const result = await response.json();

                if (result.success && result.consulta) {
                    const consulta = result.consulta;
                    
                    // Actualizar información
                    document.querySelector('.info-value').textContent = consulta.urgencia;
                    document.getElementById('pacientes-delante').textContent = consulta.pacientes_delante;
                    document.querySelectorAll('.info-value')[1].textContent = consulta.tiempo_espera;
                    
                    // Si hay celador asignado, mostrar información
                    if (consulta.celador_asignado && consulta.celador) {
                        document.getElementById('celador-nombre').textContent = consulta.celador.nombre;
                        document.getElementById('box-nombre').textContent = consulta.celador.box;
                        document.getElementById('celador-section').style.display = 'block';
                        document.getElementById('box-section').style.display = 'block';
                    }
                }
            } catch (error) {
                if (error.name === 'AbortError') {
                    console.log('Timeout al cargar estado');
                    // Mostrar valores por defecto
                    document.querySelector('.info-value').textContent = 'MEDIA';
                    document.querySelectorAll('.info-value')[1].textContent = '5 minutos';
                } else {
                    console.error('Error al cargar estado:', error);
                }
            }
        }

        // Cargar estado al iniciar
        cargarEstadoConsulta();

        confirmButton.addEventListener('click', async function () {
            // Obtener los datos de la consulta del sessionStorage
            const sintomas = sessionStorage.getItem('consultaSintomas') || '';
            const evidencia = sessionStorage.getItem('consultaEvidencia') || '';
            
            if (!sintomas) {
                showNotification('Error: No se encontraron los datos de la consulta.', 'error', 6000);
                window.location.href = 'consulta-digital_pag1.php';
                return;
            }

            // Deshabilitar el botón para evitar doble click
            this.disabled = true;
            this.textContent = 'Guardando...';

            try {
                // Guardar la consulta en la base de datos
                const response = await fetch('api/save_consulta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        sintomas: sintomas,
                        evidencias: evidencia,
                        tipo_evidencia: 'foto'
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Limpiar el sessionStorage
                    sessionStorage.removeItem('consultaSintomas');
                    sessionStorage.removeItem('consultaEvidencia');

                    // Actualizar el botón
                    this.classList.add('confirmed');
                    this.textContent = 'Consulta guardada ✓';
                    
                    // Mostrar mensaje si hay celador asignado
                    if (result.celador_asignado) {
                        showNotification(result.mensaje, 'success', 6000);
                    }
                    
                    // Recargar estado de la consulta
                    await cargarEstadoConsulta();
                    
                    // Cambiar texto del botón
                    setTimeout(() => {
                        this.textContent = 'Ir al inicio';
                        this.disabled = false;
                        this.onclick = () => window.location.href = 'index.php';
                    }, 1500);
                } else {
                    showNotification('Error al guardar la consulta: ' + result.error, 'error', 7000);
                    this.disabled = false;
                    this.textContent = 'Confirmar asistencia';
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al guardar la consulta. Inténtalo de nuevo.', 'error', 6000);
                this.disabled = false;
                this.textContent = 'Confirmar asistencia';
            }
        });
    </script>

</body>

</html>
