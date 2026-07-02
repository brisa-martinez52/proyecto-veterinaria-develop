<?php
require_once 'includes/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni = sanitize($_POST['dni']);
    $nombre = sanitize($_POST['nombre']);
    $apellido = sanitize($_POST['apellido']);
    $email = sanitize($_POST['email']);
    $telefono = sanitize($_POST['telefono']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validaciones
    if ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } else {
        // Verificar si el DNI ya existe
        $check_query = "SELECT * FROM usuarios WHERE dni = '$dni' OR email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'El DNI o email ya está registrado';
        } else {
            // Registrar usuario
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO usuarios (dni, nombre, apellido, email, telefono, password) 
                           VALUES ('$dni', '$nombre', '$apellido', '$email', '$telefono', '$hashed_password')";
            
            if (mysqli_query($conn, $insert_query)) {
                $usuario_id = mysqli_insert_id($conn);
                $_SESSION['usuario_id'] = $usuario_id;
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_dni'] = $dni;
                $_SESSION['es_admin'] = 0;
                redirect('index.php');
            } else {
                $error = 'Error al registrar usuario';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Patitas</title>
    <link rel="stylesheet" href="css/estilos.css?v=202607011">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="max-width: 600px;">
        <div class="card">
            <h1 style="text-align: center; font-size: 32px; margin-bottom: 10px;">Registrarse</h1>
            <p style="text-align: center; color: #666; margin-bottom: 30px;">Crea tu cuenta en Patitas</p>

            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">DNI</label>
                    <input type="text" name="dni" class="form-input" required>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" name="telefono" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input type="password" name="confirm_password" class="form-input" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Crear Cuenta</button>
            </form>

            <div style="margin-top: 30px; text-align: center;">
                <p>¿Ya tienes cuenta? <a href="login.php" style="color: #83d4d7; font-weight: bold;">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>
</body>
</html>
