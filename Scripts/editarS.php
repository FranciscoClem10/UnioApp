<script>
    // Manejo de selección de tipo de actividad
    document.addEventListener('DOMContentLoaded', function() {
        const tipoBtns = document.querySelectorAll('.tipo-btn');
        const tipoInput = document.getElementById('id_tipo');
        
        tipoBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const nuevoTipoId = this.getAttribute('data-id');
                tipoInput.value = nuevoTipoId;
                tipoBtns.forEach(b => {
                    b.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
                    b.classList.add('bg-surface-container-highest', 'text-on-surface-variant');
                });
                this.classList.remove('bg-surface-container-highest', 'text-on-surface-variant');
                this.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
            });
        });
    });
    
    <?php if (!$restricciones['bloquear_todo']): ?>
        // Coordenadas iniciales desde la actividad
        var lat = <?= (float)($actividad['latitud'] ?? 18.4500) ?>;
        var lng = <?= (float)($actividad['longitud'] ?? -96.3500) ?>;
        
        // --- Icono personalizado ---
        var customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="#5a2af7"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
            iconSize: [28, 28],
            popupAnchor: [0, -14]
        });
        
        // Inicializar mapa
        var map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
        }).addTo(map);
        
        // Marcador arrastrable CON ICONO PERSONALIZADO
        var marker = L.marker([lat, lng], { 
            draggable: true, 
            icon: customIcon 
        }).addTo(map);
        
        // Funciones para actualizar los campos ocultos y mostrar la dirección
        function actualizarCoordenadas(lat, lng) {
            document.getElementById('latInput').value = lat;
            document.getElementById('lngInput').value = lng;
            actualizarDireccion(lat, lng);
        }
        
        // Geocodificación inversa para mostrar la dirección (Nominatim OSM)
        function actualizarDireccion(lat, lng) {
            var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('direccionMostrada').innerHTML = data.display_name;
                    } else {
                        document.getElementById('direccionMostrada').innerHTML = 'Dirección no encontrada';
                    }
                })
                .catch(() => {
                    document.getElementById('direccionMostrada').innerHTML = 'No se pudo cargar la dirección';
                });
        }
        
        // Evento al soltar el marcador
        marker.on('dragend', function(e) {
            var pos = marker.getLatLng();
            actualizarCoordenadas(pos.lat, pos.lng);
        });
        
        // Evento al hacer clic en el mapa
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
        });
        
        // Inicializar campos y dirección con los valores actuales
        actualizarCoordenadas(lat, lng);
        
        // Si la ubicación está bloqueada por restricciones, deshabilitar interacción
        <?php if ($restricciones['hay_miembros'] && $restricciones['bloquear_ubicacion']): ?>
            marker.dragging.disable();
            map.dragging.disable();
            map.touchZoom.disable();
            map.scrollWheelZoom.disable();
            map.doubleClickZoom.disable();
            marker.bindTooltip("Ubicación bloqueada").openTooltip();
        <?php endif; ?>
    <?php else: ?>
        document.getElementById('map').style.display = 'none';
    <?php endif; ?>
</script>