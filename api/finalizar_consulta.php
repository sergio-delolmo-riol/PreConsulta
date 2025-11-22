<?php
/**
 * API para finalizar una consulta
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
    
    // Iniciar transacción
    $db->beginTransaction();
    
    // Actualizar estado de la asignación a 'finalizado'
    $sqlAsignacion = "
        UPDATE Asignacion_Celador 
        SET estado = 'finalizado',
            fecha_finalizacion = NOW()
        WHERE id_episodio = :episodio_id 
        AND id_celador = :celador_id
    ";
    
    $db->query($sqlAsignacion, [
        'episodio_id' => $episodioId,
        'celador_id' => $celadorId
    ]);
    
    // Actualizar estado del episodio a 'alta' (dado de alta)
    $sqlEpisodio = "
        UPDATE Episodio_Urgencia 
        SET estado = 'alta',
            fecha_alta = NOW()
        WHERE id_episodio = :episodio_id
    ";
    
    $db->query($sqlEpisodio, [
        'episodio_id' => $episodioId
    ]);
    
    // Confirmar transacción
    $db->commit();
    
    jsonSuccess(null, 'Consulta finalizada correctamente');
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollback();
    }
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al finalizar la consulta');
    }
}
