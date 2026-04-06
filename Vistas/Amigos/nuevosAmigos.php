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
    <title>Nuevos Amigos - UnioApp</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        .usuario-item { display: flex; align-items: center; gap: 15px; border-bottom: 1px solid #ddd; padding: 10px 0; }
        .avatar { width: 60px; height: 60px; border-radius: 50%; background: #ccc; object-fit: cover; }
        .btn { background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
        .error { color: red; }
        .exito { color: green; }
    </style>
</head>
<body>
<div class="container">
    <h2>Buscar nuevos amigos</h2>
    <form method="GET" action="<?= BASE_URL ?>">
        <input type="hidden" name="c" value="amigos">
        <input type="hidden" name="a" value="nuevosAmigos">
        <input type="text" name="buscar" placeholder="Nombre o apellido..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" style="width: 70%; padding: 8px;">
        <button type="submit" class="btn">Buscar</button>
    </form>

    <?php if (isset($_SESSION['mensaje_amigos'])): ?>
        <div class="exito"><?= htmlspecialchars($_SESSION['mensaje_amigos']) ?></div>
        <?php unset($_SESSION['mensaje_amigos']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_amigos'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['error_amigos']) ?></div>
        <?php unset($_SESSION['error_amigos']); ?>
    <?php endif; ?>

    <?php if ($termino !== '' && empty($resultados)): ?>
    <p>No se encontraron usuarios con "<?= htmlspecialchars($termino) ?>".</p>
<?php elseif (!empty($resultados)): ?>
    <h3>Resultados para "<?= htmlspecialchars($termino) ?>"</h3>
    <?php foreach ($resultados as $u): ?>
        <div class="usuario-item">
            <?php if ($u['foto_base64']): ?>
                <img src="<?= $u['foto_base64'] ?>" class="avatar">
            <?php else: ?>
                <div class="avatar" style="display: flex; align-items: center; justify-content: center;">?</div>
            <?php endif; ?>
            <div style="flex:1;">
                <strong><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?></strong><br>
                <small><?= htmlspecialchars($u['email']) ?></small><br>
                <?php if ($u['relacion'] == 'amigo'): ?>
                    <span style="color: green;">✓ Ya son amigos</span>
                <?php elseif ($u['relacion'] == 'solicitud_enviada'): ?>
                    <span style="color: orange;">⏳ Solicitud enviada (pendiente)</span>
                <?php elseif ($u['relacion'] == 'solicitud_recibida'): ?>
                    <span style="color: blue;">📩 Solicitud pendiente de respuesta</span>
                <?php endif; ?>
            </div>
            <div>
                <a href="<?= BASE_URL ?>?c=amigos&a=verPerfil&id=<?= $u['id_usuario'] ?>" class="btn">Ver perfil</a>
                <?php if ($u['relacion'] == 'ninguna'): ?>
                    <a href="<?= BASE_URL ?>?c=amigos&a=enviarSolicitud&id=<?= $u['id_usuario'] ?>" class="btn">Enviar solicitud</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
    <p><a href="<?= BASE_URL ?>?c=perfil">← Volver a mi perfil</a></p>
</div>
</body>
</html>