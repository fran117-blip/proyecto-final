<?php
session_start();
require_once __DIR__ . '/../auth/conexion.php';
header('Content-Type: application/json');

// 1. Verificamos que alguien haya iniciado sesión (no importa si es admin o mecánico)
if (!isset($_SESSION['nombre'])) {
    echo json_encode(["error" => "No hay sesión activa."]);
    exit;
}

// Sacamos el nombre del mecánico que está usando la app ahorita (ej. "Juan Mecánico")
$nombre_mecanico = $_SESSION['nombre'];

// 2. Buscamos SOLO los trabajos terminados que le pertenecen a este mecánico
$sql = "SELECT id, economico, modelo, tipo_servicio, fecha_ejecucion, hora_fin, descripcion_cierre 
        FROM mantenimientos 
        WHERE (estado = 'FINALIZADO' OR estado = 'COMPLETED' OR estado = 'COMPLETADO') 
        AND operador_asignado = ? 
        ORDER BY fecha_ejecucion DESC, id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nombre_mecanico);
$stmt->execute();
$resultado = $stmt->get_result();

$historial = [];
while ($row = $resultado->fetch_assoc()) {
    $historial[] = $row;
}

// Le devolvemos su historial personalizado
echo json_encode($historial);

$stmt->close();
$conn->close();
?>