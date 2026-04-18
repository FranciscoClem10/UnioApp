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
    <title>Mis conversaciones - UnioApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .online { color: #28a745; font-size: 0.7rem; }
        .offline { color: #6c757d; font-size: 0.7rem; }
        .avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; background: #e9ecef; }
        .conversacion-item { transition: background 0.2s; cursor: pointer; }
        .conversacion-item:hover { background: #f8f9fa; }
        .badge-nuevo { background: #dc3545; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-chat-dots"></i> Mis conversaciones</h1>
        <a href="<?= BASE_URL ?>?c=dashboard" class="btn btn-outline-secondary">Volver al dashboard</a>
    </div>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <?php if (empty($conversaciones)): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No tienes conversaciones aún.
                    <a href="<?= BASE_URL ?>?c=amigos&a=nuevosAmigos" class="alert-link">Busca amigos para empezar a chatear</a>
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($conversaciones as $c): ?>
                        <a href="<?= BASE_URL ?>?c=mensajes&a=verPrivado&id=<?= $c['id_usuario'] ?>" class="list-group-item list-group-item-action conversacion-item">
                            <div class="d-flex align-items-center gap-3">
                                <?php if ($c['foto_base64']): ?>
                                    <img src="<?= $c['foto_base64'] ?>" class="avatar" alt="Avatar">
                                <?php else: ?>
                                    <div class="avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><?= htmlspecialchars($c['nombre_completo']) ?></h5>
                                        <?php if ($c['no_leidos'] > 0): ?>
                                            <span class="badge-nuevo"><?= $c['no_leidos'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        <?php if ($c['ultimo_mensaje']): ?>
                                            <?= htmlspecialchars(substr($c['ultimo_mensaje'], 0, 50)) ?>
                                        <?php else: ?>
                                            Sin mensajes aún
                                        <?php endif; ?>
                                    </small>
                                    <div>
                                        <?php if ($c['online']): ?>
                                            <span class="online"><i class="bi bi-circle-fill"></i> En línea</span>
                                        <?php else: ?>
                                            <span class="offline"><i class="bi bi-circle"></i> Desconectado</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>