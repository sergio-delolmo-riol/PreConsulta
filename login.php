<?php
/**
 * Sistema de Login
 * Proyecto: PreConsulta - Centro de Triaje Digital
 * Valida usuarios contra la base de datos
 */

// Headers anti-caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'config/database.php';
require_once 'config/session_manager.php';
require_once 'config/helpers.php';
require_once 'classes/Database.php';

// Si ya está autenticado, redirigir al index
if (isAuthenticated()) {
    redirect('index.php');
}

$error = '';

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validaciones básicas
    if (empty($email) || empty($password)) {
        $error = 'Por favor, complete todos los campos.';
    } else if (!validateEmail($email)) {
        $error = 'El correo electrónico no es válido.';
    } else {
        try {
            $db = Database::getInstance();
            
            // Buscar usuario por email
            $sqlUsuario = "SELECT id_usuario, nombre, apellidos, email, password, estado 
                          FROM Usuario 
                          WHERE email = :email 
                          LIMIT 1";
            $user = $db->selectOne($sqlUsuario, ['email' => $email]);
            
            // DEBUG: Temporal para ver qué está pasando
            if (APP_DEBUG && !$user) {
                $error = 'DEBUG: Usuario no encontrado en la base de datos con email: ' . $email;
            } else if (APP_DEBUG && $user) {
                error_log('DEBUG LOGIN: Usuario encontrado: ' . print_r($user, true));
            }
            
            if ($user) {
                // Verificar si la cuenta está activa
                if (isset($user['estado']) && $user['estado'] !== 'activo') {
                    $error = 'Tu cuenta está inactiva o bloqueada. Contacta con soporte.';
                } 
                // Verificar la contraseña
                else if (isset($user['password']) && password_verify($password, $user['password'])) {
                    // DEBUG: Contraseña correcta
                    if (APP_DEBUG) {
                        error_log('DEBUG LOGIN: Contraseña verificada correctamente');
                        error_log('DEBUG LOGIN: ID Usuario: ' . $user['id_usuario']);
                    }
                    // Determinar el tipo de usuario
                    $userType = 'paciente'; // Por defecto
                    
                    // Verificar si es enfermero
                    $sqlEnfermero = "SELECT id_enfermero FROM Enfermero WHERE id_enfermero = :id LIMIT 1";
                    $enfermero = $db->selectOne($sqlEnfermero, ['id' => $user['id_usuario']]);
                    
                    if (APP_DEBUG) {
                        error_log('DEBUG LOGIN: Enfermero query result: ' . print_r($enfermero, true));
                    }
                    
                    if ($enfermero) {
                        $userType = 'enfermero';
                        if (APP_DEBUG) {
                            error_log('DEBUG LOGIN: Usuario es ENFERMERO');
                        }
                    } else {
                        // Verificar si es celador
                        $sqlCelador = "SELECT id_celador FROM Celador WHERE id_celador = :id LIMIT 1";
                        $celador = $db->selectOne($sqlCelador, ['id' => $user['id_usuario']]);
                        
                        if (APP_DEBUG) {
                            error_log('DEBUG LOGIN: Celador query result: ' . print_r($celador, true));
                        }
                        
                        if ($celador) {
                            $userType = 'celador';
                            if (APP_DEBUG) {
                                error_log('DEBUG LOGIN: Usuario es CELADOR');
                            }
                        } else {
                            if (APP_DEBUG) {
                                error_log('DEBUG LOGIN: Usuario es PACIENTE (default)');
                            }
                        }
                    }
                    
                    if (APP_DEBUG) {
                        error_log('DEBUG LOGIN: Tipo de usuario final: ' . $userType);
                    }
                    
                    // Actualizar último acceso
                    $sqlUpdate = "UPDATE Usuario SET ultimo_acceso = :fecha WHERE id_usuario = :id";
                    $db->query($sqlUpdate, [
                        'fecha' => date('Y-m-d H:i:s'),
                        'id' => $user['id_usuario']
                    ]);
                    
                    // Iniciar sesión
                    $nombreCompleto = $user['nombre'] . ' ' . $user['apellidos'];
                    loginUser($user['id_usuario'], $user['email'], $nombreCompleto, $userType);
                    
                    // Redirigir según el tipo de usuario
                    if ($userType === 'paciente') {
                        redirect('index.php');
                    } else if ($userType === 'celador') {
                        redirect('celador-dashboard.php');
                    } else if ($userType === 'enfermero') {
                        redirect('enfermero-dashboard.php');
                    } else {
                        redirect('index.php');
                    }
                } else {
                    // DEBUG: Contraseña incorrecta
                    if (APP_DEBUG) {
                        error_log('DEBUG LOGIN: Contraseña incorrecta para email: ' . $email);
                        error_log('DEBUG LOGIN: Hash almacenado: ' . ($user['password'] ?? 'N/A'));
                    }
                    $error = 'Correo o contraseña incorrectos.';
                }
            } else {
                if (APP_DEBUG) {
                    error_log('DEBUG LOGIN: Usuario no encontrado o no es array');
                }
                $error = 'Correo o contraseña incorrectos.';
            }
            
        } catch (Exception $e) {
            if (APP_DEBUG) {
                $error = 'Error de base de datos: ' . $e->getMessage();
            } else {
                $error = 'Error al procesar la solicitud. Inténtalo de nuevo.';
            }
        }
    }
}

