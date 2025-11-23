<?php
/**
 * Perfil de Usuario
 * Proyecto: PreConsulta - Centro de Triaje Digital
 * Muestra los datos del paciente logueado
 */

require_once 'config/database.php';
require_once 'config/session_manager.php';
require_once 'config/helpers.php';
require_once 'classes/Database.php';

// Verificar autenticación
requireAuth();
checkSession();

$db = Database::getInstance();
$userId = getUserId();

// Obtener datos del usuario (ahora incluye fecha_nacimiento, direccion y condiciones_medicas)
$sqlUsuario = "SELECT nombre, apellidos, email, dni, telefono, fecha_nacimiento, direccion, condiciones_medicas 
               FROM Usuario WHERE id_usuario = :id_usuario";
$usuario = $db->selectOne($sqlUsuario, ['id_usuario' => $userId]);

// Obtener datos adicionales del paciente (datos médicos específicos)
$sqlPaciente = "SELECT grupo_sanguineo, alergias, seguro_medico 
                FROM Paciente WHERE id_paciente = :id_paciente";
$paciente = $db->selectOne($sqlPaciente, ['id_paciente' => $userId]);

// Extraer datos
$nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellidos'];
$nombreCorto = $usuario['nombre'] . ' ' . explode(' ', $usuario['apellidos'])[0];
$email = $usuario['email'];
$dni = $usuario['dni'] ?? null;
$telefono = $usuario['telefono'] ?? null;
$fechaNacimiento = $usuario['fecha_nacimiento'] ?? null;
$direccion = $usuario['direccion'] ?? null;
$condiciones = $usuario['condiciones_medicas'] ?? null;

// Calcular edad si hay fecha de nacimiento
$edad = null;
if ($fechaNacimiento) {
    $fechaNac = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNac)->y;
}

// Verificar si hay datos faltantes
$hasMissingData = empty($dni) || empty($direccion) || empty($condiciones) || empty($fechaNacimiento) || empty($telefono);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - PreConsulta</title>
    <link rel="icon" type="image/svg+xml" href="media/icons/cardiology_24dp_007AFF_FILL1_wght300_GRAD-25_opsz24.svg">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos para data-item con edad al lado del nombre */
        .data-item-with-age {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between;
            align-items: flex-start;
            gap: 2rem;
        }
        
        .data-name-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        
        .data-age-section {
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            text-align: right;
            min-width: 100px;
        }
        
        .data-age-section .data-label {
            text-align: right;
        }
        
        .data-age-section .data-value {
            text-align: right;
        }
        
        /* Opción B: Mantener horizontal en móviles */
        @media (max-width: 768px) {
            .data-item-with-age {
                gap: 1rem;
            }
            
            .data-name-section {
                flex: 1;
            }
            
            .data-age-section {
                min-width: 80px;
            }
        }
    </style>
</head>

