<?php
/**
 * API: Toggle Estado Enfermero (Disponible/No Disponible)
 * Cambia el estado de disponibilidad del enfermero
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
        'message' => 'Acceso denegado. Solo enfermeros pueden usar esta función.'
    ]);
    exit;
}

$userId = getUserId();

try {
    $db = Database::getInstance();
    
    // Obtener estado actual
    $sqlEstado = "SELECT disponible FROM Enfermero WHERE id_enfermero = :id_enfermero";
    $enfermero = $db->selectOne($sqlEstado, ['id_enfermero' => $userId]);
    
    if (!$enfermero) {
        echo json_encode([
            'success' => false,
            'message' => 'Enfermero no encontrado'
        ]);
        exit;
    }
    
    // Cambiar estado
    $nuevoEstado = $enfermero['disponible'] ? 0 : 1;
    
    $sqlUpdate = "UPDATE Enfermero SET disponible = :disponible WHERE id_enfermero = :id_enfermero";
    $db->query($sqlUpdate, [
        'disponible' => $nuevoEstado,
        'id_enfermero' => $userId
    ]);
    
    echo json_encode([
        'success' => true,
        'disponible' => (bool)$nuevoEstado,
        'message' => $nuevoEstado ? 'Ahora estás disponible' : 'Cambiado a no disponible'
    ]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error en toggle_estado_enfermero: ' . $e->getMessage());
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al cambiar estado: ' . $e->getMessage()
    ]);
}
