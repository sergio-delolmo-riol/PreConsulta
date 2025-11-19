<?php
/**
 * Redirección de perfil.html a perfil-usuario.php
 */
require_once 'config/session_manager.php';

requireAuth();

redirect('perfil-usuario.php');
