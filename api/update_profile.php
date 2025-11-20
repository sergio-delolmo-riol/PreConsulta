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

$dni = trim($data['dni'] ?? '');
$telefono = trim($data['phone'] ?? '');
$direccion = trim($data['address'] ?? '');
$condiciones_medicas = trim($data['limitations'] ?? '');
$fecha_nacimiento = trim($data['birthDate'] ?? '');

// Validaciones
if (empty($dni) && empty($telefono) && empty($direccion) && empty($condiciones_medicas) && empty($fecha_nacimiento)) {
    jsonError('Debe proporcionar al menos un dato para actualizar');
}

try {
    $db = Database::getInstance();
    $userId = getUserId();
    
    // Actualizar DNI en tabla Usuario si se proporciona
    if (!empty($dni)) {
        // Validar formato de DNI
        if (!preg_match('/^[0-9]{8}[A-Za-z]$/', $dni)) {
            jsonError('Formato de DNI no válido. Debe ser 8 números seguidos y una letra al final');
        }
        
        // Verificar que el DNI no esté ya registrado por otro usuario
        $existingDni = $db->selectOne(
            'SELECT id_usuario FROM Usuario WHERE dni = :dni AND id_usuario != :id_usuario',
            ['dni' => $dni, 'id_usuario' => $userId]
        );
        
        if ($existingDni) {
            jsonError('Este DNI ya está registrado por otro usuario');
        }
        
        $sqlUpdateDni = "UPDATE Usuario SET dni = :dni WHERE id_usuario = :id_usuario";
        $db->query($sqlUpdateDni, [
            'dni' => $dni,
            'id_usuario' => $userId
        ]);
    }
    
    // Actualizar teléfono en tabla Usuario si se proporciona
    if (!empty($telefono)) {
        // Validar formato de teléfono
        if (!preg_match('/^[0-9\s\+\-\(\)]{9,15}$/', $telefono)) {
            jsonError('Formato de teléfono no válido');
        }
        
        $sqlUpdatePhone = "UPDATE Usuario SET telefono = :telefono WHERE id_usuario = :id_usuario";
        $db->query($sqlUpdatePhone, [
            'telefono' => $telefono,
            'id_usuario' => $userId
        ]);
    }
    
    // Preparar datos para actualizar en tabla Usuario
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
    
    // Actualizar en la tabla Usuario si hay datos
    if (!empty($updateData)) {
        $db->update(
            'Usuario',
            $updateData,
            'id_usuario = :id_usuario',
            ['id_usuario' => $userId]
        );
    }
    
    jsonSuccess(null, 'Datos actualizados correctamente');
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al actualizar los datos');
    }
}
