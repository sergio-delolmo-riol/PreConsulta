<?php
/**
 * API para obtener detalles de una consulta
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
if (!in_array($userType, ['celador', 'enfermero'])) {
    jsonError('Acceso denegado', 403);
}

// Obtener ID de episodio
$episodioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$episodioId) {
    jsonError('ID de episodio no válido');
}

try {
    $db = Database::getInstance();
    
    // Obtener detalles completos de la consulta
    $sql = "
        SELECT 
            eu.id_episodio,
            eu.fecha_llegada,
            eu.motivo_consulta,
            eu.estado,
            eu.notas_adicionales,
            eu.prioridad_actual,
            CONCAT(u.nombre, ' ', u.apellidos) as nombre_completo,
            u.dni,
            u.telefono,
            u.email,
            p.id_prioridad as codigo_prioridad,
            p.nombre as nombre_prioridad,
            p.color_hex as color,
            p.tiempo_max_atencion as tiempo_espera_max,
            t.frecuencia_cardiaca,
            t.presion_arterial,
            t.temperatura,
            t.saturacion_oxigeno,
            t.nivel_consciencia as dolor_escala,
            b.nombre as numero_box,
            b.estado as tipo_box
        FROM Episodio_Urgencia eu
        INNER JOIN Usuario u ON eu.id_paciente = u.id_usuario
        LEFT JOIN Prioridad p ON eu.prioridad_actual = p.id_prioridad
        LEFT JOIN Triaje t ON eu.id_episodio = t.id_episodio
        LEFT JOIN Box b ON eu.box_asignado = b.id_box
        WHERE eu.id_episodio = :id_episodio
        LIMIT 1
    ";
    
    $consulta = $db->selectOne($sql, ['id_episodio' => $episodioId]);
    
    if (!$consulta) {
        jsonError('Consulta no encontrada', 404);
    }
    
    // Obtener información adicional del paciente
    $sqlPaciente = "
        SELECT 
            alergias,
            grupo_sanguineo,
            seguro_medico
        FROM Paciente
        WHERE id_paciente = (
            SELECT id_paciente FROM Episodio_Urgencia WHERE id_episodio = :id_episodio
        )
    ";
    
    $pacienteInfo = $db->selectOne($sqlPaciente, ['id_episodio' => $episodioId]);
    
    if ($pacienteInfo) {
        $consulta = array_merge($consulta, $pacienteInfo);
    }
    
    jsonSuccess([
        'consulta' => $consulta
    ]);
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        jsonError('Error: ' . $e->getMessage());
    } else {
        jsonError('Error al obtener los detalles de la consulta');
    }
}
