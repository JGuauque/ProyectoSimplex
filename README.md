# Sistema de Inventario y Facturación POS - Simplex

## 🎯 Características del Sistema

- ✅ Gestión completa de inventario
- ✅ Sistema POS para ventas rápidas
- ✅ Facturación electrónica según normativa colombiana
- ✅ Reportes de ventas e inventario
- ✅ Envío automático de facturas POS por correo
- ✅ Interfaz amigable
- ✅ Multi-usuario con roles de acceso

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 11.25.0
- **Frontend**: Bootstrap, JavaScript
- **Base de datos**: MySQL
- **Servidor**: Apache (XAMPP)
- **Herramientas**: Composer, Node.js, Git

## 📋 Requisitos Previos

### Software Requerido
- [XAMPP 8.2.12+](https://www.apachefriends.org/es/index.html)
- [Composer](https://getcomposer.org/download/)
- [Node.js LTS 22.17.0+](https://nodejs.org/en/blog/release/v24.13.0)
- [Git](https://git-scm.com/install/windows)
- [Visual Studio Code](https://code.visualstudio.com/)

### Dependencias de Windows
- [Microsoft Visual C++ 2013 Redistributable](https://learn.microsoft.com/es-es/cpp/windows/latest-supported-vc-redist?view=msvc-170#visual-studio-2013-vc-120-no-longer-supported)

### Configuración Mínima del Sistema
- Windows 10/11, Linux o macOS
- 4GB RAM mínimo (8GB recomendado)
- 2GB de espacio libre en disco
- Puerto 80 y 3306 disponibles

## 🚀 Instalación Paso a Paso

### 1. Clonar el Repositorio

```bash
# Crear carpeta donde se va alojar el proyecto
mkdir ProyectoSimplex
cd ProyectoSimplex

# Clonar repositorio
git clone https://github.com/JGuauque/ProyectoSimplex.git .

```

### 2. Configurar Entorno de Desarrollo

#### A. Iniciar Servidores XAMPP
1. Abrir XAMPP Control Panel
2. Iniciar los servicios:
   - Apache (puerto 80)
   - MySQL (puerto 3306)

#### B. Crear Base de Datos
1. Abrir http://localhost/phpmyadmin
2. Crear nueva base de datos:
   - Nombre: `simplex`
   - Collation: `utf8mb4_unicode_ci`

### 3. Instalar Dependencias

En el bash donde se clono el repositorio copia los siguientes comandos:

```bash
# Instalar dependencias PHP
composer install

# Instalar dependencias JavaScript
npm install

# Si hay errores con npm, limpiar cache:
npm cache clean --force
```

### 4. Configurar Variables de Entorno

```bash
# Copiar archivo de entorno
copy .env.example .env  # En Windows
# o
cp .env.example .env    # En Linux/Mac
```



#### Editar archivo `.env` con tu configuración:

```env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simplex
DB_USERNAME=root
DB_PASSWORD=  # Dejar vacío si no tienes contraseña

# Configuración de correo para facturas (GMAIL)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicación
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_correo@gmail.com
MAIL_FROM_NAME="La Casa de Nintendo"
```

#### ⚠️ Importante para Gmail:
1. Activar verificación en 2 pasos en tu cuenta Google
2. Generar "Contraseña de aplicación" desde:
   - [Seguridad de Google](https://myaccount.google.com/security)
   - Usar esa contraseña en `MAIL_PASSWORD`

### 5. Generar Clave de Aplicación y Migraciones

```bash
# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Crear enlace de almacenamiento
php artisan storage:link
```

### 6. 🧹 Notas adicionles

El archivo .gitignore incluye .sqlite para evitar subir bases de datos locales.

Si realizas cambios en los estilos o script, pudes recompilar con:

```bash
# Para desarrollo
npm run dev
```

Para modo producción:

```bash
# Para producción
npm run build
```


### 7. Iniciar el Servidor


Abrir en el buscador de windows el XAMPP Control Panel y iniciar (Start) _Apache_ y _MySQL_
Arrancar el sistema de inventario y facturación POS

En el editor de codigo abrir terminal (CTRL + Ñ), ejecutar el siguiente comando y seguir el enlace que muestra en la terminal:

```bash
# Usando artisan
php artisan serve
```

### 8. Acceder al Sistema

1. Abrir navegador web
2. Ir a: http://localhost:8000
3. Credenciales por defecto:
   - **Administrador**: admin@demo.com / password123


## 🔧 Configuración Adicional


### Variables de Entorno para Colombia

```env
APP_TIMEZONE=America/Bogota
APP_LOCALE=es
```

## 📁 Estructura del Proyecto

```
ProyectoSimplex/
├── app/
│   ├── Http/Controllers/   # Controladores
│   ├── Models/            # Modelos Eloquent
│   ├── Services/          # Lógica de negocio
│   └── View/Components/   # Componentes Blade
├── database/
│   ├── migrations/        # Migraciones de BD
│   ├── seeders/          # Datos iniciales
│   └── factories/        # Factories para pruebas
├── public/
│   └── assets/           # CSS, JS, imágenes
├── resources/
│   ├── views/            # Vistas Blade
│   └── lang/             # Traducciones
├── routes/
│   ├── web.php           # Rutas web
│   └── api.php           # Rutas API
└── storage/
    ├── app/public/       # Archivos subidos
    └── logs/             # Logs de aplicación
```

## 🐛 Solución de Problemas Comunes

### Error de Conexión a MySQL
1. Verificar que MySQL esté corriendo en XAMPP
2. Comprobar credenciales en `.env`
3. Probar conexión manual en phpMyAdmin

### Error de Permisos

```bash
# En Linux/Mac
chmod -R 755 storage bootstrap/cache

# En Windows (ejecutar como administrador)
icacls storage /grant Users:(OI)(CI)F
```

### Error al Enviar Correos
1. Verificar contraseña de aplicación en Gmail
2. Confirmar que el correo no requiere inicio de sesión
3. Probar con otro puerto (465 con SSL)

---

**✨ ¡Listo! Tu sistema POS Simplex está instalado y listo para usar.**

*Desarrollado con ❤️ para el local comercial La Casa de Nintendo, Palmira*


## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
