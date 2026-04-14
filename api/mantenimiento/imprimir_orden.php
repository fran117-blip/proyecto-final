<?php
require_once __DIR__ . '/../auth/conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID de orden no proporcionado.");

/**
 * SOLUCIÓN MAESTRA ACTUALIZADA: 
 * 1. Unimos con la tabla 'unidades' para traer placas, marca y modelo.
 * 2. Usamos COALESCE para rescatar el 'sistema' si el 'tipo_servicio' está vacío.
 */
$sql = "SELECT m.*, 
               u.placas as unidad_placas, 
               u.marca as unidad_marca, 
               u.modelo as unidad_modelo
        FROM mantenimientos m
        LEFT JOIN unidades u ON m.economico = u.economico
        WHERE m.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$orden = $stmt->get_result()->fetch_assoc();

if (!$orden) die("Orden no encontrada.");

// Función para limpiar el informe de "N/A" y evitar errores naranjas de PHP
function validar($valor, $default = 'N/A') {
    return (isset($valor) && !empty(trim($valor)) && $valor !== 'NULL') ? htmlspecialchars($valor) : $default;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Mantenimiento - Folio #<?php echo $id; ?></title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #1e293b; line-height: 1.5; margin: 0; padding: 40px; background: #fff; }
        .header-iso { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px; }
        .logo-placeholder { font-weight: bold; color: #3b82f6; font-size: 1.5rem; text-transform: uppercase; }
        .doc-info { text-align: right; font-size: 0.8rem; color: #64748b; }
        
        .section-title { background: #f8fafc; padding: 8px 15px; font-weight: bold; text-transform: uppercase; font-size: 0.8rem; border-left: 4px solid #3b82f6; margin: 20px 0 10px 0; color: #334155; }
        
        .data-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 10px; }
        .data-item { border-bottom: 1px solid #f1f5f9; padding: 5px 0; }
        .label { font-size: 0.65rem; color: #64748b; font-weight: bold; text-transform: uppercase; display: block; }
        .value { font-size: 0.85rem; font-weight: 600; color: #0f172a; }
        
        .evidence-box { margin-top: 10px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; text-align: center; background: #fafafa; }
        .evidence-img { max-width: 100%; max-height: 350px; border-radius: 5px; border: 1px solid #ddd; }

        .signature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; margin-top: 40px; text-align: center; }
        .signature-line { border-top: 1px solid #1e293b; padding-top: 10px; font-size: 0.75rem; font-weight: bold; }
        
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 12px 25px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
            <i class="fas fa-print"></i> Confirmar Impresión / Guardar PDF
        </button>
    </div>

    <div class="header-iso">
        <div class="logo-placeholder">SISTEMA GESTIÓN DE FLOTA</div>
        <div class="doc-info">
            <strong>FOLIO: #<?php echo $id; ?></strong><br>
            EMISIÓN: <?php echo date('d/m/Y H:i'); ?><br>
            STATUS: <?php echo strtoupper(validar($orden['estado'])); ?>
        </div>
    </div>

    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="margin: 0; font-size: 1.3rem;">ORDEN DE SERVICIO Y MANTENIMIENTO</h1>
        <p style="margin: 0; font-size: 0.75rem; color: #64748b;">REGISTRO DE CONTROL OPERATIVO - AUDITORÍA ISO 9001</p>
    </div>

    <div class="section-title">1. Identificación del Activo</div>
    <div class="data-grid">
        <div class="data-item">
            <span class="label">N° Económico</span>
            <span class="value"><?php echo validar($orden['economico']); ?></span>
        </div>
        <div class="data-item">
            <span class="label">Marca/Modelo</span>
            <span class="value">
                <?php 
                    // Unimos marca y modelo de la tabla de unidades
                    $marca_modelo = trim($orden['unidad_marca'] . ' ' . $orden['unidad_modelo']);
                    echo validar($marca_modelo); 
                ?>
            </span>
        </div>
        <div class="data-item">
            <span class="label">Placas</span>
            <span class="value"><?php echo validar($orden['unidad_placas']); ?></span>
        </div>
    </div>

    <div class="section-title">2. Detalles de la Intervención</div>
    <div class="data-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="data-item">
            <span class="label">Tipo de Servicio</span>
            <span class="value"><?php echo validar($orden['tipo_servicio'], 'No especificado'); ?></span>
        </div>
        <div class="data-item">
            <span class="label">Sistema Afectado</span>
            <span class="value"><?php echo validar($orden['sistema'], 'No especificado'); ?></span>
        </div>
        <div class="data-item">
            <span class="label">Técnico Asignado</span>
            <span class="value"><?php echo validar($orden['operador_asignado']); ?></span>
        </div>
        <div class="data-item">
            <span class="label">Fecha Ejecución</span>
            <span class="value"><?php echo validar($orden['fecha_ejecucion']); ?></span>
        </div>
    </div>

    <div class="section-title">3. Tiempos de Operación</div>
    <div class="data-grid">
        <div class="data-item">
            <span class="label">Hora Inicio</span>
            <span class="value"><?php echo validar($orden['hora_inicio'], '--:--'); ?></span>
        </div>
        <div class="data-item">
            <span class="label">Hora Finalización</span>
            <span class="value"><?php echo validar($orden['hora_fin'], '--:--'); ?></span>
        </div>
        <div class="data-item">
            <span class="label">Tiempo Total</span>
            <span class="value">
                <?php 
                    // 1. Intentamos ver si ya está guardado en la columna 'duracion'
                    $tiempo_total = validar($orden['duracion'], '');
                    
                    // 2. Si está vacío, pero sí tenemos hora de inicio y fin, lo calculamos en vivo
                    if (empty($tiempo_total) && !empty($orden['hora_inicio']) && !empty($orden['hora_fin'])) {
                        try {
                            $inicio = new DateTime($orden['hora_inicio']);
                            $fin = new DateTime($orden['hora_fin']);
                            $intervalo = $inicio->diff($fin);
                            echo $intervalo->format('%h h %i m %s s'); 
                        } catch (Exception $e) {
                            echo '--';
                        }
                    } else {
                        // 3. Si no hay nada, mostramos 0
                        echo empty($tiempo_total) ? '0 h 0 m 0 s' : $tiempo_total;                    }
                ?>
            </span>
        </div>
    </div>

    <div class="section-title">4. Notas de Cierre y Diagnóstico</div>
    <div style="padding: 15px; border: 1px solid #f1f5f9; font-size: 0.85rem; min-height: 60px;">
        <?php echo nl2br(validar($orden['descripcion_cierre'], 'Sin observaciones registradas.')); ?>
    </div>

    <div class="section-title">5. Evidencia Fotográfica</div>
    <div class="evidence-box">
        <?php if(!empty($orden['foto_evidencia'])): ?>
            <img src="../../<?php echo $orden['foto_evidencia']; ?>" class="evidence-img">
        <?php else: ?>
            <p style="color: #94a3b8; font-style: italic;">No se registró evidencia visual en esta orden.</p>
        <?php endif; ?>
    </div>

    <div class="signature-grid">
        <div>
            <?php if(!empty($orden['firma_operador'])): ?>
                <img src="<?php echo $orden['firma_operador']; ?>" style="max-height: 70px;"><br>
            <?php endif; ?>
            <div class="signature-line">FIRMA MECANICO RESPONSABLE</div>
        </div>
        <div>
            <div style="height: 70px;"></div>
            <div class="signature-line">FIRMA DE GERENTE</div>
        </div>
    </div>
    
    <div style="margin-top: 30px; font-size: 0.6rem; color: #94a3b8; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 10px;">
        Este documento es un registro oficial generado por el Sistema de Gestión de Flota. Prohibida su alteración sin autorización.
    </div>
</body>
</html>