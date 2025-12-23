<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    // public function create(): View
    // {
    //     return view('auth.login');
    // }

    // Mostrar formulario de login
    public function create()
    {
        // return view('auth.login', [
        //     'reset_email' => Session::get('reset_email'),
        //     'requires_password_change' => Session::get('requires_password_change')
        // ]);
        return view('auth.login', [
            'reset_email' => session('reset_email'),
            'requires_password_change' => session('requires_password_change')
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Verificar si el usuario requiere cambio de contraseña
        if ($user->must_change_password) {
            // Guardar en sesión y redirigir al login con modo cambio
            Session::put('reset_email', $user->email);
            Session::put('requires_password_change', true);

            // Cerrar sesión
            Auth::logout();

            return redirect()->route('login')
                ->with('warning', 'Debes cambiar tu contraseña para continuar.');
        }

        $request->session()->regenerate();

        // Mapa de redirecciones por rol
        $redirectMap = [
            'Owner' => route('usuarios.index'),
            // 'Vendedor' => route('turno.index'),
            'Administrador' => route('dashboard'),
        ];

        // Buscar redirección según rol del usuario
        foreach ($redirectMap as $role => $route) {
            if ($user->hasRole($role)) {
                return redirect($route);
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }


    // Procesar login con cambio de contraseña
    public function storeWithPasswordChange(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Usuario no encontrado.',
            ]);
        }

        if (!$user->must_change_password) {
            throw ValidationException::withMessages([
                'email' => 'Este usuario no requiere cambio de contraseña.',
            ]);
        }

        // Actualizar contraseña
        $user->password = Hash::make($request->new_password);
        $user->must_change_password = false;
        $user->password_changed_at = now();
        $user->save();

        // Iniciar sesión automáticamente
        Auth::login($user);

        // Limpiar sesión
        Session::forget(['reset_email', 'requires_password_change']);

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    // Verificar si usuario requiere cambio de contraseña
    public function checkPasswordChange(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'requires_change' => $user && $user->must_change_password == true,
            'user_exists' => $user ? true : false
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
