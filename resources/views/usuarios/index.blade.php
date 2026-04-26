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
                <input
                    type="text" id="nombres" name="name" placeholder="Nombres *" required value="{{ old('name') }}"
                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                    title="Solo se permiten letras y espacios"
                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')">
                <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos *" required value="{{ old('apellidos') }}"
                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                    title="Solo se permiten letras y espacios"
                    oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')">
                <input type="text" id="identificacion" name="identificacion" placeholder="Documento *" required value="{{ old('identificacion') }}"
                    pattern="[0-9]+"
                    title="Solo se permiten números"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    maxlength="20" >
                <input type="email" id="email" name="email" placeholder="Email *" required value="{{ old('email') }}">
                <input type="text" id="usuario" name="username" placeholder="Usuario *" required value="{{ old('username') }}"
                    onblur="limpiarUsername()"
                    oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '')"
                    style="background-color: #f8f9fa;" readonly>
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
            {{ $usuarios->links() }}
        </div>
    </section>
</div>

<!-- Modal para      -->
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
                    <input type="text" id="edit_nombres" name="name" placeholder="Nombres" required
                        pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                        title="Solo se permiten letras y espacios"
                        oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')">
                    <input type="text" id="edit_apellidos" name="apellidos" placeholder="Apellidos"
                        pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                        title="Solo se permiten letras y espacios"
                        oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')">
                    <input type="text" id="edit_identificacion" name="identificacion" placeholder="ID" required
                        pattern="[0-9]+"
                        title="Solo se permiten números"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        maxlength="20">
                    <input type="email" id="edit_email" name="email" placeholder="Email" required>
                    <input type="text" id="edit_usuario" name="username" placeholder="Usuario" required readonly>
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

                    <button type="submit" class="btn btn-azul btn-guardar-modal">Actualizar</button>

                    <button type="button" class="btn btn-rojo btn-cancelar-modal" onclick="cerrarModalEditar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Función para generar username automáticamente
    function generarUsername() {
        const nombres = document.getElementById('nombres').value.trim();
        const apellidos = document.getElementById('apellidos').value.trim();
        const usernameInput = document.getElementById('usuario');

        if (nombres && apellidos) {
            // Obtener la primera letra del nombre
            const inicialNombre = nombres.charAt(0).toLowerCase();
            // Obtener el apellido completo en minúsculas y sin espacios
            const apellidoLimpio = apellidos.toLowerCase().replace(/\s/g, '');
            // Generar username
            const usernameGenerado = inicialNombre + apellidoLimpio;

            // Asignar el username generado
            usernameInput.value = usernameGenerado;

            // Opcional: Agregar un indicador visual de que se generó automáticamente
            usernameInput.style.backgroundColor = '#e8f0fe';

            // Opcional: Mostrar un tooltip o mensaje
            usernameInput.title = 'Username generado automáticamente';
        } else {
            // Si falta nombre o apellido, limpiar el username
            usernameInput.value = '';
            usernameInput.style.backgroundColor = '#f8f9fa';
        }
    }

    // Función para validar que el username no tenga espacios ni caracteres especiales
    function validarUsername(username) {
        // Solo permitir letras minúsculas, números y guión bajo
        const regex = /^[a-z0-9_]+$/;
        return regex.test(username);
    }

    // Función para limpiar el username (solo letras minúsculas, números y guión bajo)
    function limpiarUsername() {
        const usernameInput = document.getElementById('usuario');
        let username = usernameInput.value.toLowerCase();
        // Reemplazar espacios por guión bajo
        username = username.replace(/\s/g, '_');
        // Eliminar caracteres especiales (solo letras, números y guión bajo)
        username = username.replace(/[^a-z0-9_]/g, '');
        usernameInput.value = username;
    }
    
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
    // Función para generar username en el modal de edición
    function generarUsernameEdicion() {
        const nombres = document.getElementById('edit_nombres').value.trim();
        const apellidos = document.getElementById('edit_apellidos').value.trim();
        const usernameInput = document.getElementById('edit_usuario');

        if (nombres && apellidos) {
            const inicialNombre = nombres.charAt(0).toLowerCase();
            const apellidoLimpio = apellidos.toLowerCase().replace(/\s/g, '');
            const usernameGenerado = inicialNombre + apellidoLimpio;

            // Solo actualizar si el campo está vacío o si el usuario no lo ha modificado manualmente
            if (!usernameInput.dataset.manual || usernameInput.dataset.manual === 'false') {
                usernameInput.value = usernameGenerado;
            }
        }
    }

    // Modificar la función abrirModalEditar para agregar los event listeners
    function abrirModalEditar(usuarioId) {
        fetch(`/usuarios/${usuarioId}/get-data`)
            .then(response => response.json())
            .then(usuario => {
                document.getElementById('edit_nombres').value = usuario.name;
                document.getElementById('edit_apellidos').value = usuario.apellidos || '';
                document.getElementById('edit_identificacion').value = usuario.identificacion;
                document.getElementById('edit_email').value = usuario.email;
                document.getElementById('edit_usuario').value = usuario.username;

                // Agregar event listeners para el modal de edición
                const editNombres = document.getElementById('edit_nombres');
                const editApellidos = document.getElementById('edit_apellidos');
                const editUsername = document.getElementById('edit_usuario');

                // Marcar que el username no ha sido editado manualmente
                editUsername.dataset.manual = 'false';

                editNombres.addEventListener('input', function() {
                    generarUsernameEdicion();
                });
                editApellidos.addEventListener('input', function() {
                    generarUsernameEdicion();
                });

                // Cuando el usuario edite manualmente el username
                editUsername.addEventListener('input', function() {
                    this.dataset.manual = 'true';
                    // Limpiar el username
                    let username = this.value.toLowerCase();
                    username = username.replace(/\s/g, '_');
                    username = username.replace(/[^a-z0-9_]/g, '');
                    this.value = username;
                });

                // Seleccionar el rol
                const rolSelect = document.getElementById('edit_rol');
                if (usuario.roles && usuario.roles.length > 0) {
                    const userRoleName = usuario.roles[0].name;
                    for (let i = 0; i < rolSelect.options.length; i++) {
                        if (rolSelect.options[i].value === userRoleName) {
                            rolSelect.selectedIndex = i;
                            break;
                        }
                    }
                }

                document.getElementById('formEditarUsuario').action = `/usuarios/${usuarioId}`;
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
    // Agregar event listeners para generar username automáticamente
    document.addEventListener('DOMContentLoaded', function() {
        const nombresInput = document.getElementById('nombres');
        const apellidosInput = document.getElementById('apellidos');
        const usernameInput = document.getElementById('usuario');

        if (nombresInput && apellidosInput) {
            // Generar username cuando se escriba en nombre o apellido
            nombresInput.addEventListener('input', generarUsername);
            apellidosInput.addEventListener('input', generarUsername);

            // También generar cuando se pierda el foco (por si acaso)
            nombresInput.addEventListener('blur', generarUsername);
            apellidosInput.addEventListener('blur', generarUsername);
        }

        // Si el usuario edita manualmente el username, cambiar el color de fondo
        if (usernameInput) {
            usernameInput.addEventListener('focus', function() {
                this.style.backgroundColor = '#fff';
                this.title = 'Puedes editar el username manualmente';
            });

            // Validar el username al perder el foco
            usernameInput.addEventListener('blur', function() {
                limpiarUsername();
                if (!validarUsername(this.value) && this.value !== '') {
                    alert('El username solo puede contener letras minúsculas, números y guión bajo');
                    this.focus();
                }
            });
        }
    });
</script>

<script>
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