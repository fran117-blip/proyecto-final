<?php
// 1. SEGURIDAD: Iniciar sesión y validar
session_start();

// Si no está logueado, lo mandamos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Si un Administrador entra aquí por error, lo regresamos a su panel principal
if (strtolower($_SESSION['rol']) === 'administrador') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Operador - Sistema Taller</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-slate-50">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col hidden md:flex">
            <div class="p-6 text-center border-b border-slate-100 mb-4">
                <div class="w-12 h-12 bg-indigo-600 text-white rounded-xl flex items-center justify-center text-2xl mx-auto mb-3 shadow-lg shadow-indigo-200">
                    <i class="fas fa-wrench"></i>
                </div>
                <h1 class="font-bold text-indigo-600 tracking-wide text-sm">ÁREA TÉCNICA</h1>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <div id="btn-tareas" onclick="cambiarPestana('tareas')" class="nav-item active flex items-center p-3 text-indigo-700 bg-indigo-50 border-r-4 border-indigo-600 rounded-lg font-bold cursor-pointer transition-colors">
                    <i class="fas fa-clipboard-list w-6 text-center"></i> Mis Tareas
                </div>
                
                <div id="btn-historial" onclick="cambiarPestana('historial')" class="nav-item flex items-center p-3 text-slate-500 hover:bg-slate-50 rounded-lg font-medium cursor-pointer transition-colors">
                    <i class="fas fa-history w-6 text-center"></i> Historial
                </div>

                <div class="nav-item flex items-center p-3 text-slate-500 hover:bg-slate-50 rounded-lg font-medium cursor-pointer transition-colors" onclick="abrirEscanerQR()">
                    <i class="fas fa-qrcode w-6 text-center"></i> Escanear Unidad
                </div>
            </nav>

            <div class="p-4 border-t border-slate-200">
                <a href="api/auth/logout.php" class="flex items-center w-full p-3 text-red-500 hover:bg-red-50 rounded-lg font-bold transition-colors">
                    <i class="fas fa-sign-out-alt w-6 text-center"></i> Cerrar Sesión
                </a>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto p-8 relative">
            
            <div class="flex justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">
                        Bienvenido, <?php echo $_SESSION['nombre']; ?>
                    </h2>
                    <p class="text-slate-500 mt-1">Revisa tus mantenimientos pendientes para hoy.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="font-bold text-slate-700"><?php echo $_SESSION['nombre']; ?></p>
                        <p class="text-xs text-indigo-500 font-bold uppercase"><?php echo $_SESSION['rol']; ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>

            <div id="vista-tareas" class="block transition-all">
                <div id="contenedor-tareas" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <div class="col-span-full text-center py-10 text-slate-400">
                        <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
                        <p>Buscando tareas asignadas...</p>
                    </div>
                </div>
            </div>

            <div id="vista-historial" class="hidden transition-all">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-black text-slate-800 flex items-center">
                        <i class="fas fa-archive mr-3 text-slate-400"></i> Historial de Trabajos
                    </h2>
                    
                    <div class="relative w-full md:w-80">
                        <input type="text" id="buscador-historial" onkeyup="filtrarHistorial()" placeholder="Buscar T-201, Motor, etc..." class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-400 focus:outline-none text-sm shadow-sm transition-all bg-white">
                        <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 text-lg"></i>
                    </div>
                </div>
                
                <div id="contenedor-historial" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    </div>
            </div>

            <div id="modal-finalizar" class="fixed inset-0 bg-slate-900/80 backdrop-blur-md hidden items-center justify-center z-50 p-4 xl:p-0 transition-opacity">
                <div class="bg-slate-100 rounded-2xl w-full max-w-2xl shadow-2xl overflow-y-auto max-h-[90vh] border border-slate-300">

                    <div class="bg-slate-800 p-6 flex justify-between items-start text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <span class="bg-indigo-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider shadow-sm">Cierre de Orden</span>
                            <h3 class="text-3xl font-black mt-2 tracking-tight" id="modal-unidad-txt">UNIDAD: T-XXX</h3>
                        </div>
                        <button onclick="cerrarModalFinalizar()" class="relative z-10 text-slate-400 hover:text-white text-3xl transition-colors">&times;</button>
                        <i class="fas fa-truck opacity-10 text-8xl absolute -right-4 -bottom-4"></i>
                    </div>

                    <form id="form-finalizar" onsubmit="guardarOrdenFinal(event)" enctype="multipart/form-data" class="p-6">
                        <input type="hidden" id="fin-id" name="id">

                        <div class="flex gap-4 -mt-12 relative z-20 mb-6">
                            <div class="flex-1 bg-slate-900 rounded-xl p-4 shadow-lg border border-slate-700 flex flex-col items-center justify-center">
                                <span class="text-slate-400 text-xs font-bold mb-1 uppercase tracking-widest"><i class="far fa-play-circle mr-1"></i> Inicio</span>
                                <input type="time" id="fin-hora-inicio" name="hora_inicio" class="bg-transparent text-emerald-400 font-mono text-xl text-center w-full outline-none pointer-events-none" readonly>
                            </div>
                            <div class="flex-1 bg-slate-900 rounded-xl p-4 shadow-lg border border-slate-700 flex flex-col items-center justify-center">
                                <span class="text-slate-400 text-xs font-bold mb-1 uppercase tracking-widest"><i class="far fa-stop-circle mr-1"></i> Fin</span>
                                <input type="time" id="fin-hora-fin" name="hora_fin" class="bg-transparent text-red-400 font-mono text-xl text-center w-full outline-none pointer-events-none" readonly>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 mb-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Clasificación de Servicio</label>
                                <select id="fin-tipo" name="tipo_servicio" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-700 font-medium outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all cursor-pointer" required>
                                    <option value="Preventivo">Mantenimiento Preventivo</option>
                                    <option value="Correctivo">Mantenimiento Correctivo</option>
                                    <option value="Predictivo">Mantenimiento Predictivo</option>
                                    <option value="Auxilio Vial">Auxilio Vial / Rescate</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Siguiente Cita</label>
                                <input type="date" id="fin-proximo" name="proximo_servicio" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-700 font-medium outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all cursor-pointer">
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 mb-6">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Reporte Técnico / Refacciones Usadas</label>
                            <textarea id="fin-descripcion" name="descripcion_cierre" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 resize-none transition-all" placeholder="Ej. Cambio de banda de distribución, 4 balatas y relleno de fluidos..." required></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Evidencia Gráfica</label>
                                <div class="flex-1 border-2 border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center bg-slate-50 hover:bg-slate-100 transition-colors cursor-pointer relative py-5 group">
                                    <input type="file" name="foto" id="fin-foto" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" onchange="mostrarNombreFoto()">
                                    <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center text-slate-400 mb-2 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-camera text-xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-600 px-2 text-center" id="nombre-archivo">Capturar o Subir</p>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Firma Operador</label>
                                    <button type="button" onclick="limpiarFirma()" class="text-xs text-slate-400 hover:text-red-500 transition-colors" title="Borrar y firmar de nuevo"><i class="fas fa-undo-alt mr-1"></i> Borrar</button>
                                </div>
                                <div class="flex-1 border border-slate-200 rounded-lg bg-[#fefce8] overflow-hidden relative shadow-inner">
                                    <div class="absolute bottom-6 left-4 right-4 border-b-2 border-slate-300 border-dashed pointer-events-none"></div>
                                    <canvas id="pizarra-firma" height="110" class="w-full cursor-crosshair touch-none relative z-10"></canvas>
                                </div>
                                <input type="hidden" id="firma-base64" name="firma_base64">
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="button" onclick="cerrarModalFinalizar()" class="w-1/3 bg-transparent border-2 border-slate-300 text-slate-600 font-bold py-3.5 rounded-xl hover:bg-slate-200 transition-colors uppercase tracking-wide text-sm">Cancelar</button>
                            <button type="submit" class="w-2/3 bg-slate-800 text-white font-bold py-3.5 rounded-xl hover:bg-slate-900 shadow-xl shadow-slate-300/50 transition-all transform hover:-translate-y-1 uppercase tracking-wide text-sm flex items-center justify-center gap-2">
                                <i class="fas fa-check-double"></i> Sellar Orden
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="modal-escaner" class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm hidden items-center justify-center z-[60] p-4 transition-opacity">
                <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-300 relative">
                    
                    <div class="bg-indigo-600 p-5 flex justify-between items-center text-white">
                        <h3 class="text-xl font-black tracking-tight flex items-center">
                            <i class="fas fa-qrcode mr-2"></i> Escanear Unidad 
                        </h3>
                        <button onclick="cerrarEscanerQR()" class="text-indigo-200 hover:text-white text-3xl transition-colors leading-none">&times;</button>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-center text-slate-500 text-sm mb-4 font-medium">Apunta la cámara al código QR del camión</p>
                        <div id="lector-qr" class="w-full rounded-2xl overflow-hidden border-4 border-indigo-100 shadow-inner bg-slate-100 min-h-[300px] flex items-center justify-center relative">
                            </div>
                    </div>
                    
                    <div class="p-4 bg-slate-50 border-t border-slate-100 text-center">
                        <button onclick="cerrarEscanerQR()" class="px-6 py-2 bg-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-300 transition-colors text-sm">Cancelar</button>
                    </div>
                </div>
            </div>

        </main>
    </div>

<script src="js/auth.js"></script>
<script src="js/mecanico.js"></script>

</body>
</html>