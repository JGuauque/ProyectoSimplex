<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - La Casa del Nintendo</title>

    <!-- NUEVO OBJETO AGREGADO -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- CSS DEL LOGIN -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
    <!-- CAPA DE FONDO -->
    <div class="bg-layer">
        <video class="bg-video" autoplay muted loop playsinline
            poster="images/nintendo_foto2.jpg">
            <source src="media/Super Mario Bros. Plumbing Commercial.mp4" type="video/mp4" />
        </video>
        <div class="bg-overlay"></div>
    </div>

    <!-- CONTENIDO -->
    <header class="login-header">
        <h1><i class="fa-solid fa-gamepad"></i> La Casa del Nintendo</h1>
    </header>

    <main class="login-main">
        <div class="login-box">
            <h2 id="loginTitle">Iniciar Sesión</h2>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Campo oculto para detectar cambio de contraseña -->
                <input type="hidden" name="requires_password_change" id="requiresPasswordChange" value="0">

                <!-- Campo email -->
                <div class="input-group">
                    <input type="email" name="email" id="email" placeholder="Email" required
                        value="{{ old('email') ?? (session('reset_email') ?? '') }}">
                    <i class="fa-solid fa-envelope"></i>
                </div>

                <!-- Campo contraseña normal -->
                <div class="input-group" id="passwordGroup">
                    <input type="password" name="password" id="password" placeholder="Contraseña">
                    <i class="fa-solid fa-lock"></i>
                    <button type="button" class="toggle-password" id="togglePassword" data-target="password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <!-- Campos para nueva contraseña -->
                <div id="newPasswordFields" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle"></i> Debes establecer una nueva contraseña para continuar.
                    </div>

                    <div class="input-group">
                        <input type="password" name="new_password" id="newPassword" placeholder="Nueva contraseña" minlength="8">
                        <i class="fa-solid fa-key"></i>
                        <button type="button" class="toggle-password" id="toggleNewPassword" data-target="newPassword">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <div class="input-group">
                        <input type="password" name="new_password_confirmation" id="newPasswordConfirmation" placeholder="Confirmar nueva contraseña">
                        <i class="fa-solid fa-key"></i>
                        <button type="button" class="toggle-password" id="toggleNewPasswordConfirm" data-target="newPasswordConfirmation">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <div class="password-requirements">
                        La contraseña debe tener al menos 8 caracteres
                    </div>
                </div>

                <!-- Recordarme y olvidé contraseña -->
                <div class="options">
                    <!-- <label>
                        <input type="checkbox" name="remember"> Recordarme
                    </label> -->
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">¿Olvidaste contraseña?</a>
                    @endif
                </div>

                <!-- Botón principal -->
                <button type="submit" class="btn btn-azul" id="submitBtn">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span id="submitText">Entrar</span>
                </button>

                <!-- Botón para cancelar cambio de contraseña -->
                <button type="button" class="btn btn-rojo" id="cancelChangeBtn" style="display: none;">
                    <i class="fa-solid fa-times"></i> Cancelar cambio
                </button>
            </form>

            <!-- Mensajes de error -->
            @if($errors->any())
            <div class="alert alert-danger" style="margin-top: 15px;">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            @if(session('status'))
            <div class="alert alert-success" style="margin-top: 15px;">
                {{ session('status') }}
            </div>
            @endif

            <p id="loginError" class="error" aria-live="polite"></p>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Al principio del script, obtener el token desde Blade
            const csrfToken = "{{ csrf_token() }}";


            // Elementos del DOM
            const loginForm = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordGroup = document.getElementById('passwordGroup');
            const newPasswordFields = document.getElementById('newPasswordFields');
            const loginTitle = document.getElementById('loginTitle');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const cancelChangeBtn = document.getElementById('cancelChangeBtn');
            const requiresPasswordChange = document.getElementById('requiresPasswordChange');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            // Variables de estado
            let requiresChange = false;

            // Verificar si hay email en sesión de reset
            const resetEmail = "{{ session('reset_email') ?? '' }}";
            const resetRequired = "{{ session('requires_password_change') ?? '0' }}";



            if (resetEmail && resetRequired === '1') {
                activatePasswordChangeMode(resetEmail);
            }

            // Función para activar modo cambio de contraseña
            function activatePasswordChangeMode(email) {
                requiresChange = true;

                // Llenar email
                emailInput.value = email;
                emailInput.readOnly = true;
                emailInput.style.backgroundColor = 'rgba(255,255,255,0.15)';
                emailInput.style.color = 'rgba(255,255,255,0.8)';

                // Cambiar UI
                loginTitle.textContent = 'Cambiar Contraseña';
                submitText.textContent = 'Cambiar y Entrar';
                passwordGroup.style.display = 'none';
                newPasswordFields.style.display = 'block';
                cancelChangeBtn.style.display = 'block';
                requiresPasswordChange.value = '1';

                // Cambiar acción del formulario
                loginForm.action = "{{ route('login.with.password.change') }}";
            }

            // Función para desactivar modo cambio
            function deactivatePasswordChangeMode() {

                console.log('Activando modo cambio para:', email); // Para debug

                requiresChange = false;

                // Restaurar UI
                loginTitle.textContent = 'Iniciar Sesión';
                submitText.textContent = 'Entrar';
                emailInput.readOnly = false;
                emailInput.style.backgroundColor = '';
                passwordGroup.style.display = 'block';
                newPasswordFields.style.display = 'none';
                cancelChangeBtn.style.display = 'none';
                requiresPasswordChange.value = '0';

                // Limpiar campos
                document.getElementById('newPassword').value = '';
                document.getElementById('newPasswordConfirmation').value = '';
                passwordInput.value = '';
                emailInput.value = '';

                // Restaurar acción del formulario
                loginForm.action = "{{ route('login') }}";
            }

            // Event listeners
            cancelChangeBtn.addEventListener('click', deactivatePasswordChangeMode);

            // Validación del formulario
            loginForm.addEventListener('submit', function(e) {
                if (requiresChange) {
                    // Validar nueva contraseña
                    const newPassword = document.getElementById('newPassword').value;
                    const newPasswordConfirm = document.getElementById('newPasswordConfirmation').value;

                    if (newPassword.length < 8) {
                        e.preventDefault();
                        alert('La nueva contraseña debe tener al menos 8 caracteres');
                        return false;
                    }

                    if (newPassword !== newPasswordConfirm) {
                        e.preventDefault();
                        alert('Las contraseñas no coinciden');
                        return false;
                    }
                }
            });

            // Verificar automáticamente al cargar si el email ya está lleno
            if (emailInput.value && !requiresChange) {
                checkPasswordChangeStatus(emailInput.value);
            }

            // Verificar al escribir email
            emailInput.addEventListener('blur', function() {
                if (this.value && !requiresChange) {
                    checkPasswordChangeStatus(this.value);
                }
            });

            // // Mostrar/ocultar contraseña
            // if (togglePassword) {
            //     togglePassword.addEventListener('click', function() {
            //         const type = passwordInput.type === 'password' ? 'text' : 'password';
            //         passwordInput.type = type;
            //         this.querySelector('i').className = type === 'password' ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
            //     });
            // }

            // ===== FUNCIÓN PARA MOSTRAR/OCULTAR CONTRASEÑA =====
            function setupPasswordToggles() {
                // Obtener todos los botones de mostrar/ocultar
                const toggleButtons = document.querySelectorAll('.toggle-password');

                toggleButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        // Obtener el input objetivo desde data-target
                        const targetId = this.getAttribute('data-target');
                        const passwordInput = document.getElementById(targetId);

                        if (!passwordInput) return;

                        // Cambiar tipo de input
                        const type = passwordInput.type === 'password' ? 'text' : 'password';
                        passwordInput.type = type;

                        // Cambiar icono
                        const icon = this.querySelector('i');
                        if (icon) {
                            icon.className = type === 'password' ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
                        }
                    });
                });
            }

            // Inicializar los toggles de contraseña
            setupPasswordToggles();

            // Función para verificar estado de cambio de contraseña
            // Función para verificar estado de cambio de contraseña
            async function checkPasswordChangeStatus(email) {
                try {
                    console.log('Verificando email:', email);

                    // Obtener CSRF token de diferentes formas
                    let csrfToken = '';
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');

                    if (csrfMeta) {
                        csrfToken = csrfMeta.content;
                    } else {
                        // Intentar obtener del input hidden si existe
                        const csrfInput = document.querySelector('input[name="_token"]');
                        if (csrfInput) {
                            csrfToken = csrfInput.value;
                        } else {
                            console.warn('CSRF token no encontrado');
                            return;
                        }
                    }

                    const response = await fetch('/check-password-change', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            email: email
                        })
                    });

                    const data = await response.json();
                    console.log('Respuesta del servidor:', data);

                    if (data.requires_change) {
                        activatePasswordChangeMode(email);
                    }
                } catch (error) {
                    console.error('Error en la solicitud:', error);
                }

            }

        });
    </script>
</body>

</html>