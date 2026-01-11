@extends('layouts.plantilla')

@section('titulo', 'Gestión de Turnos')

@section('contenido')


<!-- Estado del turno -->
<section class="estado-turno" id="estadoTurno">
    <strong>
        <h2 style="font-size: 24px;">Turno Activo</h2>
    </strong>

    <div id="turnoInfo">

        <p>No hay turno activo</p>

    </div>
    <button onclick="abrirTurno()" class="btn btn-azul" {{ $turnoActivo ? 'disabled' : '' }}>Abrir Turno</button>
    <form id="formCerrarTurno" action="{{ route('turnos.cerrar') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-rojo" {{ !$turnoActivo ? 'disabled' : '' }}>Cerrar Turno</button>
    </form>
</section>

<!-- Estado del turno -->
<!-- <section class="estado-turno" id="estadoTurno">
    <h2>Turno Activo</h2>
    <div id="turnoInfo">
        @if($turnoActivo)
        <p><strong>Inicio:</strong> {{ $turnoActivo->inicio->format('d/m/Y H:i:s') }}</p>
        <div class="stats">
            <div class="card">
                <h3>Base</h3>
                <p>${{ number_format($turnoActivo->base, 2) }}</p>
            </div>
            <div class="card">
                <h3>Ventas Totales</h3>
                <p>${{ number_format($turnoActivo->ventas_totales, 2) }}</p>
            </div>
            <div class="card">
                <h3>Efectivo</h3>
                <p>${{ number_format($turnoActivo->efectivo, 2) }}</p>
            </div>
            <div class="card">
                <h3>Transferencia</h3>
                <p>${{ number_format($turnoActivo->transferencia, 2) }}</p>
            </div>
        </div>
        @else
        <p>No hay turno activo</p>
        @endif
    </div>
    <button onclick="abrirTurno()" class="btn btn-azul" {{ $turnoActivo ? 'disabled' : '' }}>Abrir Turno</button>
    <form id="formCerrarTurno" action="{{ route('turnos.cerrar') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-rojo" {{ !$turnoActivo ? 'disabled' : '' }}>Cerrar Turno</button>
    </form>
</section> -->

