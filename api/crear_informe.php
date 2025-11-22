<?php
/**
 * API: Crear Informe Médico
 * Crea un nuevo informe médico para un paciente
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

$idEpisodio = $input['id_episodio'] ?? null;
$diagnosticoPreliminar = $input['diagnostico_preliminar'] ?? null;
$tratamientoAplicado = $input['tratamiento_aplicado'] ?? null;
$observaciones = $input['observaciones'] ?? null;
$evolucion = $input['evolucion'] ?? null;
$derivadoA = $input['derivado_a'] ?? null;
$requiereSeguimiento = $input['requiere_seguimiento'] ?? false;

// Validar campos requeridos
if (!$idEpisodio || !$diagnosticoPreliminar) {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan campos requeridos (episodio y diagnóstico)'
    ]);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Verificar que el episodio existe y está asignado al enfermero
    $sqlVerificar = "
        SELECT ae.id_asignacion 
        FROM Asignacion_Enfermero ae
        WHERE ae.id_episodio = :id_episodio 
        AND ae.id_enfermero = :id_enfermero
        AND ae.estado IN ('asignado', 'atendiendo')
    ";
    
    $asignacion = $db->selectOne($sqlVerificar, [
        'id_episodio' => $idEpisodio,
        'id_enfermero' => $userId
    ]);
    
    if (!$asignacion) {
        echo json_encode([
            'success' => false,
            'message' => 'No tienes permiso para crear informes de este paciente'
        ]);
        exit;
    }
    
    // Insertar informe
    $sqlInsert = "
        INSERT INTO Informe_Medico (
            id_episodio,
            id_enfermero,
            diagnostico_preliminar,
            tratamiento_aplicado,
            observaciones,
            evolucion,
            derivado_a,
            requiere_seguimiento
        ) VALUES (
            :id_episodio,
            :id_enfermero,
            :diagnostico_preliminar,
            :tratamiento_aplicado,
            :observaciones,
            :evolucion,
            :derivado_a,
            :requiere_seguimiento
        )
    ";
    
    $db->query($sqlInsert, [
        'id_episodio' => $idEpisodio,
        'id_enfermero' => $userId,
        'diagnostico_preliminar' => $diagnosticoPreliminar,
        'tratamiento_aplicado' => $tratamientoAplicado,
        'observaciones' => $observaciones,
        'evolucion' => $evolucion,
        'derivado_a' => $derivadoA,
        'requiere_seguimiento' => $requiereSeguimiento ? 1 : 0
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Informe médico guardado correctamente',
        'id_informe' => $db->getConnection()->lastInsertId()
    ]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error en crear_informe: ' . $e->getMessage());
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar informe: ' . $e->getMessage()
    ]);
}
