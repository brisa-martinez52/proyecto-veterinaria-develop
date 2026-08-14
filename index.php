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
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="hero-section" style="background: linear-gradient(135deg, #acf2f5 0%, #83d4d7 100%); padding: 60px 20px;">
        <div class="hero-content" style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: center; gap: 80px; flex-wrap: wrap;">
            <div class="hero-image" style="flex-shrink: 0;">
                <img src="<?php echo asset_path('Imagenes/logo patitas.png'); ?>" alt="Patitas" style="width: 400px; height: 400px; border-radius: 50%;">
            </div>
            <div class="hero-text" style="flex: 1;c">
                <div class="hero-title-box" style="background-color: #83d4d7; border-radius: 60px; padding: 40px; box-shadow: 8px 8px 16px rgba(68,68,68,0.3); margin-bottom: 30px;">
                    <h1 style="font-size: 28px; text-align: center; margin: 0 0 15px 0;">Cuidamos a tus mascotas con amor, confianza y atención personalizada.</h1>
                    <p style="font-size: 18px; text-align: center; margin: 0;">Encontrá productos, servicios veterinarios y el historial completo de tus compañeros en un solo lugar.</p>
                </div>

                <div class="hero-actions" style="display: flex; flex-direction: column; gap: 20px; align-items: center;">
                    <?php if(!isLoggedIn()): ?>
                        <a href="login.php"><button class="btn btn-primary" style="width: 300px; padding: 18px 40px; font-size: 18px;">Iniciar Sesión</button></a>
                        <a href="register.php"><button class="btn btn-success" style="width: 300px; padding: 18px 40px; font-size: 18px;">Registrarse</button></a>
                        <a href="login.php"><button class="btn btn-danger" style="width: 300px; padding: 18px 40px; font-size: 18px; background-color: #ff9999;">Ingreso Administrador</button></a>
                    <?php else: ?>
                        <a href="productos.php"><button class="btn btn-primary" style="width: 300px; padding: 18px 40px; font-size: 18px;">Ver Productos</button></a>
                        <a href="turnos.php"><button class="btn btn-secondary" style="width: 300px; padding: 18px 40px; font-size: 18px;">Solicitar Turno</button></a>
                        <a href="mis_mascotas.php"><button class="btn btn-secondary" style="width: 300px; padding: 18px 40px; font-size: 18px;">Mis Mascotas</button></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="info-section" style="padding: 30px 20px;">
        <div class="contact-info" style="background-color: #acf2f5; border-radius: 50px; padding: 20px 40px; max-width: 800px; margin: 0 auto; box-shadow: 4px 4px 12px rgba(0,0,0,0.1);">
            <p style="text-align: center; font-size: 22px; font-weight: bold; margin: 0;">
                <strong> +54 299 123456</strong> Neuquén, Argentina
            </p>
        </div>
    </div>

    <div class="features-section" style="padding: 60px 20px;">
        <div class="container">
            <h2 style="text-align: center; font-size: 36px; font-weight: bold; margin-bottom: 40px;">Nuestros Servicios</h2>
            <div class="grid grid-4">
                <div class="card" style="text-align: center;">
                    <div style="font-size: 60px; margin-bottom: 15px;">ventas</div>
                    <h3>Tienda Online</h3>
                    <p>Alimentos, accesorios y productos de calidad para tu mascota</p>
                </div>
                <div class="card" style="text-align: center;">
                    <div style="font-size: 60px; margin-bottom: 15px;">"calendario"</div>
                    <h3>Turnos Veterinarios</h3>
                    <p>Agenda citas con nuestros profesionales especializados</p>
                </div>
                <div class="card" style="text-align: center;">
                    <div style="font-size: 60px; margin-bottom: 15px;">"notas"</div>
                    <h3>Historial Médico</h3>
                    <p>Accede al historial completo de salud de tus mascotas</p>
                </div>
                <div class="card" style="text-align: center;">
                    <div style="font-size: 60px; margin-bottom: 15px;">"corazon"</div>
                    <h3>Atención Personalizada</h3>
                    <p>Cuidado profesional y amoroso para cada paciente</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            .hero-content { flex-direction: column !important; gap: 30px !important; }
            .hero-image img { width: 250px !important; height: 250px !important; }
            .hero-title-box { padding: 30px 20px !important; }
            .hero-title-box h1 { font-size: 20px !important; }
            .hero-title-box p { font-size: 14px !important; }
            .hero-actions button { width: 100% !important; max-width: 300px; }
        }
    </style>
</body>
</html>