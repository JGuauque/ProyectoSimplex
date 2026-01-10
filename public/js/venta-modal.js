// public/js/venta-modal.js

// Variables globales
let ultimaVentaId = null;
let enviandoEmail = false;

// Función para mostrar modal con datos de la venta
function mostrarModalComprobante(data) {
    console.log('📦 Mostrando modal con datos:', data);
    
    // Guardar el ID de la venta
    ultimaVentaId = data.venta.id;
    console.log('✅ Venta ID guardado:', ultimaVentaId);

    // Verificar rutas (depuración)
    if (ultimaVentaId) {
        verificarRutas(ultimaVentaId);
    }
    
    // Actualizar número de factura en el modal
    const modalFacturaNumero = document.getElementById('modalFacturaNumero');
    if (modalFacturaNumero) {
        modalFacturaNumero.textContent = data.factura_numero || 'N/A';
    }
    
    // Prellenar campos si hay datos del cliente
    const emailCliente = document.getElementById('emailCliente');
    const whatsappCliente = document.getElementById('whatsappCliente');
    
    if (emailCliente && data.cliente_email) {
        emailCliente.value = data.cliente_email;
    }
    
    if (whatsappCliente && data.cliente_telefono) {
        whatsappCliente.value = data.cliente_telefono;
    }
    
    // Mostrar modal
    const modal = document.getElementById('comprobanteModal');
    const overlay = document.getElementById('modalOverlay');
    
    if (modal && overlay) {
        modal.style.display = 'block';
        overlay.style.display = 'block';
        console.log('✅ Modal mostrado');
    } else {
        console.error('❌ No se encontró el modal o overlay');
    }
    
    // Configurar event listeners después de mostrar el modal
    setTimeout(configurarEventListenersModal, 100);
}

// Función para verificar si las rutas existen
function verificarRutas(ventaId) {
    console.log('=== VERIFICANDO RUTAS ===');
    console.log('Ruta 80mm:', `/factura/pdf/80mm/${ventaId}`);
    console.log('Ruta A4:', `/factura/pdf/a4/${ventaId}`);

    // Hacer una prueba de fetch a la ruta (opcional)
    fetch(`/factura/pdf/80mm/${ventaId}`)
        .then(response => {
            console.log('Respuesta 80mm:', response.status, response.ok);
        })
        .catch(error => {
            console.error('Error en ruta 80mm:', error);
        });
}

// Configurar event listeners del modal
function configurarEventListenersModal() {
    console.log('🔧 Configurando event listeners del modal...');
    
    // Botón de enviar email
    const btnEnviarEmail = document.getElementById('btnEnviarEmail');
    if (btnEnviarEmail) {
        // Remover listener anterior si existe
        btnEnviarEmail.removeEventListener('click', enviarComprobanteEmail);
        btnEnviarEmail.addEventListener('click', enviarComprobanteEmail);
        console.log('✅ Botón email configurado');
    } else {
        console.error('❌ Botón btnEnviarEmail no encontrado');
    }
    
    // Botón de enviar WhatsApp
    const btnEnviarWhatsApp = document.querySelector('[data-tipo="whatsapp"]');
    if (btnEnviarWhatsApp) {
        btnEnviarWhatsApp.removeEventListener('click', enviarComprobanteWhatsApp);
        btnEnviarWhatsApp.addEventListener('click', enviarComprobanteWhatsApp);
        console.log('✅ Botón WhatsApp configurado');
    }

    // IMPORTANTE: Configurar botones de impresión CORRECTAMENTE
    configurarBotonesImpresion();
    
    // // Botones de impresión
    // document.querySelectorAll('.btn-imprimir').forEach(btn => {
    //     btn.addEventListener('click', function() {
    //         const formato = this.getAttribute('data-formato');
    //         if (!ultimaVentaId) {
    //             alert('No hay información de venta disponible');
    //             return;
    //         }
            
    //         const url = `/factura/pdf/${formato === '80MM' ? '80mm' : 'a4'}/${ultimaVentaId}`;
    //         window.open(url, '_blank');
    //     });
    // });
    
    // Cerrar modal
    const closeBtn = document.querySelector('.close-btn');
    const overlay = document.getElementById('modalOverlay');
    const btnNuevoComprobante = document.getElementById('btnNuevoComprobante');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', cerrarModal);
    }
    
    if (overlay) {
        overlay.addEventListener('click', cerrarModal);
    }
    
    if (btnNuevoComprobante) {
        btnNuevoComprobante.addEventListener('click', cerrarModal);
    }
}

