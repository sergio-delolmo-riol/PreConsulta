<?php
/**
 * Funciones de ayuda globales
 * Proyecto: PreConsulta - Centro de Triaje Digital
 * Fecha: 19/11/2025
 */

/**
 * Sanitiza una cadena de texto para prevenir XSS
 * @param string $text Texto a sanitizar
 * @return string Texto sanitizado
 */
function sanitize($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Formatea una fecha al formato español
 * @param string $date Fecha en formato YYYY-MM-DD
 * @param bool $includeTime Si incluir la hora
 * @return string Fecha formateada
 */
function formatDate($date, $includeTime = false) {
    if (empty($date)) {
        return 'No registrada';
    }
    
    $timestamp = strtotime($date);
    if ($includeTime) {
        return date('d/m/Y H:i', $timestamp);
    }
    return date('d/m/Y', $timestamp);
}

/**
 * Formatea una fecha completa en español
 * @param string $date Fecha en formato YYYY-MM-DD
 * @return string Fecha formateada en español
 */
function formatDateLong($date) {
    if (empty($date)) {
        return 'No registrada';
    }
    
    $timestamp = strtotime($date);
    $months = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];
    
    $day = date('j', $timestamp);
    $month = $months[(int)date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "$day de $month de $year";
}

/**
 * Formatea un número de teléfono español
 * @param string $phone Número de teléfono
 * @return string Teléfono formateado
 */
function formatPhone($phone) {
    if (empty($phone)) {
        return 'No registrado';
    }
    
    // Eliminar espacios y caracteres especiales
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Formatear como 698 24 47 12
    if (strlen($phone) === 9) {
        return substr($phone, 0, 3) . ' ' . substr($phone, 3, 2) . ' ' . substr($phone, 5, 2) . ' ' . substr($phone, 7, 2);
    }
    
    return $phone;
}

/**
 * Calcula la edad a partir de una fecha de nacimiento
 * @param string $birthDate Fecha de nacimiento en formato YYYY-MM-DD
 * @return int Edad en años
 */
function calculateAge($birthDate) {
    if (empty($birthDate)) {
        return 0;
    }
    
    $birth = new DateTime($birthDate);
    $today = new DateTime('today');
    return $birth->diff($today)->y;
}

/**
 * Obtiene el color hexadecimal de una prioridad
 * @param string $prioridad Tipo de prioridad
 * @return string Color hex
 */
function getPriorityColor($prioridad) {
    $colors = [
        'alta' => '#FF0000',
        'urgente' => '#FF6600',
        'media' => '#FFD700',
        'normal' => '#90EE90',
        'baja' => '#00BFFF'
    ];
    
    return $colors[strtolower($prioridad)] ?? '#CCCCCC';
}

/**
 * Obtiene el texto en español de un estado de episodio
 * @param string $estado Estado del episodio
 * @return string Estado en español
 */
function getEpisodeStatusText($estado) {
    $estados = [
        'espera_triaje' => 'En espera de triaje',
        'en_triaje' => 'En proceso de triaje',
        'espera_atencion' => 'Esperando atención médica',
        'en_atencion' => 'En atención médica',
        'finalizado' => 'Finalizado',
        'cancelado' => 'Cancelado'
    ];
    
    return $estados[$estado] ?? $estado;
}

/**
 * Redirige a una página
 * @param string $url URL de destino
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Muestra un mensaje de error JSON
 * @param string $message Mensaje de error
 * @param int $code Código HTTP
 */
function jsonError($message, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'error' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Muestra un mensaje de éxito JSON
 * @param mixed $data Datos a devolver
 * @param string $message Mensaje opcional
 */
function jsonSuccess($data = null, $message = null) {
    header('Content-Type: application/json; charset=utf-8');
    $response = ['success' => true];
    
    if ($message) {
        $response['message'] = $message;
    }
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Valida un email
 * @param string $email Email a validar
 * @return bool True si es válido
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida un teléfono español
 * @param string $phone Teléfono a validar
 * @return bool True si es válido
 */
function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) === 9;
}

/**
 * Genera un DNI/NIE aleatorio válido (solo para testing)
 * @return string DNI generado
 */
function generateDNI() {
    $number = rand(10000000, 99999999);
    $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
    $letter = $letters[$number % 23];
    return $number . $letter;
}
