// js/mecanico.js

function cargarMisTareas() {
    const contenedor = document.getElementById('contenedor-tareas');
    if (!contenedor) return;
    
    // Le agregamos el nocache por si acaso para siempre tener lo más fresco
    fetch('api/mantenimiento/obtener_tareas_mecanico.php?nocache=' + new Date().getTime())
        .then(res => res.json())
        .then(data => {
            if(data.error || data.length === 0) {
                contenedor.innerHTML = `
                    <div class="col-span-full bg-white p-10 rounded-2xl shadow-sm border border-slate-100 text-center">
                        <i class="fas fa-check-circle text-6xl text-emerald-400 mb-4"></i>
                        <h3 class="text-2xl font-bold text-slate-700">¡Estás al día!</h3>
                        <p class="text-slate-500 mt-2">No tienes tareas pendientes asignadas.</p>
                    </div>`;
                return;
            }

            contenedor.innerHTML = data.map(tarea => {
                let estado = (tarea.estado || 'PENDIENTE').toUpperCase();
                if (estado === 'PENDING') estado = 'PENDIENTE';
                if (estado === 'IN PROGRESS') estado = 'EN PROCESO';

                const colorBadge = estado === 'EN PROCESO' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700';
                
                // LÓGICA DE PRIORIDAD
                const prioridad = (tarea.prioridad || 'NORMAL').toUpperCase();
                let colorPrioridad = 'bg-slate-100 text-slate-600 border-slate-200'; 
                let iconoPrioridad = 'fa-flag';

                if (prioridad === 'ALTA' || prioridad === 'URGENTE') {
                    colorPrioridad = 'bg-red-50 text-red-600 border-red-200 shadow-sm shadow-red-100';
                    iconoPrioridad = 'fa-exclamation-triangle'; 
                } else if (prioridad === 'MEDIA') {
                    colorPrioridad = 'bg-orange-50 text-orange-600 border-orange-200 shadow-sm shadow-orange-100';
                    iconoPrioridad = 'fa-exclamation-circle';
                } else if (prioridad === 'BAJA') {
                    colorPrioridad = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                    iconoPrioridad = 'fa-arrow-down';
                }

                const detallesTexto = tarea.descripcion || 'No se agregaron detalles adicionales a esta orden.';
                const detallesSeguros = detallesTexto.replace(/'/g, "\\'").replace(/"/g, "&quot;").replace(/\n/g, "<br>");

                // Formateamos la hora de inicio quitándole los segundos si es que existe
                const horaInicioFormateada = tarea.hora_inicio ? tarea.hora_inicio.substring(0, 5) : '';

                // Botón Izquierdo: Detalles
                const btnDetalles = `<button onclick="verInstrucciones('${tarea.economico}', '${tarea.tipo_servicio}', '${detallesSeguros}')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-bold transition-colors text-sm border border-slate-300"><i class="fas fa-list-ul mr-1"></i> Detalles</button>`;

                // Botón Derecho: Iniciar / Finalizar 
                let btnAccionPrincipal = '';
                if (estado === 'PENDIENTE') {
                    btnAccionPrincipal = `<button onclick="iniciarTarea(${tarea.id})" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-xl font-bold transition-colors text-sm shadow-md shadow-indigo-200"><i class="fas fa-play mr-1"></i> Iniciar</button>`;
                } else {
                    btnAccionPrincipal = `<button onclick="abrirModalFinalizar(${tarea.id}, '${tarea.economico}', '${tarea.tipo_servicio}', '${horaInicioFormateada}')" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white py-2.5 rounded-xl font-bold transition-colors text-sm shadow-md shadow-emerald-200"><i class="fas fa-check-double mr-1"></i> Finalizar</button>`;
                }

                return `
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-lg transition-all relative overflow-hidden">
                    
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex gap-2">
                            <span class="px-3 py-1 ${colorBadge} rounded-full text-xs font-bold uppercase tracking-wide">${estado}</span>
                            <span class="px-2.5 py-1 ${colorPrioridad} border rounded-md text-[10px] font-black uppercase tracking-widest flex items-center gap-1">
                                <i class="fas ${iconoPrioridad}"></i> ${prioridad}
                            </span>
                        </div>
                        <span class="text-sm font-bold text-slate-300">#${tarea.id}</span>
                    </div>
                    
                    <h3 class="text-3xl font-black text-slate-800 mb-0">${tarea.economico}</h3>
                    <p class="text-xs font-bold text-slate-400 mb-3 uppercase tracking-wider">${tarea.modelo || 'Unidad Pesada'}</p>
                    
                    <div class="flex items-center text-sm mb-5">
                        <p class="text-indigo-600 font-bold w-1/2"><i class="fas fa-wrench mr-1"></i> ${tarea.tipo_servicio || 'Mantenimiento'}</p>
                        <p class="text-slate-500 font-medium w-1/2 text-right text-xs"><i class="far fa-calendar-alt mr-1"></i> ${tarea.fecha_principal || 'Sin fecha'}</p>
                    </div>
                    
                    <div class="flex gap-3">
                        ${btnDetalles}
                        ${btnAccionPrincipal}
                    </div>
                </div>`;
            }).join('');
        })
        .catch(err => console.error("Error:", err));
}

