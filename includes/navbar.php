<nav class="navbar" style="background-color:#83d4d7; position:fixed; top:0; left:0; right:0; z-index:1000; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
    <div class="navbar-container" style="max-width:1500px; margin:0 auto; padding:10px 20px; display:flex; align-items:center; justify-content:space-between; gap:16px; min-height:84px;">
        <?php
        // Detectar si estamos en una subcarpeta
        $base_path = (basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'admin') ? '../' : '';
        ?>
        <a href="<?php echo $base_path; ?>index.php" class="navbar-logo" style="display:flex; align-items:center; flex-shrink:0;">
            <img src="<?php echo asset_path('Imagenes/logo patitas.png'); ?>" alt="Patitas Logo" class="logo-img" style="width:100px; height:100px; min-width:100px; min-height:100px; object-fit:cover; border-radius:50%; display:block;">
        </a>

        <!-- Botón Hamburguesa -->
        <button class="hamburger" onclick="toggleMenu()" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Links de Navegación -->
        <div class="navbar-links" id="navbarLinks" style="display:flex; align-items:center; gap:6px; background-color:#acf2f5; border-radius:999px; padding:6px 10px; min-height:42px; flex-wrap:wrap; justify-content:center; margin-left:4px;">
            <a href="<?php echo $base_path; ?>index.php">Inicio</a>
            <a href="<?php echo $base_path; ?>productos.php">Productos
                <?php if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                    <span class="cart-badge"><?php echo array_sum(array_column($_SESSION['carrito'], 'cantidad')); ?></span>
                <?php endif; ?>
            </a>
            
            <?php if(isLoggedIn()): ?>
                <a href="<?php echo $base_path; ?>mis_mascotas.php">Mis Mascotas</a>
                <a href="<?php echo $base_path; ?>turnos.php">Turnos</a>
                <a href="<?php echo $base_path; ?>historial_medico.php">Historial</a>
                <?php if(isAdmin()): ?>
                    <a href="<?php echo $base_path; ?>admin/panel.php" class="admin-link">Panel Admin</a>
                <?php endif; ?>
                <a href="<?php echo $base_path; ?>faq.php">Preguntas frecuentes</a>
                <a href="<?php echo $base_path; ?>logout.php" class="nav-btn logout-btn">Cerrar Sesión</a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>faq.php">Preguntas frecuentes</a>
                <a href="<?php echo $base_path; ?>login.php"><button class="nav-btn login-btn">Iniciar Sesión</button></a>
                <a href="<?php echo $base_path; ?>register.php"><button class="nav-btn register-btn">Registrarse</button></a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
function toggleMenu() {
    const navbarLinks = document.getElementById('navbarLinks');
    const hamburger = document.getElementById('hamburger');
    navbarLinks.classList.toggle('active');
    hamburger.classList.toggle('active');
}

// Cerrar menú al hacer click en un link
document.querySelectorAll('.navbar-links a').forEach(link => {
    link.addEventListener('click', () => {
        document.getElementById('navbarLinks').classList.remove('active');
        document.getElementById('hamburger').classList.remove('active');
    });
});
</script>