<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - La Casa del Nintendo</title>

    <!-- NUEVO OBJETO AGREGADO -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- CSS DEL LOGIN -->
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}" />
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
            <h2 id="loginTitle">Recuperación de contraseña</h2>

            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400" style="margin-bottom: 10px;">
                {{ __('¿Olvidaste tu contraseña? No hay problema. Simplemente indícanos tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña y podrás elegir una nueva.') }}
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div style="margin-top: 20px;">
                    <x-input-label for="email" :value="__('Correo Electronico')"/>
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus style="margin-top: 5px;"/>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button style="width: 100%; background: linear-gradient(90deg, var(--azul) 0%, #11a6e6 100%); color: white; border: 1px solid rgba(255,255,255,0.25); border-radius: 10px; height: 40px;">
                        {{ __('Email Password Reset Link') }}
                    </x-primary-button>
                </div>
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

</body>

</html>