<!-- Historial -->
<section class="historial" style="margin: 70px; margin-top: 0px; padding: 30px;">
    <strong>
        <h2 style="font-size: 24px;">Historial de Turnos</h2>
    </strong>

    <div class="filtros">
        <input type="date" id="filtroFecha">
        <button onclick="filtrarTurnos()" class="btn btn-azul">Filtrar</button>
        <button onclick="resetearFiltro()" class="btn btn-rojo">Resetear</button>
    </div>
    <div class="tabla-container">
        <table>
            <thead>
                <tr>
                    <th>Inicio</th>
                    <th>Cierre</th>
                    <th>Base</th>
                    <th>Ventas Totales</th>
                    <th>Efectivo</th>
                    <th>Transferencia</th>
                </tr>
            </thead>
            <tbody id="tablaHistorial">
                @foreach($historialTurnos as $turno)
                <tr>
                    <td>{{ $turno->inicio->format('d/m/Y H:i') }}</td>
                    <td>{{ $turno->cierre->format('d/m/Y H:i') }}</td>
                    <td>${{ number_format($turno->base, 2) }}</td>
                    <td>${{ number_format($turno->ventas_totales, 2) }}</td>
                    <td>${{ number_format($turno->efectivo, 2) }}</td>
                    <td>${{ number_format($turno->transferencia, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $historialTurnos->links() }}
    </div>
</section>



<!-- Modal Base -->
<div id="modalBase" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Ingresar base inicial</h3>
        <form id="formAbrirTurno" action="{{ route('turnos.abrir') }}" method="POST">
            @csrf
            <input type="number" id="inputBase" name="base" placeholder="Ingrese la base" min="0" step="0.01" required>
            <div class="modal-actions">
                <button type="submit" class="btn btn-azul">Aceptar</button>
                <button type="button" id="btnCancelarBase" class="btn btn-rojo">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // ========== FUNCIONES DEL MODAL ==========
    function abrirTurno() {
        document.getElementById('modalBase').style.display = 'block';
    }

    function cerrarTurno() {
        if (confirm('¿Estás seguro de que deseas cerrar el turno?')) {
            document.getElementById('formCerrarTurno').submit();
        }
    }

    document.getElementById('btnCancelarBase').addEventListener('click', function() {
        document.getElementById('modalBase').style.display = 'none';
    });

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modalBase');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // ========== FUNCIONES DEL HISTORIAL ==========
    async function filtrarTurnos() {
        const fecha = document.getElementById('filtroFecha').value;

        try {
            const response = await fetch(`{{ route('turnos.historial') }}?fecha=${fecha}`);
            const turnos = await response.json();

            const tbody = document.getElementById('tablaHistorial');
            tbody.innerHTML = '';

            turnos.forEach(turno => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${new Date(turno.inicio).toLocaleString()}</td>
                    <td>${new Date(turno.cierre).toLocaleString()}</td>
                    <td>$${parseFloat(turno.base).toFixed(2)}</td>
                    <td>$${parseFloat(turno.ventas_totales).toFixed(2)}</td>
                    <td>$${parseFloat(turno.efectivo).toFixed(2)}</td>
                    <td>$${parseFloat(turno.transferencia).toFixed(2)}</td>
                `;
                tbody.appendChild(row);
            });
        } catch (error) {
            console.error('Error al filtrar turnos:', error);
        }
    }

    function resetearFiltro() {
        document.getElementById('filtroFecha').value = '';
        location.reload();
    }

    // ========== ACTUALIZACIÓN EN TIEMPO REAL ==========
    function actualizarEstadoTurno() {
        fetch('{{ route("turnos.estado") }}')
            .then(response => response.json())
            .then(data => {
                const turnoInfo = document.getElementById('turnoInfo');
                const btnAbrir = document.querySelector('.btn-azul');
                const btnCerrar = document.querySelector('.btn-rojo');

                if (data.turnoActivo) {
                    // Asegurar que siempre muestre valores (incluso 0.00)
                    const base = parseFloat(data.turnoActivo.base) || 0;
                    const ventasTotales = parseFloat(data.turnoActivo.ventas_totales) || 0;
                    const efectivo = parseFloat(data.turnoActivo.efectivo) || 0;
                    const transferencia = parseFloat(data.turnoActivo.transferencia) || 0;

                    turnoInfo.innerHTML = `
                    <p><strong>Inicio:</strong> ${new Date(data.turnoActivo.inicio).toLocaleString()}</p>
                    <div class="stats">
                        <div class="card"><h3>Base</h3><p>$${base.toFixed(2)}</p></div>
                        <div class="card"><h3>Ventas Totales</h3><p>$${ventasTotales.toFixed(2)}</p></div>
                        <div class="card"><h3>Efectivo</h3><p>$${efectivo.toFixed(2)}</p></div>
                        <div class="card"><h3>Transferencia</h3><p>$${transferencia.toFixed(2)}</p></div>
                    </div>
                `;

                    btnAbrir.disabled = true;
                    btnCerrar.disabled = false;
                } else {
                    turnoInfo.innerHTML = `<p>No hay turno activo</p>`;
                    btnAbrir.disabled = false;
                    btnCerrar.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Mostrar error al usuario
                document.getElementById('turnoInfo').innerHTML = '<p>Error al cargar estado del turno</p>';
            });
    }

    // ========== INICIALIZACIÓN ==========
    document.addEventListener('DOMContentLoaded', function() {
        // Iniciar actualización en tiempo real
        actualizarEstadoTurno();

        // Actualizar cada 5 segundos
        setInterval(actualizarEstadoTurno, 3000);

        // Actualizar después de cerrar turno
        document.getElementById('formCerrarTurno')?.addEventListener('submit', function() {
            setTimeout(actualizarEstadoTurno, 2000);
        });
    });
</script>
<!-- Agrega este script al final de tu vista -->


@endsection