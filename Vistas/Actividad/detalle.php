<!DOCTYPE html>
<html>
<head><title>Detalle de actividad</title></head>
<body>
    <h1><?= htmlspecialchars($actividad['nombre']) ?></h1>
    <p><?= htmlspecialchars($actividad['descripcion']) ?></p>
    <a href="<?= BASE_URL ?>?c=dashboard">Volver al dashboard</a>
</body>
</html>