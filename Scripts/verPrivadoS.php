
    <script>
        // Constantes y variables
        const miId = <?= (int)$_SESSION['usuario_id'] ?>;
        const destinatarioId = <?= (int)$destinatario['id_usuario'] ?>;
        const baseUrl = '<?= BASE_URL ?>';
        let ultimoId = 0;
        let cargando = false;
        let pollingInterval = null;
        const container = document.getElementById('mensajesContainer');

        // Funciones auxiliares
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // Formatear hora (HH:MM)
        function formatearHora(fechaISO) {
            const fecha = new Date(fechaISO.replace(' ', 'T'));
            return fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        }

        // Agregar un mensaje al contenedor
        function agregarMensaje(msg, alFinal = true) {
            // Eliminar mensaje de carga si existe
            if (container.querySelector('.text-center')) {
                container.innerHTML = '';
            }

            const esMiMensaje = (msg.id_remitente == miId);
            const divMensaje = document.createElement('div');
            divMensaje.className = `flex items-start gap-3 max-w-[85%] ${esMiMensaje ? 'self-end flex-row-reverse' : 'self-start'}`;
            divMensaje.dataset.id = msg.id_mensaje;

            // Avatar (solo para mensajes recibidos, o para enviados también? El diseño muestra avatar en ambos)
            const avatarDiv = document.createElement('div');
            avatarDiv.className = 'w-8 h-8 rounded-full flex-shrink-0 overflow-hidden bg-primary-container flex items-center justify-center';
            if (esMiMensaje) {
                // Avatar del usuario actual (podría ser su foto, pero usamos "YO" por simplicidad)
                avatarDiv.innerHTML = '<span class="text-white text-xs font-bold">YO</span>';
            } else {
                // Avatar del destinatario
                if (<?= json_encode(!empty($destinatario['foto_base64'])) ?>) {
                    avatarDiv.innerHTML = `<img class="w-full h-full object-cover" src="<?= htmlspecialchars($destinatario['foto_base64']) ?>" alt="Avatar">`;
                } else {
                    avatarDiv.innerHTML = '<span class="material-symbols-outlined text-primary text-base">person</span>';
                }
            }

            // Contenedor del contenido
            const contentDiv = document.createElement('div');
            contentDiv.className = 'space-y-1 flex flex-col';
            if (esMiMensaje) contentDiv.classList.add('items-end');

            // Nombre del remitente
            const nombreSpan = document.createElement('p');
            nombreSpan.className = `text-[11px] font-bold ${esMiMensaje ? 'text-primary mr-1' : 'text-outline ml-1'}`;
            nombreSpan.textContent = esMiMensaje ? 'Tú' : (msg.nombre_remitente || 'Amigo');
            
            // Burbuja
            const burbujaDiv = document.createElement('div');
            burbujaDiv.className = `p-3 rounded-2xl ${esMiMensaje ? 'bg-primary text-on-primary rounded-tr-none shadow-md shadow-primary/10' : 'bg-surface-container-low text-on-surface rounded-tl-none'}`;
            const textoP = document.createElement('p');
            textoP.className = 'text-sm leading-relaxed break-words';
            textoP.textContent = msg.contenido;
            burbujaDiv.appendChild(textoP);
            
            // Meta (hora + estado de leído)
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

                const eliminarBtn = document.createElement('button');
                eliminarBtn.className = 'text-error/70 hover:text-error text-[10px] ml-2';
                eliminarBtn.innerHTML = '<span class="material-symbols-outlined text-[12px]">delete</span>';
                eliminarBtn.onclick = (e) => {
                    e.preventDefault();
                    if (confirm('¿Eliminar este mensaje?')) {
                        eliminarMensajePrivado(msg.id_mensaje);
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
            
            if (alFinal) {
                container.scrollTop = container.scrollHeight;
            }
        }

        async function eliminarMensajePrivado(idMensaje) {
            try {
                const resp = await fetch(`${baseUrl}?c=mensajes&a=eliminarMensajePrivado&id=${idMensaje}`);
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

        // Cargar mensajes nuevos
        async function cargarMensajes(inicial = false) {
            if (cargando) return;
            cargando = true;
            try {
                const url = `${baseUrl}?c=mensajes&a=obtener&destinatario_id=${destinatarioId}&last_id=${ultimoId}`;
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

        // Enviar mensaje
        async function enviarMensaje() {
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
                    // Ajustar altura del textarea
                    const textarea = document.getElementById('contenidoInput');
                    textarea.style.height = 'auto';
                    // Agregar mensaje optimistamente
                    agregarMensaje({
                        id_mensaje: data.id_mensaje || Date.now(),
                        id_remitente: miId,
                        contenido: contenido,
                        fecha_envio: data.fecha_envio || new Date().toISOString().replace('T', ' '),
                        leido: 0
                    }, true);
                    ultimoId = data.id_mensaje || ultimoId;
                } else {
                    alert('Error: ' + (data.error || 'No se pudo enviar'));
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Error de conexión');
            }
        }

        // Auto-resize del textarea
        const textarea = document.getElementById('contenidoInput');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        
        // Enviar con Enter (sin Shift)
        textarea.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                enviarMensaje();
            }
        });
        
        document.getElementById('btnEnviar').addEventListener('click', enviarMensaje);

        // Iniciar carga y polling
        cargarMensajes(true);
        pollingInterval = setInterval(() => cargarMensajes(false), 3000);

        // Limpiar intervalo al salir (opcional)
        window.addEventListener('beforeunload', () => {
            if (pollingInterval) clearInterval(pollingInterval);
        });
    </script>