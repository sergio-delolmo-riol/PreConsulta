<?php
/**
 * API para obtener historial de consultas de un paciente
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session_manager.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../classes/Database.php';

// Verificar autenticación
requireAuth();

// Verificar que sea celador o enfermero
$userType = getUserType();
if ($userType !== 'celador' && $userType !== 'enfermero') {
    jsonError('Acceso denegado', 403);
}

// Obtener ID del paciente
$idPaciente = isset($_GET['id_paciente']) ? (int)$_GET['id_paciente'] : 0;

if (!$idPaciente) {
    jsonError('ID de paciente no válido');
}

try {
    $db = Database::getInstance();
    
    // Obtener historial completo de consultas
    $sql = "
        SELECT 
            eu.id_episodio,
            eu.fecha_llegada,
            eu.fecha_alta,
            eu.motivo_consulta,
            eu.estado,
            eu.notas_adicionales,
            p.id_prioridad,
            p.nombre as nombre_prioridad,
            p.color_hex,
            b.nombre as nombre_box,
            t.frecuencia_cardiaca,
            t.presion_arterial,
            t.temperatura,
            t.saturacion_oxigeno,
            t.nivel_consciencia,
            ac.estado as estado_asignacion,
            ac.fecha_asignacion,
            ac.fecha_finalizacion,
            CONCAT(u_celador.nombre, ' ', u_celador.apellidos) as nombre_celador
        FROM Episodio_Urgencia eu
        LEFT JOIN Prioridad p ON eu.prioridad_actual = p.id_prioridad
        LEFT JOIN Box b ON eu.box_asignado = b.id_box
        LEFT JOIN Triaje t ON eu.id_episodio = t.id_episodio
        LEFT JOIN Asignacion_Celador ac ON eu.id_episodio = ac.id_episodio
        LEFT JOIN Celador c ON ac.id_celador = c.id_celador
        LEFT JOIN Usuario u_celador ON c.id_celador = u_celador.id_usuario
        WHERE eu.id_paciente = :id_paciente
        ORDER BY eu.fecha_llegada DESC
    ";
    
    $historial = $db->select($sql, ['id_paciente' => $idPaciente]);
    
    jsonSuccess([
        'historial' => $historial,
        'total' => count($historial)
    ]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al obtener el historial');
    }
}
