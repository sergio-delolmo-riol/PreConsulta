<?php
/**
 * API para obtener estadísticas de consultas
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

try {
    $db = Database::getInstance();
    
    // Obtener consultas por hora y prioridad (últimos 30 días)
    $sqlPorHora = "
        SELECT 
            HOUR(eu.fecha_llegada) as hora,
            p.id_prioridad,
            p.nombre as nombre_prioridad,
            p.color_hex,
            COUNT(*) as total
        FROM Episodio_Urgencia eu
        LEFT JOIN Prioridad p ON eu.prioridad_actual = p.id_prioridad
        WHERE eu.fecha_llegada >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY HOUR(eu.fecha_llegada), p.id_prioridad
        ORDER BY hora ASC, p.id_prioridad ASC
    ";
    
    $consultasPorHora = $db->select($sqlPorHora);
    
    // Obtener resumen general
    $sqlResumen = "
        SELECT 
            COUNT(*) as total_consultas,
            COUNT(DISTINCT eu.id_paciente) as total_pacientes,
            AVG(TIMESTAMPDIFF(MINUTE, eu.fecha_llegada, eu.fecha_alta)) as tiempo_promedio_atencion
        FROM Episodio_Urgencia eu
        WHERE eu.fecha_llegada >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        AND eu.fecha_alta IS NOT NULL
    ";
    
    $resumen = $db->selectOne($sqlResumen);
    
    // Obtener consultas por prioridad
    $sqlPorPrioridad = "
        SELECT 
            p.id_prioridad,
            p.nombre as nombre_prioridad,
            p.color_hex,
            COUNT(*) as total
        FROM Episodio_Urgencia eu
        INNER JOIN Prioridad p ON eu.prioridad_actual = p.id_prioridad
        WHERE eu.fecha_llegada >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY p.id_prioridad
        ORDER BY p.id_prioridad ASC
    ";
    
    $consultasPorPrioridad = $db->select($sqlPorPrioridad);
    
    // Obtener consultas por día de la semana
    $sqlPorDiaSemana = "
        SELECT 
            DAYOFWEEK(eu.fecha_llegada) as dia_semana,
            COUNT(*) as total
        FROM Episodio_Urgencia eu
        WHERE eu.fecha_llegada >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DAYOFWEEK(eu.fecha_llegada)
        ORDER BY dia_semana ASC
    ";
    
    $consultasPorDia = $db->select($sqlPorDiaSemana);
    
    jsonSuccess([
        'consultasPorHora' => $consultasPorHora,
        'consultasPorPrioridad' => $consultasPorPrioridad,
        'consultasPorDia' => $consultasPorDia,
        'resumen' => $resumen
    ]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al obtener estadísticas');
    }
}
