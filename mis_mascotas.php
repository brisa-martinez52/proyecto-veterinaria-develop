<?php
require_once 'includes/config.php';
requireLogin();

$usuario_id = $_SESSION['usuario_id'];
$success = $error = '';

// Agregar/Editar mascota
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_mascota'])) {
    $nombre = sanitize($_POST['nombre']);
    $raza = sanitize($_POST['raza']);
    $sexo = sanitize($_POST['sexo']);
    $edad = (int)$_POST['edad'];
    $peso = (float)$_POST['peso'];
    
    if (isset($_POST['mascota_id']) && $_POST['mascota_id']) {
        $id = (int)$_POST['mascota_id'];
        $query = "UPDATE mascotas SET nombre='$nombre', raza='$raza', sexo='$sexo', edad=$edad, peso=$peso WHERE id=$id AND usuario_id=$usuario_id";
    } else {
        $query = "INSERT INTO mascotas (usuario_id, nombre, raza, sexo, edad, peso) VALUES ($usuario_id, '$nombre', '$raza', '$sexo', $edad, $peso)";
    }
    mysqli_query($conn, $query) ? $success = '¡Mascota guardada!' : $error = 'Error al guardar';
}

// Eliminar mascota
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    mysqli_query($conn, "DELETE FROM mascotas WHERE id=$id AND usuario_id=$usuario_id");
    redirect('mis_mascotas.php');
}

// Obtener mascotas
$mascotas = mysqli_query($conn, "SELECT * FROM mascotas WHERE usuario_id=$usuario_id ORDER BY nombre");

// Mascota a editar
$mascota_editar = null;
if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    $result = mysqli_query($conn, "SELECT * FROM mascotas WHERE id=$id AND usuario_id=$usuario_id");
    $mascota_editar = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Mascotas - Patitas</title>
    <link rel="stylesheet" href="css/estilos.css?v=202607011">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="flex-between mb-20">
            <h1>Mis Mascotas</h1>
            <button onclick="document.getElementById('formMascota').style.display='block'" class="btn btn-primary">+ Agregar Mascota</button>
        </div>

        <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

        <!-- Formulario -->
        <div id="formMascota" class="card mb-20" style="display:<?php echo $mascota_editar ? 'block' : 'none'; ?>">
            <h2><?php echo $mascota_editar ? 'Editar' : 'Nueva'; ?> Mascota</h2>
            <form method="POST">
                <input type="hidden" name="mascota_id" value="<?php echo $mascota_editar['id'] ?? ''; ?>">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-input" required value="<?php echo $mascota_editar['nombre'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Raza</label>
                        <input type="text" name="raza" class="form-input" required value="<?php echo $mascota_editar['raza'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sexo</label>
                        <select name="sexo" class="form-select">
                            <option <?php echo ($mascota_editar['sexo'] ?? '') == 'Macho' ? 'selected' : ''; ?>>Macho</option>
                            <option <?php echo ($mascota_editar['sexo'] ?? '') == 'Hembra' ? 'selected' : ''; ?>>Hembra</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Edad (años)</label>
                        <input type="number" name="edad" class="form-input" required value="<?php echo $mascota_editar['edad'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" step="0.1" name="peso" class="form-input" required value="<?php echo $mascota_editar['peso'] ?? ''; ?>">
                    </div>
                </div>
                <div class="flex gap-10 mt-20">
                    <button type="submit" name="guardar_mascota" class="btn btn-success">Guardar</button>
                    <button type="button" onclick="window.location='mis_mascotas.php'" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>

        <!-- Lista de mascotas -->
        <?php if (mysqli_num_rows($mascotas) == 0): ?>
            <div class="card text-center"><p>No tienes mascotas registradas aún</p></div>
        <?php else: ?>
            <div class="grid grid-3">
                <?php while ($mascota = mysqli_fetch_assoc($mascotas)): ?>
                    <div class="card-celeste">
                        <h3><?php echo $mascota['nombre']; ?></h3>
                        <p><strong>Raza:</strong> <?php echo $mascota['raza']; ?></p>
                        <p><strong>Sexo:</strong> <?php echo $mascota['sexo']; ?></p>
                        <p><strong>Edad:</strong> <?php echo $mascota['edad']; ?> años</p>
                        <p><strong>Peso:</strong> <?php echo $mascota['peso']; ?> kg</p>
                        <div class="flex gap-10 mt-20">
                            <a href="?editar=<?php echo $mascota['id']; ?>" class="btn btn-secondary">Editar</a>
                            <a href="?eliminar=<?php echo $mascota['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar mascota?')">Eliminar</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