// Verificar si hay mensaje de sesión expirada
if (isset($_GET['error']) && $_GET['error'] === 'session_expired') {
    $error = 'Tu sesión ha expirado. Por favor, inicia sesión de nuevo.';
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - PreConsulta</title>
    <link rel="icon" type="image/svg+xml" href="media/icons/cardiology_24dp_007AFF_FILL1_wght300_GRAD-25_opsz24.svg">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="login-body">

    <header class="login-header">
        <h1 class="login-title">
            <span class="welcome-text">Bienvenido al</span>
            <span class="app-name">Centro de Triaje Digital</span>
        </h1>
        <div class="header-icon-login">
            <img src="media/icons/cardiology_24dp_007AFF_FILL1_wght300_GRAD-25_opsz24.svg" alt="Logo PreConsulta" class="app-logo">
        </div>
    </header>

    <main class="login-main">
        <form id="loginForm" class="login-form" method="POST" action="login.php" novalidate>
            <div class="form-fields-container">
                <h2 class="form-title">INICIA SESIÓN</h2>

                <?php if ($error): ?>
                <div class="error-message" style="background-color: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 16px; text-align: center; font-size: 14px;">
                    <?= sanitize($error) ?>
                </div>
                <?php endif; ?>

                <div class="form-field">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" placeholder="ejemplo@gmail.com" 
                           value="<?= sanitize($_POST['email'] ?? '') ?>"
                           aria-required="true" aria-describedby="error-email" 
                           autocomplete="email" aria-invalid="false">
                    <div class="error" id="error-email" aria-live="polite"></div>
                </div>

                <div class="form-field">
                    <label for="password">Contraseña</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" 
                            autocomplete="new-password" 
                            data-password="true"
                            aria-required="true"
                            aria-describedby="error-password" 
                            aria-invalid="false">
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Mostrar u ocultar contraseña" tabindex="-1">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="error" id="error-password" aria-live="polite"></div>
                </div>

                <button type="button" class="forgot-password-button" aria-label="Recuperar contraseña olvidada">
                    ¿Has olvidado tu contraseña?
                </button>
            </div>

            <div class="form-buttons-container">
                <button type="submit" class="login-submit-button" aria-label="Iniciar sesión en la aplicación">
                    Iniciar sesión
                </button>

                <button type="button" class="register-button" aria-label="Crear una cuenta nueva">
                    Regístrate
                </button>
            </div>
        </form>
    </main>

    <script>
        // Toggle para mostrar/ocultar contraseña
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        // Prevenir que el botón tome el foco
        togglePassword.addEventListener('mousedown', function(e) {
            e.preventDefault();
        });

        togglePassword.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Cambiar el icono entre ojo abierto y ojo cerrado
            if (type === 'text') {
                eyeIcon.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>';
            } else {
                eyeIcon.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
            }
            
            // Mantener el foco en el input
            passwordInput.focus();
        });

        // Validación del formulario de login
        document.getElementById('loginForm').addEventListener('submit', function (event) {
            let valido = true;

            // Limpiar errores previos
            document.querySelectorAll('.error').forEach(e => e.textContent = '');
            document.querySelectorAll('.form-field input').forEach(e => e.classList.remove('error-borde'));

            // Validar email
            const email = document.getElementById('email').value.trim();
            const emailRegexDominio = /^[^@\s]+\.[^@\s]+$/;

            if (email === '') {
                document.getElementById('error-email').textContent = 'Debe ingresar un correo electrónico.';
                document.getElementById('email').classList.add('error-borde');
                valido = false;
            } else if (!email.includes('@')) {
                document.getElementById('error-email').textContent = 'El correo debe contener un "@".';
                document.getElementById('email').classList.add('error-borde');
                valido = false;
            } else {
                const partes = email.split('@');
                if (partes.length !== 2 || partes[0].length === 0 || partes[1].length === 0) {
                    document.getElementById('error-email').textContent = 'El correo debe tener texto antes y después del "@".';
                    document.getElementById('email').classList.add('error-borde');
                    valido = false;
                } else if (!emailRegexDominio.test(partes[1])) {
                    document.getElementById('error-email').textContent = 'El dominio del correo no es válido, reingrese su e-mail.';
                    document.getElementById('email').classList.add('error-borde');
                    valido = false;
                } else if (!(partes[1].endsWith('.com') || partes[1].endsWith('.es'))) {
                    document.getElementById('error-email').textContent = 'El dominio debe terminar en .com o .es';
                    document.getElementById('email').classList.add('error-borde');
                    valido = false;
                }
            }

            // Validar contraseña
            const password = document.getElementById('password').value;
            if (password === '') {
                document.getElementById('error-password').textContent = 'Debe ingresar una contraseña.';
                document.getElementById('password').classList.add('error-borde');
                valido = false;
            } else if (password.length < 6) {
                document.getElementById('error-password').textContent = 'La contraseña debe tener al menos 6 caracteres.';
                document.getElementById('password').classList.add('error-borde');
                valido = false;
            }

            // Si no es válido, prevenir el envío
            if (!valido) {
                event.preventDefault();
            }
        });

        // Botón de registro
        document.querySelector('.register-button').addEventListener('click', function () {
            window.location.href = 'registro.php';
        });

        // Botón de contraseña olvidada
        document.querySelector('.forgot-password-button').addEventListener('click', function () {
            alert('Funcionalidad de recuperación de contraseña en desarrollo');
        });
    </script>

</body>

</html>
