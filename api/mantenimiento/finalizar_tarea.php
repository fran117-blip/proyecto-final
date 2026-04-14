<?php
session_start();
require_once __DIR__ . '/../auth/conexion.php';

// Le decimos al sistema que vamos a devolver un JSON
header('Content-Type: application/json');

// 1. Recibimos los textos
$id = $_POST['id'] ?? '';
$hora_fin = $_POST['hora_fin'] ?? '';
$descripcion_cierre = $_POST['descripcion_cierre'] ?? '';
$proximo_servicio = $_POST['proximo_servicio'] ?? NULL;
$firma_base64 = $_POST['firma_base64'] ?? '';

if (empty($id)) {
    echo json_encode(["success" => false, "error" => "No se recibió el ID de la tarea."]);
    exit;
}

// 2. Procesamos la foto de evidencia (si es que subió una)
$ruta_foto = NULL;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $directorio_subida = __DIR__ . '/../../uploads/';
    
    // Si la carpeta uploads no existe, la crea en automático
    if (!file_exists($directorio_subida)) {
        mkdir($directorio_subida, 0777, true);
    }

    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nombre_archivo = 'evidencia_orden_' . $id . '_' . time() . '.' . $extension;
    $ruta_absoluta = $directorio_subida . $nombre_archivo;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_absoluta)) {
        // Guardamos solo la ruta relativa para la base de datos
        $ruta_foto = 'uploads/' . $nombre_archivo;
    }
}

// 3. Guardamos todo en la base de datos (¡Y cambiamos a FINALIZADO!)
$sql = "UPDATE mantenimientos 
        SET estado = 'FINALIZADO', 
            hora_fin = ?, 
            descripcion_cierre = ?, 
            proximo_servicio = ?, 
            foto_evidencia = ?, 
            firma_operador = ? 
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Error preparando la consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("sssssi", $hora_fin, $descripcion_cierre, $proximo_servicio, $ruta_foto, $firma_base64, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Error al ejecutar: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>