@extends('layouts.plantilla')

@section('titulo', 'Gestión de Préstamos')

@section('contenido')

<div class="prestamos-header">
    <strong>
        <h2 style="font-size: 28px;"><i class="fa-solid fa-handshake"></i> Gestión de Préstamos</h2>
    </strong>

    <div class="header-actions">
        <button id="btnExportPdf" class="btn btn-rojo"><i class="fa-solid fa-file-pdf"></i> Exportar PDF</button>
        <button id="btnLocales" class="btn btn-azul"><i class="fa-solid fa-store"></i> Locales Aliados</button>
    </div>
</div>

<!-- PANEL PRÉSTAMOS -->
<section id="panelPrestamos" class="panel">
    <div class="form-grid">
        <div class="form-row">
            <label>Local aliado</label>
            <select id="selectLocal" class="input"></select>
        </div>

        <div class="form-row">
            <label>Producto (inventario)</label>
            <select id="selectProducto" class="input"></select>
        </div>

        <div class="form-row">
            <label>Cantidad</label>
            <input id="cantidad" class="input" type="number" min="1" value="1">
        </div>

        <div class="form-row">
            <label>Precio (editable)</label>
            <input id="precioPrestamo" class="input" type="text" placeholder="Precio por unidad">
            <small id="costoRef" class="muted">Costo: -</small>
        </div>

        <div class="actions-row">
            <button id="btnAgregar" class="btn btn-azul"><i class="fa-solid fa-cart-plus"></i> Agregar</button>
            <button id="btnConfirmar" class="btn btn-azul"><i class="fa-solid fa-check"></i> Confirmar</button>
            <button id="btnLimpiar" class="btn btn-rojo"><i class="fa-solid fa-broom"></i> Limpiar</button>
        </div>
    </div>

    <div id="carritoWrap" class="panel-sub">
        <strong>
            <h4 style="font-size: 17px;">Productos a prestar</h4>
        </strong>

        <ul id="listaCarrito" class="lista-carrito">
            <li class="muted">No hay productos agregados</li>
        </ul>
    </div>

    <div class="filtros-prestamos">
        <input id="buscarPrestamo" type="text" class="input" placeholder="Buscar por producto o local">
        <select id="filtroEstado" class="input">
            <option value="">Todos los estados</option>
            <option value="Prestado">Prestado</option>
            <option value="Devuelto">Devuelto</option>
            <option value="Pago">Pago</option>
        </select>
    </div>

    <div id="listaPrestamos" class="prestamos-list"></div>
</section>

<!-- PANEL LOCALES -->
<section id="panelLocales" class="panel hidden">
    <div class="panel-header">
        <h2><i class="fa-solid fa-store"></i> Locales Aliados</h2>
        <div class="header-actions">
            <button id="btnNuevoLocal" class="btn btn-azul"><i class="fa-solid fa-plus"></i> Nuevo Local</button>
            <button id="btnCerrarLocales" class="btn btn-rojo"><i class="fa-solid fa-xmark"></i> Cerrar</button>
        </div>
    </div>
    <div id="listaLocales" class="cards-list"></div>
</section>
</main>

<!-- Modal Nuevo Local -->
<!-- <div id="modalLocal" class="modal hidden">
    <div class="modal-card">
        <button id="closeModalLocal" class="modal-close">&times;</button>
        <h3 id="modalLocalTitle">Nuevo Local</h3>
        <form id="formLocal">
            <input id="localNombre" type="text" placeholder="Nombre del local" required>
            <input id="localId" type="text" placeholder="ID / NIT">
            <input id="localContacto" type="text" placeholder="Contacto">
            <input id="localDireccion" type="text" placeholder="Dirección">
            <div class="modal-actions">
                <button type="submit" class="btn btn-azul">Guardar</button>
                <button type="button" id="cancelLocal" class="btn btn-rojo">Cancelar</button>
            </div>
        </form>
    </div>
</div> -->

