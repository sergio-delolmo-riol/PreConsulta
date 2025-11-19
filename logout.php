<?php
/**
 * Cerrar Sesión
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

require_once 'config/session_manager.php';

// Cerrar sesión
logoutUser();

// Redirigir al login
redirect('login.php');
