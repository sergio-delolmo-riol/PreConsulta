<?php
/**
 * Consulta Digital - Página 1: Motivo de Consulta
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

require_once 'config/session_manager.php';
require_once 'config/helpers.php';

// Verificar autenticación
requireAuth();
checkSession();

$userName = getUserName();
$nombreCorto = explode(' ', $userName)[0] ?? 'Usuario';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motivo de Consulta - Centro de Triaje Digital</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body class="full-height-body">

    <header class="consult-header" role="banner">
        <div class="header-content">
            <div class="header-text">
                <h1>Motivo<br>Consulta.</h1>
                <p>Por favor <?= sanitize($nombreCorto) ?> indíquenos el motivo de su consulta.</p>
            </div>
            <a href="index.php" class="close-button" aria-label="Cerrar y volver a la página de inicio">
                &times;
            </a>
        </div>
    </header>

    <main id="main-content" role="main" class="consult-main">
        <hr class="status-divider">
        <div class="input-method-selector" role="tablist" aria-label="Método de entrada de síntomas">
            <button id="tab-audio" role="tab" aria-selected="true" aria-controls="panel-audio"
                class="tab-button active">
                <img src="media/icons/mic_24dp_000000_FILL1_wght300_GRAD200_opsz24.svg" alt="" class="tab-icon" aria-hidden="true">
                Audio
            </button>
            <div class="separator"></div>
            <button id="tab-texto" role="tab" aria-selected="false" aria-controls="panel-texto" class="tab-button">
                <img src="media/icons/comment_24dp_000000_FILL1_wght300_GRAD-25_opsz24.svg" alt="" class="tab-icon" aria-hidden="true">
                Texto
            </button>
        </div>

        <!-- Contenedor para los paneles -->
        <div class="panels-container">
            <div id="panel-audio" role="tabpanel" aria-labelledby="tab-audio" class="tab-panel active">
                <button class="record-button" aria-label="Iniciar grabación de audio">
                    <img src="media/icons/mic_24dp_000000_FILL1_wght300_GRAD200_opsz24.svg" alt="" class="record-icon" aria-hidden="true">
                </button>
                <div class="text-container">
                    <label for="transcribed-text" class="sr-only">Texto transcrito</label>
                    <textarea id="transcribed-text" readonly placeholder="Texto transcrito:"></textarea>
                </div>
            </div>

            <div id="panel-texto" role="tabpanel" aria-labelledby="tab-texto" class="tab-panel">
                <div class="text-container">
                    <label for="symptom-text" class="sr-only">Describa sus síntomas</label>
                    <textarea id="symptom-text" maxlength="500" placeholder="Describa sus síntomas aquí..."></textarea>
                    <div class="char-counter" aria-live="polite">0/500</div>
                </div>
            </div>
        </div>
    </main>

    <footer class="consult-footer">
        <a href="consulta-digital_pag2.php" class="next-button">Siguiente</a>
        <div class="step-counter" aria-label="Paso 1 de 3">
            <span class="counter-number">1/3</span>
            <span class="counter-text">pasos</span>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>

</html>
