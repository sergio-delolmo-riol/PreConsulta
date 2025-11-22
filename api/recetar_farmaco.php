<?php
/**
 * API: Recetar Fármaco
 * Crea una nueva receta para un paciente
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
$nombreFarmaco = $input['nombre_farmaco'] ?? null;
$principioActivo = $input['principio_activo'] ?? null;
$dosis = $input['dosis'] ?? null;
$viaAdministracion = $input['via_administracion'] ?? null;
$frecuencia = $input['frecuencia'] ?? null;
$duracion = $input['duracion'] ?? null;
$indicaciones = $input['indicaciones'] ?? null;

// Validar campos requeridos
if (!$idEpisodio || !$nombreFarmaco || !$dosis || !$viaAdministracion || !$frecuencia || !$duracion) {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan campos requeridos'
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
            'message' => 'No tienes permiso para recetar a este paciente'
        ]);
        exit;
    }
    
    // Insertar receta
    $sqlInsert = "
        INSERT INTO Receta (
            id_episodio,
            id_enfermero,
            nombre_farmaco,
            principio_activo,
            dosis,
            via_administracion,
            frecuencia,
            duracion,
            indicaciones,
            estado
        ) VALUES (
            :id_episodio,
            :id_enfermero,
            :nombre_farmaco,
            :principio_activo,
            :dosis,
            :via_administracion,
            :frecuencia,
            :duracion,
            :indicaciones,
            'activa'
        )
    ";
    
    $db->query($sqlInsert, [
        'id_episodio' => $idEpisodio,
        'id_enfermero' => $userId,
        'nombre_farmaco' => $nombreFarmaco,
        'principio_activo' => $principioActivo,
        'dosis' => $dosis,
        'via_administracion' => $viaAdministracion,
        'frecuencia' => $frecuencia,
        'duracion' => $duracion,
        'indicaciones' => $indicaciones
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Receta guardada correctamente',
        'id_receta' => $db->getConnection()->lastInsertId()
    ]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error en recetar_farmaco: ' . $e->getMessage());
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar receta: ' . $e->getMessage()
    ]);
}
