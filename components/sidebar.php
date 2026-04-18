<div class="brand">
    <img src="assets/img/logo.png" alt="Logo Sistema" class="logo-pequeno">
    <span>OptiFleet</span>
</div>

<!-- Botón para cerrar el menú, solo visible en móvil -->
<button 
    onclick="cerrarSidebar()" 
    id="btn-cerrar-sidebar"
    style="
        display: none;
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 101;
        background: #f1f5f9;
        border: none;
        border-radius: 8px;
        width: 36px;
        height: 36px;
        font-size: 1.1rem;
        color: #64748b;
        cursor: pointer;
        align-items: center;
        justify-content: center;
    ">
    <i class="fas fa-arrow-left"></i>
</button>

<div class="view-container">

    <!-- Cada botón ahora también cierra el menú al hacer clic (para móvil) -->
    <div class="nav-item active" onclick="navegar('dashboard'); cerrarSidebar()">
        <i class="fas fa-chart-pie"></i>
        <span>Dashboard</span>
    </div>

    <div class="nav-item" onclick="navegar('flota'); cerrarSidebar()">
        <i class="fas fa-truck-moving"></i>
        <span>Flota</span>
    </div>

    <div class="nav-item" onclick="navegar('agenda'); cerrarSidebar()">
        <i class="fas fa-calendar-check"></i>
        <span>Agenda</span>
    </div>

    <div class="nav-item" onclick="navegar('historial'); cerrarSidebar()">
        <i class="fas fa-history"></i>
        <span>Historial</span>
    </div>

    <div class="nav-item" onclick="navegar('personal'); cerrarSidebar()">
        <i class="fas fa-users"></i>
        <span>Personal</span>
    </div>
</div>

<style>
    .btn-salir-elegante {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        background-color: transparent;
        color: #ef4444;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        position: absolute;
        bottom: 30px;
        width: calc(100% - 40px);
    }

    .btn-salir-elegante:hover {
        background-color: #fef2f2; 
        color: #dc2626; 
    }
    .btn-salir-elegante i { 
        width: 30px; 
        font-size: 1.2rem; 
    }

    html.dark .btn-salir-elegante { color: #fca5a5; }
    html.dark .btn-salir-elegante:hover { background-color: #450a0a; color: #fecaca; }
</style>

<button onclick="confirmarSalida()" class="btn-salir-elegante">
    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
</button>

<script>
    /* --- CERRAR SESIÓN (igual que antes) --- */
    function confirmarSalida() {
        Swal.fire({
            title: '¿Cerrar Sesión?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'api/auth/logout.php';
            }
        });
    }

    /* --- ABRIR EL MENÚ LATERAL --- */
    function abrirSidebar() {
        document.querySelector('.sidebar').classList.add('abierto');
        document.getElementById('sidebar-overlay').classList.add('visible');
        document.body.style.overflow = 'hidden'; /* evita que el fondo haga scroll */
    }

    /* --- CERRAR EL MENÚ LATERAL ---
       Quita la clase "abierto" para que el menú regrese fuera de pantalla */
    function cerrarSidebar() {
        document.querySelector('.sidebar').classList.remove('abierto');
        document.getElementById('sidebar-overlay').classList.remove('visible');
        document.body.style.overflow = ''; /* restaura el scroll */
    }
</script>
