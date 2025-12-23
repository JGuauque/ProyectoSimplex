<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Log;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $usuarios = User::orderBy('created_at', 'desc')->get();
        // $roles = Role::all(); // Para los selects
        // return view('usuarios.index', compact('usuarios', 'roles'));
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('usuarios.index'); // Usamos la misma vista para crear/editar
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // Validación
        $request->validate([
            'name' => 'required|string|max:255',
            // 'apellidos' => 'nullable|string|max:255',
            // 'identificacion' => 'required|string|unique:users',
            // 'email' => 'required|string|email|max:255|unique:users',
            // 'username' => 'required|string|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // 'roles' => 'required|array',
        ]);

        // Crear usuario
        $user = User::create([
            'name' => $request->name,
            // 'apellidos' => $request->apellidos,
            // 'identificacion' => $request->identificacion,
            // 'email' => $request->email,
            // 'username' => $request->username,
            'password' => Hash::make($request->password),
            // 'rol' => $request->rol,
        ]);

        // Asignar roles
        // $user->syncRoles($request->roles);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'identificacion' => 'required|string|unique:users,identificacion,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'rol' => 'required|in:Owner,Administrador,Vendedor',
            // 'roles' => 'required|array',
        ]);

        $data = [
            'name' => $request->name,
            'apellidos' => $request->apellidos,
            'identificacion' => $request->identificacion,
            'email' => $request->email,
            'username' => $request->username,
            'rol' => $request->rol,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        // Sincronizar roles
        $usuario->syncRoles($request->roles);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // //
        // $usuario = User::findOrFail($id);

        // // Evitar que el usuario actual se elimine a sí mismo
        // if ($usuario->id === auth()->id()) {
        //     return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario');
        // }

        // $usuario->delete();

        // return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente');
    }

    /**
     * Obtener datos de usuario para edición via AJAX
     */
    public function getUsuarioData($id)
    {
        $usuario = User::findOrFail($id);
        return response()->json([
            'name' => $usuario->name,
            'apellidos' => $usuario->apellidos,
            'identificacion' => $usuario->identificacion,
            'email' => $usuario->email,
            'username' => $usuario->username,
            'rol' => $usuario->rol,
        ]);
    }


    public function resetPassword($id)
    {
        try {
            $user = User::findOrFail($id);

            Log::info('Reset password for user:', [
                'id' => $user->id,
                'email' => $user->email,
                'current_must_change_password' => $user->must_change_password
            ]);

            // Marcar que debe cambiar la contraseña
            $user->must_change_password = true;
            $user->save();

            // DEBUG después de guardar
            Log::info('After reset - must_change_password:', [
                'id' => $user->id,
                'email' => $user->email,
                'new_must_change_password' => $user->must_change_password
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña reseteada exitosamente. El usuario deberá establecer una nueva contraseña al iniciar sesión.',
                'email' => $user->email
            ]);
        } catch (\Exception $e) {

            Log::error('Error resetting password:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
