@extends('layouts.plantilla')

@section('contenido')
<section class="dashboard" style="margin: 90px; padding: 5px;">
  <div class="stats">
    <div class="card rojo">
      <h3 style="color: white;">Ventas Día</h3>
      <p id="ventasDia" style="color: white; font-size: 24px; font-weight: bold;">
        ${{ isset($ventasDia) ? number_format($ventasDia, 0, ',', '.') : '0' }}
      </p>
      <small style="color: white;">{{ now()->format('d/m/Y') }}</small>
    </div>
    <div class="card azul">
      <h3 style="color: white;">Ventas Semana</h3>
      <p id="ventasSemana" style="color: white; font-size: 24px; font-weight: bold;">
        ${{ isset($ventasSemana) ? number_format($ventasSemana, 0, ',', '.') : '0' }}
      </p>
      <small style="color: white;">Semana {{ now()->weekOfYear }}</small>
    </div>
    <div class="card gris">
      <h3 style="color: white;">Ventas Mes</h3>
      <p id="ventasMes" style="color: white; font-size: 24px; font-weight: bold;">
        ${{ isset($ventasMes) ? number_format($ventasMes, 0, ',', '.') : '0' }}
      </p>
      <small style="color: white;">{{ now()->format('F Y') }}</small>
    </div>
  </div>
  
  @php
    // Asegurar que $categorias exista
    $categorias = $categorias ?? ['Tecnologia', 'Hogar', 'Jugueteria', 'Salud', 'Cocina'];
  @endphp
  
  <div class="chart-container" style="margin-top: 30px; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    <div class="chart-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #e5e7eb; padding-bottom: 15px;">
      <div>
        <h3 style="margin: 0; font-size: 20px; font-weight: 600; color: #111827;">Ventas por Categoría</h3>
        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">
          Mostrando ventas totales por categoría de productos
        </p>
      </div>
      <div style="display: flex; gap: 15px; align-items: center;">
        <div class="filter-container" style="display: flex; gap: 10px;">
          <select id="rangoSelector" class="form-control" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; background: white; color: #374151; font-size: 14px; cursor: pointer; min-width: 140px;">
            <option value="7d">Últimos 7 días</option>
            <option value="30d">Últimos 30 días</option>
            <option value="90d">Últimos 3 meses</option>
          </select>
        </div>
      </div>
    </div>
    
    <div class="categorias-filtro" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 10px;">
      @foreach($categorias as $categoria)
      <label class="categoria-checkbox" style="display: flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f3f4f6; border-radius: 20px; cursor: pointer; user-select: none; transition: all 0.2s;">
        <input type="checkbox" name="categorias[]" value="{{ $categoria }}" checked class="categoria-check" style="cursor: pointer;">
        <span style="font-size: 14px; color: #374151;">{{ $categoria }}</span>
      </label>
      @endforeach
    </div>
    
    <div class="chart-wrapper" style="position: relative; height: 400px;">
      <canvas id="ventasCategoriaChart"></canvas>
    </div>
    
    <div class="chart-footer" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: center;">
      <div class="legend" id="chartLegend" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;"></div>
    </div>
  </div>
</section>

