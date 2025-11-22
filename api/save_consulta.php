<?php
/**
 * API para guardar nueva consulta/episodio de urgencia
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session_manager.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../classes/Database.php';

// Verificar autenticación
requireAuth();

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Método no permitido', 405);
}

// Obtener datos JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    jsonError('Datos no válidos');
}

$sintomas = trim($data['sintomas'] ?? '');
$evidencias = trim($data['evidencias'] ?? '');
$tipo_evidencia = trim($data['tipo_evidencia'] ?? ''); // 'foto', 'audio', 'texto'

// Validaciones
if (empty($sintomas)) {
    jsonError('Debe proporcionar una descripción de los síntomas');
}

try {
    $db = Database::getInstance();
    $userId = getUserId();
    
    // Comenzar transacción
    $db->beginTransaction();
    
    try {
        // Insertar episodio de urgencia
        $episodioId = $db->insert('Episodio_Urgencia', [
            'id_paciente' => $userId,
            'fecha_llegada' => date('Y-m-d H:i:s'),
            'motivo_consulta' => $sintomas,
            'estado' => 'espera_triaje',
            'notas_adicionales' => $evidencias ?: null
        ]);
        
        // Buscar celador disponible y asignar automáticamente
        $sqlCelador = "
            SELECT c.id_celador, u.nombre, u.apellidos, b.nombre as box_nombre
            FROM Celador c
            INNER JOIN Usuario u ON c.id_celador = u.id_usuario
            LEFT JOIN Box b ON c.id_box = b.id_box
            WHERE c.disponible = 'si' 
            AND c.id_box IS NOT NULL
            AND u.estado = 'activo'
            ORDER BY c.id_celador ASC
            LIMIT 1
        ";
        
        $celadorDisponible = $db->selectOne($sqlCelador);
        
        $mensajeAsignacion = '';
        
        if ($celadorDisponible) {
            // Asignar al celador disponible
            $db->insert('Asignacion_Celador', [
                'id_episodio' => $episodioId,
                'id_celador' => $celadorDisponible['id_celador'],
                'fecha_asignacion' => date('Y-m-d H:i:s'),
                'estado' => 'pendiente'
            ]);
            
            $mensajeAsignacion = ' Has sido asignado a ' . $celadorDisponible['nombre'] . ' ' . $celadorDisponible['apellidos'] . ' en ' . $celadorDisponible['box_nombre'] . '.';
        } else {
            $mensajeAsignacion = ' En estos momentos no hay celadores disponibles. Serás asignado en breve.';
        }
        
        // Confirmar transacción
        $db->commit();
        
        jsonSuccess([
            'episodio_id' => $episodioId,
            'celador_asignado' => $celadorDisponible ? true : false,
            'mensaje' => 'Consulta registrada exitosamente.' . $mensajeAsignacion
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al guardar la consulta');
    }
}
