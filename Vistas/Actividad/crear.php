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
    <title>Crear nueva actividad - UnioApp</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f0f2f5; }
        .container { max-width: 800px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .campo { margin-bottom: 18px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #218838; }
        .error { color: red; margin-bottom: 15px; padding: 10px; background: #ffe6e6; border-radius: 5px; }
        #map { height: 300px; margin-top: 5px; border-radius: 8px; border: 1px solid #ccc; }
        .coord-info { margin-top: 8px; font-size: 0.9em; color: #555; }
        .btn-geo { background: #007bff; margin-top: 5px; }
        .btn-geo:hover { background: #0056b3; }
        .requerido:after { content: " *"; color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Crear nueva actividad</h2>

    <?php if (isset($_SESSION['error_crear_actividad'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['error_crear_actividad']) ?></div>
        <?php unset($_SESSION['error_crear_actividad']); ?>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>?c=actividad&a=guardar" method="POST" enctype="multipart/form-data">
        <!-- Nombre -->
        <div class="campo">
            <label class="requerido">Nombre de la actividad</label>
            <input type="text" name="nombre" required maxlength="100">
        </div>

        <!-- Tipo -->
        <div class="campo">
            <label class="requerido">Tipo de actividad</label>
            <select name="id_tipo" required>
                <option value="">Selecciona un tipo</option>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?= $tipo['id_tipo'] ?>"><?= htmlspecialchars($tipo['nombre_tipo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Descripción -->
        <div class="campo">
            <label>Descripción</label>
            <textarea name="descripcion" rows="4"></textarea>
        </div>

        <!-- Requisitos -->
        <div class="campo">
            <label>Requisitos</label>
            <textarea name="requisitos" rows="3"></textarea>
        </div>

        <!-- Edades -->
        <div style="display: flex; gap: 15px;">
            <div class="campo" style="flex:1">
                <label>Edad mínima</label>
                <input type="number" name="edad_minima" value="0" min="0" max="99">
            </div>
            <div class="campo" style="flex:1">
                <label>Edad máxima</label>
                <input type="number" name="edad_maxima" value="99" min="0" max="99">
            </div>
        </div>

        <!-- Límite participantes -->
        <div style="display: flex; gap: 15px;">
            <div class="campo" style="flex:1">
                <label>Mínimo participantes</label>
                <input type="number" name="limite_participantes_min" value="1" min="1">
            </div>
            <div class="campo" style="flex:1">
                <label>Máximo participantes (opcional)</label>
                <input type="number" name="limite_participantes_max" min="1">
            </div>
        </div>

        <!-- Privacidad -->
        <div class="campo">
            <label>Privacidad</label>
            <select name="privacidad">
                <option value="publica">Pública (cualquiera puede unirse)</option>
                <option value="por_aprobacion">Por aprobación (el organizador acepta)</option>
                <option value="privada">Privada (solo invitados)</option>
            </select>
        </div>

        <!-- Fechas (nuevo) -->
        <div style="display: flex; gap: 15px;">
            <div class="campo" style="flex:1">
                <label class="requerido">Fecha y hora de inicio</label>
                <input type="datetime-local" name="fecha_inicio" required>
            </div>
            <div class="campo" style="flex:1">
                <label class="requerido">Fecha y hora de fin</label>
                <input type="datetime-local" name="fecha_fin" required>
            </div>
        </div>

        <!-- Mapa ubicación -->
        <div class="campo">
            <label class="requerido">Ubicación de la actividad</label>
            <div id="map"></div>
            <button type="button" id="btnMiUbicacion" class="btn-geo">📍 Usar mi ubicación actual</button>
            <div class="coord-info">
                Latitud: <span id="latSpan">No seleccionada</span> | 
                Longitud: <span id="lngSpan">No seleccionada</span>
                <input type="hidden" name="latitud" id="latInput" required>
                <input type="hidden" name="longitud" id="lngInput" required>
            </div>
        </div>

        <!-- Foto -->
        <div class="campo">
            <label>Foto de la actividad (JPG, PNG, WEBP, máx. 5MB)</label>
            <input type="file" name="foto_actividad" accept="image/jpeg,image/png,image/webp">
        </div>

        <button type="submit">Crear actividad</button>
        <a href="<?= BASE_URL ?>?c=dashboard" style="margin-left: 15px;">Cancelar</a>
    </form>
</div>

<script>
    const defLat = 18.4500;
    const defLng = -96.3500;
    var map = L.map('map').setView([defLat, defLng], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
    }).addTo(map);
    var marker = L.marker([defLat, defLng], { draggable: true }).addTo(map);
    function actualizarCoordenadas(lat, lng) {
        document.getElementById('latSpan').innerText = lat.toFixed(6);
        document.getElementById('lngSpan').innerText = lng.toFixed(6);
        document.getElementById('latInput').value = lat;
        document.getElementById('lngInput').value = lng;
    }
    marker.on('dragend', function(e) {
        var pos = marker.getLatLng();
        actualizarCoordenadas(pos.lat, pos.lng);
    });
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
    });
    document.getElementById('btnMiUbicacion').addEventListener('click', function() {
        if (!navigator.geolocation) alert("Geolocalización no soportada");
        else navigator.geolocation.getCurrentPosition(function(pos) {
            var lat = pos.coords.latitude, lng = pos.coords.longitude;
            map.setView([lat, lng], 15);
            marker.setLatLng([lat, lng]);
            actualizarCoordenadas(lat, lng);
        }, function(err) { alert("Error obteniendo ubicación"); });
    });
    actualizarCoordenadas(defLat, defLng);
</script>
</body>
</html>