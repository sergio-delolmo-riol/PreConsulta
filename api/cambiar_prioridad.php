<?php
/**
 * API: Cambiar prioridad de un episodio de urgencia
 * Solo accesible por celadores y enfermeros
 */

require_once '../config/session_manager.php';
require_once '../classes/Database.php';

header('Content-Type: application/json; charset=utf-8');

requireAuth();
checkSession();

$userType = getUserType();

// Verificar que sea celador o enfermero
if (!in_array($userType, ['celador', 'enfermero'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para cambiar la prioridad'
    ]);
    exit;
}

// Obtener datos del POST
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id_episodio']) || !isset($input['nueva_prioridad'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Faltan parámetros: id_episodio y nueva_prioridad son requeridos'
    ]);
    exit;
}

$idEpisodio = $input['id_episodio'];
$nuevaPrioridad = $input['nueva_prioridad'];

try {
    $db = Database::getInstance();
    
    // Verificar que la prioridad existe
    $sqlPrioridad = "SELECT id_prioridad, nombre FROM Prioridad WHERE id_prioridad = :id_prioridad";
    $prioridad = $db->selectOne($sqlPrioridad, ['id_prioridad' => $nuevaPrioridad]);
    
    if (!$prioridad) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Prioridad no válida'
        ]);
        exit;
    }
    
    // Verificar que el episodio existe
    $sqlEpisodio = "
        SELECT eu.id_episodio, eu.prioridad_actual, u.nombre, u.apellidos
        FROM Episodio_Urgencia eu
        INNER JOIN Usuario u ON eu.id_paciente = u.id_usuario
        WHERE eu.id_episodio = :id_episodio
    ";
    
    $episodio = $db->selectOne($sqlEpisodio, ['id_episodio' => $idEpisodio]);
    
    if (!$episodio) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Episodio no encontrado'
        ]);
        exit;
    }
    
    // Actualizar la prioridad
    $sqlUpdate = "
        UPDATE Episodio_Urgencia 
        SET prioridad_actual = :nueva_prioridad
        WHERE id_episodio = :id_episodio
    ";
    
    $db->query($sqlUpdate, [
        'nueva_prioridad' => $nuevaPrioridad,
        'id_episodio' => $idEpisodio
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Prioridad actualizada correctamente',
        'data' => [
            'id_episodio' => $idEpisodio,
            'prioridad_anterior' => $episodio['prioridad_actual'],
            'prioridad_nueva' => $nuevaPrioridad,
            'nombre_prioridad' => $prioridad['nombre'],
            'paciente' => $episodio['nombre'] . ' ' . $episodio['apellidos']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al cambiar prioridad: ' . $e->getMessage()
    ]);
}
