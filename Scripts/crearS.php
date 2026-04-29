<script>
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
                    previewDiv.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.innerHTML = '';
                previewDiv.classList.add('hidden');
            }
        });
    }

    // Coordenadas iniciales desde PHP o default
    const defLat = <?= getOld('latitud') ?: 18.4500 ?>;
    const defLng = <?= getOld('longitud') ?: -96.3500 ?>;

    // Inicializar mapa Leaflet
    const map = L.map('map').setView([defLat, defLng], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
    }).addTo(map);

    // Icono SVG vectorial personalizado
    const customIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="#5a2af7"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
        iconSize: [28, 28],
        popupAnchor: [0, -14]
    });

    // Crear marcador arrastrable con icono SVG
    const marker = L.marker([defLat, defLng], {
        draggable: true,
        icon: customIcon
    }).addTo(map);

    // Guardar coordenadas en inputs ocultos (sin mostrarlas en pantalla)
    function actualizarCoordenadas(lat, lng) {
        document.getElementById('latInput').value = lat;
        document.getElementById('lngInput').value = lng;
    }

    // Obtener y mostrar dirección debajo del mapa
    async function mostrarDireccion(lat, lng) {
        const direccionEl = document.getElementById('direccionMostrada');
        if (!direccionEl) return;
        direccionEl.textContent = 'Obteniendo dirección...';
        try {
            const resp = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=es`
            );
            if (!resp.ok) throw new Error('Error de red');
            const data = await resp.json();
            direccionEl.textContent = data?.display_name || 'Dirección no encontrada';
        } catch (err) {
            console.warn('Geocodificación falló:', err);
            direccionEl.textContent = 'No se pudo obtener la dirección';
        }
    }

    // Evento: soltar marcador tras arrastrar
    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        actualizarCoordenadas(pos.lat, pos.lng);
        mostrarDireccion(pos.lat, pos.lng);
    });

    // Evento: clic en el mapa → reposicionar marcador
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
        mostrarDireccion(e.latlng.lat, e.latlng.lng);
    });

    // Botón "Mi ubicación"
    const btnGeo = document.getElementById('btnMiUbicacion');
    if (btnGeo) {
        btnGeo.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert("Geolocalización no soportada");
                return;
            }
            navigator.geolocation.getCurrentPosition(function(pos) {
                const lat = pos.coords.latitude,
                    lng = pos.coords.longitude;
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
                actualizarCoordenadas(lat, lng);
                mostrarDireccion(lat, lng);
            }, function(err) {
                alert("Error obteniendo ubicación: " + err.message);
            });
        });
    }

    // Cargar dirección y coordenadas internas al iniciar
    actualizarCoordenadas(defLat, defLng);
    mostrarDireccion(defLat, defLng);

    // Validación de fechas
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
</script>