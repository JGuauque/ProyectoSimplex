@extends('layouts.plantilla')

@section('titulo', 'Registrar Ventas')

@section('contenido')

@php
use App\Models\Venta;
$proximoNumero = Venta::obtenerProximoNumero();
@endphp

<style>
    /* Estilos para los campos de producto en línea */
    .campos-producto-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 15px;
        align-items: end;
        margin-bottom: 20px;
    }

    .campo-producto {
        display: flex;
        flex-direction: column;
    }

    .campo-producto label {
        font-weight: bold;
        margin-bottom: 5px;
        color: #555;
        font-size: 14px;
    }

    .campo-producto input {
        padding: 10px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    #buscarProducto {
        width: 300px;
    }

    #garantia {
        width: 100px;
    }

    #cantidad {
        width: 100px;
    }

    .campo-producto input:focus {
        outline: none;
        border-color: #667eea;
    }

    .mensaje-error {
        color: var(--rojo);
        font-size: 12px;
        margin-top: 4px;
        display: none;
    }

    .btn-agregar {
        white-space: nowrap;
        padding: 10px 15px;
    }

    /* Estilos para la búsqueda */
    .buscar-container {
        position: relative;
    }

    .resultados-busqueda {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-top: 2px;
    }

    .resultado-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }

    .resultado-item:hover {
        background: #f5f5f5;
    }

    .resultado-item:last-child {
        border-bottom: none;
    }

    .hidden {
        display: none;
    }

    /* Ajustes específicos para el buscador de clientes */
    /* .form-row .buscar-container {
        position: relative;
    }

    .form-row .resultados-busqueda {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-top: 2px;
    } */

    /* Estilo para campos de cliente cuando están en modo edición */
    /* input:read-only {
        background-color: #f8f9fa;
        border-color: #e9ecef;
    }

    input:not(:read-only) {
        background-color: white;
        border-color: #667eea;
    } */

    .resultados-busqueda {
        max-height: 300px;
        /* Aumentamos un poco la altura */
        overflow-y: auto;
    }

    /* Estilo para la barra de scroll */
    .resultados-busqueda::-webkit-scrollbar {
        width: 6px;
    }

    .resultados-busqueda::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .resultados-busqueda::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .resultados-busqueda::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Estilos para el modal */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        display: flex;
        justify-content: center;
        padding-top: 50px;
        overflow-y: auto;
    }

    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
        animation: modalAppear 0.3s ease;
        max-width: 500px;
        width: 90%;

        max-height: 90vh;
        /* Limitar altura máxima */
        overflow-y: auto;
        /* Permitir scroll si es necesario */

        position: relative;
        z-index: 10000;

        margin: auto;
        /* Asegurar centrado */
    }

    @keyframes modalAppear {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 28px;
        cursor: pointer;
        color: #666;
        background: none;
        border: none;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .close-btn:hover {
        background: #f5f5f5;
        color: #333;
    }

    .btn-imprimir:hover {
        opacity: 0.9;
        transform: translateY(-2px);
        transition: all 0.2s;
    }

    .btn-enviar:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        transition: all 0.2s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<!-- Intro -->
<div id="intro" class="intro">
    <img src="{{ asset('Assets/control.png') }}" alt="Control Nintendo" class="intro-img">
</div>

<!-- Main -->
<div class="ventas-bg">

    <div class="ventas-container">
        <div class="ventas-content">

            <!-- Mario -->
            <div class="ventas-mario">
                <img src="{{ asset('Assets/mario-coins.png') }}" alt="Mario Coins">
            </div>

            <!-- Formulario -->
            <div class="ventas-formulario">
                <h2 class="ventas-title">
                    <i class="fa-solid fa-cash-register"></i> Registrar Venta - Factura N° {{ $proximoNumero }}
                </h2>

                <form id="formVenta" class="ventas-form" method="POST" action="{{ route('ventas.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-row">
                            <label for="idCliente">ID Cliente</label>
                            <div class="buscar-container">
                                <input type="text" id="idCliente" name="cliente_id" required placeholder="Escribe el ID del cliente...">
                                <div id="resultadosClientes" class="resultados-busqueda hidden"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label for="nombreCliente">Nombre Cliente</label>
                            <input type="text" id="nombreCliente" name="nombre_cliente" required>
                        </div>
                        <div class="form-row">
                            <label for="celularCliente">Celular</label>
                            <input type="text" id="celularCliente" name="celular_cliente" required>
                        </div>
                    </div>

                    <hr>

                    <div id="productosSection">
                        <h3>Productos</h3>

                        <!-- Campos de producto en línea -->
                        <div class="campos-producto-grid">
                            <div class="campo-producto">
                                <label for="buscarProducto">Producto</label>
                                <div class="buscar-container">
                                    <input type="text" id="buscarProducto" placeholder="Escribe para buscar...">
                                    <div id="resultadosBusqueda" class="resultados-busqueda hidden"></div>
                                </div>
                            </div>

                            <div class="campo-producto">
                                <label for="garantia">Garantía</label>
                                <input type="text" id="garantia" name="garantia" placeholder="1 mes" value="1 mes">
                            </div>

                            <div class="campo-producto">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" id="cantidad" name="cantidad" value="1" min="1">
                            </div>

                            <div class="campo-producto tooltip-wrap">
                                <label for="precioVenta">Precio de venta</label>
                                <input type="number" id="precioVenta" name="precio_venta" step="0.01">
                                <span id="tooltipBase" class="tooltip hidden"></span>
                            </div>

                            <div class="campo-producto">
                                <label>&nbsp;</label>
                                <button type="button" id="agregarProducto" class="btn btn-azul btn-agregar">
                                    <i class="fa-solid fa-plus"> </i> Agregar producto
                                </button>
                            </div>
                        </div>

                        <!-- Tabla de productos agregados -->
                        <div id="listaProductos" class="lista-productos">
                            <table class="tabla-productos">
                                <thead>
                                    <tr>
                                        <th>PRODUCTO</th>
                                        <th>CÓDIGO</th>
                                        <th>GARANTÍA</th>
                                        <th>CANT.</th>
                                        <th>PRECIO</th>
                                        <th>SUBTOTAL</th>
                                        <th>ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyProductos">
                                    <!-- Los productos se agregarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="form-grid">
                        <div class="form-row">
                            <label for="metodoPago">Método de pago</label>
                            <select id="metodoPago" name="metodo_pago" required>
                                <option value="">— Selecciona —</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-total">
                        <h3>Total a pagar: <span id="totalVenta">$0.00</span></h3>
                        <input type="hidden" id="totalVentaInput" name="total_venta" value="0">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-azul">
                            <i class="fa-solid fa-check"></i> Confirmar Venta
                        </button>
                        <button type="reset" class="btn btn-rojo" id="btnLimpiar">
                            <i class="fa-solid fa-broom"></i> Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de comprobante -->
    <div id="comprobanteModal" class="modal" style="display: none;">
        <div class="modal-content" style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; margin: 50px auto; position: relative;">
            <!-- <span class="close-btn" style="position: absolute; top: 15px; right: 15px; font-size: 24px; cursor: pointer;">&times;</span> -->

            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="color: #333; margin-bottom: 5px;">Venta Registrada Exitosamente</h3>
                <p style="color: #666;">Comprobante: <strong id="modalFacturaNumero">FO01-79</strong></p>
            </div>

            <div style="text-align: center; margin: 25px 0;">
                <p style="margin-bottom: 20px;">Seleccione formato de impresión:</p>
                <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 30px;">
                    <!-- <a href="#"
                        id="btnImprimirA4"

                        target="_blank"
                        rel="noopener noreferrer"

                        class="btn-imprimir"
                        style="padding: 12px 25px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block;">
                        <i class="fa-solid fa-print"></i> A4
                    </a>
                    <a href="#"
                        id="btnImprimir80MM"


                        target="_blank"
                        rel="noopener noreferrer"

                        class="btn-imprimir"
                        style="padding: 12px 25px; background: #2196F3; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block;">
                        <i class="fa-solid fa-print"></i> 80MM
                    </a> -->
                    <button type="button"
                        class="btn-imprimir"
                        id="btnImprimirA4"
                        data-formato="A4"
                        style="padding: 12px 25px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block;">
                        <i class="fa-solid fa-print"></i> A4
                    </button>
                    <button type="button"
                        class="btn-imprimir"
                        id="btnImprimir80MM"
                        data-formato="80MM"
                        style="padding: 12px 25px; background: #2196F3; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block;">
                        <i class="fa-solid fa-print"></i> 80MM
                    </button>
                </div>
            </div>

            <div style="margin: 25px 0;">
                <p style="text-align: center; margin-bottom: 15px; color: #666;">Enviar comprobante:</p>

                <div style="display: flex; gap: 10px; margin-bottom: 10px;">

                    <input type="email"
                        id="emailCliente"
                        placeholder="Correo electrónico"
                        style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                        value="{{ $venta->cliente->email ?? '' }}">
                    <button type="button"
                        id="btnEnviarEmail"
                        class="btn-enviar"
                        data-tipo="email"
                        style="padding: 10px 15px; background: #FF9800; color: white; border: none; border-radius: 5px; cursor: pointer; height: 45px;">
                        <i class="fa-solid fa-envelope"></i> Enviar
                    </button>
                </div>
                <!-- <div style="display: flex; gap: 10px;">
                    <input type="text"
                        id="whatsappCliente"
                        placeholder="Número de WhatsApp"
                        style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                        value="{{ $venta->cliente->telefono ?? '' }}">
                    <button type="button"
                        id="btnEnviarWhatsApp"
                        class="btn-enviar"
                        data-tipo="whatsapp"
                        style="padding: 10px 15px; background: #25D366; color: white; border: none; border-radius: 5px; cursor: pointer; height: 45px;">
                        <i class="fa-brands fa-whatsapp"></i> Enviar
                    </button>
                </div> -->
                <div id="emailStatus" style="margin-top: 10px; text-align: center; font-size: 12px; display: none;"></div>
            </div>

            <div style="display: flex; justify-content: center; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                <a href="#" class="btn" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fa-solid fa-list"></i> Ir al listado
                </a>
                <a href="{{ route('ventas.create') }}" class="btn" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;">
                    <i class="fa-solid fa-plus"></i> Nuevo comprobante
                </a>
                <!-- <button type="button" id="btnNuevoComprobante" class="btn" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fa-solid fa-plus"></i> Nuevo comprobante
                </button> -->
            </div>
        </div>
    </div>
    <!-- Overlay para el modal -->
    <div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9998;"></div>
</div>

<script src="{{ asset('js/venta-modal.js') }}"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buscarProducto = document.getElementById('buscarProducto');
        const resultadosBusqueda = document.getElementById('resultadosBusqueda');
        const precioVenta = document.getElementById('precioVenta');
        const cantidad = document.getElementById('cantidad');
        const garantia = document.getElementById('garantia');
        const agregarProducto = document.getElementById('agregarProducto');
        const tbodyProductos = document.getElementById('tbodyProductos');
        const totalVenta = document.getElementById('totalVenta');
        const totalVentaInput = document.getElementById('totalVentaInput');
        const btnLimpiar = document.getElementById('btnLimpiar');
        const formVenta = document.getElementById('formVenta');


        // ===== BUSCADOR DE CLIENTES =====
        const idCliente = document.getElementById('idCliente');
        const nombreCliente = document.getElementById('nombreCliente');
        const celularCliente = document.getElementById('celularCliente');
        const resultadosClientes = document.getElementById('resultadosClientes');

        let productosAgregados = [];
        let productoSeleccionado = null;

        // Formatear garantía
        garantia.addEventListener('input', function() {
            let valor = this.value.replace(/[^0-9]/g, '');

            if (valor === '') {
                this.value = '0 meses';
            } else if (valor === '1') {
                this.value = valor + ' mes';
            } else {
                this.value = valor + ' meses';
            }
        });


        // ===== BUSCADOR DE PRODUCTOS =====
        buscarProducto.addEventListener('input', function() {
            const query = this.value.trim();
            buscarProductos(query);
        });

        // Mostrar todos los productos al hacer focus
        buscarProducto.addEventListener('focus', function() {
            if (this.value.trim() === '') {
                // Si el campo está vacío, mostrar todos los productos
                buscarProductos('');
            } else {
                // Si ya tiene texto, hacer búsqueda con ese texto
                buscarProductos(this.value.trim());
            }
        });

        function buscarProductos(query) {
            if (query === '' && buscarProducto !== document.activeElement) {
                // No buscar si el campo está vacío y no tiene foco
                return;
            }

            fetch(`/buscar-productos?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    mostrarResultados(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Version antigua *************
        // // Buscar productos
        // buscarProducto.addEventListener('input', function() {
        //     const query = this.value.trim();

        //     if (query.length < 2) {
        //         resultadosBusqueda.classList.add('hidden');
        //         return;
        //     }

        //     fetch(`/buscar-productos?q=${encodeURIComponent(query)}`)
        //         .then(response => response.json())
        //         .then(data => {
        //             mostrarResultados(data);
        //         })
        //         .catch(error => {
        //             console.error('Error:', error);
        //         });
        // });

        function mostrarResultados(productos) {
            resultadosBusqueda.innerHTML = '';

            if (productos.length === 0) {
                resultadosBusqueda.innerHTML = '<div class="resultado-item">No se encontraron productos</div>';
            } else {
                productos.forEach(producto => {
                    const div = document.createElement('div');
                    div.className = 'resultado-item';
                    div.innerHTML = `
                    <strong>${producto.nombre}</strong><br>
                    <small>Código: ${producto.codigo} | Precio: $${producto.precio} | Stock: ${producto.stock}</small>
                `;
                    div.addEventListener('click', function() {
                        seleccionarProducto(producto);
                    });
                    resultadosBusqueda.appendChild(div);
                });
            }

            resultadosBusqueda.classList.remove('hidden');
        }

        // Función para validar el campo de garantía
        function validarGarantia() {
            const garantiaInput = document.getElementById('garantia');
            const garantiaValor = garantiaInput.value.trim();

            // Remueve mensajes de error previos
            const errorExistente = garantiaInput.parentNode.querySelector('.mensaje-error');
            if (errorExistente) {
                errorExistente.remove();
            }

            // Valida que no esté vacío
            if (!garantiaValor) {
                mostrarErrorGarantia('Llenar campo');
                return false;
            }

            // Valida formato básico (puedes ajustar según tus necesidades)
            if (garantiaValor.length < 2) {
                mostrarErrorGarantia('La garantía debe tener al menos 2 caracteres');
                return false;
            }

            return true;
        }

        // Función para mostrar mensaje de error
        function mostrarErrorGarantia(mensaje) {
            const garantiaInput = document.getElementById('garantia');
            const campoProducto = garantiaInput.parentNode;

            // Crea o actualiza el mensaje de error
            let errorElement = campoProducto.querySelector('.mensaje-error');

            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'mensaje-error';
                campoProducto.appendChild(errorElement);
            }

            errorElement.textContent = mensaje;
            errorElement.style.display = 'block';

            // Resalta el input
            garantiaInput.style.borderColor = 'var(--rojo)';
            garantiaInput.style.borderWidth = '2px';

            // Enfoca el campo
            garantiaInput.focus();

            // Remueve el estilo de error cuando el usuario empiece a escribir
            garantiaInput.addEventListener('input', function limpiarError() {
                garantiaInput.style.borderColor = '';
                garantiaInput.style.borderWidth = '';
                errorElement.style.display = 'none';
                garantiaInput.removeEventListener('input', limpiarError);
            });
        }

        function seleccionarProducto(producto) {
            productoSeleccionado = producto;
            buscarProducto.value = producto.nombre;
            precioVenta.value = producto.precio;

            resultadosBusqueda.classList.add('hidden');

            // Mostrar tooltip con valor base
            const tooltipBase = document.getElementById('tooltipBase');
            if (tooltipBase) {
                tooltipBase.textContent = `💡 Valor base sugerido: $${producto.precio}`;
                tooltipBase.classList.remove("hidden");
                // Ocultar después de 4 segundos
                setTimeout(() => {
                    tooltipBase.classList.add("hidden");
                }, 4000);
            }

            // Poner foco en cantidad
            cantidad.focus();
        }

        // ===== NUEVA FUNCIONALIDAD: BUSCADOR DE CLIENTES =====
        // Buscar clientes
        // Mostrar todos los clientes al hacer focus
        idCliente.addEventListener('focus', function() {
            if (this.value.trim() === '') {
                // Si el campo está vacío, mostrar todos los clientes
                buscarClientes('');
            } else {
                // Si ya tiene texto, hacer búsqueda con ese texto
                buscarClientes(this.value.trim());
            }
        });

        function buscarClientes(query) {
            if (query === '' && idCliente !== document.activeElement) {
                // No buscar si el campo está vacío y no tiene foco
                return;
            }

            fetch(`/buscar-clientes?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    mostrarResultadosClientes(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        // idCliente.addEventListener('input', function() {
        //     const query = this.value.trim();
        //     buscarClientes(query);

        //     if (query.length < 2) {
        //         resultadosClientes.classList.add('hidden');
        //         return;
        //     }

        //     fetch(`/buscar-clientes?q=${encodeURIComponent(query)}`)
        //         .then(response => response.json())
        //         .then(data => {
        //             mostrarResultadosClientes(data);
        //         })
        //         .catch(error => {
        //             console.error('Error:', error);
        //         });
        // });

        function mostrarResultadosClientes(clientes) {
            resultadosClientes.innerHTML = '';

            if (clientes.length === 0) {
                // Si no hay clientes, permitir crear uno nuevo
                const div = document.createElement('div');
                div.className = 'resultado-item';
                div.innerHTML = `
                <strong>Crear nuevo cliente</strong><br>
                <small>ID: ${idCliente.value} - (No existe en la base de datos)</small>
            `;
                div.addEventListener('click', function() {
                    crearNuevoCliente();
                });
                resultadosClientes.appendChild(div);
            } else {
                clientes.forEach(cliente => {
                    const div = document.createElement('div');
                    div.className = 'resultado-item';
                    div.innerHTML = `
                    <strong>${cliente.nombre}</strong><br>
                    <small>ID: ${cliente.identificacion} | Tel: ${cliente.telefono}</small>
                `;
                    div.addEventListener('click', function() {
                        seleccionarCliente(cliente);
                    });
                    resultadosClientes.appendChild(div);
                });
            }

            resultadosClientes.classList.remove('hidden');
        }

        function seleccionarCliente(cliente) {
            idCliente.value = cliente.identificacion;
            nombreCliente.value = cliente.nombre;
            celularCliente.value = cliente.telefono;

            // Quitar readonly para permitir edición si es necesario
            nombreCliente.readOnly = false;
            celularCliente.readOnly = false;

            resultadosClientes.classList.add('hidden');
        }

        function crearNuevoCliente() {
            // Permitir edición de los campos para crear nuevo cliente
            nombreCliente.value = '';
            celularCliente.value = '';
            nombreCliente.readOnly = false;
            celularCliente.readOnly = false;

            // Poner foco en el nombre para que complete los datos
            nombreCliente.focus();

            resultadosClientes.classList.add('hidden');
        }

        // Cerrar resultados de búsqueda al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!buscarProducto.contains(e.target) && !resultadosBusqueda.contains(e.target)) {
                resultadosBusqueda.classList.add('hidden');
            }
            if (!idCliente.contains(e.target) && !resultadosClientes.contains(e.target)) {
                resultadosClientes.classList.add('hidden');
            }
        });

        // Validar que si se edita manualmente un campo, se quite el readonly
        nombreCliente.addEventListener('focus', function() {
            this.readOnly = false;
        });

        celularCliente.addEventListener('focus', function() {
            this.readOnly = false;
        });

        // Mostrar tooltip al enfocar precio de venta
        precioVenta.addEventListener('focus', function() {
            if (productoSeleccionado) {
                const tooltipBase = document.getElementById('tooltipBase');
                if (tooltipBase) {
                    tooltipBase.textContent = `💡 Valor base sugerido: $${productoSeleccionado.precio}`;
                    tooltipBase.classList.remove("hidden");
                }
            }
        });

        precioVenta.addEventListener('blur', function() {
            const tooltipBase = document.getElementById('tooltipBase');
            if (tooltipBase) {
                tooltipBase.classList.add("hidden");
            }
        });

        // Agregar producto a la tabla
        agregarProducto.addEventListener('click', function() {

            // Primero valida la garantía
            if (!validarGarantia()) {
                return; // Detiene la ejecución si hay error
            }

            if (!productoSeleccionado) {
                alert('Por favor selecciona un producto primero');
                return;
            }

            const producto = {
                id: productoSeleccionado.id,
                nombre: productoSeleccionado.nombre,
                codigo: productoSeleccionado.codigo,
                garantia: garantia.value,
                cantidad: parseInt(cantidad.value) || 1,
                precio: parseFloat(precioVenta.value) || 0,
                subtotal: 0
            };

            producto.subtotal = (producto.precio * producto.cantidad);

            // Agregar a la lista
            productosAgregados.push(producto);

            // Actualizar tabla
            actualizarTabla();

            // Limpiar campos
            limpiarCamposProducto();
        });

        function actualizarTabla() {
            tbodyProductos.innerHTML = '';
            let total = 0;

            productosAgregados.forEach((producto, index) => {
                total += producto.subtotal;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${producto.nombre}</td>
                <td>${producto.codigo}</td>
                <td>${producto.garantia}</td>
                <td>${producto.cantidad}</td>
                <td>$${producto.precio.toFixed(2)}</td>
                <td>$${producto.subtotal.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn-eliminar" onclick="eliminarProducto(${index})">
                        <i class="fa-solid fa-trash"></i> Eliminar
                    </button>
                </td>
            `;
                tbodyProductos.appendChild(tr);
            });

            // Actualizar el total en el lugar específico
            totalVenta.textContent = `$${total.toFixed(2)}`;
            totalVentaInput.value = total;
        }

        // Hacer la función global para eliminar productos
        window.eliminarProducto = function(index) {
            productosAgregados.splice(index, 1);
            actualizarTabla();
        };

        function limpiarCamposProducto() {
            productoSeleccionado = null;
            buscarProducto.value = '';
            precioVenta.value = '';
            cantidad.value = '1';
            garantia.value = '';
        }

        // Limpiar todo el formulario
        btnLimpiar.addEventListener('click', function() {
            productosAgregados = [];
            actualizarTabla();
            limpiarCamposProducto();
        });

        // Cerrar resultados de búsqueda al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!buscarProducto.contains(e.target) && !resultadosBusqueda.contains(e.target)) {
                resultadosBusqueda.classList.add('hidden');
            }
        });

        // ===== NUEVA FUNCIONALIDAD: ENVÍO DEL FORMULARIO =====
        // formVenta.addEventListener('submit', function(e) {
        //     // Prevenir el envío normal del formulario
        //     e.preventDefault();

        //     // Validar que hay productos agregados
        //     if (productosAgregados.length === 0) {
        //         alert('Por favor agrega al menos un producto a la venta');
        //         return;
        //     }

        //     // Validar que el método de pago esté seleccionado
        //     const metodoPago = document.getElementById('metodoPago').value;
        //     if (!metodoPago) {
        //         alert('Por favor selecciona un método de pago');
        //         return;
        //     }

        //     // Validar campos de cliente
        //     const idCliente = document.getElementById('idCliente').value;
        //     const nombreCliente = document.getElementById('nombreCliente').value;
        //     const celularCliente = document.getElementById('celularCliente').value;

        //     if (!idCliente || !nombreCliente || !celularCliente) {
        //         alert('Por favor completa todos los datos del cliente');
        //         return;
        //     }

        //     // Crear un campo oculto con los productos en formato JSON
        //     let productosInput = document.getElementById('productosData');
        //     if (!productosInput) {
        //         productosInput = document.createElement('input');
        //         productosInput.type = 'hidden';
        //         productosInput.name = 'productos';
        //         productosInput.id = 'productosData';
        //         this.appendChild(productosInput);
        //     }

        //     // Convertir los productos a JSON y asignarlos al campo oculto
        //     productosInput.value = JSON.stringify(productosAgregados);

        //     // Mostrar confirmación antes de enviar
        //     const confirmar = confirm(`¿Estás seguro de registrar la venta por $${totalVenta.textContent}?`);
        //     if (confirmar) {
        //         // Si todo está bien, enviar el formulario
        //         this.submit();
        //     }
        // });


        // #################################################################################################################################################


        // ===== NUEVA FUNCIONALIDAD: ENVÍO DEL FORMULARIO CON AJAX =====
        formVenta.addEventListener('submit', function(e) {
            // Prevenir el envío normal del formulario
            e.preventDefault();

            // Validar que hay productos agregados
            if (productosAgregados.length === 0) {
                alert('Por favor agrega al menos un producto a la venta');
                return;
            }

            // Validar que el método de pago esté seleccionado
            const metodoPago = document.getElementById('metodoPago').value;
            if (!metodoPago) {
                alert('Por favor selecciona un método de pago');
                return;
            }

            // Validar campos de cliente
            const idCliente = document.getElementById('idCliente').value;
            const nombreCliente = document.getElementById('nombreCliente').value;
            const celularCliente = document.getElementById('celularCliente').value;

            if (!idCliente || !nombreCliente || !celularCliente) {
                alert('Por favor completa todos los datos del cliente');
                return;
            }

            // Preparar datos para enviar
            const formData = new FormData(this);
            formData.append('productos', JSON.stringify(prepararDatosParaEnviar()));

            // Mostrar confirmación antes de enviar
            const confirmar = confirm(`¿Estás seguro de registrar la venta por $${totalVenta.textContent}?`);
            if (!confirmar) {
                return;
            }

            // Deshabilitar botón de enviar para evitar doble envío
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';

            // Enviar datos via AJAX
            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar el modal con el número de factura
                        mostrarModalComprobante(data);

                        // Limpiar el formulario para nueva venta
                        limpiarFormulario();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la venta');
                })
                .finally(() => {
                    // Rehabilitar botón
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> Confirmar Venta';
                });
        });


        // Función para verificar si las rutas existen
        function verificarRutas(ventaId) {
            console.log('=== VERIFICANDO RUTAS ===');
            console.log('Ruta 80mm:', `/factura/pdf/80mm/${ventaId}`);
            console.log('Ruta A4:', `/factura/pdf/a4/${ventaId}`);

            // Hacer una prueba de fetch a la ruta (opcional)
            fetch(`/factura/pdf/80mm/${ventaId}`)
                .then(response => {
                    console.log('Respuesta 80mm:', response.status, response.ok);
                })
                .catch(error => {
                    console.error('Error en ruta 80mm:', error);
                });
        }

        // function mostrarModalComprobante(data) {

        //     console.log('Datos de venta recibidos:', data); // Para depurar

        //     // Actualizar información en el modal
        //     document.getElementById('modalFacturaNumero').textContent = data.factura_numero || 'N/A';

        //     // Guardar el ID de la venta para usar en los botones de impresión
        //     window.ultimaVentaId = data.venta.id;
        //     console.log('Venta ID guardado:', window.ultimaVentaId); // Para depurar

        //     // Mostrar modal y overlay

        //     // Actualizar los href de los botones de impresión
        //     if (window.ultimaVentaId) {
        //         document.getElementById('btnImprimirA4').href = `/factura/pdf/a4/${window.ultimaVentaId}`;
        //         document.getElementById('btnImprimir80MM').href = `/factura/pdf/80mm/${window.ultimaVentaId}`;
        //     }

        //     // Mostrar modal
        //     document.getElementById('comprobanteModal').style.display = 'block';
        //     document.getElementById('modalOverlay').style.display = 'block';

        // }

        function limpiarFormulario() {
            // Limpiar productos
            productosAgregados = [];
            actualizarTabla();

            // Limpiar campos de producto
            limpiarCamposProducto();

            // Mantener datos del cliente (opcional)
            // Si quieres limpiar también los datos del cliente, descomenta:
            // document.getElementById('idCliente').value = '';
            // document.getElementById('nombreCliente').value = '';
            // document.getElementById('celularCliente').value = '';
            // document.getElementById('metodoPago').selectedIndex = 0;
        }

        // Función para preparar los datos antes de enviar
        function prepararDatosParaEnviar() {
            return productosAgregados.map(producto => ({
                id: producto.id,
                nombre: producto.nombre,
                codigo: producto.codigo,
                cantidad: producto.cantidad,
                precio: producto.precio,
                garantia: producto.garantia,
                subtotal: producto.subtotal
            }));
        }

        // Manejar eventos del modal
        document.addEventListener('DOMContentLoaded', function() {
            // Cerrar modal
            document.querySelector('.close-btn').addEventListener('click', cerrarModal);
            document.getElementById('modalOverlay').addEventListener('click', cerrarModal);

            // Botones de impresión (solo demo por ahora)
            document.querySelectorAll('.btn-imprimir', ).forEach(btn => {
                btn.addEventListener('click', function() {
                    const formato = this.getAttribute('data-formato');
                    const ventaId = window.ultimaVentaId; // Necesitamos guardar el ID de la última venta

                    console.log('Botón clickeado:', formato); // Para depurar
                    console.log('Venta ID disponible:', ventaId); // Para depurar

                    if (!ventaId) {
                        alert('No hay información de venta disponible');
                        return;
                    }

                    // Construir la URL correctamente
                    let url = '';

                    if (formato === '80MM') {
                        // Abrir PDF de 80mm en nueva pestaña
                        window.open(`/factura/pdf/80mm/${ventaId}`, '_blank');
                        url = `/factura/pdf/80mm/${ventaId}`;
                    } else if (formato === 'A4') {
                        // Abrir PDF A4 en nueva pestaña
                        window.open(`/factura/pdf/a4/${ventaId}`, '_blank');
                        url = `/factura/pdf/a4/${ventaId}`;
                    }

                    console.log('URL generada:', url); // Para depurar

                    // Abrir en nueva pestaña
                    if (url) {
                        window.open(url, '_blank');
                    }


                    // alert(`Imprimiendo en formato ${formato}...\n\nEsta funcionalidad se implementará próximamente.`);
                });



            });
        });



        function cerrarModal() {
            document.getElementById('comprobanteModal').style.display = 'none';
            document.getElementById('modalOverlay').style.display = 'none';
        }

        // Cerrar modal con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });


        // #################################################################################################################################################

        // Función para preparar los datos antes de enviar (versión mejorada)
        // function prepararDatosParaEnviar() {
        //     return productosAgregados.map(producto => ({
        //         id: producto.id,
        //         nombre: producto.nombre,
        //         codigo: producto.codigo,
        //         cantidad: producto.cantidad,
        //         precio: producto.precio,
        //         garantia: producto.garantia,
        //         subtotal: producto.subtotal
        //     }));
        // }

        // // Modificar el event listener del submit para usar la función de preparación
        // const originalSubmitHandler = formVenta.onsubmit;
        // formVenta.onsubmit = null;

        // formVenta.addEventListener('submit', function(e) {
        //     e.preventDefault();

        //     if (productosAgregados.length === 0) {
        //         alert('Por favor agrega al menos un producto a la venta');
        //         return;
        //     }

        //     const metodoPago = document.getElementById('metodoPago').value;
        //     if (!metodoPago) {
        //         alert('Por favor selecciona un método de pago');
        //         return;
        //     }

        //     const idCliente = document.getElementById('idCliente').value;
        //     const nombreCliente = document.getElementById('nombreCliente').value;
        //     const celularCliente = document.getElementById('celularCliente').value;

        //     if (!idCliente || !nombreCliente || !celularCliente) {
        //         alert('Por favor completa todos los datos del cliente');
        //         return;
        //     }

        //     let productosInput = document.getElementById('productosData');
        //     if (!productosInput) {
        //         productosInput = document.createElement('input');
        //         productosInput.type = 'hidden';
        //         productosInput.name = 'productos';
        //         productosInput.id = 'productosData';
        //         this.appendChild(productosInput);
        //     }

        //     // Usar la función de preparación
        //     productosInput.value = JSON.stringify(prepararDatosParaEnviar());

        //     const confirmar = confirm(`¿Estás seguro de registrar la venta por $${totalVenta.textContent}?`);
        //     if (confirmar) {
        //         this.submit();
        //     }
        // });
        // Verificar que el modal existe
        console.log('Modal existe:', document.getElementById('comprobanteModal') ? 'Sí' : 'No');
        console.log('Botones de impresión:', document.querySelectorAll('.btn-imprimir').length);
    });
</script>

@endsection