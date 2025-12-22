@extends('layouts.plantilla')

@section('contenido')
    
<div class="cards">
    <div class="card">
      <div class="card-content">
        <div class="number">{{ $totalClientes }}</div>
        <div class="card-name">Clientes</div>
      </div>
      <div class="icon-box">
        <i class="fas fa-users"></i>
      </div>
    </div>
    <div class="card">
      <div class="card-content">
        <div class="number">{{ $totalInstructores }}</div>
        <div class="card-name">Instructores</div>
      </div>
      <div class="icon-box">
        <i class="fas fa-chalkboard-teacher"></i>
      </div>
    </div>
    <div class="card">
      <div class="card-content">
        <div class="number">{{ $totalUsuarios }}</div>
        <div class="card-name">Usuarios</div>
      </div>
      <div class="icon-box">
        <i class="fas fa-user"></i>
      </div>
    </div>
    <div class="card">
      <div class="card-content">
        <div class="number">{{ $totalUsuarios }}</div>
        <div class="card-name">Proveedores</div>
      </div>
      <div class="icon-box">
        <i class="fas fa-truck"></i>
      </div>
    </div>
  </div>
  <div class="charts">
    <div class="chart">
      <h2>Ganancias (Pasados 12 meses)</h2>
      <div>
        <canvas id="lineChart"></canvas>
      </div>
    </div>
    <div class="chart" id="doughnut-chart">
      <h2>Datos</h2>
      <div>
        <canvas id="doughnut"></canvas>
      </div>
    </div>
  </div>
</div>
@endsection

