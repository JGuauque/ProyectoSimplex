<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>La Casa del Nintendo - Dashboard</title>

  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  <!-- <link rel="stylesheet" href="css/dashboard.css"> -->
  <link rel="stylesheet" href="css/estilos-usuarios.css"><!-- estilos propios -->
  <link rel="stylesheet" href="{{ asset('css/estilos-inventario.css') }}"><!-- estilos inventario -->
  <link rel="stylesheet" href="{{ asset('css/estilos-ventas.css') }}">
  <link rel="stylesheet" href="{{ asset('css/estilos-clientes.css') }}">
  <link rel="stylesheet" href="{{ asset('css/estilos-turnos.css') }}">
  <link rel="stylesheet" href="{{ asset('css/estilos-prestamos.css') }}">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar">
    <div class="sidebar-header">
      <img src="{{ asset('Assets\lacasadelnintendo-removebg-preview.png') }}" alt="Logo" class="logo">
      <h2>La Casa del Nintendo</h2>
    </div>
    <nav class="sidebar-nav">
      
      @can('ver dashboard')
        <a href="{{ route('dashboard') }}" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
      @endcan
      
      @can('ver turnos')
        <a href="#"><i class="fa-solid fa-clock"></i> Turno</a>
      @endcan
      
      @can('ver usuarios')
        <a href="{{ route('usuarios.index') }}"><i class="fa-solid fa-user-gear"></i> Usuarios</a>
      @endcan
      
      @can('ver clientes')
        <a href="#"><i class="fa-solid fa-users"></i> Clientes</a>
      @endcan
      
      @can('ver inventario')
        <a href="{{ route('inventario.index') }}"><i class="fa-solid fa-boxes-stacked"></i> Inventario</a>
      @endcan
      
      @can('ver prestamos')
        <a href="#"><i class="fa-solid fa-handshake"></i> Préstamos</a>
      @endcan
      
      @can('ver ventas')
        <a href="#"><i class="fa-solid fa-cash-register"></i> Ventas</a>
      @endcan
      
    </nav>
  </aside>

  <!-- Header -->
  <header class="main-header">
    <button id="menuToggle" class="menu-btn">
      <img src="{{ asset('Assets/control.png') }}" alt="Menu" class="menu-icon">
    </button>

    <h1 id="headerTitle">@yield('titulo', 'Dashboard')</h1>

    <div class="user-info">
      <a href="{{route('profile.edit')}}">👤</a>
      <!-- <span id="usuarioActivo" href="{{route('profile.edit')}}" >👤}}</span> -->
      <form method="POST" action="{{ route('logout') }}">
        @csrf
      <button id="logoutBtn" type="submit" class="logout-btn">Salir</button>
      </form>
    </div>
  </header>

  <!-- Main Content -->
  <main id="mainContent">
    <!-- AQUI SE COLOCA TODOS LOS ELEMENTOS CAMBIANTES -->
      @yield('contenido')
  </main>

  <script src="{{ asset('js/app.js') }}"></script>
  <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
