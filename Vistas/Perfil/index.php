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
    <title>Mi Perfil - UnioApp</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        .perfil-header { display: flex; gap: 20px; align-items: center; margin-bottom: 30px; }
        .avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; background: #ccc; }
        .info { flex: 1; }
        .seccion { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; }
        .lista-amigos { display: flex; flex-wrap: wrap; gap: 15px; }
        .amigo-card { width: 150px; text-align: center; }
        .amigo-card img { width: 80px; height: 80px; border-radius: 50%; }
        .solicitud-item { background: #f9f9f9; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .btn { background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; margin: 0 2px; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
        .error { color: red; }
        .exito { color: green; }
    </style>
</head>
<body>
<div class="container">
    <h1>Mi Perfil</h1>
    <?php if (isset($_SESSION['mensaje_perfil'])): ?>
        <div class="exito"><?= htmlspecialchars($_SESSION['mensaje_perfil']) ?></div>
        <?php unset($_SESSION['mensaje_perfil']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_perfil'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['error_perfil']) ?></div>
        <?php unset($_SESSION['error_perfil']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensaje_amigos'])): ?>
        <div class="exito"><?= htmlspecialchars($_SESSION['mensaje_amigos']) ?></div>
        <?php unset($_SESSION['mensaje_amigos']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_amigos'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['error_amigos']) ?></div>
        <?php unset($_SESSION['error_amigos']); ?>
    <?php endif; ?>

    <div class="perfil-header">
        <?php if ($usuario['foto_base64']): ?>
            <img src="<?= $usuario['foto_base64'] ?>" class="avatar">
        <?php else: ?>
            <div class="avatar" style="display: flex; align-items: center; justify-content: center;">Sin foto</div>
        <?php endif; ?>
        <div class="info">
            <h2><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono'] ?? 'No especificado') ?></p>
            <p><strong>Fecha nacimiento:</strong> <?= $usuario['fecha_nacimiento'] ?></p>
            <p><strong>Género:</strong> <?= $usuario['genero'] ?></p>
            <p><strong>Biografía:</strong> <?= nl2br(htmlspecialchars($usuario['biografia'] ?? '')) ?></p>
            <p><strong>Ubicación:</strong> <?= $usuario['latitud'] ? "$usuario[latitud], $usuario[longitud]" : 'No especificada' ?></p>
            <a href="<?= BASE_URL ?>?c=perfil&a=editar" class="btn">Editar perfil</a>
        </div>
    </div>

    <div class="seccion">
        <h3>Solicitudes de amistad pendientes</h3>
        <?php if (empty($solicitudes)): ?>
            <p>No hay solicitudes pendientes.</p>
        <?php else: ?>
            <?php foreach ($solicitudes as $s): ?>
                <div class="solicitud-item">
                    <?php if ($s['foto_base64']): ?>
                        <img src="<?= $s['foto_base64'] ?>" style="width: 50px; height: 50px; border-radius: 50%; vertical-align: middle;">
                    <?php endif; ?>
                    <strong><?= htmlspecialchars($s['nombre'] . ' ' . $s['apellidos']) ?></strong>
                    <a href="<?= BASE_URL ?>?c=amigos&a=responder&id=<?= $s['id_solicitante'] ?>&accion=aceptar" class="btn btn-success">Aceptar</a>
                    <a href="<?= BASE_URL ?>?c=amigos&a=responder&id=<?= $s['id_solicitante'] ?>&accion=rechazar" class="btn btn-danger">Rechazar</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="seccion">
        <h3>Mis amigos</h3>
        <?php if (empty($amigos)): ?>
            <p>Aún no tienes amigos. <a href="<?= BASE_URL ?>?c=amigos&a=nuevosAmigos">Buscar nuevos amigos</a></p>
        <?php else: ?>
            <div class="lista-amigos">
                <?php foreach ($amigos as $a): ?>
                    <div class="amigo-card">
                        <?php if ($a['foto_base64']): ?>
                            <img src="<?= $a['foto_base64'] ?>">
                        <?php else: ?>
                            <div style="width:80px;height:80px;background:#ccc;border-radius:50%;margin:0 auto;"></div>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($a['nombre'] . ' ' . $a['apellidos']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p><a href="<?= BASE_URL ?>?c=amigos&a=nuevosAmigos">➕ Buscar más amigos</a></p>
        <?php endif; ?>
    </div>

    <p><a href="<?= BASE_URL ?>?c=dashboard">← Volver al dashboard</a></p>
</div>
</body>
</html>