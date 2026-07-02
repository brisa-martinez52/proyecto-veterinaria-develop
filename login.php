<?php
require_once 'includes/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni = sanitize($_POST['dni']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM usuarios WHERE dni = '$dni'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $usuario = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_dni'] = $usuario['dni'];
            $_SESSION['es_admin'] = $usuario['es_admin'];
            
            if ($usuario['es_admin'] == 1) {
                redirect('admin/panel.php');
            } 
            
            else {
                redirect('index.php');
            }
        } 
        
        else {
            $error = 'DNI o contraseña incorrectos';
        }
    } 
    
    else {
        $error = 'DNI o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Patitas</title>
    <link rel="stylesheet" href="css/estilos.css?v=202607011">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="max-width: 500px;">
        <div class="card">
            <h1 style="text-align: center; font-size: 32px; margin-bottom: 10px;">Iniciar Sesión</h1>
            <p style="text-align: center; color: #666; margin-bottom: 30px;">Ingresa a tu cuenta de Patitas</p>

            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">DNI</label>
                    <input type="text" name="dni" class="form-input" required placeholder="Ingresa tu DNI">
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-input" required placeholder="Ingresa tu contraseña">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Iniciar Sesión</button>
            </form>

            <div style="margin-top: 30px; text-align: center;">
                <p>¿No tienes cuenta? <a href="register.php" style="color: #83d4d7; font-weight: bold;">Regístrate aquí</a></p>
                <p style="background-color: #fff3cd; border: 2px solid #ffc107; border-radius: 10px; padding: 10px; margin-top: 15px; font-size: 13px; color: #856404; font-weight: bold;">
                    🔑 Admin: DNI="admin" | Contraseña="admin123"
                </p>
            </div>
        </div>
    </div>
</body>
</html>