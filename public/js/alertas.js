/**
 * Sistema de Alertas Globales
 * Para usar en todas las páginas del sistema POS
 */

class AlertManager {
    constructor() {
        this.container = document.getElementById('alertContainer');
        if (!this.container) {
            console.warn('Contenedor de alertas no encontrado. Creando uno...');
            this.createContainer();
        }
        this.init();
    }

    createContainer() {
        this.container = document.createElement('div');
        this.container.id = 'alertContainer';
        this.container.className = 'alert-container';
        document.body.appendChild(this.container);
    }

    init() {
        // Mostrar alertas de sesión PHP automáticamente
        this.showSessionAlerts();

        // Hacer disponible globalmente
        window.showAlert = this.show.bind(this);
        window.removeAlert = this.remove.bind(this);
    }

    showSessionAlerts() {
        // Las alertas de PHP se manejarán desde las vistas
        // Esta función se puede extender si se necesita
        // Método 1: Usar variables globales definidas en Blade
        if (window.sessionAlerts) {
            if (window.sessionAlerts.success) {
                this.success(window.sessionAlerts.success);
            }
            if (window.sessionAlerts.error) {
                this.error(window.sessionAlerts.error);
            }
            if (window.sessionAlerts.validationErrors && window.sessionAlerts.validationErrors.length > 0) {
                let errorHtml = '<ul>';
                window.sessionAlerts.validationErrors.forEach(error => {
                    errorHtml += `<li>${this.escapeHtml(error)}</li>`;
                });
                errorHtml += '</ul>';
                this.error(errorHtml);
            }
        }
        // Método 2: Buscar elementos de alerta en el DOM (alternativa)
        // Puedes agregar elementos ocultos en Blade y leerlos aquí
        const hiddenAlerts = document.querySelectorAll('[data-alert-type]');
        hiddenAlerts.forEach(alert => {
            const type = alert.getAttribute('data-alert-type');
            const message = alert.textContent || alert.innerHTML;
            if (type && message) {
                this.show(message, type);
            }
        });
    }

    /**
     * Mostrar una alerta
     * @param {string} message - Mensaje o HTML a mostrar
     * @param {string} type - Tipo: 'success', 'danger', 'warning', 'info'
     * @param {number} timeout - Tiempo en ms para auto-cerrar (0 = no auto-cerrar)
     * @returns {string} ID de la alerta creada
     */
    show(message, type = 'success', timeout = 5000) {
        const alertId = 'alert-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

        const alertDiv = document.createElement('div');
        alertDiv.id = alertId;
        alertDiv.className = `alert alert-${type}`;

        alertDiv.innerHTML = `
            <div class="alert-content">
                ${this.escapeHtml(message)}
            </div>
            <button class="alert-close-btn" onclick="removeAlert('${alertId}')" aria-label="Cerrar">
                &times;
            </button>
        `;

        // Insertar al inicio (las más nuevas arriba)
        this.container.insertBefore(alertDiv, this.container.firstChild);

        // Auto-cerrar después del timeout
        if (timeout > 0) {
            setTimeout(() => {
                this.fadeOut(alertId);
            }, timeout);
        }

        // Auto-eliminar después de la animación (por si acaso)
        setTimeout(() => {
            if (document.getElementById(alertId)) {
                this.remove(alertId);
            }
        }, timeout + 60000); // 1 minuto como máximo

        return alertId;
    }

    /**
     * Desvanecer una alerta
     */
    fadeOut(alertId) {
        const alert = document.getElementById(alertId);
        if (alert && !alert.classList.contains('fade-out')) {
            alert.classList.add('fade-out');

            // Eliminar después de la animación
            setTimeout(() => {
                this.remove(alertId);
            }, 400); // Coincide con la duración CSS
        }
    }

    /**
     * Eliminar una alerta inmediatamente
     */
    remove(alertId) {
        const alert = document.getElementById(alertId);
        if (alert && alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }

    /**
     * Escapar HTML para seguridad
     */
    escapeHtml(text) {
        if (typeof text !== 'string') return text;

        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        // Si el mensaje parece contener HTML intencional (como <ul><li>), no escapar
        if (text.includes('<ul>') || text.includes('<li>') || text.includes('<br>') || text.includes('<p>')) {
            return text;
        }

        return text.replace(/[&<>"']/g, function (m) { return map[m]; });
    }

    /**
     * Métodos rápidos (para usar más fácilmente)
     */
    success(message, timeout = 5000) {
        return this.show(message, 'success', timeout);
    }

    error(message, timeout = 5000) {
        return this.show(message, 'danger', timeout);
    }

    warning(message, timeout = 5000) {
        return this.show(message, 'warning', timeout);
    }

    info(message, timeout = 5000) {
        return this.show(message, 'info', timeout);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    window.alertManager = new AlertManager();

    // Hacer métodos disponibles globalmente
    window.showAlert = window.alertManager.show.bind(window.alertManager);
    window.showSuccess = window.alertManager.success.bind(window.alertManager);
    window.showError = window.alertManager.error.bind(window.alertManager);
    window.showWarning = window.alertManager.warning.bind(window.alertManager);
    window.showInfo = window.alertManager.info.bind(window.alertManager);

    // Para compatibilidad con código existente
    window.fadeOutAlert = function (alertId) {
        window.alertManager.fadeOut(alertId);
    };
    // Eliminar alertas automáticamente después de 10 segundos por seguridad
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-container .alert');
        alerts.forEach(alert => {
            if (alert.id && !alert.classList.contains('fade-out')) {
                window.alertManager.fadeOut(alert.id);
            }
        });
    }, 10000);
});