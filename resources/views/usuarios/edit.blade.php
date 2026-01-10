@extends('layouts.plantilla')

@section('contenido')
<style>
/* Los mismos estilos que en index */
:root {
  --rojo: #e60012;
  --azul: #0f9bd7;
  --blanco: #ffffff;
  --negro: #111111;
  --radius: 12px;
}

.usuarios-main { padding: 30px; }

.formulario-usuarios {
  background: var(--blanco);
  padding: 20px;
  border-radius: var(--radius);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  margin-bottom: 30px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 12px;
}

.form-grid input, .form-grid select {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: var(--radius);
  font-size: 15px;
}

#nombres { grid-column: 1; grid-row: 1; }
#apellidos { grid-column: 1; grid-row: 2; }
#identificacion { grid-column: 2; grid-row: 1; }
#email { grid-column: 2; grid-row: 2; }
#usuario { grid-column: 3; grid-row: 1; }
#password { grid-column: 4; grid-row: 1; }
#password_confirmation { grid-column: 4; grid-row: 2; }
#rol { grid-column: 5; grid-row: 1; }
.btn-guardar { grid-column: 6; grid-row: 1; height: fit-content; align-self: center; }

.btn {
  padding: 8px 12px;
  border: none;
  border-radius: var(--radius);
  font-weight: bold;
  cursor: pointer;
  color: var(--blanco);
}
.btn-azul { background: var(--azul); }
.btn-rojo { background: var(--rojo); }

.alert {
  padding: 15px;
  margin-bottom: 20px;
  border-radius: var(--radius);
}
.alert-success { background: #d4edda; color: #155724; }
.alert-danger { background: #f8d7da; color: #721c24; }
</style>

<div class="usuarios-main">
    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario Editar -->
    <section class="formulario-usuarios">
        <h2>Editar Usuario: {{ $usuario->name }}</h2>
        <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <input type="text" id="nombres" name="name" placeholder="Nombres" required value="{{ old('name', $usuario->name) }}">
                <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos" value="{{ old('apellidos', $usuario->apellidos) }}">
                <input type="text" id="identificacion" name="identificacion" placeholder="ID" required value="{{ old('identificacion', $usuario->identificacion) }}">
                <input type="email" id="email" name="email" placeholder="Email" required value="{{ old('email', $usuario->email) }}">
                <input type="text" id="usuario" name="username" placeholder="Usuario" required value="{{ old('username', $usuario->username) }}">
                <input type="password" id="password" name="password" placeholder="Contraseña (dejar en blanco para no cambiar)">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirmar Contraseña">
                <select id="edit_rol" name="roles[]">

                        @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach

                        <!-- <option value="Owner">Owner</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Vendedor">Vendedor</option> -->
                    </select>
                <button type="submit" class="btn btn-azul btn-guardar">Actualizar Usuario</button>
            </div>
        </form>
        
        <div style="margin-top: 20px;">
            <a href="{{ route('usuarios.index') }}" class="btn btn-rojo">Cancelar y Volver</a>
        </div>
    </section>
</div>

<script>
// Validación de contraseñas para edición
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;
        
        // Solo validar si se está cambiando la contraseña
        if (password !== '' && password !== passwordConfirm) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            return false;
        }
        
        if (password !== '' && password.length < 8) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 8 caracteres');
            return false;
        }
    });
});
</script>
@endsection