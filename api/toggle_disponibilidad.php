<?php
/**
 * API: Cambiar disponibilidad del celador
 * Asigna o libera box según disponibilidad
 */

require_once '../config/session_manager.php';
require_once '../classes/Database.php';

header('Content-Type: application/json');

requireAuth();
checkSession();

if (getUserType() !== 'celador') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$userId = getUserId();

try {
    $db = Database::getInstance();
    
    // Obtener estado actual del celador
    $sql = "SELECT id_box, disponible FROM Celador WHERE id_celador = :id_celador";
    $celador = $db->selectOne($sql, ['id_celador' => $userId]);
    
    if (!$celador) {
        throw new Exception('Celador no encontrado');
    }
    
    $disponibleActual = $celador['disponible'];
    $nuevaDisponibilidad = ($disponibleActual === 'si') ? 'no' : 'si';
    
    if ($nuevaDisponibilidad === 'si') {
        // Activar: buscar primer box libre
        $sqlBox = "SELECT id_box, nombre FROM Box WHERE estado = 'libre' ORDER BY id_box ASC LIMIT 1";
        $boxLibre = $db->selectOne($sqlBox);
        
        if (!$boxLibre) {
            echo json_encode([
                'success' => false, 
                'message' => 'No hay boxes disponibles en este momento'
            ]);
            exit;
        }
        
        // Asignar box al celador y marcarlo como ocupado
        $db->update('Celador', 
            ['disponible' => 'si', 'id_box' => $boxLibre['id_box']], 
            'id_celador = :id_celador',
            ['id_celador' => $userId]
        );
        
        $db->update('Box', 
            ['estado' => 'ocupado'], 
            'id_box = :id_box',
            ['id_box' => $boxLibre['id_box']]
        );
        
        echo json_encode([
            'success' => true,
            'disponible' => 'si',
            'box' => $boxLibre['nombre'],
            'id_box' => $boxLibre['id_box'],
            'message' => 'Ahora estás disponible y asignado al ' . $boxLibre['nombre']
        ]);
        
    } else {
        // Desactivar: liberar box
        $idBoxActual = $celador['id_box'];
        
        $db->update('Celador', 
            ['disponible' => 'no', 'id_box' => NULL], 
            'id_celador = :id_celador',
            ['id_celador' => $userId]
        );
        
        if ($idBoxActual) {
            $db->update('Box', 
                ['estado' => 'libre'], 
                'id_box = :id_box',
                ['id_box' => $idBoxActual]
            );
        }
        
        echo json_encode([
            'success' => true,
            'disponible' => 'no',
            'box' => null,
            'message' => 'Ahora estás no disponible. Box liberado.'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al cambiar disponibilidad: ' . $e->getMessage()
    ]);
}
