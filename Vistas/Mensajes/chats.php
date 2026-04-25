<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '?c=login');
    exit;
}
require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../includes/top-nav.php';
?>

<main class="flex flex-col h-[calc(100dvh-64px-56px)] overflow-hidden max-w-4xl mx-auto w-full">
<br>
<div class="mb-6 text-center md:text-left">
    <h1 class="text-[2.5rem] md:text-[3.5rem] font-extrabold tracking-tight leading-tight text-on-surface mb-2 font-headline">Conversaciones</h1>
    <p class="text-on-surface-variant max-w-xl">Conecta con tu comunidad, comparte ideas y haz que cada mensaje cuente.</p>
</div>
    <div class="flex flex-col flex-1 bg-white/60 backdrop-blur-sm rounded-2xl shadow-sm border border-surface-container-low overflow-hidden">
        <!-- Cabecera: título + botón nuevo chat (ahora juntos) -->
        <div class="px-4 md:px-6 pt-4 md:pt-6 pb-2 md:pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
            
        </div>

        <!-- Buscador -->
        <div class="px-4 md:px-6 pb-3 shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant text-base">search</span>
                <input type="text" id="searchConversacion" class="w-full bg-surface-container-low border-none rounded-xl py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary/20" placeholder="Buscar conversación...">
            </div>
        </div>

        <!-- Lista de conversaciones (scrollable) -->
        <div id="listaConversaciones" class="flex-1 overflow-y-auto divide-y divide-surface-container-low px-2 md:px-4">
            <!-- Sección: Chats de actividades -->
            <?php if (!empty($conversacionesActividad)): ?>
                <div class="px-2 pt-3 pb-1 text-xs font-semibold text-outline-variant uppercase tracking-wider">Actividades</div>
                <?php foreach ($conversacionesActividad as $c): ?>
                    <a href="<?= BASE_URL ?>?c=mensajesGrupo&a=verActividad&id=<?= $c['id_actividad'] ?>" class="conversation-item flex items-center gap-3 p-3 md:p-4 hover:bg-surface-container-low rounded-xl transition-all">
                        <div class="relative flex-shrink-0">
                            <?php if (!empty($c['foto_base64'])): ?>
                                <img class="w-12 h-12 rounded-xl object-cover" src="<?= htmlspecialchars($c['foto_base64']) ?>" alt="Actividad">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-xl bg-primary-container/30 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined text-2xl">event</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline gap-2">
                                <h3 class="text-[15px] font-bold text-on-surface truncate"><?= htmlspecialchars($c['nombre_actividad']) ?></h3>
                            </div>
                            <div class="flex items-center justify-between gap-2 mt-0.5">
                                <p class="text-xs text-on-surface-variant truncate"><?= htmlspecialchars(mb_substr($c['ultimo_mensaje'] ?? '', 0, 45)) ?></p>
                                <?php if (isset($c['no_leidos']) && $c['no_leidos'] > 0): ?>
                                    <span class="bg-red-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full"><?= $c['no_leidos'] > 9 ? '9+' : $c['no_leidos'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Sección: Chats privados -->
            <?php if (!empty($conversacionesPrivadas)): ?>
                <div class="px-2 pt-3 pb-1 text-xs font-semibold text-outline-variant uppercase tracking-wider">Amigos</div>
                <?php foreach ($conversacionesPrivadas as $c): ?>
                    <a href="<?= BASE_URL ?>?c=mensajes&a=verPrivado&id=<?= $c['id_usuario'] ?>" class="conversation-item flex items-center gap-3 p-3 md:p-4 hover:bg-surface-container-low rounded-xl transition-all">
                        <div class="relative flex-shrink-0">
                            <?php if (!empty($c['foto_base64'])): ?>
                                <img class="w-12 h-12 rounded-full object-cover" src="<?= htmlspecialchars($c['foto_base64']) ?>" alt="Avatar">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-full bg-primary-container/30 flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined text-2xl">person</span>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($c['online']) && $c['online']): ?>
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline gap-2">
                                <h3 class="text-[15px] font-bold text-on-surface truncate"><?= htmlspecialchars($c['nombre_completo']) ?></h3>
                            </div>
                            <div class="flex items-center justify-between gap-2 mt-0.5">
                                <p class="text-xs text-on-surface-variant truncate"><?= htmlspecialchars(mb_substr($c['ultimo_mensaje'] ?? '', 0, 45)) ?></p>
                                <?php if (isset($c['no_leidos']) && $c['no_leidos'] > 0): ?>
                                    <span class="bg-red-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full"><?= $c['no_leidos'] > 9 ? '9+' : $c['no_leidos'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (empty($conversacionesPrivadas) && empty($conversacionesActividad)): ?>
                <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline-variant mb-3">chat_bubble_outline</span>
                    <h3 class="text-lg font-semibold text-on-surface">Sin conversaciones aún</h3>
                    <p class="text-sm text-on-surface-variant mt-1">Participa en actividades o agrega amigos para empezar a chatear.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- MODAL: Nuevo chat con amigos -->
<div id="modalNuevoChat" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden transition-all">
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-xl transform transition-all">
        <div class="flex justify-between items-center p-5 border-b border-surface-container-low">
            <h3 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">group_add</span>
                Iniciar nuevo chat
            </h3>
            <button id="cerrarModal" class="text-outline-variant hover:text-on-surface transition">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-2 max-h-96 overflow-y-auto">
            <?php if (empty($amigos)): ?>
                <div class="text-center py-8 text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl mb-2">sentiment_dissatisfied</span>
                    <p>No tienes amigos agregados aún.</p>
                    <p class="text-sm">Agrega amigos para empezar un chat.</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-surface-container-low">
                    <?php foreach ($amigos as $amigo): ?>
                        <a href="<?= BASE_URL ?>?c=mensajes&a=verPrivado&id=<?= $amigo['id_usuario'] ?>" 
                           class="flex items-center gap-3 p-3 hover:bg-surface-container-low rounded-xl transition">
                            <div class="relative flex-shrink-0">
                                <?php if ($amigo['foto_base64']): ?>
                                    <img class="w-10 h-10 rounded-full object-cover" src="<?= htmlspecialchars($amigo['foto_base64']) ?>" alt="Avatar">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-primary-container/30 flex items-center justify-center text-primary">
                                        <span class="material-symbols-outlined text-xl">person</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($amigo['online']): ?>
                                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-on-surface"><?= htmlspecialchars($amigo['nombre_completo']) ?></h4>
                            </div>
                            <span class="material-symbols-outlined text-outline-variant text-sm">chevron_right</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Botón flotante + -->
<button id="btnNuevoChat" class="fixed bottom-24 right-4 md:bottom-6 md:right-6 z-50 w-14 h-14 bg-primary text-on-primary rounded-full shadow-lg flex items-center justify-center hover:bg-primary-dark transition active:scale-95">
    <span class="material-symbols-outlined text-2xl">add</span>
</button>

<!-- Script para controlar el modal (mejorado con tecla ESC) -->
<script>
    const btnNuevoChat = document.getElementById('btnNuevoChat');
    const modal = document.getElementById('modalNuevoChat');
    const cerrarModal = document.getElementById('cerrarModal');

    function abrirModal() {
        modal.classList.remove('hidden');
    }

    function cerrarModalFunc() {
        modal.classList.add('hidden');
    }

    if (btnNuevoChat) {
        btnNuevoChat.addEventListener('click', abrirModal);
    }

    if (cerrarModal) {
        cerrarModal.addEventListener('click', cerrarModalFunc);
    }

    // Cerrar al hacer clic fuera del contenido
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            cerrarModalFunc();
        }
    });

    // Cerrar con tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            cerrarModalFunc();
        }
    });
</script>

<?php require_once __DIR__ . '/../../includes/bottom-nav.php'; ?>     
<?php require_once __DIR__ . '/../../Scripts/ChatsS.php'; ?>   
</body>
</html>