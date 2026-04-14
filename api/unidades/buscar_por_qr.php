<?php
// Incluimos la conexión a la base de datos
require_once __DIR__ . '/../auth/conexion.php';

// Le decimos al navegador que vamos a devolver un JSON
header('Content-Type: application/json');

// Revisamos si recibimos el número económico desde el escáner
if (!isset($_GET['economico']) || empty($_GET['economico'])) {
    echo json_encode(["error" => "No se recibió ningún código"]);
    exit;
}

$economico = $_GET['economico'];

// Preparamos la consulta de forma segura
$sql = "SELECT economico, marca, modelo, placas, anio FROM unidades WHERE economico = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Error preparando la base de datos."]);
    exit;
}

$stmt->bind_param("s", $economico);
$stmt->execute();
$resultado = $stmt->get_result();

// Si encontramos el camión, mandamos sus datos
if ($fila = $resultado->fetch_assoc()) {
    echo json_encode($fila);
} else {
    // Si no existe, mandamos un error controlado
    echo json_encode(["error" => "Unidad no encontrada en el inventario."]);
}

$stmt->close();
$conn->close();
?>