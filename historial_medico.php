<?php
require_once 'includes/config.php';
requireLogin();

$usuario_id = $_SESSION['usuario_id'];
$mascota_id = isset($_GET['mascota']) ? (int)$_GET['mascota'] : 0;

$mascotas = mysqli_query($conn, "SELECT * FROM mascotas WHERE usuario_id=$usuario_id");
$historial = [];
$mascota_seleccionada = null;

if ($mascota_id) {
    $result = mysqli_query($conn, "SELECT * FROM mascotas WHERE id=$mascota_id AND usuario_id=$usuario_id");
    $mascota_seleccionada = mysqli_fetch_assoc($result);
    
    $historial = mysqli_query($conn, "SELECT h.*, v.nombre as vet_nombre, v.apellido as vet_apellido 
                                      FROM historial_medico h 
                                      JOIN veterinarios v ON h.veterinario_id = v.id 
                                      WHERE h.mascota_id=$mascota_id 
                                      ORDER BY h.fecha DESC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Médico - Patitas</title>
    <link rel="stylesheet" href="css/estilos.css?v=202607011">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <h1>Historial Médico</h1>

        <div class="card mb-20">
            <div class="form-group">
                <label class="form-label">Selecciona una mascota</label>
                <select class="form-select" onchange="window.location='historial_medico.php?mascota='+this.value">
                    <option value="">-- Selecciona una mascota --</option>
                    <?php while ($m = mysqli_fetch_assoc($mascotas)): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo $mascota_id == $m['id'] ? 'selected' : ''; ?>>
                            <?php echo $m['nombre'].' - '.$m['raza']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <?php if ($mascota_seleccionada): ?>
            <div class="card-celeste mb-20">
                <h2><?php echo $mascota_seleccionada['nombre']; ?></h2>
                <div class="grid grid-4">
                    <p><strong>Raza:</strong> <?php echo $mascota_seleccionada['raza']; ?></p>
                    <p><strong>Sexo:</strong> <?php echo $mascota_seleccionada['sexo']; ?></p>
                    <p><strong>Edad:</strong> <?php echo $mascota_seleccionada['edad']; ?> años</p>
                    <p><strong>Peso:</strong> <?php echo $mascota_seleccionada['peso']; ?> kg</p>
                </div>
            </div>

            <?php if (mysqli_num_rows($historial) == 0): ?>
                <div class="card text-center"><p>No hay registros médicos para esta mascota</p></div>
            <?php else: ?>
                <h2>Registros Médicos</h2>
                <?php while ($h = mysqli_fetch_assoc($historial)): ?>
                    <div class="card mb-20">
                        <div class="flex-between mb-20">
                            <h3>📅 <?php echo date('d/m/Y', strtotime($h['fecha'])); ?></h3>
                            <p><strong>Dr. <?php echo $h['vet_nombre'].' '.$h['vet_apellido']; ?></strong></p>
                        </div>
                        <div class="grid grid-2">
                            <div>
                                <p><strong>Diagnóstico:</strong></p>
                                <p><?php echo $h['diagnostico']; ?></p>
                            </div>
                            <div>
                                <p><strong>Tratamiento:</strong></p>
                                <p><?php echo $h['tratamiento']; ?></p>
                            </div>
                            <?php if ($h['vacunas']): ?>
                                <div>
                                    <p><strong>Vacunas:</strong></p>
                                    <p><?php echo $h['vacunas']; ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if ($h['estudios']): ?>
                                <div>
                                    <p><strong>Estudios:</strong></p>
                                    <p><?php echo $h['estudios']; ?></p>
                                </div>
                            <?php endif; ?>
                            <div>
                                <p><strong>Peso:</strong> <?php echo $h['peso']; ?> kg</p>
                            </div>
                            <?php if ($h['observaciones']): ?>
                                <div style="grid-column: span 2;">
                                    <p><strong>Observaciones:</strong></p>
                                    <p><?php echo $h['observaciones']; ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
