<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimplexTC — Control total, decisiones simples</title>

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos-home.css">

    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <div class="container-wide">
        <!-- contenido más ancho -->
    </div>

    <!-- ====== HEADER ====== -->
    <header class="site-header">
        <div class="container header-inner">
            <div class="brand">
                <img src="{{ asset('Assets\logo.png') }}" alt="SimplexTC logo" class="logo">
                <span class="brand-text">SimplexTC</span>
            </div>
            <nav class="main-nav">
                <a href="#hero">Inicio</a>
                <a href="#about">¿Qué es?</a>
                <a href="#modulos">Módulos</a>
                <a href="#diferencial">Diferenciales</a>
                <a href="#prestamo">Préstamo</a>
                <a href="#contacto">Contacto</a>
                @if (Route::has('login'))
                    @auth

                    @else
                    <a class="btn btn-login" href="#">Iniciar sesión</a> 
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <!-- ====== HERO ====== -->
    <section id="hero" class="hero">
        <div class="container hero-grid">
            <div class="hero-left" data-aos="fade-right">
                <h1>Control total, <span class="accent">decisiones simples</span></h1>
                <p class="lead">Desde la caja hasta el inventario, todo en un solo lugar y listo para que tomes decisiones
                    rápidas y seguras.</p>
                <div class="hero-ctas">
                    <a href="#prestamo" class="btn btn-primary">Conoce el módulo Préstamos</a>
                    <a href="#" class="btn btn-outline">Iniciar sesión</a>
                </div>
            </div>
            <div class="hero-right" data-aos="fade-left">
                <div class="hero-image-wrap">
                    <img src="{{ asset('Assets\hero-persona.png') }}" alt="Persona usando SimplexTC" class="hero-image">
                    <div class="hero-deco"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== ABOUT ====== -->
    <section id="about" class="section about">
        <div class="container about-grid">
            <div class="about-text" data-aos="fade-up">
                <h2>¿Qué es <strong>SimplexTC</strong>?</h2>
                <p>Una plataforma integral para pequeños, medianos y grandes comercios. Fue diseñada por gente que trabajó en la
                    primera línea del negocio: detectamos problemas reales y creamos soluciones simples para que puedas enfocarte
                    en vender.</p>
                <ul class="about-list">
                    <li>Panel de control con métricas en tiempo real</li>
                    <li>Gestión de turnos y cierre de caja</li>
                    <li>Inventario, ventas, usuarios y clientes</li>
                    <li>Módulo exclusivo de préstamos entre locales</li>
                </ul>
            </div>
            <div class="about-media" data-aos="fade-left">
                <img src="{{ asset('Assets\que-es-simplex.png') }}" alt="Ilustración sobre SimplexTC" class="responsive-img">
            </div>
        </div>
    </section>

    <!-- ====== MÓDULOS ====== -->
    <section id="modulos" class="section modules">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Nuestros Módulos</h2>
            <p class="section-sub" data-aos="fade-up" data-aos-delay="50">Todo lo que tu comercio necesita, organizado y
                accesible.</p>
            <div class="cards-grid">
                <article class="card" data-aos="zoom-in">
                    <div class="card-icon">💳</div>
                    <h3>Ventas</h3>
                    <p>Registro rápido de ventas, facturación y control de pagos.</p>
                </article>
                <article class="card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="card-icon">📦</div>
                    <h3>Inventario</h3>
                    <p>Creación de productos, códigos, precios, costos y stock por local.</p>
                </article>
                <article class="card" data-aos="zoom-in" data-aos-delay="200">
                    <div class="card-icon">👥</div>
                    <h3>Clientes</h3>
                    <p>Base de datos con historial de compras y comunicación directa.</p>
                </article>
                <article class="card" data-aos="zoom-in" data-aos-delay="300">
                    <div class="card-icon">🔐</div>
                    <h3>Usuarios</h3>
                    <p>Control de roles: admin, vendedor, y permisos personalizados.</p>
                </article>
                <article class="card" data-aos="zoom-in" data-aos-delay="400">
                    <div class="card-icon">⏱️</div>
                    <h3>Turnos</h3>
                    <p>Inicia y cierra turnos con resumen en tiempo real de ventas.</p>
                </article>
                <article class="card" data-aos="zoom-in" data-aos-delay="500">
                    <div class="card-icon">📊</div>
                    <h3>Panel</h3>
                    <p>Dashboard estilo Shopify para decisiones rápidas.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- ====== DIFERENCIALES ====== -->
    <section id="diferencial" class="section diferencial">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">¿Qué nos diferencia?</h2>
            <div class="diff-grid">
                <div class="diff-item" data-aos="fade-right">
                    <h3>Hecho desde la experiencia</h3>
                    <p>Desarrollado por equipos que vivieron el negocio, entendemos tus necesidades.</p>
                </div>
                <div class="diff-item" data-aos="fade-up" data-aos-delay="100">
                    <h3>Simple y escalable</h3>
                    <p>Interfaz intuitiva que crece con tu negocio.</p>
                </div>
                <div class="diff-item" data-aos="fade-left" data-aos-delay="200">
                    <h3>Acompañamiento real</h3>
                    <p>Soporte y formación para que tu equipo saque el máximo provecho.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== PRESTAMO ====== -->
    <section id="prestamo" class="section prestamo">
        <div class="container prestamo-grid" data-aos="zoom-in-up">
            <div class="prestamo-card">
                <h2>Módulo Estrella</h2>
                <h3>Préstamos a Aliados</h3>
                <p class="lead">Registra préstamos entre locales con trazabilidad completa: fecha, producto, código, cantidad y
                    precio de préstamo. Evita pérdidas por devoluciones no registradas y simplifica la logística interlocal.</p>
                <ul class="prestamo-list">
                    <li><strong>Registro completo:</strong> producto, cantidad, fecha y local origen/destino.</li>
                    <li><strong>Reglas de precio:</strong> no permitir préstamo a precio menor o igual al costo.</li>
                    <li><strong>Alertas:</strong> seguimiento de devoluciones pendientes.</li>
                </ul>
                <div class="prestamo-cta">
                    <a href="#" class="btn btn-primary">Solicita acceso al módulo</a>
                    <a href="#contacto" class="btn btn-primary">Más información</a>
                </div>
            </div>
            <div class="prestamo-media">
                <img src="{{ asset('Assets\modulo-prestamos.jpg') }}" alt="Módulo de Préstamos" class="responsive-img">
            </div>
        </div>
    </section>

    <!-- ====== CONTACTO + EMPRESA ====== -->
    <section id="contacto" class="contact-footer">
        <div class="container contact-container">

            <!-- Formulario -->
            <div class="contact-form">
                <h2>Solicita más información</h2>
                <form id="contactForm">
                    <label for="name">Nombre</label>
                    <input id="name" name="name" type="text" required placeholder="Tu nombre">
                    <label for="emailC">Correo</label>
                    <input id="emailC" name="email" type="email" required placeholder="tu@ejemplo.com">
                    <label for="phone">Teléfono</label>
                    <input id="phone" name="phone" type="tel" placeholder="+57 300 000 0000">
                    <label for="msg">Mensaje</label>
                    <textarea id="msg" name="message" rows="4" placeholder="¿En qué te podemos ayudar?"></textarea>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                    <p id="contactMessage" class="form-message" aria-live="polite"></p>
                </form>
            </div>

            <!-- Info empresa + redes -->
            <div class="company-info">
                <h2>Sobre nosotros</h2>
                <p>
                    Somos <strong>SimplexTC</strong>, una empresa enfocada en brindar soluciones
                    tecnológicas y asesoría personalizada para impulsar tu crecimiento.

                    <img src="{{ asset('Assets\repre.png') }}" alt="Ilustración sobre SimplexTC" class="responsive-img">
                </p>
                <div class="social-icons">
                    <a href="https://wa.me/3163348177" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/reel/1088159892899278" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/simplex_tc?igsh=YTBvMjQyeHh1NTZ1&utm_source=qr" target="_blank"><i
                            class="fab fa-instagram"></i></a>
                </div>
            </div>

        </div>
    </section>

    <!-- ====== FOOTER ====== -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="#">Política de privacidad</a>
                    <a href="#">Términos legales</a>
                    <a href="#">PQRSF</a>
                </div>
                <div class="footer-partners">
                    <img src="{{ asset('Assets\SuperIntendencia.png') }}" alt="Superintendencia de Industria y Comercio">
                    <img src="{{ asset('Assets\DIAN.png') }}" alt="DIAN">
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 SimplexTC — Todos los derechos reservados</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- <script src="js/script.js"></script> -->
    <script>
        AOS.init({
            duration: 700,
            once: true
        });
    </script>
</body>

</html>