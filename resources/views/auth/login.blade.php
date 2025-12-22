<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href= {{asset("css/login.css") }}>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src={{asset('Assets/Home/Logogym.jpg')}} alt="Logo">
            </div>
            <h2>Iniciar Sesión</h2>
            <p>S U G U S</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group">
                    <input type="email" name="email" placeholder="Usuario" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Contraseña" required>
                </div>
                <div class="options">
                    <label><input type="checkbox"> Recordarme</label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">¿Olvidaste contraseña?</a>
                    @endif
                </div>
                <button class="btn-login" type="submit">Iniciar</button>
            </form>

            <div class="divider">
                <span>ó</span>
            </div>

            <button class="btn-google">
                Registrate
            </button>
        </div>
    </div>
</body>
</html>