function verInstrucciones(unidad, servicio, detalles) {
    Swal.fire({
        title: 'Orden: ' + unidad,
        html: `<p class="text-slate-600 mb-3">Servicio programado: <b class="text-indigo-600">${servicio}</b></p>
               <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-left text-sm text-slate-700 shadow-inner max-h-48 overflow-y-auto">
                   <p class="font-bold text-slate-500 mb-2 uppercase text-[10px] tracking-widest"><i class="fas fa-comment-alt mr-1"></i> Instrucciones del Taller:</p>
                   <p class="leading-relaxed">${detalles}</p>
               </div>`,
        icon: 'info',
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Entendido'
    });
}

function iniciarTarea(id) {
    Swal.fire({
        title: '¿Iniciar Mantenimiento?',
        text: "Se registrará la hora de inicio.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Sí, arrancar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Conectando con el servidor...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() }
            });

            fetch('api/mantenimiento/iniciar_tarea.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(async res => {
                const texto = await res.text();
                try {
                    return JSON.parse(texto);
                } catch (e) {
                    throw new Error(texto || "El servidor no respondió correctamente.");
                }
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Iniciado!', 'El cronómetro ha comenzado.', 'success');
                    cargarMisTareas();
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error de Conexión', err.message, 'error');
            });
        }
    });
}

function abrirModalFinalizar(id, unidad, tipo_servicio, hora_inicio) {
    document.getElementById('fin-id').value = id;
    document.getElementById('modal-unidad-txt').innerText = 'UNIDAD: ' + unidad;
    
    if(tipo_servicio) {
        document.getElementById('fin-tipo').value = tipo_servicio;
    }
    
    const ahora = new Date();
    const hora = ahora.getHours().toString().padStart(2, '0');
    const min = ahora.getMinutes().toString().padStart(2, '0');
    document.getElementById('fin-hora-fin').value = `${hora}:${min}`;
    
    document.getElementById('fin-hora-inicio').value = hora_inicio || '--:--'; 
    
    const modal = document.getElementById('modal-finalizar');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    if(window.prepararCanvas) window.prepararCanvas();
}

function cerrarModalFinalizar() {
    document.getElementById('modal-finalizar').classList.add('hidden');
    document.getElementById('modal-finalizar').classList.remove('flex');
    document.getElementById('form-finalizar').reset();
    document.getElementById('nombre-archivo').innerText = 'Capturar o Subir';
    limpiarFirma();
}

function mostrarNombreFoto() {
    const input = document.getElementById('fin-foto');
    const texto = document.getElementById('nombre-archivo');
    if(input.files && input.files[0]) {
        texto.innerText = input.files[0].name;
        texto.classList.add('text-emerald-600', 'font-bold');
    }
}

