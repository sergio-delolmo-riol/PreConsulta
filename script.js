// DOM Elements
const startTriageBtn = document.getElementById('startTriageBtn');
const navLinks = document.querySelectorAll('.nav-link');

// Event Listeners
if (startTriageBtn) {
    startTriageBtn.addEventListener('click', handleStartTriage);
}

// Add click handlers for navigation
navLinks.forEach(link => {
    link.addEventListener('click', handleNavClick);
});

// Handle start triage button click
function handleStartTriage() {
    // Announce to screen readers
    announceToScreenReader('Iniciando evaluación de triaje digital');
    
    // In a real application, this would navigate to the triage form page
    console.log('Iniciar evaluación de triaje');
    
    // For now, show an alert (will be replaced with actual navigation later)
    alert('La funcionalidad de evaluación será implementada en la siguiente fase. Esta es la pantalla de inicio (Home).');
}

// Handle navigation clicks
function handleNavClick(event) {
    const href = event.currentTarget.getAttribute('href');
    
    // Don't prevent default for emergency call (tel: link)
    if (href && href.startsWith('tel:')) {
        announceToScreenReader('Realizando llamada al servicio de emergencias 112');
        return;
    }
    
    // Handle other navigation
    event.preventDefault();
    
    // Remove active class from all nav links
    navLinks.forEach(link => {
        link.classList.remove('active');
        link.removeAttribute('aria-current');
    });
    
    // Add active class to clicked link
    event.currentTarget.classList.add('active');
    event.currentTarget.setAttribute('aria-current', 'page');
    
    // Extract page name from href
    const pageName = href.replace('#', '');
    announceToScreenReader(`Navegando a ${pageName}`);
    
    // Handle different navigation targets
    switch(pageName) {
        case 'home':
            console.log('Navegando a: Inicio');
            break;
        case 'profile':
            console.log('Navegando a: Perfil');
            alert('La página de Perfil será implementada en la siguiente fase.');
            break;
        default:
            console.log('Navegación:', pageName);
    }
}

// Helper function to announce messages to screen readers
function announceToScreenReader(message) {
    const announcement = document.createElement('div');
    announcement.setAttribute('role', 'status');
    announcement.setAttribute('aria-live', 'polite');
    announcement.className = 'visually-hidden';
    announcement.textContent = message;
    
    document.body.appendChild(announcement);
    
    // Remove after announcement is made
    setTimeout(() => {
        document.body.removeChild(announcement);
    }, 1000);
}

// Keyboard navigation support
document.addEventListener('keydown', (event) => {
    // Handle escape key to clear focus
    if (event.key === 'Escape') {
        document.activeElement.blur();
    }
});

// Initialize
console.log('PreConsulta - Sistema de Triaje Digital inicializado');
console.log('Accesibilidad: WCAG 2.0 AA compatible');
