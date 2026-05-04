<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/top-nav.php';

$id_usuario_actual = $_SESSION['usuario_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - <?= htmlspecialchars($actividad['nombre']) ?></title>
    <!-- Tailwind y Material Symbols (asumo que ya los tienes) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0,1" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<main class="flex flex-col h-[calc(100dvh-64px-56px)] max-w-4xl mx-auto w-full">
    <!-- Cabecera del chat -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col flex-1">
        <div class="p-4 border-b border-gray-200 flex items-center gap-3">
            <a href="<?= BASE_URL ?>?c=mensajes&a=chats" class="text-gray-500 hover:text-gray-700">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div class="flex items-center gap-3 flex-1">
                <?php if (!empty($actividad['foto_base64'])): ?>
                    <img src="<?= htmlspecialchars($actividad['foto_base64']) ?>" class="w-10 h-10 rounded-xl object-cover">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">event</span>
                    </div>
                <?php endif; ?>
                <div>
                    <h2 class="font-bold text-gray-800"><?= htmlspecialchars($actividad['nombre']) ?></h2>
                    <p class="text-xs text-gray-500"><?= count($participantes) ?> participantes</p>
                </div>
            </div>
            <button id="btnInfo" class="p-2 rounded-full hover:bg-gray-100">
                <span class="material-symbols-outlined">info</span>
            </button>
        </div>

        <!-- Contenedor de mensajes -->
        <div id="mensajesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
            <?php foreach ($mensajes as $msg): ?>
                <?php $esPropio = ($msg['id_usuario'] == $id_usuario_actual); ?>
                <div class="flex <?= $esPropio ? 'justify-end' : 'justify-start' ?>">
                    <div class="flex <?= $esPropio ? 'flex-row-reverse' : '' ?> items-end gap-2 max-w-[80%]">
                        <?php if (!$esPropio): ?>
                            <div class="flex-shrink-0">
                                <?php if (!empty($msg['foto_base64'])): ?>
                                    <img src="<?= htmlspecialchars($msg['foto_base64']) ?>" class="w-8 h-8 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-sm">person</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <?php if (!$esPropio): ?>
                                <p class="text-xs text-gray-500 mb-1"><?= htmlspecialchars($msg['nombre_completo']) ?></p>
                            <?php endif; ?>
                            <div class="rounded-2xl px-4 py-2 <?= $esPropio ? 'bg-blue-500 text-white' : 'bg-white border border-gray-200 text-gray-800' ?>">
                                <p class="text-sm"><?= nl2br(htmlspecialchars($msg['contenido'])) ?></p>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 <?= $esPropio ? 'text-right' : '' ?>">
                                <?= date('H:i', strtotime($msg['fecha_envio'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div id="finMensajes"></div>
        </div>

        <!-- Formulario de envío -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <form id="formMensaje" class="flex gap-2">
                <input type="text" id="inputMensaje" name="mensaje" placeholder="Escribe un mensaje..." 
                       class="flex-1 rounded-full border-gray-300 bg-gray-100 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit" class="bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600 transition">
                    <span class="material-symbols-outlined">send</span>
                </button>
            </form>
        </div>
    </div>
</main>

<!-- Panel de información (oculto inicialmente) -->
<div id="infoPanel" class="fixed right-0 top-0 h-full w-80 bg-white shadow-lg transform translate-x-full transition-transform duration-300 z-50 p-4 overflow-y-auto">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-lg">Participantes</h3>
        <button id="cerrarInfo" class="p-1 rounded-full hover:bg-gray-100">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div class="space-y-3">
        <?php foreach ($participantes as $p): ?>
            <div class="flex items-center gap-3">
                <?php if (!empty($p['foto_base64'])): ?>
                    <img src="<?= htmlspecialchars($p['foto_base64']) ?>" class="w-10 h-10 rounded-full object-cover">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                <?php endif; ?>
                <div>
                    <p class="font-medium text-sm"><?= htmlspecialchars($p['nombre_completo']) ?></p>
                    <p class="text-xs text-gray-500"><?= ucfirst($p['rol']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/bottom-nav.php'; ?>
<?php require_once __DIR__ . '/../../Scripts/verActividadS.php'; ?>  

</body>
</html>