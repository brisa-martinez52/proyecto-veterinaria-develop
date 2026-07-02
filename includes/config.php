<?php
// Configuración de Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'veterinaria_patitas');

// Conexión a la base de datos
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Funciones útiles
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

function isAdmin() {
    return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 1;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit();
    }
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

function normalize_image_path($path) {
    if (empty($path)) {
        return '';
    }

    $path = trim($path);
    if (preg_match('~^(https?:)?//|^data:~', $path)) {
        return $path;
    }

    $path = str_replace('\\', '/', $path);
    $path = ltrim($path, '/');

    if (preg_match('~^(imagenes/|images/)~i', $path)) {
        $path = 'Imagenes/' . preg_replace('~^(imagenes/|images/)~i', '', $path);
    } elseif (stripos($path, 'Imagenes/') !== 0) {
        $path = 'Imagenes/' . $path;
    }

    return $path;
}

function asset_path($path) {
    if (empty($path)) {
        return '';
    }

    $path = normalize_image_path($path);
    if (empty($path)) {
        return '';
    }

    if (preg_match('~^(https?:)?//|^data:~', $path)) {
        return $path;
    }

    $path = str_replace(' ', '%20', $path);

    if (strpos($path, '/') === 0) {
        return $path;
    }

    $base_path = basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'admin' ? '../' : '';
    return $base_path . $path;
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>