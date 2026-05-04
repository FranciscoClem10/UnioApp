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
    <title>Perfil de <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?> - UnioApp</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        .avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; background: #ccc; }
        .btn { background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
        #map { height: 300px; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div style="display: flex; gap: 20px; align-items: center;">
        <?php if (!empty($usuario['foto_base64'])): ?>
            <img src="<?= $usuario['foto_base64'] ?>" class="avatar">
        <?php else: ?>
            <div class="avatar" style="display: flex; align-items: center; justify-content: center;">Sin foto</div>
        <?php endif; ?>
        <div>
            <h2><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono'] ?? 'No especificado') ?></p>
            <p><strong>Biografía:</strong> <?= nl2br(htmlspecialchars($usuario['biografia'] ?? '')) ?></p>
            <?php if ($usuario['latitud'] && $usuario['longitud']): ?>
                <p><strong>Ubicación:</strong> <?= $usuario['latitud'] ?>, <?= $usuario['longitud'] ?></p>
            <?php endif; ?>
            <?php
            // Verificar relación
            $modelo = new ModeloUsuario();
            $rel = null;
            $sqlRel = "SELECT estado, id_solicitante FROM amistades WHERE (id_solicitante = :id1 AND id_receptor = :id2) OR (id_solicitante = :id2 AND id_receptor = :id1)";
            $stmt = Database::getConexion()->prepare($sqlRel);
            $stmt->execute([':id1' => $_SESSION['usuario_id'], ':id2' => $usuario['id_usuario']]);
            $rel = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($rel && $rel['estado'] == 'aceptado') {
                echo '<p><strong>Estado:</strong> Amigos</p>';
            } elseif ($rel && $rel['estado'] == 'pendiente') {
                if ($rel['id_solicitante'] == $_SESSION['usuario_id']) {
                    echo '<p><strong>Estado:</strong> Solicitud enviada (pendiente)</p>';
                } else {
                    echo '<p><strong>Estado:</strong> Solicitud recibida</p>';
                    echo '<a href="' . BASE_URL . '?c=amigos&a=responder&id=' . $usuario['id_usuario'] . '&accion=aceptar" class="btn">Aceptar solicitud</a> ';
                    echo '<a href="' . BASE_URL . '?c=amigos&a=responder&id=' . $usuario['id_usuario'] . '&accion=rechazar" class="btn">Rechazar</a>';
                }
            } else {
                echo '<a href="' . BASE_URL . '?c=amigos&a=enviarSolicitud&id=' . $usuario['id_usuario'] . '" class="btn">Enviar solicitud de amistad</a>';
            }
            ?>
        </div>
    </div>
    <?php if ($usuario['latitud'] && $usuario['longitud']): ?>
        <h3>Ubicación</h3>
        <div id="map"></div>
        <script>
            var lat = <?= $usuario['latitud'] ?>;
            var lng = <?= $usuario['longitud'] ?>;
            var map = L.map('map').setView([lat, lng], 13);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
            }).addTo(map);
            L.marker([lat, lng]).addTo(map);
        </script>
    <?php endif; ?>
    <p><a href="<?= BASE_URL ?>?c=amigos&a=nuevosAmigos">← Volver a buscar amigos</a></p>
</div>
</body>
</html>