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
        
        // Confirmar transacción
        $db->commit();
        
        jsonSuccess([
            'episodio_id' => $episodioId,
            'mensaje' => 'Consulta registrada exitosamente'
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
