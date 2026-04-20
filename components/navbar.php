<style>
    /* La barra superior usa flexbox para alinear cosas */
    .mi-navbar {
        display: flex;
        justify-content: space-between;  /* izquierda y derecha separados */
        align-items: center;
        width: 100%;
        padding: 12px 24px;
        background-color: #ffffff;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Lado izquierdo: botón ☰ + título */
    .mi-navbar-izquierda {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mi-navbar-titulo {
        font-size: 1.4rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }

    /* Lado derecho: luna + perfil */
    .mi-navbar-derecha {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    /* Botón de tema (luna/sol) */
    .btn-luna {
        background: none;
        border: none;
        font-size: 1.3rem;
        color: #64748b;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .btn-luna:hover { background-color: #f1f5f9; }

    /* Caja del perfil */
    .perfil-caja {
        display: flex;
        align-items: center;
        gap: 12px;
        border-left: 2px solid #e2e8f0;
        padding-left: 20px;
    }

    .perfil-textos { text-align: right; }

    .perfil-rol {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
    }

    .perfil-admin {
        margin: 0;
        font-size: 0.75rem;
        font-weight: 800;
        color: #2563eb;
    }

    .perfil-avatar {
        height: 42px;
        width: 42px;
        border-radius: 50%;
        background-color: #2563eb;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        flex-shrink: 0; /* evita que se achique */
    }

    /* Modo oscuro */
    html.dark .mi-navbar { background-color: #1e293b; border-bottom-color: #334155; }
    html.dark .mi-navbar-titulo, html.dark .perfil-rol { color: #f8fafc; }
    html.dark .perfil-caja { border-left-color: #334155; }
    html.dark .btn-luna { color: #f8fafc; }
    html.dark .btn-luna:hover { background-color: #334155; }

    /* En celular ocultamos el texto del perfil */
    @media (max-width: 768px) {
        .mi-navbar { padding: 10px 14px; }
        .perfil-textos { display: none; }
        .perfil-caja { border-left: none; padding-left: 0; }
    }
</style>

<div class="mi-navbar">

    <!-- IZQUIERDA: botón de menú + título -->
    <div class="mi-navbar-izquierda">

        <!-- Botón hamburguesa ☰
             Llamo a abrirSidebar() que está definida en sidebar.php
             El CSS lo oculta en desktop y lo muestra en móvil -->
        <button class="btn-hamburguesa" onclick="abrirSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <h1 class="mi-navbar-titulo">Panel de Control</h1>
    </div>

    <!-- DERECHA: tema + perfil -->
    <div class="mi-navbar-derecha">

        <button id="btn-theme" onclick="toggleTheme()" class="btn-luna" title="Cambiar Tema">
            <i id="theme-icon" class="fas fa-moon"></i>
        </button>

        <div class="perfil-caja">
            <div class="perfil-textos">
                <p class="perfil-rol">Jefe de Taller</p>
                <p class="perfil-admin">ADMINISTRADOR</p>
            </div>
            <div class="perfil-avatar">JE</div>
        </div>

    </div>
</div>