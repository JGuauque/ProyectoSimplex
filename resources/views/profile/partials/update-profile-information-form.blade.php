<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Información del perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Información del perfil y la dirección de correo electrónico de tu cuenta.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Información Básica -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" :value="__('Nombre completo')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                    :value="old('name', $user->name)" required autofocus autocomplete="name" readonly/>
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Correo electrónico')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                    :value="old('email', $user->email)" required autocomplete="username" readonly/>
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="identificacion" :value="__('Documento de identidad')" />
                <x-text-input id="identificacion" name="identificacion" type="text"
                    class="mt-1 block w-full" :value="old('identificacion', $user->identificacion)"
                    placeholder="Cédula, NIT, Pasaporte" readonly/>
                <x-input-error class="mt-2" :messages="$errors->get('identificacion')" />
            </div>
            <!-- Información de Roles (solo lectura) -->
            @if($user->roles->count() > 0)
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">
                    {{ __('Roles asignados') }}
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($user->roles as $role)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200" style="color: white;">
                        <i class="fas fa-user-shield mr-2" style="color: white;">­</i>
                        {{ ucfirst($role->name) }}
                    </span>
                    @endforeach
                </div>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Los roles definen los permisos de acceso en el sistema. Contacta al administrador para cambios.') }}
                </p>
            </div>
            @endif

            <!-- Información de Estado (solo lectura) -->
            <div>
                <x-input-label :value="__('Estado de la cuenta')" />
                <div class="mt-2 flex items-center">
                    <p class="ml-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Fecha de registro: ') }} {{ $user->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>

        </div>


    </form>
</section>