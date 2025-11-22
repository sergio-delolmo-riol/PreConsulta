<?php
/**
 * API: Get Paciente Detalle
 * Obtiene información detallada de un paciente/episodio incluyendo signos vitales
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
$idEpisodio = $_GET['id_episodio'] ?? null;

if (!$idEpisodio) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de episodio no proporcionado'
    ]);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Obtener información del paciente y episodio
    $sqlPaciente = "
        SELECT 
            eu.id_episodio,
            eu.fecha_llegada,
            eu.motivo_consulta,
            eu.estado,
            eu.notas_adicionales,
            eu.prioridad_actual,
            u.nombre,
            u.apellidos,
            u.dni,
            u.fecha_nacimiento,
            u.telefono,
            u.email,
            p.id_prioridad,
            p.nombre as nombre_prioridad,
            p.color_hex as color,
            t.presion_arterial,
            t.frecuencia_cardiaca,
            t.temperatura,
            t.saturacion_oxigeno,
            t.frecuencia_respiratoria,
            ae.id_asignacion,
            ae.estado as estado_asignacion,
            ae.fecha_asignacion
        FROM Episodio_Urgencia eu
        INNER JOIN Usuario u ON eu.id_paciente = u.id_usuario
        LEFT JOIN Prioridad p ON eu.prioridad_actual = p.id_prioridad
        LEFT JOIN Triaje t ON eu.id_episodio = t.id_episodio
        LEFT JOIN Asignacion_Enfermero ae ON eu.id_episodio = ae.id_episodio AND ae.id_enfermero = :id_enfermero
        WHERE eu.id_episodio = :id_episodio
    ";
    
    $paciente = $db->selectOne($sqlPaciente, [
        'id_episodio' => $idEpisodio,
        'id_enfermero' => $userId
    ]);
    
    if (!$paciente) {
        echo json_encode([
            'success' => false,
            'message' => 'Paciente no encontrado'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'paciente' => $paciente
    ]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error en get_paciente_detalle: ' . $e->getMessage());
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener detalles: ' . $e->getMessage()
    ]);
}
