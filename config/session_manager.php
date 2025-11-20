<?php
/**
 * Gestor de Sesiones
 * Proyecto: PreConsulta - Centro de Triaje Digital
 * Fecha: 19/11/2025
 * 
 * Maneja la autenticación y autorización de usuarios
 */

// Cargar configuración si no está cargada
if (!defined('SESSION_LIFETIME')) {
    require_once __DIR__ . '/database.php';
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario está autenticado
 * @return bool True si está autenticado, false si no
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Redirige al login si no está autenticado
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Obtiene el ID del usuario actual
 * @return int|null ID del usuario o null si no está autenticado
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtiene el email del usuario actual
 * @return string|null Email del usuario o null si no está autenticado
 */
function getUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

/**
 * Obtiene el nombre del usuario actual
 * @return string|null Nombre del usuario o null si no está autenticado
 */
function getUserName() {
    return $_SESSION['user_name'] ?? null;
}

/**
 * Obtiene el tipo de usuario (paciente, enfermero, celador)
 * @return string|null Tipo de usuario o null si no está autenticado
 */
function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

/**
 * Inicia sesión para un usuario
 * @param int $userId ID del usuario
 * @param string $email Email del usuario
 * @param string $nombre Nombre del usuario
 * @param string $userType Tipo de usuario (paciente, enfermero, celador)
 */
function loginUser($userId, $email, $nombre, $userType = 'paciente') {
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $nombre;
    $_SESSION['user_type'] = $userType;
    $_SESSION['login_time'] = time();
    
    // Regenerar ID de sesión por seguridad
    session_regenerate_id(true);
}

/**
 * Cierra la sesión del usuario
 */
function logoutUser() {
    // Limpiar todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    
    // Destruir la sesión
    session_destroy();
}

/**
 * Verifica si la sesión ha expirado
 * @return bool True si ha expirado, false si no
 */
function isSessionExpired() {
    if (!isset($_SESSION['login_time'])) {
        return true;
    }
    
    $sessionLifetime = SESSION_LIFETIME; // Definido en config/database.php
    $currentTime = time();
    $loginTime = $_SESSION['login_time'];
    
    return ($currentTime - $loginTime) > $sessionLifetime;
}

/**
 * Actualiza el timestamp de la sesión
 */
function updateSessionActivity() {
    $_SESSION['login_time'] = time();
}

/**
 * Verifica y actualiza la sesión
 */
function checkSession() {
    if (isAuthenticated()) {
        if (isSessionExpired()) {
            logoutUser();
            header('Location: login.php?error=session_expired');
            exit();
        } else {
            updateSessionActivity();
        }
    }
}
