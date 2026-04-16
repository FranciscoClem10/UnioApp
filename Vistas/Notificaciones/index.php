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
    <title>Mis notificaciones - UnioApp</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { margin-top: 0; }
        .btn { background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 15px; }
        .btn-secondary { background: #6c757d; }
        .notificacion { padding: 12px; margin-bottom: 10px; border-radius: 6px; border-left: 4px solid #007bff; background: #f9f9f9; }
        .notificacion.leida { opacity: 0.7; border-left-color: #6c757d; background: #f0f0f0; }
        .notificacion .fecha { font-size: 0.8em; color: #666; margin-bottom: 5px; }
        .notificacion .titulo { font-weight: bold; margin-bottom: 5px; }
        .notificacion .contenido { margin-bottom: 5px; }
        .notificacion .enlace { font-size: 0.85em; }
        .grupo-fecha { margin-top: 20px; margin-bottom: 10px; font-weight: bold; background: #e9ecef; padding: 5px 10px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Mis notificaciones</h1>
    <div>
        <a href="<?= BASE_URL ?>?c=notificacion&a=marcarTodasLeidas" class="btn btn-secondary">Marcar todas como leídas</a>
        <a href="<?= BASE_URL ?>?c=dashboard" class="btn">Volver al dashboard</a>
    </div>

    <?php if (empty($notificaciones)): ?>
        <p>No tienes notificaciones.</p>
    <?php else: ?>
        <?php
        // Agrupar por fecha (Hoy, Ayer, Esta semana, Anteriores)
        $hoy = date('Y-m-d');
        $ayer = date('Y-m-d', strtotime('-1 day'));
        $semana = date('Y-m-d', strtotime('-7 days'));

        $grupos = [
            'Hoy' => [],
            'Ayer' => [],
            'Esta semana' => [],
            'Anteriores' => []
        ];

        foreach ($notificaciones as $n) {
            $fecha = date('Y-m-d', strtotime($n['fecha_creacion']));
            if ($fecha == $hoy) {
                $grupos['Hoy'][] = $n;
            } elseif ($fecha == $ayer) {
                $grupos['Ayer'][] = $n;
            } elseif ($fecha >= $semana) {
                $grupos['Esta semana'][] = $n;
            } else {
                $grupos['Anteriores'][] = $n;
            }
        }

        foreach ($grupos as $nombre => $notis):
            if (empty($notis)) continue;
        ?>
            <div class="grupo-fecha"><?= $nombre ?></div>
            <?php foreach ($notis as $n): ?>
                <div class="notificacion <?= $n['leida'] ? 'leida' : '' ?>">
                    <div class="fecha"><?= date('d/m/Y H:i', strtotime($n['fecha_creacion'])) ?></div>
                    <div class="titulo"><?= htmlspecialchars($n['titulo']) ?></div>
                    <div class="contenido"><?= nl2br(htmlspecialchars($n['contenido'])) ?></div>
                    <?php if ($n['enlace']): ?>
                        <div class="enlace">
                            <a href="<?= BASE_URL . ltrim($n['enlace'], '/') ?>">Ver más →</a>
                        </div>
                    <?php endif; ?>
                    <?php if (!$n['leida']): ?>
                        <div style="margin-top: 5px;">
                            <a href="<?= BASE_URL ?>?c=notificacion&a=marcarLeida&id=<?= $n['id_notificacion'] ?>" class="btn btn-secondary" style="font-size:0.75rem;">Marcar como leída</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>