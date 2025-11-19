<?php
/**
 * Detalle de Consulta
 * Proyecto: PreConsulta - Centro de Triaje Digital
 * Muestra información completa de una consulta específica
 */

require_once 'config/database.php';
require_once 'config/session_manager.php';
require_once 'config/helpers.php';
require_once 'classes/Database.php';

// Verificar autenticación
requireAuth();
checkSession();

$db = Database::getInstance();
$userId = getUserId();
$episodioId = $_GET['id'] ?? null;

if (!$episodioId) {
    redirect('perfil-usuario.php');
}

// Obtener datos completos del episodio
$sql = "
    SELECT 
        e.id_episodio,
        e.fecha_llegada,
        e.sintomas,
        e.estado,
        e.evidencias,
        p.nombre AS prioridad,
        p.color_hex AS color_prioridad,
        p.descripcion AS descripcion_prioridad,
        p.tiempo_max_atencion,
        t.frecuencia_cardiaca,
        t.presion_arterial,
        t.temperatura,
        t.saturacion_oxigeno,
        t.observaciones AS observaciones_triaje,
        t.fecha_triaje,
        b.numero AS box_numero,
        b.tipo AS box_tipo,
        h.antecedentes,
        h.medicacion_actual,
        h.ultima_actualizacion
    FROM Episodio_Urgencia e
    LEFT JOIN Triaje t ON e.id_episodio = t.id_episodio
    LEFT JOIN Prioridad p ON t.id_prioridad = p.id_prioridad
    LEFT JOIN Box b ON e.id_box = b.id_box
    LEFT JOIN Historial_Clinico h ON e.id_paciente = h.id_paciente
    WHERE e.id_episodio = :id_episodio AND e.id_paciente = :id_paciente
";

$resultados = $db->query($sql, [
    'id_episodio' => $episodioId,
    'id_paciente' => $userId
]);

$consulta = !empty($resultados) ? $resultados[0] : null;

if (!$consulta) {
    redirect('perfil-usuario.php');
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Consulta - PreConsulta</title>
    <link rel="icon" type="image/svg+xml" href="media/icons/cardiology_24dp_007AFF_FILL1_wght300_GRAD-25_opsz24.svg">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .detail-container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            padding-bottom: 100px;
        }
        
        .detail-header {
            background: linear-gradient(135deg, #007AFF 0%, #005BBB 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .detail-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .detail-date {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .detail-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #007AFF;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .detail-item {
            margin-bottom: 12px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
            display: block;
            margin-bottom: 4px;
        }
        
        .detail-value {
            color: #333;
            font-size: 16px;
        }
        
        .prioridad-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        
        .estado-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            background-color: #E3F2FD;
            color: #1976D2;
            font-weight: 600;
            font-size: 14px;
        }
        
        .vitales-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 12px;
        }
        
        .vital-card {
            background: #F5F5F5;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
        }
        
        .vital-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
        }
        
        .vital-value {
            font-size: 20px;
            font-weight: 700;
            color: #007AFF;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: white;
            color: #007AFF;
            border: 2px solid #007AFF;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 20px;
        }
        
        .back-button:hover {
            background: #007AFF;
            color: white;
        }
    </style>
</head>

