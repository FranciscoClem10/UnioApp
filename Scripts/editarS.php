<script>
    // Previsualización de imagen
    const fotoInput = document.getElementById('fotoInput');
    const previewDiv = document.getElementById('previewImage');
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    previewDiv.innerHTML = `<img src="${ev.target.result}" class="w-full h-auto rounded-xl max-h-48 object-cover">`;
                };
                reader.readAsDataURL(file);
            } else if (previewDiv.querySelector('img') && previewDiv.querySelector('img').src.startsWith('data:')) {
                // Si no hay archivo y la imagen actual es de base64 del servidor, la dejamos
            } else {
                previewDiv.innerHTML = '';
            }
        });
    }

    // Clasificación por botones de tipo
    const tipoBtns = document.querySelectorAll('.tipo-btn');
    const idTipoInput = document.getElementById('id_tipo');
    if (tipoBtns.length) {
        tipoBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                tipoBtns.forEach(b => {
                    b.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
                    b.classList.add('bg-surface-container-highest', 'text-on-surface-variant');
                });
                this.classList.remove('bg-surface-container-highest', 'text-on-surface-variant');
                this.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
                idTipoInput.value = this.dataset.id;
            });
        });
    }

    // Validación de fechas (inicio < fin)
    const inicio = document.querySelector('input[name="fecha_inicio"]');
    const fin = document.querySelector('input[name="fecha_fin"]');
    if (inicio && fin) {
        const validar = () => {
            if (inicio.value && fin.value && fin.value < inicio.value) {
                alert("La fecha de fin no puede ser anterior a la de inicio.");
                fin.value = "";
            }
        };
        inicio.addEventListener('change', validar);
        fin.addEventListener('change', validar);
    }

    // ---------- MAPA Y DIRECCIÓN ----------
    const defLat = <?= (float)($actividad['latitud'] ?? 18.4500) ?>;
    const defLng = <?= (float)($actividad['longitud'] ?? -96.3500) ?>;
    let map, marker;
    let mapaBloqueado = <?= ($restricciones['hay_miembros'] && $restricciones['bloquear_ubicacion']) ? 'true' : 'false' ?>;

    function initMap() {
        map = L.map('map').setView([defLat, defLng], 15);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
        }).addTo(map);

        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="#5a2af7"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
            iconSize: [28, 28],
            popupAnchor: [0, -14]
        });

        marker = L.marker([defLat, defLng], { draggable: !mapaBloqueado, icon: customIcon }).addTo(map);

        function actualizarCoordenadas(lat, lng) {
            document.getElementById('latInput').value = lat;
            document.getElementById('lngInput').value = lng;
        }

        async function mostrarDireccion(lat, lng) {
            const direccionEl = document.getElementById('direccionMostrada');
            if (!direccionEl) return;
            direccionEl.textContent = 'Obteniendo dirección...';
            try {
                const resp = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=es`);
                if (!resp.ok) throw new Error('Error de red');
                const data = await resp.json();
                direccionEl.textContent = data?.display_name || 'Dirección no encontrada';
            } catch (err) {
                console.warn('Geocodificación falló:', err);
                direccionEl.textContent = 'No se pudo obtener la dirección';
            }
        }

        if (!mapaBloqueado) {
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                actualizarCoordenadas(pos.lat, pos.lng);
                mostrarDireccion(pos.lat, pos.lng);
            });
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
                mostrarDireccion(e.latlng.lat, e.latlng.lng);
            });
        } else {
            marker.dragging.disable();
            map.dragging.disable();
            map.touchZoom.disable();
            map.scrollWheelZoom.disable();
            map.doubleClickZoom.disable();
        }

        actualizarCoordenadas(defLat, defLng);
        mostrarDireccion(defLat, defLng);

        // Botón mi ubicación
        const btnGeo = document.getElementById('btnMiUbicacion');
        if (btnGeo && !mapaBloqueado) {
            btnGeo.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert("Geolocalización no soportada");
                    return;
                }
                navigator.geolocation.getCurrentPosition(function(pos) {
                    const lat = pos.coords.latitude, lng = pos.coords.longitude;
                    map.setView([lat, lng], 15);
                    marker.setLatLng([lat, lng]);
                    actualizarCoordenadas(lat, lng);
                    mostrarDireccion(lat, lng);
                }, function(err) {
                    alert("Error obteniendo ubicación: " + err.message);
                });
            });
        } else if (btnGeo && mapaBloqueado) {
            btnGeo.disabled = true;
            btnGeo.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    if (document.getElementById('map')) {
        initMap();
    }

    // ========== BÚSQUEDA DE USUARIOS PARA ORGANIZADORES ==========
    const inputOrg = document.getElementById('buscar_organizador_input');
    const btnBuscarOrg = document.getElementById('btn_buscar_organizador');
    const resultadosOrgDiv = document.getElementById('resultados_organizador');
    const hiddenOrgId = document.getElementById('organizador_seleccionado');

    async function buscarUsuarios(termino) {
        if (termino.length < 2) return [];
        const response = await fetch(`<?= BASE_URL ?>?c=actividad&a=buscarAmigos&term=${encodeURIComponent(termino)}`);
        return await response.json();
    }

    if (btnBuscarOrg) {
        btnBuscarOrg.addEventListener('click', async function() {
            const term = inputOrg.value.trim();
            if (term.length < 2) {
                resultadosOrgDiv.innerHTML = '<div class="p-3 text-error">Ingrese al menos 2 caracteres</div>';
                resultadosOrgDiv.classList.remove('hidden');
                return;
            }
            const data = await buscarUsuarios(term);
            if (!data || data.length === 0) {
                resultadosOrgDiv.innerHTML = '<div class="p-3 text-error">No se encontraron usuarios</div>';
                resultadosOrgDiv.classList.remove('hidden');
                return;
            }
            let html = '';
            data.forEach(user => {
                html += `<div class="p-3 hover:bg-surface-container-low cursor-pointer border-b border-outline-variant/30" onclick="seleccionarOrganizador(${user.id_usuario}, '${user.nombre_completo.replace(/'/g, "\\'")}')">
                            ${user.nombre_completo} (${user.email})
                        </div>`;
            });
            resultadosOrgDiv.innerHTML = html;
            resultadosOrgDiv.classList.remove('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!inputOrg.contains(e.target) && !btnBuscarOrg.contains(e.target) && !resultadosOrgDiv.contains(e.target)) {
                resultadosOrgDiv.classList.add('hidden');
            }
        });
    }

    window.seleccionarOrganizador = function(id, nombre) {
        hiddenOrgId.value = id;
        inputOrg.value = nombre;
        resultadosOrgDiv.classList.add('hidden');
    };

    // ========== BÚSQUEDA PARA INVITACIONES ==========
    const busquedaInv = document.getElementById('buscar_invitado');
    const resultadosInv = document.getElementById('resultados_invitacion');
    const hiddenInv = document.getElementById('invitado_hidden');
    let timeoutInv;
    if (busquedaInv) {
        busquedaInv.addEventListener('input', function() {
            clearTimeout(timeoutInv);
            const term = this.value.trim();
            if (term.length < 2) {
                resultadosInv.innerHTML = '';
                resultadosInv.classList.add('hidden');
                return;
            }
            timeoutInv = setTimeout(() => {
                fetch('<?= BASE_URL ?>?c=actividad&a=buscarUsuario&term=' + encodeURIComponent(term))
                    .then(res => res.json())
                    .then(data => {
                        resultadosInv.innerHTML = '';
                        if (!data.length) {
                            resultadosInv.innerHTML = '<div class="p-3 text-error">No se encontraron usuarios</div>';
                            resultadosInv.classList.remove('hidden');
                            return;
                        }
                        data.forEach(user => {
                            const div = document.createElement('div');
                            div.textContent = user.nombre_completo + ' (' + user.email + ')';
                            div.className = 'p-3 hover:bg-surface-container-low cursor-pointer border-b border-outline-variant/30';
                            div.onclick = () => {
                                hiddenInv.value = user.id_usuario;
                                busquedaInv.value = user.nombre_completo;
                                resultadosInv.innerHTML = '';
                                resultadosInv.classList.add('hidden');
                            };
                            resultadosInv.appendChild(div);
                        });
                        resultadosInv.classList.remove('hidden');
                    });
            }, 300);
        });
    }

    // Mostrar/ocultar campos de invitación
    const tipoSelect = document.getElementById('tipo_invitacion');
    const divUsuario = document.getElementById('destinatario_usuario');
    const divEmail = document.getElementById('destinatario_email');
    if (tipoSelect) {
        tipoSelect.addEventListener('change', function() {
            if (this.value === 'usuario') {
                divUsuario.style.display = 'block';
                divEmail.style.display = 'none';
                document.querySelector('#destinatario_email input')?.removeAttribute('name');
                document.querySelector('#destinatario_usuario input')?.setAttribute('name', 'buscar_amigo');
            } else {
                divUsuario.style.display = 'none';
                divEmail.style.display = 'block';
                document.querySelector('#destinatario_usuario input')?.removeAttribute('name');
                document.querySelector('#destinatario_email input')?.setAttribute('name', 'destinatario');
            }
        });
        tipoSelect.dispatchEvent(new Event('change'));
    }
</script>
