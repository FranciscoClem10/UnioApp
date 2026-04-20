<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../includes/top-nav.php';
?>

<!-- Estilos para el scroll personalizado -->
<style>
#mensajesContainer::-webkit-scrollbar {
    width: 6px;
}
#mensajesContainer::-webkit-scrollbar-track {
    background: #e7e8e8;
    border-radius: 10px;
}
#mensajesContainer::-webkit-scrollbar-thumb {
    background: #5a2af7;
    border-radius: 10px;
}
#mensajesContainer::-webkit-scrollbar-thumb:hover {
    background: #4e0bec;
}

/* Firefox */
#mensajesContainer {
    scrollbar-width: thin;
    scrollbar-color: #5a2af7 #e7e8e8;
}
</style>

<!-- 
    Ajuste de altura:
    64px = top-nav
    56px = bottom-nav
    Modifica si tus barras tienen otra altura
-->
<main class="flex flex-col h-[calc(100dvh-64px-56px)] overflow-hidden max-w-4xl mx-auto w-full">

    <!-- Chat Header (siempre visible) -->
    <div class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-surface-container-low bg-white/50 backdrop-blur-sm shrink-0">
        <div class="flex items-center gap-3">
            <!-- Avatar del destinatario -->
            <div class="w-10 h-10 rounded-full overflow-hidden bg-primary-container flex-shrink-0">
                <?php if (!empty($destinatario['foto_base64'])): ?>
                    <img class="w-full h-full object-cover" src="<?= htmlspecialchars($destinatario['foto_base64']) ?>" alt="Avatar">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <h1 class="text-base font-bold text-on-surface"><?= htmlspecialchars($destinatario['nombre_completo'] ?? $destinatario['nombre']) ?></h1>
                <p class="text-xs text-primary font-medium" id="estadoUsuario">
                    <?php if (isset($destinatario['online']) && $destinatario['online']): ?>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 bg-green-500 rounded-full"></span> En línea</span>
                    <?php else: ?>
                        <span class="text-outline-variant">Desconectado</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= BASE_URL ?>?c=perfil&a=ver&id=<?= $destinatario['id_usuario'] ?>" class="flex items-center gap-2 px-4 py-2 bg-surface-container-low text-primary text-sm font-semibold rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-sm">person</span>
                Ver perfil
            </a>
        </div>
    </div>

    <!-- Mensajes (scroll real aquí) -->
    <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 flex flex-col" id="mensajesContainer">
        <div class="text-center text-outline-variant py-8">Cargando mensajes...</div>
    </div>

    <!-- Input area (pegado abajo) -->
    <div class="p-4 md:p-6 bg-white/50 backdrop-blur-sm border-t border-surface-container-low shrink-0 relative z-20">
        <div class="w-full md:max-w-4xl mx-auto flex items-end gap-2 bg-surface-container-low p-2 rounded-2xl focus-within:ring-2 focus-within:ring-primary/10 transition-all">
            <button type="button" class="material-symbols-outlined text-outline p-2 rounded-full hover:bg-white transition-colors" id="btnEmoji" style="display: none;">mood</button>
            <textarea id="contenidoInput" class="flex-1 min-w-0 bg-transparent border-none focus:ring-0 text-sm py-2 px-2 resize-none h-10 max-h-32 placeholder:text-outline-variant" placeholder="Escribe un mensaje..." rows="1"></textarea>
            <button type="submit" id="btnEnviar" class="w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full shadow-lg shadow-primary/20 active:scale-95 transition-transform">
                <span class="material-symbols-outlined">send</span>
            </button>
        </div>
    </div>

</main>

<!-- Auto scroll al entrar -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("mensajesContainer");
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});
</script>

<!-- Bottom Navigation para móviles (fijo abajo) -->
<?php require_once __DIR__ . '/../../includes/bottom-nav.php'; ?>     
<?php require_once __DIR__ . '/../../Scripts/verPrivadoS.php'; ?>         
</body>
</html>