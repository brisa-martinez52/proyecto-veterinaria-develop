<?php
require_once 'includes/config.php';

$hash = password_hash('admin123', PASSWORD_DEFAULT);

$query = "UPDATE usuarios SET password = ? WHERE dni = 'admin'";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $hash);
$stmt->execute();

echo "Admin actualizado con nueva contraseña segura.";
?>
