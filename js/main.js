document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-button');
    const panels = document.querySelectorAll('.tab-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Desactivar todas las pestañas y paneles
            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            panels.forEach(p => {
                p.classList.remove('active');
            });

            // Activar la pestaña y el panel seleccionados
            tab.classList.add('active');
            const panelId = tab.getAttribute('aria-controls');
            const selectedPanel = document.getElementById(panelId);
            selectedPanel.classList.add('active');
            tab.setAttribute('aria-selected', 'true');
        });
    });

    // Lógica del contador de caracteres
    const symptomText = document.getElementById('symptom-text');
    const charCounter = document.querySelector('.char-counter');

    if (symptomText && charCounter) {
        symptomText.addEventListener('input', () => {
            const count = symptomText.value.length;
            const max = symptomText.maxLength;
            charCounter.textContent = `${count}/${max}`;
        });
    }

    // Guardar síntomas en sessionStorage cuando se hace clic en "Siguiente"
    const nextButton = document.querySelector('.next-button');
    if (nextButton && (symptomText || document.getElementById('transcribed-text'))) {
        nextButton.addEventListener('click', function(e) {
            let sintomas = '';
            
            // Obtener síntomas del panel activo
            const activePanel = document.querySelector('.tab-panel.active');
            if (activePanel) {
                if (activePanel.id === 'panel-texto') {
                    sintomas = symptomText.value.trim();
                } else if (activePanel.id === 'panel-audio') {
                    sintomas = document.getElementById('transcribed-text').value.trim();
                }
            }

            if (sintomas) {
                sessionStorage.setItem('consultaSintomas', sintomas);
            } else {
                // Si no hay síntomas, prevenir navegación y mostrar alerta
                e.preventDefault();
                showNotification('Por favor, describa sus síntomas antes de continuar.', 'warning', 5000);
            }
        });
    }

    // Cargar síntomas guardados si volvemos a la página
    if (symptomText) {
        const sintomasGuardados = sessionStorage.getItem('consultaSintomas');
        if (sintomasGuardados) {
            symptomText.value = sintomasGuardados;
            if (charCounter) {
                charCounter.textContent = `${sintomasGuardados.length}/${symptomText.maxLength}`;
            }
        }
    }
});
