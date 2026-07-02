<?php
require_once 'includes/config.php';
requireLogin();

$usuario_id = $_SESSION['usuario_id'];
$success = '';

// Solicitar turno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['solicitar_turno'])) {
    $mascota_id = (int)$_POST['mascota_id'];
    $veterinario_id = (int)$_POST['veterinario_id'];
    $fecha = sanitize($_POST['fecha']);
    $hora = sanitize($_POST['hora']);
    
    $query = "INSERT INTO turnos (usuario_id, mascota_id, veterinario_id, fecha, hora) 
              VALUES ($usuario_id, $mascota_id, $veterinario_id, '$fecha', '$hora')";
    if (mysqli_query($conn, $query)) {
        $success = '¡Turno solicitado con éxito!';
    }
}

// Cancelar turno
if (isset($_GET['cancelar'])) {
    $id = (int)$_GET['cancelar'];
    mysqli_query($conn, "UPDATE turnos SET estado='cancelado' WHERE id=$id AND usuario_id=$usuario_id");
    redirect('turnos.php');
}

$mascotas = mysqli_query($conn, "SELECT * FROM mascotas WHERE usuario_id=$usuario_id");
$veterinarios = mysqli_query($conn, "SELECT * FROM veterinarios");
$turnos = mysqli_query($conn, "SELECT t.*, m.nombre as mascota_nombre, v.nombre as vet_nombre, v.apellido as vet_apellido 
                                FROM turnos t 
                                JOIN mascotas m ON t.mascota_id = m.id 
                                JOIN veterinarios v ON t.veterinario_id = v.id 
                                WHERE t.usuario_id=$usuario_id ORDER BY t.fecha DESC, t.hora DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos - Patitas</title>
    <link rel="stylesheet" href="css/estilos.css?v=202607011">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Solicitar Turno</h1>
        <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

        <div class="card mb-20">
            <h2>Nuevo Turno</h2>
            <form method="POST">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Mascota</label>
                        <select name="mascota_id" class="form-select" required>
                            <option value="">Selecciona tu mascota</option>
                            <?php while ($m = mysqli_fetch_assoc($mascotas)): ?>
                                <option value="<?php echo $m['id']; ?>"><?php echo $m['nombre']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Veterinario</label>
                        <select name="veterinario_id" class="form-select" required>
                            <option value="">Selecciona veterinario</option>
                            <?php while ($v = mysqli_fetch_assoc($veterinarios)): ?>
                                <option value="<?php echo $v['id']; ?>"><?php echo $v['nombre'].' '.$v['apellido'].' - '.$v['especialidad']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hora</label>
                        <select name="hora" class="form-select" required>
                            <option value="">Selecciona horario</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="12:00">12:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                            <option value="18:00">18:00</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="solicitar_turno" class="btn btn-success mt-20">Solicitar Turno</button>
            </form>
        </div>

        <h2>Mis Turnos</h2>
        <?php if (mysqli_num_rows($turnos) == 0): ?>
            <div class="card text-center"><p>No tienes turnos programados</p></div>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Mascota</th><th>Veterinario</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php while ($t = mysqli_fetch_assoc($turnos)): ?>
                        <tr>
                            <td><?php echo $t['mascota_nombre']; ?></td>
                            <td><?php echo $t['vet_nombre'].' '.$t['vet_apellido']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($t['fecha'])); ?></td>
                            <td><?php echo $t['hora']; ?></td>
                            <td><?php echo $t['estado']; ?></td>
                            <td>
                                <?php if ($t['estado'] == 'activo'): ?>
                                    <a href="?cancelar=<?php echo $t['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Cancelar turno?')">Cancelar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
