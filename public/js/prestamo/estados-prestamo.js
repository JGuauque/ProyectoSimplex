// estados-prestamo.js
// Lógica de validación de transiciones de estado para préstamos

class ValidadorEstadosPrestamo {
    constructor() {
        // Definir transiciones permitidas
        this.transicionesPermitidas = {
            'Prestado': ['Devuelto', 'Pago'],  // De Prestado puede ir a Devuelto o Pago
            'Devuelto': [],                    // De Devuelto NO puede cambiar a ningún otro estado
            'Pago': []                         // De Pago NO puede cambiar a ningún otro estado
        };
        
        // Mensajes de error
        this.mensajesError = {
            'Devuelto': 'Un préstamo devuelto no puede cambiar de estado.',
            'Pago': 'Un préstamo pagado no puede cambiar de estado.',
            'invalid': 'Transición de estado no permitida.'
        };
    }
    
    /**
     * Validar si una transición de estado es permitida
     * @param {string} estadoActual - Estado actual del préstamo
     * @param {string} nuevoEstado - Nuevo estado deseado
     * @returns {object} {valido: boolean, mensaje: string}
     */
    validarTransicion(estadoActual, nuevoEstado) {
        // Estados válidos
        const estadosValidos = ['Prestado', 'Devuelto', 'Pago'];
        
        // Validar que los estados sean válidos
        if (!estadosValidos.includes(estadoActual) || !estadosValidos.includes(nuevoEstado)) {
            return {
                valido: false,
                mensaje: `Estado inválido. Estados permitidos: ${estadosValidos.join(', ')}`
            };
        }
        
        // Si el estado actual ya es final (Devuelto o Pago)
        if (estadoActual === 'Devuelto' || estadoActual === 'Pago') {
            return {
                valido: false,
                mensaje: this.mensajesError[estadoActual]
            };
        }
        
        // Verificar si la transición está permitida
        const transiciones = this.transicionesPermitidas[estadoActual];
        
        if (transiciones && transiciones.includes(nuevoEstado)) {
            return {
                valido: true,
                mensaje: 'Transición permitida'
            };
        } else {
            return {
                valido: false,
                mensaje: this.mensajesError.invalid
            };
        }
    }
    
    /**
     * Obtener estados disponibles basados en el estado actual
     * @param {string} estadoActual - Estado actual del préstamo
     * @returns {Array} Lista de estados disponibles
     */
    obtenerEstadosDisponibles(estadoActual) {
        if (!estadoActual) return ['Prestado', 'Devuelto', 'Pago'];
        
        if (estadoActual === 'Prestado') {
            return ['Devuelto', 'Pago'];
        }
        
        // Si ya es Devuelto o Pago, no hay estados disponibles
        return [];
    }
    
    /**
     * Actualizar select de estados en el modal basado en estado actual
     * @param {string} estadoActual - Estado actual del préstamo
     * @param {HTMLElement} selectElement - Elemento select HTML
     */
    actualizarSelectEstados(estadoActual, selectElement) {
        if (!selectElement) return;
        
        // Limpiar opciones actuales
        selectElement.innerHTML = '<option value="">Seleccione un estado</option>';
        
        // Obtener estados disponibles
        const estadosDisponibles = this.obtenerEstadosDisponibles(estadoActual);
        
        if (estadosDisponibles.length === 0) {
            // Si no hay estados disponibles, deshabilitar el select
            selectElement.innerHTML = '<option value="">No hay cambios disponibles</option>';
            selectElement.disabled = true;
            return;
        }
        
        // Habilitar select
        selectElement.disabled = false;
        
        // Agregar opciones
        estadosDisponibles.forEach(estado => {
            const option = document.createElement('option');
            option.value = estado;
            option.textContent = estado;
            selectElement.appendChild(option);
        });
    }
    
    /**
     * Verificar si se puede eliminar un préstamo basado en su estado
     * @param {string} estadoActual - Estado actual del préstamo
     * @returns {boolean} true si se puede eliminar
     */
    sePuedeEliminar(estadoActual) {
        // Solo se pueden eliminar préstamos en estado "Prestado"
        return estadoActual === 'Prestado';
    }
    
    /**
     * Obtener clase CSS para el badge según el estado
     * @param {string} estado - Estado del préstamo
     * @returns {string} Clase CSS
     */
    obtenerClaseEstado(estado) {
        const clases = {
            'Prestado': 'badge-warning',
            'Devuelto': 'badge-success',
            'Pago': 'badge-info'
        };
        
        return clases[estado] || 'badge-secondary';
    }
}

// Crear instancia global
const ValidadorEstados = new ValidadorEstadosPrestamo();

// Hacer disponible globalmente
window.ValidadorEstados = ValidadorEstados;