// Configurar botones de impresión (función separada para mejor control)
function configurarBotonesImpresion() {
    console.log('🖨️ Configurando botones de impresión...');
    
    // Seleccionar TODOS los botones con clase btn-imprimir
    const botonesImpresion = document.querySelectorAll('.btn-imprimir');
    console.log(`Encontrados ${botonesImpresion.length} botones de impresión`);
    
    botonesImpresion.forEach((btn, index) => {
        console.log(`Botón ${index}:`, btn);
        
        // IMPORTANTE: Prevenir el comportamiento por defecto (submit)
        btn.addEventListener('click', function(e) {
            console.log(`🖨️ Botón ${index} clickeado`);
            
            // PREVENIR que el botón haga submit del formulario
            e.preventDefault();
            e.stopPropagation();
            
            const formato = this.getAttribute('data-formato');
            console.log('Formato:', formato, 'Venta ID:', ultimaVentaId);
            
            if (!ultimaVentaId) {
                alert('No hay información de venta disponible');
                return;
            }
            
            // Construir la URL correctamente
            let url = '';
            
            if (formato === '80MM') {
                url = `/factura/pdf/80mm/${ultimaVentaId}`;
            } else if (formato === 'A4') {
                url = `/factura/pdf/a4/${ultimaVentaId}`;
            }
            
            console.log('URL generada:', url);
            
            // Abrir en nueva pestaña
            if (url) {
                window.open(url, '_blank', 'noopener,noreferrer');
            }
        });
    });
}

// Función para enviar comprobante por email
async function enviarComprobanteEmail() {
    console.log('📧 Función enviarComprobanteEmail ejecutada');
    
    if (enviandoEmail) {
        console.log('⚠️ Ya se está enviando un email');
        return;
    }
    
    const emailInput = document.getElementById('emailCliente');
    const email = emailInput ? emailInput.value.trim() : '';
    const btnEmail = document.getElementById('btnEnviarEmail');
    
    console.log('📝 Email:', email, 'Venta ID:', ultimaVentaId, 'Botón:', btnEmail);
    
    // Validaciones básicas
    if (!email) {
        alert('Por favor ingresa un correo electrónico');
        if (emailInput) emailInput.focus();
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Por favor ingresa un correo electrónico válido');
        if (emailInput) emailInput.focus();
        return;
    }
    
    if (!ultimaVentaId) {
        alert('Error: No hay información de venta disponible');
        console.error('❌ ultimaVentaId es null o undefined');
        return;
    }
    
    // Mostrar loading
    enviandoEmail = true;
    const originalText = btnEmail.innerHTML;
    btnEmail.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enviando...';
    btnEmail.disabled = true;
    
    try {
        // Obtener token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const token = csrfToken ? csrfToken.getAttribute('content') : null;
        
        console.log('🔑 CSRF Token encontrado:', token ? 'Sí' : 'No');
        console.log('🚀 Enviando request a:', `/venta/${ultimaVentaId}/enviar-email`);
        
        // Enviar petición
        const response = await fetch(`/venta/${ultimaVentaId}/enviar-email`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                email: email,
                venta_id: ultimaVentaId
            })
        });
        
        console.log('📨 Response status:', response.status);
        
        const data = await response.json();
        console.log('📊 Response data:', data);
        
        if (data.success) {
            alert('✅ ' + data.message);
            // Limpiar campo después de enviar exitosamente
            if (emailInput) emailInput.value = '';
        } else {
            alert('❌ ' + data.message);
        }
        
    } catch (error) {
        console.error('💥 Error en fetch:', error);
        alert('Error de conexión. Por favor, intenta nuevamente.');
    } finally {
        // Restaurar botón
        enviandoEmail = false;
        if (btnEmail) {
            btnEmail.innerHTML = originalText;
            btnEmail.disabled = false;
        }
    }
}

