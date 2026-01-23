// ===================== VARIABLES GLOBALES =====================
let localActualId = null;

// ===================== FUNCIONES GLOBALES =====================

// Función para editar local
window.editarLocal = async function (id) {
    try {
        console.log('Abriendo modal para editar local ID:', id);
        localActualId = id;

        // Cargar datos del local
        const response = await fetch(`/locales-aliados/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error('Error al cargar el local');
        }

        const local = await response.json();
        console.log('Datos del local cargados:', local);

        // Llenar campos del formulario de edición
        document.getElementById('edit_localIdHidden').value = local.id;
        document.getElementById('edit_localNombre').value = local.nombre || '';
        document.getElementById('edit_localId').value = local.identificacion || '';
        document.getElementById('edit_localContacto').value = local.contacto || '';
        document.getElementById('edit_localDireccion').value = local.direccion || '';

        // Mostrar modal de edición
        const modalEditarLocal = document.getElementById('modalEditarLocal');
        if (modalEditarLocal) {
            modalEditarLocal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Enfocar primer campo
            setTimeout(() => {
                const nombreInput = document.getElementById('edit_localNombre');
                if (nombreInput) nombreInput.focus();
            }, 100);
        }

    } catch (error) {
        console.error('Error al cargar local:', error);
        alert('Error al cargar los datos del local: ' + error.message);
    }
};

// Función para actualizar local
async function actualizarLocal(e) {
    e.preventDefault();
    console.log('Actualizando local ID:', localActualId);

    const editLocalNombre = document.getElementById('edit_localNombre');
    const editLocalId = document.getElementById('edit_localId');
    const editLocalContacto = document.getElementById('edit_localContacto');
    const editLocalDireccion = document.getElementById('edit_localDireccion');

    // Validación básica
    if (!editLocalNombre || !editLocalNombre.value.trim()) {
        alert('El nombre del local es obligatorio');
        editLocalNombre.focus();
        return;
    }

    // Preparar datos
    const datosLocal = {
        nombre: editLocalNombre.value.trim(),
        identificacion: editLocalId.value.trim() || null,
        contacto: editLocalContacto.value.trim() || null,
        direccion: editLocalDireccion.value.trim() || null
    };

    console.log('Datos a enviar:', datosLocal);

    try {
        const response = await fetch(`/locales-aliados/${localActualId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(datosLocal)
        });

        const resultado = await response.json();
        console.log('Respuesta del servidor:', resultado);

        if (response.ok) {
            alert('Local actualizado correctamente');
            cerrarModalEditarLocal();

            // ========== IMPORTANTE: RECARGAR LISTAS ==========

            // 1. Recargar la lista de locales en el panel de locales
            if (typeof window.cargarListaLocales === 'function') {
                console.log('Recargando lista de locales...');
                await window.cargarListaLocales();
            } else {
                console.warn('Función cargarListaLocales no encontrada');
                // Intentar recargar manualmente
                await recargarListaLocales();
            }

            // 2. Recargar el select de locales en el panel principal
            if (typeof window.cargarLocales === 'function') {
                console.log('Recargando select de locales...');
                await window.cargarLocales();
            }

        } else {
            throw new Error(resultado.message || 'Error al actualizar el local');
        }

    } catch (error) {
        console.error('Error al actualizar local:', error);

        let mensajeError = 'Error al actualizar el local';
        if (error.message.includes('identificacion')) {
            mensajeError = 'La identificación ya está registrada para otro local';
        }

        alert(mensajeError + ': ' + error.message);
    }
}

// Función auxiliar para recargar lista de locales
async function recargarListaLocales() {
    try {
        const response = await fetch('/locales-aliados');
        const locales = await response.json();

        const listaLocales = document.getElementById('listaLocales');
        if (!listaLocales) {
            console.error('Elemento listaLocales no encontrado');
            return;
        }

        // Función para escapar HTML (si no está disponible globalmente)
        const escapeHtml = (text) => {
            if (!text) return '';
            return text
                .toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        };

        listaLocales.innerHTML = locales.map(local => `
            <div class="card-local">
                <h4>${escapeHtml(local.nombre)}</h4>
                <p><strong>ID:</strong> ${escapeHtml(local.identificacion || 'N/A')}</p>
                <p><strong>Contacto:</strong> ${escapeHtml(local.contacto || 'N/A')}</p>
                <p><strong>Dirección:</strong> ${escapeHtml(local.direccion || 'N/A')}</p>
                <div class="card-actions">
                    <button onclick="editarLocal(${local.id})" class="btn btn-editar">Editar</button>
                    <button onclick="eliminarLocal(${local.id})" class="btn btn-rojo">Eliminar</button>
                </div>
            </div>
        `).join('');
        
        console.log('Lista de locales recargada manualmente');
        
    } catch (error) {
        console.error('Error recargando lista de locales:', error);
        alert('Error al recargar la lista de locales');
    }
}

// Función para cerrar modal de edición
function cerrarModalEditarLocal() {
    const modalEditarLocal = document.getElementById('modalEditarLocal');
    if (modalEditarLocal) {
        modalEditarLocal.classList.add('hidden');
        document.body.style.overflow = 'auto';

        const formEditarLocal = document.getElementById('formEditarLocal');
        if (formEditarLocal) {
            formEditarLocal.reset();
        }

        localActualId = null;
    }
}

// ===================== INICIALIZACIÓN =====================
document.addEventListener('DOMContentLoaded', function () {
    console.log('Inicializando funcionalidad de edición de locales...');

    // Obtener referencias a los elementos del modal de edición
    const modalEditarLocal = document.getElementById('modalEditarLocal');
    const formEditarLocal = document.getElementById('formEditarLocal');
    const btnCancelEditarLocal = document.getElementById('cancelEditarLocal');

    // Configurar event listeners para el modal de edición
    if (formEditarLocal) {
        formEditarLocal.addEventListener('submit', actualizarLocal);
        console.log('Event listener añadido al formulario de edición');
    }

    if (btnCancelEditarLocal) {
        btnCancelEditarLocal.addEventListener('click', cerrarModalEditarLocal);
        console.log('Event listener añadido al botón Cancelar de edición');
    }

    // Cerrar modal de edición al hacer clic fuera
    if (modalEditarLocal) {
        modalEditarLocal.addEventListener('click', function (e) {
            if (e.target === modalEditarLocal) {
                cerrarModalEditarLocal();
            }
        });
    }

    // Cerrar modal de edición con tecla ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (modalEditarLocal && !modalEditarLocal.classList.contains('hidden')) {
                cerrarModalEditarLocal();
            }
        }
    });

    console.log('Funcionalidad de edición de locales inicializada');
});