<body class="full-height-body">
    <!-- Overlay oscuro -->
    <div class="form-overlay" id="formOverlay"></div>

    <!-- Botón flotante de alerta para datos faltantes -->
    <button class="floating-alert-button" id="floatingAlertBtn" 
            style="display: <?= $hasMissingData ? 'flex' : 'none' ?>;"
            aria-label="Completar datos personales faltantes" aria-expanded="false">
        <span class="alert-icon" aria-hidden="true">⚠️</span>
        <span class="alert-text">Completa tu perfil</span>
    </button>

    <!-- Formulario desplegable para datos faltantes -->
    <div class="missing-data-form" id="missingDataForm" role="dialog" aria-labelledby="formTitle" aria-hidden="true">
        <div class="form-header">
            <h2 id="formTitle">Completa tu información</h2>
            <button class="form-close-btn" id="formCloseBtn" aria-label="Cerrar formulario">✕</button>
        </div>
        <form id="userDataForm">
            <div class="form-group">
                <label for="dniInput">DNI:</label>
                <input type="text" id="dniInput" name="dni" 
                       placeholder="Ej: 12345678A" 
                       value="<?= sanitize($dni ?? '') ?>" maxlength="9" 
                       pattern="[0-9]{8}[A-Za-z]" 
                       title="Formato: 8 números seguidos de una letra">
            </div>
            
            <div class="form-group">
                <label for="phoneInput">Teléfono:</label>
                <input type="tel" id="phoneInput" name="phone" 
                       placeholder="Ej: 698 24 47 12" 
                       value="<?= sanitize($telefono ?? '') ?>" maxlength="15">
            </div>
            
            <div class="form-group">
                <label for="addressInput">Dirección completa:</label>
                <input type="text" id="addressInput" name="address" 
                       placeholder="Ej: Calle Principal 123, Madrid" 
                       value="<?= sanitize($direccion ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="limitationsInput">Limitaciones o condiciones médicas:</label>
                <textarea id="limitationsInput" name="limitations" rows="3" 
                          placeholder="Ej: Alergias, movilidad reducida, etc."><?= sanitize($condiciones ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="birthDateInput">Fecha de nacimiento:</label>
                <input type="date" id="birthDateInput" name="birthDate" 
                       value="<?= $fechaNacimiento ?>" required>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" id="cancelBtn">Cancelar</button>
                <button type="submit" class="btn-save">Guardar</button>
            </div>
        </form>
    </div>

    <main role="main" id="main-content">
        <section class="profile-section" aria-labelledby="profile-header">
            <!-- Header -->
            <div class="profile-header" id="profile-header">
                <div class="profile-header-top">
                    <h1 class="profile-welcome">
                        <span class="profile-greeting">Bienvenido,</span>
                        <span class="profile-username" id="userName"><?= sanitize($nombreCorto) ?></span>
                    </h1>
                </div>

                <!-- Navigation tabs -->
                <nav class="profile-nav" role="tablist" aria-label="Navegación de perfil">
                    <button class="profile-tab active" role="tab" aria-selected="true" aria-controls="datos-panel" id="datos-tab">
                        Datos
                    </button>
                    <button class="profile-tab" role="tab" aria-selected="false" aria-controls="historial-panel" id="historial-tab">
                        Historial Consultas
                    </button>
                </nav>
            </div>

            <!-- Data Panel -->
            <div class="profile-content-panel active" id="datos-panel" role="tabpanel" aria-labelledby="datos-tab">
                <div class="profile-data-container">
                    <!-- Nombre con Edad al lado -->
                    <div class="data-item data-item-with-age">
                        <div class="data-name-section">
                            <span class="data-label">Nombre:</span>
                            <span class="data-value" id="dataName"><?= sanitize($nombreCompleto) ?></span>
                        </div>
                        <div class="data-age-section">
                            <span class="data-label">Edad:</span>
                            <span class="data-value" id="dataEdad"><?= $edad !== null ? $edad . ' años' : 'No registrada' ?></span>
                        </div>
                    </div>
                    
                    <div class="data-item">
                        <span class="data-label">Correo electrónico:</span>
                        <span class="data-value" id="dataEmail"><?= sanitize($email) ?></span>
                    </div>
                    
                    <div class="data-item">
                        <span class="data-label">DNI:</span>
                        <span class="data-value" id="dataDNI"><?= $dni ? sanitize($dni) : 'No registrado' ?></span>
                    </div>
                    
                    <div class="data-item">
                        <span class="data-label">Teléfono:</span>
                        <span class="data-value" id="dataPhone"><?= $telefono ? sanitize($telefono) : 'No registrado' ?></span>
                    </div>
                    
                    <div class="data-item">
                        <span class="data-label">Dirección:</span>
                        <span class="data-value" id="dataAddress"><?= $direccion ? sanitize($direccion) : 'No hay dirección registrada' ?></span>
                    </div>
                    
                    <div class="data-item">
                        <span class="data-label">Limitaciones:</span>
                        <span class="data-value" id="dataLimitations"><?= $condiciones ? sanitize($condiciones) : 'No hay limitaciones registradas' ?></span>
                    </div>
                    
                    <div class="data-item">
                        <span class="data-label">Fecha de nacimiento:</span>
                        <span class="data-value" id="dataAge"><?= $fechaNacimiento ? formatDateLong($fechaNacimiento) : 'No hay fecha de nacimiento registrada' ?></span>
                    </div>
                </div>
            </div>

            <!-- Historial Panel -->
            <div class="profile-content-panel" id="historial-panel" role="tabpanel" aria-labelledby="historial-tab">
                <div id="historial-content">
                    <div class="historial-empty">
                        <p>Cargando historial...</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav" role="navigation" aria-label="Menú principal">
        <ul>
            <li>
                <a href="index.php" aria-label="Ir a la página de inicio" class="nav-button">
                    <img src="media/icons/home_24dp_000000_FILL0_wght400_GRAD0_opsz48.svg" alt="Icono de inicio" class="nav-icon">
                    <span class="nav-text">Inicio</span>
                </a>
            </li>
            <li>
                <a href="perfil.php" aria-label="Ir a tu perfil de usuario" class="nav-button active">
                    <img src="media/icons/person_heart_24dp_007AFF_FILL1_wght500_GRAD0_opsz24.svg" alt="Icono de perfil" class="nav-icon">
                    <span class="nav-text">Perfil</span>
                </a>
            </li>
            <li>
                <a href="tel:112" aria-label="Llamar a emergencias" class="nav-button call-button">
                    <img src="media/icons/phone_enable_emergency_red.svg" alt="Icono de teléfono" class="nav-icon">
                    <span class="nav-text">Llamada emergencia</span>
                </a>
            </li>
        </ul>
    </nav>

    <script>
        // Tab switching functionality
        const tabs = document.querySelectorAll('.profile-tab');
        const panels = document.querySelectorAll('.profile-content-panel');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and panels
                tabs.forEach(t => {
                    t.classList.remove('active');
                    t.setAttribute('aria-selected', 'false');
                });
                panels.forEach(p => p.classList.remove('active'));

                // Add active class to clicked tab
                tab.classList.add('active');
                tab.setAttribute('aria-selected', 'true');

                // Show corresponding panel
                const panelId = tab.getAttribute('aria-controls');
                const panel = document.getElementById(panelId);
                panel.classList.add('active');

                // Cargar historial si se selecciona esa pestaña
                if (panelId === 'historial-panel') {
                    loadHistorial();
                }
            });
        });

        // ========================================
        // FUNCIONALIDAD DEL FORMULARIO FLOTANTE
        // ========================================

        const floatingBtn = document.getElementById('floatingAlertBtn');
        const missingDataForm = document.getElementById('missingDataForm');
        const formOverlay = document.getElementById('formOverlay');
        const formCloseBtn = document.getElementById('formCloseBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const userDataForm = document.getElementById('userDataForm');

        // Abrir formulario
        function openForm() {
            missingDataForm.classList.add('active');
            formOverlay.classList.add('active');
            missingDataForm.setAttribute('aria-hidden', 'false');
            floatingBtn.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
            
            setTimeout(() => {
                document.getElementById('addressInput').focus();
            }, 100);
        }

        // Cerrar formulario
        function closeForm() {
            missingDataForm.classList.remove('active');
            formOverlay.classList.remove('active');
            missingDataForm.setAttribute('aria-hidden', 'true');
            floatingBtn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            floatingBtn.focus();
        }

        // Event listeners
        if (floatingBtn) {
            floatingBtn.addEventListener('click', openForm);
        }
        formCloseBtn.addEventListener('click', closeForm);
        cancelBtn.addEventListener('click', closeForm);
        formOverlay.addEventListener('click', closeForm);

        // Cerrar con tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && missingDataForm.classList.contains('active')) {
                closeForm();
            }
        });

        // Guardar datos del formulario
        userDataForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = {
                dni: document.getElementById('dniInput').value.trim(),
                phone: document.getElementById('phoneInput').value.trim(),
                address: document.getElementById('addressInput').value.trim(),
                limitations: document.getElementById('limitationsInput').value.trim(),
                birthDate: document.getElementById('birthDateInput').value
            };

            try {
                const response = await fetch('api/update_profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    // Actualizar los datos en la vista
                    if (formData.dni) {
                        document.getElementById('dataDNI').textContent = formData.dni;
                    }
                    
                    if (formData.phone) {
                        document.getElementById('dataPhone').textContent = formData.phone;
                    }
                    
                    if (formData.address) {
                        document.getElementById('dataAddress').textContent = formData.address;
                    }

                    if (formData.limitations) {
                        document.getElementById('dataLimitations').textContent = formData.limitations;
                    } else {
                        document.getElementById('dataLimitations').textContent = 'Sin limitaciones registradas';
                    }

                    if (formData.birthDate) {
                        const date = new Date(formData.birthDate);
                        const formattedDate = date.toLocaleDateString('es-ES', { 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        });
                        document.getElementById('dataAge').textContent = formattedDate;
                        
                        // Calcular y actualizar edad
                        const today = new Date();
                        let age = today.getFullYear() - date.getFullYear();
                        const monthDiff = today.getMonth() - date.getMonth();
                        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < date.getDate())) {
                            age--;
                        }
                        document.getElementById('dataEdad').textContent = age + ' años';
                    }

                    closeForm();
                    
                    // Ocultar botón flotante si ya no hay datos faltantes
                    const dni = document.getElementById('dataDNI').textContent;
                    const phone = document.getElementById('dataPhone').textContent;
                    const address = document.getElementById('dataAddress').textContent;
                    const limitations = document.getElementById('dataLimitations').textContent;
                    const age = document.getElementById('dataAge').textContent;
                    
                    const stillHasMissing = 
                        dni.includes('No registrado') ||
                        phone.includes('No registrado') ||
                        address.includes('No hay') || 
                        limitations.includes('No hay') || 
                        age.includes('No hay');
                    
                    if (!stillHasMissing && floatingBtn) {
                        floatingBtn.style.display = 'none';
                    }

                    showNotification('Datos guardados correctamente', 'success');
                } else {
                    showNotification('Error: ' + result.error, 'error', 6000);
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al guardar los datos. Inténtalo de nuevo.', 'error');
            }
        });

        // Cargar historial de consultas
        async function loadHistorial() {
            const historialContent = document.getElementById('historial-content');
            historialContent.innerHTML = '<div class="historial-empty"><p>Cargando historial...</p></div>';

            try {
                const response = await fetch('api/get_historial.php');
                const result = await response.json();

                if (result.success && result.data && result.data.length > 0) {
                    let html = '<div class="historial-list">';
                    
                    result.data.forEach(consulta => {
                        html += `
                            <div class="historial-item" onclick="window.location.href='detalle-consulta.php?id=${consulta.id_episodio}'">
                                <div class="historial-item-header">
                                    <span class="historial-date">${consulta.fecha}</span>
                                    <span class="historial-prioridad" style="background-color: ${consulta.color_prioridad};">
                                        ${consulta.prioridad}
                                    </span>
                                </div>
                                <div class="historial-item-body">
                                    <p class="historial-estado">${consulta.estado}</p>
                                    <p class="historial-sintomas">${consulta.sintomas_preview}</p>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    historialContent.innerHTML = html;
                } else {
                    historialContent.innerHTML = '<div class="historial-empty"><p>No se han encontrado todavía ninguna consulta finalizada.</p></div>';
                }
            } catch (error) {
                console.error('Error:', error);
                historialContent.innerHTML = '<div class="historial-empty"><p>Error al cargar el historial</p></div>';
            }
        }
    </script>
</body>

</html>
