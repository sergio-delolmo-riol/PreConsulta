<?php
/**
 * API para buscar paciente por DNI
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

// Obtener DNI del query parameter
$dni = isset($_GET['dni']) ? trim($_GET['dni']) : '';

if (empty($dni)) {
    jsonError('DNI no proporcionado');
}

try {
    $db = Database::getInstance();
    
    // Buscar paciente por DNI
    $sql = "
        SELECT 
            u.id_usuario as id_paciente,
            u.dni,
            u.nombre,
            u.apellidos,
            u.email,
            u.telefono,
            u.fecha_registro,
            p.alergias,
            p.grupo_sanguineo,
            p.seguro_medico,
            COUNT(eu.id_episodio) as total_consultas
        FROM Usuario u
        INNER JOIN Paciente p ON u.id_usuario = p.id_paciente
        LEFT JOIN Episodio_Urgencia eu ON u.id_usuario = eu.id_paciente
        WHERE u.dni = :dni
        GROUP BY u.id_usuario
        LIMIT 1
    ";
    
    $result = $db->selectOne($sql, ['dni' => $dni]);
    
    if (!$result) {
        jsonError('Paciente no encontrado', 404);
    }
    
    jsonSuccess($result, 'Paciente encontrado');
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al buscar el paciente');
    }
}