// ==========================================
// LÓGICA DE LA FIRMA DIGITAL (CANVAS)
// ==========================================
let canvas, ctx, dibujando = false;

document.addEventListener('DOMContentLoaded', () => {
    cargarMisTareas();

    canvas = document.getElementById('pizarra-firma');
    if (!canvas) return;
    
    ctx = canvas.getContext('2d');
    
    function ajustarCanvas() {
        const rect = canvas.parentElement.getBoundingClientRect();
        canvas.width = rect.width;
    }
    window.addEventListener('resize', ajustarCanvas);
    
    ctx.lineWidth = 3;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#1e293b'; 

    canvas.addEventListener('mousedown', iniciarDibujo);
    canvas.addEventListener('mousemove', dibujar);
    canvas.addEventListener('mouseup', detenerDibujo);
    canvas.addEventListener('mouseout', detenerDibujo);

    canvas.addEventListener('touchstart', (e) => { e.preventDefault(); iniciarDibujo(e.touches[0]); });
    canvas.addEventListener('touchmove', (e) => { e.preventDefault(); dibujar(e.touches[0]); });
    canvas.addEventListener('touchend', detenerDibujo);

    window.prepararCanvas = ajustarCanvas;
});

function obtenerPosicion(e) {
    const rect = canvas.getBoundingClientRect();
    return { x: e.clientX - rect.left, y: e.clientY - rect.top };
}

