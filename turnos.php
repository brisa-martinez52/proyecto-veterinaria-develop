<?php
require_once 'includes/config.php';
requireLogin();

$usuario_id = $_SESSION['usuario_id'];
$success = '';
$error = '';
$hoy = date('Y-m-d');
$fecha_minima = date('Y-m-d', strtotime('tomorrow'));
$turnos_vencidos_actualizados = mysqli_query($conn, "UPDATE turnos SET estado='completado' WHERE estado='activo' AND fecha < '$hoy'");
if (!$turnos_vencidos_actualizados) {
    $error = 'No se pudieron actualizar los estados de tus turnos vencidos.';
}

// Solicitar turno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['solicitar_turno'])) {
    $mascota_id = isset($_POST['mascota_id']) ? (int)$_POST['mascota_id'] : 0;
    $veterinario_id = isset($_POST['veterinario_id']) ? (int)$_POST['veterinario_id'] : 0;
    $fecha = isset($_POST['fecha']) && is_string($_POST['fecha']) ? $_POST['fecha'] : '';
    $hora = isset($_POST['hora']) && is_string($_POST['hora']) ? $_POST['hora'] : '';

    if ($mascota_id < 1) {
        $error = 'Debes seleccionar una mascota registrada en tu cuenta.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)
        || !checkdate((int)substr($fecha, 5, 2), (int)substr($fecha, 8, 2), (int)substr($fecha, 0, 4))
        || $fecha <= $hoy) {
        $error = 'Selecciona una fecha posterior a la fecha actual.';
    } else {
        $query = "INSERT INTO turnos (usuario_id, mascota_id, veterinario_id, fecha, hora)
                  SELECT ?, id, ?, ?, ? FROM mascotas WHERE id = ? AND usuario_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'iissii', $usuario_id, $veterinario_id, $fecha, $hora, $mascota_id, $usuario_id);

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) === 1) {
                    $success = '¡Turno solicitado con éxito!';
                } else {
                    $error = 'La mascota seleccionada no está registrada en tu cuenta.';
                }
            } else {
                $error = 'No se pudo solicitar el turno. Inténtalo nuevamente.';
            }

            mysqli_stmt_close($stmt);
        } else {
            $error = 'No se pudo procesar la solicitud del turno. Inténtalo nuevamente.';
        }
    }
}

// Cancelar turno
if (isset($_GET['cancelar'])) {
    $id = (int)$_GET['cancelar'];
    mysqli_query($conn, "UPDATE turnos SET estado='cancelado' WHERE id=$id AND usuario_id=$usuario_id");
    redirect('turnos.php');
}

$mascotas = mysqli_query($conn, "SELECT * FROM mascotas WHERE usuario_id=$usuario_id");
$tiene_mascotas = mysqli_num_rows($mascotas) > 0;
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
    <link rel="stylesheet" href="css/estilos.css?v=20261031">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Solicitar Turno</h1>
        <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

        <div class="card mb-20">
            <h2>Nuevo Turno</h2>
            <?php if (!$tiene_mascotas): ?>
                <div class="alert alert-error">
                    Para solicitar un turno, primero debes <a href="mis_mascotas.php">registrar una mascota</a>.
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Mascota</label>
                        <select name="mascota_id" class="form-select" required <?php echo !$tiene_mascotas ? 'disabled' : ''; ?>>
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
                        <input type="date" name="fecha" class="form-input" min="<?php echo $fecha_minima; ?>" required>
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
                <button type="submit" name="solicitar_turno" class="btn btn-success mt-20" <?php echo !$tiene_mascotas ? 'disabled' : ''; ?>>Solicitar Turno</button>
            </form>
        </div>

        <h2>Mis Turnos</h2>
        <?php if (mysqli_num_rows($turnos) == 0): ?>
            <div class="card text-center"><p>No tienes turnos programados</p></div>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Mascota</th><th>Veterinario</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Aviso</th><th>Acciones</th></tr>
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
                                <?php if (!empty($t['aviso_admin'])): ?>
                                    <?php echo nl2br(htmlspecialchars($t['aviso_admin'], ENT_QUOTES, 'UTF-8')); ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
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
