<?php
/**
 * Panel de Estadísticas - Celador
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

require_once __DIR__ . '/config/session_manager.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Database.php';

// Verificar autenticación y tipo de usuario
requireAuth();
$userType = getUserType();
if ($userType !== 'celador' && $userType !== 'enfermero') {
    header('Location: index.php');
    exit();
}

$userId = getUserId();
$userName = getUserName();

// Obtener información según el tipo de usuario
$userInfo = [];
try {
    $db = Database::getInstance();
    
    if ($userType === 'enfermero') {
        $sql = "
            SELECT e.id_box, e.disponible, b.nombre as nombre_box, e.especialidad
            FROM Enfermero e
            LEFT JOIN Box b ON e.id_box = b.id_box
            WHERE e.id_enfermero = :id_usuario
        ";
        $userInfo = $db->selectOne($sql, ['id_usuario' => $userId]);
        if (!$userInfo) {
            $userInfo = ['id_box' => null, 'disponible' => 0, 'nombre_box' => null, 'especialidad' => 'General'];
        }
    } else {
        $sql = "
            SELECT c.id_box, c.disponible, b.nombre as nombre_box
            FROM Celador c
            LEFT JOIN Box b ON c.id_box = b.id_box
            WHERE c.id_celador = :id_usuario
        ";
        $userInfo = $db->selectOne($sql, ['id_usuario' => $userId]);
        if (!$userInfo) {
            $userInfo = ['id_box' => null, 'disponible' => 'no', 'nombre_box' => null];
        }
    }
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error al obtener info del usuario: ' . $e->getMessage());
    }
    $userInfo = ['id_box' => null, 'disponible' => $userType === 'enfermero' ? 0 : 'no', 'nombre_box' => null];
}
$userEmail = getUserEmail();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Celador Dashboard</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/celador-dashboard.css">
    <link rel="stylesheet" href="CSS/celador-estadisticas.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="dashboard-body">
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" role="navigation" aria-label="Menú principal">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="logo-icon">📋</span>
                    <span class="logo-text">PreConsulta</span>
                </div>
            </div>
            
            <div class="user-profile">
                <img src="media/icons/person_heart_24dp_007AFF_FILL1_wght500_GRAD0_opsz24.svg" alt="Avatar" class="user-avatar">
                <div class="user-info">
                    <p class="user-name"><?= sanitize($userName) ?></p>
                    <p class="user-role"><?= $userType === 'enfermero' ? 'Enfermero' : 'Celador' ?></p>
                    <?php if ($userType === 'enfermero' && isset($userInfo['especialidad'])): ?>
                        <p class="user-specialty"><?= sanitize($userInfo['especialidad']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="<?= $userType === 'enfermero' ? 'enfermero-dashboard.php' : 'celador-dashboard.php' ?>" class="nav-link">
                            <img src="media/icons/calendar_today_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                            <span><?= $userType === 'enfermero' ? 'Mi Paciente' : 'Consultas' ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $userType === 'enfermero' ? 'enfermero-pacientes.php' : 'celador-pacientes.php' ?>" class="nav-link">
                            <img src="media/icons/group_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                            <span><?= $userType === 'enfermero' ? 'Buscar Pacientes' : 'Pacientes' ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="celador-estadisticas.php" class="nav-link active" aria-current="page">
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
                <button id="toggle-disponibilidad" class="btn-disponibilidad <?= ($userType === 'enfermero' ? $userInfo['disponible'] : ($userInfo['disponible'] === 'si')) ? 'activo' : '' ?>" title="Cambiar disponibilidad">
                    <img src="media/icons/toggle_on_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                    <span id="disponibilidad-text"><?= ($userType === 'enfermero' ? $userInfo['disponible'] : ($userInfo['disponible'] === 'si')) ? 'Disponible' : 'No Disponible' ?></span>
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
                           placeholder="Buscar paciente..." 
                           aria-label="Buscar paciente">
                </div>

                <div class="box-info">
                    <?php if (($userType === 'enfermero' && $userInfo['disponible'] && $userInfo['nombre_box']) || ($userType === 'celador' && $userInfo['disponible'] === 'si' && $userInfo['nombre_box'])): ?>
                        <div class="box-badge">
                            <img src="media/icons/meeting_room_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Box" class="box-icon">
                            <span id="box-asignado"><?= sanitize($userInfo['nombre_box']) ?></span>
                        </div>
                    <?php else: ?>
                        <div class="box-badge inactive">
                            <img src="media/icons/meeting_room_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Sin box" class="box-icon">
                            <span id="box-asignado">Sin Box Asignado</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="top-bar-actions">
                    <button class="icon-btn" id="btnNotificaciones" title="Notificaciones">
                        <img src="media/icons/notifications_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" aria-hidden="true">
                        <span class="badge" id="notifBadge" style="display: none;">0</span>
                    </button>
                </div>
            </header>

            <!-- Panel de Notificaciones -->
            <div class="notificaciones-panel" id="notificacionesPanel" style="display: none;">
                <div class="panel-header">
                    <h3>Notificaciones</h3>
                    <button class="btn-close" id="btnCerrarNotif">×</button>
                </div>
                <div class="panel-content" id="notificacionesContent">
                    <!-- Las notificaciones se cargan dinámicamente -->
                </div>
            </div>

            <!-- Contenido Principal -->
            <main class="content-area" role="main">
                <div class="content-header">
                    <h1 class="page-title">Estadísticas del Servicio</h1>
                    <p class="page-subtitle">Datos de los últimos 30 días</p>
                </div>

                <!-- Tarjetas de resumen -->
                <section class="stats-summary">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #dbeafe;">
                            <span style="color: #2563eb;">📊</span>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value" id="totalConsultas">-</h3>
                            <p class="stat-label">Total Consultas</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #dcfce7;">
                            <span style="color: #16a34a;">👥</span>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value" id="totalPacientes">-</h3>
                            <p class="stat-label">Pacientes Atendidos</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #fef3c7;">
                            <span style="color: #ca8a04;">⏱️</span>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value" id="tiempoPromedio">-</h3>
                            <p class="stat-label">Tiempo Promedio</p>
                        </div>
                    </div>
                </section>

                <!-- Gráficos -->
                <section class="charts-section">
                    <div class="chart-card chart-large">
                        <div class="chart-header">
                            <h2>Consultas por Hora del Día</h2>
                            <p>Distribución de consultas según hora de llegada y gravedad</p>
                        </div>
                        <div class="chart-container">
                            <canvas id="chartPorHora"></canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h2>Consultas por Prioridad</h2>
                            <p>Distribución según nivel de gravedad</p>
                        </div>
                        <div class="chart-container">
                            <canvas id="chartPorPrioridad"></canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h2>Consultas por Día de la Semana</h2>
                            <p>Actividad según día de la semana</p>
                        </div>
                        <div class="chart-container">
                            <canvas id="chartPorDia"></canvas>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="js/accessible-notifications.js"></script>
    <script src="js/celador-estadisticas.js"></script>
</body>
</html>
