@extends('layouts.plantilla')

@section('titulo', 'Gestión de Clientes')

@section('contenido')

<style>
    /* Modales */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);

    }

    .modal-content {
        background: white;
        margin: 5% auto;
        padding: 25px;
        border-radius: 10px;
        width: 90%;
        max-width: 800px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        border-radius: 10px;
        padding: 10px;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #eee;
        padding-bottom: 15px;
        background-color: #4299e1;
        /* background-color: #c9e9f9ff; */
    }

    .modal-header h2 {
        color: #f8f9fa;
        margin: 0;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: black;
    }

    .close-modal:hover {
        color: #f25858ff;
    }

    .tabla-ventas {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .tabla-ventas th {
        background: #3498db;
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: bold;
    }

    .tabla-ventas td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    .tabla-ventas tbody tr:hover {
        background: #f8f9fa;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    /* Estilos para el buscador de ventas */
    .buscador-ventas {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .filtros-grid {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .filtro-item label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2d3748;
        font-size: 14px;
    }

    .filtro-item input {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .filtro-item input:focus {
        outline: none;
        border-color: #4299e1;
        background: white;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }

    .filtro-item input::placeholder {
        color: #a0aec0;
        font-size: 13px;
    }

    /* Estilo para botones del buscador */
    .btn-buscar,
    .btn-resetear {
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-buscar {
        background: #48bb78;
        color: white;
    }

    .btn-buscar:hover {
        background: #38a169;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(72, 187, 120, 0.2);
    }

    .btn-resetear {
        background: #e53e3e;
        color: white;
    }

    .btn-resetear:hover {
        background: #c53030;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(229, 62, 62, 0.2);
    }

    /* Contador de resultados */
    #contadorResultados {
        background: linear-gradient(135deg, #a0d9ff 0%, #c9e9f9 100%);
        border: 2px solid #4299e1;
        color: #2d3748;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Paginación */
    #paginacionVentas {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 2px solid #e2e8f0;
    }

    #btnAnterior:disabled,
    #btnSiguiente:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    #infoPagina {
        background: #4299e1;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        min-width: 120px;
        display: inline-block;
    }
</style>

<div class="clientes-main">

    <section class="formulario-clientes">
        <strong>
            <h2 style="font-size: 24px;">Registrar Cliente</h2>
        </strong>

        <form method="POST" action="{{ route('clientes.store') }}" class="form-grid">
            @csrf
            <input type="text" id="nombreCliente" name="nombre" placeholder="Nombre completo *" required value="{{ old('nombre') }}">
            <input type="text" id="idCliente" name="identificacion" placeholder="Identificación *" required value="{{ old('identificacion') }}">
            <input type="email" id="correoCliente" name="email" placeholder="Correo electrónico" value="{{ old('email') }}">
            <input type="text" id="telefonoCliente" name="telefono" placeholder="Teléfono" value="{{ old('telefono') }}">
            <button type="submit" class="btn btn-azul">Guardar</button>
        </form>
    </section>

    <!-- Listado -->
    <section class="listado-clientes">
        <strong>
            <h2 style="font-size: 24px;">Clientes Registrados</h2>
        </strong>

        <div class="tabla-container">
            <table class="tabla-clientes">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>ID</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="listaClientes">
                    @foreach($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->identificacion }}</td>
                        <td>{{ $cliente->email ?? 'N/A' }}</td>
                        <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                        <td>
                            <div class="acciones-botones">
                                <button class="btn btn-editar" onclick="editarCliente({{ $cliente->id }})">
                                    <i class="fa-solid fa-edit"></i> Editar
                                </button>
                                <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-rojo" onclick="return confirm('¿Estás seguro de eliminar este cliente? \nℹ️ Si elimas a este cliente se perderán todas la ventas asociadas con el cliente.')">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                </form>
                                <button class="btn btn-info" onclick="verVentas({{ $cliente->id }}, '{{ $cliente->nombre }}')">
                                    <i class="fa-solid fa-list"></i> Ventas
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- PAGINACIÓN LARAVEL -->
            @if($clientes->hasPages())
            <div class="paginacion-container" style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <!-- Información de la paginación -->
                <div class="paginacion-info" style="font-size: 14px; color: #555;">
                    Mostrando {{ $clientes->firstItem() }} - {{ $clientes->lastItem() }} de {{ $clientes->total() }} clientes
                </div>

                <!-- Navegación -->
                <div class="paginacion-nav">
                    <nav>
                        <ul class="pagination" style="margin: 0; display: flex; list-style: none; gap: 5px;">
                            <!-- Botón Anterior -->
                            <li class="page-item {{ $clientes->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $clientes->previousPageUrl() }}"
                                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #4299e1;">
                                    <i class="fa-solid fa-chevron-left"></i> Anterior
                                </a>
                            </li>

                            <!-- Números de página -->
                            @foreach ($clientes->getUrlRange(1, $clientes->lastPage()) as $page => $url)
                            @if($page == $clientes->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="padding: 8px 12px; background: #4299e1; color: white; border-radius: 4px; border: 1px solid #4299e1;">
                                    {{ $page }}
                                </span>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}"
                                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #4299e1;">
                                    {{ $page }}
                                </a>
                            </li>
                            @endif
                            @endforeach

                            <!-- Botón Siguiente -->
                            <li class="page-item {{ !$clientes->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $clientes->nextPageUrl() }}"
                                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #4299e1;">
                                    Siguiente <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            @endif
        </div>

    </section>

    <!-- Modal para editar cliente -->
    <div id="modalEditarCliente" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <strong>
                    <h2 style="font-size: 24px;">Editar Cliente</h2>
                </strong>
                <!-- <h3>Editar Cliente</h3> -->
                <!-- <button type="button" class="close-modal" onclick="cerrarModalEditar()">&times;</button> -->
            </div>
            <form id="formEditarCliente" method="POST">
                @csrf
                @method('PUT')
                <div class="form-grid-2">
                    <input type="text" id="edit_nombre" name="nombre" placeholder="Nombre completo" required>
                    <input type="text" id="edit_identificacion" name="identificacion" placeholder="ID" required>
                    <input type="email" id="edit_email" name="email" placeholder="Correo electrónico">
                    <input type="text" id="edit_telefono" name="telefono" placeholder="Teléfono">
                    <button type="submit" class="btn btn-azul btn-guardar-modal">Actualizar</button>
                    <button type="button" class="btn btn-rojo btn-cancelar-modal" onclick="cerrarModalEditar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal ventas cliente -->
    <div id="modalVentas" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <strong>
                    <h2 style="font-size: 20px;">Historial de Ventas - <span id="clienteModalNombre"></span></h2>
                </strong>
                <!-- <h3>Historial de Ventas - <span id="clienteModalNombre"></span></h3> -->
                <button type="button" class="close-modal" onclick="cerrarModalVentas()">&times;</button>
            </div>

            <!-- BUSCADOR AVANZADO -->
            <div class="buscador-ventas" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <div class="filtros-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; align-items: end;">
                    <!-- Filtro por N° Factura -->
                    <div class="filtro-item">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">N° Factura</label>
                        <input type="text" id="filtroFactura" placeholder="CDN-2025-001"
                            style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px;">
                    </div>

                    <!-- Filtro por Fecha -->
                    <div class="filtro-item">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Fecha</label>
                        <input type="date" id="filtroFecha"
                            style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px;">
                    </div>

                    <!-- Filtro por Precio (Rango) -->
                    <div class="filtro-item">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Precio Min.</label>
                        <input type="number" id="filtroPrecioMin" placeholder="Mínimo" step="0.01"
                            style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px;">
                    </div>

                    <div class="filtro-item">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Precio Max.</label>
                        <input type="number" id="filtroPrecioMax" placeholder="Máximo" step="0.01"
                            style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px;">
                    </div>

                    <!-- Botones de acción -->
                    <div class="filtro-item" style="grid-column: span 4; display: flex; gap: 10px; justify-content: center; margin-top: 10px;">
                        <button onclick="aplicarFiltros()" class="btn btn-azul" style="padding: 10px 20px;">
                            <i class="fa-solid fa-search"></i> Buscar
                        </button>
                        <button onclick="resetearFiltros()" class="btn btn-rojo" style="padding: 10px 20px;">
                            <i class="fa-solid fa-rotate-left"></i> Resetear
                        </button>
                    </div>
                </div>
            </div>

            <!-- CONTADOR DE RESULTADOS -->
            <div id="contadorResultados" style="margin-bottom: 10px; padding: 10px; background: #e3f2fd; border-radius: 5px; display: none;">
                <strong>Resultados encontrados: <span id="totalResultados">0</span></strong>
            </div>

            <!-- TABLA DE VENTAS -->
            <div class="tabla-container">
                <table class="tabla-ventas">
                    <thead>
                        <tr>
                            <th>N° Factura</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Método de Pago</th>
                            <th>Acciónes</th>
                        </tr>
                    </thead>
                    <tbody id="tablaVentasCliente">
                        <!-- Las ventas se cargarán aquí dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN -->
            <div id="paginacionVentas" style="display: none; margin-top: 20px; text-align: center;">
                <button id="btnAnterior" onclick="cambiarPagina(-1)" class="btn btn-azul" disabled>
                    <i class="fa-solid fa-chevron-left"></i> Anterior
                </button>
                <span id="infoPagina" style="margin: 0 15px; font-weight: bold;"></span>
                <button id="btnSiguiente" onclick="cambiarPagina(1)" class="btn btn-azul" disabled>
                    Siguiente <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <button onclick="cerrarModalVentas()" class="btn btn-rojo">Cerrar</button>
            </div>
        </div>
    </div>

</div>

<script>
    // ===== VARIABLES GLOBALES =====
    let ventasClienteActual = [];
    let clienteIdActual = null;
    let paginaActual = 1;
    const ventasPorPagina = 10;

    // ===== FUNCIONES DE FILTRADO =====

    // Función para aplicar filtros
    function aplicarFiltros() {
        const filtroFactura = document.getElementById('filtroFactura')?.value.trim().toLowerCase() || '';
        const filtroFecha = document.getElementById('filtroFecha')?.value || '';
        const filtroPrecioMin = parseFloat(document.getElementById('filtroPrecioMin')?.value) || 0;
        const filtroPrecioMax = parseFloat(document.getElementById('filtroPrecioMax')?.value) || Infinity;

        // Si no hay ventas cargadas, salir
        if (!ventasClienteActual || ventasClienteActual.length === 0) {
            const tbody = document.getElementById('tablaVentasCliente');
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No hay ventas para filtrar</td></tr>';
            return;
        }

        // Filtrar ventas
        let ventasFiltradas = ventasClienteActual.filter(venta => {
            // Filtro por número de factura
            if (filtroFactura && !venta.numero_factura.toLowerCase().includes(filtroFactura)) {
                return false;
            }

            // // Filtro por fecha
            // if (filtroFecha) {
            //     const fechaVenta = new Date(venta.created_at).toISOString().split('T')[0];
            //     if (fechaVenta !== filtroFecha) {
            //         return false;
            //     }
            // }

            // POR ESTO:
            if (filtroFecha) {
                // Obtener fecha en formato YYYY-MM-DD en zona horaria local
                const fecha = new Date(venta.created_at);
                const fechaLocal = fecha.getFullYear() + '-' +
                    String(fecha.getMonth() + 1).padStart(2, '0') + '-' +
                    String(fecha.getDate()).padStart(2, '0');

                if (fechaLocal !== filtroFecha) {
                    return false;
                }
            }


            // Filtro por rango de precio
            const totalVenta = parseFloat(venta.total);
            if (totalVenta < filtroPrecioMin || totalVenta > filtroPrecioMax) {
                return false;
            }

            return true;
        });

        // Actualizar tabla con ventas filtradas
        actualizarTablaVentas(ventasFiltradas);

        // Mostrar contador de resultados si existe el elemento
        const contadorResultados = document.getElementById('contadorResultados');
        const totalResultados = document.getElementById('totalResultados');

        if (contadorResultados && totalResultados) {
            totalResultados.textContent = ventasFiltradas.length;
            contadorResultados.style.display = 'block';
        }

        // Configurar paginación
        configurarPaginacion(ventasFiltradas);
    }

    // Función para resetear filtros
    function resetearFiltros() {
        // Limpiar campos de filtro
        const filtroFactura = document.getElementById('filtroFactura');
        const filtroFecha = document.getElementById('filtroFecha');
        const filtroPrecioMin = document.getElementById('filtroPrecioMin');
        const filtroPrecioMax = document.getElementById('filtroPrecioMax');

        if (filtroFactura) filtroFactura.value = '';
        if (filtroFecha) filtroFecha.value = '';
        if (filtroPrecioMin) filtroPrecioMin.value = '';
        if (filtroPrecioMax) filtroPrecioMax.value = '';

        // Ocultar contador de resultados
        const contadorResultados = document.getElementById('contadorResultados');
        if (contadorResultados) {
            contadorResultados.style.display = 'none';
        }

        // Si hay ventas cargadas, mostrar todas
        if (ventasClienteActual && ventasClienteActual.length > 0) {
            aplicarFiltros();
        }
    }

    // ===== FUNCIONES EXISTENTES =====

    async function editarCliente(clienteId) {
        try {
            const response = await fetch(`/clientes/${clienteId}/edit`);
            const cliente = await response.json();

            // Llenar el formulario con los datos del cliente
            document.getElementById('edit_nombre').value = cliente.nombre;
            document.getElementById('edit_identificacion').value = cliente.identificacion;
            document.getElementById('edit_email').value = cliente.email || '';
            document.getElementById('edit_telefono').value = cliente.telefono || '';

            // Actualizar el action del formulario
            const form = document.getElementById('formEditarCliente');
            form.action = `/clientes/${clienteId}`;

            // Mostrar el modal
            document.getElementById('modalEditarCliente').style.display = 'block';

        } catch (error) {
            console.error('Error al cargar cliente:', error);
            alert('Error al cargar los datos del cliente');
        }
    }

    // Función para cerrar el modal de edición
    function cerrarModalEditar() {
        document.getElementById('modalEditarCliente').style.display = 'none';
        // Limpiar el formulario al cerrar
        document.getElementById('formEditarCliente').reset();
    }

    // Función para ver ventas del cliente
    async function verVentas(clienteId, clienteNombre) {
        try {
            clienteIdActual = clienteId;
            paginaActual = 1;

            document.getElementById('clienteModalNombre').textContent = clienteNombre;
            document.getElementById('modalVentas').style.display = 'block';

            // Mostrar loading
            const tbody = document.getElementById('tablaVentasCliente');
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Cargando ventas...</td></tr>';

            // Resetear filtros
            resetearFiltros();

            // Cargar todas las ventas del cliente
            const response = await fetch(`/clientes/${clienteId}/ventas`);
            ventasClienteActual = await response.json();

            // Mostrar todas las ventas inicialmente
            aplicarFiltros();

        } catch (error) {
            console.error('Error al cargar ventas:', error);
            const tbody = document.getElementById('tablaVentasCliente');
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Error al cargar las ventas</td></tr>';
        }
    }

    // Función para configurar paginación
    function configurarPaginacion(ventasFiltradas) {
        const totalPaginas = Math.ceil(ventasFiltradas.length / ventasPorPagina);
        const paginacionDiv = document.getElementById('paginacionVentas');
        const btnAnterior = document.getElementById('btnAnterior');
        const btnSiguiente = document.getElementById('btnSiguiente');
        const infoPagina = document.getElementById('infoPagina');

        if (totalPaginas > 1) {
            paginacionDiv.style.display = 'block';
            btnAnterior.disabled = paginaActual === 1;
            btnSiguiente.disabled = paginaActual === totalPaginas;
            infoPagina.textContent = `Página ${paginaActual} de ${totalPaginas}`;
        } else {
            paginacionDiv.style.display = 'none';
        }

        // Mostrar ventas de la página actual
        const inicio = (paginaActual - 1) * ventasPorPagina;
        const fin = inicio + ventasPorPagina;
        const ventasPagina = ventasFiltradas.slice(inicio, fin);

        actualizarTablaVentas(ventasPagina);
    }

    // Función para cambiar de página
    function cambiarPagina(direccion) {
        const totalVentas = ventasClienteActual.length;
        const totalPaginas = Math.ceil(totalVentas / ventasPorPagina);

        paginaActual += direccion;

        if (paginaActual < 1) paginaActual = 1;
        if (paginaActual > totalPaginas) paginaActual = totalPaginas;

        aplicarFiltros();
    }

    // Función para actualizar la tabla de ventas
    function actualizarTablaVentas(ventas) {
        const tbody = document.getElementById('tablaVentasCliente');

        if (ventas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px;">No se encontraron ventas con los filtros aplicados</td></tr>';
            return;
        }

        tbody.innerHTML = ventas.map(venta => `
        <tr>
            <td><strong>${venta.numero_factura}</strong></td>
            <td>${new Date(venta.created_at).toLocaleDateString()}</td>
            <td><span style="font-weight: bold; color: #2d3748;">$${parseFloat(venta.total).toFixed(2)}</span></td>
            <td>
                <span class="badge ${venta.metodo_pago === 'Efectivo' ? 'badge-success' : 'badge-info'}" 
                      style="background: ${venta.metodo_pago === 'Efectivo' ? '#48bb78' : '#4299e1'}; 
                             color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                    ${venta.metodo_pago}
                </span>
            </td>
            <td>
                <button class="btn btn-info btn-sm" onclick="verDetalleVenta(${venta.id})" 
                        style="background: #4299e1; color: white; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer;"
                        title="Ver factura en formato A4">
                    <i class="fa-solid fa-eye"></i> Ver Detalle
                </button>
            </td>
        </tr>
    `).join('');
    }

    // Función para ver detalle de venta con manejo de errores
    function verDetalleVenta(ventaId) {
        if (!ventaId || ventaId <= 0) {
            alert('Error: ID de venta no válido');
            return;
        }

        try {
            // Mostrar indicador de carga (opcional)
            const btn = event.target;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Cargando...';
            btn.disabled = true;

            // Abrir PDF A4 en nueva pestaña
            const url = `/factura/pdf/a4/${ventaId}`;
            const nuevaVentana = window.open(url, '_blank');

            if (!nuevaVentana || nuevaVentana.closed || typeof nuevaVentana.closed === 'undefined') {
                // Si el popup fue bloqueado, redirigir en la misma pestaña
                alert('Permite las ventanas emergentes para ver la factura o haz clic en el enlace.');
                window.location.href = url;
            }

            // Restaurar botón después de 2 segundos
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, 2000);

        } catch (error) {
            console.error('Error al abrir la factura:', error);
            alert('Error al abrir la factura. Por favor, intenta nuevamente.');

            // Restaurar botón en caso de error
            const btn = event.target;
            btn.innerHTML = '<i class="fa-solid fa-file-pdf"></i> Ver Factura';
            btn.disabled = false;
        }
    }

    // Función para ver detalle de venta
    // function verDetalleVenta(ventaId) {
    //     // alert(`Detalle de venta ${ventaId} - Esta funcionalidad se puede expandir para mostrar productos, etc.`);
    //     // Abrir PDF A4 en nueva pestaña
    //     window.open(`/factura/pdf/a4/${ventaId}`, '_blank');

    //     // O si prefieres mantener un registro en consola:
    //     console.log(`Abriendo factura A4 para venta ID: ${ventaId}`);
    // }

    // Función para cerrar el modal de ventas
    function cerrarModalVentas() {
        document.getElementById('modalVentas').style.display = 'none';
        // Limpiar la tabla al cerrar
        document.getElementById('tablaVentasCliente').innerHTML = '';
        // Resetear variables
        ventasClienteActual = [];
        clienteIdActual = null;
        paginaActual = 1;
    }

    // Permitir buscar con Enter
    document.addEventListener('DOMContentLoaded', function() {
        const inputsFiltro = ['filtroFactura', 'filtroFecha', 'filtroPrecioMin', 'filtroPrecioMax'];

        inputsFiltro.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        aplicarFiltros();
                    }
                });
            }
        });
    });

    // Cerrar modales al hacer click fuera
    window.onclick = function(event) {
        const modalEditar = document.getElementById('modalEditarCliente');
        const modalVentas = document.getElementById('modalVentas');

        if (event.target === modalEditar) {
            modalEditar.style.display = 'none';
        }
        if (event.target === modalVentas) {
            modalVentas.style.display = 'none';
        }
    }
</script>

@endsection