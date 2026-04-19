<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con <?= htmlspecialchars($destinatario['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; }
        .chat-container { max-width: 800px; margin: 20px auto; }
        .chat-card { border-radius: 16px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .chat-header { background: #075e54; color: white; padding: 12px 16px; }
        .chat-messages { height: 70vh; overflow-y: auto; background: #e5ddd5; padding: 16px; display: flex; flex-direction: column; }
        .message { max-width: 70%; margin-bottom: 12px; display: flex; flex-direction: column; }
        .message.sent { align-self: flex-end; }
        .message.received { align-self: flex-start; }
        .message-content { padding: 8px 12px; border-radius: 18px; position: relative; word-wrap: break-word; }
        .sent .message-content { background: #dcf8c6; border-bottom-right-radius: 4px; }
        .received .message-content { background: white; border-bottom-left-radius: 4px; }
        .message-meta { font-size: 0.7rem; color: #667781; margin-top: 4px; display: flex; gap: 8px; align-items: center; }
        .sent .message-meta { justify-content: flex-end; }
        .status-check { color: #34b7f1; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .input-area { background: white; padding: 12px; border-top: 1px solid #ddd; }
        .online-indicator { font-size: 0.8rem; }
        .online-indicator i { font-size: 0.6rem; }
    </style>
</head>
<body>
<div class="chat-container">
    <div class="card chat-card">
        <div class="chat-header d-flex align-items-center gap-3">
            <a href="<?= BASE_URL ?>?c=mensajes&a=chats" class="text-white"><i class="bi bi-arrow-left"></i></a>
            <?php if (!empty($destinatario['foto_base64'])): ?>
                <img src="<?= $destinatario['foto_base64'] ?>" class="avatar" alt="Avatar">
            <?php else: ?>
                <div class="avatar bg-secondary d-flex align-items-center justify-content-center text-white">
                    <i class="bi bi-person-fill"></i>
                </div>
            <?php endif; ?>
            <div class="flex-grow-1">
                <h5 class="mb-0"><?= htmlspecialchars($destinatario['nombre_completo']) ?></h5>
                <div class="online-indicator" id="estadoUsuario">
                    <!-- Se actualizará vía AJAX -->
                    <i class="bi bi-circle-fill text-success"></i> <span>En línea</span>
                </div>
            </div>
        </div>
        <div class="chat-messages" id="mensajesContainer">
            <div class="text-center text-muted py-4">Cargando mensajes...</div>
        </div>
        <div class="input-area">
            <form id="formMensaje" class="d-flex gap-2">
                <input type="hidden" id="destinatarioId" value="<?= $destinatario['id_usuario'] ?>">
                <textarea id="contenidoInput" class="form-control" placeholder="Escribe un mensaje..." rows="1" style="resize: none;"></textarea>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
            </form>
        </div>
    </div>
</div>

<script>
    const miId = <?= $_SESSION['usuario_id'] ?>;
    const destinatarioId = <?= $destinatario['id_usuario'] ?>;
    const baseUrl = '<?= BASE_URL ?>';
    let ultimoId = 0;
    let cargando = false;
    const container = document.getElementById('mensajesContainer');

    // Función para agregar mensaje al contenedor
    function agregarMensaje(msg, alFinal = true) {
        if (container.querySelector('.text-center')) {
            container.innerHTML = '';
        }

        const divMensaje = document.createElement('div');
        divMensaje.className = `message ${msg.id_remitente == miId ? 'sent' : 'received'}`;
        divMensaje.dataset.id = msg.id_mensaje;

        const contenidoDiv = document.createElement('div');
        contenidoDiv.className = 'message-content';
        contenidoDiv.textContent = msg.contenido;

        const metaDiv = document.createElement('div');
        metaDiv.className = 'message-meta';

        const fecha = new Date(msg.fecha_envio.replace(' ', 'T'));
        const hora = fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

        metaDiv.innerHTML = `<span>${hora}</span>`;

        if (msg.id_remitente == miId) {
            const estadoSpan = document.createElement('span');
            estadoSpan.className = 'status-check';
            estadoSpan.innerHTML = msg.leido == 1 ? '✓✓' : '✓';
            estadoSpan.title = msg.leido == 1 ? 'Leído' : 'Enviado';
            metaDiv.appendChild(estadoSpan);
        }

        divMensaje.appendChild(contenidoDiv);
        divMensaje.appendChild(metaDiv);
        container.appendChild(divMensaje);

        if (alFinal) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Cargar mensajes
    async function cargarMensajes(inicial = false) {
        if (cargando) return;
        cargando = true;
        try {
            const url = `${baseUrl}?c=mensajes&a=obtener&destinatario_id=${destinatarioId}&last_id=${ultimoId}`;
            const resp = await fetch(url);
            const data = await resp.json();
            if (data.mensajes) {
                if (inicial) {
                    container.innerHTML = '';
                    if (data.mensajes.length === 0) {
                        container.innerHTML = '<div class="text-center text-muted py-4">No hay mensajes aún. ¡Envía el primero!</div>';
                    }
                }
                for (let msg of data.mensajes) {
                    agregarMensaje(msg, !inicial);
                    if (msg.id_mensaje > ultimoId) ultimoId = msg.id_mensaje;
                }
                if (!inicial && data.mensajes.length > 0) {
                    container.scrollTop = container.scrollHeight;
                }
            }
        } catch (err) {
            console.error('Error cargando mensajes:', err);
        } finally {
            cargando = false;
        }
    }

    // Enviar mensaje
    document.getElementById('formMensaje').addEventListener('submit', async (e) => {
        e.preventDefault();
        const contenido = document.getElementById('contenidoInput').value.trim();
        if (!contenido) return;

        const formData = new FormData();
        formData.append('destinatario_id', destinatarioId);
        formData.append('contenido', contenido);

        try {
            const resp = await fetch(`${baseUrl}?c=mensajes&a=enviar`, {
                method: 'POST',
                body: formData
            });
            const data = await resp.json();
            if (data.success) {
                document.getElementById('contenidoInput').value = '';
                agregarMensaje({
                    id_mensaje: data.id_mensaje,
                    id_remitente: miId,
                    contenido: contenido,
                    fecha_envio: data.fecha_envio,
                    leido: 0
                }, true);
                ultimoId = data.id_mensaje;
            } else {
                alert('Error al enviar: ' + (data.error || ''));
            }
        } catch (err) {
            console.error('Error:', err);
            alert('Error de conexión');
        }
    });

    // Ajustar altura del textarea automáticamente
    const textarea = document.getElementById('contenidoInput');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Iniciar carga
    cargarMensajes(true);
    // Polling cada 2 segundos
    setInterval(() => cargarMensajes(false), 2000);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>