// Función para enviar por WhatsApp (placeholder)
function enviarComprobanteWhatsApp() {
    console.log('📱 Función enviarComprobanteWhatsApp ejecutada');
    
    const whatsappInput = document.getElementById('whatsappCliente');
    const whatsapp = whatsappInput ? whatsappInput.value.trim() : '';
    
    if (!whatsapp) {
        alert('Por favor ingresa un número de WhatsApp');
        if (whatsappInput) whatsappInput.focus();
        return;
    }
    
    // Limpiar número
    const numeroLimpio = whatsapp.replace(/\D/g, '');
    
    if (numeroLimpio.length < 10) {
        alert('Por favor ingresa un número válido (mínimo 10 dígitos)');
        return;
    }
    
    const numeroFormateado = numeroLimpio.startsWith('+') ? numeroLimpio : `+${numeroLimpio}`;
    const facturaNumero = document.getElementById('modalFacturaNumero')?.textContent || '';
    const mensaje = `Hola, aquí está tu comprobante de compra en La Casa del Nintendo. Factura: ${facturaNumero}`;
    const whatsappUrl = `https://wa.me/${numeroFormateado}?text=${encodeURIComponent(mensaje)}`;
    
    console.log('🔗 Abriendo WhatsApp URL:', whatsappUrl);
    window.open(whatsappUrl, '_blank');
}

// Función para cerrar modal
function cerrarModal() {
    console.log('❌ Cerrando modal');
    
    const modal = document.getElementById('comprobanteModal');
    const overlay = document.getElementById('modalOverlay');
    
    if (modal) modal.style.display = 'none';
    if (overlay) overlay.style.display = 'none';
}

// // Cerrar modal con ESC
// document.addEventListener('keydown', function(e) {
//     if (e.key === 'Escape') {
//         cerrarModal();
//     }
// });

// ===== EVENT DELEGATION PARA BOTONES DIFÍCILES =====
// Esta es una solución más robusta para botones que no capturan los eventos
document.addEventListener('click', function(e) {
    // Para botones de impresión
    if (e.target && (
        e.target.classList.contains('btn-imprimir') || 
        e.target.closest('.btn-imprimir')
    )) {
        console.log('🖨️ Event delegation: Botón impresión clickeado');
        e.preventDefault();
        e.stopPropagation();
        
        const btn = e.target.closest('.btn-imprimir') || e.target;
        const formato = btn.getAttribute('data-formato');
        
        if (!ultimaVentaId) {
            alert('No hay información de venta disponible');
            return;
        }
        
        const url = `/factura/pdf/${formato === '80MM' ? '80mm' : 'a4'}/${ultimaVentaId}`;
        window.open(url, '_blank', 'noopener,noreferrer');
    }
    
    // Para botón de email
    if (e.target && (
        e.target.id === 'btnEnviarEmail' || 
        e.target.closest('#btnEnviarEmail')
    )) {
        console.log('📧 Event delegation: Botón email clickeado');
        enviarComprobanteEmail();
    }
    
    // Para botón de WhatsApp
    if (e.target && (
        e.target.dataset.tipo === 'whatsapp' || 
        e.target.closest('[data-tipo="whatsapp"]')
    )) {
        console.log('📱 Event delegation: Botón WhatsApp clickeado');
        enviarComprobanteWhatsApp();
    }
});


// Exportar funciones para uso global
window.mostrarModalComprobante = mostrarModalComprobante;
window.enviarComprobanteEmail = enviarComprobanteEmail;
window.enviarComprobanteWhatsApp = enviarComprobanteWhatsApp;
window.cerrarModal = cerrarModal;
window.verificarRutas = verificarRutas;

console.log('✅ venta-modal.js cargado correctamente');