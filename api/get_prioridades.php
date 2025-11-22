<?php
/**
 * API: Obtener lista de prioridades disponibles
 */

require_once '../config/session_manager.php';
require_once '../classes/Database.php';

header('Content-Type: application/json; charset=utf-8');

requireAuth();
checkSession();

try {
    $db = Database::getInstance();
    
    $sql = "
        SELECT 
            id_prioridad,
            nombre AS nombre_prioridad,
            tipo_prioridad,
            color_hex,
            tiempo_max_atencion,
            descripcion
        FROM Prioridad
        ORDER BY id_prioridad ASC
    ";
    
    $prioridades = $db->select($sql);
    
    echo json_encode([
        'success' => true,
        'data' => $prioridades
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener prioridades: ' . $e->getMessage()
    ]);
}
