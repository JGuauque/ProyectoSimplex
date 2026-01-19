@extends('layouts.plantilla')

@section('titulo', 'Gestión de Usuarios')

@section('contenido')

<style>
    .usuarios-main {
        padding: 30px;
    }

    /* FORM */

    .form-grid-2 {
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
    }

    .form-grid-2 input,
    .form-grid-2 select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: var(--radius);
        font-size: 15px;
    }

    #edit_nombres {
        grid-column: 1 / span 1;
        grid-row: 1;
    }

    #edit_apellidos {
        grid-column: 1 / span 1;
        grid-row: 2;
    }

    #edit_identificacion {
        grid-column: 2 / span 1;
        grid-row: 1;
    }

    #edit_email {
        grid-column: 2 / span 1;
        grid-row: 2;
    }

    #edit_usuario {
        grid-column: 3 / span 1;
        grid-row: 1;
    }

    #edit_password {
        grid-column: 4 / span 1;
        grid-row: 1;
    }

    #edit_password_confirmation {
        grid-column: 4 / span 1;
        grid-row: 2;
    }

    #edit_rol {
        grid-column: 5 / span 1;
        grid-row: 1;
    }

    /* BOTONES DEL MODAL - CORREGIDOS */
    .btn-guardar-modal {
        grid-column: 6 / span 1;
        grid-row: 1;
        height: fit-content;
        align-self: center;
        padding: 10px;
    }

    .btn-cancelar-modal {
        grid-column: 6 / span 1;
        grid-row: 2;
        height: fit-content;
        align-self: center;
        padding: 10px;
    }

    /* NUEVOS ESTILOS PARA BOTONES */
    .btn-verde {
        background: #28a745;
        color: white;
    }

    .btn-verde:hover {
        background: #218838;
    }

    /* Ajustar espacio entre botones */
    td .btn {
        margin: 2px;
        padding: 6px 10px;
        font-size: 13px;
    }


    /* MODAL */
    .modal-editar-cliente {
        padding: 80px;
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content-editar-cliente {
        background-color: var(--blanco);
        margin: 2% auto;
        padding: 0;
        border-radius: var(--radius);
        width: 95%;
        max-width: 1500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: modalAppear 0.3s ease;
    }

    @keyframes modalAppear {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header-editar-cliente {
        background: var(--azul);
        color: white;
        padding: 15px 20px;
        border-radius: var(--radius) var(--radius) 0 0;
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .modal-header-editar-cliente h2 {
        margin: 0;
        flex: 1;
        font-size: 1.4rem;
    }

    .modal-editar-cliente-body {
        padding: 20px;
    }
</style>

<div class="usuarios-main">

    <!-- Formulario Crear -->
    <section class="formulario-usuarios">
        <strong>
            <h2 style="font-size: 24px; margin-bottom: 15px;">Crear Nuevo Usuario</h2>
        </strong>
        <form method="POST" action="{{ route('usuarios.store') }}" id="formCrearUsuario">
            @csrf
            <div class="form-grid">
                <input type="text" id="nombres" name="name" placeholder="Nombres *" required value="{{ old('name') }}">
                <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos *" required value="{{ old('apellidos') }}">
                <input type="text" id="identificacion" name="identificacion" placeholder="Documento *" required value="{{ old('identificacion') }}">
                <input type="email" id="email" name="email" placeholder="Email *" required value="{{ old('email') }}">
                <input type="text" id="usuario" name="username" placeholder="Usuario *" required value="{{ old('username') }}">
                <input type="password" id="password" name="password" placeholder="Contraseña *" required>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirmar Contraseña *" required>

                <select id="rol" name="roles[]">

                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('roles') && in_array($role->name, old('roles')) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach

                    <!-- <option value="Owner" {{ old('rol') == 'Owner' ? 'selected' : '' }}>Owner</option>
                    <option value="Administrador" {{ old('rol') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="Vendedor" {{ old('rol') == 'Vendedor' ? 'selected' : '' }}>Vendedor</option> -->
                </select>

                <button type="submit" class="btn btn-azul btn-guardar">Guardar</button>
            </div>
        </form>
    </section>

    <!-- Tabla de Usuarios -->
    <section class="listado-section" style="margin: 70px; margin-top:10px; padding: 20px;">
        <strong>
            <h2 style="font-size: 24px; margin-bottom:0px">Usuarios Registrados</h2>
        </strong>
        <div class="tabla-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->apellidos ?? '-' }}</td>
                        <td>{{ $usuario->identificacion }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->username }}</td>
                        <!-- <td>{{ $usuario->rol }}</td> -->

                        <td>
                            @foreach($usuario->roles as $role)
                            <span class="badge" style="color:white; background:#2196f3;">{{ $role->name }}</span>
                            @endforeach
                        </td>

                        <td>
                            <button class="btn btn-azul" onclick="abrirModalEditar(<?php echo $usuario->id; ?>)">Editar</button>

                            <!-- NUEVOS BOTONES -->
                            <!-- Botón Resetear Contraseña (solo mostrar si tiene permiso) -->

                            <!-- <button class="btn btn-verde" onclick="resetearContrasena(<?php echo $usuario->id; ?>, '<?php echo $usuario->email; ?>')">
                                Resetear Contraseña
                            </button> -->

                            @if(auth()->user()->can('resetear contraseña'))
                            @php
                            $userAutenticado = auth()->user();
                            $mostrarBotonReset = true;

                            // Lógica para ocultar botón según reglas
                            if ($userAutenticado->hasRole('Administrador') && $usuario->hasRole('Administrador') && $userAutenticado->id !== $usuario->id) {
                            $mostrarBotonReset = false;
                            }

                            if ($userAutenticado->hasRole('Vendedor') && $userAutenticado->id !== $usuario->id) {
                            $mostrarBotonReset = false;
                            }
                            @endphp

                            @if($mostrarBotonReset)
                            <button class="btn btn-verde" onclick="resetearContrasena({{ $usuario->id }}, '{{ $usuario->email }}')">
                                Resetear Contraseña
                            </button>
                            @endif
                            @endif

                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-rojo" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Modal para Editar Usuario -->
<div id="modalEditar" class="modal-editar-cliente">
    <div class="modal-content-editar-cliente">
        <div class="modal-header-editar-cliente">
            <strong>
                <h2 style="font-size: 24px;">Editar Usuario</h2>
            </strong>

            <!-- <button class="close-modal" onclick="cerrarModalEditar()">&times;</button> -->
        </div>
        <div class="modal-body">
            <form method="POST" id="formEditarUsuario">
                @csrf
                @method('PUT')
                <div class="form-grid-2">
                    <input type="text" id="edit_nombres" name="name" placeholder="Nombres" required>
                    <input type="text" id="edit_apellidos" name="apellidos" placeholder="Apellidos">
                    <input type="text" id="edit_identificacion" name="identificacion" placeholder="ID" required>
                    <input type="email" id="edit_email" name="email" placeholder="Email" required>
                    <input type="text" id="edit_usuario" name="username" placeholder="Usuario" required>
                    <input type="password" id="edit_password" name="password" placeholder="Contraseña (dejar en blanco para no cambiar)">
                    <input type="password" id="edit_password_confirmation" name="password_confirmation" placeholder="Confirmar Contraseña">
                    <select id="edit_rol" name="roles[]">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}"
                            @if(isset($usuario) && $usuario->hasRole($role->name)) selected @endif>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                    <!-- <select id="edit_rol" name="roles[]">

                        @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select> -->
                    <button type="submit" class="btn btn-azul btn-guardar-modal">Actualizar</button>

                    <button type="button" class="btn btn-rojo btn-cancelar-modal" onclick="cerrarModalEditar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Modal functions
    function abrirModalEditar(usuarioId) {
        // Obtener datos del usuario via AJAX
        fetch(`/usuarios/${usuarioId}/get-data`)
            .then(response => response.json())
            .then(usuario => {
                // Llenar el formulario con los datos del usuario
                document.getElementById('edit_nombres').value = usuario.name;
                document.getElementById('edit_apellidos').value = usuario.apellidos || '';
                document.getElementById('edit_identificacion').value = usuario.identificacion;
                document.getElementById('edit_email').value = usuario.email;
                document.getElementById('edit_usuario').value = usuario.username;
                // Obtener el primer rol del usuario (si tiene)
                const rolSelect = document.getElementById('edit_rol');
                if (usuario.roles && usuario.roles.length > 0) {
                    // Seleccionar el rol actual del usuario
                    const userRoleName = usuario.roles[0].name;

                    // Buscar la opción que coincida con el rol del usuario
                    for (let i = 0; i < rolSelect.options.length; i++) {
                        if (rolSelect.options[i].value === userRoleName) {
                            rolSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
                // document.getElementById('edit_rol').value = usuario.rol;

                // Actualizar la acción del formulario
                document.getElementById('formEditarUsuario').action = `/usuarios/${usuarioId}`;

                // Mostrar el modal
                document.getElementById('modalEditar').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del usuario');
            });
    }

    function cerrarModalEditar() {
        document.getElementById('modalEditar').style.display = 'none';
        // Limpiar formulario al cerrar
        document.getElementById('formEditarUsuario').reset();
    }

    // Cerrar modal al hacer clic fuera del contenido
    window.onclick = function(event) {
        const modal = document.getElementById('modalEditar');
        if (event.target === modal) {
            cerrarModalEditar();
        }
    }

    // Cerrar modal con tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalEditar();
        }
    });

    // Validación de contraseñas para crear usuario
    document.getElementById('formCrearUsuario').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;

        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 8 caracteres');
            return false;
        }
    });

    // Validación de contraseñas para editar usuario
    document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
        const password = document.getElementById('edit_password').value;
        const passwordConfirm = document.getElementById('edit_password_confirmation').value;

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
</script>

<script>
    // Asegurar que las funciones sean globales
    window.resetearContrasena = async function(usuarioId, email) {
        if (!confirm(`¿Desea resetear la contraseña para ${email}?\n\nEl usuario deberá establecer una nueva contraseña al siguiente inicio de sesión.`)) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`/usuarios/${usuarioId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                alert('✅ ' + data.message + '\n\nEl usuario ' + email + ' deberá establecer una nueva contraseña al iniciar sesión.');
            } else {
                alert('❌ Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error al resetear la contraseña');
        }
    };
</script>

@endsection