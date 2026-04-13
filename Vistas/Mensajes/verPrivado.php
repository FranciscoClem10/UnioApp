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
    <title>Chat privado - UnioApp</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        .mensaje { margin-bottom: 15px; padding: 8px; border-bottom: 1px solid #eee; }
        .mensaje-propio { background: #e1f5fe; border-radius: 8px; }
        .fecha { font-size: 0.7em; color: #999; }
        .nombre { font-weight: bold; }
        form { margin-top: 20px; display: flex; gap: 10px; }
        textarea { flex: 1; padding: 8px; }
        .error { color: red; }
        .eliminar { font-size: 0.7em; color: red; text-decoration: none; margin-left: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Chat con <?= htmlspecialchars($amigo['nombre'] . ' ' . $amigo['apellido_paterno']) ?></h1>
    <?php if (isset($_SESSION['error_mensaje'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['error_mensaje']) ?></div>
        <?php unset($_SESSION['error_mensaje']); ?>
    <?php endif; ?>

    <div id="mensajes">
        <?php foreach ($mensajes as $msg): ?>
            <div class="mensaje <?= $msg['id_remitente'] == $_SESSION['usuario_id'] ? 'mensaje-propio' : '' ?>">
                <div class="nombre">
                    <?= htmlspecialchars($msg['nombre_completo']) ?>
                    <?php if ($msg['id_remitente'] == $_SESSION['usuario_id']): ?>
                        <a href="<?= BASE_URL ?>?c=mensajes&a=eliminar&tipo=privado&id=<?= $msg['id_mensaje'] ?>&id_amigo=<?= $amigo['id_usuario'] ?>" class="eliminar" onclick="return confirm('¿Eliminar este mensaje?')">🗑️</a>
                    <?php endif; ?>
                </div>
                <div><?= nl2br(htmlspecialchars($msg['contenido'])) ?></div>
                <div class="fecha"><?= $msg['fecha_envio'] ?> <?= $msg['leido'] ? '(Leído)' : '' ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <form action="<?= BASE_URL ?>?c=mensajes&a=enviar" method="POST">
        <input type="hidden" name="tipo" value="privado">
        <input type="hidden" name="id_amigo" value="<?= $amigo['id_usuario'] ?>">
        <textarea name="contenido" rows="3" required placeholder="Escribe tu mensaje..."></textarea>
        <button type="submit">Enviar</button>
    </form>
    <p><a href="<?= BASE_URL ?>?c=mensajes&a=chats">← Volver a conversaciones</a></p>
</div>
</body>
</html>