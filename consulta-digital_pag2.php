<?php
/**
 * Consulta Digital - Página 2: Evidencia
 * Proyecto: PreConsulta - Centro de Triaje Digital
 */

require_once 'config/session_manager.php';
require_once 'config/helpers.php';

// Verificar autenticación
requireAuth();
checkSession();

$userName = getUserName();
$nombreCorto = explode(' ', $userName)[0] ?? 'Usuario';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evidencia de la Consulta - PreConsulta</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="full-height-body">

    <header class="consult-header" role="banner">
        <div class="header-content">
            <div>
                <h1>
                    <span class="line1">Evidencia</span>
                    <span class="line2">Consulta.</span>
                </h1>
                <p>Por favor <?= sanitize($nombreCorto) ?> muéstrenos una evidencia de su consulta.</p>
            </div>
            <a href="index.php" class="close-button" aria-label="Cerrar y volver al inicio">×</a>
        </div>
    </header>

    <main class="consult-main" role="main">
        <hr class="status-divider">
        
        <!-- Botones para elegir método de captura -->
        <div class="capture-options" id="captureOptions">
            <button type="button" class="capture-button" id="uploadBtn" aria-label="Subir imagen desde galería">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                </svg>
                <span>Subir imagen</span>
            </button>
            
            <button type="button" class="capture-button" id="cameraBtn" aria-label="Tomar foto con cámara">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 17.5c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9z"/>
                </svg>
                <span>Tomar foto</span>
            </button>
        </div>
        
        <!-- Input oculto para subir archivos -->
        <input type="file" accept="image/*" class="sr-only" id="fileInput">
        
        <!-- Contenedor para la cámara -->
        <div class="camera-container" id="cameraContainer" style="display: none;">
            <video id="cameraStream" autoplay playsinline></video>
            <canvas id="photoCanvas" style="display: none;"></canvas>
            <div class="camera-controls">
                <button type="button" class="camera-control-btn" id="takePictureBtn" aria-label="Tomar foto">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="white">
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                </button>
                <button type="button" class="camera-control-btn cancel-btn" id="cancelCameraBtn" aria-label="Cancelar">Cancelar</button>
            </div>
        </div>
        
        <!-- Contenedor para la vista previa -->
        <div class="preview-container" id="previewContainer" style="display: none;">
            <img id="imagePreview" alt="Vista previa de la imagen">
            <div class="preview-controls">
                <button type="button" class="preview-btn delete-btn" id="deleteImageBtn" aria-label="Eliminar imagen">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                    </svg>
                    Eliminar
                </button>
            </div>
        </div>
        
        <button class="confirm-button" id="confirmBtn" disabled style="opacity: 0.5; cursor: not-allowed;">
            Confirmar evidencia
        </button>
    </main>

    <footer class="consult-footer" role="contentinfo">
        <a href="consulta-digital_pag1.php" class="back-button" aria-label="Volver al paso anterior">
            <img src="media/icons/arrow_back_24dp_000000_FILL1_wght300_GRAD-25_opsz24.svg" alt="Flecha volver" class="back-arrow-icon">
        </a>
        <button type="button" id="nextButton" class="next-button" disabled style="opacity: 0.5; cursor: not-allowed; pointer-events: none;">
            Siguiente
        </button>
        <div class="step-counter" aria-label="Paso 2 de 3">
            <span class="counter-number">2/3</span>
            <span class="counter-text">pasos</span>
        </div>
    </footer>

    <script>
        let capturedImage = null;
        let cameraStream = null;

        const captureOptions = document.getElementById('captureOptions');
        const uploadBtn = document.getElementById('uploadBtn');
        const cameraBtn = document.getElementById('cameraBtn');
        const fileInput = document.getElementById('fileInput');
        const cameraContainer = document.getElementById('cameraContainer');
        const cameraStreamElement = document.getElementById('cameraStream');
        const photoCanvas = document.getElementById('photoCanvas');
        const takePictureBtn = document.getElementById('takePictureBtn');
        const cancelCameraBtn = document.getElementById('cancelCameraBtn');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const deleteImageBtn = document.getElementById('deleteImageBtn');
        const confirmBtn = document.getElementById('confirmBtn');
        const nextButton = document.getElementById('nextButton');

        // Botón subir imagen
        uploadBtn.addEventListener('click', () => {
            fileInput.click();
        });

        // Manejo de archivo seleccionado
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    capturedImage = event.target.result;
                    showPreview(capturedImage);
                };
                reader.readAsDataURL(file);
            }
        });

        // Botón tomar foto con cámara
        cameraBtn.addEventListener('click', async () => {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                cameraStreamElement.srcObject = cameraStream;
                captureOptions.style.display = 'none';
                cameraContainer.style.display = 'flex';
            } catch (error) {
                alert('❌ No se pudo acceder a la cámara. Por favor, asegúrate de que tienes una cámara conectada y has dado permisos.');
                console.error('Error accediendo a la cámara:', error);
            }
        });

        // Tomar foto
        takePictureBtn.addEventListener('click', () => {
            const context = photoCanvas.getContext('2d');
            photoCanvas.width = cameraStreamElement.videoWidth;
            photoCanvas.height = cameraStreamElement.videoHeight;
            context.drawImage(cameraStreamElement, 0, 0);
            
            capturedImage = photoCanvas.toDataURL('image/jpeg');
            stopCamera();
            showPreview(capturedImage);
        });

        // Cancelar cámara
        cancelCameraBtn.addEventListener('click', () => {
            stopCamera();
            captureOptions.style.display = 'flex';
        });

        // Mostrar vista previa
        function showPreview(imageData) {
            imagePreview.src = imageData;
            captureOptions.style.display = 'none';
            cameraContainer.style.display = 'none';
            previewContainer.style.display = 'flex';
            confirmBtn.disabled = false;
            confirmBtn.style.opacity = '1';
            confirmBtn.style.cursor = 'pointer';
        }

        // Eliminar imagen
        deleteImageBtn.addEventListener('click', () => {
            capturedImage = null;
            imagePreview.src = '';
            previewContainer.style.display = 'none';
            captureOptions.style.display = 'flex';
            confirmBtn.disabled = true;
            confirmBtn.style.opacity = '0.5';
            confirmBtn.style.cursor = 'not-allowed';
            fileInput.value = '';
        });

        // Confirmar evidencia
        confirmBtn.addEventListener('click', () => {
            if (capturedImage) {
                // Guardar imagen en sessionStorage
                sessionStorage.setItem('evidencia_consulta', capturedImage);
                
                // Habilitar botón siguiente
                nextButton.disabled = false;
                nextButton.style.opacity = '1';
                nextButton.style.cursor = 'pointer';
                nextButton.style.pointerEvents = 'auto';
                
                // Cambiar a enlace
                nextButton.onclick = () => {
                    window.location.href = 'consulta-digital_pag3.php';
                };
                
                alert('✅ Evidencia confirmada. Puede continuar al siguiente paso.');
            }
        });

        // Detener cámara
        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
                cameraStreamElement.srcObject = null;
            }
            cameraContainer.style.display = 'none';
        }

        // Limpiar al salir de la página
        window.addEventListener('beforeunload', () => {
            stopCamera();
        });
    </script>

    <style>
        .capture-options {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .capture-button {
            background-color: #007aff;
            color: white;
            border: none;
            border-radius: 15px;
            padding: 20px 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            transition: background-color 0.3s, transform 0.2s;
            min-width: 150px;
        }

        .capture-button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .capture-button:active {
            transform: translateY(0);
        }

        .camera-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .camera-container video {
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            background-color: #000;
        }

        .camera-controls {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .camera-control-btn {
            background-color: #007aff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s, transform 0.2s;
        }

        .camera-control-btn.cancel-btn {
            border-radius: 10px;
            width: auto;
            padding: 15px 25px;
            background-color: #e60000;
        }

        .camera-control-btn:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }

        .camera-control-btn.cancel-btn:hover {
            background-color: #cc0000;
        }

        .preview-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .preview-container img {
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .preview-controls {
            display: flex;
            gap: 15px;
        }

        .preview-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .preview-btn.delete-btn {
            background-color: #e60000;
            color: white;
        }

        .preview-btn.delete-btn:hover {
            background-color: #cc0000;
            transform: translateY(-2px);
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        @media (max-width: 600px) {
            .capture-options {
                flex-direction: column;
                width: 100%;
            }

            .capture-button {
                width: 100%;
            }
        }
    </style>

</body>

</html>
