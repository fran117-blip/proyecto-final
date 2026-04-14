<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-bold text-slate-800">Agenda de Mantenimiento</h2>
    <button class="btn-primary" onclick="abrirModalAgenda()" style="background:#4f46e5; color:white; padding:10px 20px; border-radius:8px; border:none; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-calendar-plus"></i> Nueva Cita
    </button>
</div>

<div class="tabla-contenedor card">
    <table>
        <thead>
            <tr>
                <th class="text-xs font-bold text-slate-500 uppercase">UNIDAD</th>
                <th class="text-xs font-bold text-slate-500 uppercase">MODELO</th>
                <th class="text-xs font-bold text-slate-500 uppercase">MOTIVO</th>
                <th class="text-xs font-bold text-slate-500 uppercase">FECHA</th>
                <th class="text-xs font-bold text-slate-500 uppercase">DÍAS</th>
                <th class="text-xs font-bold text-slate-500 uppercase">MECÁNICO</th>
                <th class="text-xs font-bold text-slate-500 uppercase">ESTADO</th>
            </tr>
        </thead>
        <tbody id="agenda-datos-reales">
            <tr>
                <td colspan="7" class="text-center p-8 text-slate-400">Cargando datos del servidor...</td>
            </tr>
        </tbody>
    </table>
</div>

<div id="modal-agenda" class="modal-overlay" style="display:none;">
    <div class="modal-moderno">
        <div class="modal-header">
            <h3><i class="fas fa-calendar-plus text-primary"></i> Programar Mantenimiento</h3>
            <button onclick="cerrarModalAgenda()" class="btn-cerrar-modal">&times;</button>
        </div>

        <div class="modal-body">
            <form id="form-nueva-agenda">
                
                <div class="input-group" style="margin-bottom: 20px;">
                    <label>Unidad (N° Económico)</label>
                    <div class="input-with-icon">
                        <i class="fas fa-truck"></i>
                        <select name="unidad_id" id="select-unidades-agenda" required>
                            <option value="" disabled selected>Selecciona una unidad...</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2" style="margin-bottom: 20px;">
                    <div class="input-group">
                        <label>Motivo</label>
                        <div class="input-with-icon">
                            <i class="fas fa-clipboard-list"></i>
                            <select name="motivo" required>
                                <option value="Preventivo">Preventivo</option>
                                <option value="Predictivo">Predictivo</option>
                                <option value="Correctivo">Correctivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label>Fecha Programada</label>
                        <div class="input-with-icon">
                            <i class="fas fa-calendar-day"></i>
                            <input type="date" name="fecha" required>
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label>Asignar Mecánico Responsable</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user-wrench"></i>
                        <select name="usuario_id" id="select-mecanicos-agenda" required>
                            <option value="" disabled selected>Cargando mecánicos...</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="cerrarModalAgenda()" class="btn-cancelar">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-guardar">
                        <i class="fas fa-save"></i> Guardar en Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- FUNCIONES DEL MODAL ---
    function abrirModalAgenda() {
        document.getElementById('modal-agenda').style.display = 'flex';
        cargarSelectoresAgenda();
    }

    function cerrarModalAgenda() {
        document.getElementById('modal-agenda').style.display = 'none';
        document.getElementById('form-nueva-agenda').reset();
    }

    function cargarSelectoresAgenda() {
        // Cargar Mecánicos (Usa la API corregida)
        fetch('api/usuarios/obtener_mecanicos.php')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('select-mecanicos-agenda');
                select.innerHTML = '<option value="" disabled selected>Selecciona un mecánico...</option>';
                data.forEach(m => {
                    select.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
                });
            });

        // Cargar Unidades
        fetch('api/unidades/obtener_unidades.php')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('select-unidades-agenda');
                select.innerHTML = '<option value="" disabled selected>Selecciona una unidad...</option>';
                data.forEach(u => {
                    select.innerHTML += `<option value="${u.id}">${u.num_economico} - ${u.modelo}</option>`;
                });
            });
    }

    // --- CARGAR TABLA (Tu lógica original mejorada) ---
    function cargarAgenda() {
        const tbody = document.getElementById('agenda-datos-reales');
        if (!tbody) return;

        fetch('api/mantenimiento/obtener_agenda.php')
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center p-8">No hay mantenimientos pendientes.</td></tr>';
                    return;
                }

                tbody.innerHTML = data.map(a => {
                    const hoy = new Date();
                    hoy.setHours(0,0,0,0);
                    const fechaServicio = new Date(a.proximo_servicio + 'T00:00:00');
                    const diffTime = fechaServicio - hoy;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

                    let badgeHTML = '';
                    let diasClass = 'font-bold';
                    let diasTexto = diffDays;

                    if (diffDays < 0) {
                        badgeHTML = '<span class="badge" style="background:#fee2e2; color:#ef4444; padding:4px 8px; border-radius:12px; font-weight:bold; font-size:0.7rem;">VENCIDO</span>';
                        diasClass += ' text-red-600';
                    } else if (diffDays === 0) {
                        badgeHTML = '<span class="badge" style="background:#fef3c7; color:#d97706; padding:4px 8px; border-radius:12px; font-weight:bold; font-size:0.7rem;">HOY</span>';
                        diasClass += ' text-orange-500';
                    } else {
                        badgeHTML = '<span class="badge" style="background:#eff6ff; color:#3b82f6; padding:4px 8px; border-radius:12px; font-weight:bold; font-size:0.7rem;">PROGRAMADO</span>';
                        diasClass += ' text-slate-600';
                    }

                    return `
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td class="p-4 font-bold text-slate-700">${a.economico}</td>
                            <td class="p-4 text-slate-600">${a.modelo}</td>
                            <td class="p-4 text-slate-600">${a.tipo_servicio || 'Preventivo'}</td>
                            <td class="p-4 font-medium text-slate-700">${fechaServicio.toLocaleDateString('es-MX')}</td>
                            <td class="p-4 ${diasClass}">${diasTexto}</td>
                            <td class="p-4 text-slate-600">${a.ultimo_mecanico || 'Sin Asignar'}</td>
                            <td class="p-4">${badgeHTML}</td>
                        </tr>
                    `;
                }).join('');
            });
    }

    // Inicialización y envío
    document.addEventListener('DOMContentLoaded', () => {
        cargarAgenda();
        
        document.getElementById('form-nueva-agenda').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const datos = Object.fromEntries(formData.entries());

            fetch('api/mantenimiento/guardar_agenda.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    Swal.fire('¡Éxito!', 'Cita programada correctamente', 'success');
                    cerrarModalAgenda();
                    cargarAgenda();
                }
            });
        });
    });
</script>
