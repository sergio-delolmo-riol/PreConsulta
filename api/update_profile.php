<?php
/**
 * API para actualizar datos del paciente
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

$direccion = trim($data['address'] ?? '');
$condiciones_medicas = trim($data['limitations'] ?? '');
$fecha_nacimiento = trim($data['birthDate'] ?? '');

// Validaciones
if (empty($direccion) && empty($condiciones_medicas) && empty($fecha_nacimiento)) {
    jsonError('Debe proporcionar al menos un dato para actualizar');
}

try {
    $db = Database::getInstance();
    $userId = getUserId();
    
    // Preparar datos para actualizar
    $updateData = [];
    
    if (!empty($direccion)) {
        $updateData['direccion'] = $direccion;
    }
    
    if (!empty($condiciones_medicas)) {
        $updateData['condiciones_medicas'] = $condiciones_medicas;
    }
    
    if (!empty($fecha_nacimiento)) {
        // Validar formato de fecha
        $date = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
        if (!$date) {
            jsonError('Formato de fecha no válido');
        }
        $updateData['fecha_nacimiento'] = $fecha_nacimiento;
    }
    
    // Actualizar en la tabla Paciente
    $result = $db->update(
        'Paciente',
        $updateData,
        ['id_paciente' => $userId]
    );
    
    if ($result) {
        jsonSuccess(null, 'Datos actualizados correctamente');
    } else {
        jsonError('No se pudieron actualizar los datos');
    }
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al actualizar los datos');
    }
}
