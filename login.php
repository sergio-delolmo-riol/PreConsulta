<?php
/**
 * Sistema de Login
 * Proyecto: PreConsulta - Centro de Triaje Digital
 * Valida usuarios contra la base de datos
 */

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
            $user = $db->selectOne(
                'Usuario',
                ['id_usuario', 'nombre', 'apellidos', 'email', 'password', 'estado'],
                ['email' => $email]
            );
            
            if ($user) {
                // Verificar si la cuenta está activa
                if ($user['estado'] !== 'activo') {
                    $error = 'Tu cuenta está inactiva o bloqueada. Contacta con soporte.';
                } 
                // Verificar la contraseña
                else if (password_verify($password, $user['password'])) {
                    // Determinar el tipo de usuario
                    $userType = 'paciente'; // Por defecto
                    
                    // Verificar si es enfermero
                    $enfermero = $db->selectOne('Enfermero', ['id_enfermero'], ['id_enfermero' => $user['id_usuario']]);
                    if ($enfermero) {
                        $userType = 'enfermero';
                    } else {
                        // Verificar si es celador
                        $celador = $db->selectOne('Celador', ['id_celador'], ['id_celador' => $user['id_usuario']]);
                        if ($celador) {
                            $userType = 'celador';
                        }
                    }
                    
                    // Actualizar último acceso
                    $db->update(
                        'Usuario',
                        ['ultimo_acceso' => date('Y-m-d H:i:s')],
                        ['id_usuario' => $user['id_usuario']]
                    );
                    
                    // Iniciar sesión
                    $nombreCompleto = $user['nombre'] . ' ' . $user['apellidos'];
                    loginUser($user['id_usuario'], $user['email'], $nombreCompleto, $userType);
                    
                    // Redirigir según el tipo de usuario
                    if ($userType === 'paciente') {
                        redirect('index.php');
                    } else {
                        // Para enfermeros y celadores, redirigir a una página específica (puedes cambiar esto)
                        redirect('index.php');
                    }
                } else {
                    $error = 'Correo o contraseña incorrectos.';
                }
            } else {
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
                    <input type="password" id="password" name="password" aria-required="true"
                        aria-describedby="error-password" autocomplete="current-password" aria-invalid="false">
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
