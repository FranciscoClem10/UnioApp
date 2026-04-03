<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario - UnioApp</title>
    <!-- Leaflet CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .contenedor { max-width: 700px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        .campo { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="tel"], select, textarea {
            width: 100%; padding: 8px; box-sizing: border-box;
        }
        button { padding: 8px 16px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .error { color: red; margin-bottom: 15px; }
        .ubicacion { margin-top: 15px; }
        #map { height: 300px; margin-top: 10px; border-radius: 6px; border: 1px solid #ccc; }
        .boton-geo { margin-top: 10px; background-color: #007bff; }
        .boton-geo:hover { background-color: #0056b3; }
        .coord-info { font-size: 0.9em; color: #555; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>Crear Cuenta en UnioApp</h2>
        
        <?php if (isset($_SESSION['error_registro'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['error_registro']) ?></div>
            <?php unset($_SESSION['error_registro']); ?>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>?c=registro&a=registrar" method="POST" enctype="multipart/form-data">
            <div class="campo">
                <label for="nombre">Nombre *</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>
            <div class="campo">
                <label for="apellidos">Apellidos *</label>
                <input type="text" name="apellidos" id="apellidos" required>
            </div>
            <div class="campo">
                <label for="email">Correo electrónico *</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="campo">
                <label for="pass">Contraseña *</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="campo">
                <label for="confirm_password">Confirmar contraseña *</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div class="campo">
                <label for="fecha_nacimiento">Fecha de nacimiento *</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
            </div>
            <div class="campo">
                <label for="telefono">Teléfono</label>
                <input type="tel" name="telefono" id="telefono">
            </div>
            <div class="campo">
                <label for="genero">Género</label>
                <select name="genero" id="genero">
                    <option value="Prefiero no decir">Prefiero no decir</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="campo">
                <label for="biografia">Biografía</label>
                <textarea name="biografia" id="biografia" rows="3"></textarea>
            </div>
            <div class="campo">
                <label for="foto_perfil">Foto de perfil (JPG o PNG, máximo 2MB)</label>
                <input type="file" name="foto_perfil" id="foto_perfil" accept="image/jpeg,image/png">
            </div>

            <!-- Sección de ubicación con mapa -->
            <div class="ubicacion">
                <label>Ubicación (marca en el mapa o usa tu ubicación actual)</label>
                <div id="map"></div>
                <button type="button" id="btn_geo" class="boton-geo">📍 Usar mi ubicación actual</button>
                <div class="coord-info">
                    Coordenadas seleccionadas: <span id="coord_text">Ninguna</span>
                </div>
                <input type="hidden" name="latitud" id="latitud" value="">
                <input type="hidden" name="longitud" id="longitud" value="">
            </div>

            <button type="submit">Registrarse</button>
        </form>
        <div class="enlaces">
            ¿Ya tienes cuenta? <a href="<?= BASE_URL ?>?c=login">Inicia sesión aquí</a>
        </div>
    </div>

    <script>
        // Coordenadas de Tierra Blanca, Veracruz, México
        const TIERRA_BLANCA = { lat: 18.4500, lng: -96.3500 };
        
        // Inicializar mapa centrado en Tierra Blanca
        var map = L.map('map').setView([TIERRA_BLANCA.lat, TIERRA_BLANCA.lng], 13);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
        }).addTo(map);

        // Marcador arrastrable (inicialmente en Tierra Blanca)
        var marker = L.marker([TIERRA_BLANCA.lat, TIERRA_BLANCA.lng], { draggable: true }).addTo(map);
        
        // Función para actualizar campos ocultos y texto
        function actualizarCoordenadas(lat, lng) {
            document.getElementById('latitud').value = lat.toFixed(8);
            document.getElementById('longitud').value = lng.toFixed(8);
            document.getElementById('coord_text').innerHTML = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
        
        // Evento cuando se arrastra el marcador
        marker.on('dragend', function(e) {
            var pos = marker.getLatLng();
            actualizarCoordenadas(pos.lat, pos.lng);
        });
        
        // Geolocalización del navegador
        document.getElementById('btn_geo').addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert("Geolocalización no soportada por este navegador.");
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
        
        // Inicializar coordenadas con Tierra Blanca
        actualizarCoordenadas(TIERRA_BLANCA.lat, TIERRA_BLANCA.lng);
        
        // Opcional: también actualizar si el usuario hace clic en el mapa
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
        });
    </script>
</body>
</html>