function iniciarDibujo(e) {
    dibujando = true;
    const pos = obtenerPosicion(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}

function dibujar(e) {
    if (!dibujando) return;
    const pos = obtenerPosicion(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
}

function detenerDibujo() {
    dibujando = false;
    ctx.closePath();
    document.getElementById('firma-base64').value = canvas.toDataURL('image/png');
}

function limpiarFirma() {
    if(ctx) ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('firma-base64').value = '';
}

// ==========================================
// NUEVO: LÓGICA PARA ENVIAR EL FORMULARIO FINAL
// ==========================================
function guardarOrdenFinal(e) {
    e.preventDefault(); // Detiene la recarga de la página

    const firmaBase64 = document.getElementById('firma-base64').value;
    if (!firmaBase64) {
        Swal.fire('Firma Requerida', 'El operador debe firmar de conformidad.', 'warning');
        return;
    }

    const form = document.getElementById('form-finalizar');
    const formData = new FormData(form);

    Swal.fire({
        title: 'Sellando Orden...',
        text: 'Subiendo evidencias y registrando horas.',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading() }
    });

    fetch('api/mantenimiento/finalizar_tarea.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire('¡Orden Finalizada!', 'El reporte se ha guardado con éxito.', 'success');
            cerrarModalFinalizar();
            cargarMisTareas(); // Esto hará que la tarjeta desaparezca mágicamente al cambiar a 'FINALIZADO'
        } else {
            Swal.fire('Error', data.error || 'No se pudo guardar la orden.', 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error de Conexión', 'Ocurrió un problema.', 'error');
        console.error(err);
    });
}

// ==========================================
// LÓGICA DE LA PESTAÑA HISTORIAL
// ==========================================

function cambiarPestana(pestana) {
    // Escondemos todo
    document.getElementById('vista-tareas').classList.add('hidden');
    document.getElementById('vista-historial').classList.add('hidden');
    
    // Apagamos los botones
    document.getElementById('btn-tareas').classList.remove('bg-indigo-50', 'text-indigo-600', 'border-r-4', 'border-indigo-600');
    document.getElementById('btn-historial').classList.remove('bg-indigo-50', 'text-indigo-600', 'border-r-4', 'border-indigo-600');
    
    // Encendemos la que nos pidieron
    if (pestana === 'tareas') {
        document.getElementById('vista-tareas').classList.remove('hidden');
        document.getElementById('btn-tareas').classList.add('bg-indigo-50', 'text-indigo-600', 'border-r-4', 'border-indigo-600');
        cargarMisTareas();
    } else if (pestana === 'historial') {
        document.getElementById('vista-historial').classList.remove('hidden');
        document.getElementById('btn-historial').classList.add('bg-indigo-50', 'text-indigo-600', 'border-r-4', 'border-indigo-600');
        cargarHistorial();
    }
}

function cargarHistorial() {
    const contenedor = document.getElementById('contenedor-historial');
    if (!contenedor) return;

    fetch('api/mantenimiento/obtener_historial_mecanico.php?nocache=' + new Date().getTime())
        .then(res => res.json())
        .then(data => {
            if(data.error || data.length === 0) {
                contenedor.innerHTML = `
                    <div class="col-span-full bg-slate-50 p-10 rounded-2xl border border-slate-200 text-center">
                        <i class="fas fa-box-open text-5xl text-slate-300 mb-4"></i>
                        <h3 class="text-xl font-bold text-slate-500">Historial vacío</h3>
                        <p class="text-slate-400 mt-2">Aún no has finalizado ninguna tarea.</p>
                    </div>`;
                return;
            }

            contenedor.innerHTML = data.map(tarea => {
                
                // Hacemos que el código sea inteligente por si faltan datos en la BD
                const servicioSeguro = tarea.tipo_servicio || tarea.motivo || 'Servicio General';
                const fechaSegura = tarea.fecha_ejecucion || tarea.fecha_programada || 'Fecha no registrada';
                const horaSegura = tarea.hora_fin || '--:--';
                const notasSeguras = tarea.descripcion_cierre || 'Sin observaciones.';

                // Tarjeta estilo "Archivo Muerto" (gris y elegante)
                return `
                <div class="tarjeta-historial bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <span class="px-3 py-1 bg-slate-200 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-wide"><i class="fas fa-check-double mr-1"></i> Finalizado</span>
                        <span class="text-xs font-bold text-slate-400">#${tarea.id}</span>
                    </div>
                    
                    <h3 class="economico-filtro text-2xl font-black text-slate-700 mb-0">${tarea.economico || 'Unidad'}</h3>
                    <p class="text-xs font-bold text-slate-400 mb-3 uppercase tracking-wider">${tarea.modelo || 'Unidad Pesada'}</p>
                    
                    <div class="bg-white p-3 rounded-xl border border-slate-200 mb-3">
                        <p class="servicio-filtro text-indigo-600 font-bold text-sm"><i class="fas fa-wrench mr-1"></i> ${servicioSeguro}</p>
                        <p class="text-slate-500 text-xs mt-1"><i class="far fa-calendar-check mr-1"></i> Cerrado el: ${fechaSegura} a las ${horaSegura}</p>
                    </div>
                    
                    <p class="text-xs text-slate-500 line-clamp-2" title="${notasSeguras}"><span class="font-bold text-slate-600">Notas:</span> ${notasSeguras}</p>
                </div>`;
            }).join('');
        })
        .catch(err => console.error("Error cargando historial:", err));
}

// ==========================================
// LÓGICA DEL ESCÁNER QR DE UNIDADES 
// ==========================================
let html5QrCode = null;

function abrirEscanerQR() {
    const modal = document.getElementById('modal-escaner');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Usamos el motor puro sin la interfaz fea predeterminada
    html5QrCode = new Html5Qrcode("lector-qr");

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    // Intentamos iniciar directo con la cámara trasera (environment)
    html5QrCode.start({ facingMode: "environment" }, config, alDetectarQR)
    .catch(err => {
        // Si es una laptop y no tiene cámara trasera, encendemos la frontal (user)
        html5QrCode.start({ facingMode: "user" }, config, alDetectarQR)
        .catch(e => {
            console.error("Error al iniciar cámara", e);
            Swal.fire('Error', 'No se pudo acceder a la cámara.', 'error');
        });
    });
}

function cerrarEscanerQR() {
    const modal = document.getElementById('modal-escaner');
    modal.classList.add('hidden');
    modal.classList.remove('flex');

    // Apagamos la cámara correctamente
    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
        }).catch(err => console.error("Error apagando el escáner.", err));
    }
}