<body class="full-height-body">
    <div class="detail-container">
        <a href="perfil-usuario.php" class="back-button">
            <img src="media/icons/arrow_back_24dp_000000_FILL1_wght500_GRAD200_opsz24.svg" alt="Volver" style="width: 20px; height: 20px;">
            Volver al perfil
        </a>

        <div class="detail-header">
            <div class="detail-title">Consulta #{<?= $consulta['id_episodio'] ?>}</div>
            <div class="detail-date">📅 <?= formatDate($consulta['fecha_llegada'], true) ?></div>
        </div>

        <!-- Estado y Prioridad -->
        <div class="detail-section">
            <div class="section-title">📊 Estado de la Consulta</div>
            <div class="detail-item">
                <span class="detail-label">Estado:</span>
                <span class="estado-badge"><?= getEpisodeStatusText($consulta['estado']) ?></span>
            </div>
            <?php if ($consulta['prioridad']): ?>
            <div class="detail-item">
                <span class="detail-label">Prioridad:</span>
                <span class="prioridad-badge" style="background-color: <?= $consulta['color_prioridad'] ?>;">
                    <?= sanitize($consulta['prioridad']) ?>
                </span>
                <?php if ($consulta['descripcion_prioridad']): ?>
                <p style="margin-top: 8px; font-size: 14px; color: #666;">
                    <?= sanitize($consulta['descripcion_prioridad']) ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($consulta['box_numero']): ?>
            <div class="detail-item">
                <span class="detail-label">Box asignado:</span>
                <span class="detail-value">Box #<?= $consulta['box_numero'] ?> (<?= ucfirst($consulta['box_tipo']) ?>)</span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Síntomas -->
        <div class="detail-section">
            <div class="section-title">💬 Motivo de Consulta</div>
            <div class="detail-item">
                <p class="detail-value"><?= sanitize($consulta['sintomas'] ?? 'No especificado') ?></p>
            </div>
            
            <?php if ($consulta['evidencias']): ?>
            <div class="detail-item" style="margin-top: 16px;">
                <span class="detail-label">Evidencias adjuntas:</span>
                <p class="detail-value" style="color: #007AFF; font-size: 14px;">
                    📎 <?= sanitize($consulta['evidencias']) ?>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Signos Vitales -->
        <?php if ($consulta['fecha_triaje']): ?>
        <div class="detail-section">
            <div class="section-title">❤️ Signos Vitales (Triaje)</div>
            <div class="detail-item">
                <span class="detail-label">Fecha del triaje:</span>
                <span class="detail-value"><?= formatDate($consulta['fecha_triaje'], true) ?></span>
            </div>
            
            <div class="vitales-grid">
                <?php if ($consulta['frecuencia_cardiaca']): ?>
                <div class="vital-card">
                    <div class="vital-label">Frecuencia Cardíaca</div>
                    <div class="vital-value"><?= $consulta['frecuencia_cardiaca'] ?> <span style="font-size: 14px;">bpm</span></div>
                </div>
                <?php endif; ?>
                
                <?php if ($consulta['presion_arterial']): ?>
                <div class="vital-card">
                    <div class="vital-label">Presión Arterial</div>
                    <div class="vital-value" style="font-size: 16px;"><?= $consulta['presion_arterial'] ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($consulta['temperatura']): ?>
                <div class="vital-card">
                    <div class="vital-label">Temperatura</div>
                    <div class="vital-value"><?= $consulta['temperatura'] ?> <span style="font-size: 14px;">°C</span></div>
                </div>
                <?php endif; ?>
                
                <?php if ($consulta['saturacion_oxigeno']): ?>
                <div class="vital-card">
                    <div class="vital-label">Saturación O₂</div>
                    <div class="vital-value"><?= $consulta['saturacion_oxigeno'] ?> <span style="font-size: 14px;">%</span></div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($consulta['observaciones_triaje']): ?>
            <div class="detail-item" style="margin-top: 16px;">
                <span class="detail-label">Observaciones del triaje:</span>
                <p class="detail-value"><?= sanitize($consulta['observaciones_triaje']) ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Historial Clínico -->
        <?php if ($consulta['antecedentes'] || $consulta['medicacion_actual']): ?>
        <div class="detail-section">
            <div class="section-title">📋 Historial Clínico</div>
            
            <?php if ($consulta['antecedentes']): ?>
            <div class="detail-item">
                <span class="detail-label">Antecedentes:</span>
                <p class="detail-value"><?= sanitize($consulta['antecedentes']) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($consulta['medicacion_actual']): ?>
            <div class="detail-item">
                <span class="detail-label">Medicación actual:</span>
                <p class="detail-value"><?= sanitize($consulta['medicacion_actual']) ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav" role="navigation" aria-label="Menú principal">
        <ul>
            <li>
                <a href="index.php" aria-label="Ir a la página de inicio" class="nav-button">
                    <img src="media/icons/home_24dp_000000_FILL0_wght400_GRAD0_opsz48.svg" alt="Icono de inicio" class="nav-icon">
                    <span class="nav-text">Inicio</span>
                </a>
            </li>
            <li>
                <a href="perfil-usuario.php" aria-label="Ir a tu perfil de usuario" class="nav-button active">
                    <img src="media/icons/person_heart_24dp_007AFF_FILL1_wght500_GRAD0_opsz24.svg" alt="Icono de perfil" class="nav-icon">
                    <span class="nav-text">Perfil</span>
                </a>
            </li>
            <li>
                <a href="tel:112" aria-label="Llamar a emergencias" class="nav-button call-button">
                    <img src="media/icons/phone_enable_emergency_red.svg" alt="Icono de teléfono" class="nav-icon">
                    <span class="nav-text">Llamada emergencia</span>
                </a>
            </li>
        </ul>
    </nav>
</body>

</html>
