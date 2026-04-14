function exportarFlotaExcel() {
    const tablaOriginal = document.getElementById("tabla-unidades"); // ID actualizado
    if (!tablaOriginal) return;

    let tablaParaExcel = tablaOriginal.cloneNode(true);
    
    // Quitamos la columna de "Acción" para que el Excel sea profesional
    const filas = tablaParaExcel.querySelectorAll('tr');
    filas.forEach(fila => {
        if (fila.lastElementChild) fila.removeChild(fila.lastElementChild);
    });

    const contenido = tablaParaExcel.outerHTML;
    const blob = new Blob(['\ufeff' + contenido], { type: 'application/vnd.ms-excel' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    
    a.href = url;
    a.download = "Inventario_Flota_" + new Date().toLocaleDateString() + ".xls";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Función para el buscador manual de unidades (T-101, T-102, etc.)
function filtrarFlota() {
    const texto = document.getElementById("buscador-flota").value.toUpperCase();
    const filas = document.querySelectorAll("#tabla-unidades-cuerpo tr");
    filas.forEach(fila => {
        fila.style.display = fila.innerText.toUpperCase().includes(texto) ? "" : "none";
    });
}

// ==========================================
// MAGIA DEL GENERADOR DE CÓDIGO QR
// ==========================================
let unidadActualQR = ''; 

function abrirQR(economico, marca, modelo) {
    unidadActualQR = economico;
    
    // Ahora lo abrimos con style.display igual que tus otros modales
    const modal = document.getElementById('modal-generador-qr');
    modal.style.display = 'flex';
    
    document.getElementById('qr-titulo-unidad').innerText = economico;
    
    const textoSubtitulo = (marca || '') + ' ' + (modelo || '');
    document.getElementById('qr-subtitulo').innerText = textoSubtitulo.trim() || 'UNIDAD VEHICULAR';
    
    const contenedorQR = document.getElementById('contenedor-qr');
    contenedorQR.innerHTML = ''; 
    
    new QRCode(contenedorQR, {
        text: economico,
        width: 220,
        height: 220,
        colorDark : "#1e293b", 
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
}

function cerrarModalQR() {
    // Lo cerramos ocultando el display
    document.getElementById('modal-generador-qr').style.display = 'none';
}

function imprimirQR() {
    // Tomamos la imagen que hizo la librería
    const qrImage = document.querySelector('#contenedor-qr img').src;
    const economico = document.getElementById('qr-titulo-unidad').innerText;
    
    // Abrimos una pestaña blanca invisible para mandar a la impresora
    const ventanaPrint = window.open('', '', 'width=600,height=600');
    ventanaPrint.document.write(`
        <html>
        <head>
            <title>Imprimir QR - ${economico}</title>
            <style>
                body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #fff; }
                .etiqueta { border: 3px dashed #cbd5e1; padding: 30px; text-align: center; border-radius: 15px; }
                h1 { font-size: 50px; margin: 0 0 15px 0; color: #1e293b; }
                img { width: 300px; height: 300px; }
                p { margin-top: 15px; color: #64748b; font-weight: bold; font-size: 18px; text-transform: uppercase; letter-spacing: 2px;}
            </style>
        </head>
        <body>
            <div class="etiqueta">
                <h1>${economico}</h1>
                <img src="${qrImage}" />
                <p>Uso Exclusivo - Taller</p>
            </div>
            <script>
                // Le damos medio segundo para cargar la foto y lanzamos la impresora
                setTimeout(() => { window.print(); window.close(); }, 500);
            </script>
        </body>
        </html>
    `);
    ventanaPrint.document.close();
}

function irAHistorialQR() {
    cerrarModalQR();
    
    // 1. Nos movemos a la pestaña Historial (usando tu función de navegación)
    if(typeof navegar === 'function') {
        navegar('historial');
    } else {
        console.warn("No encontré la función de navegar.");
    }
    
    // 2. Buscamos la barra de búsqueda del Historial y le escribimos el "T-101" automáticamente
    setTimeout(() => {
        // Busca el input por su placeholder (como se ve en tu foto)
        const buscador = document.querySelector('input[placeholder*="Buscar por unidad"]');
        if(buscador) {
            buscador.value = unidadActualQR;
            // Simulamos que el usuario presionó el teclado para que filtre
            buscador.dispatchEvent(new Event('keyup'));
        }
    }, 400); // 400 milisegundos de espera para que la pantalla del historial alcance a cargar
}