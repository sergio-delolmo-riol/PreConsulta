<?php
/**
 * API: Iniciar Atención
 * Marca una asignación como "atendiendo" y registra la hora de inicio
 */

header('Content-Type: application/json; charset=utf-8');

require_once '../config/session_manager.php';
require_once '../classes/Database.php';

// Verificar autenticación
requireAuth();
checkSession();

// Verificar que sea enfermero
$userType = getUserType();
if ($userType !== 'enfermero') {
    echo json_encode([
        'success' => false,
        'message' => 'Acceso denegado'
    ]);
    exit;
}

$userId = getUserId();

// Obtener datos del POST
$input = json_decode(file_get_contents('php://input'), true);
$idAsignacion = $input['id_asignacion'] ?? null;

if (!$idAsignacion) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de asignación no proporcionado'
    ]);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Verificar que la asignación pertenece al enfermero
    $sqlVerificar = "
        SELECT id_asignacion, id_episodio 
        FROM Asignacion_Enfermero 
        WHERE id_asignacion = :id_asignacion 
        AND id_enfermero = :id_enfermero
        AND estado = 'asignado'
    ";
    
    $asignacion = $db->selectOne($sqlVerificar, [
        'id_asignacion' => $idAsignacion,
        'id_enfermero' => $userId
    ]);
    
    if (!$asignacion) {
        echo json_encode([
            'success' => false,
            'message' => 'Asignación no encontrada o ya iniciada'
        ]);
        exit;
    }
    
    // Actualizar asignación
    $sqlUpdate = "
        UPDATE Asignacion_Enfermero 
        SET estado = 'atendiendo',
            fecha_inicio_atencion = NOW()
        WHERE id_asignacion = :id_asignacion
    ";
    
    $db->query($sqlUpdate, ['id_asignacion' => $idAsignacion]);
    
    // Actualizar estado del episodio
    $sqlEpisodio = "
        UPDATE Episodio_Urgencia 
        SET estado = 'en_atencion'
        WHERE id_episodio = :id_episodio
    ";
    
    $db->query($sqlEpisodio, ['id_episodio' => $asignacion['id_episodio']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Atención iniciada correctamente'
    ]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error en iniciar_atencion: ' . $e->getMessage());
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al iniciar atención: ' . $e->getMessage()
    ]);
}