// ==========================================
// EXPEDIENTE ORGANIZADO (ESTILO IMAGEN)
// ==========================================
async function alDetectarQR(textoDecodificado) {
    cerrarEscanerQR(); 

    // 1. Mostrar carga inicial
    Swal.fire({
        title: 'Cargando expediente...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading() }
    });

    try {
        const resUnidad = await fetch(`api/unidades/buscar_por_qr.php?economico=${textoDecodificado}`);
        const unidad = await resUnidad.json();

        const resHistorial = await fetch(`api/mantenimiento/obtener_historial_unidad.php?economico=${textoDecodificado}`);
        let htmlHistorial = "";

        if(resHistorial.ok) {
            const historial = await resHistorial.json();
            if (historial.length > 0 && !historial.error) {
                htmlHistorial = `
                    <div class="text-left mt-4 border-t border-slate-100 pt-3">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">
                            <i class="fas fa-history mr-1"></i> Últimos Servicios
                        </h4>
                        <div style="max-height: 120px; overflow-y: auto;">`;
                
                historial.forEach(item => {
                    htmlHistorial += `
                        <div class="p-3 bg-slate-50 rounded-lg border-l-4 border-indigo-500 mb-2 shadow-sm">
                            <div class="flex justify-between items-start">
                                <span class="font-bold text-indigo-600 text-sm">
                                    <i class="fas fa-wrench mr-1"></i> ${item.tipo_servicio}
                                </span>
                                <span class="text-xs font-bold text-slate-400">${item.fecha_ejecucion}</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1"><b>Atendió:</b> ${item.operador_asignado}</p>
                        </div>`;
                });
                htmlHistorial += `</div></div>`;
            }
        }

        // --- SOLUCIÓN AL SPINNER ---
        // Cerramos la alerta de carga por completo para limpiar el DOM de SweetAlert
        Swal.close();

        // Pequeña pausa para asegurar que el DOM se limpió del estado "loading"
        setTimeout(() => {
            Swal.fire({
                html: `
                    <div class="flex justify-center mb-4">
                        <div class="bg-emerald-50 h-16 w-16 rounded-full flex items-center justify-center border-4 border-white shadow-sm">
                            <i class="fas fa-check text-2xl text-emerald-500"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-700 mb-4 text-center">Expediente de unidad</h2>
                    <div class="text-left p-5 bg-slate-50 rounded-2xl border border-slate-100 shadow-inner">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-truck text-indigo-500 w-5 text-center text-lg"></i>
                                <span class="text-slate-500 font-medium text-base">Económico:</span>
                                <span class="text-slate-800 font-bold text-lg">${textoDecodificado}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-industry text-indigo-500 w-5 text-center text-lg"></i>
                                <span class="text-slate-500 font-medium text-base">Marca:</span>
                                <span class="text-slate-800 font-bold text-lg">${unidad.marca || 'N/A'}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-tag text-indigo-500 w-5 text-center text-lg"></i>
                                <span class="text-slate-500 font-medium text-base">Modelo:</span>
                                <span class="text-slate-800 font-bold text-lg">${unidad.modelo || 'N/A'}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-id-card text-indigo-500 w-5 text-center text-lg"></i>
                                <span class="text-slate-500 font-medium text-base">Placas:</span>
                                <span class="text-slate-800 font-bold text-lg">${unidad.placas || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    ${htmlHistorial}
                `,
                showConfirmButton: true,
                confirmButtonText: 'Cerrar Expediente',
                confirmButtonColor: '#64748b',
                customClass: {
                    popup: 'rounded-3xl shadow-2xl',
                    confirmButton: 'rounded-xl px-12 py-3 font-bold text-sm uppercase tracking-widest'
                }
            });
        }, 100);

    } catch (error) {
        Swal.fire('Error', 'No se pudo obtener el expediente.', 'error');
    }
}