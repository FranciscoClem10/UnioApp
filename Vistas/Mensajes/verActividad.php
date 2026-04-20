<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../includes/top-nav.php';
?>

<style>
#mensajesContainer::-webkit-scrollbar { width: 6px; }
#mensajesContainer::-webkit-scrollbar-track { background: #e7e8e8; border-radius: 10px; }
#mensajesContainer::-webkit-scrollbar-thumb { background: #5a2af7; border-radius: 10px; }
#mensajesContainer::-webkit-scrollbar-thumb:hover { background: #4e0bec; }
#mensajesContainer { scrollbar-width: thin; scrollbar-color: #5a2af7 #e7e8e8; }
</style>

<main class="flex flex-col h-[calc(100dvh-64px-56px)] overflow-hidden max-w-4xl mx-auto w-full">

    <!-- Header de la actividad -->
    <div class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-surface-container-low bg-white/50 backdrop-blur-sm shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full overflow-hidden bg-primary-container flex-shrink-0">
                <?php if (!empty($actividad['foto_actividad'])): 
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_buffer($finfo, $actividad['foto_actividad']);
                    finfo_close($finfo);
                    $fotoBase64 = 'data:' . $mime . ';base64,' . base64_encode($actividad['foto_actividad']);
                ?>
                    <img class="w-full h-full object-cover" src="<?= htmlspecialchars($fotoBase64) ?>" alt="Actividad">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">event</span>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <h1 class="text-base font-bold text-on-surface"><?= htmlspecialchars($actividad['nombre']) ?></h1>
                <p class="text-xs text-outline-variant">Chat grupal de la actividad</p>
            </div>
        </div>
        <a href="<?= BASE_URL ?>?c=mensajes&a=chats" class="flex items-center gap-2 px-4 py-2 bg-surface-container-low text-primary text-sm font-semibold rounded-full hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Volver
        </a>
    </div>

    <!-- Mensajes -->
    <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 flex flex-col" id="mensajesContainer">
        <div class="text-center text-outline-variant py-8">Cargando mensajes...</div>
    </div>

    <!-- Input para enviar mensaje -->
    <div class="p-4 md:p-6 bg-white/50 backdrop-blur-sm border-t border-surface-container-low shrink-0">
        <div class="w-full md:max-w-4xl mx-auto flex items-end gap-2 bg-surface-container-low p-2 rounded-2xl focus-within:ring-2 focus-within:ring-primary/10">
            <textarea id="contenidoInput" class="flex-1 bg-transparent border-none focus:ring-0 text-sm py-2 px-2 resize-none h-10 max-h-32 placeholder:text-outline-variant" placeholder="Escribe un mensaje..." rows="1"></textarea>
            <button id="btnEnviar" class="w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full shadow-lg shadow-primary/20 active:scale-95 transition-transform">
                <span class="material-symbols-outlined">send</span>
            </button>
        </div>
    </div>
</main>

