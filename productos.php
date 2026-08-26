<?php
require_once 'includes/config.php';
?>

<?php
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
                $query2 = "INSERT INTO compra_items 
                           (compra_id, producto_id, cantidad, precio_unitario) 
                           VALUES ($compra_id, {$item['id']}, {$item['cantidad']}, {$item['precio']})";

                mysqli_query($conn, $query2);

                // Actualizar stock
                $query3 = "UPDATE productos 
                           SET stock = stock - {$item['cantidad']} 
                           WHERE id = {$item['id']}";

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

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <style>

        /* =====================================================
           ENCABEZADO
           ===================================================== */

        .productos-header {
            margin-bottom: 30px;
        }

        .productos-header h1 {
            margin-bottom: 8px;
            font-size: 36px;
        }

        .productos-subtitulo {
            color: #666;
            margin: 0;
            font-size: 16px;
        }


        /* =====================================================
           FILTROS
           ===================================================== */

        .filtros {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 35px;
        }

        .filtro {
            text-decoration: none;
            padding: 9px 18px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.25s ease;
        }

        .filtro-activo {
            background-color: #83d4d7;
            color: #000;
        }

        .filtro-inactivo {
            background-color: white;
            color: #555;
            border: 1px solid #d9eeee;
        }

        .filtro:hover {
            transform: translateY(-2px);
        }


        /* =====================================================
           TARJETAS DE PRODUCTOS
           ===================================================== */

        .producto-card {
            background-color: white;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 5px 7px 16px rgba(68, 68, 68, 0.12);
            margin-bottom: 20px;

            transition:
                transform 0.3s ease,
                box-shadow 0.3s ease;
        }


        /* =====================================================
           ANIMACIÓN AL HACER SCROLL
           ===================================================== */

        .producto-scroll {
            opacity: 0;
            transform: translateY(50px);

            transition:
                opacity 0.7s ease,
                transform 0.7s ease;
        }

        .producto-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }


        /* Los productos aparecen escalonados */

        .producto-scroll:nth-child(3n + 1) {
            transition-delay: 0.05s;
        }

        .producto-scroll:nth-child(3n + 2) {
            transition-delay: 0.15s;
        }

        .producto-scroll:nth-child(3n + 3) {
            transition-delay: 0.25s;
        }


        /* Efecto al pasar el mouse */

        .producto-card:hover {
            transform: translateY(-6px);
            box-shadow: 8px 12px 22px rgba(68, 68, 68, 0.18);
        }


        /* =====================================================
           IMAGEN DEL PRODUCTO
           ===================================================== */

        .producto-imagen {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 15px;
            margin-bottom: 15px;
            background-color: #f7ffff;
        }


        /* =====================================================
           INFORMACIÓN DEL PRODUCTO
           ===================================================== */

        .producto-nombre {
            margin: 5px 0 8px;
            font-size: 20px;
        }

        .producto-descripcion {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            min-height: 42px;
        }

        .producto-categoria {
            display: inline-block;
            background-color: #e4fafa;
            color: #3b7779;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 10px 0;
        }


        /* =====================================================
           PRECIO Y STOCK
           ===================================================== */

        .producto-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }

        .producto-precio {
            font-size: 25px;
            font-weight: bold;
            color: #3b8f92;
        }

        .stock {
            padding: 6px 11px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .stock-alto {
            background-color: #e3f7e8;
            color: #2f8a43;
        }

        .stock-bajo {
            background-color: #fff3d6;
            color: #9a6b00;
        }

        .stock-agotado {
            background-color: #ffe4e4;
            color: #c03939;
        }


        /* =====================================================
           BOTÓN CARRITO
           ===================================================== */

        .btn-carrito {
            width: 100%;
            margin-top: 18px;

            display: flex;
            justify-content: center;
            align-items: center;

            gap: 8px;
        }


        /* =====================================================
           BOTÓN DEL CARRITO SUPERIOR
           ===================================================== */

        .carrito-boton {
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .carrito-numero {
            background-color: #ff8f8f;
            color: white;

            min-width: 22px;
            height: 22px;

            border-radius: 50%;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            font-size: 11px;
            font-weight: bold;
        }


        /* =====================================================
           ELIMINAR DEL CARRITO
           ===================================================== */

        .eliminar-carrito {
            background: none;
            border: none;
            font-size: 18px;
            color: #e66;
            cursor: pointer;

            transition: transform 0.2s ease;
        }

        .eliminar-carrito:hover {
            transform: scale(1.15);
        }


        /* =====================================================
           RESPONSIVE
           ===================================================== */

        @media (max-width: 768px) {

            .productos-header h1 {
                font-size: 30px;
            }

            .producto-info {
                align-items: flex-start;
                flex-direction: column;
            }

            .producto-precio {
                font-size: 23px;
            }

        }

    </style>

</head>


<body>

<?php include 'includes/navbar.php'; ?>


<div class="container">


    <!-- =====================================================
         ENCABEZADO
         ===================================================== -->

    <div class="productos-header">

        <div class="flex-between">

            <div>

                <h1>
                    Tienda de Productos
                </h1>

                <p class="productos-subtitulo">
                    Todo lo que tu mascota necesita, en un solo lugar.
                </p>

            </div>


            <button onclick="toggleCarrito()"
                    class="btn btn-primary carrito-boton"
                    id="btnCarrito">

                <i class="fa-solid fa-cart-shopping"></i>

                Carrito

                <span class="carrito-numero">

                    <?php echo count($_SESSION['carrito']); ?>

                </span>

            </button>

        </div>

    </div>


    <!-- =====================================================
         MENSAJE DE COMPRA
         ===================================================== -->

    <?php if(isset($success)): ?>

        <div class="alert alert-success">

            <?php echo $success; ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         FILTROS
         ===================================================== -->

    <div class="filtros">

        <a href="productos.php?categoria=Todos"
           class="filtro <?php echo $categoria_filtro == 'Todos' ? 'filtro-activo' : 'filtro-inactivo'; ?>">

            Todos

        </a>


        <?php while ($cat = mysqli_fetch_assoc($categorias_result)): ?>

            <a href="productos.php?categoria=<?php echo $cat['categoria']; ?>"
               class="filtro <?php echo $categoria_filtro == $cat['categoria'] ? 'filtro-activo' : 'filtro-inactivo'; ?>">

                <?php echo $cat['categoria']; ?>

            </a>

        <?php endwhile; ?>

    </div>


    <!-- =====================================================
         PRODUCTOS
         ===================================================== -->

    <div class="grid grid-3">


        <?php while ($producto = mysqli_fetch_assoc($productos)): ?>

            <?php

            $imagen_producto = !empty($producto['imagen_url'])
                ? $producto['imagen_url']
                : '';


            if (empty($imagen_producto)) {

                $nombre_normalizado = strtolower($producto['nombre']);

                $categoria_normalizada =
                    strtolower($producto['categoria']);


                $es_shampoo =
                    strpos($nombre_normalizado, 'shampoo') !== false ||
                    $categoria_normalizada === 'higiene';


                if ($es_shampoo) {

                    $imagen_producto =
                        'Imagenes/foto-shampoo.png';

                }

            }

            ?>


            <!-- PRODUCTO -->

            <div class="producto-card producto-scroll">


                <?php if ($imagen_producto): ?>

                    <img src="<?php echo asset_path($imagen_producto); ?>"
                         alt="<?php echo $producto['nombre']; ?>"
                         class="producto-imagen">

                <?php endif; ?>


                <h3 class="producto-nombre">

                    <?php echo $producto['nombre']; ?>

                </h3>


                <p class="producto-descripcion">

                    <?php echo $producto['descripcion']; ?>

                </p>


                <span class="producto-categoria">

                    <?php echo $producto['categoria']; ?>

                </span>


                <div class="producto-info">


                    <!-- PRECIO -->

                    <span class="producto-precio">

                        $<?php echo number_format(
                            $producto['precio'],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </span>


                    <!-- STOCK -->

                    <?php if ($producto['stock'] == 0): ?>

                        <span class="stock stock-agotado">

                            <i class="fa-solid fa-circle-xmark"></i>

                            Sin stock

                        </span>


                    <?php elseif ($producto['stock'] <= 5): ?>

                        <span class="stock stock-bajo">

                            <i class="fa-solid fa-triangle-exclamation"></i>

                            Últimas unidades

                        </span>


                    <?php else: ?>

                        <span class="stock stock-alto">

                            <i class="fa-solid fa-circle-check"></i>

                            <?php echo $producto['stock']; ?>

                            disponibles

                        </span>

                    <?php endif; ?>


                </div>


                <!-- BOTÓN AGREGAR -->

                <form method="POST">

                    <input type="hidden"
                           name="producto_id"
                           value="<?php echo $producto['id']; ?>">


                    <button type="submit"
                            name="agregar_carrito"
                            class="btn btn-primary btn-carrito"
                            <?php echo $producto['stock'] == 0
                                ? 'disabled'
                                : ''; ?>>


                        <?php if ($producto['stock'] == 0): ?>

                            <i class="fa-solid fa-ban"></i>

                            Sin Stock


                        <?php else: ?>

                            <i class="fa-solid fa-cart-plus"></i>

                            Agregar al Carrito

                        <?php endif; ?>


                    </button>

                </form>


            </div>


        <?php endwhile; ?>


    </div>

</div>



<!-- =====================================================
     CARRITO OVERLAY
     ===================================================== -->

<div id="carritoOverlay"
     style="
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 2000;
     "
     onclick="toggleCarrito()">

</div>



<!-- =====================================================
     CARRITO SIDEBAR
     ===================================================== -->

<div id="carritoSidebar"
     style="
        display: none;
        position: fixed;
        top: 0;
        right: 0;
        width: 450px;
        height: 100vh;
        background-color: white;
        box-shadow: -4px 0 20px rgba(0,0,0,0.2);
        z-index: 2001;
        overflow-y: auto;
     ">


    <!-- CABECERA DEL CARRITO -->

    <div style="
        background-color: #83d4d7;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    ">

        <h2 style="margin: 0;">

            <i class="fa-solid fa-cart-shopping"></i>

            Carrito de Compras

        </h2>


        <button onclick="toggleCarrito()"
                style="
                    background: none;
                    border: none;
                    font-size: 28px;
                    cursor: pointer;
                ">

            ✕

        </button>

    </div>



    <div style="padding: 20px;">


        <!-- CARRITO VACÍO -->

        <?php if (count($_SESSION['carrito']) == 0): ?>

            <p style="
                text-align: center;
                padding: 40px;
                color: #666;
            ">

                <i class="fa-solid fa-cart-shopping"
                   style="
                       font-size: 35px;
                       display: block;
                       margin-bottom: 15px;
                       color: #83d4d7;
                   ">
                </i>

                Tu carrito está vacío

            </p>


        <?php else: ?>


            <?php

            $total = 0;

            foreach ($_SESSION['carrito'] as $item):

                $total +=
                    $item['precio'] *
                    $item['cantidad'];

            ?>


                <!-- PRODUCTO DEL CARRITO -->

                <div class="card"
                     style="margin-bottom: 15px;">


                    <h4>

                        <?php echo $item['nombre']; ?>

                    </h4>


                    <p style="
                        color: #3b8f92;
                        font-weight: bold;
                        font-size: 18px;
                    ">

                        $<?php echo number_format(
                            $item['precio'],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </p>


                    <div class="flex-between mt-20">


                        <!-- CANTIDAD -->

                        <form method="POST"
                              style="
                                  display: flex;
                                  align-items: center;
                                  gap: 10px;
                              ">


                            <input type="hidden"
                                   name="producto_id"
                                   value="<?php echo $item['id']; ?>">


                            <button type="submit"
                                    name="actualizar_cantidad"
                                    class="btn btn-secondary"
                                    style="padding: 5px 15px;"
                                    onclick="
                                        this.form.cantidad.value =
                                        parseInt(this.form.cantidad.value) - 1;
                                    ">

                                −

                            </button>


                            <input type="number"
                                   name="cantidad"
                                   value="<?php echo $item['cantidad']; ?>"
                                   style="
                                       width: 50px;
                                       text-align: center;
                                       border: 2px solid #acf2f5;
                                       border-radius: 10px;
                                       padding: 5px;
                                   ">


                            <button type="submit"
                                    name="actualizar_cantidad"
                                    class="btn btn-secondary"
                                    style="padding: 5px 15px;"
                                    onclick="
                                        this.form.cantidad.value =
                                        parseInt(this.form.cantidad.value) + 1;
                                    ">

                                +

                            </button>


                        </form>


                        <!-- ELIMINAR -->

                        <form method="POST">

                            <input type="hidden"
                                   name="producto_id"
                                   value="<?php echo $item['id']; ?>">


                            <button type="submit"
                                    name="eliminar"
                                    class="eliminar-carrito"
                                    title="Eliminar producto">

                                <i class="fa-solid fa-trash"></i>

                            </button>

                        </form>


                    </div>

                </div>


            <?php endforeach; ?>


            <!-- TOTAL -->

            <div style="
                border-top: 2px solid #e0e0e0;
                padding-top: 20px;
                margin-top: 20px;
            ">


                <div class="flex-between mb-20">

                    <h3>
                        Total:
                    </h3>


                    <h3 style="color: #3b8f92;">

                        $<?php echo number_format(
                            $total,
                            0,
                            ',',
                            '.'
                        ); ?>

                    </h3>

                </div>


                <form method="POST">


                    <!-- FINALIZAR COMPRA -->

                    <button type="submit"
                            name="finalizar_compra"
                            class="btn btn-success"
                            style="
                                width: 100%;
                                margin-bottom: 10px;
                            "
                            <?php echo !isLoggedIn()
                                ? 'disabled'
                                : ''; ?>>

                        <i class="fa-solid fa-credit-card"></i>

                        <?php echo isLoggedIn()
                            ? 'Finalizar Compra'
                            : 'Inicia sesión para comprar'; ?>

                    </button>


                    <!-- VACIAR CARRITO -->

                    <button type="submit"
                            name="vaciar_carrito"
                            class="btn btn-secondary"
                            style="width: 100%;">

                        <i class="fa-solid fa-trash-can"></i>

                        Vaciar Carrito

                    </button>


                </form>


            </div>


        <?php endif; ?>


    </div>

</div>



<!-- =====================================================
     JAVASCRIPT DEL CARRITO
     ===================================================== -->

<script>

function toggleCarrito() {

    const overlay =
        document.getElementById('carritoOverlay');

    const sidebar =
        document.getElementById('carritoSidebar');


    if (overlay.style.display === 'none') {

        overlay.style.display = 'block';

        sidebar.style.display = 'block';

    } else {

        overlay.style.display = 'none';

        sidebar.style.display = 'none';

    }

}

</script>



<!-- =====================================================
     JAVASCRIPT DE ANIMACIÓN AL HACER SCROLL
     ===================================================== -->

<script>

const productosAnimados =
    document.querySelectorAll('.producto-scroll');


const observerProductos =
    new IntersectionObserver(

        (entradas) => {

            entradas.forEach((entrada) => {

                if (entrada.isIntersecting) {

                    entrada.target.classList.add('visible');

                    observerProductos.unobserve(
                        entrada.target
                    );

                }

            });

        },

        {
            threshold: 0.15
        }

    );


productosAnimados.forEach((producto) => {

    observerProductos.observe(producto);

});

</script>



<!-- =====================================================
     RESPONSIVE
     ===================================================== -->

<style>

@media (max-width: 768px) {

    #carritoSidebar {
        width: 90% !important;
        max-width: 400px;
    }


    .productos-header .flex-between {

        flex-direction: column;

        align-items: flex-start;

        gap: 20px;

    }

}

</style>


</body>
</html>