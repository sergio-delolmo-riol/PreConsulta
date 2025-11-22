<?php
/**
 * API para marcar notificaciones como leídas
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

// Obtener datos del request
$data = json_decode(file_get_contents('php://input'), true);
$episodioId = isset($data['episodio_id']) ? (int)$data['episodio_id'] : 0;

if (!$episodioId) {
    jsonError('ID de episodio no válido');
}

try {
    $db = Database::getInstance();
    $celadorId = getUserId();
    
    // Marcar como leído
    $sql = "
        UPDATE Asignacion_Celador 
        SET leido = 'si'
        WHERE id_episodio = :episodio_id 
        AND id_celador = :celador_id
    ";
    
    $result = $db->execute($sql, [
        'episodio_id' => $episodioId,
        'celador_id' => $celadorId
    ]);
    
    if ($result) {
        jsonSuccess(null, 'Notificación marcada como leída');
    } else {
        jsonError('No se pudo marcar la notificación como leída');
    }
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al marcar la notificación');
    }
}
