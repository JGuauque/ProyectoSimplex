document.addEventListener("DOMContentLoaded", () => {
    /* ===================== VARIABLES GLOBALES ===================== */
    const btnExportPdf = document.getElementById("btnExportPdf");

    /* ===================== EXPORTAR PDF ===================== */
    if (btnExportPdf) {
        btnExportPdf.addEventListener("click", () => {
            // Mostrar loader
            const originalText = btnExportPdf.innerHTML;
            btnExportPdf.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generando PDF...';
            btnExportPdf.disabled = true;

            // Hacer la petición al servidor
            fetch('/prestamos/exportar-pdf', {
                method: 'GET',
                headers: {
                    'Accept': 'application/pdf',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.blob();
            })
            .then(blob => {
                // Crear enlace para descargar el PDF
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `reporte_prestamos_${new Date().toISOString().slice(0,10)}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                
                // Restaurar botón
                btnExportPdf.innerHTML = originalText;
                btnExportPdf.disabled = false;
                
                // Opcional: Mostrar mensaje de éxito
                mostrarNotificacion('PDF generado exitosamente', 'success');
            })
            .catch(error => {
                console.error('Error:', error);
                btnExportPdf.innerHTML = originalText;
                btnExportPdf.disabled = false;
                
                // Mostrar mensaje de error
                mostrarNotificacion('Error al generar el PDF', 'error');
            });
        });
    }

    /* ===================== FUNCIÓN DE NOTIFICACIÓN ===================== */
    function mostrarNotificacion(mensaje, tipo) {
        // Usar Toastr, SweetAlert o un sistema propio
        if (typeof toastr !== 'undefined') {
            toastr[tipo === 'success' ? 'success' : 'error'](mensaje);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: tipo,
                title: mensaje,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            alert(mensaje);
        }
    }
});