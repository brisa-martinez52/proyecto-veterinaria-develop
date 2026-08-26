```php
<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria Patitas - Inicio</title>

    <link rel="stylesheet" href="css/estilos.css?v=202607011">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        /* =====================================================
           ANIMACIONES AL HACER SCROLL
           ===================================================== */

        .scroll-animado {
            opacity: 0;
            transform: translateY(50px);
            transition:
                opacity 0.8s ease,
                transform 0.8s ease;
        }

        .scroll-animado.visible {
            opacity: 1;
            transform: translateY(0);
        }


        /* Animación especial para la imagen principal */

        .hero-image {
            animation: aparecerLogo 1s ease forwards;
        }

        @keyframes aparecerLogo {

            from {
                opacity: 0;
                transform: scale(0.85);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }

        }


        /* Animación del texto principal */

        .hero-text {
            animation: aparecerTexto 1s ease 0.2s forwards;
            opacity: 0;
        }

        @keyframes aparecerTexto {

            from {
                opacity: 0;
                transform: translateX(40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }

        }


        /* Las tarjetas aparecen una después de otra */

        .servicio-1 {
            transition-delay: 0.1s;
        }

        .servicio-2 {
            transition-delay: 0.25s;
        }

        .servicio-3 {
            transition-delay: 0.4s;
        }

        .servicio-4 {
            transition-delay: 0.55s;
        }


        /* Animación de los íconos */

        .icono-servicio {
            transition: transform 0.3s ease;
        }

        .card:hover .icono-servicio {
            transform: translateY(-5px) scale(1.08);
        }


        /* =====================================================
           ESTILOS DE SERVICIOS
           ===================================================== */

        .servicio-card {
            text-align: center;
            transition:
                transform 0.3s ease,
                box-shadow 0.3s ease;
        }

        .servicio-card:hover {
            transform: translateY(-7px);
            box-shadow: 8px 12px 22px rgba(68, 68, 68, 0.18);
        }

        .icono-servicio {
            font-size: 55px;
            margin-bottom: 18px;
            color: #3b8f92;
        }

        .servicio-card h3 {
            margin-bottom: 10px;
        }

        .servicio-card p {
            color: #666;
            line-height: 1.5;
        }


        /* =====================================================
           CONTACTO
           ===================================================== */

        .contact-info {
            transition:
                transform 0.3s ease,
                box-shadow 0.3s ease;
        }

        .contact-info:hover {
            transform: translateY(-3px);
            box-shadow: 6px 8px 18px rgba(0, 0, 0, 0.14) !important;
        }


        /* =====================================================
           RESPONSIVE
           ===================================================== */

        @media (max-width: 768px) {

            .hero-content {
                flex-direction: column !important;
                gap: 30px !important;
            }

            .hero-image img {
                width: 250px !important;
                height: 250px !important;
            }

            .hero-title-box {
                padding: 30px 20px !important;
            }

            .hero-title-box h1 {
                font-size: 20px !important;
            }

            .hero-title-box p {
                font-size: 14px !important;
            }

            .hero-actions button {
                width: 100% !important;
                max-width: 300px;
            }

            .icono-servicio {
                font-size: 48px;
            }

        }

    </style>
</head>

<body>

    <?php include 'includes/navbar.php'; ?>


    <!-- =====================================================
         HERO
         ===================================================== -->

    <div class="hero-section"
         style="
            background: linear-gradient(135deg, #acf2f5 0%, #83d4d7 100%);
            padding: 60px 20px;
         ">

        <div class="hero-content"
             style="
                max-width: 1200px;
                margin: 0 auto;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 80px;
                flex-wrap: wrap;
             ">


            <!-- LOGO -->

            <div class="hero-image"
                 style="flex-shrink: 0;">

                <img src="<?php echo asset_path('Imagenes/logo patitas.png'); ?>"
                     alt="Patitas"
                     style="
                        width: 400px;
                        height: 400px;
                        border-radius: 50%;
                     ">

            </div>


            <!-- TEXTO -->

            <div class="hero-text"
                 style="flex: 1;">

                <div class="hero-title-box"
                     style="
                        background-color: #83d4d7;
                        border-radius: 60px;
                        padding: 40px;
                        box-shadow: 8px 8px 16px rgba(68,68,68,0.3);
                        margin-bottom: 30px;
                     ">

                    <h1 style="
                        font-size: 28px;
                        text-align: center;
                        margin: 0 0 15px 0;
                    ">
                        Cuidamos a tus mascotas con amor,
                        confianza y atención personalizada.
                    </h1>

                    <p style="
                        font-size: 18px;
                        text-align: center;
                        margin: 0;
                    ">
                        Encontrá productos, servicios veterinarios
                        y el historial completo de tus compañeros
                        en un solo lugar.
                    </p>

                </div>


                <!-- BOTONES -->

                <div class="hero-actions"
                     style="
                        display: flex;
                        flex-direction: column;
                        gap: 20px;
                        align-items: center;
                     ">

                    <?php if(!isLoggedIn()): ?>

                        <a href="login.php">
                            <button class="btn btn-primary"
                                    style="
                                        width: 300px;
                                        padding: 18px 40px;
                                        font-size: 18px;
                                    ">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                Iniciar Sesión
                            </button>
                        </a>


                        <a href="register.php">
                            <button class="btn btn-success"
                                    style="
                                        width: 300px;
                                        padding: 18px 40px;
                                        font-size: 18px;
                                    ">
                                <i class="fa-solid fa-user-plus"></i>
                                Registrarse
                            </button>
                        </a>


                        <a href="login.php">
                            <button class="btn btn-danger"
                                    style="
                                        width: 300px;
                                        padding: 18px 40px;
                                        font-size: 18px;
                                        background-color: #ff9999;
                                    ">
                                <i class="fa-solid fa-user-shield"></i>
                                Ingreso Administrador
                            </button>
                        </a>


                    <?php else: ?>


                        <a href="productos.php">
                            <button class="btn btn-primary"
                                    style="
                                        width: 300px;
                                        padding: 18px 40px;
                                        font-size: 18px;
                                    ">
                                <i class="fa-solid fa-store"></i>
                                Ver Productos
                            </button>
                        </a>


                        <a href="turnos.php">
                            <button class="btn btn-secondary"
                                    style="
                                        width: 300px;
                                        padding: 18px 40px;
                                        font-size: 18px;
                                    ">
                                <i class="fa-solid fa-calendar-check"></i>
                                Solicitar Turno
                            </button>
                        </a>


                        <a href="mis_mascotas.php">
                            <button class="btn btn-secondary"
                                    style="
                                        width: 300px;
                                        padding: 18px 40px;
                                        font-size: 18px;
                                    ">
                                <i class="fa-solid fa-paw"></i>
                                Mis Mascotas
                            </button>
                        </a>


                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>



    <!-- =====================================================
         INFORMACIÓN DE CONTACTO
         ===================================================== -->

    <div class="info-section scroll-animado"
         style="padding: 60px 20px;">

        <div class="contact-info"
             style="
                background-color: #acf2f5;
                border-radius: 50px;
                padding: 20px 40px;
                max-width: 800px;
                margin: 0 auto;
                box-shadow: 4px 4px 12px rgba(0,0,0,0.1);
             ">

            <p style="
                text-align: center;
                font-size: 22px;
                font-weight: bold;
                margin: 0;
            ">

                <i class="fa-solid fa-phone"></i>
                +54 299 123456

                &nbsp;&nbsp;

                <i class="fa-solid fa-location-dot"></i>
                Neuquén, Argentina

            </p>

        </div>

    </div>



    <!-- =====================================================
         SERVICIOS
         ===================================================== -->

    <div class="features-section"
         style="
            padding: 60px 20px;
         ">

        <div class="container">


            <!-- TÍTULO -->

            <div class="scroll-animado">

                <h2 style="
                    text-align: center;
                    font-size: 36px;
                    font-weight: bold;
                    margin-bottom: 40px;
                ">
                    Nuestros Servicios
                </h2>

            </div>


            <!-- TARJETAS -->

            <div class="grid grid-4">


                <!-- TIENDA -->

                <div class="card servicio-card scroll-animado servicio-1">

                    <div class="icono-servicio">
                        <i class="fa-solid fa-truck"></i>
                    </div>

                    <h3>Tienda Online</h3>

                    <p>
                        Alimentos, accesorios y productos
                        de calidad para tu mascota.
                    </p>

                </div>


                <!-- TURNOS -->

                <div class="card servicio-card scroll-animado servicio-2">

                    <div class="icono-servicio">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <h3>Turnos Veterinarios</h3>

                    <p>
                        Agendá citas con nuestros profesionales
                        especializados.
                    </p>

                </div>


                <!-- HISTORIAL -->

                <div class="card servicio-card scroll-animado servicio-3">

                    <div class="icono-servicio">
                        <i class="fa-solid fa-notes-medical"></i>
                    </div>

                    <h3>Historial Médico</h3>

                    <p>
                        Accedé al historial completo de salud
                        de tus mascotas.
                    </p>

                </div>


                <!-- ATENCIÓN -->

                <div class="card servicio-card scroll-animado servicio-4">

                    <div class="icono-servicio">
                        <i class="fa-solid fa-heart"></i>
                    </div>

                    <h3>Atención Personalizada</h3>

                    <p>
                        Cuidado profesional y amoroso
                        para cada paciente.
                    </p>

                </div>


            </div>

        </div>

    </div>



    <!-- =====================================================
         JAVASCRIPT PARA ANIMACIONES
         ===================================================== -->

    <script>

        const elementos = document.querySelectorAll('.scroll-animado');

        const observer = new IntersectionObserver(
            (entradas) => {

                entradas.forEach((entrada) => {

                    if (entrada.isIntersecting) {

                        entrada.target.classList.add('visible');

                        observer.unobserve(entrada.target);

                    }

                });

            },
            {
                threshold: 0.15
            }
        );


        elementos.forEach((elemento) => {

            observer.observe(elemento);

        });

    </script>


</body>
</html>
```
