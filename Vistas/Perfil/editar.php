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
    <title>Editar Perfil - UnioApp</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f0f2f5; }
        .container { max-width: 700px; margin: auto; background: white; padding: 25px; border-radius: 10px; }
        .campo { margin-bottom: 18px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        .btn-geo { background: #007bff; margin-top: 5px; }
        .error { color: red; margin-bottom: 15px; }
        #map { height: 250px; margin-top: 5px; border-radius: 8px; border: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="container">
    <h2>Editar Perfil</h2>
    <?php if (isset($_SESSION['error_perfil'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['error_perfil']) ?></div>
        <?php unset($_SESSION['error_perfil']); ?>
    <?php endif; ?>
    <form action="<?= BASE_URL ?>?c=perfil&a=actualizar" method="POST" enctype="multipart/form-data">
        <div class="campo">
            <label>Nombre *</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
        </div>
        <div class="campo">
            <label>Apellidos *</label>
            <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
        </div>
        <div class="campo">
            <label>Teléfono</label>
            <input type="tel" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
        </div>
        <div class="campo">
            <label>Fecha de nacimiento *</label>
            <input type="date" name="fecha_nacimiento" value="<?= $usuario['fecha_nacimiento'] ?>" required>
        </div>
        <div class="campo">
            <label>Género</label>
            <select name="genero">
                <option value="M" <?= $usuario['genero'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                <option value="F" <?= $usuario['genero'] == 'F' ? 'selected' : '' ?>>Femenino</option>
                <option value="Otro" <?= $usuario['genero'] == 'Otro' ? 'selected' : '' ?>>Otro</option>
                <option value="Prefiero no decir" <?= $usuario['genero'] == 'Prefiero no decir' ? 'selected' : '' ?>>Prefiero no decir</option>
            </select>
        </div>
        <div class="campo">
            <label>Biografía</label>
            <textarea name="biografia" rows="4"><?= htmlspecialchars($usuario['biografia'] ?? '') ?></textarea>
        </div>
        <div class="campo">
            <label>Foto de perfil (JPG, PNG, WEBP, máx. 5MB)</label>
            <?php if ($usuario['foto_base64']): ?>
                <img src="<?= $usuario['foto_base64'] ?>" style="max-width:100px; display:block; margin-bottom:10px;">
            <?php endif; ?>
            <input type="file" name="foto_perfil" accept="image/jpeg,image/png,image/webp">
        </div>

        <!-- Ubicación en mapa -->
        <div class="campo">
            <label>Ubicación (arrastra el marcador o usa tu ubicación actual)</label>
            <div id="map"></div>
            <button type="button" id="btnGeo" class="btn-geo">📍 Usar mi ubicación actual</button>
            <input type="hidden" name="latitud" id="latitud" value="<?= $usuario['latitud'] ?>">
            <input type="hidden" name="longitud" id="longitud" value="<?= $usuario['longitud'] ?>">
        </div>

        <button type="submit">Guardar cambios</button>
        <a href="<?= BASE_URL ?>?c=perfil" style="margin-left: 15px;">Cancelar</a>
    </form>
</div>

<script>
    var defLat = <?= $usuario['latitud'] ?: 18.4500 ?>;
    var defLng = <?= $usuario['longitud'] ?: -96.3500 ?>;
    var map = L.map('map').setView([defLat, defLng], 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
    }).addTo(map);
    var marker = L.marker([defLat, defLng], { draggable: true }).addTo(map);
    function actualizarCoords(lat, lng) {
        document.getElementById('latitud').value = lat;
        document.getElementById('longitud').value = lng;
    }
    marker.on('dragend', function(e) {
        var pos = marker.getLatLng();
        actualizarCoords(pos.lat, pos.lng);
    });
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        actualizarCoords(e.latlng.lat, e.latlng.lng);
    });
    document.getElementById('btnGeo').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
                actualizarCoords(lat, lng);
            }, function(err) { alert("Error obteniendo ubicación"); });
        } else { alert("Geolocalización no soportada"); }
    });
    actualizarCoords(defLat, defLng);
</script>
</body>
</html>