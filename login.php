<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Mantenimiento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* ---- FONDO: la foto ocupa toda la pantalla ---- */
        .login-bg {
            min-height: 100vh;
            width: 100%;
            background: url('assets/img/fondo_login.webp') center/cover no-repeat;
            display: flex;
            align-items: center;     
            justify-content: center;
            padding: 20px;
        }

        /* ---- TARJETA OSCURA ---- */
        .login-card {
            background: rgba(10, 15, 30, 0.85);  /* negro azulado semitransparente */
            backdrop-filter: blur(12px);           /* desenfoca el fondo detrás */
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        /* ---- ÍCONO SUPERIOR ---- */
        .logo-icon {
            font-size: 2.5rem;
            color: #3b82f6;
            margin-bottom: 16px;
        }

        /* ---- TÍTULOS ---- */
        .login-card h2 {
            color: #f8fafc;
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .login-card p {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 36px;
        }

        /* ---- ETIQUETAS ---- */
        label {
            display: block;
            text-align: left;
            font-size: 0.8rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ---- GRUPO DE INPUT ---- */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        /* ---- INPUTS ---- */
        .input-control {
            width: 100%;
            padding: 13px 16px;
            background: rgba(255,255,255,0.05);   /* casi transparente */
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            font-size: 0.95rem;
            color: #f8fafc;
            outline: none;
            transition: border-color 0.2s;
        }

        /* Cuando el input está seleccionado */
        .input-control:focus {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.08);
        }

        /* Ícono del ojo para ver contraseña */
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 38px;
            color: #475569;
            cursor: pointer;
            transition: color 0.2s;
        }

        .toggle-password:hover { color: #94a3b8; }

        /* ---- MENSAJE DE ERROR ---- */
        #mensaje-error {
            color: #fca5a5;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 16px;
            display: none;
        }

        /* ---- BOTÓN PRINCIPAL ---- */
        .btn-primary {
            width: 100%;
            padding: 13px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-bottom: 12px;
            letter-spacing: 0.3px;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* ---- BOTÓN SECUNDARIO ---- */
        .btn-secondary {
            width: 100%;
            padding: 13px;
            background: transparent;
            color: #94a3b8;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.05);
            color: #f8fafc;
            border-color: rgba(255,255,255,0.25);
        }

        /* ---- LÍNEA DIVISORA ---- */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: #334155;
            font-size: 0.8rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }

        /* ================================================
           RESPONSIVE MÓVIL
           ================================================ */
        @media (max-width: 768px) {

            /* El fondo ocupa toda la pantalla sin padding lateral */
            .login-bg {
                padding: 0;
                align-items: flex-end;
            
            .login-card {
                max-width: 100%;
                width: 100%;
                min-height: 100vh;
                border-radius: 24px 24px 0 0;
                padding: 36px 24px 48px;
                border-left: none;
                border-right: none;
                border-bottom: none;
                display: flex;             
                flex-direction: column;
                justify-content: center;
    }
}

        @media (max-width: 480px) {
            .login-card h2 { font-size: 1.4rem; }
            .btn-primary, .btn-secondary { padding: 12px; }
        }
    </style>
</head>
<body>

    <div class="login-bg">
        <div class="login-card">

            <!-- Ícono y títulos -->
            <i class="fas fa-check-double logo-icon"></i>
            <h2>OptiFleet</h2>
            <p>Ingreso de Personal</p>

            <!-- Mensaje de error (oculto por defecto) -->
            <div id="mensaje-error"></div>

            <!-- Formulario -->
            <form id="formLogin">

                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input
                        type="email"
                        name="email"
                        class="input-control"
                        value="admin@test.com"
                        required>
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        id="passInput"
                        class="input-control"
                        value="1234"
                        required>
                    <i class="fas fa-eye toggle-password" id="togglePass"></i>
                </div>

                <button type="submit" class="btn-primary">
                    Iniciar Sesión
                </button>

                <div class="divider">o</div>

                <button type="button" class="btn-secondary" onclick="abrirConsultaUnidad()">
                    <i class="fas fa-search"></i> Consultar Estado de Unidad
                </button>

            </form>
        </div>
    </div>

    <script>
        /* ---- Mostrar/ocultar contraseña ---- */
        document.getElementById('togglePass').addEventListener('click', function() {
            const input = document.getElementById('passInput');
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                this.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        /* ---- Enviar formulario ---- */
        document.getElementById('formLogin').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const msjError = document.getElementById('mensaje-error');
            msjError.style.display = 'none';

            fetch('api/auth/login.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: 'Sesión iniciada como ' + data.usuario.rol,
                        showConfirmButton: false,
                        timer: 1500,
                        background: '#0f172a',
                        color: '#f8fafc'
                    }).then(() => {
                        const rol = data.usuario.rol.toLowerCase();
                        window.location.href = rol === 'administrador' ? 'index.php' : 'mecanico.php';
                    });
                } else {
                    msjError.innerText = data.mensaje || 'Correo o contraseña incorrectos';
                    msjError.style.display = 'block';
                }
            })
            .catch(() => {
                msjError.innerText = 'Error de conexión con el servidor.';
                msjError.style.display = 'block';
            });
        });

        /* ---- Consultar estado de unidad (igual que antes) ---- */
        function abrirConsultaUnidad() {
            Swal.fire({
                title: 'Consultar Estado',
                text: 'Ingresa el N° Económico o Placas del vehículo:',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off',
                    placeholder: 'Ej: T-005 o 58-AK-9'
                },
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-search"></i> Buscar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#475569',
                background: '#0f172a',
                color: '#f8fafc',
                showLoaderOnConfirm: true,
                preConfirm: (valor) => {
                    if (!valor) {
                        Swal.showValidationMessage('Escribe un N° Económico o Placas.');
                        return false;
                    }
                    return fetch(`api/unidades/consultar_estado.php?unidad=${encodeURIComponent(valor)}`)
                        .then(res => res.json())
                        .catch(() => {
                            Swal.showValidationMessage('Error de conexión.');
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = result.value;
                    if (data.success) {
                        const enTaller = data.data.estatus === 'En Mantenimiento';
                        const color = enTaller ? '#f59e0b' : '#10b981';
                        const icono = enTaller ? 'warning' : 'success';

                        Swal.fire({
                            title: `Unidad ${data.data.economico}`,
                            html: `
                                <div style="text-align:left; background:rgba(255,255,255,0.05); padding:16px; border-radius:12px; border:1px solid rgba(255,255,255,0.1);">
                                    <p style="margin-bottom:10px; color:#cbd5e1;"><strong style="color:#f8fafc;">Marca/Modelo:</strong> ${data.data.marca} ${data.data.modelo}</p>
                                    <p style="color:#cbd5e1;"><strong style="color:#f8fafc;">Estado:</strong> <span style="color:${color}; font-weight:800;">${data.data.estatus}</span></p>
                                </div>`,
                            icon: icono,
                            confirmButtonColor: '#2563eb',
                            confirmButtonText: 'Cerrar',
                            background: '#0f172a',
                            color: '#f8fafc'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'No Encontrada',
                            text: data.message,
                            confirmButtonColor: '#ef4444',
                            background: '#0f172a',
                            color: '#f8fafc'
                        });
                    }
                }
            });
        }
    </script>
</body>
</html>