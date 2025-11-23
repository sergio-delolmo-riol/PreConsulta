<?php
/**
 * Dashboard Principal - Enfermero
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

require_once 'config/session_manager.php';
require_once 'config/helpers.php';
require_once 'classes/Database.php';

// Verificar autenticación y que sea enfermero
requireAuth();
checkSession();

$userType = getUserType();
if ($userType !== 'enfermero') {
    redirect('index.php');
}

$userId = getUserId();
$userName = getUserName();

// Obtener información del enfermero (box asignado y disponibilidad)
$enfermeroInfo = [];
try {
    $db = Database::getInstance();
    
    $sqlEnfermero = "
        SELECT e.id_box, e.disponible, e.especialidad, e.numero_colegiado, b.nombre as nombre_box
        FROM Enfermero e
        LEFT JOIN Box b ON e.id_box = b.id_box
        WHERE e.id_enfermero = :id_enfermero
    ";
    
    $enfermeroInfo = $db->selectOne($sqlEnfermero, ['id_enfermero' => $userId]);
    if (!$enfermeroInfo) {
        $enfermeroInfo = ['id_box' => null, 'disponible' => 0, 'nombre_box' => null, 'especialidad' => 'General'];
    }
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error al obtener info del enfermero: ' . $e->getMessage());
    }
    $enfermeroInfo = ['id_box' => null, 'disponible' => 0, 'nombre_box' => null, 'especialidad' => 'General'];
}

// Obtener paciente actualmente asignado (solo uno a la vez)
$pacienteAsignado = null;
try {
    $sqlPacienteAsignado = "
        SELECT 
            eu.id_episodio,
            eu.fecha_llegada,
            eu.motivo_consulta,
            eu.estado,
            eu.prioridad_actual,
            u.nombre,
            u.apellidos,
            u.dni,
            u.fecha_nacimiento,
            u.telefono,
            p.id_prioridad,
            p.nombre as nombre_prioridad,
            p.color_hex as color,
            ae.estado as estado_asignacion,
            ae.fecha_asignacion,
            ae.id_asignacion
        FROM Asignacion_Enfermero ae
        INNER JOIN Episodio_Urgencia eu ON ae.id_episodio = eu.id_episodio
        INNER JOIN Usuario u ON eu.id_paciente = u.id_usuario
        LEFT JOIN Prioridad p ON eu.prioridad_actual = p.id_prioridad
        WHERE ae.id_enfermero = :id_enfermero
        AND ae.estado IN ('asignado', 'atendiendo')
        ORDER BY ae.fecha_asignacion DESC
        LIMIT 1
    ";
    
    $pacienteAsignado = $db->selectOne($sqlPacienteAsignado, ['id_enfermero' => $userId]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error al obtener paciente asignado: ' . $e->getMessage());
    }
    $pacienteAsignado = null;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PreConsulta - Dashboard Enfermero</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/enfermero-dashboard.css">
</head>
<body class="dashboard-body">
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <!-- Sidebar -->
    <aside class="sidebar" role="navigation" aria-label="Menú principal">
        <div class="sidebar-header">
            <div class="logo">
                <span class="logo-icon">🏥</span>
                <span class="logo-text">PreConsulta</span>
            </div>
        </div>

        <div class="user-profile">
            <img src="media/icons/person_heart_24dp_007AFF_FILL1_wght500_GRAD0_opsz24.svg" alt="Avatar" class="user-avatar">
            <div class="user-info">
                <p class="user-name"><?= sanitize($userName) ?></p>
                <p class="user-role">Enfermero</p>
                <p class="user-specialty"><?= sanitize($enfermeroInfo['especialidad']) ?></p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="enfermero-dashboard.php" class="nav-link active" aria-current="page">
                        <img src="media/icons/calendar_today_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                        <span>Mi Paciente</span>
                    </a>
                </li>
                <li>
                    <a href="enfermero-pacientes.php" class="nav-link">
                        <img src="media/icons/group_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                        <span>Buscar Pacientes</span>
                    </a>
                </li>
                <li>
                    <a href="celador-estadisticas.php" class="nav-link">
                        <img src="media/icons/bar_chart_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                        <span>Estadísticas</span>
                    </a>
                </li>
                <li>
                    <a href="celador-configuracion.php" class="nav-link">
                        <img src="media/icons/settings_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                        <span>Configuración</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <button id="toggle-disponibilidad" class="btn-disponibilidad <?= $enfermeroInfo['disponible'] ? 'activo' : '' ?>" title="Cambiar disponibilidad" aria-pressed="<?= $enfermeroInfo['disponible'] ? 'true' : 'false' ?>" aria-label="Cambiar disponibilidad de trabajo">
                <img src="media/icons/toggle_on_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                <span id="disponibilidad-text"><?= $enfermeroInfo['disponible'] ? 'Disponible' : 'No Disponible' ?></span>
            </button>
            <a href="logout.php" class="nav-link logout-link">
                <img src="media/icons/logout_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Top Bar -->
        <header class="top-bar" role="banner">
            <div class="search-container">
                <img src="media/icons/search_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="search-icon" aria-hidden="true">
                <input type="search" 
                       id="patient-search" 
                       class="search-input" 
                       placeholder="Buscar paciente por DNI o nombre..."
                       aria-label="Buscar paciente">
            </div>

            <div class="box-info">
                <?php if ($enfermeroInfo['disponible'] && $enfermeroInfo['nombre_box']): ?>
                    <div class="box-badge">
                        <img src="media/icons/meeting_room_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Box" class="box-icon">
                        <span id="box-asignado"><?= sanitize($enfermeroInfo['nombre_box']) ?></span>
                    </div>
                <?php else: ?>
                    <div class="box-badge inactive">
                        <img src="media/icons/meeting_room_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Sin box" class="box-icon">
                        <span id="box-asignado">Sin Box Asignado</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="top-bar-actions">
                <button class="icon-button" id="btnNotificaciones" aria-label="Notificaciones" aria-haspopup="dialog" aria-expanded="false">
                    <img src="media/icons/notifications_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Notificaciones">
                    <span class="badge" id="notificaciones-badge" style="display: none;">0</span>
                </button>
                <div class="user-menu">
                    <img src="media/icons/person_heart_24dp_007AFF_FILL1_wght500_GRAD0_opsz24.svg" alt="Usuario" class="user-avatar-small">
                </div>
            </div>
            
            <!-- Panel de Notificaciones -->
            <div class="notificaciones-panel" id="notificacionesPanel" role="dialog" aria-labelledby="notificaciones-titulo" aria-hidden="true">
                <div class="notificaciones-header">
                    <h3 id="notificaciones-titulo">Notificaciones</h3>
                    <button class="btn-cerrar-notif" id="btnCerrarNotif" aria-label="Cerrar panel de notificaciones">&times;</button>
                </div>
                <div class="notificaciones-content" id="notificacionesContent">
                    <div class="loading">Cargando notificaciones...</div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-wrapper">
            <!-- Left Panel - Paciente Asignado -->
            <main class="paciente-panel" role="main" id="main-content">
                <div class="panel-header">
                    <div class="header-top">
                        <h1>Mi Paciente Actual</h1>
                        <p class="header-subtitle">Hoy, <?= date('d \\d\\e F Y') ?></p>
                    </div>
                </div>

                <div id="paciente-asignado-container" class="paciente-asignado-container" role="region" aria-label="Paciente asignado" aria-live="polite">
                    <?php if ($pacienteAsignado): ?>
                        <div class="paciente-card active" role="article" tabindex="0" aria-label="Información del paciente <?= sanitize($pacienteAsignado['nombre'] . ' ' . $pacienteAsignado['apellidos']) ?>" data-episodio="<?= $pacienteAsignado['id_episodio'] ?>">
                            <div class="card-header">
                                <h3 class="patient-name"><?= sanitize($pacienteAsignado['nombre'] . ' ' . $pacienteAsignado['apellidos']) ?></h3>
                                <span class="status-badge <?= getPrioridadClass($pacienteAsignado['id_prioridad'] ?? 3) ?>">
                                    <?= sanitize($pacienteAsignado['nombre_prioridad'] ?? 'Media') ?>
                                </span>
                            </div>
                            <p class="card-dni">DNI: <?= sanitize($pacienteAsignado['dni']) ?></p>
                            <p class="card-edad">Edad: <?= calcularEdad($pacienteAsignado['fecha_nacimiento']) ?> años</p>
                            <p class="card-description">
                                <?= sanitize(mb_substr($pacienteAsignado['motivo_consulta'], 0, 100)) ?>...
                            </p>
                            <div class="card-footer">
                                <span class="card-time">
                                    <img src="media/icons/schedule_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Hora" class="time-icon">
                                    <?= date('H:i', strtotime($pacienteAsignado['fecha_asignacion'])) ?>
                                </span>
                                <span class="card-status">
                                    <?= $pacienteAsignado['estado_asignacion'] === 'atendiendo' ? 'En Atención' : 'Asignado' ?>
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <img src="media/icons/inbox_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="empty-icon">
                            <p>No tienes ningún paciente asignado en este momento</p>
                            <p class="empty-subtitle">Ponte disponible para recibir asignaciones</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sección de Historial Médico -->
                <div class="historial-section">
                    <div class="section-header">
                        <h2>Historial Médico</h2>
                        <button id="btn-refresh-historial" class="btn-icon-sm" title="Actualizar historial" aria-label="Actualizar historial médico del paciente">
                            <img src="media/icons/refresh_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Actualizar">
                        </button>
                    </div>
                    <div id="historial-content" class="historial-content" role="region" aria-label="Historial médico del paciente" aria-live="polite">
                        <div class="empty-state-sm">
                            <p>Selecciona un paciente para ver su historial</p>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Right Panel - Detalles y Acciones -->
            <aside class="detalles-panel" role="complementary">
                <div class="panel-header">
                    <h2>Atención Médica</h2>
                    <p class="panel-subtitle">Información del paciente y acciones de atención.</p>
                </div>

                <div id="detalles-content" class="detalles-content">
                    <div class="empty-state">
                        <img src="media/icons/description_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="empty-icon">
                        <p>Selecciona un paciente para ver los detalles y realizar acciones</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script src="js/accessible-notifications.js"></script>
    <script src="js/enfermero-dashboard.js"></script>
</body>
</html>

<?php
// Función helper para calcular edad
function calcularEdad($fechaNacimiento) {
    if (!$fechaNacimiento) return 'N/A';
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    return $hoy->diff($nacimiento)->y;
}

// Función helper para clase de prioridad
function getPrioridadClass($idPrioridad) {
    switch ($idPrioridad) {
        case 1:
        case 2:
            return 'urgencia-alta';
        case 3:
            return 'pendiente';
        default:
            return 'autorizada';
    }
}
?>
