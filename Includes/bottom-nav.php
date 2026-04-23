<?php
if (!isset($_SESSION['usuario_id'])) return;
?>
<nav class="md:hidden fixed bottom-0 left-0 w-full flex justify-around items-center px-4 pt-3 pb-6 h-20 bg-white/70 backdrop-blur-xl border-t border-outline-variant/20 shadow-[0_-8px_32px_rgba(45,47,47,0.06)] z-50 rounded-t-2xl">
    <a href="<?= BASE_URL ?>?c=dashboard" class="flex flex-col items-center justify-center text-on-surface opacity-60">
        <span class="material-symbols-outlined">explore</span>
        <span class="text-[10px] font-medium uppercase tracking-widest mt-1">Explorar</span>      
    </a>
    <a href="<?= BASE_URL ?>?c=actividad&a=crear" class="flex flex-col items-center justify-center text-on-surface opacity-60">
        <span class="material-symbols-outlined">add_circle</span>
        <span class="text-[10px] font-medium uppercase tracking-widest mt-1">Crear</span>
    </a>
    <a href="<?= BASE_URL ?>?c=mensajes&a=chats" class="flex flex-col items-center justify-center bg-primary/10 text-primary rounded-2xl px-4 py-1">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">chat</span>
        <span class="text-[10px] font-medium uppercase tracking-widest mt-1">Mensajes</span>
    </a>
</nav>