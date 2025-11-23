/**
 * Sistema de Notificaciones Accesibles
 * Compatible con WCAG 2.1 AA
 * Reemplaza alert() y confirm() por notificaciones accesibles
 */

class AccessibleNotification {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Crear contenedor de notificaciones si no existe
        if (!document.getElementById('accessible-notifications-container')) {
            this.container = document.createElement('div');
            this.container.id = 'accessible-notifications-container';
            this.container.className = 'notifications-container';
            this.container.setAttribute('aria-live', 'polite');
            this.container.setAttribute('aria-atomic', 'true');
            this.container.setAttribute('role', 'status');
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('accessible-notifications-container');
        }
    }

    /**
     * Muestra una notificación accesible
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo: 'success', 'error', 'warning', 'info'
     * @param {number} duration - Duración en ms (0 = permanente hasta cerrar)
     */
    show(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.setAttribute('role', 'alert');
        notification.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');

        // Icono según tipo
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon" aria-hidden="true">${icons[type]}</span>
                <span class="notification-message">${this.escapeHtml(message)}</span>
            </div>
            <button class="notification-close" aria-label="Cerrar notificación">
                <span aria-hidden="true">×</span>
            </button>
        `;

        // Agregar al contenedor
        this.container.appendChild(notification);

        // Animar entrada
        setTimeout(() => notification.classList.add('show'), 10);

        // Botón cerrar
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => this.close(notification));

        // Auto-cerrar si tiene duración
        if (duration > 0) {
            setTimeout(() => this.close(notification), duration);
        }

        // Focus en el botón cerrar para accesibilidad
        closeBtn.focus();

        return notification;
    }

    /**
     * Muestra un diálogo de confirmación accesible
     * @param {string} message - Mensaje de confirmación
     * @param {Function} onConfirm - Callback al confirmar
     * @param {Function} onCancel - Callback al cancelar
     */
    confirm(message, onConfirm, onCancel = null) {
        const dialog = document.createElement('div');
        dialog.className = 'notification-dialog';
        dialog.setAttribute('role', 'dialog');
        dialog.setAttribute('aria-modal', 'true');
        dialog.setAttribute('aria-labelledby', 'dialog-title');
        dialog.setAttribute('aria-describedby', 'dialog-message');

        const dialogId = 'dialog-' + Date.now();

        dialog.innerHTML = `
            <div class="notification-dialog-overlay"></div>
            <div class="notification-dialog-content">
                <h2 id="dialog-title" class="dialog-title">Confirmación</h2>
                <p id="dialog-message" class="dialog-message">${this.escapeHtml(message)}</p>
                <div class="dialog-buttons">
                    <button class="btn-dialog btn-cancel" data-action="cancel">
                        Cancelar
                    </button>
                    <button class="btn-dialog btn-confirm" data-action="confirm" autofocus>
                        Confirmar
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(dialog);
        setTimeout(() => dialog.classList.add('show'), 10);

        // Trap focus dentro del diálogo
        const focusableElements = dialog.querySelectorAll('button');
        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        dialog.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeDialog(dialog);
                if (onCancel) onCancel();
            }

            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstFocusable) {
                        e.preventDefault();
                        lastFocusable.focus();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        e.preventDefault();
                        firstFocusable.focus();
                    }
                }
            }
        });

        // Eventos de botones
        const btnConfirm = dialog.querySelector('[data-action="confirm"]');
        const btnCancel = dialog.querySelector('[data-action="cancel"]');

        btnConfirm.addEventListener('click', () => {
            this.closeDialog(dialog);
            if (onConfirm) onConfirm();
        });

        btnCancel.addEventListener('click', () => {
            this.closeDialog(dialog);
            if (onCancel) onCancel();
        });

        // Focus en confirmar
        btnConfirm.focus();

        return dialog;
    }

    close(notification) {
        notification.classList.remove('show');
        notification.classList.add('hide');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    closeDialog(dialog) {
        dialog.classList.remove('show');
        setTimeout(() => {
            if (dialog.parentNode) {
                dialog.parentNode.removeChild(dialog);
            }
        }, 300);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Instancia global
const notifications = new AccessibleNotification();

// Funciones helper globales para reemplazar alert() y confirm()
function showNotification(message, type = 'info', duration = 5000) {
    return notifications.show(message, type, duration);
}

function showConfirm(message, onConfirm, onCancel) {
    return notifications.confirm(message, onConfirm, onCancel);
}

// Alias para compatibilidad
window.accessibleAlert = showNotification;
window.accessibleConfirm = showConfirm;
