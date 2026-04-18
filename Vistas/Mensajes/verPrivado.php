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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con <?= htmlspecialchars($amigo['nombre_completo']) ?> - UnioApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .chat-container {
            height: calc(100vh - 180px);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            background: #f8f9fa;
        }
        .mensaje {
            max-width: 75%;
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .mensaje-propio {
            flex-direction: row-reverse;
            margin-left: auto;
        }
        .mensaje-propio .burbuja {
            background: #0d6efd;
            color: white;
            border-radius: 1rem 1rem 0.25rem 1rem;
        }
        .mensaje-otro .burbuja {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 1rem 1rem 1rem 0.25rem;
        }
        .burbuja {
            padding: 0.5rem 1rem;
            max-width: 100%;
        }
        .avatar-chat {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background: #e9ecef;
        }
        .fecha-hora {
            font-size: 0.7rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        .input-group-custom {
            background: white;
            border-top: 1px solid #dee2e6;
            padding: 0.75rem;
        }
    </style>
</head>
<body>
<div class="container-fluid h-100 d-flex flex-column">
    <!-- Header -->
    <div class="bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= BASE_URL ?>?c=mensajes&a=chats" class="btn btn-link text-decoration-none text-secondary">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <?php if ($amigo['foto_base64']): ?>
                <img src="<?= $amigo['foto_base64'] ?>" class="avatar-chat" alt="Avatar">
            <?php else: ?>
                <div class="avatar-chat d-flex align-items-center justify-content-center bg-secondary text-white">
                    <i class="bi bi-person-fill"></i>
                </div>
            <?php endif; ?>
            <div>
                <h5 class="mb-0"><?= htmlspecialchars($amigo['nombre_completo']) ?></h5>
            </div>
        </div>
    </div>

    <!-- Mensajes de error/éxito -->
    <?php if (isset($_SESSION['error_mensaje'])): ?>
        <div class="alert alert-danger m-2"><?= htmlspecialchars($_SESSION['error_mensaje']) ?></div>
        <?php unset($_SESSION['error_mensaje']); ?>
    <?php endif; ?>

    <!-- Área de mensajes -->
    <div class="chat-container" id="mensajesContainer">
        <?php foreach ($mensajes as $msg): ?>
            <?php $esPropio = ($msg['id_remitente'] == $_SESSION['usuario_id']); ?>
            <div class="mensaje <?= $esPropio ? 'mensaje-propio' : 'mensaje-otro' ?>">
                <?php if (!$esPropio && $msg['foto_base64']): ?>
                    <img src="<?= $msg['foto_base64'] ?>" class="avatar-chat" alt="Avatar">
                <?php elseif (!$esPropio): ?>
                    <div class="avatar-chat d-flex align-items-center justify-content-center bg-secondary text-white">
                        <i class="bi bi-person-fill"></i>
                    </div>
                <?php endif; ?>
                <div class="burbuja">
                    <div class="fw-bold small"><?= $esPropio ? 'Tú' : htmlspecialchars($msg['nombre_completo']) ?></div>
                    <div><?= nl2br(htmlspecialchars($msg['contenido'])) ?></div>
                    <div class="fecha-hora text-end"><?= date('H:i', strtotime($msg['fecha_envio'])) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Formulario de envío (sin AJAX) -->
    <div class="input-group-custom">
        <form action="<?= BASE_URL ?>?c=mensajes&a=enviar" method="POST" class="input-group">
            <input type="hidden" name="id_amigo" value="<?= $amigo['id_usuario'] ?>">
            <textarea name="contenido" class="form-control" rows="1" placeholder="Escribe un mensaje..." style="resize: none;" required></textarea>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send"></i> Enviar
            </button>
        </form>
    </div>
</div>

<script>
    // Auto-scroll al final y autoajuste del textarea
    const container = document.getElementById('mensajesContainer');
    if (container) container.scrollTop = container.scrollHeight;
    const textarea = document.querySelector('textarea[name="contenido"]');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 150) + 'px';
        });
    }
</script>
</body>
</html>