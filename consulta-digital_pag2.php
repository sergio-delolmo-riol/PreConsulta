<?php
/**
 * Consulta Digital - Página 2: Evidencia
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
    <title>Evidencia de la Consulta - PreConsulta</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="full-height-body">

    <header class="consult-header" role="banner">
        <div class="header-content">
            <div>
                <h1>
                    <span class="line1">Evidencia</span>
                    <span class="line2">Consulta.</span>
                </h1>
                <p>Por favor <?= sanitize($nombreCorto) ?> muéstrenos una evidencia de su consulta.</p>
            </div>
            <a href="index.php" class="close-button" aria-label="Cerrar y volver al inicio">×</a>
        </div>
    </header>

    <main class="consult-main" role="main">
        <hr class="status-divider">
        <div class="evidence-container">
            <div class="evidence-upload-box" role="button" tabindex="0"
                aria-label="Adjuntar una imagen sobre el motivo de su consulta">
                <span class="upload-icon" aria-hidden="true">📷</span>
                <span class="upload-text">Adjunte una imagen sobre el motivo de su consulta.</span>
                <input type="file" accept="image/*" class="sr-only" id="file-upload">
            </div>
        </div>
        <button class="confirm-button">Confirmar evidencia</button>
    </main>

    <footer class="consult-footer" role="contentinfo">
        <a href="consulta-digital_pag1.php" class="back-button" aria-label="Volver al paso anterior">
            <img src="media/icons/arrow_back_24dp_000000_FILL1_wght300_GRAD-25_opsz24.svg" alt="Flecha volver" class="back-arrow-icon">
        </a>
        <a href="consulta-digital_pag3.php" class="next-button">Siguiente</a>
        <div class="step-counter" aria-label="Paso 2 de 3">
            <span class="counter-number">2/3</span>
            <span class="counter-text">pasos</span>
        </div>
    </footer>

</body>

</html>
