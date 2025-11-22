<?php
/**
 * Dashboard Principal - Celador
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

require_once 'config/session_manager.php';
require_once 'config/helpers.php';
require_once 'classes/Database.php';

// Verificar autenticación y que sea celador
requireAuth();
checkSession();

$userType = getUserType();
if ($userType !== 'celador') {
    redirect('index.php');
}

$userId = getUserId();
$userName = getUserName();

// Obtener información del celador (box asignado y disponibilidad)
$celadorInfo = [];
try {
    $db = Database::getInstance();
    
    $sqlCelador = "
        SELECT c.id_box, c.disponible, b.nombre as nombre_box
        FROM Celador c
        LEFT JOIN Box b ON c.id_box = b.id_box
        WHERE c.id_celador = :id_celador
    ";
    
    $celadorInfo = $db->selectOne($sqlCelador, ['id_celador' => $userId]);
    if (!$celadorInfo) {
        $celadorInfo = ['id_box' => null, 'disponible' => 'no', 'nombre_box' => null];
    }
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error al obtener info del celador: ' . $e->getMessage());
    }
    $celadorInfo = ['id_box' => null, 'disponible' => 'no', 'nombre_box' => null];
}

// Obtener consultas asignadas al celador
try {
    
    // Consultas asignadas con información del paciente
    $sqlConsultas = "
        SELECT 
            eu.id_episodio,
            eu.fecha_llegada,
            eu.motivo_consulta,
            eu.estado,
            eu.notas_adicionales,
            eu.prioridad_actual,
            u.nombre,
            u.apellidos,
            u.dni,
            p.id_prioridad,
            p.nombre as nombre_prioridad,
            p.color_hex as color,
            p.tiempo_max_atencion as tiempo_espera_max,
            ac.estado as estado_asignacion,
            ac.fecha_asignacion
        FROM Asignacion_Celador ac
        INNER JOIN Episodio_Urgencia eu ON ac.id_episodio = eu.id_episodio
        INNER JOIN Usuario u ON eu.id_paciente = u.id_usuario
        LEFT JOIN Triaje t ON eu.id_episodio = t.id_episodio
        LEFT JOIN Prioridad p ON eu.prioridad_actual = p.id_prioridad
        WHERE ac.id_celador = :id_celador
        AND ac.estado IN ('pendiente', 'en_curso')
        ORDER BY eu.prioridad_actual ASC, eu.fecha_llegada ASC
    ";
    
    $consultas = $db->select($sqlConsultas, ['id_celador' => $userId]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error al obtener consultas: ' . $e->getMessage());
    }
    $consultas = [];
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediConsult - Dashboard Celador</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/celador-dashboard.css">
</head>
<body class="dashboard-body">

    <!-- Sidebar -->
    <aside class="sidebar" role="navigation" aria-label="Menú principal">
        <div class="sidebar-header">
            <div class="logo">
                <span class="logo-icon">📋</span>
                <span class="logo-text">MediConsult</span>
            </div>
        </div>

        <div class="user-profile">
            <img src="media/icons/person_heart_24dp_007AFF_FILL1_wght500_GRAD0_opsz24.svg" alt="Avatar" class="user-avatar">
            <div class="user-info">
                <p class="user-name"><?= sanitize($userName) ?></p>
                <p class="user-role">Celador</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="celador-dashboard.php" class="nav-link active" aria-current="page">
                        <img src="media/icons/calendar_today_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                        <span>Consultas</span>
                    </a>
                </li>
                <li>
                    <a href="celador-pacientes.php" class="nav-link">
                        <img src="media/icons/group_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                        <span>Pacientes</span>
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
            <button id="toggle-disponibilidad" class="btn-disponibilidad <?= $celadorInfo['disponible'] === 'si' ? 'activo' : '' ?>" title="Cambiar disponibilidad">
                <img src="media/icons/toggle_on_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                <span id="disponibilidad-text"><?= $celadorInfo['disponible'] === 'si' ? 'Disponible' : 'No Disponible' ?></span>
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
                       placeholder="Buscar paciente por DNI..."
                       aria-label="Buscar paciente por DNI">
            </div>

            <div class="box-info">
                <?php if ($celadorInfo['disponible'] === 'si' && $celadorInfo['nombre_box']): ?>
                    <div class="box-badge">
                        <img src="media/icons/meeting_room_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Box" class="box-icon">
                        <span id="box-asignado"><?= sanitize($celadorInfo['nombre_box']) ?></span>
                    </div>
                <?php else: ?>
                    <div class="box-badge inactive">
                        <img src="media/icons/meeting_room_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Sin box" class="box-icon">
                        <span id="box-asignado">Sin Box Asignado</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="top-bar-actions">
                <button class="icon-button" id="btnNotificaciones" aria-label="Notificaciones">
                    <img src="media/icons/notifications_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Notificaciones">
                    <span class="badge" id="notificaciones-badge" style="display: none;">0</span>
                </button>
                <div class="user-menu">
                    <img src="media/icons/person_heart_24dp_007AFF_FILL1_wght500_GRAD0_opsz24.svg" alt="Usuario" class="user-avatar-small">
                </div>
            </div>
            
            <!-- Panel de Notificaciones -->
            <div class="notificaciones-panel" id="notificacionesPanel">
                <div class="notificaciones-header">
                    <h3>Notificaciones</h3>
                    <button class="btn-cerrar-notif" id="btnCerrarNotif">&times;</button>
                </div>
                <div class="notificaciones-content" id="notificacionesContent">
                    <div class="loading">Cargando notificaciones...</div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-wrapper">
            <!-- Left Panel - Consultas List -->
            <main class="consultas-panel" role="main">
                <div class="panel-header">
                    <div class="header-top">
                        <h1>Consultas</h1>
                        <p class="header-subtitle">Hoy, <?= date('d \\d\\e F Y') ?></p>
                    </div>
                </div>

                <div class="filters-tabs" role="tablist">
                    <button role="tab" aria-selected="true" class="tab-button active" data-filter="todas">
                        Todas
                    </button>
                    <button role="tab" aria-selected="false" class="tab-button" data-filter="pendientes">
                        Pendientes
                    </button>
                    <button role="tab" aria-selected="false" class="tab-button" data-filter="autorizadas">
                        Autorizadas
                    </button>
                </div>

                <div class="consultas-list">
                    <?php if (empty($consultas)): ?>
                        <div class="empty-state">
                            <img src="media/icons/inbox_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="empty-icon">
                            <p>No tienes consultas asignadas en este momento</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($consultas as $consulta): ?>
                            <?php
                                $estadoClass = '';
                                $estadoLabel = '';
                                
                                switch ($consulta['id_prioridad']) {
                                    case 1: // Resucitación
                                    case 2: // Emergencia
                                        $estadoClass = 'urgencia-alta';
                                        $estadoLabel = 'Urgencia Alta';
                                        break;
                                    case 3: // Urgente
                                        $estadoClass = 'pendiente';
                                        $estadoLabel = 'Pendiente';
                                        break;
                                    default:
                                        $estadoClass = 'autorizada';
                                        $estadoLabel = 'Autorizada';
                                }
                            ?>
                            <div class="consulta-card" 
                                 data-episodio="<?= $consulta['id_episodio'] ?>"
                                 data-estado="<?= strtolower($estadoLabel) ?>"
                                 data-dni="<?= sanitize($consulta['dni']) ?>">
                                <div class="card-header">
                                    <h3 class="patient-name"><?= sanitize($consulta['nombre'] . ' ' . $consulta['apellidos']) ?></h3>
                                    <span class="status-badge <?= $estadoClass ?>"><?= $estadoLabel ?></span>
                                </div>
                                <p class="card-dni">DNI: <?= sanitize($consulta['dni']) ?></p>
                                <p class="card-description">
                                    <?= sanitize(mb_substr($consulta['motivo_consulta'], 0, 80)) ?>...
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>

            <!-- Right Panel - Detalles -->
            <aside class="detalles-panel" role="complementary">
                <div class="panel-header">
                    <h2>Detalles de Pre-consulta</h2>
                    <p class="panel-subtitle">Revisar y autorizar la consulta del paciente.</p>
                </div>

                <div id="detalles-content" class="detalles-content">
                    <div class="empty-state">
                        <img src="media/icons/description_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="empty-icon">
                        <p>Selecciona una consulta para ver los detalles</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script src="js/celador-dashboard.js"></script>
</body>
</html>
