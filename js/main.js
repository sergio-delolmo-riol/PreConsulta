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
});
