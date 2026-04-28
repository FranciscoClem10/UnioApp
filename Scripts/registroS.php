    <script>
        // Coordenadas de Tierra Blanca, Veracruz, México
        const TIERRA_BLANCA = { lat: 18.4500, lng: -96.3500 };

        // --- Función de geocodificación inversa mejorada ---
        async function reverseGeocode(lat, lng) {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=es`;
            const direccionSpan = document.getElementById('direccion_text');
            direccionSpan.innerHTML = '<span class="opacity-70">Obteniendo dirección...</span>';
            
            try {
                const response = await fetch(url, {
                    headers: { 'User-Agent': 'UnioApp-Registro/1.0' }
                });
                const data = await response.json();
                
                if (data && data.address) {
                    const addr = data.address;
                    const partes = [];
                    
                    // Calle (road) o peatonal (pedestrian)
                    if (addr.road) partes.push(addr.road);
                    else if (addr.pedestrian) partes.push(addr.pedestrian);
                    
                    // Colonia / barrio (suburb) o barrio (neighbourhood)
                    if (addr.suburb) partes.push(addr.suburb);
                    else if (addr.neighbourhood) partes.push(addr.neighbourhood);
                    
                    // Ciudad / pueblo / villa
                    if (addr.city) partes.push(addr.city);
                    else if (addr.town) partes.push(addr.town);
                    else if (addr.village) partes.push(addr.village);
                    
                    // Código postal
                    if (addr.postcode) partes.push(addr.postcode);
                    
                    // Estado
                    if (addr.state) partes.push(addr.state);
                    
                    // País
                    if (addr.country) partes.push(addr.country);
                    
                    let direccionFormateada = partes.length > 0 ? partes.join(', ') : data.display_name;
                    direccionSpan.innerHTML = `Dirección: ${direccionFormateada}`;
                } else {
                    direccionSpan.innerHTML = 'Dirección no encontrada';
                }
            } catch (error) {
                console.error('Error en geocodificación:', error);
                direccionSpan.innerHTML = `Dirección aproximada: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            }
        }

        function actualizarCoordenadas(lat, lng) {
            document.getElementById('latitud').value = lat.toFixed(8);
            document.getElementById('longitud').value = lng.toFixed(8);
            reverseGeocode(lat, lng);
        }

        // --- Inicialización del mapa ---
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Crear el mapa
            var map = L.map('map').setView([TIERRA_BLANCA.lat, TIERRA_BLANCA.lng], 13);
            
            // 2. Agregar capa de tiles
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
            }).addTo(map);
            
            // 3. Definir el icono personalizado (círculo morado)
            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="#5a2af7"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
                iconSize: [28, 28],
                popupAnchor: [0, -14]
            });
                        
            // 4. Crear el marcador arrastrable con ese icono
            var marker = L.marker([TIERRA_BLANCA.lat, TIERRA_BLANCA.lng], { draggable: true, icon: customIcon }).addTo(map);
            
            // 5. Eventos (dragend y click)
            marker.on('dragend', function(e) {
                var pos = marker.getLatLng();
                actualizarCoordenadas(pos.lat, pos.lng);
            });
            
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
            });
            
            // 6. Botón de geolocalización
            document.getElementById('btn_geo').addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert("Geolocalización no soportada.");
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        map.setView([lat, lng], 15);
                        marker.setLatLng([lat, lng]);
                        actualizarCoordenadas(lat, lng);
                    },
                    function(error) {
                        let msg = "Error obteniendo ubicación: ";
                        switch(error.code) {
                            case error.PERMISSION_DENIED: msg += "Permiso denegado."; break;
                            case error.POSITION_UNAVAILABLE: msg += "Ubicación no disponible."; break;
                            case error.TIMEOUT: msg += "Tiempo agotado."; break;
                            default: msg += "Desconocido.";
                        }
                        alert(msg);
                    }
                );
            });
            
            // 7. Inicializar coordenadas y dirección
            actualizarCoordenadas(TIERRA_BLANCA.lat, TIERRA_BLANCA.lng);
        });
    </script>