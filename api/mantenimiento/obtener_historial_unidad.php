<?php
require_once __DIR__ . '/../auth/conexion.php';
header('Content-Type: application/json');

if (!isset($_GET['economico']) || empty($_GET['economico'])) {
    echo json_encode(["error" => "Falta el número económico"]);
    exit;
}

$economico = $_GET['economico'];

// Traemos los últimos 10 mantenimientos finalizados para el expediente clínico
$sql = "SELECT id, fecha_ejecucion, hora_fin, tipo_servicio, operador_asignado, descripcion_cierre 
        FROM mantenimientos 
        WHERE economico = ? AND (estado = 'FINALIZADO' OR estado = 'COMPLETED' OR estado = 'COMPLETADO')
        ORDER BY fecha_ejecucion DESC, id DESC LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $economico);
$stmt->execute();
$resultado = $stmt->get_result();

$historial = [];
while ($row = $resultado->fetch_assoc()) {
    $historial[] = $row;
}

echo json_encode($historial);

$stmt->close();
$conn->close();
?>