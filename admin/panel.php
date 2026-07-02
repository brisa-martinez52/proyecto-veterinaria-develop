<?php
require_once '../includes/config.php';
requireAdmin();

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'stats';

// Agregar producto
if (isset($_POST['agregar_producto'])) {
    $nombre = sanitize($_POST['nombre']);
    $descripcion = sanitize($_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $categoria = sanitize($_POST['categoria']);
    $imagen_url = sanitize($_POST['imagen_url']);
    
    mysqli_query($conn, "INSERT INTO productos (nombre, descripcion, precio, stock, categoria, imagen_url) 
                        VALUES ('$nombre', '$descripcion', $precio, $stock, '$categoria', '$imagen_url')");
}

// Agregar veterinario
if (isset($_POST['agregar_veterinario'])) {
    $nombre = sanitize($_POST['nombre']);
    $apellido = sanitize($_POST['apellido']);
    $especialidad = sanitize($_POST['especialidad']);
    $telefono = sanitize($_POST['telefono']);
    
    mysqli_query($conn, "INSERT INTO veterinarios (nombre, apellido, especialidad, telefono) 
                        VALUES ('$nombre', '$apellido', '$especialidad', '$telefono')");
}

// Actualizar stock
if (isset($_POST['actualizar_stock'])) {
    $producto_id = (int)$_POST['producto_id'];
    $stock = (int)$_POST['stock'];
    mysqli_query($conn, "UPDATE productos SET stock=$stock WHERE id=$producto_id");
}

// Editar imagen de producto
if (isset($_POST['editar_producto'])) {
    $producto_id = (int)$_POST['producto_id'];
    $imagen_url = sanitize($_POST['imagen_url']);
    mysqli_query($conn, "UPDATE productos SET imagen_url='$imagen_url' WHERE id=$producto_id");
}

// Eliminar producto
if (isset($_GET['eliminar_producto'])) {
    $id = (int)$_GET['eliminar_producto'];
    mysqli_query($conn, "DELETE FROM productos WHERE id=$id");
    redirect('panel.php?tab=productos');
}

// Estadísticas
$stats = [
    'usuarios' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios"))['total'],
    'mascotas' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mascotas"))['total'],
    'productos' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM productos"))['total'],
    'turnos_activos' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM turnos WHERE estado='activo'"))['total']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Patitas</title>
    <link rel="stylesheet" href="../css/estilos.css?v=202607011">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container">
        <h1 style="color: #ff6b6b;">🔧 Panel de Administración</h1>

        <!-- Tabs -->
        <div style="display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap;">
            <?php foreach (['stats'=>'Estadísticas', 'productos'=>'Productos', 'veterinarios'=>'Veterinarios', 'turnos'=>'Turnos', 'mascotas'=>'Mascotas'] as $key => $label): ?>
                <a href="?tab=<?php echo $key; ?>" class="btn <?php echo $tab == $key ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo $label; ?></a>
            <?php endforeach; ?>
        </div>

        <!-- Stats Tab -->
        <?php if ($tab == 'stats'): ?>
            <div class="grid grid-4">
                <div class="card-celeste text-center"><h2><?php echo $stats['usuarios']; ?></h2><p>Usuarios</p></div>
                <div class="card-celeste text-center"><h2><?php echo $stats['mascotas']; ?></h2><p>Mascotas</p></div>
                <div class="card-celeste text-center"><h2><?php echo $stats['productos']; ?></h2><p>Productos</p></div>
                <div class="card-celeste text-center"><h2><?php echo $stats['turnos_activos']; ?></h2><p>Turnos Activos</p></div>
            </div>
        <?php endif; ?>

        <!-- Productos Tab -->
        <?php if ($tab == 'productos'): ?>
            <button onclick="document.getElementById('formProducto').style.display='block'" class="btn btn-primary mb-20">+ Agregar Producto</button>
            
            <div id="formProducto" class="card mb-20" style="display: none;">
                <h2>Nuevo Producto</h2>
                <form method="POST">
                    <div class="grid grid-2">
                        <div class="form-group"><label class="form-label">Nombre</label><input name="nombre" class="form-input" required></div>
                        <div class="form-group">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select">
                                <option>Alimentos</option><option>Accesorios</option><option>Juguetes</option><option>Higiene</option>
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">Precio</label><input type="number" step="0.01" name="precio" class="form-input" required></div>
                        <div class="form-group"><label class="form-label">Stock</label><input type="number" name="stock" class="form-input" required></div>
                        <div class="form-group" style="grid-column: span 2;"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-textarea" required></textarea></div>
                        <div class="form-group" style="grid-column: span 2;"><label class="form-label">URL Imagen</label><input name="imagen_url" class="form-input"></div>
                    </div>
                    <button type="submit" name="agregar_producto" class="btn btn-success mt-20">Guardar</button>
                </form>
            </div>

            <table>
                <thead><tr><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Imagen</th><th>Stock</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php
                    $productos = mysqli_query($conn, "SELECT * FROM productos ORDER BY nombre");
                    while ($p = mysqli_fetch_assoc($productos)):
                    ?>
                        <tr>
                            <td><?php echo $p['nombre']; ?></td>
                            <td><?php echo $p['categoria']; ?></td>
                            <td>$<?php echo number_format($p['precio'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if (!empty($p['imagen_url'])): ?>
                                    <img src="<?php echo asset_path($p['imagen_url']); ?>" alt="<?php echo $p['nombre']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-bottom: 8px; display: block;">
                                <?php else: ?>
                                    <div style="width: 60px; height: 60px; border-radius: 8px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999; margin-bottom: 8px;">Sin foto</div>
                                <?php endif; ?>
                                <form method="POST" style="display:flex; flex-direction:column; gap:6px; min-width:220px;">
                                    <input type="hidden" name="producto_id" value="<?php echo $p['id']; ?>">
                                    <input type="text" name="imagen_url" value="<?php echo htmlspecialchars($p['imagen_url'] ?? ''); ?>" style="padding:6px; border:1px solid #ddd; border-radius:6px;" placeholder="Imagenes/archivo.png">
                                    <button type="submit" name="editar_producto" class="btn btn-secondary" style="padding:6px 10px;">Actualizar imagen</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="producto_id" value="<?php echo $p['id']; ?>">
                                    <input type="number" name="stock" value="<?php echo $p['stock']; ?>" style="width:70px;padding:5px;" onchange="this.form.submit()">
                                    <input type="hidden" name="actualizar_stock" value="1">
                                </form>
                            </td>
                            <td><a href="?tab=productos&eliminar_producto=<?php echo $p['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Veterinarios Tab -->
        <?php if ($tab == 'veterinarios'): ?>
            <button onclick="document.getElementById('formVet').style.display='block'" class="btn btn-primary mb-20">+ Agregar Veterinario</button>
            
            <div id="formVet" class="card mb-20" style="display: none;">
                <h2>Nuevo Veterinario</h2>
                <form method="POST">
                    <div class="grid grid-2">
                        <div class="form-group"><label class="form-label">Nombre</label><input name="nombre" class="form-input" required></div>
                        <div class="form-group"><label class="form-label">Apellido</label><input name="apellido" class="form-input" required></div>
                        <div class="form-group"><label class="form-label">Especialidad</label><input name="especialidad" class="form-input" required></div>
                        <div class="form-group"><label class="form-label">Teléfono</label><input name="telefono" class="form-input" required></div>
                    </div>
                    <button type="submit" name="agregar_veterinario" class="btn btn-success mt-20">Guardar</button>
                </form>
            </div>

            <div class="grid grid-3">
                <?php
                $vets = mysqli_query($conn, "SELECT * FROM veterinarios");
                while ($v = mysqli_fetch_assoc($vets)):
                ?>
                    <div class="card-celeste">
                        <h3><?php echo $v['nombre'].' '.$v['apellido']; ?></h3>
                        <p><strong>Especialidad:</strong> <?php echo $v['especialidad']; ?></p>
                        <p><strong>Teléfono:</strong> <?php echo $v['telefono']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <!-- Turnos Tab -->
        <?php if ($tab == 'turnos'): ?>
            <table>
                <thead><tr><th>Usuario</th><th>Mascota</th><th>Veterinario</th><th>Fecha</th><th>Hora</th><th>Estado</th></tr></thead>
                <tbody>
                    <?php
                    $turnos = mysqli_query($conn, "SELECT t.*, u.nombre as usuario_nombre, m.nombre as mascota_nombre, v.nombre as vet_nombre 
                                                    FROM turnos t 
                                                    JOIN usuarios u ON t.usuario_id = u.id 
                                                    JOIN mascotas m ON t.mascota_id = m.id 
                                                    JOIN veterinarios v ON t.veterinario_id = v.id 
                                                    ORDER BY t.fecha DESC, t.hora DESC");
                    while ($t = mysqli_fetch_assoc($turnos)):
                    ?>
                        <tr>
                            <td><?php echo $t['usuario_nombre']; ?></td>
                            <td><?php echo $t['mascota_nombre']; ?></td>
                            <td><?php echo $t['vet_nombre']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($t['fecha'])); ?></td>
                            <td><?php echo $t['hora']; ?></td>
                            <td><?php echo $t['estado']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Mascotas Tab -->
        <?php if ($tab == 'mascotas'): ?>
            <table>
                <thead><tr><th>Nombre</th><th>Raza</th><th>Sexo</th><th>Edad</th><th>Peso</th><th>Dueño</th></tr></thead>
                <tbody>
                    <?php
                    $mascotas = mysqli_query($conn, "SELECT m.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido 
                                                     FROM mascotas m 
                                                     JOIN usuarios u ON m.usuario_id = u.id 
                                                     ORDER BY m.nombre");
                    while ($m = mysqli_fetch_assoc($mascotas)):
                    ?>
                        <tr>
                            <td><?php echo $m['nombre']; ?></td>
                            <td><?php echo $m['raza']; ?></td>
                            <td><?php echo $m['sexo']; ?></td>
                            <td><?php echo $m['edad']; ?></td>
                            <td><?php echo $m['peso']; ?></td>
                            <td><?php echo $m['usuario_nombre'].' '.$m['usuario_apellido']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
