<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Inventario de Unidades</h2>
    
    <!-- Botones separados del buscador -->
    <div class="flex gap-3 items-center">
        
        <!-- Botones en su propio div -->
        <div class="flota-botones" style="display:flex; gap:8px;">
            <button class="btn-verde-excel" onclick="exportarFlotaExcel()" style="background-color:#10b981; color:white; padding:10px 20px; border-radius:8px; border:none; cursor:pointer; display:flex; align-items:center; gap:8px; font-weight:600;">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
            <button class="btn-primario" onclick="abrirModalCamion()" style="background-color:#10b981; color:white; padding:10px 20px; border-radius:8px; border:none; cursor:pointer; font-weight:600;">
                <i class="fas fa-plus"></i> Nuevo Camión
            </button>
        </div>

        <!-- Buscador en su propio div -->
        <div class="flota-buscador relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" 
                id="buscador-flota" 
                class="input-elegante" 
                style="padding:10px 10px 10px 40px; width:300px; border:1px solid #e2e8f0; border-radius:8px; outline:none;" 
                placeholder="Buscar unidad, marca o placa..."
                inkeyup="filtrarFlota()">
        </div>

    </div>
</div>

<div class="tabla-pro-container" style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden;">
    <table class="tabla-pro" id="tabla-unidades" style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8fafc;">
            <tr>
                <th style="padding: 15px; text-align: left; color: #64748b; font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">ECONÓMICO</th>
                <th style="padding: 15px; text-align: left; color: #64748b; font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">MARCA / MODELO</th>
                <th style="padding: 15px; text-align: left; color: #64748b; font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">AÑO</th>
                <th style="padding: 15px; text-align: left; color: #64748b; font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">PLACAS</th>
                <th style="padding: 15px; text-align: left; color: #64748b; font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">ESTATUS</th>
                <th style="padding: 15px; text-align: center; color: #64748b; font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">ACCIÓN</th>
            </tr>
        </thead>
        <tbody id="tabla-unidades-cuerpo">
            <tr>
                <td colspan="6" class="text-center py-10 text-slate-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando inventario...
                </td>
            </tr> 
        </tbody>
    </table>
</div>

<div id="modal-generador-qr" class="modal-overlay" style="display: none;">
    <div class="modal-moderno" style="max-width: 400px; text-align: center;">
        
        <div class="modal-header">
            <h3><i class="fas fa-qrcode text-primary"></i> Identificador Único</h3>
            <button type="button" class="btn-cerrar-modal" onclick="cerrarModalQR()"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="modal-body" style="padding: 30px 20px;">
            <h2 id="qr-titulo-unidad" style="font-size: 2.5rem; font-weight: 900; color: #1e293b; margin: 0;">T-XXX</h2>
            <p id="qr-subtitulo" style="font-size: 0.85rem; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;">Marca Modelo</p>
            
            <div id="contenedor-qr" style="background: white; padding: 15px; border-radius: 12px; border: 2px solid #f1f5f9; display: inline-block; margin-bottom: 25px;"></div>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <button onclick="imprimirQR()" style="width: 100%; background: #f8fafc; color: #475569; font-weight: bold; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; cursor: pointer; transition: 0.2s;">
                    <i class="fas fa-print"></i> Imprimir Etiqueta
                </button>
                
                <button onclick="irAHistorialQR()" style="width: 100%; background: #2563eb; color: white; font-weight: bold; padding: 12px; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); transition: 0.2s;">
                    <i class="fas fa-history"></i> Ver Historial de Unidad
                </button>
            </div>
        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="js/flota.js"></script>