<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('ventasCategoriaChart').getContext('2d');
    let ventasChart;
    const coloresCategorias = [
        '#3b82f6', // Azul - Tecnologia
        '#10b981', // Verde - Hogar
        '#f59e0b', // Amarillo - Jugueteria
        '#ef4444', // Rojo - Salud
        '#8b5cf6', // Violeta - Cocina
        '#ec4899', // Rosa
        '#14b8a6', // Turquesa
        '#f97316', // Naranja
    ];
    
    // Elementos del DOM
    const rangoSelector = document.getElementById('rangoSelector');
    const categoriasCheckboxes = document.querySelectorAll('.categoria-check');
    const chartLegend = document.getElementById('chartLegend');
    
    // Estado de la aplicación
    let estado = {
        rango: '7d',
        categoriasSeleccionadas: Array.from(categoriasCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value)
    };
    
    // Mostrar/ocultar loading
    function mostrarLoading(mostrar) {
        let overlay = document.querySelector('.loading-overlay');
        if (!overlay && mostrar) {
            overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = '<div class="spinner"></div>';
            document.querySelector('.chart-wrapper').appendChild(overlay);
        } else if (overlay && !mostrar) {
            overlay.remove();
        }
    }
    
    // Actualizar leyenda
    function actualizarLeyenda(categorias) {
        chartLegend.innerHTML = '';
        categorias.forEach((categoria, index) => {
            const color = coloresCategorias[index % coloresCategorias.length];
            const item = document.createElement('div');
            item.className = 'legend-item';
            item.style.display = 'flex';
            item.style.alignItems = 'center';
            item.style.gap = '8px';
            
            item.innerHTML = `
                <div style="width: 16px; height: 16px; background: ${color}; border-radius: 3px;"></div>
                <span style="font-size: 14px; color: #374151;">${categoria}</span>
            `;
            
            chartLegend.appendChild(item);
        });
    }
    
    // Formatear fecha
    function formatearFecha(fechaStr) {
        const fecha = new Date(fechaStr);
        return fecha.toLocaleDateString('es-CO', {
            month: 'short',
            day: 'numeric'
        });
    }
    
    // Cargar datos de la gráfica
    async function cargarDatosGrafica() {
        mostrarLoading(true);
        
        try {
            // Convertir array a string separado por comas
            const categoriasStr = estado.categoriasSeleccionadas.join(',');
            
            const params = new URLSearchParams({
                rango: estado.rango,
                categorias: categoriasStr
            });
            
            const response = await fetch(`/dashboard/ventas-categoria?${params}`);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            renderizarGrafica(data.data, data.categorias);
            actualizarLeyenda(data.categorias);
        } catch (error) {
            console.error('Error cargando datos:', error);
            alert('Error al cargar los datos de la gráfica: ' + error.message);
        } finally {
            mostrarLoading(false);
        }
    }
    
    // Renderizar gráfica
    function renderizarGrafica(datos, categorias) {
        // Destruir gráfica existente
        if (ventasChart) {
            ventasChart.destroy();
        }
        
        // Preparar labels (fechas)
        const labels = datos.map(item => item.fecha);
        
        // Preparar datasets para cada categoría
        const datasets = categorias.map((categoria, index) => {
            const color = coloresCategorias[index % coloresCategorias.length];
            
            return {
                label: categoria,
                data: datos.map(item => item[categoria] || 0),
                borderColor: color,
                backgroundColor: color + '20', // Añadir transparencia
                fill: true,
                tension: 0.4,
                pointBackgroundColor: color,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            };
        });
        
        // Crear nueva gráfica
        ventasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'white',
                        titleColor: '#111827',
                        bodyColor: '#374151',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            title: function(tooltipItems) {
                                return formatearFecha(tooltipItems[0].label);
                            },
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.y;
                                return `${label}: $${value.toLocaleString('es-CO')}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280',
                            maxRotation: 45,
                            callback: function(value) {
                                return formatearFecha(this.getLabelForValue(value));
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            color: '#6b7280',
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-CO');
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Event Listeners
    rangoSelector.addEventListener('change', function(e) {
        estado.rango = e.target.value;
        cargarDatosGrafica();
    });
    
    categoriasCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            estado.categoriasSeleccionadas = Array.from(categoriasCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            cargarDatosGrafica();
        });
    });
    
    // Cargar datos iniciales
    cargarDatosGrafica();
});
</script>

<style>
.categoria-checkbox:hover {
  background: #e5e7eb;
  transform: translateY(-1px);
}

.categoria-checkbox input[type="checkbox"]:checked + span {
  font-weight: 600;
  color: #111827;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10;
  border-radius: 12px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Estilos para el dashboard */
.dashboard .stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.dashboard .card {
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s;
}

.dashboard .card:hover {
    transform: translateY(-5px);
}

.card.rojo {
    background: linear-gradient(135deg, #ff6b6b, #e60012);
}

.card.azul {
    background: linear-gradient(135deg, #4eadcdff, #0f6ab4);
}

.card.gris {
    background: linear-gradient(135deg, #666e76, #444);
}
</style>
@endsection