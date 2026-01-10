@extends('layouts.plantilla')

@section('contenido')
    
<section class="dashboard" style="margin: 90px; padding: 5px;">
  <div class="stats">
    <div class="card rojo">
      <h3 style="color: white;">Ventas Día</h3>
      <p id="ventasDia" style="color: white">$0</p>
    </div>
    <div class="card azul">
      <h3 style="color: white;">Ventas Semana</h3>
      <p id="ventasSemana" style="color: white">$0</p>
    </div>
    <div class="card gris">
      <h3 style="color: white;">Ventas Mes</h3>
      <p id="ventasMes" style="color: white">$0</p>
    </div>
  </div>
  <div class="chart">
    <h3>Gráfica de Ventas</h3>
    <img src="Assets/grafica.webp" alt="Gráfico de Ventas" class="chart-img">
  </div>
</section>

@endsection

