<?php
// Al inicio del dashboard, antes del HTML
require_once 'Modelos/ModeloNotificacion.php';
$modeloNotif = new ModeloNotificacion();
$notificacionesNoLeidas = $modeloNotif->contarNoLeidas($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - UnioApp</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; }
        .btn { padding: 8px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px; border: none; cursor: pointer; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
        .stat-card { background: #e9ecef; padding: 15px; border-radius: 8px; flex: 1; text-align: center; }
        .grid { display: flex; gap: 30px; flex-wrap: wrap; }
        .actividades-list { flex: 1; min-width: 300px; }
        .actividad-item { border: 1px solid #ddd; margin-bottom: 15px; padding: 12px; border-radius: 6px; background: #fafafa; }
        .map-container { flex: 1; min-width: 300px; }
        #map { height: 400px; width: 100%; border-radius: 8px; border: 1px solid #ccc; }
        .footer { margin-top: 40px; text-align: center; font-size: 0.8em; color: #777; border-top: 1px solid #ccc; padding-top: 15px; }
        .badge { background: #17a2b8; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75em; }
    </style>
</head>
<body>
<div class="container">
    <h1>UnioApp</h1>
    <div class="header">
        
        <div>

            <span>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>

            <a href="<?= BASE_URL ?>?c=notificacion" class="btn">
                Notificaciones
                <?php if ($notificacionesNoLeidas > 0): ?>
                    <span style=""><?= $notificacionesNoLeidas ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= BASE_URL ?>?c=actividad&a=crear" class="btn btn-success">+ Crear actividad</a>
            <a href="<?= BASE_URL ?>?c=actividad&a=edicion" class="btn btn-success">Editar actividad</a>
            <a href="<?= BASE_URL ?>?c=mensajes&a=chats" class="btn">Mensajes y grupos</a>
            <a href="<?= BASE_URL ?>?c=perfil&a=index" class="btn">Mi perfil</a>
            <a href="<?= BASE_URL ?>?c=login&a=logout" class="btn btn-danger">Cerrar sesión</a>


            

        </div>
    </div>

    <div class="stats">
        <div class="stat-card">
            <strong>Total actividades</strong><br>
            <?= $totalActividades ?>
        </div>
        <div class="stat-card">
            <strong>Actividades por categoría</strong><br>
            <?php foreach ($actividadesPorCategoria as $cat => $count): ?>
                <?= "$cat: $count" ?><br>
            <?php endforeach; ?>
        </div>
        <div class="stat-card">
            <strong>Mis actividades</strong><br>
            <?= $totalMisActividades ?>
        </div>
    </div>

    <div class="grid">
        <div class="actividades-list">
            <h2>Actividades disponibles</h2>
            <?php if (empty($actividades)): ?>
                <p>No hay actividades disponibles en este momento.</p>
            <?php else: ?>
                <?php foreach ($actividades as $act): ?>
                    <div class="actividad-item">
                        <h3><?= htmlspecialchars($act['titulo']) ?> 
                            <span class="badge"><?= htmlspecialchars($act['categoria']) ?></span>
                        </h3>
                        <p><strong>Fecha/Hora:</strong> <?= $act['fecha'] ?? 'Por definir' ?> <?= isset($act['hora']) ? 'a las ' . substr($act['hora'], 0, 5) : '' ?></p>
                        <p><strong>Tipo acceso:</strong> <?= ucfirst($act['tipo_acceso']) ?></p>
                        <p><strong>Edad mínima:</strong> <?= $act['edad_minima'] ?> años</p>
                        <p><strong>Requisitos:</strong> <?= htmlspecialchars($act['requisitos']) ?></p>
                        <p><strong>Límite personas:</strong> <?= $act['limite_personas'] ?></p>
                        <p><strong>Coordenadas:</strong> <?= $act['latitud'] ?>, <?= $act['longitud'] ?></p>
                        <a href="<?= BASE_URL ?>?c=actividad&a=detalle&id=<?= $act['id_actividad'] ?>" class="btn">Ver detalles</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="map-container">
            <h2>Mapa de actividades</h2>
            <div id="map"></div>
            <p style="font-size:0.8em; margin-top:5px;">📍 Haz clic en un marcador para ver detalles. Pasa el cursor sobre el marcador para información rápida.</p>
        </div>
    </div>

    <div class="footer">
        UnioApp - Conectando personas a través de actividades al aire libre
    </div>
</div>

<script>
    const actividades = <?= json_encode($actividades) ?>;
    var map = L.map('map').setView([18.4500, -96.3500], 12);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
    }).addTo(map);

    let userMarker = null;

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function agregarMarcadoresActividades() {
        actividades.forEach(act => {
            if (act.latitud && act.longitud && act.estado !== 'cancelada') {
                var marker = L.marker([parseFloat(act.latitud), parseFloat(act.longitud)]).addTo(map);
                let tooltipContent = `<strong>${escapeHtml(act.titulo)}</strong><br>
                                       Creador ID: ${act.id_creador}<br>
                                       Fecha: ${act.fecha || 'Próximamente'}<br>
                                       Requisitos: ${act.requisitos ? escapeHtml(act.requisitos.substring(0, 50)) : 'Ninguno'}`;
                marker.bindTooltip(tooltipContent, { sticky: true });
                marker.on('click', function() {
                    window.location.href = "<?= BASE_URL ?>?c=actividad&a=detalle&id=" + act.id_actividad;
                });
            }
        });
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                map.setView([userLat, userLng], 13);
                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.marker([userLat, userLng], {
                    icon: L.divIcon({ className: 'user-marker', html: '📍', iconSize: [20, 20] })
                }).addTo(map);
                userMarker.bindTooltip("Tu ubicación actual", { sticky: true });
                agregarMarcadoresActividades();
            },
            function(error) {
                console.warn("Error de geolocalización:", error);
                agregarMarcadoresActividades();
            }
        );
    } else {
        agregarMarcadoresActividades();
    }
</script>
</body>
</html>