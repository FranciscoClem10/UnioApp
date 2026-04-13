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
    <title>Mis conversaciones - UnioApp</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        .conversacion { display: flex; align-items: center; gap: 15px; border-bottom: 1px solid #ddd; padding: 12px 0; cursor: pointer; }
        .conversacion:hover { background: #f9f9f9; }
        .avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; background: #ccc; }
        .info { flex: 1; }
        .no-leidos { background: red; color: white; border-radius: 50%; padding: 2px 8px; font-size: 0.8em; }
        .titulo { font-weight: bold; }
        .ultimo { font-size: 0.8em; color: #666; }
    </style>
</head>
<body>
<div class="container">
    <h1>Mensajes y grupos</h1>
    <h2>Chats de actividades</h2>
    <?php if (empty($conversacionesActividad)): ?>
        <p>No estás participando en ninguna actividad.</p>
    <?php else: ?>
        <?php foreach ($conversacionesActividad as $conv): ?>
            <div class="conversacion" onclick="window.location.href='<?= BASE_URL ?>?c=mensajes&a=verActividad&id=<?= $conv['id_actividad'] ?>'">
                <?php if ($conv['foto_base64']): ?>
                    <img src="<?= $conv['foto_base64'] ?>" class="avatar">
                <?php else: ?>
                    <div class="avatar" style="background: #ccc; text-align: center; line-height: 50px;">📷</div>
                <?php endif; ?>
                <div class="info">
                    <div class="titulo"><?= htmlspecialchars($conv['titulo']) ?></div>
                    <div class="ultimo"><?= $conv['no_leidos'] > 0 ? "<span class='no-leidos'>{$conv['no_leidos']} nuevos</span>" : "Sin nuevos" ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h2>Chats privados con amigos</h2>
    <?php if (empty($conversacionesPrivadas)): ?>
        <p>No tienes amigos aún. <a href="<?= BASE_URL ?>?c=amigos&a=nuevosAmigos">Buscar amigos</a></p>
    <?php else: ?>
        <?php foreach ($conversacionesPrivadas as $amigo): ?>
            <div class="conversacion" onclick="window.location.href='<?= BASE_URL ?>?c=mensajes&a=verPrivado&id=<?= $amigo['id_usuario'] ?>'">
                <?php if ($amigo['foto_base64']): ?>
                    <img src="<?= $amigo['foto_base64'] ?>" class="avatar">
                <?php else: ?>
                    <div class="avatar" style="background: #ccc; text-align: center; line-height: 50px;">👤</div>
                <?php endif; ?>
                <div class="info">
                    <div class="titulo"><?= htmlspecialchars($amigo['nombre_completo']) ?></div>
                    <div class="ultimo"><?= $amigo['no_leidos'] > 0 ? "<span class='no-leidos'>{$amigo['no_leidos']} nuevos</span>" : "Sin nuevos" ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <p><a href="<?= BASE_URL ?>?c=dashboard">← Volver al dashboard</a></p>
</div>
</body>
</html>