<!-- Script específico para actividad -->
<script>
    const miId = <?= (int)$_SESSION['usuario_id'] ?>;
    const actividadId = <?= (int)$actividad['id_actividad'] ?>;
    const baseUrl = '<?= BASE_URL ?>';
    let ultimoId = 0;
    let cargando = false;
    let pollingInterval = null;
    const container = document.getElementById('mensajesContainer');

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function formatearHora(fechaISO) {
        const fecha = new Date(fechaISO.replace(' ', 'T'));
        return fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    }

    function agregarMensaje(msg, alFinal = true) {
        if (container.querySelector('.text-center')) container.innerHTML = '';
        const esMiMensaje = (msg.id_usuario == miId);
        const divMensaje = document.createElement('div');
        divMensaje.className = `flex items-start gap-3 max-w-[85%] ${esMiMensaje ? 'self-end flex-row-reverse' : 'self-start'}`;
        divMensaje.dataset.id = msg.id_mensaje;

        // Avatar
        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'w-8 h-8 rounded-full flex-shrink-0 overflow-hidden bg-primary-container flex items-center justify-center';
        if (esMiMensaje) {
            avatarDiv.innerHTML = '<span class="text-white text-xs font-bold">YO</span>';
        } else {
            if (msg.foto_base64) {
                avatarDiv.innerHTML = `<img class="w-full h-full object-cover" src="${msg.foto_base64}" alt="Avatar">`;
            } else {
                avatarDiv.innerHTML = '<span class="material-symbols-outlined text-primary text-base">person</span>';
            }
        }

        const contentDiv = document.createElement('div');
        contentDiv.className = 'space-y-1 flex flex-col';
        if (esMiMensaje) contentDiv.classList.add('items-end');

        const nombreSpan = document.createElement('p');
        nombreSpan.className = `text-[11px] font-bold ${esMiMensaje ? 'text-primary mr-1' : 'text-outline ml-1'}`;
        nombreSpan.textContent = esMiMensaje ? 'Tú' : (msg.nombre_completo || 'Usuario');

        const burbujaDiv = document.createElement('div');
        burbujaDiv.className = `p-3 rounded-2xl ${esMiMensaje ? 'bg-primary text-on-primary rounded-tr-none shadow-md shadow-primary/10' : 'bg-surface-container-low text-on-surface rounded-tl-none'}`;
        const textoP = document.createElement('p');
        textoP.className = 'text-sm leading-relaxed break-words';
        textoP.textContent = msg.contenido;
        burbujaDiv.appendChild(textoP);

        const metaDiv = document.createElement('div');
        metaDiv.className = `flex items-center gap-1 ${esMiMensaje ? 'justify-end mr-1' : 'ml-1'}`;
        const horaSpan = document.createElement('span');
        horaSpan.className = 'text-[10px] text-outline-variant';
        horaSpan.textContent = formatearHora(msg.fecha_envio);
        metaDiv.appendChild(horaSpan);
        if (esMiMensaje) {
            const leidoSpan = document.createElement('span');
            leidoSpan.className = 'material-symbols-outlined text-[12px] text-primary';
            leidoSpan.textContent = (msg.leido == 1) ? 'done_all' : 'done';
            leidoSpan.title = (msg.leido == 1) ? 'Leído' : 'Enviado';
            metaDiv.appendChild(leidoSpan);
            // Botón eliminar para mensajes propios
            const eliminarBtn = document.createElement('button');
            eliminarBtn.className = 'text-error/70 hover:text-error text-[10px] ml-2';
            eliminarBtn.innerHTML = '<span class="material-symbols-outlined text-[12px]">delete</span>';
            eliminarBtn.onclick = (e) => {
                e.preventDefault();
                if (confirm('¿Eliminar este mensaje?')) {
                    eliminarMensaje(msg.id_mensaje);
                }
            };
            metaDiv.appendChild(eliminarBtn);
        }

        contentDiv.appendChild(nombreSpan);
        contentDiv.appendChild(burbujaDiv);
        contentDiv.appendChild(metaDiv);
        divMensaje.appendChild(avatarDiv);
        divMensaje.appendChild(contentDiv);
        container.appendChild(divMensaje);
        if (alFinal) container.scrollTop = container.scrollHeight;
    }

    async function eliminarMensaje(idMensaje) {
        try {
            const resp = await fetch(`${baseUrl}?c=mensajes&a=eliminarMensajeActividad&id=${idMensaje}`);
            const data = await resp.json();
            if (data.success) {
                const elemento = document.querySelector(`[data-id="${idMensaje}"]`);
                if (elemento) elemento.remove();
            } else {
                alert(data.error || 'Error al eliminar');
            }
        } catch (err) {
            console.error(err);
            alert('Error de conexión');
        }
    }

    async function cargarMensajes(inicial = false) {
        if (cargando) return;
        cargando = true;
        try {
            const url = `${baseUrl}?c=mensajes&a=obtenerActividad&id=${actividadId}&last_id=${ultimoId}`;
            const resp = await fetch(url);
            const data = await resp.json();
            if (data.mensajes && data.mensajes.length) {
                if (inicial) container.innerHTML = '';
                for (let msg of data.mensajes) {
                    agregarMensaje(msg, !inicial);
                    if (msg.id_mensaje > ultimoId) ultimoId = msg.id_mensaje;
                }
                if (!inicial) container.scrollTop = container.scrollHeight;
            } else if (inicial && (!data.mensajes || data.mensajes.length === 0)) {
                container.innerHTML = '<div class="text-center text-outline-variant py-8">No hay mensajes aún. ¡Envía el primero!</div>';
            }
        } catch (err) {
            console.error('Error cargando mensajes:', err);
        } finally {
            cargando = false;
        }
    }

    async function enviarMensaje() {
        const contenido = document.getElementById('contenidoInput').value.trim();
        if (!contenido) return;
        const formData = new FormData();
        formData.append('id_actividad', actividadId);
        formData.append('contenido', contenido);
        try {
            const resp = await fetch(`${baseUrl}?c=mensajes&a=enviarActividad`, {
                method: 'POST',
                body: formData
            });
            const data = await resp.json();
            if (data.success) {
                document.getElementById('contenidoInput').value = '';
                const textarea = document.getElementById('contenidoInput');
                textarea.style.height = 'auto';
                agregarMensaje({
                    id_mensaje: data.id_mensaje || Date.now(),
                    id_usuario: miId,
                    contenido: contenido,
                    fecha_envio: data.fecha_envio || new Date().toISOString().replace('T', ' '),
                    leido: 0,
                    nombre_completo: 'Tú'
                }, true);
                ultimoId = data.id_mensaje || ultimoId;
            } else {
                alert('Error: ' + (data.error || 'No se pudo enviar'));
            }
        } catch (err) {
            console.error(err);
            alert('Error de conexión');
        }
    }

    const textarea = document.getElementById('contenidoInput');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    textarea.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            enviarMensaje();
        }
    });
    document.getElementById('btnEnviar').addEventListener('click', enviarMensaje);

    cargarMensajes(true);
    pollingInterval = setInterval(() => cargarMensajes(false), 3000);
    window.addEventListener('beforeunload', () => { if (pollingInterval) clearInterval(pollingInterval); });
</script>

<?php require_once __DIR__ . '/../../includes/bottom-nav.php'; ?>
</body>
</html>