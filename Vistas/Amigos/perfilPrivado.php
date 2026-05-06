<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unio | Perfil privado</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6f6f6; color: #2d2f2f; }
        .glass-nav { backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 0px); }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">

<?php include 'includes/top-nav.php'; ?>

<main class="flex-1 pt-24 flex items-center justify-center">
    <div class="bg-surface-container-lowest max-w-md mx-auto p-8 rounded-3xl text-center shadow-xl">
        <span class="material-symbols-outlined text-7xl text-outline-variant">lock</span>
        <h2 class="text-2xl font-bold mt-4">Perfil privado</h2>
        <p class="text-on-surface-variant mt-2">
            <?= htmlspecialchars($nombreCompleto ?? 'Este usuario') ?> ha restringido el acceso a su perfil.
        </p>
        <?php if (!$esPropietario && $relacion !== 'aceptado'): ?>
            <div class="mt-6">
                <a href="<?= BASE_URL ?>?c=amigos&a=enviarSolicitud&id=<?= $id_perfil ?>" class="inline-block px-6 py-2 bg-primary text-on-primary rounded-xl hover:bg-primary-dim transition">
                    Enviar solicitud de amistad
                </a>
            </div>
        <?php endif; ?>
        <?php if ($esPropietario): ?>
            <div class="mt-6">
                <a href="<?= BASE_URL ?>?c=ajustes" class="inline-block px-6 py-2 bg-primary text-on-primary rounded-xl hover:bg-primary-dim transition">
                    Cambiar configuración de privacidad
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/bottom-nav.php'; ?>
</nav>

</body>
</html>