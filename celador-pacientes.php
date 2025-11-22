<?php
/**
 * Panel de Pacientes - Celador
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

require_once __DIR__ . '/config/session_manager.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Database.php';

// Verificar autenticación y tipo de usuario
requireAuth();
$userType = getUserType();
if ($userType !== 'celador') {
    header('Location: index.php');
    exit();
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
$userEmail = getUserEmail();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes - Celador Dashboard</title>
    <link rel="stylesheet" href="CSS/celador-dashboard.css">
    <link rel="stylesheet" href="CSS/celador-pacientes.css">
</head>
<body>
    <div class="dashboard-container">
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
                        <a href="celador-dashboard.php" class="nav-link">
                            <img src="media/icons/calendar_today_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="" class="nav-icon" aria-hidden="true">
                            <span>Consultas</span>
                        </a>
                    </li>
                    <li>
                        <a href="celador-pacientes.php" class="nav-link active" aria-current="page">
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
                           placeholder="Buscar paciente..." 
                           aria-label="Buscar paciente">
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
                    <h1 class="page-title">Gestión de Pacientes</h1>
                </div>
                <!-- Buscador de pacientes -->
                <section class="search-section">
                    <div class="search-card">
                        <h2>Buscar Paciente</h2>
                        <form id="formBuscarPaciente" class="search-form">
                            <div class="search-input-group">
                                <input 
                                    type="text" 
                                    id="inputDNI" 
                                    name="dni" 
                                    placeholder="Introduce el DNI del paciente (ej: 12345678A)"
                                    pattern="[0-9]{8}[A-Za-z]"
                                    required
                                >
                                <button type="submit" class="btn-search">
                                    <img src="media/icons/search_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Buscar">
                                    Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Información del paciente -->
                <section id="seccionPaciente" class="patient-section" style="display: none;">
                    <div class="patient-card">
                        <div class="patient-header">
                            <div class="patient-avatar-large" id="pacienteAvatar">P</div>
                            <div class="patient-info-main">
                                <h2 id="pacienteNombre">-</h2>
                                <div class="patient-meta">
                                    <span class="meta-item">
                                        <strong>DNI:</strong> <span id="pacienteDNI">-</span>
                                    </span>
                                    <span class="meta-item">
                                        <strong>Email:</strong> <span id="pacienteEmail">-</span>
                                    </span>
                                    <span class="meta-item">
                                        <strong>Teléfono:</strong> <span id="pacienteTelefono">-</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="patient-details">
                            <div class="detail-item">
                                <strong>Grupo Sanguíneo:</strong>
                                <span id="pacienteGrupo">-</span>
                            </div>
                            <div class="detail-item">
                                <strong>Alergias:</strong>
                                <span id="pacienteAlergias">Ninguna</span>
                            </div>
                            <div class="detail-item">
                                <strong>Seguro Médico:</strong>
                                <span id="pacienteSeguro">-</span>
                            </div>
                            <div class="detail-item">
                                <strong>Total Consultas:</strong>
                                <span id="pacienteTotalConsultas">0</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Historial de consultas -->
                <section id="seccionHistorial" class="history-section" style="display: none;">
                    <div class="section-header">
                        <h2>Historial de Consultas</h2>
                        <span class="history-count" id="historialCount">0 consultas</span>
                    </div>
                    <div class="history-list" id="historialLista">
                        <!-- Las consultas se cargan dinámicamente -->
                    </div>
                </section>

                <!-- Mensaje cuando no hay resultados -->
                <div id="mensajeSinResultados" class="empty-state" style="display: none;">
                    <img src="media/icons/search_24dp_FILL0_wght300_GRAD0_opsz24.svg" alt="Sin resultados">
                    <h3>No se encontraron resultados</h3>
                    <p>Introduce un DNI válido para buscar un paciente</p>
                </div>
            </main>
        </div>
    </div>

    <script src="js/celador-pacientes.js"></script>
</body>
</html>
