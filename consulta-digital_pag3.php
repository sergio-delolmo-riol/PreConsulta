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

// En una implementación real, aquí obtendríamos datos reales de la base de datos
// Por ahora usamos valores de ejemplo
$urgencia = "MEDIO";
$tiempoEspera = "8 minutos";
$pacientesDelante = "3";

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
                <span class="info-value"><?= sanitize($pacientesDelante) ?></span>
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

        confirmButton.addEventListener('click', async function () {
            // Obtener los datos de la consulta del sessionStorage
            const sintomas = sessionStorage.getItem('consultaSintomas') || '';
            const evidencia = sessionStorage.getItem('consultaEvidencia') || '';
            
            if (!sintomas) {
                alert('Error: No se encontraron los datos de la consulta.');
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
                    this.textContent = 'Asistencia confirmada';
                    
                    // Redirigir después de 2 segundos
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    alert('Error al guardar la consulta: ' + result.error);
                    this.disabled = false;
                    this.textContent = 'Confirmar asistencia';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar la consulta. Inténtalo de nuevo.');
                this.disabled = false;
                this.textContent = 'Confirmar asistencia';
            }
        });
    </script>

</body>

</html>
