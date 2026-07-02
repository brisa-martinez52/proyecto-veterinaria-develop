<?php
require_once 'includes/config.php';

// Destruir la sesión
session_unset();
session_destroy();

// Redirigir al inicio
redirect('index.php');
?>