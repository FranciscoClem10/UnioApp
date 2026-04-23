<script>
    // Variables globales
    const idActividad = <?= (int)$id_actividad ?>;
    const idUsuarioActual = <?= (int)$id_usuario_actual ?>;
    let ultimoIdMensaje = <?= !empty($mensajes) ? end($mensajes)['id_mensaje'] : 0 ?>;
    let pollingActivo = true;
    let pollingEnCurso = false;  // Evita solapamiento de peticiones

    // Referencias DOM
    const mensajesContainer = document.getElementById('mensajesContainer');
    const formMensaje = document.getElementById('formMensaje');
    const inputMensaje = document.getElementById('inputMensaje');
    const finMensajes = document.getElementById('finMensajes');
    const btnInfo = document.getElementById('btnInfo');
    const infoPanel = document.getElementById('infoPanel');
    const cerrarInfo = document.getElementById('cerrarInfo');

    // Scroll automático al final
    function scrollToBottom() {
        finMensajes.scrollIntoView({ behavior: 'smooth' });
    }

    // Agregar un mensaje al DOM (con atributo único para evitar duplicados)
    function agregarMensaje(msg, esPropio) {
        // Verificar si el mensaje ya existe en el DOM
        if (document.querySelector(`[data-id-mensaje="${msg.id_mensaje}"]`)) {
            return; // Ya está agregado, no duplicar
        }

        const divExterior = document.createElement('div');
        divExterior.className = `flex ${esPropio ? 'justify-end' : 'justify-start'}`;
        divExterior.setAttribute('data-id-mensaje', msg.id_mensaje); // Identificador único

        const divInterior = document.createElement('div');
        divInterior.className = `flex ${esPropio ? 'flex-row-reverse' : ''} items-end gap-2 max-w-[80%]`;

        // Avatar (solo si no es propio)
        if (!esPropio) {
            const avatarDiv = document.createElement('div');
            avatarDiv.className = 'flex-shrink-0';
            if (msg.foto_base64) {
                avatarDiv.innerHTML = `<img src="${msg.foto_base64}" class="w-8 h-8 rounded-full object-cover">`;
            } else {
                avatarDiv.innerHTML = `<div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center"><span class="material-symbols-outlined text-sm">person</span></div>`;
            }
            divInterior.appendChild(avatarDiv);
        }

        const contenidoDiv = document.createElement('div');
        // Nombre (solo si no es propio)
        if (!esPropio) {
            const nombreP = document.createElement('p');
            nombreP.className = 'text-xs text-gray-500 mb-1';
            nombreP.textContent = msg.nombre_completo;
            contenidoDiv.appendChild(nombreP);
        }

        const burbuja = document.createElement('div');
        burbuja.className = `rounded-2xl px-4 py-2 ${esPropio ? 'bg-blue-500 text-white' : 'bg-white border border-gray-200 text-gray-800'}`;
        burbuja.innerHTML = `<p class="text-sm">${msg.contenido.replace(/\n/g, '<br>')}</p>`;
        contenidoDiv.appendChild(burbuja);

        const horaP = document.createElement('p');
        horaP.className = `text-[10px] text-gray-400 mt-1 ${esPropio ? 'text-right' : ''}`;
        const fecha = new Date(msg.fecha_envio);
        horaP.textContent = fecha.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        contenidoDiv.appendChild(horaP);

        divInterior.appendChild(contenidoDiv);
        divExterior.appendChild(divInterior);
        mensajesContainer.insertBefore(divExterior, finMensajes);
    }

    // Polling para nuevos mensajes (con control de concurrencia y filtro de duplicados)
    async function pollNuevosMensajes() {
        if (!pollingActivo || pollingEnCurso) return;
        pollingEnCurso = true;
        try {
            const response = await fetch(`<?= BASE_URL ?>?c=mensajesGrupo&a=obtenerNuevos&id_actividad=${idActividad}&ultimo_id=${ultimoIdMensaje}`);
            const data = await response.json();
            if (data.mensajes && data.mensajes.length > 0) {
                let maxId = ultimoIdMensaje;
                data.mensajes.forEach(msg => {
                    // Verificar si el mensaje ya fue agregado al DOM
                    if (!document.querySelector(`[data-id-mensaje="${msg.id_mensaje}"]`)) {
                        const esPropio = (msg.id_usuario == idUsuarioActual);
                        agregarMensaje(msg, esPropio);
                        if (msg.id_mensaje > maxId) maxId = msg.id_mensaje;
                    }
                });
                ultimoIdMensaje = maxId;
                if (data.mensajes.length > 0) scrollToBottom();
            }
        } catch (error) {
            console.error('Error en polling:', error);
        } finally {
            pollingEnCurso = false;
            setTimeout(pollNuevosMensajes, 2000);
        }
    }

    // Enviar mensaje
    formMensaje.addEventListener('submit', async (e) => {
        e.preventDefault();
        const contenido = inputMensaje.value.trim();
        if (!contenido) return;

        const btnSubmit = formMensaje.querySelector('button[type="submit"]');
        btnSubmit.disabled = true;

        try {
            const formData = new FormData();
            formData.append('id_actividad', idActividad);
            formData.append('contenido', contenido);

            const response = await fetch('<?= BASE_URL ?>?c=mensajesGrupo&a=enviar', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                inputMensaje.value = '';
                // No forzamos polling inmediato para evitar carreras; el intervalo normal lo traerá.
                // Si quieres respuesta más rápida, implementa la solución 3 (mensaje local + ID real).
            } else {
                alert('Error al enviar: ' + (data.error || 'Desconocido'));
            }
        } catch (error) {
            alert('Error de conexión');
        } finally {
            btnSubmit.disabled = false;
            inputMensaje.focus();
        }
    });

    // Panel de información
    btnInfo.addEventListener('click', () => {
        infoPanel.classList.remove('translate-x-full');
    });
    cerrarInfo.addEventListener('click', () => {
        infoPanel.classList.add('translate-x-full');
    });

    // Iniciar polling
    setTimeout(pollNuevosMensajes, 1000);
    scrollToBottom();
</script>