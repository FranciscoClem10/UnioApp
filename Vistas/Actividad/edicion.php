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
    <title>Mis actividades - UnioApp</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .btn { background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
        .btn-danger { background: #dc3545; }
        .estado { padding: 2px 8px; border-radius: 12px; font-size: 0.8em; }
        .estado-pendiente { background: #ffc107; color: #333; }
        .estado-en_curso { background: #17a2b8; color: white; }
        .estado-finalizada { background: #6c757d; color: white; }
        .estado-cancelada { background: #dc3545; color: white; }
    </style>
</head>
<body>
<div class="container">
    <h2>Mis actividades</h2>
    <a href="<?= BASE_URL ?>?c=dashboard">← Volver al dashboard</a>
    <?php if (isset($_SESSION['exito_edicion'])): ?>
        <div style="color:green"><?= htmlspecialchars($_SESSION['exito_edicion']) ?></div>
        <?php unset($_SESSION['exito_edicion']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_edicion'])): ?>
        <div style="color:red"><?= htmlspecialchars($_SESSION['error_edicion']) ?></div>
        <?php unset($_SESSION['error_edicion']); ?>
    <?php endif; ?>
    <?php if (empty($actividades)): ?>
        <p>No has creado ninguna actividad aún.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Nombre</th><th>Categoría</th><th>Inicio</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($actividades as $act): ?>
                <tr>
                    <td><?= htmlspecialchars($act['titulo']) ?></td>
                    <td><?= htmlspecialchars($act['categoria']) ?></td>
                    <td><?= $act['fecha'] ?? 'Por definir' ?></td>
                    <td><span class="estado estado-<?= $act['estado'] ?>"><?= ucfirst($act['estado']) ?></span></td>
                    <td>
                        <a href="<?= BASE_URL ?>?c=actividad&a=editar&id=<?= $act['id_actividad'] ?>" class="btn">Editar</a>
                        <?php if (in_array($act['estado'], ['finalizada', 'cancelada'])): ?>
                            <a href="<?= BASE_URL ?>?c=actividad&a=eliminarActividad&id=<?= $act['id_actividad'] ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar definitivamente?')">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>