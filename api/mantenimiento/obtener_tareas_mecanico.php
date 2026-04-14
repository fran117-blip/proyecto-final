<?php
session_start();
require_once __DIR__ . '/../auth/conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['nombre'])) {
    echo json_encode([]);
    exit;
}

$nombre_mecanico = $_SESSION['nombre'];

// temporalmente para no tener que moverle nada al diseño de tu pantalla de mecanico.php
$sql = "SELECT id, economico, modelo, tipo_servicio, fecha_ejecucion as fecha_principal, estado, prioridad, descripcion, hora_inicio
        FROM mantenimientos 
        WHERE operador_asignado = ? 
        AND estado NOT IN ('COMPLETED', 'FINALIZADO', 'Finalizado') 
        ORDER BY fecha_ejecucion ASC";
        
$stmt = $conn->prepare($sql);

// Protección por si la base de datos rechaza la consulta
if (!$stmt) {
    echo json_encode(["error" => "Error SQL: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $nombre_mecanico);
$stmt->execute();
$result = $stmt->get_result();

$tareas = [];
while ($row = $result->fetch_assoc()) {
    $tareas[] = $row;
}

echo json_encode($tareas);

$stmt->close();
$conn->close();
?>