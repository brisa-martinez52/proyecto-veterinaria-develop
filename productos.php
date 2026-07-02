<?php
require_once 'includes/config.php';

// Inicializar carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar al carrito
if (isset($_POST['agregar_carrito'])) {
    $producto_id = $_POST['producto_id'];
    
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]['cantidad']++;
    } else {
        $query = "SELECT * FROM productos WHERE id = $producto_id";
        $result = mysqli_query($conn, $query);
        $producto = mysqli_fetch_assoc($result);
        $_SESSION['carrito'][$producto_id] = [
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'cantidad' => 1
        ];
    }
}

// Actualizar cantidad
if (isset($_POST['actualizar_cantidad'])) {
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    if ($cantidad > 0) {
        $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
    } else {
        unset($_SESSION['carrito'][$producto_id]);
    }
}

// Eliminar del carrito
if (isset($_POST['eliminar'])) {
    $producto_id = $_POST['producto_id'];
    unset($_SESSION['carrito'][$producto_id]);
}

// Vaciar carrito
if (isset($_POST['vaciar_carrito'])) {
    $_SESSION['carrito'] = [];
}

// Finalizar compra
if (isset($_POST['finalizar_compra'])) {
    if (isLoggedIn() && count($_SESSION['carrito']) > 0) {
        $usuario_id = $_SESSION['usuario_id'];
        $total = 0;
        
        foreach ($_SESSION['carrito'] as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        $query = "INSERT INTO compras (usuario_id, total) VALUES ($usuario_id, $total)";
        if (mysqli_query($conn, $query)) {
            $compra_id = mysqli_insert_id($conn);
            
            foreach ($_SESSION['carrito'] as $item) {
                $query2 = "INSERT INTO compra_items (compra_id, producto_id, cantidad, precio_unitario) 
                          VALUES ($compra_id, {$item['id']}, {$item['cantidad']}, {$item['precio']})";
                mysqli_query($conn, $query2);
                
                // Actualizar stock
                $query3 = "UPDATE productos SET stock = stock - {$item['cantidad']} WHERE id = {$item['id']}";
                mysqli_query($conn, $query3);
            }
            
            $_SESSION['carrito'] = [];
            $success = '¡Compra realizada con éxito!';
        }
    }
}

// Obtener productos
$categoria_filtro = isset($_GET['categoria']) ? sanitize($_GET['categoria']) : 'Todos';
$where = $categoria_filtro != 'Todos' ? "WHERE categoria = '$categoria_filtro'" : '';
$query = "SELECT * FROM productos $where ORDER BY nombre";
$productos = mysqli_query($conn, $query);

// Obtener categorías
$query_cat = "SELECT DISTINCT categoria FROM productos";
$categorias_result = mysqli_query($conn, $query_cat);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Patitas</title>
    <link rel="stylesheet" href="css/estilos.css?v=202607011">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="flex-between mb-20">
            <h1>Tienda de Productos</h1>
            <button onclick="toggleCarrito()" class="btn btn-primary" id="btnCarrito">
                🛒 Carrito (<?php echo count($_SESSION['carrito']); ?>)
            </button>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Filtros de categoría -->
        <div style="margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="productos.php?categoria=Todos" class="btn <?php echo $categoria_filtro == 'Todos' ? 'btn-primary' : 'btn-secondary'; ?>">Todos</a>
            <?php while ($cat = mysqli_fetch_assoc($categorias_result)): ?>
                <a href="productos.php?categoria=<?php echo $cat['categoria']; ?>" 
                   class="btn <?php echo $categoria_filtro == $cat['categoria'] ? 'btn-primary' : 'btn-secondary'; ?>">
                    <?php echo $cat['categoria']; ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Grid de productos -->
        <div class="grid grid-3">
            <?php while ($producto = mysqli_fetch_assoc($productos)): ?>
                <?php
                $imagen_producto = !empty($producto['imagen_url']) ? $producto['imagen_url'] : '';

                if (empty($imagen_producto)) {
                    $nombre_normalizado = strtolower($producto['nombre']);
                    $categoria_normalizada = strtolower($producto['categoria']);
                    $es_shampoo = strpos($nombre_normalizado, 'shampoo') !== false || $categoria_normalizada === 'higiene';
                    if ($es_shampoo) {
                        $imagen_producto = 'Imagenes/foto-shampoo.png';
                    }
                }
                ?>
                <div class="card">
                    <?php if ($imagen_producto): ?>
                        <img src="<?php echo asset_path($imagen_producto); ?>" alt="<?php echo $producto['nombre']; ?>" 
                             style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 15px;">
                    <?php endif; ?>
                    <h3><?php echo $producto['nombre']; ?></h3>
                    <p style="color: #666; font-size: 14px;"><?php echo $producto['descripcion']; ?></p>
                    <div style="display: inline-block; background-color: #acf2f5; padding: 5px 15px; border-radius: 15px; font-size: 12px; font-weight: bold; margin: 10px 0;">
                        <?php echo $producto['categoria']; ?>
                    </div>
                    <div class="flex-between mt-20">
                        <span style="font-size: 24px; font-weight: bold; color: #51cf66;">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></span>
                        <span>Stock: <?php echo $producto['stock']; ?></span>
                    </div>
                    <form method="POST" class="mt-20">
                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                        <button type="submit" name="agregar_carrito" class="btn btn-primary" style="width: 100%;" 
                                <?php echo $producto['stock'] == 0 ? 'disabled' : ''; ?>>
                            <?php echo $producto['stock'] == 0 ? 'Sin Stock' : 'Agregar al Carrito'; ?>
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Carrito Sidebar -->
    <div id="carritoOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 2000;" onclick="toggleCarrito()"></div>
    <div id="carritoSidebar" style="display: none; position: fixed; top: 0; right: 0; width: 450px; height: 100vh; background-color: white; box-shadow: -4px 0 20px rgba(0,0,0,0.2); z-index: 2001; overflow-y: auto;">
        <div style="background-color: #83d4d7; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0;">Carrito de Compras</h2>
            <button onclick="toggleCarrito()" style="background: none; border: none; font-size: 28px; cursor: pointer;">✕</button>
        </div>
        
        <div style="padding: 20px;">
            <?php if (count($_SESSION['carrito']) == 0): ?>
                <p style="text-align: center; padding: 40px; color: #666;">Tu carrito está vacío</p>
            <?php else: ?>
                <?php 
                $total = 0;
                foreach ($_SESSION['carrito'] as $item): 
                    $total += $item['precio'] * $item['cantidad'];
                ?>
                    <div class="card" style="margin-bottom: 15px;">
                        <h4><?php echo $item['nombre']; ?></h4>
                        <p style="color: #51cf66; font-weight: bold; font-size: 18px;">$<?php echo number_format($item['precio'], 0, ',', '.'); ?></p>
                        <div class="flex-between mt-20">
                            <form method="POST" style="display: flex; align-items: center; gap: 10px;">
                                <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="actualizar_cantidad" class="btn btn-secondary" style="padding: 5px 15px;" 
                                        onclick="this.form.cantidad.value = parseInt(this.form.cantidad.value) - 1;">-</button>
                                <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" 
                                       style="width: 50px; text-align: center; border: 2px solid #acf2f5; border-radius: 10px; padding: 5px;">
                                <button type="submit" name="actualizar_cantidad" class="btn btn-secondary" style="padding: 5px 15px;"
                                        onclick="this.form.cantidad.value = parseInt(this.form.cantidad.value) + 1;">+</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="eliminar" style="background: none; border: none; font-size: 20px; cursor: pointer;">🗑️</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div style="border-top: 2px solid #e0e0e0; padding-top: 20px; margin-top: 20px;">
                    <div class="flex-between mb-20">
                        <h3>Total:</h3>
                        <h3>$<?php echo number_format($total, 0, ',', '.'); ?></h3>
                    </div>
                    <form method="POST">
                        <button type="submit" name="finalizar_compra" class="btn btn-success" style="width: 100%; margin-bottom: 10px;" 
                                <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                            <?php echo isLoggedIn() ? 'Finalizar Compra' : 'Inicia sesión para comprar'; ?>
                        </button>
                        <button type="submit" name="vaciar_carrito" class="btn btn-secondary" style="width: 100%;">Vaciar Carrito</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function toggleCarrito() {
        const overlay = document.getElementById('carritoOverlay');
        const sidebar = document.getElementById('carritoSidebar');
        if (overlay.style.display === 'none') {
            overlay.style.display = 'block';
            sidebar.style.display = 'block';
        } else {
            overlay.style.display = 'none';
            sidebar.style.display = 'none';
        }
    }
    </script>

    <style>
    @media (max-width: 768px) {
        #carritoSidebar { width: 90% !important; max-width: 400px; }
    }
    </style>
</body>
</html>
