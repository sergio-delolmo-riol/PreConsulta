<?php
/**
 * API para obtener historial de consultas del paciente
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session_manager.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../classes/Database.php';

// Verificar autenticación
requireAuth();

try {
    $db = Database::getInstance();
    $userId = getUserId();
    
    // Consultar episodios del paciente
    $sql = "
        SELECT 
            e.id_episodio,
            e.fecha_llegada,
            e.sintomas,
            e.estado,
            p.nombre AS prioridad,
            p.color_hex AS color_prioridad
        FROM Episodio_Urgencia e
        LEFT JOIN Triaje t ON e.id_episodio = t.id_episodio
        LEFT JOIN Prioridad p ON t.id_prioridad = p.id_prioridad
        WHERE e.id_paciente = :id_paciente
        ORDER BY e.fecha_llegada DESC
    ";
    
    $consultas = $db->query($sql, ['id_paciente' => $userId]);
    
    // Formatear datos
    $resultado = [];
    
    // Verificar que la consulta devolvió resultados
    if (is_array($consultas)) {
        foreach ($consultas as $consulta) {
            $sintomas_preview = $consulta['sintomas'] 
                ? (strlen($consulta['sintomas']) > 100 
                    ? substr($consulta['sintomas'], 0, 100) . '...' 
                    : $consulta['sintomas'])
                : 'Sin descripción';
            
            $resultado[] = [
                'id_episodio' => $consulta['id_episodio'],
                'fecha' => formatDate($consulta['fecha_llegada'], true),
                'sintomas_preview' => $sintomas_preview,
                'estado' => getEpisodeStatusText($consulta['estado']),
                'prioridad' => $consulta['prioridad'] ?? 'Pendiente',
                'color_prioridad' => $consulta['color_prioridad'] ?? '#CCCCCC'
            ];
        }
    }
    
    jsonSuccess($resultado);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al cargar el historial');
    }
}
