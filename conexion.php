<?php
$host = "sql213.infinityfree.com";
$usuario = "if0_38930661";
$contrasena = "S9uA79CR0rRD0Jg";  // Reemplázala con tu contraseña real
$base_de_datos = "if0_38930661_medicare"; // Reemplaza XXX con el nombre correcto de la base

try {
    $conn = new PDO("mysql:host=$host;dbname=$base_de_datos;charset=utf8", $usuario, $contrasena);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>

