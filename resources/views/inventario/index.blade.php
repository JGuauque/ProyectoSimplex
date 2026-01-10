@extends('layouts.plantilla')

@section('titulo', 'Gestión de Inventario')

@section('contenido')


<div class="inventario-main">

    <!-- Formulario Crear Producto -->
    <section class="formulario-inventario">
        <strong>
            <h2 style="font-size: 24px;">Registrar Producto</h2>
        </strong>
        <!-- <h2>Registrar Producto</h2> -->
        <form method="POST" action="{{ route('inventario.store') }}" id="formInventario" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <input type="text" id="nombreProducto" name="nombre" placeholder="Nombre del producto *" required value="{{ old('nombre') }}">
                <input type="text" id="codigoProducto" placeholder="Código (auto)" readonly>
                <input type="number" id="costo" name="costo" step="0.01" placeholder="Costo *" required value="{{ old('costo') }}">
                <input type="number" id="precio" name="precio" step="0.01" placeholder="Precio *" required value="{{ old('precio') }}">
                <input type="number" id="stock" name="stock" min="0" placeholder="Stock *" required value="{{ old('stock') }}">
                <select id="categoria" name="categoria" required>
                    <option value="">Seleccione categoría *</option>
                    <option value="Tecnología" {{ old('categoria') == 'Tecnología' ? 'selected' : '' }}>Tecnología</option>
                    <option value="Hogar" {{ old('categoria') == 'Hogar' ? 'selected' : '' }}>Hogar</option>
                    <option value="Juguetería" {{ old('categoria') == 'Juguetería' ? 'selected' : '' }}>Juguetería</option>
                    <option value="Salud" {{ old('categoria') == 'Salud' ? 'selected' : '' }}>Salud</option>
                    <option value="Cocina" {{ old('categoria') == 'Cocina' ? 'selected' : '' }}>Cocina</option>
                </select>
                <label class="destacado-check">
                    <input id="destacado" name="destacado" type="checkbox" {{ old('destacado') ? 'checked' : '' }}>
                    <span>Destacado</span>
                </label>
                <input id="imagen" name="imagen" type="file" accept="image/*">
                <button type="submit" class="btn btn-azul">Guardar</button>
            </div>
        </form>
    </section>

    <!-- Filtros -->
    <section class="filtros-inventario">
        <input id="buscarNombre" type="text" placeholder="Buscar por nombre...">
        <select id="buscarCategoria">
            <option value="">Todas las categorías</option>
            <option value="Tecnología">Tecnología</option>
            <option value="Hogar">Hogar</option>
            <option value="Juguetería">Juguetería</option>
            <option value="Salud">Salud</option>
            <option value="Cocina">Cocina</option>
        </select>
        <button id="btnReset" class="btn btn-rojo">Reset</button>
    </section>

    <!-- Grid de Productos -->
    <section class="listado-inventario">
        <strong>
            <h2 style="font-size: 24px;">Productos</h2>
        </strong>

        <div id="gridProductos" class="grid-productos">
            @foreach($productos as $producto)
            <div class="card-producto" data-categoria="{{ $producto->categoria }}" data-nombre="{{ strtolower($producto->nombre) }}">
                @if($producto->destacado)
                <div class="star">
                    ⭐ <!-- Emoji de estrella -->
                </div>
                @endif

                @if($producto->imagen)
                <img src="{{ url('storage/' . $producto->imagen) }}"
                    alt="{{ $producto->nombre }}"
                    class="imagen-producto">
                @else
                <img src="{{ url('images/placeholder-producto.png') }}"
                    alt="Sin imagen"
                    class="imagen-producto">
                @endif



                <h3>{{ $producto->nombre }}</h3>
                <p><strong>Código:</strong> {{ $producto->codigo }}</p>
                <p><strong>Costo:</strong> ${{ number_format($producto->costo, 2) }}</p>
                <p><strong>Precio:</strong> ${{ number_format($producto->precio, 2) }}</p>
                <p><strong>Stock:</strong> {{ $producto->stock }}</p>
                <p><strong>Categoría:</strong> {{ $producto->categoria }}</p>

                <div class="buttons">
                    <button class="btn btn-editar" data-producto-id="{{ $producto->id }}">Editar</button>
                    <form action="{{ route('inventario.destroy', $producto->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-rojo" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </section>
</div>

