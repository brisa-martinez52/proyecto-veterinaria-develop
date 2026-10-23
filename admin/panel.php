<?php
require_once '../includes/config.php';
requireAdmin();

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'stats';
$producto_error = '';
$producto_edicion_id = null;
$modo_edicion_producto = false;
$mostrar_form_producto = false;
$producto_form_values = [
    'nombre' => '',
    'categoria' => 'Alimentos',
    'precio' => '',
    'stock' => '',
    'descripcion' => '',
    'imagen_url' => ''
];

// Agregar producto
if (isset($_POST['agregar_producto'])) {
    $mostrar_form_producto = true;
    foreach ($producto_form_values as $campo => $valor) {
        $valor_enviado = $_POST[$campo] ?? '';
        $producto_form_values[$campo] = is_scalar($valor_enviado) ? (string)$valor_enviado : '';
    }

    $precio_ingresado = trim((string)($producto_form_values['precio']));
    $stock_ingresado = trim((string)($producto_form_values['stock']));
    $precio_valido = is_numeric($precio_ingresado) && is_finite((float)$precio_ingresado) && (float)$precio_ingresado >= 0;
    $stock_validado = filter_var($stock_ingresado, FILTER_VALIDATE_INT);
    $errores_producto = [];

    if (!$precio_valido) {
        $errores_producto[] = 'El precio debe ser un número igual o mayor que cero.';
    }
    if ($stock_validado === false || $stock_validado < 0) {
        $errores_producto[] = 'El stock debe ser un número entero igual o mayor que cero.';
    }

    if ($errores_producto) {
        $producto_error = implode(' ', $errores_producto);
    } else {
        $nombre = sanitize($producto_form_values['nombre']);
        $descripcion = sanitize($producto_form_values['descripcion']);
        $precio = (float)$precio_ingresado;
        $stock = $stock_validado;
        $categoria = sanitize($producto_form_values['categoria']);
        $imagen_url = sanitize($producto_form_values['imagen_url']);

        mysqli_query($conn, "INSERT INTO productos (nombre, descripcion, precio, stock, categoria, imagen_url)
                            VALUES ('$nombre', '$descripcion', $precio, $stock, '$categoria', '$imagen_url')");
        $mostrar_form_producto = false;
    }
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

// Actualizar producto
if (isset($_POST['editar_producto'])) {
    $producto_id = filter_var($_POST['producto_id'] ?? '', FILTER_VALIDATE_INT);
    $producto_edicion_id = $producto_id !== false ? $producto_id : null;
    $modo_edicion_producto = true;
    foreach ($producto_form_values as $campo => $valor) {
        $valor_enviado = $_POST[$campo] ?? '';
        $producto_form_values[$campo] = is_scalar($valor_enviado) ? trim((string)$valor_enviado) : '';
    }

    $precio_ingresado = $producto_form_values['precio'];
    $stock_validado = filter_var($producto_form_values['stock'], FILTER_VALIDATE_INT);
    $precio_valido = is_numeric($precio_ingresado) && is_finite((float)$precio_ingresado) && (float)$precio_ingresado >= 0;
    $errores_producto = [];
    $mostrar_form_producto = true;

    if (!$precio_valido) {
        $errores_producto[] = 'El precio debe ser un número igual o mayor que cero.';
    }
    if ($stock_validado === false || $stock_validado < 0) {
        $errores_producto[] = 'El stock debe ser un número entero igual o mayor que cero.';
    }

    if ($producto_edicion_id === null || $producto_edicion_id <= 0) {
        $errores_producto[] = 'No se pudo identificar el producto que querés actualizar.';
    }

    if ($errores_producto) {
        $producto_error = implode(' ', $errores_producto);
    } else {
        $nombre = sanitize($producto_form_values['nombre']);
        $descripcion = sanitize($producto_form_values['descripcion']);
        $precio = (float)$precio_ingresado;
        $categoria = sanitize($producto_form_values['categoria']);
        $imagen_url = sanitize($producto_form_values['imagen_url']);
        $stmt = mysqli_prepare($conn, 'UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria = ?, imagen_url = ? WHERE id = ?');

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssdissi', $nombre, $descripcion, $precio, $stock_validado, $categoria, $imagen_url, $producto_edicion_id);
            if (!mysqli_stmt_execute($stmt)) {
                $producto_error = 'No se pudo actualizar el producto. Verificá los datos e intentá nuevamente.';
                $mostrar_form_producto = true;
            } else {
                redirect('panel.php?tab=productos');
            }
            mysqli_stmt_close($stmt);
        } else {
            $producto_error = 'No se pudo actualizar el producto. Verificá los datos e intentá nuevamente.';
            $mostrar_form_producto = true;
        }
    }
}

if (isset($_GET['editar_producto']) && !isset($_POST['editar_producto'])) {
    $producto_id = filter_var($_GET['editar_producto'], FILTER_VALIDATE_INT);
    if ($producto_id !== false && $producto_id > 0) {
        $result = mysqli_query($conn, 'SELECT * FROM productos WHERE id = ' . (int)$producto_id);
        $producto = $result ? mysqli_fetch_assoc($result) : false;
        if ($producto) {
            $producto_edicion_id = (int)$producto['id'];
            $modo_edicion_producto = true;
            foreach ($producto_form_values as $campo => $valor) {
                $producto_form_values[$campo] = (string)($producto[$campo] ?? '');
            }
        } else {
            $producto_error = 'No se encontró el producto que querés editar.';
        }
    } else {
        $producto_error = 'El producto seleccionado no es válido.';
    }
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
            <?php if ($producto_error): ?>
                <div class="alert alert-error" role="alert"><?php echo htmlspecialchars($producto_error); ?></div>
            <?php endif; ?>
            <?php if (!$modo_edicion_producto): ?>
                <button onclick="document.getElementById('formProducto').style.display='block'" class="btn btn-primary mb-20">+ Agregar Producto</button>
            <?php endif; ?>

            <div id="formProducto" class="card mb-20" style="display: <?php echo ($modo_edicion_producto || $mostrar_form_producto) ? 'block' : 'none'; ?>;">
                <h2><?php echo $modo_edicion_producto ? 'Editar Producto' : 'Nuevo Producto'; ?></h2>
                <form method="POST" id="formNuevoProducto" novalidate>
                    <input type="hidden" name="tab" value="productos">
                    <?php if ($modo_edicion_producto): ?>
                        <input type="hidden" name="producto_id" value="<?php echo (int)$producto_edicion_id; ?>">
                    <?php endif; ?>
                    <div class="grid grid-2">
                        <div class="form-group"><label class="form-label">Nombre</label><input name="nombre" class="form-input" value="<?php echo htmlspecialchars($producto_form_values['nombre']); ?>" required></div>
                        <div class="form-group">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select">
                                <?php if (!in_array($producto_form_values['categoria'], ['Alimentos', 'Accesorios', 'Juguetes', 'Higiene'], true)): ?>
                                    <option selected><?php echo htmlspecialchars($producto_form_values['categoria']); ?></option>
                                <?php endif; ?>
                                <?php foreach (['Alimentos', 'Accesorios', 'Juguetes', 'Higiene'] as $categoria_opcion): ?>
                                    <option <?php echo $producto_form_values['categoria'] === $categoria_opcion ? 'selected' : ''; ?>><?php echo $categoria_opcion; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">Precio</label><input type="number" min="0" step="0.01" name="precio" class="form-input" value="<?php echo htmlspecialchars($producto_form_values['precio']); ?>" required></div>
                        <div class="form-group"><label class="form-label">Stock</label><input type="number" min="0" step="1" name="stock" class="form-input" value="<?php echo htmlspecialchars($producto_form_values['stock']); ?>" required></div>
                        <div class="form-group" style="grid-column: span 2;"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-textarea" required><?php echo htmlspecialchars($producto_form_values['descripcion']); ?></textarea></div>
                        <div class="form-group" style="grid-column: span 2;"><label class="form-label">URL Imagen</label><input name="imagen_url" class="form-input" value="<?php echo htmlspecialchars($producto_form_values['imagen_url']); ?>"></div>
                    </div>
                    <div id="errorValidacionProducto" class="alert alert-error" role="alert" style="display:none;"></div>
                    <button type="submit" name="<?php echo $modo_edicion_producto ? 'editar_producto' : 'agregar_producto'; ?>" class="btn btn-success mt-20"><?php echo $modo_edicion_producto ? 'Guardar cambios' : 'Guardar'; ?></button>
                    <a href="panel.php?tab=productos" class="btn btn-secondary mt-20">Cancelar</a>
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
                            <td><?php echo htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($p['categoria'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>$<?php echo number_format($p['precio'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if (!empty($p['imagen_url'])): ?>
                                    <img src="<?php echo htmlspecialchars(asset_path($p['imagen_url']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8'); ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;margin-bottom:8px;display:block;">
                                <?php else: ?>
                                    <div style="width:60px;height:60px;border-radius:8px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;color:#999;margin-bottom:8px;">Sin foto</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo (int)$p['stock']; ?></td>
                            <td>
                                <a href="panel.php?tab=productos&amp;editar_producto=<?php echo (int)$p['id']; ?>" class="btn btn-secondary">Editar</a>
                                <a href="?tab=productos&eliminar_producto=<?php echo (int)$p['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                            </td>
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
    <script>
        const formNuevoProducto = document.getElementById('formNuevoProducto');
        if (formNuevoProducto) {
            formNuevoProducto.addEventListener('submit', function (event) {
                const campos = [
                    { nombre: 'nombre', etiqueta: 'Completa el nombre del producto.' },
                    { nombre: 'precio', etiqueta: 'El precio debe ser igual o mayor que cero.' },
                    { nombre: 'stock', etiqueta: 'El stock debe ser un entero igual o mayor que cero.' },
                    { nombre: 'descripcion', etiqueta: 'Completa la descripción del producto.' }
                ];
                const errores = [];

                campos.forEach(function (campo) {
                    const input = formNuevoProducto.elements[campo.nombre];
                    const valor = input.value.trim();
                    let valorValido = valor !== '';

                    if (campo.nombre === 'precio') {
                        valorValido = valorValido && Number.isFinite(Number(valor)) && Number(valor) >= 0;
                    } else if (campo.nombre === 'stock') {
                        valorValido = valorValido &&
                            Number.isFinite(Number(valor)) &&
                            Number.isInteger(Number(valor)) &&
                            Number(valor) >= 0;
                    }

                    input.style.borderColor = valorValido ? '' : '#c92a2a';
                    if (!valorValido) {
                        errores.push(campo.etiqueta);
                    }
                });

                const mensaje = document.getElementById('errorValidacionProducto');
                if (errores.length > 0) {
                    event.preventDefault();
                    mensaje.textContent = errores.join(' ');
                    mensaje.style.display = 'block';
                } else {
                    mensaje.textContent = '';
                    mensaje.style.display = 'none';
                }
            });
        }

    </script>
</body>
</html>
