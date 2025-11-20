<?php
/**
 * Sistema de Registro
 * Proyecto: PreConsulta - Centro de Triaje Digital
 * Registra nuevos usuarios en la base de datos
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
$success = '';

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validaciones
    if (empty($nombre) || empty($apellidos) || empty($dni) || empty($email) || empty($password)) {
        $error = 'Por favor, complete todos los campos.';
    } else if (strlen($nombre) < 2) {
        $error = 'El nombre debe tener al menos 2 caracteres.';
    } else if (strlen($apellidos) < 2) {
        $error = 'Los apellidos deben tener al menos 2 caracteres.';
    } else if (!preg_match('/^[0-9]{8}[A-Za-z]$/', $dni)) {
        $error = 'El DNI debe tener 8 dígitos seguidos de una letra.';
    } else if (!validateEmail($email)) {
        $error = 'El correo electrónico no es válido.';
    } else if (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        try {
            $db = Database::getInstance();
            
            // Verificar si el email ya existe
            $sqlCheckEmail = "SELECT id_usuario FROM Usuario WHERE email = :email";
            $existingUser = $db->selectOne($sqlCheckEmail, ['email' => $email]);
            
            if ($existingUser) {
                $error = 'Este correo electrónico ya está registrado.';
            } else {
                try {
                    // Comenzar transacción
                    $db->beginTransaction();
                    
                    // Hashear la contraseña
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insertar en tabla Usuario usando SQL directo
                    $sqlUsuario = "INSERT INTO Usuario (nombre, apellidos, email, dni, password, estado) 
                                  VALUES (:nombre, :apellidos, :email, :dni, :password, 'activo')";
                    
                    $db->query($sqlUsuario, [
                        'nombre' => $nombre,
                        'apellidos' => $apellidos,
                        'email' => $email,
                        'dni' => $dni,
                        'password' => $passwordHash
                    ]);
                    
                    // Obtener el ID del usuario recién creado
                    $userId = $db->getConnection()->lastInsertId();
                    
                    if (!$userId) {
                        throw new Exception('No se pudo obtener el ID del usuario creado');
                    }
                    
                    // Insertar en tabla Paciente usando SQL directo
                    $sqlPaciente = "INSERT INTO Paciente (id_paciente) VALUES (:id_paciente)";
                    
                    $db->query($sqlPaciente, [
                        'id_paciente' => $userId
                    ]);
                    
                    // Confirmar transacción
                    $db->commit();
                    
                    $success = 'Cuenta creada exitosamente. Redirigiendo al inicio de sesión...';
                    
                    // Redirigir después de 2 segundos
                    header('Refresh: 2; URL=login.php');
                    
                } catch (Exception $e) {
                    // Revertir transacción si falla
                    $db->rollback();
                    
                    if (APP_DEBUG) {
                        $error = 'Error al crear la cuenta: ' . $e->getMessage();
                    } else {
                        $error = 'Error al crear la cuenta. Por favor, inténtalo de nuevo.';
                    }
                    
                    error_log('Error en registro: ' . $e->getMessage());
                }
            }
            
        } catch (Exception $e) {
            if (APP_DEBUG) {
                $error = 'Error de base de datos: ' . $e->getMessage();
            } else {
                $error = 'Error al procesar el registro. Inténtalo de nuevo.';
            }
            error_log('Error en registro (catch externo): ' . $e->getMessage());
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - PreConsulta</title>
    <link rel="icon" type="image/svg+xml" href="media/icons/cardiology_24dp_007AFF_FILL1_wght300_GRAD-25_opsz24.svg">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="login-body">

    <header class="login-header">
        <h1 class="register-title">
            <span class="register-main-text">Registro</span>
            <span class="register-subtitle">Centro de Triaje Digital</span>
        </h1>
        <div class="header-icon-login">
            <img src="media/icons/cardiology_24dp_007AFF_FILL1_wght300_GRAD-25_opsz24.svg" alt="Logo PreConsulta" class="app-logo">
        </div>
    </header>

    <main class="login-main">
        <hr class="register-divider">
        <form id="registerForm" class="login-form" method="POST" action="registro.php" novalidate>
            <div class="form-fields-container">
                <h2 class="form-title">Datos personales</h2>

                <?php if ($error): ?>
                <div class="error-message" style="background-color: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 16px; text-align: center; font-size: 14px;">
                    <?= sanitize($error) ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="success-message" style="background-color: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 8px; margin-bottom: 16px; text-align: center; font-size: 14px;">
                    ✅ <?= sanitize($success) ?>
                </div>
                <?php endif; ?>

                <div class="form-field">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Juan" 
                           value="<?= sanitize($_POST['nombre'] ?? '') ?>"
                           aria-required="true" aria-describedby="error-nombre" 
                           autocomplete="given-name" aria-invalid="false">
                    <div class="error" id="error-nombre" aria-live="polite"></div>
                </div>

                <div class="form-field">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" placeholder="Torres Mena" 
                           value="<?= sanitize($_POST['apellidos'] ?? '') ?>"
                           aria-required="true" aria-describedby="error-apellidos" 
                           autocomplete="family-name" aria-invalid="false">
                    <div class="error" id="error-apellidos" aria-live="polite"></div>
                </div>

                <div class="form-field">
                    <label for="dni">DNI</label>
                    <input type="text" id="dni" name="dni" placeholder="12345678A" 
                           value="<?= sanitize($_POST['dni'] ?? '') ?>"
                           aria-required="true" aria-describedby="error-dni" 
                           autocomplete="off" aria-invalid="false" maxlength="9">
                    <div class="error" id="error-dni" aria-live="polite"></div>
                </div>

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
            </div>

            <div class="form-buttons-container">
                <button type="submit" class="register-submit-button" aria-label="Crear cuenta nueva">
                    Crear cuenta
                </button>
                
                <button type="button" class="already-account-button" aria-label="Ya tengo cuenta, ir a inicio de sesión">
                    ¿Ya tienes cuenta?
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

        // Solo permitir números en el input de teléfono
        document.getElementById('telefono').addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Validación del formulario de registro
        document.getElementById('registerForm').addEventListener('submit', function (event) {
            let valido = true;

            // Limpiar errores previos
            document.querySelectorAll('.error').forEach(e => e.textContent = '');
            document.querySelectorAll('.form-field input').forEach(e => e.classList.remove('error-borde'));

            // Validar nombre
            const nombre = document.getElementById('nombre').value.trim();
            if (nombre.length < 2) {
                document.getElementById('error-nombre').textContent = 'El nombre debe tener al menos 2 caracteres, reingrese su nombre.';
                document.getElementById('nombre').classList.add('error-borde');
                valido = false;
            }

            // Validar apellidos
            const apellidos = document.getElementById('apellidos').value.trim();
            if (apellidos.length < 2) {
                document.getElementById('error-apellidos').textContent = 'Los apellidos deben tener al menos 2 caracteres, reingrese sus apellidos.';
                document.getElementById('apellidos').classList.add('error-borde');
                valido = false;
            }

            // Validar teléfono
            const telefono = document.getElementById('telefono').value.trim();
            if (telefono.length !== 9) {
                document.getElementById('error-telefono').textContent = 'El número de teléfono debe tener 9 dígitos.';
                document.getElementById('telefono').classList.add('error-borde');
                valido = false;
            }

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

        // Botón de "¿Ya tienes cuenta?"
        document.querySelector('.already-account-button').addEventListener('click', function() {
            window.location.href = 'login.php';
        });
    </script>

</body>

</html>
