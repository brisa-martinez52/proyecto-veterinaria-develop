<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Patitas</title>
    <link rel="stylesheet" href="css/estilos.css?v=202607011">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="container">

        <h1 class="text-center">Preguntas Frecuentes</h1>

        <p class="text-center mb-20" style="font-size: 18px; color: #666;">
            Encuentra respuestas a las preguntas más comunes sobre nuestros servicios
        </p>

        <div style="max-width: 900px; margin: 0 auto;">

            <?php
            $faqs = [
                ['Cómo puedo registrar a mi mascota', 'Una vez registrado e iniciado sesión, ve a la sección "Mis Mascotas" y hace clic en "Agregar Mascota". Completa el formulario con los datos de tu compañero.'],

                ['Cómo solicito un turno veterinario', 'En la sección "Turnos" podrás seleccionar a tu mascota, el veterinario de preferencia, la fecha y el horario disponible. Recibirás una confirmación instantánea.'],

                ['Puedo cancelar un turno', 'Sí, puedes cancelar tus turnos desde la misma sección "Turnos". Los turnos activos tendrán un botón de "Cancelar" disponible.'],

                ['Cómo accedo al historial médico de mi mascota', 'En la sección "Historial Médico" selecciona tu mascota y podrás ver todos los registros de consultas, vacunas, tratamientos y estudios realizados.'],

                ['Cómo realizo una compra en la tienda', 'Navega por nuestros productos, agrégalos al carrito y cuando estés listo, haz clic en "Finalizar Compra". Tenes que estar registrado para completar la transacción.'],

                ['Qué métodos de pago aceptan', 'Actualmente aceptamos todos los medios de pago digitales. El sistema actualizará automáticamente el stock tras cada compra.'],

                ['Los veterinarios pueden actualizar el historial', 'Sí, nuestros veterinarios actualizan el historial médico de tu mascota después de cada consulta con diagnósticos, tratamientos y observaciones.'],

                ['Cómo contacto a la clínica', 'Puedes llamarnos al +54 299 123456 o visitarnos en Neuquén, Argentina. Nuestro horario de atención es de lunes a viernes de 9:00 a 18:00 hs.'],

                ['Qué especialidades veterinarias tienen', 'Contamos con profesionales especializados en Medicina General, Cirugía, Dermatología, y otras especialidades para el cuidado integral de tu mascota.'],

                ['Puedo modificar los datos de mi mascota', 'Sí, en la sección "Mis Mascotas" puedes editar toda la información de tus mascotas registradas, incluyendo peso, edad y otros datos importantes.']
            ];

            foreach ($faqs as $index => $faq):
            ?>

                <div class="card mb-20">
                    <h3 style="color: #83d4d7; margin-bottom: 15px;">
                        <?php echo $faq[0]; ?>
                    </h3>

                    <p style="font-size: 16px; line-height: 1.6; color: #444;">
                        <?php echo $faq[1]; ?>
                    </p>
                </div>

            <?php endforeach; ?>

        </div>

        <div class="card-celeste text-center mt-20">

            <h2>¿Tienes más preguntas?</h2>

            <p style="font-size: 18px; margin-top: 15px;">
                Contáctanos al <strong>+54 299 123456</strong> o visítanos en
                <strong>Neuquén, Argentina</strong>
            </p>

        </div>

    </div>

</body>
</html>