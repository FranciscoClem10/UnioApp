<script>
    // Clasificación (tipos)
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

    // Mapa Leaflet - recuperar coordenadas guardadas si existen
    const defLat = <?= getOld('latitud') ?: 18.4500 ?>;
    const defLng = <?= getOld('longitud') ?: -96.3500 ?>;
    const map = L.map('map').setView([defLat, defLng], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
    }).addTo(map);
    const marker = L.marker([defLat, defLng], { draggable: true }).addTo(map);
    
    function actualizarCoordenadas(lat, lng) {
        document.getElementById('latSpan').innerText = lat.toFixed(6);
        document.getElementById('lngSpan').innerText = lng.toFixed(6);
        document.getElementById('latInput').value = lat;
        document.getElementById('lngInput').value = lng;
    }
    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        actualizarCoordenadas(pos.lat, pos.lng);
    });
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
    });
    const btnGeo = document.getElementById('btnMiUbicacion');
    if (btnGeo) {
        btnGeo.addEventListener('click', function() {
            if (!navigator.geolocation) alert("Geolocalización no soportada");
            else navigator.geolocation.getCurrentPosition(function(pos) {
                const lat = pos.coords.latitude, lng = pos.coords.longitude;
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
                actualizarCoordenadas(lat, lng);
            }, function(err) { alert("Error obteniendo ubicación: " + err.message); });
        });
    }
    // Si ya había coordenadas guardadas, mostrarlas
    if (defLat != 18.4500 || defLng != -96.3500) {
        actualizarCoordenadas(defLat, defLng);
    } else {
        actualizarCoordenadas(defLat, defLng);
    }

    // Validación simple de fechas
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