<!-- Modal para Editar Producto -->
<div id="modalEditar" class="modal hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Editar Producto</h2>
            <button class="close-modal" onclick="cerrarModalEditar()">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" id="formEditarProducto" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <input type="text" id="edit_nombre" name="nombre" placeholder="Nombre del producto" required>
                    <input type="text" id="edit_codigo" placeholder="Código" readonly>
                    <input type="number" id="edit_costo" name="costo" step="0.01" placeholder="Costo" required>
                    <input type="number" id="edit_precio" name="precio" step="0.01" placeholder="Precio" required>
                    <input type="number" id="edit_stock" name="stock" min="0" placeholder="Stock" required>
                    <select id="edit_categoria" name="categoria" required>
                        <option value="Tecnología">Tecnología</option>
                        <option value="Hogar">Hogar</option>
                        <option value="Juguetería">Juguetería</option>
                        <option value="Salud">Salud</option>
                        <option value="Cocina">Cocina</option>
                    </select>
                    <label class="destacado-check">
                        <input id="edit_destacado" name="destacado" type="checkbox" value="1">
                        <span></span>
                    </label>
                    <input id="edit_imagen" name="imagen" type="file" accept="image/*">
                    <button type="submit" class="btn btn-azul">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Imagen -->
<div id="modalImagen" class="modal hidden">
    <div class="modal-wrap">
        <button class="modal-close" onclick="cerrarModalImagen()">&times;</button>
        <img id="imagenAmpliada" alt="Zoom producto">
    </div>
</div>

<script>
    // Funciones para modales
    function abrirModalEditar(productoId) {
        fetch(`/inventario/${productoId}/get-data`)
            .then(response => response.json())
            .then(producto => {
                document.getElementById('edit_nombre').value = producto.nombre;
                document.getElementById('edit_codigo').value = producto.codigo;
                document.getElementById('edit_costo').value = producto.costo;
                document.getElementById('edit_precio').value = producto.precio;
                document.getElementById('edit_stock').value = producto.stock;
                document.getElementById('edit_categoria').value = producto.categoria;
                document.getElementById('edit_destacado').checked = producto.destacado;

                document.getElementById('formEditarProducto').action = `/inventario/${productoId}`;
                document.getElementById('modalEditar').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del producto');
            });
    }

    function cerrarModalEditar() {
        document.getElementById('modalEditar').style.display = 'none';
    }

    function ampliarImagen(src) {
        document.getElementById('imagenAmpliada').src = src;
        document.getElementById('modalImagen').style.display = 'block';
    }

    function cerrarModalImagen() {
        document.getElementById('modalImagen').style.display = 'none';
    }

    // Filtros
    document.addEventListener('DOMContentLoaded', function() {
        // Event listeners para botones editar
        document.querySelectorAll('.btn-editar').forEach(button => {
            button.addEventListener('click', function() {
                const productoId = this.getAttribute('data-producto-id');
                abrirModalEditar(productoId);
            });
        });

        document.querySelectorAll('.imagen-producto').forEach(img => {
            img.addEventListener('click', function() {
                const src = this.getAttribute('data-imagen-src');
                ampliarImagen(src);
            });
        });

        // Filtro por nombre
        document.getElementById('buscarNombre').addEventListener('input', function() {
            filtrarProductos();
        });

        // Filtro por categoría
        document.getElementById('buscarCategoria').addEventListener('change', function() {
            filtrarProductos();
        });

        // Reset filtros
        document.getElementById('btnReset').addEventListener('click', function() {
            document.getElementById('buscarNombre').value = '';
            document.getElementById('buscarCategoria').value = '';
            filtrarProductos();
        });

        // Cerrar modales al hacer clic fuera
        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('modalEditar')) {
                cerrarModalEditar();
            }
            if (event.target === document.getElementById('modalImagen')) {
                cerrarModalImagen();
            }
        });

        // Cerrar modales con ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                cerrarModalEditar();
                cerrarModalImagen();
            }
        });
    });

    function filtrarProductos() {
        const nombre = document.getElementById('buscarNombre').value.toLowerCase();
        const categoria = document.getElementById('buscarCategoria').value;

        document.querySelectorAll('.card-producto').forEach(card => {
            const cardNombre = card.getAttribute('data-nombre');
            const cardCategoria = card.getAttribute('data-categoria');

            const coincideNombre = cardNombre.includes(nombre);
            const coincideCategoria = !categoria || cardCategoria === categoria;

            if (coincideNombre && coincideCategoria) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection