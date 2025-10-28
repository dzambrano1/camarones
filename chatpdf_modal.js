// Open ChatPDF Modal with ChatGPT-like interface
function openChatPDFWindow(sourceId, filename) {
    console.log('Opening ChatPDF window with sourceId:', sourceId, 'filename:', filename);
    
    // Extract tagid from filename for display
    const tagid = filename.split('_')[1] || 'Unknown';
    
    const modal = document.createElement('div');
    modal.id = 'chatpdf-modal';
    modal.style.cssText = `position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.75); z-index: 99999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px); animation: fadeIn 0.2s;`;

    modal.innerHTML = `
        <div class="chatpdf-container" style="width: 90%; max-width: 1200px; height: 85vh; background: #343541; border-radius: 12px; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.5); overflow: hidden; animation: slideUp 0.3s;">
            <div style="background: linear-gradient(90deg, #2d2d38 0%, #343541 100%); padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #4d4d4f;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10a37f 0%, #0d8a69 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(16,163,127,0.3);">
                        <i class="fas fa-user-doctor" style="color: white; font-size: 18px;"></i>
                    </div>
                    <div>
                        <h3 style="color: #ececf1; margin: 0; font-size: 16px; font-weight: 600;">Veterinario ChatGPT</h3>
                        <p style="color: #8e8ea0; margin: 2px 0 0 0; font-size: 12px;">Tag ID: ${tagid} • ${filename}</p>
                    </div>
                </div>
                <button id="close-chatpdf" style="background: #40414f; border: 1px solid #565869; color: #ececf1; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 4px; width: 60px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="chatpdf-messages" style="flex: 1; overflow-y: auto; background: #343541; display: flex; flex-direction: column;">
                <div style="background: #444654; padding: 24px; border-bottom: 1px solid #4d4d4f;">
                    <div style="max-width: 800px; margin: 0 auto; display: flex; gap: 16px;">
                        <div style="width: 30px; height: 30px; background: linear-gradient(135deg, #10a37f 0%, #0d8a69 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-stethoscope" style="color: white; font-size: 14px;"></i>
                        </div>
                        <div style="color: #ececf1; line-height: 1.6; font-size: 14px;">
                            <strong style="display: block; margin-bottom: 8px; font-size: 15px;">¡Hola! Soy tu Veterinario IA Especializado</strong>
                            He analizado el historial médico completo del estanque <strong style="color: #10a37f;">Tag ID: ${tagid}</strong>.
                            <br><br>Puedo ayudarte con:
                            <ul style="margin: 12px 0; padding-left: 20px;">
                                <li>Estado de salud actual del estanque</li>
                                <li>Análisis de parámetros del agua</li>
                                <li>Producción y biomasa</li>
                                <li>Recomendaciones veterinarias</li>
                            </ul>¿En qué te puedo asistir?
                        </div>
                    </div>
                </div>
            </div>
            <div style="background: #40414f; padding: 16px 24px; border-top: 1px solid #565869;">
                <div style="max-width: 800px; margin: 0 auto 12px auto; display: flex; gap: 8px; flex-wrap: wrap;">
                    <button class="chatpdf-quick-btn" data-question="¿Cuál es el estado de salud actual de este estanque?" style="background: #343541; border: 1px solid #565869; color: #ececf1; padding: 8px 14px; border-radius: 20px; cursor: pointer; font-size: 13px; transition: all 0.2s; white-space: nowrap;">
                        <i class="fas fa-heartbeat"></i> Estado de Salud
                    </button>
                    <button class="chatpdf-quick-btn" data-question="¿Qué parámetros del agua están registrados?" style="background: #343541; border: 1px solid #565869; color: #ececf1; padding: 8px 14px; border-radius: 20px; cursor: pointer; font-size: 13px; transition: all 0.2s; white-space: nowrap;">
                        <i class="fas fa-tint"></i> Parámetros Agua
                    </button>
                    <button class="chatpdf-quick-btn" data-question="¿Cómo está la producción y biomasa?" style="background: #343541; border: 1px solid #565869; color: #ececf1; padding: 8px 14px; border-radius: 20px; cursor: pointer; font-size: 13px; transition: all 0.2s; white-space: nowrap;">
                        <i class="fas fa-chart-line"></i> Producción
                    </button>
                    <button class="chatpdf-quick-btn" data-question="Dame recomendaciones veterinarias para este estanque" style="background: #343541; border: 1px solid #565869; color: #ececf1; padding: 8px 14px; border-radius: 20px; cursor: pointer; font-size: 13px; transition: all 0.2s; white-space: nowrap;">
                        <i class="fas fa-clipboard-check"></i> Recomendaciones
                    </button>
                </div>
                <div style="max-width: 800px; margin: 0 auto; display: flex; gap: 8px; align-items: center;">
                    <input type="text" id="chatpdf-input" placeholder="Pregúntame sobre este estanque..." style="flex: 1; background: #40414f; border: 1px solid #565869; color: #ececf1; font-size: 15px; outline: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; padding: 16px 20px; min-width: 0; border-radius: 10px; transition: border-color 0.2s; width: 100%;" onfocus="this.style.borderColor='#10a37f'" onblur="this.style.borderColor='#565869'" />
                    <button id="chatpdf-send" style="background: linear-gradient(135deg, #10a37f 0%, #0d8a69 100%); border: none; color: white; padding: 10px; border-radius: 8px; cursor: pointer; font-size: 16px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(16,163,127,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0; width: 44px; height: 44px;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .chatpdf-quick-btn:hover { background: #40414f !important; border-color: #8e8ea0 !important; transform: translateY(-1px); }
        #close-chatpdf:hover { background: #565869 !important; transform: translateY(-1px); }
        #chatpdf-send:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16,163,127,0.4) !important; }
        #chatpdf-messages::-webkit-scrollbar { width: 8px; }
        #chatpdf-messages::-webkit-scrollbar-track { background: #343541; }
        #chatpdf-messages::-webkit-scrollbar-thumb { background: #565869; border-radius: 4px; }
        #chatpdf-messages::-webkit-scrollbar-thumb:hover { background: #6e6e80; }
    `;
    document.head.appendChild(style);

    const input = document.getElementById('chatpdf-input');
    const sendBtn = document.getElementById('chatpdf-send');
    const closeBtn = document.getElementById('close-chatpdf');
    const messagesContainer = document.getElementById('chatpdf-messages');
    
    closeBtn.onclick = () => {
        document.body.removeChild(modal);
        document.body.style.overflow = '';
        document.head.removeChild(style);
    };

    const sendMessage = async () => {
        const message = input.value.trim();
        if (!message) return;

        addChatPDFMessage('user', message, messagesContainer);
        input.value = '';
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

        try {
            const response = await fetch('chatpdf_proxy.php?action=chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    sourceId: sourceId,
                    messages: [{ role: 'user', content: message }]
                })
            });

            const data = await response.json();

            if (response.ok && data.content) {
                addChatPDFMessage('assistant', data.content, messagesContainer);
            } else {
                addChatPDFMessage('error', data.error || 'Error al obtener respuesta', messagesContainer);
            }
        } catch (error) {
            addChatPDFMessage('error', 'Error de conexión: ' + error.message, messagesContainer);
        }

        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    sendBtn.onclick = sendMessage;
    input.onkeypress = (e) => { if (e.key === 'Enter') sendMessage(); };
    document.querySelectorAll('.chatpdf-quick-btn').forEach(btn => {
        btn.onclick = () => {
            input.value = btn.getAttribute('data-question');
            sendMessage();
        };
    });
    input.focus();
}

function addChatPDFMessage(role, content, container) {
    const isUser = role === 'user';
    const isError = role === 'error';
    
    const messageDiv = document.createElement('div');
    messageDiv.style.cssText = `background: ${isUser ? '#343541' : (isError ? '#ff6b6b22' : '#444654')}; padding: 24px; border-bottom: 1px solid #4d4d4f;`;
    messageDiv.innerHTML = `
        <div style="max-width: 800px; margin: 0 auto; display: flex; gap: 16px;">
            <div style="width: 30px; height: 30px; background: ${isUser ? '#5436da' : (isError ? '#ff6b6b' : 'linear-gradient(135deg, #10a37f 0%, #0d8a69 100%)')}; border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-${isUser ? 'user' : (isError ? 'exclamation-triangle' : 'stethoscope')}" style="color: white; font-size: 14px;"></i>
            </div>
            <div style="color: ${isError ? '#ff6b6b' : '#ececf1'}; line-height: 1.6; font-size: 14px; flex: 1;">${content}</div>
        </div>
    `;
    container.appendChild(messageDiv);
    container.scrollTop = container.scrollHeight;
}

// Make the function globally accessible immediately
window.generateAndUploadPDF = async function(tagid) {
    if (!tagid) {
        alert('Error: No se proporcionó el ID del estanque');
        return;
    }

    console.log('Starting PDF generation for tagid:', tagid);

    // Show loading message
    Swal.fire({
        title: 'Generando PDF...',
        text: 'Por favor espere mientras se genera el reporte y se sube a la IA',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Make AJAX request to generate and upload PDF using camarones_report.php
    $.ajax({
        url: 'camarones_report.php',
        type: 'GET',
        data: {
            tagid: tagid,
            upload_to_chatpdf: '1'
        },
        dataType: 'json',
        cache: false,
        success: function(response) {
            console.log('AJAX Success Response:', response);
            Swal.close();
            
            if (response.success) {
                // Check upload status
                const uploadSuccess = response.upload_result && response.upload_result.success;
                const uploadError = response.upload_result && response.upload_result.error ? response.upload_result.error : 'Error desconocido';
                
                if (uploadSuccess) {
                    // Upload successful - directly open chat modal (streamlined UX)
                    const sourceId = response.upload_result.sourceId;
                    openChatPDFWindow(sourceId, response.filename);
                } else {
                    // Upload failed - show error with options
                    Swal.fire({
                        icon: 'warning',
                        title: 'PDF Generado (Error al subir a IA)',
                        html: `
                            <p><strong>Archivo:</strong> ${response.filename}</p>
                            <p><strong>Estado de subida:</strong> Fallido</p>
                            <p style="color: #ff6b6b;"><small>Error: ${uploadError}</small></p>
                            <p><strong>¿Qué deseas hacer?</strong></p>
                            <p>• Ver el PDF generado</p>
                            <p>• Reintentar la subida</p>
                        `,
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'Reintentar',
                        denyButtonText: 'Ver PDF',
                        cancelButtonText: 'Cerrar',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Retry the upload
                            window.generateAndUploadPDF(tagid);
                        } else if (result.isDenied) {
                            // Open PDF directly
                            const directUrl = window.location.origin + window.location.pathname.replace('inventario_camarones.php', '') + 'reports/' + response.filename;
                            const viewerUrl = window.location.origin + window.location.pathname.replace('inventario_camarones.php', '') + 'view_pdf.php?file=' + response.filename;
                            
                            console.log('No sourceId, trying direct PDF access:', directUrl);
                            const testWindow = window.open(directUrl, '_blank');
                            
                            setTimeout(() => {
                                if (testWindow && testWindow.closed) {
                                    console.log('Direct access failed, trying viewer...');
                                    window.open(viewerUrl, '_blank');
                                }
                            }, 1000);
                        }
                    });
                }
            } else {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error al Generar PDF',
                    text: response.error || response.message || 'Error desconocido',
                    confirmButtonText: 'Cerrar'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            
            console.error('=== AJAX ERROR DETAILS ===');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('XHR Status:', xhr.status);
            console.error('XHR Response Text:', xhr.responseText);
            console.error('XHR Ready State:', xhr.readyState);
            console.error('========================');
            
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                html: `
                    <p>No se pudo conectar con el servidor.</p>
                    <p><small>Status: ${status}</small></p>
                    <p><small>Error: ${error}</small></p>
                    <p><small>HTTP Status: ${xhr.status}</small></p>
                    <details>
                        <summary>Detalles técnicos</summary>
                        <pre style="text-align: left; font-size: 10px; max-height: 200px; overflow: auto;">${xhr.responseText}</pre>
                    </details>
                `,
                confirmButtonText: 'Cerrar'
            });
        }
    });
};
