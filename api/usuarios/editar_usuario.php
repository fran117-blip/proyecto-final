<?php
require_once __DIR__ . '/../auth/conexion.php';
header('Content-Type: application/json');

// Recibimos los datos enviados por JS
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

// Limpiamos los datos para evitar errores
$id = $conn->real_escape_string($data['id']);
$nombre = $conn->real_escape_string($data['nombre']);
$email = $conn->real_escape_string($data['email']);
$rol = $conn->real_escape_string($data['rol']);
$estado = $conn->real_escape_string($data['estado']);

// Actualizamos los campos en la tabla
$sql = "UPDATE usuarios SET nombre = '$nombre', email = '$email', rol = '$rol', estado = '$estado' WHERE id = '$id'";

if ($conn->query($sql)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Error al actualizar: " . $conn->error]);
}

$conn->close();
?>