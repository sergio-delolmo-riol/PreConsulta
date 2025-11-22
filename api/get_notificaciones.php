<?php
/**
 * API para obtener notificaciones de nuevos pacientes
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session_manager.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../classes/Database.php';

// Verificar autenticación
requireAuth();

// Verificar que sea celador
$userType = getUserType();
if ($userType !== 'celador') {
    jsonError('Acceso denegado', 403);
}

try {
    $db = Database::getInstance();
    $celadorId = getUserId();
    
    // Obtener las consultas no leídas asignadas al celador
    $sql = "
        SELECT 
            eu.id_episodio,
            eu.fecha_llegada,
            CONCAT(u.nombre, ' ', u.apellidos) as nombre_completo,
            u.dni,
            p.id_prioridad,
            p.nombre as nombre_prioridad,
            p.color_hex,
            ac.estado as estado_asignacion,
            ac.fecha_asignacion,
            ac.leido
        FROM Asignacion_Celador ac
        INNER JOIN Episodio_Urgencia eu ON ac.id_episodio = eu.id_episodio
        INNER JOIN Usuario u ON eu.id_paciente = u.id_usuario
        LEFT JOIN Prioridad p ON eu.prioridad_actual = p.id_prioridad
        WHERE ac.id_celador = :id_celador
        AND ac.estado IN ('pendiente', 'en_curso')
        AND (ac.leido = 'no' OR ac.leido IS NULL)
        ORDER BY eu.prioridad_actual ASC, eu.fecha_llegada ASC
    ";
    
    $notificaciones = $db->select($sql, ['id_celador' => $celadorId]);
    
    // Contar el total de notificaciones no leídas
    $totalNoLeidas = count($notificaciones);
    
    jsonSuccess([
        'notificaciones' => $notificaciones,
        'total' => $totalNoLeidas
    ]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al obtener las notificaciones');
    }
}
