<?php
/**
 * API: Obtener estado de la última consulta del paciente
 * Devuelve información de celador asignado, tiempo de espera, etc.
 */

require_once '../config/session_manager.php';
require_once '../classes/Database.php';

header('Content-Type: application/json');

requireAuth();
checkSession();

$userId = getUserId();

try {
    $db = Database::getInstance();
    
    // Obtener la última consulta del paciente
    $sql = "
        SELECT 
            eu.id_episodio,
            eu.fecha_llegada,
            eu.estado,
            eu.prioridad_actual,
            p.nombre_prioridad,
            p.tipo_prioridad,
            p.tiempo_max_atencion,
            ac.id_celador,
            uc.nombre as celador_nombre,
            uc.apellidos as celador_apellidos,
            b.nombre as box_nombre,
            b.ubicacion as box_ubicacion
        FROM Episodio_Urgencia eu
        LEFT JOIN Prioridad p ON eu.prioridad_actual = p.id_prioridad
        LEFT JOIN Asignacion_Celador ac ON eu.id_episodio = ac.id_episodio
        LEFT JOIN Usuario uc ON ac.id_celador = uc.id_usuario
        LEFT JOIN Celador c ON ac.id_celador = c.id_celador
        LEFT JOIN Box b ON c.id_box = b.id_box
        WHERE eu.id_paciente = :id_paciente
        ORDER BY eu.fecha_llegada DESC
        LIMIT 1
    ";
    
    $consulta = $db->selectOne($sql, ['id_paciente' => $userId]);
    
    if (!$consulta) {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró consulta activa'
        ]);
        exit;
    }
    
    // Calcular pacientes delante en la misma prioridad o mayor
    $sqlDelante = "
        SELECT COUNT(*) as total
        FROM Episodio_Urgencia eu2
        WHERE eu2.fecha_llegada < :fecha_llegada
        AND eu2.estado IN ('espera_triaje', 'en_triaje', 'espera_atencion')
        AND eu2.prioridad_actual >= :prioridad_actual
    ";
    
    $resultDelante = $db->selectOne($sqlDelante, [
        'fecha_llegada' => $consulta['fecha_llegada'],
        'prioridad_actual' => $consulta['prioridad_actual']
    ]);
    $pacientesDelante = $resultDelante['total'] ?? 0;
    
    // Calcular tiempo de espera estimado
    // Si hay pacientes delante: 5 minutos por paciente
    // Si no hay pacientes delante (eres el siguiente): 0 minutos
    $tiempoEsperaMinutos = $pacientesDelante * 5;
    
    // Formatear mensaje de tiempo
    if ($tiempoEsperaMinutos == 0) {
        $tiempoEsperaTexto = "Eres el siguiente";
    } else {
        $tiempoEsperaTexto = $tiempoEsperaMinutos . ' minutos';
    }
    
    // Preparar respuesta
    $response = [
        'success' => true,
        'consulta' => [
            'id_episodio' => $consulta['id_episodio'],
            'estado' => $consulta['estado'],
            'fecha_llegada' => $consulta['fecha_llegada'],
            'urgencia' => strtoupper($consulta['tipo_prioridad'] ?? 'MEDIA'),
            'nombre_prioridad' => $consulta['nombre_prioridad'] ?? 'Sin asignar',
            'tiempo_espera' => $tiempoEsperaTexto,
            'pacientes_delante' => $pacientesDelante,
            'celador_asignado' => false
        ]
    ];
    
    // Si hay celador asignado
    if ($consulta['id_celador']) {
        $response['consulta']['celador_asignado'] = true;
        $response['consulta']['celador'] = [
            'nombre' => $consulta['celador_nombre'] . ' ' . $consulta['celador_apellidos'],
            'box' => $consulta['box_nombre'] ?? 'Por asignar',
            'ubicacion' => $consulta['box_ubicacion'] ?? ''
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener estado de consulta: ' . $e->getMessage()
    ]);
}
