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
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #eee;
        padding-bottom: 15px;
        background-color: #4299e1;
        /* background-color: #c9e9f9ff; */
    }

    .modal-header h3 {
        color: #333;
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


    

@endsection