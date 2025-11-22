<?php
/**
 * API: Get Historial Médico
 * Obtiene el historial médico completo de un paciente
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
    
    // Obtener ID del paciente del episodio actual
    $sqlPaciente = "SELECT id_paciente FROM Episodio_Urgencia WHERE id_episodio = :id_episodio";
    $episodio = $db->selectOne($sqlPaciente, ['id_episodio' => $idEpisodio]);
    
    if (!$episodio) {
        echo json_encode([
            'success' => false,
            'message' => 'Episodio no encontrado'
        ]);
        exit;
    }
    
    $idPaciente = $episodio['id_paciente'];
    
    // Obtener episodios anteriores del paciente (últimos 10)
    $sqlEpisodios = "
        SELECT 
            eu.id_episodio,
            eu.fecha_llegada,
            eu.fecha_alta,
            eu.motivo_consulta,
            eu.estado,
            p.nombre as nombre_prioridad
        FROM Episodio_Urgencia eu
        LEFT JOIN Prioridad p ON eu.prioridad_actual = p.id_prioridad
        WHERE eu.id_paciente = :id_paciente
        AND eu.id_episodio != :id_episodio_actual
        ORDER BY eu.fecha_llegada DESC
        LIMIT 10
    ";
    
    $episodiosAnteriores = $db->select($sqlEpisodios, [
        'id_paciente' => $idPaciente,
        'id_episodio_actual' => $idEpisodio
    ]);
    
    // Obtener informes médicos del paciente (últimos 10)
    $sqlInformes = "
        SELECT 
            im.id_informe,
            im.fecha_creacion,
            im.diagnostico_preliminar,
            im.tratamiento_aplicado,
            im.observaciones,
            im.derivado_a,
            im.requiere_seguimiento,
            u.nombre as nombre_enfermero,
            u.apellidos as apellidos_enfermero
        FROM Informe_Medico im
        INNER JOIN Episodio_Urgencia eu ON im.id_episodio = eu.id_episodio
        LEFT JOIN Usuario u ON im.id_enfermero = u.id_usuario
        WHERE eu.id_paciente = :id_paciente
        ORDER BY im.fecha_creacion DESC
        LIMIT 10
    ";
    
    $informes = $db->select($sqlInformes, ['id_paciente' => $idPaciente]);
    
    // Obtener recetas del paciente (últimas 15)
    $sqlRecetas = "
        SELECT 
            r.id_receta,
            r.fecha_prescripcion,
            r.nombre_farmaco,
            r.principio_activo,
            r.dosis,
            r.via_administracion,
            r.frecuencia,
            r.duracion,
            r.indicaciones,
            r.estado,
            u.nombre as nombre_enfermero,
            u.apellidos as apellidos_enfermero
        FROM Receta r
        INNER JOIN Episodio_Urgencia eu ON r.id_episodio = eu.id_episodio
        LEFT JOIN Usuario u ON r.id_enfermero = u.id_usuario
        WHERE eu.id_paciente = :id_paciente
        ORDER BY r.fecha_prescripcion DESC
        LIMIT 15
    ";
    
    $recetas = $db->select($sqlRecetas, ['id_paciente' => $idPaciente]);
    
    echo json_encode([
        'success' => true,
        'historial' => [
            'episodios_anteriores' => $episodiosAnteriores,
            'informes' => $informes,
            'recetas' => $recetas
        ]
    ]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        error_log('Error en get_historial_medico: ' . $e->getMessage());
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener historial: ' . $e->getMessage()
    ]);
}
