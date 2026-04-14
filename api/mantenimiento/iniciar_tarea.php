<?php
session_start();
require_once __DIR__ . '/../auth/conexion.php';
header('Content-Type: application/json');

// Recibimos el paquete de datos que nos manda mecanico.js
$data = json_decode(file_get_contents("php://input"), true);

// Verificamos que sí nos haya llegado el ID de la tarea
if (!isset($data['id'])) {
    echo json_encode(["success" => false, "error" => "No se recibió el ID de la tarea."]);
    exit;
}

$id = $data['id'];

// Preparamos la orden para la base de datos
// Cambiamos a 'EN PROCESO' y guardamos la hora actual
$sql = "UPDATE mantenimientos SET estado = 'EN PROCESO', hora_inicio = CURTIME() WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Error preparando la consulta: " . $conn->error]);
    exit;
}

// Vinculamos el ID (como un número entero 'i') y ejecutamos
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Si todo salió bien, le decimos a JavaScript "success: true"
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Error al ejecutar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>