<!-- Modal Nuevo Local -->
<div id="modalLocal" class="modal-prestamo hidden">
    <div class="modal-card-prestamo">
        <div class="modal-header-prestamo">
            <h3 id="modalLocalTitle" class="modal-title-prestamo">Nuevo Local</h3>
            <!-- <button id="closeModalLocal" class="modal-close">&times;</button> -->
        </div>

        <form id="formLocal" class="modal-form-prestamo">
            <div class="form-row-prestamo">

                <div class="form-group-prestamo">
                    <label for="localNombre">Nombre del local <span class="required">*</span></label>
                    <input id="localNombre" type="text" placeholder="Ingrese el nombre del local" required>
                    
                </div>

                <div class="form-group-prestamo">
                    <label for="localId">ID / NIT</label>
                    <input id="localId" type="text" placeholder="Ej: 123456789-0">
                    
                </div>

                <div class="form-group-prestamo">
                    <label for="localContacto">Contacto</label>
                    <input id="localContacto" type="text" placeholder="Teléfono o email">
                </div>

                <div class="form-group-prestamo">
                    <label for="localDireccion">Dirección</label>
                    <input id="localDireccion" type="text" placeholder="Dirección">
                </div>
            </div>

            <div class="modal-actions-prestamo">
                <button type="submit" class="btn btn-azul">Guardar</button>
                <button type="button"
                        id="closeModalLocal"
                        class="btn btn-rojo">
                        Cancelar    
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Estado -->
<div id="modalEstado" class="modal hidden">
    <div class="modal-card">
        <button id="closeModalEstado" class="modal-close">&times;</button>
        <h3>Cambiar estado del préstamo</h3>
        <p id="estadoDetalle" class="muted"></p>
        <select id="selectNuevoEstado" class="input"></select>
        <div class="modal-actions">
            <button id="confirmEstado" class="btn btn-azul">Confirmar</button>
            <button id="cancelEstado" class="btn btn-rojo">Cancelar</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ========== FUNCIONES UTILITARIAS ==========
        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe
                .toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-CO', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        // ========== FIN FUNCIONES UTILITARIAS ==========

        // Variables globales
        let carrito = [];
        let locales = [];
        let productos = [];
        let prestamosData = [];

        // Elementos del DOM
        const selectLocal = document.getElementById('selectLocal');
        const selectProducto = document.getElementById('selectProducto');
        const cantidadInput = document.getElementById('cantidad');
        const precioPrestamoInput = document.getElementById('precioPrestamo');
        const costoRef = document.getElementById('costoRef');
        const listaCarrito = document.getElementById('listaCarrito');
        const btnAgregar = document.getElementById('btnAgregar');
        const btnConfirmar = document.getElementById('btnConfirmar');
        const btnLimpiar = document.getElementById('btnLimpiar');
        const btnLocales = document.getElementById('btnLocales');
        const btnCerrarLocales = document.getElementById('btnCerrarLocales');
        const panelPrestamos = document.getElementById('panelPrestamos');
        const panelLocales = document.getElementById('panelLocales');
        const listaLocales = document.getElementById('listaLocales');
        const modalLocal = document.getElementById('modalLocal');
        const formLocal = document.getElementById('formLocal');
        const buscarPrestamo = document.getElementById('buscarPrestamo');
        const filtroEstado = document.getElementById('filtroEstado');
        const listaPrestamos = document.getElementById('listaPrestamos');
        const modalEstado = document.getElementById('modalEstado');
        const closeModalEstado = document.getElementById('closeModalEstado');
        const cancelEstado = document.getElementById('cancelEstado');
        const confirmEstado = document.getElementById('confirmEstado');

        // Inicializar
        cargarLocales();
        cargarProductos();
        renderPrestamos();

        // Event Listeners
        btnAgregar.addEventListener('click', agregarAlCarrito);
        btnConfirmar.addEventListener('click', confirmarPrestamo);
        btnLimpiar.addEventListener('click', limpiarCarrito);
        btnLocales.addEventListener('click', mostrarPanelLocales);
        btnCerrarLocales.addEventListener('click', mostrarPanelPrestamos);
        selectProducto.addEventListener('change', actualizarPrecioReferencia);
        formLocal.addEventListener('submit', guardarLocal);

        // Filtros
        if (buscarPrestamo) buscarPrestamo.addEventListener('input', renderPrestamos);
        if (filtroEstado) filtroEstado.addEventListener('change', renderPrestamos);

        // Modal Estado
        if (closeModalEstado) closeModalEstado.addEventListener('click', cerrarModalEstado);
        if (cancelEstado) cancelEstado.addEventListener('click', cerrarModalEstado);
        if (confirmEstado) confirmEstado.addEventListener('click', confirmarCambioEstado);

        if (modalEstado) {
            modalEstado.addEventListener('click', function(e) {
                if (e.target === modalEstado) cerrarModalEstado();
            });
        }

        // ========== FUNCIONES PRINCIPALES ==========

        async function cargarLocales() {
            try {
                const response = await fetch('/locales-aliados');
                locales = await response.json();
                actualizarSelectLocales();
            } catch (error) {
                console.error('Error cargando locales:', error);
            }
        }

        function actualizarSelectLocales() {
            selectLocal.innerHTML = '<option value="">— Seleccione un local —</option>';
            locales.forEach(local => {
                const option = document.createElement('option');
                option.value = local.id;
                option.textContent = `${local.nombre} ${local.identificacion ? `(ID: ${local.identificacion})` : ''}`;
                selectLocal.appendChild(option);
            });
        }

        async function cargarProductos() {
            try {
                const response = await fetch('/api/productos-con-stock');
                productos = await response.json();
                actualizarSelectProductos();
            } catch (error) {
                console.error('Error cargando productos:', error);
                actualizarSelectProductos();
            }
        }

        function actualizarSelectProductos() {
            selectProducto.innerHTML = '<option value="">— Seleccione un producto —</option>';

            productos.forEach(producto => {
                const option = document.createElement('option');
                option.value = producto.id;
                option.textContent = `${producto.nombre} - Stock: ${producto.stock}`;
                option.setAttribute('data-precio', producto.precio);
                option.setAttribute('data-costo', producto.costo);
                selectProducto.appendChild(option);
            });
        }

        function actualizarPrecioReferencia() {
            const selectedOption = selectProducto.options[selectProducto.selectedIndex];
            if (selectedOption.value) {
                const costo = selectedOption.getAttribute('data-costo');
                costoRef.textContent = `Costo: $${costo}`;
                precioPrestamoInput.value = selectedOption.getAttribute('data-precio');
            } else {
                costoRef.textContent = 'Costo: -';
                precioPrestamoInput.value = '';
            }
        }

        function agregarAlCarrito() {
            const localId = selectLocal.value;
            const productoId = selectProducto.value;
            const cantidad = parseInt(cantidadInput.value);
            const precio = parseFloat(precioPrestamoInput.value);

            if (!localId || !productoId || !cantidad || !precio) {
                alert('Por favor complete todos los campos');
                return;
            }

            const producto = selectProducto.options[selectProducto.selectedIndex];
            const local = selectLocal.options[selectLocal.selectedIndex];
            const productoNombre = producto.textContent.split(' - ')[0];
            const subtotal = cantidad * precio;

            const item = {
                localId,
                localNombre: local.textContent,
                productoId,
                productoNombre,
                cantidad,
                precio,
                subtotal,
                timestamp: Date.now() // ID temporal único
            };

            carrito.push(item);
            actualizarCarrito();
            limpiarCamposProducto();
        }

        function actualizarCarrito() {
            if (carrito.length === 0) {
                listaCarrito.innerHTML = '<li class="muted">No hay productos agregados</li>';
                return;
            }

            listaCarrito.innerHTML = carrito.map((item, index) => `
            <li>
                <strong>${escapeHtml(item.productoNombre)}</strong> x ${item.cantidad}
                <br>
                <small>Local: ${escapeHtml(item.localNombre)}</small>
                <br>
                <small>Precio: ${formatCurrency(item.precio)} | Subtotal: ${formatCurrency(item.subtotal)}</small>
                <button onclick="eliminarDelCarrito(${index})" class="btn-eliminar-item">✕</button>
            </li>
        `).join('');
        }

        function limpiarCamposProducto() {
            selectProducto.value = '';
            cantidadInput.value = '1';
            precioPrestamoInput.value = '';
            costoRef.textContent = 'Costo: -';
        }

        function limpiarCarrito() {
            carrito = [];
            actualizarCarrito();
            selectLocal.value = '';
        }

        async function confirmarPrestamo() {
            if (carrito.length === 0) {
                alert('No hay productos en el carrito');
                return;
            }

            if (!selectLocal.value) {
                alert('Seleccione un local');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Preparar datos para enviar
            const productosData = carrito.map(item => ({
                id: parseInt(item.productoId),
                cantidad: parseInt(item.cantidad),
                precio: parseFloat(item.precio)
            }));

            console.log('Enviando datos:', {
                local_id: parseInt(selectLocal.value),
                productos: productosData
            });

            try {
                const response = await fetch('/prestamos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        local_id: parseInt(selectLocal.value),
                        productos: productosData
                    })
                });

                const contentType = response.headers.get("content-type");

                if (contentType && contentType.includes("application/json")) {
                    const result = await response.json();

                    if (result.success) {
                        alert(result.message);
                        limpiarCarrito();
                        // Recargar la lista de préstamos
                        await cargarPrestamosData();
                        renderPrestamos();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } else {
                    const text = await response.text();
                    console.error('Respuesta no JSON:', text);
                    alert('Error del servidor. Revisa la consola para más detalles.');
                }

            } catch (error) {
                console.error('Error de red:', error);
                alert('Error de conexión: ' + error.message);
            }
        }

        // ========== FUNCIONES PARA RENDERIZAR PRÉSTAMOS ==========

        async function cargarPrestamosData() {
            try {
                const response = await fetch('/api/prestamos');
                prestamosData = await response.json();
                window.prestamosData = prestamosData; // Para acceso global
                return prestamosData;
            } catch (error) {
                console.error('Error cargando préstamos:', error);
                return [];
            }
        }

        async function renderPrestamos() {
            if (!listaPrestamos) return;

            const q = (buscarPrestamo?.value || "").toLowerCase().trim();
            const estadoFilter = filtroEstado?.value || "";

            listaPrestamos.innerHTML = '<p class="muted">Cargando préstamos...</p>';

            try {
                // Cargar datos si no están cargados
                if (prestamosData.length === 0) {
                    await cargarPrestamosData();
                }

                let any = false;
                listaPrestamos.innerHTML = ''; // Limpiar contenido

                prestamosData.forEach((prestamo, idx) => {
                    const producto = prestamo.producto || {};
                    const local = prestamo.local || {};

                    const productoNombre = producto.nombre || 'Producto no encontrado';
                    const localNombre = local.nombre || 'Local no encontrado';

                    // Aplicar filtros
                    const matchesQ = !q ||
                        productoNombre.toLowerCase().includes(q) ||
                        localNombre.toLowerCase().includes(q);

                    const matchesEstado = !estadoFilter || prestamo.estado === estadoFilter;

                    if (!matchesQ || !matchesEstado) return;

                    any = true;

                    const badgeClass = (prestamo.estado || "").toLowerCase();
                    const subtotal = parseFloat(prestamo.subtotal) || 0;
                    const precioUnitario = parseFloat(prestamo.precio_unitario) || 0;

                    const div = document.createElement("div");
                    div.className = "card-prestamo";
                    div.innerHTML = `
                    <div class="card-prestamo-info">
                        <h4><span style="font-weight:800;color:#333;">${escapeHtml(productoNombre)} x${prestamo.cantidad || 1}</span> <span style="font-weight:700;color:#333;">(${formatCurrency(precioUnitario)})</span></h4>
                        <p><strong>Local:</strong> ${escapeHtml(localNombre)}</p>
                        <p><strong>Fecha:</strong> ${formatDate(prestamo.created_at)}</p>
                        <p><strong>Subtotal:</strong> ${formatCurrency(subtotal)}</p>
                        <p class="muted"><strong>Costo referencia:</strong> ${formatCurrency(producto.costo || 0)}</p>
                    </div>
                    <div class="card-prestamo-actions">
                        <span class="badge ${badgeClass}">${escapeHtml(prestamo.estado)}</span>
                        <button class="btn btn-azul btn-sm" onclick="cambiarEstado(${prestamo.id})">
                            Cambiar Estado
                        </button>
                        <button class="btn btn-rojo btn-sm" onclick="eliminarPrestamo(${prestamo.id})">
                            Eliminar
                        </button>
                    </div>
                `;
                    listaPrestamos.appendChild(div);
                });

                if (!any) {
                    listaPrestamos.innerHTML = `
                    <p class="muted" style="text-align: center; padding: 20px;">
                        No hay préstamos coincidentes.
                    </p>`;
                }
            } catch (error) {
                console.error('Error cargando préstamos:', error);
                listaPrestamos.innerHTML = '<p class="muted" style="color: red;">Error cargando préstamos</p>';
            }
        }

        // ========== FUNCIONES PARA CAMBIAR ESTADO ==========

        window.cambiarEstado = async function(prestamoId) {
            console.log('Buscando préstamo ID:', prestamoId);

            // Asegurar que los datos estén cargados
            if (prestamosData.length === 0) {
                await cargarPrestamosData();
            }

            const prestamo = prestamosData.find(p => p.id == prestamoId);

            if (!prestamo) {
                alert('Préstamo no encontrado');
                return;
            }


            // Obtener detalles
            const producto = prestamo.producto || {};
            const local = prestamo.local || {};


            // Llenar el select de estados
            const selectNuevoEstado = document.getElementById('selectNuevoEstado');
            ValidadorEstados.actualizarSelectEstados(prestamo.estado, selectNuevoEstado);

            // Si no hay estados disponibles, mostrar mensaje y salir
            if (selectNuevoEstado.disabled) {
                alert('Este préstamo ya no puede cambiar de estado.');
                return;
            }

            // Llenar el modal con la información
            document.getElementById('estadoDetalle').innerHTML = `
            <strong>${escapeHtml(producto.nombre || 'Producto no encontrado')}</strong> — 
            ${escapeHtml(local.nombre || 'Local no encontrado')} —<br>
            Cantidad: ${prestamo.cantidad || 1} — 
            Estado actual: <span class="${ValidadorEstados.obtenerClaseEstado(prestamo.estado)}">${prestamo.estado}</span><br>
            ${formatDate(prestamo.created_at)}`;



            selectNuevoEstado.innerHTML = `
            <option value="">Seleccione un estado</option>
            <option value="Prestado" ${prestamo.estado === 'Prestado' ? 'selected' : ''}>Prestado</option>
            <option value="Devuelto" ${prestamo.estado === 'Devuelto' ? 'selected' : ''}>Devuelto</option>
            <option value="Pago" ${prestamo.estado === 'Pago' ? 'selected' : ''}>Pago</option>`;

            // Mostrar el modal y guardar el ID
            modalEstado.classList.remove('hidden');
            modalEstado.setAttribute('data-prestamo-id', prestamoId);


        }

        function cerrarModalEstado() {
            modalEstado.classList.add('hidden');
        }

        async function confirmarCambioEstado() {
            const prestamoId = modalEstado.getAttribute('data-prestamo-id');
            const selectNuevoEstado = document.getElementById('selectNuevoEstado');
            const nuevoEstado = selectNuevoEstado.value;

            if (!nuevoEstado) {
                alert('Por favor seleccione un estado');
                return;
            }

            // Obtener datos del préstamo
            const prestamo = prestamosData.find(p => p.id == prestamoId);

            if (!prestamo) {
                alert('Préstamo no encontrado');
                return;
            }

            // Validar la transición usando el validador
            const validacion = ValidadorEstados.validarTransicion(prestamo.estado, nuevoEstado);

            if (!validacion.valido) {
                alert(validacion.mensaje);
                return;
            }

            // if (!['Prestado', 'Devuelto', 'Pago'].includes(nuevoEstado)) {
            //     alert('Estado inválido');
            //     return;
            // }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`/prestamos/${prestamoId}/estado`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        estado: nuevoEstado
                    })
                });

                if (response.ok) {
                    alert('Estado actualizado exitosamente');
                    cerrarModalEstado();
                    // Actualizar datos y re-renderizar
                    await cargarPrestamosData();
                    renderPrestamos();
                } else {
                    const error = await response.json();
                    alert('Error: ' + error.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cambiar el estado');
            }
        }

        // ========== FUNCIONES PARA ELIMINAR PRÉSTAMO ==========

        window.eliminarPrestamo = async function(prestamoId) {

            // Obtener datos del préstamo
            const prestamo = prestamosData.find(p => p.id == prestamoId);

            if (!prestamo) {
                alert('Préstamo no encontrado');
                return;
            }

            // Validar si se puede eliminar
            if (!ValidadorEstados.sePuedeEliminar(prestamo.estado)) {
                alert(`No se puede eliminar un préstamo en estado "${prestamo.estado}". Solo se pueden eliminar préstamos en estado "Prestado".`);
                return;
            }

            if (!confirm('¿Está seguro de eliminar este préstamo (ID: ${prestamoId})?')) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`/prestamos/${prestamoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    alert('Préstamo eliminado exitosamente');
                    // Actualizar datos y re-renderizar
                    await cargarPrestamosData();
                    renderPrestamos();
                } else {
                    const error = await response.text();
                    alert('Error al eliminar el préstamo');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al eliminar el préstamo');
            }
        }

        // ========== FUNCIONES PARA LOCALES ALIADOS ==========

        function mostrarPanelLocales() {
            panelPrestamos.classList.add('hidden');
            panelLocales.classList.remove('hidden');
            cargarListaLocales();
        }

        function mostrarPanelPrestamos() {
            panelLocales.classList.add('hidden');
            panelPrestamos.classList.remove('hidden');
        }

        async function cargarListaLocales() {
            try {
                const response = await fetch('/locales-aliados');
                const locales = await response.json();

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
            } catch (error) {
                console.error('Error cargando locales:', error);
            }
        }

        async function guardarLocal(e) {
            e.preventDefault();

            const data = {
                nombre: document.getElementById('localNombre').value,
                identificacion: document.getElementById('localId').value,
                contacto: document.getElementById('localContacto').value,
                direccion: document.getElementById('localDireccion').value
            };

            try {
                const response = await fetch('/locales-aliados', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    modalLocal.classList.add('hidden');
                    formLocal.reset();
                    cargarLocales();
                    cargarListaLocales();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar el local');
            }
        }

        // ========== FUNCIONES GLOBALES ==========

        window.eliminarDelCarrito = function(index) {
            carrito.splice(index, 1);
            actualizarCarrito();
        };

        window.editarLocal = function(localId) {
            // Implementar edición de local
            alert('Editar local ' + localId + ' - Función pendiente');
        };

        window.eliminarLocal = async function(localId) {
            if (!confirm('¿Está seguro de eliminar este local?')) return;

            try {
                const response = await fetch(`/locales-aliados/${localId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    cargarLocales();
                    cargarListaLocales();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al eliminar el local');
            }
        };

        // ========== MODAL LOCAL ==========

        document.getElementById('btnNuevoLocal').addEventListener('click', () => {
            document.getElementById('modalLocalTitle').textContent = 'Nuevo Local';
            formLocal.reset();
            modalLocal.classList.remove('hidden');
        });

        document.getElementById('closeModalLocal').addEventListener('click', () => {
            modalLocal.classList.add('hidden');
        });

        document.getElementById('cancelLocal').addEventListener('click', () => {
            modalLocal.classList.add('hidden');
        });
    });
</script>

<script src="{{ asset('js\prestamo\estados-prestamo.js') }}"></script>
<script src="{{ asset('js\prestamo\reporte-prestamo.js') }}"></script>


@endsection